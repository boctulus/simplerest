<?php

namespace Boctulus\Simplerest\Tests\Files;

use Boctulus\Simplerest\Core\Libs\FileUploader;
use PHPUnit\Framework\Attributes\PreserveGlobalState;
use PHPUnit\Framework\Attributes\RunInSeparateProcess;
use PHPUnit\Framework\TestCase;

final class FileUploaderTypeValidationTest extends TestCase
{
    #[RunInSeparateProcess]
    #[PreserveGlobalState(false)]
    public function test_host_configuration_overrides_the_default_allowlist(): void
    {
        $root = $this->configureApplication([
            'files' => [
                'media_accepted' => [
                    'extensions' => ['.CSV'],
                    'mime_types' => ['TEXT/PLAIN'],
                ],
            ],
        ]);

        try {
            $uploader = new InspectableFileUploader();

            self::assertSame(['csv'], $uploader->allowedExtensions());
            self::assertSame(['text/plain'], $uploader->allowedMimeTypes());
        } finally {
            $this->removeDirectory($root);
        }
    }

    #[RunInSeparateProcess]
    #[PreserveGlobalState(false)]
    public function test_fallback_allowlist_works_without_application_configuration(): void
    {
        $root = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'simplerest-uploader-' . bin2hex(random_bytes(8));
        mkdir($root, 0777, true);
        define('UPLOADS_PATH', $root . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR);

        try {
            $uploader = new InspectableFileUploader();

            self::assertContains('jpg', $uploader->allowedExtensions());
            self::assertContains('image/jpeg', $uploader->allowedMimeTypes());
        } finally {
            $this->removeDirectory($root);
        }
    }

    #[RunInSeparateProcess]
    #[PreserveGlobalState(false)]
    public function test_extension_and_sniffed_mime_must_both_be_allowed(): void
    {
        $root = $this->configureApplication();
        $sample = $root . DIRECTORY_SEPARATOR . 'sample.txt';
        file_put_contents($sample, "ordinary text\n");

        try {
            $uploader = InspectableFileUploader::withoutConstructor();
            $uploader->setUploadedFileForTest(true);
            $uploader->setAllowedTypes(['.TXT'], ['TEXT/PLAIN']);

            self::assertTrue($uploader->typeAllowed($sample, 'report.txt'));
            self::assertFalse($uploader->typeAllowed($sample, 'report.php'));

            $uploader->setAllowedTypes(['jpg'], ['image/jpeg']);
            self::assertFalse($uploader->typeAllowed($sample, 'report.jpg'));

            $uploader->setMimeTypeForTest(null);
            self::assertFalse($uploader->typeAllowed($sample, 'report.jpg'));
        } finally {
            $this->removeDirectory($root);
        }
    }

    #[RunInSeparateProcess]
    #[PreserveGlobalState(false)]
    public function test_do_upload_rejects_a_local_temporary_file_as_a_fake_http_upload(): void
    {
        $root = $this->configureApplication();
        $tmpPath = $root . DIRECTORY_SEPARATOR . 'not-an-upload.txt';
        $destination = $root . DIRECTORY_SEPARATOR . 'stored';
        file_put_contents($tmpPath, "ordinary text\n");
        $originalFiles = $_FILES;

        try {
            $uploader = (new FileUploader())
                ->setLocation($destination)
                ->setAllowedTypes(['txt'], ['text/plain']);

            $_FILES = [
                'document' => [
                    'name' => 'document.txt',
                    'type' => 'text/plain',
                    'tmp_name' => $tmpPath,
                    'error' => UPLOAD_ERR_OK,
                    'size' => filesize($tmpPath),
                ],
            ];

            $uploader->doUpload('document');

            self::assertSame([], $uploader->getFileNames());
            self::assertCount(1, $uploader->getErrors());
            self::assertSame('document.txt', $uploader->getErrors()[0]['ori_name']);
            self::assertFileExists($tmpPath);
            self::assertSame([], array_values(array_diff(scandir($destination) ?: [], ['.', '..'])));
        } finally {
            $_FILES = $originalFiles;
            $this->removeDirectory($root);
        }
    }

