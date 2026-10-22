<?php

namespace Boctulus\Simplerest\Core\Psr7;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use Boctulus\Simplerest\Core\Response;

/**
 * PSR-7 ResponseInterface adapter
 *
 * Adapts SimpleRest Response to PSR-7 ResponseInterface
 * This adapter wraps the existing Response instance
 *
 * @author Pablo Bozzolo (boctulus)
 */
class ResponseAdapter implements ResponseInterface
{
    private Response $response;
    private ?StreamInterface $body = null;
    private int $statusCode = 200;
    private string $reasonPhrase = '';
    private array $headers = [];

    // HTTP status code reason phrases
    private static array $phrases = [
        100 => 'Continue',
        101 => 'Switching Protocols',
        200 => 'OK',
        201 => 'Created',
        202 => 'Accepted',
        204 => 'No Content',
        301 => 'Moved Permanently',
        302 => 'Found',
        304 => 'Not Modified',
        307 => 'Temporary Redirect',
        308 => 'Permanent Redirect',
        400 => 'Bad Request',
        401 => 'Unauthorized',
        403 => 'Forbidden',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        409 => 'Conflict',
        422 => 'Unprocessable Entity',
        429 => 'Too Many Requests',
        500 => 'Internal Server Error',
        501 => 'Not Implemented',
        502 => 'Bad Gateway',
        503 => 'Service Unavailable',
        504 => 'Gateway Timeout',
    ];

    /**
     * Constructor
     *
     * @param Response|null $response The SimpleRest Response instance to adapt (optional)
     */
    public function __construct(?Response $response = null)
    {
        $this->response = $response ?? Response::getInstance();
    }

    /**
     * Get the underlying SimpleRest Response instance
     *
     * @return Response
     */
    public function getSimpleRestResponse(): Response
    {
        return $this->response;
    }

    // ========== ResponseInterface Methods ==========

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function withStatus(int $code, string $reasonPhrase = ''): ResponseInterface
    {
        if ($code < 100 || $code > 599) {
            throw new \InvalidArgumentException('Invalid HTTP status code');
        }

        $new = clone $this;
        $new->statusCode = $code;
        $new->reasonPhrase = $reasonPhrase;

        // Update underlying Response instance
        $new->response->code($code, $reasonPhrase);

        return $new;
    }

    public function getReasonPhrase(): string
    {
        if ($this->reasonPhrase !== '') {
            return $this->reasonPhrase;
        }

        return self::$phrases[$this->statusCode] ?? '';
    }

    // ========== MessageInterface Methods ==========

    public function getProtocolVersion(): string
    {
        // SimpleRest uses HTTP/1.1 by default
        return '1.1';
    }

    public function withProtocolVersion(string $version): ResponseInterface
    {
        // Protocol version is typically read-only in responses
        // Return clone to maintain immutability
        return clone $this;
    }

    public function getHeaders(): array
    {
        // Normalize header names
        $normalized = [];
        foreach ($this->headers as $name => $value) {
            $name = str_replace(' ', '-', ucwords(str_replace('-', ' ', $name)));
            $normalized[$name] = is_array($value) ? $value : [$value];
        }

        return $normalized;
    }

    public function hasHeader(string $name): bool
    {
        return isset($this->headers[strtolower($name)]);
    }

    public function getHeader(string $name): array
    {
        $lowerName = strtolower($name);

        if (!isset($this->headers[$lowerName])) {
            return [];
        }

        $value = $this->headers[$lowerName];
        return is_array($value) ? $value : [$value];
    }

    public function getHeaderLine(string $name): string
    {
        $values = $this->getHeader($name);
        return implode(', ', $values);
    }

    public function withHeader(string $name, $value): ResponseInterface
    {
        $new = clone $this;
        $lowerName = strtolower($name);

        $new->headers[$lowerName] = is_array($value) ? $value : [$value];

        // Update underlying Response instance
        $headerString = is_array($value) ? implode(', ', $value) : $value;
        $new->response->addHeader("$name: $headerString");

        return $new;
    }

    public function withAddedHeader(string $name, $value): ResponseInterface
    {
        $new = clone $this;
        $lowerName = strtolower($name);

        if (isset($new->headers[$lowerName])) {
            $existing = is_array($new->headers[$lowerName]) ? $new->headers[$lowerName] : [$new->headers[$lowerName]];
            $newValues = is_array($value) ? $value : [$value];
            $new->headers[$lowerName] = array_merge($existing, $newValues);
        } else {
            $new->headers[$lowerName] = is_array($value) ? $value : [$value];
        }

        // Update underlying Response instance
        $headerString = is_array($value) ? implode(', ', $value) : $value;
        $new->response->addHeader("$name: $headerString");

        return $new;
    }

    public function withoutHeader(string $name): ResponseInterface
    {
        $new = clone $this;
        unset($new->headers[strtolower($name)]);

        return $new;
    }

    public function getBody(): StreamInterface
    {
        if ($this->body === null) {
            // Get data from underlying Response
            $data = $this->response->get();
            $this->body = new StreamAdapter($data ?? '');
        }

        return $this->body;
    }

    public function withBody(StreamInterface $body): ResponseInterface
    {
        $new = clone $this;
        $new->body = $body;

        // Update underlying Response instance
        $bodyContents = (string) $body;
        $new->response->set($bodyContents);

        return $new;
    }

    /**
     * Send the response using the underlying SimpleRest Response
     *
     * This is a convenience method to output the response
     * Not part of PSR-7, but useful for integration
     */
    public function send(): void
    {
        $this->response->flush();
    }

    /**
     * Create a JSON response
     *
     * Convenience method for creating JSON responses
     * Not part of PSR-7, but commonly needed
     *
     * @param mixed $data
     * @param int $status
     * @return ResponseInterface
     */
    public function withJson($data, int $status = 200): ResponseInterface
    {
        $new = $this->withStatus($status);
        $new = $new->withHeader('Content-Type', 'application/json');

        $json = is_string($data) ? $data : json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        $new = $new->withBody(new StreamAdapter($json));

        return $new;
    }
}
