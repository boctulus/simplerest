<?php

use Boctulus\Simplerest\Core\Commands\BaseCommand;
use Boctulus\Simplerest\Core\Libs\Files;

abstract class BaseFileCommand extends BaseCommand
{
    protected function getFileEntries(array $parsed): array
    {
        $dir     = $parsed['_positional'][0] ?? $this->opt($parsed, 'dir', '.');
        $pattern = $this->opt($parsed, 'pattern', '*.*');
        $exclude = $this->opt($parsed, 'exclude', null);

        $recursive    = $this->opt($parsed, 'recursive', false);
        $onlyDirs     = $this->opt($parsed, 'only_dirs', false);
        $includeDirs  = $this->opt($parsed, 'include_dirs', false);

        $flags = 0;

        if ($recursive) {
            $flags |= GLOB_RECURSE;
        }

        if ($onlyDirs) {
            $flags |= GLOB_ONLYDIR;
        }

        $dir = Files::addTrailingSlash($dir);

        if (!is_dir($dir)) {
            return [];
        }

        $excludeArr = [];
        if (!empty($exclude)) {
            $excludeArr = array_map('trim', explode('|', $exclude));
        }

        if ($onlyDirs) {
            $entries = glob($dir . '*', GLOB_ONLYDIR);
        } else {
            $entries = Files::glob($dir, $pattern, $flags, $excludeArr);
        }

        if (!$includeDirs && !$onlyDirs) {
            $entries = array_filter($entries, fn($e) => is_file($e));
        }

        sort($entries);

        return array_values($entries);
    }
}
