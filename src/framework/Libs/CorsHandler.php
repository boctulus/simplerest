<?php

namespace Boctulus\Simplerest\Core\Libs;

/*
    CORS Handler
    ============

    Soporta todas las opciones del array de configuración estándar:
    - paths: Rutas donde aplicar CORS (ej: 'api/*')
    - maxAge: Cabecera Access-Control-Max-Age
    - exposedHeaders: Cabecera Access-Control-Expose-Headers
    - Manejo de wildcards (*) en métodos y headers
*/
class CorsHandler 
{
    private array $paths;
    private array $allowedOrigins;
    private bool  $supportsCredentials;
    private array $allowedHeaders;
    private array $allowedMethods;
    private array $allowedOriginsPatterns;
    private array $exposedHeaders;
    private int   $maxAge;

    public function __construct(
        array $paths = ['*'],
        array $allowedOrigins = ['*'],
        array $allowedMethods = ['*'],
        array $allowedHeaders = ['*'],
        array $exposedHeaders = [],
        array $allowedOriginsPatterns = [],
        int $maxAge = 0,
        bool $supportsCredentials = false
    ) {
        $this->validateConfiguration($allowedOrigins, $supportsCredentials);
        
        $this->paths = $paths;
        $this->allowedOrigins = $allowedOrigins;
        $this->allowedMethods = $this->parseWildcard($allowedMethods, [
            'GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'OPTIONS'
        ]);
        $this->allowedHeaders = $this->parseWildcard($allowedHeaders);
        $this->exposedHeaders = $exposedHeaders;
        $this->allowedOriginsPatterns = $allowedOriginsPatterns;
        $this->maxAge = $maxAge;
        $this->supportsCredentials = $supportsCredentials;
    }

    public function handle(): void {
        if (!$this->isPathAllowed()) {
            return;
        }

        $origin = $_SERVER['HTTP_ORIGIN'] ?? ($_SERVER['HTTP_REFERER'] ?? '');

        if ($this->isOriginAllowed($origin)) {
            $this->setCorsHeaders($origin);
            $this->handlePreflightRequest();
        } else {
            $this->denyRequest();
        }
    }

    public function loadConfig(array $config): void {
        $mapping = [
            'paths' => 'setPaths',
            'allowedMethods' => 'setAllowedMethods',
            'allowedOrigins' => 'setAllowedOrigins',
            'allowedOriginsPatterns' => 'setAllowedOriginsPatterns',
            'allowedHeaders' => 'setAllowedHeaders',
            'exposedHeaders' => 'setExposedHeaders',
            'maxAge' => 'setMaxAge',
            'supportsCredentials' => 'setSupportsCredentials'
        ];

        foreach ($config as $key => $value) {
            if (isset($mapping[$key])) {
                $this->{$mapping[$key]}($value);
            }
        }
    }

    // Setters con validación
    public function setPaths(array $paths): void {
        $this->paths = $paths;
    }

    public function setAllowedOrigins(array $origins): void {
        $this->validateConfiguration($origins, $this->supportsCredentials);
        $this->allowedOrigins = $origins;
    }

    public function setSupportsCredentials(bool $supports): void {
        $this->validateConfiguration($this->allowedOrigins, $supports);
        $this->supportsCredentials = $supports;
    }

    public function setAllowedHeaders(array $headers): void {
        $this->allowedHeaders = $this->parseWildcard($headers);
    }

    public function setAllowedMethods(array $methods): void {
        $this->allowedMethods = $this->parseWildcard($methods, [
            'GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'OPTIONS'
        ]);
    }

    public function setAllowedOriginsPatterns(array $patterns): void {
        $this->allowedOriginsPatterns = $patterns;
    }

    public function setExposedHeaders(array $headers): void {
        $this->exposedHeaders = $headers;
    }

    public function setMaxAge(int $seconds): void {
        $this->maxAge = max(0, $seconds);
    }

    private function isPathAllowed(): bool {
        $requestPath = parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH) ?? '/';        
        
        foreach ($this->paths as $pattern) {
            if ($pattern === '*' || fnmatch($pattern, $requestPath)) {
                return true;
            }
        }
        
        return false;
    }

    private function parseWildcard(array $items, array $allItems = []): array {
        if (in_array('*', $items)) {
            return $allItems ?: ['*'];
        }
        return $items;
    }

    private function validateConfiguration(array $origins, bool $supportsCredentials): void {
        if ($supportsCredentials && in_array('*', $origins)) {
            throw new \InvalidArgumentException(
                "Cannot use wildcard origin '*' with credentials"
            );
        }
    }

    private function isOriginAllowed(string $origin): bool {
        return in_array('*', $this->allowedOrigins) ||
               in_array($origin, $this->allowedOrigins) ||
               $this->matchesPattern($origin);
    }

    private function setCorsHeaders(string $origin): void {
        // Headers básicos
        header("Access-Control-Allow-Origin: " . ($this->supportsCredentials ? $origin : '*'));
        header('Access-Control-Allow-Credentials: ' . ($this->supportsCredentials ? 'true' : 'false'));
        
        // Headers condicionales
        if (!empty($this->allowedHeaders)) {
            header('Access-Control-Allow-Headers: ' . implode(', ', $this->allowedHeaders));
        }
        
        if (!empty($this->allowedMethods)) {
            header('Access-Control-Allow-Methods: ' . implode(', ', $this->allowedMethods));
        }
        
        if (!empty($this->exposedHeaders)) {
            header('Access-Control-Expose-Headers: ' . implode(', ', $this->exposedHeaders));
        }
        
        if ($this->maxAge > 0) {
            header('Access-Control-Max-Age: ' . $this->maxAge);
        }
    }

    private function handlePreflightRequest(): void {
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            http_response_code(204);
            header('Content-Length: 0');
            exit();
        }
    }

    private function denyRequest(): void {
        http_response_code(403);
        throw new \RuntimeException('CORS Policy: Origin not allowed', 403);

    }

    private function matchesPattern(string $origin): bool {
        foreach ($this->allowedOriginsPatterns as $pattern) {
            if (@preg_match($pattern, $origin) === 1) {
                return true;
            }
        }
        return false;
    }
}