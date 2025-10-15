<?php

namespace Boctulus\Simplerest\Core\Exceptions;

use Boctulus\Simplerest\Core\Libs\SystemMessages;

use Throwable;

abstract class BaseException extends \Exception
{
    protected static string $errorCode = 'UNKNOWN_ERROR';
    protected array $args = [];
    protected int $httpStatus = 500;
    protected array $meta = [];

    /**
     * IMPORTANTE: constructor compatible con \Exception signature.
     * No hacemos side-effects pesados aquí.
     *
     * @param string $message
     * @param int $code (int)
     * @param Throwable|null $previous
     */
    public function __construct(string $message = "", int $code = 0, ?Throwable $previous = null)
    {
         if (empty($message)) {
            try {
                $message = SystemMessages::get(static::$errorCode, ...$this->args);
            } catch (\Throwable $e) {
                // Fallback seguro
                $message = static::$errorCode;
            }
        }

        parent::__construct($message, $code, $previous);
    }

    /**
     * Factory para crear la excepción desde un errorCode simbólico.
     *
     * @param string $errorCode Código simbólico (ej. 'MIDDLEWARE_NOT_FOUND')
     * @param array $args Argumentos para interpolación en el texto
     * @param int $httpStatus HTTP status code para la respuesta
     * @param int $internalCode Código entero para parent Exception (opcional)
     * @param Throwable|null $previous
     * @param array $meta Meta adicional
     * @param string|null $message  Si proporcionas message, se usa en vez del traducido
     * @return static
     */
    public static function fromErrorCode(
        string $errorCode,
        array $args = [],
        int $httpStatus = 500,
        int $internalCode = 0,
        ?Throwable $previous = null,
        array $meta = [],
        ?string $message  = null
    ): static {
        // Resolver mensaje traducido *antes* de instanciar

        if (empty($message)) {
            try {
                $message = SystemMessages::get($errorCode, ...$args);
            } catch (\Throwable $e) {
                // Fallback seguro
                $message = $errorCode;
            }
        }

        /** @var static $instance */
        $instance = new static($message, $internalCode, $previous);

        // Set properties (son protegidas, pero estamos en la clase base)
        $instance->errorCode  = $errorCode;
        $instance->args       = $args;
        $instance->httpStatus = $httpStatus;
        $instance->meta       = $meta;

        return $instance;
    }

    /* Getters */
    public function getErrorCode(): string { return static::$errorCode; }
    public function getArgs(): array { return $this->args; }
    public function getHttpStatus(): int { return $this->httpStatus; }
    public function getMeta(): array { return $this->meta; }

    public function getTranslatedMessage(): string
    {
        try {
            return SystemMessages::get(static::$errorCode, ...$this->args);
        } catch (\Throwable $_) {
            return $this->getMessage() ?: static::$errorCode;
        }
    }

    public function toArray(): array
    {
        $entry = SystemMessages::getEntry(static::$errorCode);
        $type  = $entry['type'] ?? 'GENERAL';

        return [
            'type' => $type,
            'code' => static::$errorCode,
            'message' => $this->getTranslatedMessage(),
            'http_status' => $this->getHttpStatus(),
            'meta' => $this->getMeta(),
        ];
    }
}
