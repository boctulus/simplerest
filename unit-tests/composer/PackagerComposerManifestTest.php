<?php

namespace Boctulus\Simplerest\Tests\Composer;

use PHPUnit\Framework\TestCase;

class PackagerComposerManifestTest extends TestCase
{
    private ?string $temporaryDirectory = null;

    protected function tearDown(): void
    {
        if ($this->temporaryDirectory !== null) {
            $this->removeDirectory($this->temporaryDirectory);
        }

        parent::tearDown();
    }

    public function test_curated_starter_keeps_runtime_dependencies_and_dotenv(): void
    {
        $repositoryRoot = dirname(__DIR__, 2);
        require_once $repositoryRoot . DIRECTORY_SEPARATOR . 'scripts' . DIRECTORY_SEPARATOR
            . 'pack_framework.php';

        $this->temporaryDirectory = sys_get_temp_dir() . DIRECTORY_SEPARATOR
            . 'simplerest-packager-manifest-' . bin2hex(random_bytes(8));
        mkdir($this->temporaryDirectory . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'framework', 0777, true);
        mkdir($this->temporaryDirectory . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'Controllers', 0777, true);
        copy($repositoryRoot . DIRECTORY_SEPARATOR . 'composer.json', $this->temporaryDirectory . DIRECTORY_SEPARATOR . 'composer.json');

        ob_start();
        try {
            $packager = new \SimpleRestPackager($repositoryRoot, $this->temporaryDirectory);
            $method = new \ReflectionMethod($packager, 'processComposerJson');
            $method->invoke($packager);
        } finally {
            ob_end_clean();
        }

        $manifest = json_decode(
            file_get_contents($this->temporaryDirectory . DIRECTORY_SEPARATOR . 'composer.json'),
            true,
            512,
            JSON_THROW_ON_ERROR
        );

        $this->assertSame('>=8.1,<8.5', $manifest['require']['php']);
        $this->assertSame('^2.0', $manifest['require']['doctrine/inflector']);
        $this->assertSame('^2.0', $manifest['require']['psr/http-message']);
        $this->assertSame('^7.1', $manifest['require']['firebase/php-jwt']);
        $this->assertSame('^6.4', $manifest['require']['symfony/dom-crawler']);
        $this->assertSame('^6.4', $manifest['require']['symfony/css-selector']);
        $this->assertSame('^5.2', $manifest['require']['vlucas/phpdotenv']);
        $this->assertArrayNotHasKey('phpmailer/phpmailer', $manifest['require']);
        $this->assertArrayNotHasKey('require-dev', $manifest);
    }

    private function removeDirectory(string $directory): void
    {
        if (!is_dir($directory)) {
            return;
        }

        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($directory, \FilesystemIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::CHILD_FIRST
        );

        foreach ($iterator as $item) {
            $item->isDir() ? rmdir($item->getPathname()) : unlink($item->getPathname());
        }

        rmdir($directory);
    }
}
