<?php

namespace Boctulus\Simplerest\tests;

use Boctulus\Simplerest\Core\Interfaces\IWebhookTransport;
use Boctulus\Simplerest\Core\Libs\WebhookDeliveryDispatcher;
use Boctulus\Simplerest\Core\Libs\WebhookEvent;
use Boctulus\Simplerest\Core\Libs\WebhookPublisher;
use Boctulus\Simplerest\Core\Libs\WebhookSubscriptionMatcher;
use PHPUnit\Framework\TestCase;

final class InMemoryWebhookSubscriptionMatcher extends WebhookSubscriptionMatcher
{
    public function __construct(
        private array $subscriptions,
        private array $entityData
    ) {
    }

    protected function loadSubscriptions(WebhookEvent $event): iterable
    {
        return array_values(array_filter(
            $this->subscriptions,
            fn (array $subscription): bool =>
                $subscription['op'] === $event->getEventType()
                && $subscription['entity'] === $event->getEntity()
        ));
    }

    protected function loadEntityData(WebhookEvent $event)
    {
        return $this->entityData[$event->getId()] ?? null;
    }
}

final class WebhookRecordingTransport implements IWebhookTransport
{
    public array $deliveries = [];
    public array $trace;

    public function __construct(array &$trace)
    {
        $this->trace = &$trace;
    }

    public function send(string $callback, string $rawBody, array $headers): mixed
    {
        $this->trace[] = 'send:' . $callback;
        $this->deliveries[] = [
            'callback' => $callback,
            'raw_body' => $rawBody,
            'headers' => $headers
        ];

        return null;
    }
}

final class WebhookPublisherTest extends TestCase
{
    public function test_update_conditions_are_checked_before_merging_the_previous_row(): void
    {
        $matcher = new InMemoryWebhookSubscriptionMatcher(
            [[
                'id' => 1,
                'op' => 'update',
                'entity' => 'users',
                'conditions' => 'status=active',
                'callback' => 'https://callback.test/active'
            ]],
            [7 => ['id' => 7, 'status' => 'active', 'name' => 'Before']]
        );

        $matches = iterator_to_array($matcher->findMatches(new WebhookEvent(
            'update',
            'users',
            ['name' => 'After'],
            7
        )));

        $this->assertSame([], $matches);
    }

    public function test_update_matching_preserves_data_shared_between_subscriptions(): void
    {
        $matcher = new InMemoryWebhookSubscriptionMatcher(
            [
                [
                    'id' => 1,
                    'op' => 'update',
                    'entity' => 'users',
                    'conditions' => '',
                    'callback' => 'https://callback.test/all'
                ],
                [
                    'id' => 2,
                    'op' => 'update',
                    'entity' => 'users',
                    'conditions' => 'status=active',
                    'callback' => 'https://callback.test/active'
                ]
            ],
            [7 => ['id' => 7, 'status' => 'active', 'name' => 'Before']]
        );

        $matches = iterator_to_array($matcher->findMatches(new WebhookEvent(
            'update',
            'users',
            ['name' => 'After'],
            7
        )));

        $this->assertSame([1, 2], array_column(array_column($matches, 'subscription'), 'id'));
        $this->assertSame(
            ['id' => 7, 'status' => 'active', 'name' => 'After'],
            $matches[1]['data']
        );
    }

    public function test_webhook_subscription_secrets_are_removed_from_event_data(): void
    {
        $matcher = new InMemoryWebhookSubscriptionMatcher(
            [[
                'id' => 1,
                'op' => 'update',
                'entity' => 'webhooks',
                'conditions' => '',
                'callback' => 'https://callback.test/subscription'
            ]],
            [7 => [
                'id' => 7,
                'name' => 'subscription',
                'secret' => str_repeat('a', 64)
            ]]
        );

        $matches = iterator_to_array($matcher->findMatches(new WebhookEvent(
            'update',
            'webhooks',
            ['secret' => str_repeat('b', 64), 'name' => 'updated'],
            7
        )));

        $this->assertCount(1, $matches);
        $this->assertSame(['id' => 7, 'name' => 'updated'], $matches[0]['data']);
    }

    public function test_list_event_does_not_include_nested_webhook_secrets(): void
    {
        $matcher = new InMemoryWebhookSubscriptionMatcher(
            [[
                'id' => 1,
                'op' => 'list',
                'entity' => 'webhooks',
                'conditions' => '',
                'callback' => 'https://callback.test/subscription'
            ]],
            []
        );

        $matches = iterator_to_array($matcher->findMatches(new WebhookEvent(
            'list',
            'webhooks',
            [[
                'id' => 7,
                'name' => 'subscription',
                'secret' => str_repeat('c', 64)
            ]]
        )));

        $this->assertCount(1, $matches);
        $this->assertSame([[
            'id' => 7,
            'name' => 'subscription'
        ]], $matches[0]['data']);
    }

