<?php

namespace Boctulus\Simplerest\tests\api;

use Boctulus\Simplerest\Core\Api\Collections;
use PHPUnit\Framework\TestCase;

final class CollectionEntityValidationHarness extends Collections
{
    public function validateEntity(string $entity, ?string $requestedEntity = null): ?array
    {
        return $this->entityValidationError($entity, $requestedEntity);
    }
}

class CollectionEntityValidationTest extends TestCase
{
    private function newController(): CollectionEntityValidationHarness
    {
        return (new \ReflectionClass(CollectionEntityValidationHarness::class))
            ->newInstanceWithoutConstructor();
    }

    public function test_regular_entity_is_available(): void
    {
        $this->assertNull($this->newController()->validateEntity('products'));
    }

    public function test_forbidden_entity_is_rejected(): void
    {
        $this->assertSame(
            ['Forbidden', 403, "Table 'roles' is not available for collections"],
            $this->newController()->validateEntity('roles')
        );
    }

    public function test_requested_entity_must_match_the_stored_entity(): void
    {
        $this->assertSame(
            ['Entity does not match the collection', 400, null],
            $this->newController()->validateEntity('products', 'customers')
        );
    }

    public function test_entity_comparison_is_case_insensitive(): void
    {
        $this->assertNull($this->newController()->validateEntity('Products', 'products'));
    }
}
