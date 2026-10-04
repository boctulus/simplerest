<?php

namespace Boctulus\Simplerest\Core\Interfaces;

interface IWebhookTransport
{
    public function send(string $callback, array $payload): mixed;
}
