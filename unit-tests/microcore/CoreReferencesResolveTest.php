<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

/**
 * Every core class or function that src/framework references must be shipped
 * by src/framework itself. A reference to a class or function that only an
 * application or optional package defines makes the published core fatal for
 * every consumer (e.g. the removed Core\Libs\Impersonation imports in ApiController).
 */
final class CoreReferencesResolveTest extends TestCase
{
    /**
     * Platform or extension functions the core calls behind a runtime check.
     */
    private const GUARDED_PLATFORM_FUNCTIONS = [
        'sys_getloadavg', // Libs/System.php: non-Windows branch only
        'uuid_create',    // Traits/Uuids.php: behind function_exists()
    ];

    /**
     * Optional-package classes the core may import because every use is
     * behind class_exists(): file => [class relative to Boctulus\Simplerest\Core\].
     */
    private const GUARDED_OPTIONAL_IMPORTS = [
        'Libs/Validator.php' => ['Libs\\i18n\\Translate'], // boctulus/simplerest-i18n
    ];

    private static function frameworkPath(): string
    {
        return dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'framework';
    }

    /** @return iterable<string, string> relative path => contents */
    private static function coreFiles(): iterable
    {
        $root = self::frameworkPath();
        $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($root, FilesystemIterator::SKIP_DOTS));

        foreach ($iterator as $file) {
            $relative = str_replace(DIRECTORY_SEPARATOR, '/', substr($file->getPathname(), strlen($root) + 1));
            if ($file->getExtension() !== 'php' || str_starts_with($relative, 'Templates/')) {
                continue;
            }
            yield $relative => (string) file_get_contents($file->getPathname());
        }
    }

    public function testImportedCoreClassesExistInTheCore(): void
    {
        $missing = [];

        foreach (self::coreFiles() as $relative => $contents) {
            preg_match_all('/^use\s+Boctulus\\\\Simplerest\\\\Core\\\\([A-Za-z0-9_\\\\]+)\s*;/m', $contents, $matches);
            foreach ($matches[1] as $class) {
                $path = self::frameworkPath() . DIRECTORY_SEPARATOR . str_replace('\\', DIRECTORY_SEPARATOR, $class) . '.php';
                $guarded = in_array($class, self::GUARDED_OPTIONAL_IMPORTS[$relative] ?? [], true)
                    && str_contains($contents, 'class_exists(' . substr(strrchr('\\' . $class, '\\'), 1) . '::class)');
                if (!is_file($path) && !$guarded) {
                    $missing[] = "{$relative}: Core\\{$class}";
                }
            }
        }

        self::assertSame([], $missing, 'src/framework imports core classes that are not shipped');
    }

    public function testCalledGlobalFunctionsAreDefinedByTheCoreOrPhp(): void
    {
        $defined = [];
        $calls = [];

        foreach (self::coreFiles() as $relative => $contents) {
            $tokens = token_get_all($contents);
            $count = count($tokens);

            for ($i = 0; $i < $count; $i++) {
                if (!is_array($tokens[$i])) {
                    continue;
                }

                if ($tokens[$i][0] === T_FUNCTION) {
                    $j = $i + 1;
                    while ($j < $count && is_array($tokens[$j]) && $tokens[$j][0] === T_WHITESPACE) {
                        $j++;
                    }
                    if ($j < $count && is_array($tokens[$j]) && $tokens[$j][0] === T_STRING) {
                        $defined[strtolower($tokens[$j][1])] = true;
                    }
                    continue;
                }

                if ($tokens[$i][0] !== T_STRING && $tokens[$i][0] !== T_NAME_FULLY_QUALIFIED) {
                    continue;
                }

                $next = $i + 1;
                while ($next < $count && is_array($tokens[$next]) && $tokens[$next][0] === T_WHITESPACE) {
                    $next++;
                }
                if ($next >= $count || $tokens[$next] !== '(') {
                    continue;
                }

                $prev = $i - 1;
                while ($prev >= 0 && is_array($tokens[$prev]) && in_array($tokens[$prev][0], [T_WHITESPACE, T_COMMENT, T_DOC_COMMENT], true)) {
                    $prev--;
                }
                $prevToken = $tokens[$prev] ?? null;
                if (is_array($prevToken) && in_array($prevToken[0], [T_OBJECT_OPERATOR, T_NULLSAFE_OBJECT_OPERATOR, T_DOUBLE_COLON, T_NEW, T_FUNCTION, T_FN], true)) {
                    continue;
                }

                $name = strtolower(ltrim($tokens[$i][1], '\\'));
                if (str_contains($name, '\\')) {
                    continue;
                }
                $calls[$name][] = "{$relative}:{$tokens[$i][2]}";
            }
        }

        $undefined = [];
        foreach ($calls as $name => $locations) {
            if (isset($defined[$name]) || in_array($name, self::GUARDED_PLATFORM_FUNCTIONS, true)) {
                continue;
            }
            if (function_exists($name) && (new ReflectionFunction($name))->isInternal()) {
                continue;
            }
            if (in_array($name, ['array', 'list', 'isset', 'empty', 'unset', 'exit', 'die', 'eval', 'match', 'declare', 'static', 'self', 'parent', 'fn', 'function', 'include', 'require', 'echo', 'print'], true)) {
                continue;
            }
            $undefined[] = $name . ' (' . $locations[0] . ')';
        }

        self::assertSame([], $undefined, 'src/framework calls functions that neither PHP nor the core defines');
    }
}
