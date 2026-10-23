<?php

declare(strict_types=1);

namespace Boctulus\Simplerest\Core;

use Boctulus\Simplerest\Core\Exceptions\InvalidRequestBodyException;
use JsonException;

final class Request
{
    private string $method;
    private string $path;
    private array $headers;
    private array $queryParams;
    private string $body;
    private array $attributes;
    private ?array $parsedBody;

    public function __construct(
        string $method = 'GET',
        string $uri = '/',
        array $headers = [],
        array $queryParams = [],
        string $body = '',
        array $attributes = [],
        ?array $parsedBody = null
    ) {
        $path = parse_url($uri, PHP_URL_PATH);
        $this->method = strtoupper($method);
        $this->path = self::normalizePath(is_string($path) ? $path : '/');
        $this->headers = self::normalizeHeaders($headers);
        $this->queryParams = $queryParams;
        $this->body = $body;
        $this->attributes = $attributes;
        $this->parsedBody = $parsedBody;
    }

    public static function fromGlobals(): self
    {
        $headers = [];
        if (function_exists('getallheaders')) {
            $serverHeaders = getallheaders();
            if (is_array($serverHeaders)) {
                $headers = $serverHeaders;
            }
        }

        foreach ($_SERVER as $key => $value) {
            if (str_starts_with($key, 'HTTP_')) {
                $name = str_replace('_', '-', strtolower(substr($key, 5)));
                $headers[$name] ??= (string) $value;
            }
        }
        if (isset($_SERVER['CONTENT_TYPE'])) {
            $headers['content-type'] ??= (string) $_SERVER['CONTENT_TYPE'];
        }
        if (isset($_SERVER['CONTENT_LENGTH'])) {
            $headers['content-length'] ??= (string) $_SERVER['CONTENT_LENGTH'];
        }

        $body = file_get_contents('php://input');
        return new self(
            (string) ($_SERVER['REQUEST_METHOD'] ?? 'GET'),
            (string) ($_SERVER['REQUEST_URI'] ?? '/'),
            $headers,
            $_GET,
            $body === false ? '' : $body,
            [],
            $_POST !== [] ? $_POST : null
        );
    }

    public function method(): string
    {
        return $this->method;
    }

    public function path(): string
    {
        return $this->path;
    }

    public function uri(): string
    {
        return $this->path;
    }

    public function headers(): array
    {
        return $this->headers;
    }

    public function header(string $name, ?string $default = null): ?string
    {
        return $this->headers[strtolower($name)] ?? $default;
    }

    public function query(?string $key = null, mixed $default = null): mixed
    {
        if ($key === null) {
            return $this->queryParams;
        }

        return $this->queryParams[$key] ?? $default;
    }

    public function queryParams(): array
    {
        return $this->queryParams;
    }

    public function rawBody(): string
    {
        return $this->body;
    }

    public function getBody(): mixed
    {
        return $this->parsedBody();
    }

    public function parsedBody(): mixed
    {
        if ($this->parsedBody !== null) {
            return $this->parsedBody;
        }

        $contentType = strtolower(trim(explode(';', $this->header('content-type', '') ?? '')[0]));
        if ($contentType !== 'application/json' || $this->body === '') {
            return $this->body;
        }

        try {
            return json_decode($this->body, true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException $exception) {
            throw new InvalidRequestBodyException('The request body is not valid JSON.', 0, $exception);
        }
    }

    public function attribute(string $key, mixed $default = null): mixed
    {
        return $this->attributes[$key] ?? $default;
    }

    public function setAttribute(string $key, mixed $value): void
    {
        $this->attributes[$key] = $value;
    }

    public function withAttribute(string $key, mixed $value): self
    {
        $request = clone $this;
        $request->setAttribute($key, $value);
        return $request;
    }

    private static function normalizeHeaders(array $headers): array
    {
        $normalized = [];
        foreach ($headers as $key => $value) {
            $normalized[strtolower((string) $key)] = is_array($value) ? implode(', ', $value) : (string) $value;
        }
        return $normalized;
    }

    private static function normalizePath(string $path): string
    {
        $path = '/' . trim($path, '/');
        return $path === '/' ? '/' : rtrim($path, '/');
    }
}
