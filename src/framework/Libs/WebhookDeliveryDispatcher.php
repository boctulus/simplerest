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
            $payload = [
                'webhook_id' => $subscription['id'],
                'event_type' => $event->getEventType(),
                'entity' => $event->getEntity(),
                'id' => $event->getId(),
                'data' => $match['data'],
                'user_id' => $event->getActorId(),
                'at' => $event->getOccurredAt()
            ];

            $this->transport->send($subscription['callback'], $payload);
        }
    }
}
