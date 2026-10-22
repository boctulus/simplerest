<?php

declare(strict_types=1);

use Boctulus\Simplerest\CodeTools\Cleaners\AngularCleaner;
use Boctulus\Simplerest\CodeTools\Cleaners\BootstrapCleaner;
use Boctulus\Simplerest\Core\Libs\PHPParser;
use PHPUnit\Framework\TestCase;

final class CodeToolsPackageTest extends TestCase
{
    public function testReduceCodeKeepsRequestedMethodsAndDropsOtherMethods(): void
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

    public function testBootstrapCleanerRemovesFrameworkClassNamesAndPreservesCustomClass(): void
    {
        $cleaned = BootstrapCleaner::remove('<button class="btn btn-primary custom-action">Run</button>');

        self::assertStringNotContainsString('btn-primary', $cleaned);
        self::assertStringContainsString('custom-action', $cleaned);
    }

    public function testAngularCleanerRemovesAngularArtifactsAndKeepsApplicationMarkup(): void
    {
        $cleaned = AngularCleaner::remove('<div data-ng-if="ready" class="ng-pristine ng-hide ng-pristine-extra custom-state"></div>');

        self::assertStringNotContainsString('data-ng-if', $cleaned);
        self::assertDoesNotMatchRegularExpression('/(?<![A-Za-z0-9_-])ng-pristine(?![A-Za-z0-9_-])/', $cleaned);
        self::assertStringContainsString('d-none', $cleaned);
        self::assertStringContainsString('ng-pristine-extra', $cleaned);
        self::assertStringContainsString('custom-state', $cleaned);
    }
}
