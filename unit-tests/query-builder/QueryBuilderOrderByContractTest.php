<?php

namespace Boctulus\Simplerest\tests;

use Boctulus\Simplerest\Core\Traits\QueryBuilderTrait;
use PHPUnit\Framework\TestCase;

final class OrderByTraitHarness
{
    use QueryBuilderTrait;

    public function getOrder(): array
    {
        return $this->order;
    }
}

class QueryBuilderOrderByContractTest extends TestCase
{
    private function newBuilder(): OrderByTraitHarness
    {
        return new OrderByTraitHarness();
    }

    public function test_order_by_accepts_a_single_field_and_direction(): void
    {
        $order = $this->newBuilder()->orderBy('cost', 'DESC')->getOrder();

        $this->assertSame(['cost' => 'DESC'], $order);
    }

    public function test_order_by_keeps_associative_arrays_and_merges_calls(): void
    {
        $order = $this->newBuilder()
            ->orderBy(['cost' => 'ASC'])
            ->orderBy('id', 'DESC')
            ->getOrder();

        $this->assertSame(['cost' => 'ASC', 'id' => 'DESC'], $order);
    }

    public function test_order_by_rejects_invalid_direction_values(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $this->newBuilder()->orderBy('cost', 'DESC; DROP TABLE products');
    }

    public function test_order_by_rejects_invalid_array_entries(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $this->newBuilder()->orderBy(['cost' => 'DESC; DROP TABLE products']);
    }

    public function test_order_by_requires_a_direction_for_single_fields(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $this->newBuilder()->orderBy('cost');
    }

    public function test_order_by_does_not_accept_direction_for_an_array(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $this->newBuilder()->orderBy(['cost' => 'ASC'], 'DESC');
    }
}
