<?php

namespace Boctulus\Simplerest\Tests\Command;

use Boctulus\Simplerest\Core\Commands\BaseCommand;
use Boctulus\Simplerest\Core\Libs\CommandRegistry;
use PHPUnit\Framework\TestCase;

class CommandRegistryDispatchTest extends TestCase
{
    public function test_flags_without_a_command_show_group_help_without_dispatching(): void
    {
        foreach ([['--help'], ['-h'], ['--dry-run']] as $args) {
            [$registry, $command] = $this->createRegistry();

            ob_start();
            $registry->dispatch('tools', $args);
            $output = ob_get_clean();

            $this->assertFalse($command->executed);
            $this->assertStringContainsString("Comandos del grupo 'tools':", $output);
            $this->assertStringNotContainsString('Comando: php com tools list', $output);
        }
    }

    public function test_help_after_a_command_shows_command_details(): void
    {
        [$registry, $command] = $this->createRegistry();

        ob_start();
        $registry->dispatch('tools', ['list', '--help']);
        $output = ob_get_clean();

        $this->assertStringContainsString('Comando: php com tools list', $output);
        $this->assertFalse($command->executed);
    }

    private function createRegistry(): array
    {
        $registry = new CommandRegistry();
        $command = new RegistryDispatchFixtureCommand();
        $command->group = 'tools';
        $command->command = 'list';
        $command->description = 'List tools';

        $reflection = new \ReflectionClass(CommandRegistry::class);
        $commands = $reflection->getProperty('commands');
        $commands->setValue($registry, ['tools:list' => $command]);

        $groups = $reflection->getProperty('groups');
        $groups->setValue($registry, ['tools' => ['list']]);

        return [$registry, $command];
    }
}

class RegistryDispatchFixtureCommand extends BaseCommand
{
    public bool $executed = false;

    public function execute(array $parsed): void
    {
        $this->executed = true;
    }
}
