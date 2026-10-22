<?php

namespace Boctulus\Simplerest\tests\Validator;

require_once __DIR__ . '/../../src/framework/Helpers/i18n.php';

use Boctulus\Simplerest\Core\Libs\Validator;
use PHPUnit\Framework\TestCase;

class ValidatorSkipBoundsContractTest extends TestCase
{
    private function newValidator(): Validator
    {
        // The production constructor binds the application translation domain.
        // These tests exercise validation rules without booting the application.
        return (new \ReflectionClass(Validator::class))->newInstanceWithoutConstructor();
    }

    public function test_read_filters_skip_string_and_numeric_write_bounds(): void
    {
        $validator = $this->newValidator();
        $valid = $validator
            ->setRequired(false)
            ->skipBoundsChecks(true)
            ->validate(
                [
                    'username' => 'long@example.com',
                    'age' => '99',
                ],
                [
                    'username' => ['type' => 'string', 'max' => 10],
                    'age' => ['type' => 'int', 'min' => 18, 'max' => 65, 'between' => [18, 65]],
                ]
            );

        $this->assertTrue($valid);
        $this->assertSame([], $validator->getErrors());
    }

    public function test_read_filters_still_enforce_value_types(): void
    {
        $validator = $this->newValidator();
        $valid = $validator
            ->setRequired(false)
            ->skipBoundsChecks(true)
            ->validate(
                ['age' => 'not-a-number'],
                ['age' => ['type' => 'int', 'min' => 18]]
            );

        $this->assertFalse($valid);
        $this->assertArrayHasKey('age', $validator->getErrors());
        $this->assertSame('type', $validator->getErrors()['age'][0]['error']);
    }
}
