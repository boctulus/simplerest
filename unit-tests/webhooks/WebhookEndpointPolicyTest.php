<?php

namespace Boctulus\Simplerest\tests;

use Boctulus\Simplerest\Core\Libs\WebhookEndpointPolicy;
use Boctulus\Simplerest\Core\Libs\WebhookHttpTransport;
use PHPUnit\Framework\TestCase;

final class FixtureWebhookEndpointPolicy extends WebhookEndpointPolicy
{
    public function __construct(string $environment, private array $records = [])
    {
        parent::__construct($environment);
    }

    protected function lookupDnsRecords(string $hostname): array
    {
        return $this->records[strtolower($hostname)] ?? [];
    }
}

final class FixtureWebhookHttpTransport extends WebhookHttpTransport
{
    public array $captured = [];

    public function __construct(?WebhookEndpointPolicy $policy = null)
    {
        parent::__construct($policy ?? new FixtureWebhookEndpointPolicy('development', [
            'example.com' => [['type' => 'A', 'ip' => '8.8.8.8']],
        ]));
    }

    protected function performRequest(array $endpoint, string $rawBody, array $requestHeaders): array
    {
        $this->captured = [
            'endpoint' => $endpoint,
            'raw_body' => $rawBody,
            'request_headers' => $requestHeaders,
            'options' => $this->buildCurlOptions(
                $endpoint,
                $rawBody,
                $requestHeaders,
                static fn ($handle, string $chunk): int => strlen($chunk),
                static fn ($handle, string $header): int => strlen($header)
            ),
        ];

        return ['ok' => true, 'status' => 204, 'error' => null];
    }
}

final class WebhookEndpointPolicyTest extends TestCase
{
    public function test_http_is_permitted_outside_production_and_https_is_required_in_production(): void
    {
        $records = ['example.com' => [['type' => 'A', 'ip' => '8.8.8.8']]];
        $development = new FixtureWebhookEndpointPolicy('development', $records);

        $this->assertSame('http', $development->resolve('http://example.com/hook')['scheme']);
        $this->assertSame('https', $development->resolve('https://example.com/hook')['scheme']);
        $this->assertSame('https', (new FixtureWebhookEndpointPolicy('PRODUCTION', $records))
            ->resolve('https://example.com/hook')['scheme']);

        foreach (['prod', 'production'] as $environment) {
            $this->assertRejected(
                fn () => (new FixtureWebhookEndpointPolicy($environment, $records))
                    ->resolve('http://example.com/hook')
            );
        }
    }

    public function test_ipv4_and_ipv6_special_destinations_are_rejected(): void
    {
        foreach ([
            '127.0.0.1',
            '10.0.0.1',
            '172.16.0.1',
            '192.168.1.1',
            '100.64.0.1',
            '169.254.169.254',
            '192.0.2.1',
            '224.0.0.1',
        ] as $address) {
            $this->assertRejected(fn () => (new FixtureWebhookEndpointPolicy('development'))
                ->resolve('http://' . $address . '/hook'));
        }

        foreach ([
            '::1',
            'fc00::1',
            'fe80::1',
            'ff02::1',
            '2001:db8::1',
            '::ffff:8.8.8.8',
            '64:ff9b::808:808',
        ] as $address) {
            $this->assertRejected(fn () => (new FixtureWebhookEndpointPolicy('development'))
                ->resolve('http://[' . $address . ']/hook'));
        }

        $this->assertSame('8.8.8.8', (new FixtureWebhookEndpointPolicy('development'))
            ->resolve('https://8.8.8.8/hook')['addresses'][0]);
        $this->assertSame('2001:4860:4860::8888', (new FixtureWebhookEndpointPolicy('development'))
            ->resolve('https://[2001:4860:4860::8888]/hook')['addresses'][0]);
    }

