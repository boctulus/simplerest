<?php

namespace Boctulus\Simplerest\Core\Psr7;

use Psr\Http\Message\StreamInterface;

/**
 * PSR-7 StreamInterface adapter
 *
 * Adapts string/array body data to PSR-7 StreamInterface
 *
 * @author Pablo Bozzolo (boctulus)
 */
class StreamAdapter implements StreamInterface
{
    private $resource;
    private $seekable;
    private $readable;
    private $writable;
    private $size;

    /**
     * Constructor
     *
     * @param string|array|resource $body
     */
    public function __construct($body = '')
    {
        // Convert array/object to JSON
        if (is_array($body) || is_object($body)) {
            $body = json_encode($body, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        }

        // Create stream from string
        if (is_string($body)) {
            $this->resource = fopen('php://temp', 'r+');
            if ($body !== '') {
                fwrite($this->resource, $body);
                rewind($this->resource);
            }
        } elseif (is_resource($body)) {
            $this->resource = $body;
        } else {
            throw new \InvalidArgumentException('Stream must be a string, array, or resource');
        }

        $meta = stream_get_meta_data($this->resource);
        $this->seekable = $meta['seekable'];
        $this->readable = (bool) preg_match('/r|a\+|ab\+|w\+|wb\+|x\+|xb\+|c\+|cb\+/', $meta['mode']);
        $this->writable = (bool) preg_match('/a|w|r\+|rb\+|rw|x|c/', $meta['mode']);
    }

    public function __toString(): string
    {
        try {
            $this->rewind();
            return $this->getContents();
        } catch (\Exception $e) {
            return '';
        }
    }

    public function close(): void
    {
        if (isset($this->resource)) {
            if (is_resource($this->resource)) {
                fclose($this->resource);
            }
            $this->detach();
        }
    }

    public function detach()
    {
        if (!isset($this->resource)) {
            return null;
        }

        $result = $this->resource;
        unset($this->resource);
        $this->size = null;
        $this->seekable = false;
        $this->readable = false;
        $this->writable = false;

        return $result;
    }

    public function getSize(): ?int
    {
        if ($this->size !== null) {
            return $this->size;
        }

        if (!isset($this->resource)) {
            return null;
        }

        $stats = fstat($this->resource);
        if (isset($stats['size'])) {
            $this->size = $stats['size'];
            return $this->size;
        }

        return null;
    }

    public function tell(): int
    {
        if (!isset($this->resource)) {
            throw new \RuntimeException('Stream is detached');
        }

        $result = ftell($this->resource);

        if ($result === false) {
            throw new \RuntimeException('Unable to determine stream position');
        }

        return $result;
    }

    public function eof(): bool
    {
        return !isset($this->resource) || feof($this->resource);
    }

    public function isSeekable(): bool
    {
        return $this->seekable;
    }

    public function seek(int $offset, int $whence = SEEK_SET): void
    {
        if (!isset($this->resource)) {
            throw new \RuntimeException('Stream is detached');
        }

        if (!$this->seekable) {
            throw new \RuntimeException('Stream is not seekable');
        }

        if (fseek($this->resource, $offset, $whence) === -1) {
            throw new \RuntimeException('Unable to seek to stream position');
        }
    }

    public function rewind(): void
    {
        $this->seek(0);
    }

    public function isWritable(): bool
    {
        return $this->writable;
    }

    public function write(string $string): int
    {
        if (!isset($this->resource)) {
            throw new \RuntimeException('Stream is detached');
        }

        if (!$this->writable) {
            throw new \RuntimeException('Cannot write to a non-writable stream');
        }

        $this->size = null;
        $result = fwrite($this->resource, $string);

        if ($result === false) {
            throw new \RuntimeException('Unable to write to stream');
        }

        return $result;
    }

    public function isReadable(): bool
    {
        return $this->readable;
    }

    public function read(int $length): string
    {
        if (!isset($this->resource)) {
            throw new \RuntimeException('Stream is detached');
        }

        if (!$this->readable) {
            throw new \RuntimeException('Cannot read from non-readable stream');
        }

        if ($length < 0) {
            throw new \RuntimeException('Length parameter cannot be negative');
        }

        if ($length === 0) {
            return '';
        }

        $result = fread($this->resource, $length);

        if ($result === false) {
            throw new \RuntimeException('Unable to read from stream');
        }

        return $result;
    }

    public function getContents(): string
    {
        if (!isset($this->resource)) {
            throw new \RuntimeException('Stream is detached');
        }

        $contents = stream_get_contents($this->resource);

        if ($contents === false) {
            throw new \RuntimeException('Unable to read stream contents');
        }

        return $contents;
    }

    public function getMetadata(?string $key = null)
    {
        if (!isset($this->resource)) {
            return $key ? null : [];
        }

        $meta = stream_get_meta_data($this->resource);

        if ($key === null) {
            return $meta;
        }

        return $meta[$key] ?? null;
    }
}
