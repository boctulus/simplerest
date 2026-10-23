<?php

declare(strict_types=1);

namespace Boctulus\Simplerest\Core;

use JsonException;
use Stringable;

final class Response implements Stringable
{
    private int $statusCode;
    private array $headers;
    private string $body;

    public function __construct(string $body = '', int $statusCode = 200, array $headers = [])
    {
        if ($statusCode < 100 || $statusCode > 599) {
            throw new \InvalidArgumentException('HTTP status must be between 100 and 599.');
        }

        $this->statusCode = $statusCode;
        $this->headers = $headers;
        $this->body = $body;
    }

    public static function json(mixed $data, int $statusCode = 200, array $headers = []): self
    {
        try {
            $body = json_encode($data, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        } catch (JsonException $exception) {
            throw new \RuntimeException('The response could not be encoded as JSON.', 0, $exception);
        }

        $headers = ['Content-Type' => 'application/json; charset=utf-8'] + $headers;
        return new self($body, $statusCode, $headers);
    }

    public function statusCode(): int
    {
        return $this->statusCode;
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function headers(): array
    {
        return $this->headers;
    }

    public function header(string $name): ?string
    {
        foreach ($this->headers as $key => $value) {
            if (strcasecmp((string) $key, $name) === 0) {
                return is_array($value) ? implode(', ', $value) : (string) $value;
            }
        }
        return null;
    }

    public function getHeaderLine(string $name): string
    {
        return $this->header($name) ?? '';
    }

    public function body(): string
    {
        return $this->body;
    }

    public function getBody(): string
    {
        return $this->body;
    }

    public function withStatus(int $statusCode): self
    {
        $response = clone $this;
        $response->statusCode = $statusCode;
        return $response;
    }

    public function withHeader(string $name, string $value): self
    {
        $response = clone $this;
        foreach (array_keys($response->headers) as $key) {
            if (strcasecmp((string) $key, $name) === 0) {
                unset($response->headers[$key]);
            }
        }
        $response->headers[$name] = $value;
        return $response;
    }

    public function send(): void
    {
        if (!headers_sent()) {
            http_response_code($this->statusCode);
            foreach ($this->headers as $name => $value) {
                header($name . ': ' . $value, true);
            }
        }

        echo $this->body;
    }

    public function __toString(): string
    {
        return $this->body;
    }
}
