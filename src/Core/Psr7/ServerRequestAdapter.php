<?php

namespace Boctulus\Simplerest\Core\Psr7;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UriInterface;
use Psr\Http\Message\StreamInterface;
use Boctulus\Simplerest\Core\Request;

/**
 * PSR-7 ServerRequestInterface adapter
 *
 * Adapts SimpleRest Request to PSR-7 ServerRequestInterface
 * This is a read-only adapter that wraps the existing Request instance
 *
 * @author Pablo Bozzolo (boctulus)
 */
class ServerRequestAdapter implements ServerRequestInterface
{
    private Request $request;
    private ?UriInterface $uri = null;
    private ?StreamInterface $body = null;
    private array $attributes = [];
    private array $cookieParams = [];
    private array $uploadedFiles = [];

    /**
     * Constructor
     *
     * @param Request $request The SimpleRest Request instance to adapt
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
        $this->cookieParams = $_COOKIE ?? [];
    }

    // ========== ServerRequestInterface Methods ==========

    public function getServerParams(): array
    {
        return $_SERVER;
    }

    public function getCookieParams(): array
    {
        return $this->cookieParams;
    }

    public function withCookieParams(array $cookies): ServerRequestInterface
    {
        $new = clone $this;
        $new->cookieParams = $cookies;
        return $new;
    }

    public function getQueryParams(): array
    {
        return $this->request->getQuery() ?? [];
    }

    public function withQueryParams(array $query): ServerRequestInterface
    {
        $new = clone $this;
        // Create new Request instance with modified query
        $newRequest = clone $this->request;

        // Use reflection to modify protected property
        $reflection = new \ReflectionClass($newRequest);
        $property = $reflection->getProperty('query_arr');
        $property->setAccessible(true);
        $property->setValue($newRequest, $query);

        $new->request = $newRequest;
        return $new;
    }

    public function getUploadedFiles(): array
    {
        return $this->uploadedFiles;
    }

    public function withUploadedFiles(array $uploadedFiles): ServerRequestInterface
    {
        $new = clone $this;
        $new->uploadedFiles = $uploadedFiles;
        return $new;
    }

    public function getParsedBody()
    {
        $body = $this->request->getBody(false); // false = return as array
        return $body;
    }

    public function withParsedBody($data): ServerRequestInterface
    {
        if (!is_array($data) && !is_object($data) && $data !== null) {
            throw new \InvalidArgumentException('Parsed body must be array, object, or null');
        }

        $new = clone $this;

        // Create new Request instance with modified body
        $newRequest = clone $this->request;

        // Use reflection to modify protected property
        $reflection = new \ReflectionClass($newRequest);
        $property = $reflection->getProperty('body');
        $property->setAccessible(true);
        $property->setValue($newRequest, $data);

        $new->request = $newRequest;
        return $new;
    }

    public function getAttributes(): array
    {
        return $this->attributes;
    }

    public function getAttribute(string $name, $default = null)
    {
        return $this->attributes[$name] ?? $default;
    }

    public function withAttribute(string $name, $value): ServerRequestInterface
    {
        $new = clone $this;
        $new->attributes[$name] = $value;
        return $new;
    }

    public function withoutAttribute(string $name): ServerRequestInterface
    {
        $new = clone $this;
        unset($new->attributes[$name]);
        return $new;
    }

    // ========== MessageInterface Methods (inherited from RequestInterface) ==========

    public function getProtocolVersion(): string
    {
        $protocol = $_SERVER['SERVER_PROTOCOL'] ?? 'HTTP/1.1';
        return str_replace('HTTP/', '', $protocol);
    }

    public function withProtocolVersion(string $version): ServerRequestInterface
    {
        // Protocol version is read-only in this adapter
        // Return clone to maintain immutability
        return clone $this;
    }

    public function getHeaders(): array
    {
        $headers = $this->request->headers();

        // Normalize header names to capitalize each word
        $normalized = [];
        foreach ($headers as $name => $value) {
            $name = str_replace(' ', '-', ucwords(str_replace('-', ' ', $name)));
            $normalized[$name] = is_array($value) ? $value : [$value];
        }

        return $normalized;
    }

    public function hasHeader(string $name): bool
    {
        return $this->request->getHeader($name) !== null;
    }

    public function getHeader(string $name): array
    {
        $value = $this->request->getHeader($name);

        if ($value === null) {
            return [];
        }

        return is_array($value) ? $value : [$value];
    }

    public function getHeaderLine(string $name): string
    {
        $values = $this->getHeader($name);
        return implode(', ', $values);
    }

    public function withHeader(string $name, $value): ServerRequestInterface
    {
        $new = clone $this;

        // Create new Request instance with modified headers
        $newRequest = clone $this->request;

        // Use reflection to modify protected property
        $reflection = new \ReflectionClass($newRequest);
        $property = $reflection->getProperty('headers');
        $property->setAccessible(true);
        $headers = $property->getValue($newRequest);
        $headers[strtolower($name)] = is_array($value) ? implode(', ', $value) : $value;
        $property->setValue($newRequest, $headers);

        $new->request = $newRequest;
        return $new;
    }

    public function withAddedHeader(string $name, $value): ServerRequestInterface
    {
        $new = clone $this;

        // Create new Request instance with added header
        $newRequest = clone $this->request;

        // Use reflection to modify protected property
        $reflection = new \ReflectionClass($newRequest);
        $property = $reflection->getProperty('headers');
        $property->setAccessible(true);
        $headers = $property->getValue($newRequest);

        $lowerName = strtolower($name);
        if (isset($headers[$lowerName])) {
            $existing = is_array($headers[$lowerName]) ? $headers[$lowerName] : [$headers[$lowerName]];
            $new_values = is_array($value) ? $value : [$value];
            $headers[$lowerName] = array_merge($existing, $new_values);
        } else {
            $headers[$lowerName] = is_array($value) ? implode(', ', $value) : $value;
        }

        $property->setValue($newRequest, $headers);
        $new->request = $newRequest;
        return $new;
    }

    public function withoutHeader(string $name): ServerRequestInterface
    {
        $new = clone $this;

        // Create new Request instance without header
        $newRequest = clone $this->request;

        // Use reflection to modify protected property
        $reflection = new \ReflectionClass($newRequest);
        $property = $reflection->getProperty('headers');
        $property->setAccessible(true);
        $headers = $property->getValue($newRequest);
        unset($headers[strtolower($name)]);
        $property->setValue($newRequest, $headers);

        $new->request = $newRequest;
        return $new;
    }

    public function getBody(): StreamInterface
    {
        if ($this->body === null) {
            $bodyData = $this->request->getRaw();
            $this->body = new StreamAdapter($bodyData ?? '');
        }

        return $this->body;
    }

    public function withBody(StreamInterface $body): ServerRequestInterface
    {
        $new = clone $this;
        $new->body = $body;
        return $new;
    }

    // ========== RequestInterface Methods ==========

    public function getRequestTarget(): string
    {
        if (isset($_SERVER['REQUEST_URI'])) {
            return $_SERVER['REQUEST_URI'];
        }

        return '/';
    }

    public function withRequestTarget(string $requestTarget): ServerRequestInterface
    {
        // Request target is read-only in this adapter
        return clone $this;
    }

    public function getMethod(): string
    {
        return $this->request->method();
    }

    public function withMethod(string $method): ServerRequestInterface
    {
        // Method is read-only in this adapter
        return clone $this;
    }

    public function getUri(): UriInterface
    {
        if ($this->uri === null) {
            $scheme = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' ? 'https' : 'http';
            $host = $_SERVER['HTTP_HOST'] ?? $_SERVER['SERVER_NAME'] ?? 'localhost';
            $uri = $_SERVER['REQUEST_URI'] ?? '/';

            $this->uri = new UriAdapter($scheme . '://' . $host . $uri);
        }

        return $this->uri;
    }

    public function withUri(UriInterface $uri, bool $preserveHost = false): ServerRequestInterface
    {
        $new = clone $this;
        $new->uri = $uri;

        if (!$preserveHost && $uri->getHost() !== '') {
            // Update Host header
            return $new->withHeader('Host', $uri->getHost());
        }

        return $new;
    }
}
