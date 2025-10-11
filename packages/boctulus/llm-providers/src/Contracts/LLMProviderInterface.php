<?php

namespace Boctulus\LLMProviders\Contracts;

/**
 * Interface para proveedores de LLM (Large Language Models)
 *
 * Define el contrato que deben implementar todos los proveedores de IA
 * para garantizar una interfaz consistente y extensible.
 */
interface LLMProviderInterface
{
    /**
     * Configura el modelo a utilizar
     *
     * @param string $name Nombre del modelo
     * @return self
     */
    public function setModel(string $name);

    /**
     * Obtiene el modelo actual
     *
     * @return string
     */
    public function getModel(): string;

    /**
     * Configura parámetros adicionales para la petición
     *
     * @param array $params Parámetros como max_tokens, temperature, etc.
     * @return self
     */
    public function setParams(array $params);

    /**
     * Agrega contenido/mensaje a la conversación
     *
     * @param string $content El contenido del mensaje
     * @param string $role El rol del mensaje (user, assistant, system)
     * @return self
     */
    public function addContent(string $content, string $role = 'user');

    /**
     * Ejecuta la petición al proveedor de LLM
     *
     * @param string|null $model Modelo opcional para esta ejecución
     * @return array Respuesta del proveedor
     */
    public function exec(?string $model = null): array;

    /**
     * Obtiene el contenido de la respuesta
     *
     * @param bool $decode Si debe decodificar JSON embebido
     * @return mixed
     */
    public function getContent(bool $decode = true);

    /**
     * Obtiene información sobre el uso de tokens
     *
     * @return array|null
     */
    public function getTokenUsage(): ?array;

    /**
     * Obtiene la razón de finalización de la generación
     *
     * @return string|null
     */
    public function getFinishReason(): ?string;

    /**
     * Verifica si la respuesta está completa
     *
     * @return bool
     */
    public function isComplete(): bool;

    /**
     * Verifica si hubo suficientes tokens para la respuesta
     *
     * @return bool
     */
    public function wereTokenEnough(): bool;

    /**
     * Obtiene el mensaje de error si hubo alguno
     *
     * @return mixed
     */
    public function error();

    /**
     * Obtiene el cliente HTTP subyacente
     *
     * @return object
     */
    public function getClient();

    /**
     * Configura el límite máximo de tokens
     *
     * @param int $val Cantidad máxima de tokens
     * @return self
     */
    public function setMaxTokens(int $val);

    /**
     * Obtiene el límite máximo de tokens configurado
     *
     * @return int|null
     */
    public function getMaxTokens(): ?int;

    /**
     * Configura la temperatura para la generación
     *
     * @param float $val Valor de temperatura (0.0 - 2.0)
     * @return self
     */
    public function setTemperature(float $val);
}
