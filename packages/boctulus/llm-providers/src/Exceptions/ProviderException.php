<?php

namespace Boctulus\LLMProviders\Exceptions;

/**
 * Excepción personalizada para errores de proveedores LLM
 */
class ProviderException extends \Exception
{
    protected $provider;
    protected $details;

    public function __construct(
        string $message = "",
        int $code = 0,
        ?\Throwable $previous = null,
        ?string $provider = null,
        $details = null
    ) {
        parent::__construct($message, $code, $previous);
        $this->provider = $provider;
        $this->details = $details;
    }

    /**
     * Obtiene el nombre del proveedor que generó el error
     *
     * @return string|null
     */
    public function getProvider(): ?string
    {
        return $this->provider;
    }

    /**
     * Obtiene detalles adicionales del error
     *
     * @return mixed
     */
    public function getDetails()
    {
        return $this->details;
    }
}
