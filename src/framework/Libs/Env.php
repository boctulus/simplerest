<?php declare(strict_types=1);

namespace Boctulus\Simplerest\Core\Libs;

use Boctulus\Simplerest\Core\Libs\Strings;

class Env
{
    static $data;
    private static ?\Throwable $setupError = null;

    /**
     * Loads environment files into memory and optionally validates required keys.
     *
     * @param string|null $file Relative to ROOT_PATH or an absolute path; null uses .env.
     * @param bool $inherit Whether to apply an eligible .dev-env overlay.
     * @param array $required Keys that must resolve to non-empty values.
     */
    static function setup(?string $file = null, bool $inherit = true, array $required = []): void {
        static::$data = null;
        static::$setupError = null;

        try {
            $file ??= '.env';
            $envPath = static::resolvePath($file);
            $data = static::parseFile($envPath);

            if ($inherit && static::isDevelopmentEnvironment(static::processValue('APP_ENV') ?? ($data['APP_ENV'] ?? null))){
                $devEnvPath = dirname($envPath) . DIRECTORY_SEPARATOR . '.dev-env';

                if (is_file($devEnvPath)){
                    $devData = static::parseFile($devEnvPath);

                    if (array_key_exists('APP_ENV', $devData)){
                        throw new \InvalidArgumentException('.dev-env must not define APP_ENV; set it in .env or the process environment.');
                    }

                    $data = array_replace($data, $devData);
                }
            }

            static::validateRequired($data, $required);
            static::$data = $data;
        } catch (\Throwable $error) {
            static::$data = null;
            static::$setupError = $error;
            throw $error;
        }
    }

    private static function ensureSetup(): void {
        if (static::$data !== null){
            return;
        }

        if (static::$setupError !== null){
            throw static::$setupError;
        }

        static::setup();
    }

    private static function resolvePath(string $file): string {
        $isAbsolute = str_starts_with($file, '/')
            || str_starts_with($file, '\\')
            || preg_match('/^[A-Za-z]:[\\\\\/]/', $file) === 1;

        if ($isAbsolute){
            return $file;
        }

        return rtrim(ROOT_PATH, '/\\') . DIRECTORY_SEPARATOR . ltrim($file, '/\\');
    }

    private static function processValue(string $key): ?string {
        $value = getenv($key);

        return $value === false ? null : $value;
    }

    private static function isDevelopmentEnvironment(?string $environment): bool {
        if (!is_string($environment)){
            return false;
        }

        return in_array(strtolower(trim($environment)), ['local', 'development'], true);
    }

    private static function validateRequired(array $data, array $required): void {
        $missing = [];

        foreach ($required as $key){
            if (!is_string($key) || $key === ''){
                throw new \InvalidArgumentException('Required environment variable names must be non-empty strings');
            }

            $value = static::processValue($key);

            if ($value === null){
                $value = array_key_exists($key, $data) ? $data[$key] : null;
            }

            if ($value === null || $value === ''){
                $missing[] = $key;
            }
        }

        if ($missing){
            throw new \RuntimeException('Required environment variables are missing or empty: ' . implode(', ', $missing));
        }
    }

    /**
     * Parses an env file from disk.
     *
     * @param string $path Absolute path to the env file.
     * @return array Key/value pairs (values are always strings).
     * @throws \Exception If the file cannot be read.
     */
    static function parseFile(string $path): array {
        $content = @file_get_contents($path);

        if ($content === false){
            throw new \Exception("Cannot read env file \"$path\"");
        }

        return static::parse($content);
    }

