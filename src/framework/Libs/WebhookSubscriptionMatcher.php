<?php

namespace Boctulus\Simplerest\Core\Libs;

class WebhookSubscriptionMatcher
{
    /**
     * Return matching subscriptions with the data to include in each delivery.
     *
     * Each match contains the subscription row and its delivery data. The
     * ordering and condition behavior mirror ApiController::webhook().
     */
    public function findMatches(WebhookEvent $event): \Generator
    {
        $webhooks = $this->loadSubscriptions($event);

        $oldData = null;
        $deliveryData = $this->sanitizeEventData($event, $event->getData());

        foreach ($webhooks as $hook) {
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

                    yield [
                        'subscription' => $hook,
                        'data' => $deliveryData
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
            }

            if (empty($hook['conditions'])) {
                yield [
                    'subscription' => $hook,
                    'data' => $deliveryData
                ];
            } elseif (
                $event->getEventType() !== 'list'
                && Strings::filter($deliveryData, $conditions)
            ) {
                yield [
                    'subscription' => $hook,
                    'data' => $deliveryData
                ];
            }
        }
    }

    protected function loadSubscriptions(WebhookEvent $event): iterable
    {
        DB::getDefaultConnection();

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
