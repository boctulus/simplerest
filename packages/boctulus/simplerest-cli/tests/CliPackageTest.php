<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

/**
 * Structural checks. Runtime discovery needs a host application
 * (config/, helpers, constants) and is verified in a consumer project.
 */
final class CliPackageTest extends TestCase
{
    private const GROUPS = ['acl', 'file', 'migrations', 'module', 'mysql', 'roles', 'router', 'sql', 'system', 'users'];

    public function testEveryGroupShipsCommandClasses(): void
    {
        foreach (self::GROUPS as $group) {
            $files = array_filter(
                glob(dirname(__DIR__) . "/src/Commands/{$group}/*Command.php") ?: [],
                static fn (string $file): bool => !str_starts_with(basename($file), 'Base')
            );
            self::assertNotEmpty($files, "Group '{$group}' has no command files");

            foreach ($files as $file) {
                self::assertMatchesRegularExpression(
                    '/class\s+\w+\s+extends\s+\S*Command\b/',
                    file_get_contents($file),
                    basename($file) . ' must extend a command base class'
                );
            }
        }
    }

    public function testPackageContainsNoMonorepoToolingGroups(): void
    {
        foreach (['pack', 'todo', 'skill', 'doc'] as $group) {
            self::assertDirectoryDoesNotExist(dirname(__DIR__) . '/src/Commands/' . $group);
        }
    }
}
