<?php

declare(strict_types=1);

use Boctulus\Simplerest\CodeTools\Commands\make\MakeApiCommand;
use Boctulus\Simplerest\CodeTools\Commands\make\MakeControllerCommand;
use Boctulus\Simplerest\CodeTools\Commands\make\MakeMigrationFileCommand;
use Boctulus\Simplerest\CodeTools\Commands\make\MakeModelCommand;
use PHPUnit\Framework\TestCase;

final class ScaffoldingPackageTest extends TestCase
{
    public function testRepresentativeMakeCommandsAutoloadFromThePackage(): void
    {
        foreach ([
            MakeModelCommand::class,
            MakeControllerCommand::class,
            MakeApiCommand::class,
            MakeMigrationFileCommand::class,
        ] as $command) {
            self::assertTrue(class_exists($command), $command);
        }
    }
}