    public function test_dns_answers_are_followed_and_the_entire_address_set_is_checked(): void
    {
        $policy = new FixtureWebhookEndpointPolicy('development', [
            'example.com' => [['type' => 'CNAME', 'target' => 'delivery.example.net.']],
            'delivery.example.net' => [['type' => 'CNAME', 'target' => 'edge.example.org']],
            'edge.example.org' => [
                ['type' => 'A', 'ip' => '8.8.4.4'],
                ['type' => 'AAAA', 'ipv6' => '2001:4860:4860::8844'],
            ],
        ]);

        $this->assertSame(
            ['8.8.4.4', '2001:4860:4860::8844'],
            $policy->resolve('https://example.com/hook')['addresses']
        );

        $mixed = new FixtureWebhookEndpointPolicy('development', [
            'example.com' => [
                ['type' => 'A', 'ip' => '8.8.8.8'],
                ['type' => 'AAAA', 'ipv6' => 'fd00::1'],
            ],
        ]);
        $this->assertRejected(fn () => $mixed->resolve('https://example.com/hook'));

        $this->assertRejected(fn () => (new FixtureWebhookEndpointPolicy('development'))
            ->resolve('https://example.com/hook'));
    }

    public function test_ambiguous_schemes_hosts_and_url_parts_are_rejected(): void
    {
        $policy = new FixtureWebhookEndpointPolicy('development');
        foreach ([
            '/relative/path',
            'ftp://example.com/hook',
            'http://user:pass@example.com/hook',
            'http://example.com/hook#fragment',
            'http://localhost/hook',
            'http://127.1/hook',
            'http://example.com\\@127.0.0.1/hook',
        ] as $callback) {
            $this->assertRejected(fn () => $policy->resolve($callback));
        }
    }

    public function test_transport_pins_dns_and_uses_bounded_direct_strict_http_options(): void
    {
        $policy = new FixtureWebhookEndpointPolicy('development', [
            'example.com' => [
                ['type' => 'A', 'ip' => '8.8.8.8'],
                ['type' => 'AAAA', 'ipv6' => '2001:4860:4860::8888'],
            ],
        ]);
        $transport = new FixtureWebhookHttpTransport($policy);

        $body = '{"safe":"exact bytes"}';
        $headers = ['Content-Type' => 'application/json; charset=utf-8'];
        $this->assertSame(['ok' => true, 'status' => 204, 'error' => null],
            $transport->send('https://example.com/hook', $body, $headers));
        $captured = $transport->captured;
        $options = $captured['options'];

        $this->assertSame($body, $captured['raw_body']);
        $this->assertSame(['Content-Type: application/json; charset=utf-8'], $captured['request_headers']);
        $this->assertSame($body, $options[CURLOPT_POSTFIELDS]);
        $this->assertFalse($options[CURLOPT_FOLLOWLOCATION]);
        $this->assertSame(0, $options[CURLOPT_MAXREDIRS]);
        $this->assertSame(5, $options[CURLOPT_CONNECTTIMEOUT]);
        $this->assertSame(10, $options[CURLOPT_TIMEOUT]);
        $this->assertTrue($options[CURLOPT_SSL_VERIFYPEER]);
        $this->assertSame(2, $options[CURLOPT_SSL_VERIFYHOST]);
        $this->assertSame('', $options[CURLOPT_PROXY]);
        $this->assertSame('*', $options[CURLOPT_NOPROXY]);
        $this->assertSame(CURLPROTO_HTTPS, $options[CURLOPT_PROTOCOLS]);
        $this->assertSame(CURLPROTO_HTTPS, $options[CURLOPT_REDIR_PROTOCOLS]);
        $this->assertContains($options[CURLOPT_RESOLVE][0], [
            'example.com:443:8.8.8.8',
            'example.com:443:[2001:4860:4860::8888]',
        ]);
    }

    public function test_transport_rejects_header_injection_without_exposing_url_details(): void
    {
        $transport = new FixtureWebhookHttpTransport();
        $result = $transport->send('https://example.com/hook', '{}', [
            'X-Test' => "safe\r\nHost: 127.0.0.1",
        ]);

        $this->assertSame(['ok' => false, 'status' => null, 'error' => 'endpoint_policy_rejected'], $result);
        $this->assertSame([], $transport->captured);
    }

    public function test_transport_rejects_oversized_requests_before_resolution_or_connection(): void
    {
        $transport = new FixtureWebhookHttpTransport();
        $result = $transport->send('https://example.com/hook', str_repeat('x', 1048577), []);

        $this->assertSame(['ok' => false, 'status' => null, 'error' => 'request_body_too_large'], $result);
        $this->assertSame([], $transport->captured);
    }

    private function assertRejected(callable $callback): void
    {
        try {
            $callback();
            $this->fail('Expected callback endpoint to be rejected.');
        } catch (\InvalidArgumentException) {
            $this->addToAssertionCount(1);
        }
    }
}
