<?php

namespace Boctulus\Simplerest\Core\Libs;

use Boctulus\Simplerest\Core\Interfaces\IWebhookTransport;

class WebhookHttpTransport implements IWebhookTransport
{
    public function send(string $callback, array $payload): mixed
    {
        return \consume_api($callback, 'POST', $payload);
    }
}
