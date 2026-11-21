<?php

namespace Boctulus\Simplerest\Core\Libs;

class WebhookSubscriptionMatcher
{
    private const SUBSCRIPTION_OWNER_FIELD = 'belongs_to';

    /**
     * Return matching subscriptions with the data to include in each delivery.
     *
     * Each match contains the subscription row and its delivery data. The
     * ordering and condition behavior mirror ApiController::webhook().
     */
    public function findMatches(WebhookEvent $event): \Generator
    {
        $connection = $this->selectConnectionForEvent($event);
        if ($connection === null) {
            return;
        }

        try {
            yield from $this->findMatchesWithinScope($event);
        } finally {
            $this->restoreConnection($connection);
        }
    }

    protected function findMatchesWithinScope(WebhookEvent $event): \Generator
    {
        $scope = $event->getScopeContext();
        $ownerScope = $scope['owner_scope'] ?? null;
        $ownerField = $scope['owner_field'] ?? null;
        $isSingleOwner = $ownerScope === 'single';
        $isOwnerRows = $ownerScope === 'rows';

        if (!in_array($ownerScope, ['global', 'single', 'rows'], true)) {
            return;
        }

        if (
            $isSingleOwner
            && (
                !is_string($ownerField)
                || !preg_match('/^[A-Za-z_][A-Za-z0-9_]*$/', $ownerField)
                || !array_key_exists('owner_id', $scope)
                || (!is_scalar($scope['owner_id']) && $scope['owner_id'] !== null)
            )
        ) {
            return;
        }

        $deliveryData = $this->sanitizeEventData($event, $event->getData());
        if ($isOwnerRows) {
            if (
                $event->getEventType() !== 'list'
                || !is_string($ownerField)
                || !preg_match('/^[A-Za-z_][A-Za-z0-9_]*$/', $ownerField)
            ) {
                return;
            }

            foreach ($deliveryData as $row) {
                if (!is_array($row) || !array_key_exists($ownerField, $row)) {
                    return;
                }
            }
        }

        $webhooks = $this->loadSubscriptions($event);

        $oldData = null;

        foreach ($webhooks as $hook) {
            $subscriptionData = $deliveryData;

            if ($isSingleOwner) {
                if (
                    !array_key_exists(self::SUBSCRIPTION_OWNER_FIELD, $hook)
                    || !$this->sameOwner(
                        $hook[self::SUBSCRIPTION_OWNER_FIELD],
                        $scope['owner_id']
                    )
                ) {
                    continue;
                }
            } elseif ($isOwnerRows) {
                if (!array_key_exists(self::SUBSCRIPTION_OWNER_FIELD, $hook)) {
                    continue;
                }

                $subscriptionData = $this->filterRowsForOwner(
                    $deliveryData,
                    $ownerField,
                    $hook[self::SUBSCRIPTION_OWNER_FIELD]
                );

                if (empty($subscriptionData)) {
                    continue;
                }
            }

            $conditions = [];
            if (!empty($hook['conditions'])) {
                parse_str($hook['conditions'], $conditions);
            }

            if (
                $event->getEventType() === 'update'
                && !empty($hook['conditions'])
            ) {
                $conditionFields = array_keys($conditions);
                $conditionFields = array_unique($conditionFields);
                $rowFields = array_keys($deliveryData);

                if (count(array_diff($conditionFields, $rowFields)) === 0
                    && Strings::filter($deliveryData, $conditions)) {
                    if ($oldData === null) {
                        $oldData = $this->loadPreviousEntityData($event);
                        $deliveryData = array_merge($oldData, $deliveryData);
                    }
                    $subscriptionData = $deliveryData;

                    yield [
                        'subscription' => $hook,
                        'data' => $subscriptionData
                    ];
                }

                continue;
            }

            if (
                $event->getEventType() === 'update'
                || $event->getEventType() === 'delete'
                || (
                    $event->getEventType() === 'show'
                    && $event->showFieldsRequested()
                )
            ) {
                if ($oldData === null) {
                    $oldData = $this->loadPreviousEntityData($event);
                    $deliveryData = array_merge($oldData, $deliveryData);
                }

                $deliveryData = array_merge($oldData, $deliveryData);
                $subscriptionData = $deliveryData;
            }

            if (empty($hook['conditions'])) {
                yield [
                    'subscription' => $hook,
                    'data' => $subscriptionData
                ];
            } elseif (
                $event->getEventType() !== 'list'
                && Strings::filter($deliveryData, $conditions)
            ) {
                yield [
                    'subscription' => $hook,
                    'data' => $subscriptionData
                ];
            }
        }
    }

    protected function selectConnectionForEvent(WebhookEvent $event): ?array
    {
        $scope = $event->getScopeContext();
        $previous = DB::getCurrentConnectionId();
        $connectionId = $scope['connection_id'] ?? null;

        if ($connectionId === null) {
            $defaultConnectionId = DB::getDefaultConnectionId();
            if ($previous !== null && $previous !== $defaultConnectionId) {
                return null;
            }

            $connectionId = $defaultConnectionId;
        }

        if (!is_string($connectionId) || !DB::connectionExists($connectionId)) {
            return null;
        }

        try {
            if ($previous !== $connectionId) {
                DB::setConnection($connectionId);
            }
        } catch (\Throwable $e) {
            return null;
        }

        return [
            'previous' => $previous,
            'selected' => $connectionId,
        ];
    }

    protected function restoreConnection(array $connection): void
    {
        $previous = $connection['previous'];
        $selected = $connection['selected'];

        if ($previous !== null && $previous !== $selected) {
            DB::setConnection($previous);
        } elseif ($previous === null && $selected !== DB::getDefaultConnectionId()) {
            DB::setConnection(DB::getDefaultConnectionId());
        }
    }

    protected function loadSubscriptions(WebhookEvent $event): iterable
    {
        return DB::table('webhooks')
            ->where([
                'op' => $event->getEventType(),
                'entity' => $event->getEntity()
            ])
            ->get();
    }

    protected function loadEntityData(WebhookEvent $event)
    {
        return DB::table($event->getEntity())
            ->assoc()
            ->find($event->getId())
            ->deleted()
            ->first();
    }

    private function filterRowsForOwner(array $rows, string $ownerField, $ownerId): array
    {
        return array_values(array_filter(
            $rows,
            fn (array $row): bool => $this->sameOwner($row[$ownerField], $ownerId)
        ));
    }

    private function sameOwner($left, $right): bool
    {
        if ($left === null || $right === null) {
            return $left === null && $right === null;
        }

        if (!is_scalar($left) || !is_scalar($right)) {
            return false;
        }

        return (string) $left === (string) $right;
    }

    private function loadPreviousEntityData(WebhookEvent $event): ?array
    {
        $data = $this->loadEntityData($event);

        return is_array($data)
            ? $this->sanitizeEventData($event, $data)
            : null;
    }

    private function sanitizeEventData(WebhookEvent $event, array $data): array
    {
        if (strtolower($event->getEntity()) === 'webhooks') {
            unset($data['secret']);

            foreach ($data as $key => $value) {
                if (is_array($value)) {
                    $data[$key] = $this->sanitizeEventData($event, $value);
                }
            }
        }

        return $data;
    }
}
