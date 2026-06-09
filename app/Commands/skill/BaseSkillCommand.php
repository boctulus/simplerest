<?php

use Boctulus\Simplerest\Core\Commands\BaseCommand;

abstract class BaseSkillCommand extends BaseCommand
{
    public function __construct()
    {
        $this->group = 'skill';
    }

    protected function getAgentDir(string $agent = 'agent'): string
    {
        $agentDirMap = [
            'agent'  => '.agent',
            'claude' => '.claude',
            'qwen'   => '.qwen',
        ];

        return $agentDirMap[$agent] ?? ".{$agent}";
    }

    protected function getSkillsDirectory(string $agentDir): array
    {
        $skillsPath = $agentDir . DIRECTORY_SEPARATOR . 'skills';

        if (!is_dir($skillsPath)) {
            return [];
        }

        $entries = scandir($skillsPath);
        $skills  = [];

        foreach ($entries as $entry) {
            if ($entry === '.' || $entry === '..') {
                continue;
            }

            $skillPath = $skillsPath . DIRECTORY_SEPARATOR . $entry;

            if (is_dir($skillPath)) {
                $skills[] = [
                    'name' => $entry,
                    'path' => $skillPath,
                ];
            }
        }

        return $skills;
    }

    protected function parseSkillFrontmatter(string $filePath): ?array
    {
        if (!is_file($filePath)) {
            return null;
        }

        $content = file_get_contents($filePath);

        if ($content === false) {
            return null;
        }

        $frontmatterRegex = '/^---\r?\n(.*?)\r?\n---/s';

        if (!preg_match($frontmatterRegex, $content, $match)) {
            return null;
        }

        $frontmatterContent = $match[1];
        $result             = ['raw' => $frontmatterContent];

        if (preg_match('/^name:\s*(.+?)\r?$/m', $frontmatterContent, $nameMatch)) {
            $result['name'] = trim($nameMatch[1]);
        }

        if (preg_match('/^description:\s*(.+?)\r?$/m', $frontmatterContent, $descMatch)) {
            $result['description'] = trim($descMatch[1]);
        }

        return $result;
    }

    protected function getFileSizeKB(string $filePath): ?string
    {
        if (!is_file($filePath)) {
            return null;
        }

        $size = filesize($filePath);

        if ($size === false) {
            return null;
        }

        return number_format($size / 1024, 2);
    }

    protected function toKebabCase(string $str): string
    {
        $str = preg_replace('/([a-z])([A-Z])/u', '$1-$2', $str);
        $str = preg_replace('/\s+/u', '-', $str);
        return mb_strtolower($str, 'UTF-8');
    }

    protected function ensureDirectoryExists(string $dirPath): void
    {
        if (!is_dir($dirPath)) {
            mkdir($dirPath, 0777, true);
            $this->log("Directorio creado: {$dirPath}", 'success');
        } else {
            $this->log("Directorio ya existe: {$dirPath}", 'info');
        }
    }

    protected function skillFileExists(string $filePath): bool
    {
        return is_file($filePath);
    }

    protected function writeSkillFile(string $filePath, string $content, bool $force = false): bool
    {
        if (is_file($filePath) && !$force) {
            $this->log("El archivo ya existe: {$filePath}", 'warning');
            return false;
        }

        file_put_contents($filePath, $content);
        $this->log("Archivo creado: {$filePath}", 'success');
        return true;
    }

    protected function readSkillContent(string $filePath): string
    {
        if (!is_file($filePath)) {
            return '';
        }

        $content = file_get_contents($filePath);
        return $content !== false ? $content : '';
    }

    protected function extractDependencies(string $content): array
    {
        $dependencies = [];

        $bulletSectionPatterns = [
            '/## REQUIRES\s*\(HARD DEPENDENCIES\)\s*\n(.*?)(?=##|$)/si',
            '/## SKILLS USED\s*.*?\n(.*?)(?=##|$)/si',
            '/## Complementary Skills\s*\n(.*?)(?=##|$)/si',
            '/## Related Skills\s*\n(.*?)(?=##|$)/si',
        ];

        foreach ($bulletSectionPatterns as $sectionRegex) {
            if (preg_match($sectionRegex, $content, $sectionMatch)) {
                $section = $sectionMatch[1];

                if (preg_match_all('/^-\s+([a-z][a-z0-9-]*)/m', $section, $skillMatches)) {
                    foreach ($skillMatches[1] as $skill) {
                        $dependencies[$skill] = true;
                    }
                }
            }
        }

        if (preg_match('/## SKILL ORDER EXECUTION\s*\n(.*?)(?=##|$)/si', $content, $orderMatch)) {
            $orderSection = $orderMatch[1];

            if (preg_match_all('/^\d+\.\s+([a-z][a-z0-9-]*)/m', $orderSection, $numMatches)) {
                foreach ($numMatches[1] as $skill) {
                    $dependencies[$skill] = true;
                }
            }
        }

        return array_keys($dependencies);
    }

    protected function normalizeSkillName(string $name): string
    {
        return $this->toKebabCase($name);
    }
}