    public function test_publisher_dispatches_each_match_through_an_injected_transport(): void
    {
        $trace = [];
        $matcher = new class($trace) extends WebhookSubscriptionMatcher {
            public array $trace;

            public function __construct(array &$trace)
            {
                $this->trace = &$trace;
            }

            public function findMatches(WebhookEvent $event): \Generator
            {
                $this->trace[] = 'match:first';
                yield [
                    'subscription' => [
                        'id' => 10,
                        'callback' => 'https://callback.test/first',
                        'secret' => str_repeat('a', 64)
                    ],
                    'data' => ['amount' => 125]
                ];

                $this->trace[] = 'match:second';
                yield [
                    'subscription' => [
                        'id' => 11,
                        'callback' => 'https://callback.test/second',
                        'secret' => str_repeat('b', 64)
                    ],
                    'data' => ['amount' => 125]
                ];
            }
        };

        $transport = new WebhookRecordingTransport($trace);

        $event = new WebhookEvent(
            'create',
            'payments',
            ['amount' => 125],
            'payment-42',
            8,
            ['tenant_id' => 3],
            false,
            '2026-10-04 12:00:00',
            '123e4567-e89b-42d3-a456-426614174000'
        );

        (new WebhookPublisher(
            $matcher,
            new WebhookDeliveryDispatcher($transport)
        ))->publish($event);

        $this->assertSame([
            'match:first',
            'send:https://callback.test/first',
            'match:second',
            'send:https://callback.test/second'
        ], $trace);

        $first = $transport->deliveries[0];
        $second = $transport->deliveries[1];
        $expectedBody = '{"webhook_id":10,"event_type":"create","entity":"payments","id":"payment-42","data":{"amount":125},"user_id":8,"at":"2026-10-04 12:00:00"}';

        $this->assertSame($expectedBody, $first['raw_body']);
        $this->assertSame(str_replace('"webhook_id":10', '"webhook_id":11', $expectedBody), $second['raw_body']);
        $this->assertSame('123e4567-e89b-42d3-a456-426614174000', $first['headers']['X-Simplerest-Webhook-Event-Id']);
        $this->assertSame($first['headers']['X-Simplerest-Webhook-Event-Id'], $second['headers']['X-Simplerest-Webhook-Event-Id']);

        foreach ([[$first, str_repeat('a', 64)], [$second, str_repeat('b', 64)]] as [$delivery, $secret]) {
            $timestamp = $delivery['headers']['X-Simplerest-Webhook-Timestamp'];
            $signatureInput = 'v1.' . $timestamp . '.'
                . $delivery['headers']['X-Simplerest-Webhook-Event-Id'] . '.'
                . $delivery['raw_body'];
            $this->assertSame(
                'v1=' . hash_hmac('sha256', $signatureInput, $secret),
                $delivery['headers']['X-Simplerest-Webhook-Signature']
            );
            $this->assertSame('application/json; charset=utf-8', $delivery['headers']['Content-Type']);
        }
    }

    public function test_publisher_accepts_custom_event_names_without_http_context(): void
    {
        $trace = [];
        $transport = new WebhookRecordingTransport($trace);
        $publisher = new WebhookPublisher(
            new InMemoryWebhookSubscriptionMatcher([], []),
            new WebhookDeliveryDispatcher($transport)
        );

        $publisher->publish(new WebhookEvent(
            'payment.refunded',
            'payments',
            ['amount' => 125],
            'payment-42',
            8,
            ['tenant_id' => 3]
        ));

        $this->assertSame([], $transport->deliveries);
        $this->assertSame([], $trace);
    }

    public function test_dispatcher_skips_subscriptions_without_a_valid_server_generated_secret(): void
    {
        $trace = [];
        $matcher = new class extends WebhookSubscriptionMatcher {
            public function findMatches(WebhookEvent $event): \Generator
            {
                yield [
                    'subscription' => [
                        'id' => 20,
                        'callback' => 'https://callback.example/hook',
                        'secret' => 'client-controlled-secret',
                    ],
                    'data' => ['amount' => 50],
                ];
            }
        };
        $transport = new WebhookRecordingTransport($trace);

        (new WebhookPublisher(
            $matcher,
            new WebhookDeliveryDispatcher($transport)
        ))->publish(new WebhookEvent('create', 'payments', ['amount' => 50]));

        $this->assertSame([], $transport->deliveries);
        $this->assertSame([], $trace);
    }
}
