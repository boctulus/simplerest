<?php

namespace Boctulus\Simplerest\Tests\Files;

use Boctulus\Simplerest\Core\Libs\Files;
use Boctulus\Simplerest\Core\Libs\System;
use PHPUnit\Framework\TestCase;

class FilesWritablePathTest extends TestCase
{
    public function test_writable_or_fail_keeps_original_path_when_realpath_fails(): void
    {
        if (System::isWindows()) {
            $this->markTestSkipped('The permission check short-circuits on Windows.');
        }

        $path = sys_get_temp_dir()
            . DIRECTORY_SEPARATOR
            . 'simplerest-missing-' . bin2hex(random_bytes(8))
            . DIRECTORY_SEPARATOR
            . 'document.txt';

        self::assertDirectoryDoesNotExist(dirname($path));

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage("Permission error. Path '{$path}' is not writable");

        Files::writableOrFail($path);
    }
}
