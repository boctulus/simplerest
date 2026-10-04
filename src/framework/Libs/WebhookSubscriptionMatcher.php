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
        DB::getDefaultConnection();

        $webhooks = DB::table('webhooks')
            ->where([
                'op' => $event->getEventType(),
                'entity' => $event->getEntity()
            ])
            ->get();

        $oldData = null;
        $deliveryData = $event->getData();

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
                        $oldData = DB::table($event->getEntity())
                            ->assoc()
                            ->find($event->getId())
                            ->deleted()
                            ->first();
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
                    $oldData = DB::table($event->getEntity())
                        ->assoc()
                        ->find($event->getId())
                        ->deleted()
                        ->first();
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
}
