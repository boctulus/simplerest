<?php

use Boctulus\Simplerest\Core\Libs\Brevo;
use Boctulus\Simplerest\Core\Libs\Config;
use Brevo\Brevo as BrevoClient;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;

final class BrevoTestHarness extends Brevo
{
    public static Client $http;

    protected static function client(string $apiKey): BrevoClient
    {
        return new BrevoClient($apiKey, ['client' => self::$http, 'maxRetries' => 0]);
    }
}

final class BrevoTest extends TestCase
{
    private array $originalConfig;
    private array $history = [];
    private MockHandler $mock;

    protected function setUp(): void
    {
        $property = new ReflectionProperty(Config::class, 'data');
        $this->originalConfig = $property->getValue();
        $property->setValue(null, [
            'brevo_api_key' => 'test-api-key',
            'email' => ['from' => ['address' => 'sender@example.com', 'name' => 'Sender']],
        ]);
        $this->mock = new MockHandler();
        $handler = HandlerStack::create($this->mock);
        $handler->push(Middleware::history($this->history));
        BrevoTestHarness::$http = new Client(['handler' => $handler, 'http_errors' => false]);
    }

    protected function tearDown(): void
    {
        (new ReflectionProperty(Config::class, 'data'))->setValue(null, $this->originalConfig);
    }

    public function test_sdk_serializes_recipients_text_and_attachments(): void
    {
        $this->mock->append(new Response(201, [], '{"messageId":"<test@brevo>"}'));
        self::assertTrue(BrevoTestHarness::send(
            ['email' => 'recipient@example.com'], 'Test', '<p>HTML</p>',
            ['data' => 'attachment bytes', 'name' => 'test.txt'], [],
            [['email' => 'cc@example.com']], 'bcc@example.com', [], 'Plain text'
        ));
        $request = $this->history[0]['request'];
        $payload = json_decode((string) $request->getBody(), true, 512, JSON_THROW_ON_ERROR);
        self::assertSame('POST', $request->getMethod());
        self::assertSame('https://api.brevo.com/v3/smtp/email', (string) $request->getUri());
        self::assertSame('test-api-key', $request->getHeaderLine('api-key'));
        self::assertSame('recipient@example.com', $payload['to'][0]['email']);
        self::assertSame('cc@example.com', $payload['cc'][0]['email']);
        self::assertSame('bcc@example.com', $payload['bcc'][0]['email']);
        self::assertSame('sender@example.com', $payload['replyTo']['email']);
        self::assertSame('Plain text', $payload['textContent']);
        self::assertSame('attachment bytes', base64_decode($payload['attachment'][0]['content']));
        self::assertSame('<test@brevo>', BrevoTestHarness::messageId());
    }

    public function test_api_error_is_reported_and_cleared_after_success(): void
    {
        $this->mock->append(new Response(401, [], '{"message":"Key not found","code":"unauthorized"}'));
        self::assertFalse(BrevoTestHarness::send('recipient@example.com', 'Test', 'Body'));
        self::assertStringContainsString('401', BrevoTestHarness::errors());
        self::assertNull(BrevoTestHarness::messageId());
        $this->mock->append(new Response(201, [], '{"messageId":"<success@brevo>"}'));
        self::assertTrue(BrevoTestHarness::send('recipient@example.com', 'Test', 'Body'));
        self::assertNull(BrevoTestHarness::errors());
    }

    public function test_empty_content_and_missing_attachment_do_not_send(): void
    {
        self::assertFalse(BrevoTestHarness::send('recipient@example.com', 'Test', ''));
        self::assertSame('Body or alt_body is required', BrevoTestHarness::errors());
        self::assertFalse(BrevoTestHarness::send('recipient@example.com', 'Test', 'Body', __DIR__ . '/missing-brevo-file.txt'));
        self::assertSame([], $this->history);
    }

    public function test_response_without_message_id_is_not_success(): void
    {
        $this->mock->append(new Response(201, [], '{}'));
        self::assertFalse(BrevoTestHarness::send('recipient@example.com', 'Test', '', null, [], [], [], [], 'Plain text only'));
        self::assertSame('Brevo did not return a message ID', BrevoTestHarness::errors());
    }
}
