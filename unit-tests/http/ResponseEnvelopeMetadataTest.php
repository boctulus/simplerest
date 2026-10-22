<?php

namespace Boctulus\Simplerest\Tests\Http;

use Boctulus\Simplerest\Core\Response;
use PHPUnit\Framework\TestCase;

class ResponseEnvelopeMetadataTest extends TestCase
{
    public function test_send_preserves_sibling_metadata_and_regenerates_envelope_fields(): void
    {
        $response = $this->createResponse();

        $response->send([
            'data' => ['rows' => [['id' => 7]]],
            'paginator' => ['total' => 18],
            'meta' => ['cursor' => 'next'],
            'status_code' => 500,
            'error' => ['message' => 'stale'],
        ], 201);

        $payload = json_decode($response->get(), true, 512, JSON_THROW_ON_ERROR);

        $this->assertSame(['rows' => [['id' => 7]]], $payload['data']);
        $this->assertSame(['total' => 18], $payload['paginator']);
        $this->assertSame(['cursor' => 'next'], $payload['meta']);
        $this->assertSame(201, $payload['status_code']);
        $this->assertSame([], $payload['error']);
    }

    public function test_send_json_preserves_metadata_when_data_is_null(): void
    {
        $response = $this->createResponse();

        $response->sendJson([
            'data' => null,
            'meta' => ['reason' => 'empty'],
            'status_code' => 500,
            'error' => ['message' => 'stale'],
        ], 202, 'accepted');

        $payload = $response->get();

        $this->assertNull($payload['data']);
        $this->assertSame(['reason' => 'empty'], $payload['meta']);
        $this->assertSame(202, $payload['status_code']);
        $this->assertSame('accepted', $payload['error']);
    }

    public function test_send_json_wraps_a_scalar_payload(): void
    {
        $response = $this->createResponse();

        $response->sendJson('plain text');

        $this->assertSame([
            'data' => 'plain text',
            'status_code' => 200,
            'error' => '',
        ], $response->get());
    }

    private function createResponse(): Response
    {
        $reflection = new \ReflectionClass(ResponseEnvelopeTestResponse::class);
        $response = $reflection->newInstanceWithoutConstructor();

        $config = $reflection->getProperty('config');
        $config->setValue($response, ['paginator' => ['position' => 'TOP']]);

        return $response;
    }
}

class ResponseEnvelopeTestResponse extends Response
{
    public function __destruct()
    {
    }
}
