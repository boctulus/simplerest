<?php

use Boctulus\Simplerest\Core\Libs\Files;

require_once __DIR__ . '/BaseFileCommand.php';

class FileCopyCommand extends BaseFileCommand
{
    public function __construct()
    {
        $this->command     = 'copy';
        $this->description = 'Copia un archivo o directorio, creando el destino cuando sea necesario';
        $this->aliases     = ['cp'];
        $this->examples    = [
            'php com file copy .\\agents\\skills\\git-worktree-policies\\SKILL.md ..\\directory\\.agents\\skills\\git-worktree-policies\\SKILL.md',
            'php com file copy agents\\skills\\git-worktree-policies\\SKILL.md ..\\directory\\.agents\\skills\\git-worktree-policies\\',
            'php com file copy D:\\source\\assets D:\\backup\\assets',
        ];
    }

    public static function config(): array
    {
        return [
            'required' => [],
            'optional' => [],
            'flags'    => [],
            'options'  => [],
        ];
    }

    public function execute(array $parsed): void
    {
        $args = $parsed['_positional'] ?? [];

        if (count($args) !== 2) {
            echo "✗ Uso: php com file copy {origin} {destination}\n";
            $this->showUsage();
            return;
        }

        [$origin, $destination] = $args;

        if (!file_exists($origin)) {
            echo "✗ El origen '{$origin}' no existe.\n";
            return;
        }

        if (!is_readable($origin)) {
            echo "✗ El origen '{$origin}' no es legible.\n";
            return;
        }

        if (is_file($origin)) {
            $this->copyFile($origin, $destination);
            return;
        }

        if (is_dir($origin)) {
            $this->copyDirectory($origin, $destination);
            return;
        }

        echo "✗ El origen '{$origin}' no es un archivo ni un directorio.\n";
    }

    private function copyFile(string $origin, string $destination): void
    {
        $isDestinationDirectory = is_dir($destination) || $this->hasTrailingSeparator($destination);
        $directory = $isDestinationDirectory ? $destination : dirname($destination);

        Files::mkDirOrFail($directory);
        Files::isDirectoryWritableOrFail($directory);

        $target = $isDestinationDirectory
            ? Files::addTrailingSlash($directory) . basename($origin)
            : $destination;

        if (!Files::isWritable($target)) {
            throw new \Exception("El destino '{$target}' no es escribible");
        }

        if (!Files::cp($origin, $target)) {
            throw new \Exception("No fue posible copiar '{$origin}' a '{$target}'");
        }
    }

    private function copyDirectory(string $origin, string $destination): void
    {
        if (file_exists($destination) && !is_dir($destination)) {
            throw new \InvalidArgumentException("El destino '{$destination}' debe ser un directorio");
        }

        Files::mkDirOrFail($destination);
        Files::isDirectoryWritableOrFail($destination);
        Files::copy($origin, $destination);
    }

    private function hasTrailingSeparator(string $path): bool
    {
        return str_ends_with($path, '/') || str_ends_with($path, '\\');
    }
}
