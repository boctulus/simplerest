<?php

namespace Boctulus\Simplerest\Core\Libs;

use Boctulus\Simplerest\Core\Interfaces\IWebhookTransport;

class WebhookDeliveryDispatcher
{
    protected IWebhookTransport $transport;

    public function __construct(IWebhookTransport $transport)
    {
        $this->transport = $transport;
    }

    public function dispatch(WebhookEvent $event, iterable $matches): void
    {
        foreach ($matches as $match) {
            $subscription = $match['subscription'];
            $secret = $subscription['secret'] ?? null;
            if (!is_string($secret) || !preg_match('/^[0-9a-f]{64}$/', $secret)) {
                continue;
            }

            $payload = [
                'webhook_id' => $subscription['id'],
                'event_type' => $event->getEventType(),
                'entity' => $event->getEntity(),
                'id' => $event->getId(),
                'data' => $match['data'],
                'user_id' => $event->getActorId(),
                'at' => $event->getOccurredAt()
            ];

            $rawBody = json_encode(
                $payload,
                JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR
            );
            $timestamp = (string) time();
            $eventId = $event->getEventId();
            $signatureInput = 'v1.' . $timestamp . '.' . $eventId . '.' . $rawBody;
            $digest = hash_hmac('sha256', $signatureInput, $secret);
            $headers = [
                'Content-Type' => 'application/json; charset=utf-8',
                'X-Simplerest-Webhook-Event-Id' => $eventId,
                'X-Simplerest-Webhook-Timestamp' => $timestamp,
                'X-Simplerest-Webhook-Signature' => 'v1=' . $digest,
            ];

            $this->transport->send($subscription['callback'], $rawBody, $headers);
        }
    }
}