    #[RunInSeparateProcess]
    #[PreserveGlobalState(false)]
    public function test_failed_move_is_not_reported_as_a_successful_upload(): void
    {
        $root = $this->configureApplication();
        $tmpPath = $root . DIRECTORY_SEPARATOR . 'document.txt';
        $destination = $root . DIRECTORY_SEPARATOR . 'stored';
        file_put_contents($tmpPath, "ordinary text\n");
        $originalFiles = $_FILES;

        try {
            $uploader = (new InspectableFileUploader())
                ->setAllowedTypes(['txt'], ['text/plain'])
                ->setLocation($destination);
            $uploader->setUploadedFileForTest(true);

            $_FILES = [
                'document' => [
                    'name' => 'document.txt',
                    'type' => 'text/plain',
                    'tmp_name' => $tmpPath,
                    'error' => UPLOAD_ERR_OK,
                    'size' => filesize($tmpPath),
                ],
            ];

            $uploader->doUpload('document');

            self::assertSame([], $uploader->getFileNames());
            self::assertCount(1, $uploader->getErrors());
            self::assertFileExists($tmpPath);
            self::assertSame([], array_values(array_diff(scandir($destination) ?: [], ['.', '..'])));
        } finally {
            $_FILES = $originalFiles;
            $this->removeDirectory($root);
        }
    }

    private function configureApplication(array $config = []): string
    {
        $root = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'simplerest-uploader-' . bin2hex(random_bytes(8));
        $configDirectory = $root . DIRECTORY_SEPARATOR . 'config';
        mkdir($configDirectory, 0777, true);

        file_put_contents(
            $configDirectory . DIRECTORY_SEPARATOR . 'config.php',
            '<?php return ' . var_export($config, true) . ';'
        );
        file_put_contents($configDirectory . DIRECTORY_SEPARATOR . 'databases.php', '<?php return [];');

        define('CONFIG_PATH', $configDirectory . DIRECTORY_SEPARATOR);
        define('UPLOADS_PATH', $root . DIRECTORY_SEPARATOR . 'default-uploads' . DIRECTORY_SEPARATOR);

        return $root;
    }

    private function removeDirectory(string $path): void
    {
        if (!is_dir($path)) {
            return;
        }

        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($path, \FilesystemIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::CHILD_FIRST
        );

        foreach ($iterator as $item) {
            $item->isDir() ? rmdir($item->getPathname()) : unlink($item->getPathname());
        }

        rmdir($path);
    }
}

final class InspectableFileUploader extends FileUploader
{
    private bool $uploadedFileForTest = false;
    private ?string $mimeTypeForTest = null;
    private bool $overrideMimeTypeForTest = false;

    public static function withoutConstructor(): self
    {
        return (new \ReflectionClass(self::class))->newInstanceWithoutConstructor();
    }

    public function typeAllowed(string $tmpPath, string $clientName): bool
    {
        return $this->isTypeAllowed($tmpPath, $clientName);
    }

    public function allowedExtensions(): array
    {
        return $this->allowedExtensions;
    }

    public function allowedMimeTypes(): array
    {
        return $this->allowedMimeTypes;
    }

    public function setUploadedFileForTest(bool $uploaded): void
    {
        $this->uploadedFileForTest = $uploaded;
    }

    public function setMimeTypeForTest(?string $mimeType): void
    {
        $this->overrideMimeTypeForTest = true;
        $this->mimeTypeForTest = $mimeType;
    }

    protected function isUploadedFile(string $tmpPath): bool
    {
        return $this->uploadedFileForTest;
    }

    protected function detectMimeType(string $tmpPath): ?string
    {
        if ($this->overrideMimeTypeForTest) {
            return $this->mimeTypeForTest;
        }

        return parent::detectMimeType($tmpPath);
    }
}
