<?php

namespace Boctulus\Simplerest\Core\Interfaces;

interface IWebhookTransport
{
    public function send(string $callback, string $rawBody, array $headers): mixed;
}