    /**
     * Parses env content into key/value pairs.
     *
     * Supported syntax:
     * - Full-line comments starting with '#' or ';'
     * - Inline comments: '#' or ';' preceded by whitespace on unquoted values
     * - Quoted values (double quotes support \" and \\ escapes; single quotes are literal)
     * - Bare keys (KEY) and empty values (KEY=)
     * - Optional 'export ' prefix
     * - BOM and CRLF line endings
     *
     * Section headers ([section]) are ignored, matching the old
     * parse_ini_file() behavior without process_sections.
     *
     * @param string $content Raw file content.
     * @return array Key/value pairs (values are always strings).
     */
    static function parse(string $content): array {
        // Remove UTF-8 BOM if present
        $content = preg_replace('/^\xEF\xBB\xBF/', '', $content);

        $result = [];

        foreach (preg_split('/\r\n|\r|\n/', $content) ?: [] as $line){
            $line = trim($line);

            if ($line === '' || $line[0] === '#' || $line[0] === ';'){
                continue;
            }

            if ($line[0] === '[' && substr($line, -1) === ']'){
                continue;
            }

            if (stripos($line, 'export ') === 0){
                $line = trim(substr($line, 7));
            }

            $eq_pos = strpos($line, '=');

            if ($eq_pos === false){
                $result[trim($line)] = '';
                continue;
            }

            $key   = trim(substr($line, 0, $eq_pos));
            $value = trim(substr($line, $eq_pos + 1));

            if ($key === ''){
                continue;
            }

            $result[$key] = static::parseValue($value);
        }

        return $result;
    }

    /**
     * Parses a single value, honoring quotes and inline comments.
     *
     * @param string $value Trimmed raw value (right side of '=').
     * @return string Parsed value.
     */
    static function parseValue(string $value): string {
        if ($value === ''){
            return '';
        }

        $quote = $value[0];

        if ($quote === '"' || $quote === "'"){
            $parsed = self::parseQuoted($value, $quote);

            // Unterminated quote: keep the raw value as-is
            return $parsed ?? $value;
        }

        // Unquoted: an inline comment starts at the first '#' or ';' preceded by whitespace
        $value = preg_split('/\s[#;]/', $value, 2) ?: [$value];

        return trim($value[0]);
    }

    /**
     * Extracts the content of a quoted value.
     *
     * Double quotes support \" and \\ escapes; single quotes are literal.
     * Other backslash sequences (e.g. \n) are kept as-is, matching the old
     * parse_ini_file() behavior — .env files store PEM keys and JSON blobs
     * with literal '\n' sequences that must survive untouched.
     * Anything after the closing quote (e.g. an inline comment) is discarded.
     *
     * @param string $value Raw value including the opening quote.
     * @param string $quote Quote character ('"' or "'").
     * @return string|null Parsed content, or null if the quote is never closed.
     */
    private static function parseQuoted(string $value, string $quote): ?string {
        $len = strlen($value);
        $out = '';
        $i   = 1;

        $escapes = ['"' => '"', '\\' => '\\'];

        while ($i < $len){
            $char = $value[$i];

            if ($quote === '"' && $char === '\\' && $i + 1 < $len){
                $next = $value[$i + 1];
                $out .= $escapes[$next] ?? ('\\' . $next);
                $i   += 2;
                continue;
            }

            if ($char === $quote){
                return $out;
            }

            $out .= $char;
            $i++;
        }

        return null;
    }

    static function set(string $key = null, $value){
        static::ensureSetup();
        
        static::$data[$key] = $value;
    }

    static function get(?string $key = null, $default_value = null){
        static::ensureSetup();

        if (empty($key)){
            return static::$data;
        } 

        $processValue = static::processValue($key);

        if ($processValue !== null){
            return $processValue;
        }

        return static::$data[$key] ?? $default_value;
    }

    /**
     * Retrieves a boolean value from the environment configuration.
     *
     * @param string $key The environment key to fetch.
     * @param bool $default_value Default value to return if the key is not found or invalid.
     * @return bool Parsed boolean value.
     */
    static function getBool(string $key, bool $default_value = false): bool {
        static::ensureSetup();

        $value = static::get($key, $default_value);

        if ($value === ''){
            return false;
        }

        // Use Strings::parseOption to convert to boolean
        return Strings::parseOption($value, $default_value);
    }
}

