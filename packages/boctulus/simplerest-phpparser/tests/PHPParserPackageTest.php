<?php

declare(strict_types=1);

use Boctulus\Simplerest\Core\Libs\PHPParser;
use PHPUnit\Framework\TestCase;

final class PHPParserPackageTest extends TestCase
{
    public function testReduceCodeKeepsRequestedMethodsAndDropsOthers(): void
    {
        $source = <<<'PHP'
<?php
class Example {
    public function keepMe(): string { return 'kept'; }
    public function removeMe(): string { return 'removed'; }
}
PHP;

        $reduced = (new PHPParser())->reduceCode($source, ['keepMe']);

        self::assertStringContainsString('function keepMe()', $reduced);
        self::assertStringNotContainsString('function removeMe()', $reduced);
    }

    public function testReduceCodeReportsInvalidPhpAsRuntimeException(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Parse error:');

        (new PHPParser())->reduceCode('<?php class Example {', []);
    }
}
