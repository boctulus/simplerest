<?php

namespace Boctulus\Simplerest\Core\Libs;

class WebhookPublisher
{
    protected WebhookSubscriptionMatcher $subscriptionMatcher;
    protected WebhookDeliveryDispatcher $deliveryDispatcher;

    public function __construct(
        ?WebhookSubscriptionMatcher $subscriptionMatcher = null,
        ?WebhookDeliveryDispatcher $deliveryDispatcher = null
    ) {
        $this->subscriptionMatcher = $subscriptionMatcher ?? new WebhookSubscriptionMatcher();
        $this->deliveryDispatcher = $deliveryDispatcher ?? new WebhookDeliveryDispatcher(
            new WebhookHttpTransport()
        );
    }

    public function publish(WebhookEvent $event): void
    {
        $matches = $this->subscriptionMatcher->findMatches($event);
        $this->deliveryDispatcher->dispatch($event, $matches);
    }
}
