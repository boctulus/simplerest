<?php

namespace Boctulus\Zippy\Strategies;

use Boctulus\Zippy\Contracts\CategoryMatchingStrategyInterface;
use Boctulus\LLMProviders\Factory\LLMFactory;
use Boctulus\Simplerest\Core\Libs\Logger;

/**
 * Estrategia de matching basada en LLM (Large Language Model)
 *
 * Usa Ollama local para clasificación semántica inteligente de categorías.
 * Más preciso que fuzzy matching pero requiere Ollama running.
 */
class LLMMatchingStrategy implements CategoryMatchingStrategyInterface
{
    protected string $model;
    protected float $temperature;
    protected ?int $maxTokens;
    protected bool $verbose;

    /**
     * @param string $model Modelo de Ollama a usar (ej: 'llama3.2', 'qwen2.5', 'mistral')
     * @param float $temperature Temperatura para generación (0.0-1.0, menor = más determinístico)
     * @param int|null $maxTokens Límite de tokens para la respuesta
     * @param bool $verbose Si debe loguear detalles para debugging
     */
    public function __construct(
        string $model = 'qwen2.5:1.5b',
        float $temperature = 0.2,
        ?int $maxTokens = 500,
        bool $verbose = false
    ) {
        $this->model = $model;
        $this->temperature = $temperature;
        $this->maxTokens = $maxTokens;
        $this->verbose = $verbose;
    }

    /**
     * {@inheritdoc}
     */
    public function match(string $raw, array $availableCategories, ?float $threshold = null): ?array
    {
        $threshold = $threshold ?? 0.95; // default, ej: 0.95 significa 95%

        try {
            $llm = LLMFactory::ollama();
            $llm->setModel($this->model);
            $llm->setTemperature($this->temperature);

            if ($this->maxTokens) {
                $llm->setMaxTokens($this->maxTokens);
            }

            // Construir el prompt con las categorías disponibles
            $prompt = $this->buildPrompt($raw, $availableCategories);

            $llm->addContent($prompt, 'user');

            // Ejecutar
            $response = $llm->exec();

            if ($response['status'] !== 200){
                $error = $response['data']['data']['error'];

                throw new \Exception("Error '$error | http status code: {$response['status']}");
            }

            // if ($error = $llm->error()) {
            //     if ($this->verbose) {
            //         Logger::log("LLM Error: {$error}");
            //     }
            //     return null;
            // }

            $content = $llm->getContent();

            if (empty($content)) {
                if ($this->verbose) {
                    Logger::log("LLM returned empty content");
                }
                return null;
            }

            // Parsear respuesta JSON
            $result = $this->parseResponse($content, $availableCategories);

            if (!$result) {
                if ($this->verbose) {
                    Logger::log("Failed to parse LLM response: {$content}");
                }
                return null;
            }

            // Verificar threshold
            if ($result['score'] < ($threshold * 100)) {
                if ($this->verbose) {
                    Logger::log("LLM confidence {$result['score']}% below threshold " . ($threshold * 100) . "%");
                }
                return null;
            }

            // if ($this->verbose) {
            //     Logger::log("LLM matched '{$raw}' to '{$result['category']}' with {$result['score']}% confidence");
            //     if (isset($result['reasoning'])) {
            //         Logger::log("Reasoning: {$result['reasoning']}");
            //     }
            // }

            return $result;
        } catch (\Exception $e) {
            if ($this->verbose) {
                Logger::log("LLM Strategy exception: " . $e->getMessage());
            }
            return null;
        }
    }

    /**
     * Construye el prompt para el LLM
     */
    protected function buildPrompt(string $raw, array $availableCategories): string
    {
        // Construir lista de categorías con formato legible
        $categoriesList = [];
        foreach ($availableCategories as $slug => $categoryData) {
            $name = is_array($categoryData) ? $categoryData['name'] : $categoryData->name;
            $parentSlug = is_array($categoryData)
                ? ($categoryData['parent_slug'] ?? null)
                : ($categoryData->parent_slug ?? null);

            $line = "- {$slug}: {$name}";
            if ($parentSlug) {
                $line .= " (subcategoría de {$parentSlug})";
            }
            $categoriesList[] = $line;
        }

        $categoriesText = implode("\n", $categoriesList);

        $prompt = <<<PROMPT
Eres un sistema experto en clasificación de productos para supermercados y tiendas.

Debes clasificar el siguiente texto de categoría o devolver NULL si no hay seguridad:

Texto a clasificar: "{$raw}"

Categorías disponibles:
{$categoriesText}

IMPORTANTE:
- Debes devolver SOLO un objeto JSON válido, sin texto adicional antes o después
- Devuelves SOLO UNA categoria o NULL.
- El formato debe ser exactamente: {"category": "slug-de-categoria", "is_new", "sugested_name", "sugested_parent_slug", "confidence": 95, "reasoning": "explicación breve"}
- El campo "category" debe ser uno de los slugs listados arriba excepto que no la encuentres pero puedas sugerir una nueva.
- El campo "confidence" debe ser un número entre 0 y 100
- El campo  "reasoning" debe explicar brevemente por qué elegiste esa categoría
- El campo "is_new" devuelve un bool que indica si es nueva (la estas sugiriendo).
- Los campos "sugested_name", "sugested_parent_slug" hacen referencia a una categoria nueva propuesta.
- Cuando propongas una nueva categoria debes estar absolutamente segura de que no existe y que no es redundante o irrelevante y debes enviar los campos pertinentes: "sugested_name", "sugested_parent_slug" asi como colocar "is_new" en true.
- En caso de no encontrar categoria con nivel de confianza alto debes devuelver NULL.

Responde SOLO con el JSON:
PROMPT;

        return $prompt;
    }

    /**
     * Parsea la respuesta del LLM
     */
    protected function parseResponse(string $content, array $availableCategories): ?array
    {
        // Limpiar posible texto antes/después del JSON
        $content = trim($content);

        // Intentar extraer JSON si está embebido en texto
        if (!str_starts_with($content, '{')) {
            // Buscar el primer { y el último }
            $start = strpos($content, '{');
            $end = strrpos($content, '}');

            if ($start !== false && $end !== false && $end > $start) {
                $content = substr($content, $start, $end - $start + 1);
            }
        }

        $data = json_decode($content, true);

        if (!$data || !isset($data['category']) || !isset($data['confidence'])) {
            return null;
        }

        $slug = $data['category'];
        $confidence = (float)$data['confidence'];
        $reasoning = $data['reasoning'] ?? '';

        // Verificar que la categoría existe
        if (!isset($availableCategories[$slug])) {
            return null;
        }

        return [
            'category' => $availableCategories[$slug],
            'score' => $confidence,
            'reasoning' => $reasoning,
            'strategy' => 'llm'
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        return "LLM Semantic Matching ({$this->model})";
    }

    /**
     * {@inheritdoc}
     */
    public function requiresExternalService(): bool
    {
        return true; // Requiere Ollama running
    }

    /**
     * Verifica si Ollama está disponible
     */
    public static function isAvailable(): bool
    {
        try {
            $llm = LLMFactory::ollama();
            $models = $llm->listModels();
            return !empty($models);
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Lista modelos disponibles en Ollama
     */
    public static function getAvailableModels(): array
    {
        try {
            $llm = LLMFactory::ollama();
            $models = $llm->listModels();
            return array_column($models, 'name');
        } catch (\Exception $e) {
            return [];
        }
    }
}
