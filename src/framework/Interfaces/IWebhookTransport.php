<?php

declare(strict_types=1);
namespace Boctulus\Simplerest\Core\Interfaces;

interface IWebhookTransport
{
    public function send(string $callback, string $rawBody, array $headers): mixed;
}
