<?php

namespace Boctulus\Simplerest\tests;

use Boctulus\Simplerest\Core\Interfaces\IWebhookTransport;
use Boctulus\Simplerest\Core\Libs\Config;
use Boctulus\Simplerest\Core\Libs\DB;
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

    protected function selectConnectionForEvent(WebhookEvent $event): ?array
    {
        return [
            'previous' => null,
            'selected' => 'fixture',
        ];
    }

    protected function restoreConnection(array $connection): void
    {
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

final class ConnectionScopedInMemoryWebhookSubscriptionMatcher extends WebhookSubscriptionMatcher
{
    private ?string $selectedConnectionId = null;

    public function __construct(private array $subscriptionsByConnection)
    {
    }

    protected function selectConnectionForEvent(WebhookEvent $event): ?array
    {
        $connectionId = $event->getScopeContext()['connection_id'] ?? null;
        if (!is_string($connectionId) || !array_key_exists($connectionId, $this->subscriptionsByConnection)) {
            return null;
        }

        $previous = $this->selectedConnectionId;
        $this->selectedConnectionId = $connectionId;

        return [
            'previous' => $previous,
            'selected' => $connectionId,
        ];
    }

    protected function restoreConnection(array $connection): void
    {
        $this->selectedConnectionId = $connection['previous'];
    }

    protected function loadSubscriptions(WebhookEvent $event): iterable
    {
        return array_values(array_filter(
            $this->subscriptionsByConnection[$this->selectedConnectionId] ?? [],
            fn (array $subscription): bool =>
                $subscription['op'] === $event->getEventType()
                && $subscription['entity'] === $event->getEntity()
        ));
    }

    protected function loadEntityData(WebhookEvent $event)
    {
        return null;
    }
}

final class ConfiguredConnectionWebhookSubscriptionMatcher extends WebhookSubscriptionMatcher
{
    public array $connectionsRead = [];

    public function __construct(private array $subscriptionsByConnection)
    {
    }

    protected function loadSubscriptions(WebhookEvent $event): iterable
    {
        $connectionId = DB::getCurrentConnectionId();
        $this->connectionsRead[] = $connectionId;

        return array_values(array_filter(
            $this->subscriptionsByConnection[$connectionId] ?? [],
            fn (array $subscription): bool =>
                $subscription['op'] === $event->getEventType()
                && $subscription['entity'] === $event->getEntity()
        ));
    }

    protected function loadEntityData(WebhookEvent $event)
    {
        return null;
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
            7,
            null,
            ['connection_id' => 'fixture', 'owner_scope' => 'global']
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
            7,
            null,
            ['connection_id' => 'fixture', 'owner_scope' => 'global']
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
                'callback' => 'https://callback.test/subscription',
                'belongs_to' => 8,
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
            7,
            null,
            [
                'connection_id' => 'fixture',
                'owner_scope' => 'single',
                'owner_field' => 'belongs_to',
                'owner_id' => 8,
            ]
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
                'callback' => 'https://callback.test/subscription',
                'belongs_to' => 8,
            ]],
            []
        );

        $matches = iterator_to_array($matcher->findMatches(new WebhookEvent(
            'list',
            'webhooks',
            [[
                'id' => 7,
                'name' => 'subscription',
                'secret' => str_repeat('c', 64),
                'belongs_to' => 8,
            ]],
            null,
            null,
            [
                'connection_id' => 'fixture',
                'owner_scope' => 'rows',
                'owner_field' => 'belongs_to',
            ]
        )));

        $this->assertCount(1, $matches);
        $this->assertSame([[
            'id' => 7,
            'name' => 'subscription',
            'belongs_to' => 8,
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

    public function test_publisher_dispatches_custom_events_only_within_connection_and_owner_scope(): void
    {
        $trace = [];
        $transport = new WebhookRecordingTransport($trace);
        $matcher = new ConnectionScopedInMemoryWebhookSubscriptionMatcher([
            'tenant-a' => [
                [
                    'id' => 20,
                    'op' => 'payment.refunded',
                    'entity' => 'payments',
                    'belongs_to' => 12,
                    'callback' => 'https://callback.test/tenant-a-owner-12',
                    'secret' => str_repeat('a', 64),
                ],
                [
                    'id' => 21,
                    'op' => 'payment.refunded',
                    'entity' => 'payments',
                    'belongs_to' => 13,
                    'callback' => 'https://callback.test/tenant-a-owner-13',
                    'secret' => str_repeat('b', 64),
                ],
            ],
            'tenant-b' => [
                [
                    'id' => 22,
                    'op' => 'payment.refunded',
                    'entity' => 'payments',
                    'belongs_to' => 12,
                    'callback' => 'https://callback.test/tenant-b-owner-12',
                    'secret' => str_repeat('c', 64),
                ],
            ],
        ]);
        $publisher = new WebhookPublisher(
            $matcher,
            new WebhookDeliveryDispatcher($transport)
        );

        $publisher->publish(new WebhookEvent(
            'payment.refunded',
            'payments',
            ['amount' => 125],
            'payment-42',
            8,
            [
                'connection_id' => 'tenant-a',
                'owner_scope' => 'single',
                'owner_field' => 'belongs_to',
                'owner_id' => 12,
            ]
        ));

        $this->assertSame([
            'https://callback.test/tenant-a-owner-12',
        ], array_column($transport->deliveries, 'callback'));
        $payload = json_decode($transport->deliveries[0]['raw_body'], true, 512, JSON_THROW_ON_ERROR);
        $this->assertSame(8, $payload['user_id']);
    }

    public function test_list_delivery_rows_are_partitioned_by_subscription_owner(): void
    {
        $matcher = new ConnectionScopedInMemoryWebhookSubscriptionMatcher([
            'main' => [
                [
                    'id' => 30,
                    'op' => 'list',
                    'entity' => 'payments',
                    'belongs_to' => 12,
                    'callback' => 'https://callback.test/owner-12',
                ],
                [
                    'id' => 31,
                    'op' => 'list',
                    'entity' => 'payments',
                    'belongs_to' => 13,
                    'callback' => 'https://callback.test/owner-13',
                ],
            ],
        ]);

        $matches = iterator_to_array($matcher->findMatches(new WebhookEvent(
            'list',
            'payments',
            [
                ['id' => 1, 'belongs_to' => 12, 'amount' => 100],
                ['id' => 2, 'belongs_to' => 13, 'amount' => 200],
                ['id' => 3, 'belongs_to' => 99, 'amount' => 300],
            ],
            null,
            8,
            [
                'connection_id' => 'main',
                'owner_scope' => 'rows',
                'owner_field' => 'belongs_to',
            ]
        )));

        $this->assertSame([30, 31], array_column(array_column($matches, 'subscription'), 'id'));
        $this->assertSame([
            ['id' => 1, 'belongs_to' => 12, 'amount' => 100],
        ], $matches[0]['data']);
        $this->assertSame([
            ['id' => 2, 'belongs_to' => 13, 'amount' => 200],
        ], $matches[1]['data']);
    }

    public function test_unresolved_owner_and_unknown_connection_fail_closed(): void
    {
        $subscriptions = [[
            'id' => 40,
            'op' => 'payment.refunded',
            'entity' => 'payments',
            'belongs_to' => 12,
            'callback' => 'https://callback.test/owner-12',
        ]];
        $matcher = new ConnectionScopedInMemoryWebhookSubscriptionMatcher([
            'main' => $subscriptions,
        ]);

        $unresolved = iterator_to_array($matcher->findMatches(new WebhookEvent(
            'payment.refunded',
            'payments',
            ['amount' => 125],
            'payment-42',
            null,
            [
                'connection_id' => 'main',
                'owner_scope' => 'unresolved',
            ]
        )));
        $unknownConnection = iterator_to_array($matcher->findMatches(new WebhookEvent(
            'payment.refunded',
            'payments',
            ['amount' => 125],
            'payment-42',
            null,
            [
                'connection_id' => 'tenant-missing',
                'owner_scope' => 'single',
                'owner_field' => 'belongs_to',
                'owner_id' => 12,
            ]
        )));

        $this->assertSame([], $unresolved);
        $this->assertSame([], $unknownConnection);
    }

    public function test_real_connection_selector_scopes_and_restores_configured_connections(): void
    {
        $configData = new \ReflectionProperty(Config::class, 'data');
        $currentConnection = new \ReflectionProperty(DB::class, 'current_id_conn');
        $previousConfig = $configData->getValue();
        $previousConnection = $currentConnection->getValue();

        $configData->setValue(null, [
            'db_connection_default' => 'main',
            'db_connections' => [
                'main' => [],
                'tenant-a' => [],
                'tenant-b' => [],
            ],
        ]);

        try {
            $currentConnection->setValue(null, 'tenant-b');
            $matcher = new ConfiguredConnectionWebhookSubscriptionMatcher([
                'tenant-a' => [
                    [
                        'id' => 51,
                        'op' => 'payment.refunded',
                        'entity' => 'payments',
                        'belongs_to' => 12,
                        'callback' => 'https://callback.test/tenant-a-owner-12',
                    ],
                    [
                        'id' => 52,
                        'op' => 'payment.refunded',
                        'entity' => 'payments',
                        'belongs_to' => 13,
                        'callback' => 'https://callback.test/tenant-a-owner-13',
                    ],
                ],
                'tenant-b' => [[
                    'id' => 53,
                    'op' => 'payment.refunded',
                    'entity' => 'payments',
                    'belongs_to' => 12,
                    'callback' => 'https://callback.test/tenant-b-owner-12',
                ]],
            ]);

            $event = new WebhookEvent(
                'payment.refunded',
                'payments',
                ['amount' => 125],
                'payment-42',
                8,
                [
                    'connection_id' => 'tenant-a',
                    'owner_scope' => 'single',
                    'owner_field' => 'belongs_to',
                    'owner_id' => 12,
                ]
            );

            $matches = iterator_to_array($matcher->findMatches($event));

            $this->assertSame([51], array_column(array_column($matches, 'subscription'), 'id'));
            $this->assertSame(['tenant-a'], $matcher->connectionsRead);
            $this->assertSame('tenant-b', DB::getCurrentConnectionId());

            $missingConnection = iterator_to_array($matcher->findMatches(new WebhookEvent(
                'payment.refunded',
                'payments',
                ['amount' => 125],
                'payment-43',
                8,
                ['owner_scope' => 'global']
            )));

            $this->assertSame([], $missingConnection);
            $this->assertSame(['tenant-a'], $matcher->connectionsRead);
            $this->assertSame('tenant-b', DB::getCurrentConnectionId());

            $unknownConnection = iterator_to_array($matcher->findMatches(new WebhookEvent(
                'payment.refunded',
                'payments',
                ['amount' => 125],
                'payment-44',
                8,
                [
                    'connection_id' => 'tenant-missing',
                    'owner_scope' => 'global',
                ]
            )));

            $this->assertSame([], $unknownConnection);
            $this->assertSame(['tenant-a'], $matcher->connectionsRead);
            $this->assertSame('tenant-b', DB::getCurrentConnectionId());

            $currentConnection->setValue(null, null);
            iterator_to_array($matcher->findMatches($event));

            $this->assertSame('main', DB::getCurrentConnectionId());
            $this->assertSame(['tenant-a', 'tenant-a'], $matcher->connectionsRead);
        } finally {
            $currentConnection->setValue(null, $previousConnection);
            $configData->setValue(null, $previousConfig);
        }
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
