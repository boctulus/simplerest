<?php

namespace Boctulus\LLMProviders\Factory;

use Boctulus\LLMProviders\Contracts\LLMProviderInterface;
use Boctulus\LLMProviders\Providers\OpenAIProvider;
use Boctulus\LLMProviders\Providers\ClaudeProvider;
use Boctulus\LLMProviders\Providers\GrokProvider;
use Boctulus\LLMProviders\Providers\OllamaProvider;
use Boctulus\LLMProviders\Exceptions\ProviderException;

/**
 * Factory para crear instancias de proveedores LLM
 *
 * Facilita la creación de proveedores mediante un patrón factory,
 * permitiendo cambiar entre proveedores fácilmente.
 *
 * Ejemplo de uso:
 * ```php
 * // Crear proveedor de OpenAI
 * $provider = LLMFactory::create('openai');
 *
 * // Crear proveedor de Claude
 * $provider = LLMFactory::create('claude');
 *
 * // Con API key personalizada
 * $provider = LLMFactory::create('openai', ['api_key' => 'sk-...']);
 * ```
 */
class LLMFactory
{
    /**
     * Proveedores disponibles
     */
    const PROVIDERS = [
        'openai' => OpenAIProvider::class,
        'chatgpt' => OpenAIProvider::class,
        'gpt' => OpenAIProvider::class,
        'claude' => ClaudeProvider::class,
        'anthropic' => ClaudeProvider::class,
        'grok' => GrokProvider::class,
        'xai' => GrokProvider::class,
        'ollama' => OllamaProvider::class,
    ];

    /**
     * Crea una instancia del proveedor especificado
     *
     * @param string $provider Nombre del proveedor (openai, claude, etc.)
     * @param array $config Configuración adicional (api_key, etc.)
     * @return LLMProviderInterface
     * @throws ProviderException
     */
    public static function create(string $provider, array $config = []): LLMProviderInterface
    {
        $provider = strtolower(trim($provider));

        if (!isset(self::PROVIDERS[$provider])) {
            throw new ProviderException(
                "Provider '$provider' is not supported. Available providers: " . implode(', ', array_keys(self::PROVIDERS)),
                0,
                null,
                null,
                ['provider' => $provider, 'available' => array_keys(self::PROVIDERS)]
            );
        }

        $providerClass = self::PROVIDERS[$provider];

        return self::instantiateProvider($providerClass, $config);
    }

    /**
     * Instancia un proveedor con su configuración
     *
     * @param string $providerClass Clase del proveedor
     * @param array $config Configuración
     * @return LLMProviderInterface
     */
    protected static function instantiateProvider(string $providerClass, array $config): LLMProviderInterface
    {
        switch ($providerClass) {
            case OpenAIProvider::class:
                $apiKey = $config['api_key'] ?? null;
                return new OpenAIProvider($apiKey);

            case ClaudeProvider::class:
                $apiKey = $config['api_key'] ?? null;
                $apiVersion = $config['api_version'] ?? null;
                return new ClaudeProvider($apiKey, $apiVersion);

            case GrokProvider::class:
                $apiKey = $config['api_key'] ?? null;
                return new GrokProvider($apiKey);

            case OllamaProvider::class:
                return new OllamaProvider();

            default:
                throw new ProviderException(
                    "Provider class '$providerClass' cannot be instantiated",
                    0,
                    null,
                    null,
                    ['class' => $providerClass]
                );
        }
    }

    /**
     * Verifica si un proveedor está soportado
     *
     * @param string $provider Nombre del proveedor
     * @return bool
     */
    public static function isSupported(string $provider): bool
    {
        $provider = strtolower(trim($provider));
        return isset(self::PROVIDERS[$provider]);
    }

    /**
     * Obtiene la lista de proveedores soportados
     *
     * @return array
     */
    public static function getSupportedProviders(): array
    {
        return array_keys(self::PROVIDERS);
    }

    /**
     * Crea una instancia de OpenAI
     *
     * @param string|null $apiKey API key opcional
     * @return OpenAIProvider
     */
    public static function openai(?string $apiKey = null): OpenAIProvider
    {
        return new OpenAIProvider($apiKey);
    }

    /**
     * Crea una instancia de Claude
     *
     * @param string|null $apiKey API key opcional
     * @param string|null $apiVersion Versión de API opcional
     * @return ClaudeProvider
     */
    public static function claude(?string $apiKey = null, ?string $apiVersion = null): ClaudeProvider
    {
        return new ClaudeProvider($apiKey, $apiVersion);
    }

    /**
     * Crea una instancia de Grok
     *
     * @param string|null $apiKey API key opcional
     * @return GrokProvider
     */
    public static function grok(?string $apiKey = null): GrokProvider
    {
        return new GrokProvider($apiKey);
    }

    /**
     * Crea una instancia de Ollama
     *
     * @return OllamaProvider
     */
    public static function ollama(): OllamaProvider
    {
        return new OllamaProvider();
    }
}
