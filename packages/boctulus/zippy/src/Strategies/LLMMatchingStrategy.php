<?php

namespace Boctulus\Zippy\Strategies;

use Boctulus\Zippy\Contracts\CategoryMatchingStrategyInterface;
use Boctulus\LLMProviders\Factory\LLMFactory;
use Boctulus\Simplerest\Core\Libs\Logger;

/**
 * Estrategia de matching basada en LLM
 */
class LLMMatchingStrategy implements CategoryMatchingStrategyInterface
{
    protected string $model;
    protected float $temperature;
    protected ?int $maxTokens;
    protected bool $verbose;

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
     *
     * @param string $raw
     * @param array $availableCategories slug => ['name'=>..., 'parent_slug'=>..., 'id'=>...]
     * @param float|null $threshold 0..1
     * @return array|null
     */
    public function match(string $raw, array $availableCategories, ?float $threshold = null): ?array
    {
        $threshold = $threshold ?? 0.95; // default 95%

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
                $error = $response['data']['data']['error'] ?? 'unknown';
                throw new \Exception("Error LLM exec: '$error' | http status code: {$response['status']}");
            }

            $content = $llm->getContent();

            if (empty($content)) {
                if ($this->verbose) {
                    Logger::log("LLM returned empty content");
                }
                return null;
            }

            // Parsear respuesta JSON (maneja sugerencias new y retornos por slug)
            $result = $this->parseResponse($content, $availableCategories);

            if (!$result) {
                if ($this->verbose) {
                    Logger::log("Failed to parse LLM response: {$content}");
                }
                return null;
            }

            // Verificar threshold (result['score'] está en 0..100)
            if ($result['score'] < ($threshold * 100)) {
                if ($this->verbose) {
                    Logger::log("LLM confidence {$result['score']}% below threshold " . ($threshold * 100) . "%");
                }
                return null;
            }

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
            $name = is_array($categoryData) ? ($categoryData['name'] ?? '') : ($categoryData->name ?? '');
            $parentSlug = is_array($categoryData) ? ($categoryData['parent_slug'] ?? null) : ($categoryData->parent_slug ?? null);

            $line = "- {$slug}: {$name}";
            if ($parentSlug) {
                $line .= " (subcategoría de {$parentSlug})";
            }
            $categoriesList[] = $line;
        }

        $categoriesText = implode("\n", $categoriesList);

        $prompt = <<<PROMPT
You are an expert system for product and brand classification in supermarkets and stores.

You must classify the following text or return NULL if you're not confident:

Text to classify: "{$raw}"

Available categories:
{$categoriesText}

CRITICAL RULES:
- Return ONLY a valid JSON object, no additional text before or after
- Return ONLY ONE category or NULL
- ALWAYS respond in English or Spanish, NEVER in other languages
- If the text is just symbols, numbers, or meaningless characters, return NULL
- Format must be exactly:
  {"category": "category-slug" | {"name":"Category Name"}, "is_new": true|false, "sugested_name": string|null, "sugested_parent_slug": string|null, "confidence": int, "reasoning": "brief explanation IN ENGLISH OR SPANISH"}
- If proposing a new category (is_new = true), fill "sugested_name" and "sugested_parent_slug"
- The "category" field can be:
    * An existing slug (e.g., "dairy.milk")
    * An object with {"name":"category name"} if you prefer to reference by name
- confidence: number between 0 and 100 (be conservative, require HIGH confidence)
- reasoning: brief explanation in English or Spanish explaining why you chose that category
- Return NULL if:
  * You're not confident (confidence < 85)
  * The text is just numbers or symbols
  * The text is ambiguous or meaningless
  * You cannot clearly identify what product category it belongs to

Respond ONLY with the JSON.
PROMPT;

        return $prompt;
    }

    /**
     * Parsea la respuesta del LLM
     *
     * Responde un array con:
     *  - 'category' => slug string o array/object (si 'is_new' true, category puede ser null)
     *  - 'score' => float (0..100)
     *  - 'reasoning' => string
     *  - 'is_new' => bool
     *  - 'sugested_name' => string|null
     *  - 'sugested_parent_slug' => string|null
     */
    protected function parseResponse(string $content, array $availableCategories): ?array
    {
        $content = trim($content);

        // Extraer JSON si viene envuelto en texto
        if (!str_starts_with($content, '{')) {
            $start = strpos($content, '{');
            $end = strrpos($content, '}');
            if ($start !== false && $end !== false && $end > $start) {
                $content = substr($content, $start, $end - $start + 1);
            }
        }

        $data = json_decode($content, true);

        if (!$data) {
            return null;
        }

        // Si devuelve explicitamente NULL (string "null" o null), manejar
        if (is_null($data) || (is_string($data) && strtolower($data) === 'null')) {
            return null;
        }

        // Validaciones mínimas
        $confidence = null;
        if (isset($data['confidence'])) {
            $confidence = (float)$data['confidence'];
        } elseif (isset($data['confidence']) === false && isset($data['score'])) {
            $confidence = (float)$data['score'];
        }

        $isNew = isset($data['is_new']) ? (bool)$data['is_new'] : false;
        $reasoning = $data['reasoning'] ?? ($data['reason'] ?? '');

        // Si LLM dice que es nueva
        if ($isNew) {
            $suggestedName = $data['sugested_name'] ?? $data['suggested_name'] ?? null;
            $suggestedParent = $data['sugested_parent_slug'] ?? $data['suggested_parent_slug'] ?? $data['parent_slug'] ?? null;

            if (empty($suggestedName) || $confidence === null) {
                return null;
            }

            return [
                'category' => null,
                'score' => $confidence,
                'reasoning' => $reasoning,
                'is_new' => true,
                'sugested_name' => $suggestedName,
                'sugested_parent_slug' => $suggestedParent,
                'strategy' => 'llm'
            ];
        }

        // Si LLM devolvió una categoría (slug o objeto con name)
        if (!isset($data['category']) || $confidence === null) {
            return null;
        }

        $cat = $data['category'];

        // Si category es slug string y existe en availableCategories -> OK
        if (is_string($cat)) {
            if (!isset($availableCategories[$cat])) {
                // No existe ese slug, considerarlo no válido
                return null;
            }
            return [
                'category' => $cat,
                'score' => $confidence,
                'reasoning' => $reasoning,
                'is_new' => false,
                'strategy' => 'llm'
            ];
        }

        // Si category es objeto/array con name, intentar buscar un slug por nombre
        if (is_array($cat) || is_object($cat)) {
            $name = is_array($cat) ? ($cat['name'] ?? null) : ($cat->name ?? null);
            if (!$name) {
                return null;
            }

            // Buscar slug por name en availableCategories
            foreach ($availableCategories as $slug => $catData) {
                if (($catData['name'] ?? null) === $name) {
                    return [
                        'category' => $slug,
                        'score' => $confidence,
                        'reasoning' => $reasoning,
                        'is_new' => false,
                        'strategy' => 'llm'
                    ];
                }
            }

            // Si no encontramos por name, devolver el objeto (caller intentará inferir)
            return [
                'category' => (is_array($cat) ? $cat : (array)$cat),
                'score' => $confidence,
                'reasoning' => $reasoning,
                'is_new' => false,
                'strategy' => 'llm'
            ];
        }

        return null;
    }

    public function getName(): string
    {
        return "LLM Semantic Matching ({$this->model})";
    }

    public function requiresExternalService(): bool
    {
        return true;
    }

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

    /**
     * Procesa múltiples productos en una sola llamada LLM
     *
     * @param array $rawItems Array de strings a clasificar
     * @param array $availableCategories slug => ['name'=>..., 'parent_slug'=>..., 'id'=>...]
     * @param float|null $threshold 0..1
     * @return array Resultado indexado por el mismo índice que $rawItems
     */
    public function matchBatch(array $rawItems, array $availableCategories, ?float $threshold = null): array
    {
        $threshold = $threshold ?? 0.95;
        
        try {
            $llm = LLMFactory::ollama();
            $llm->setModel($this->model);
            $llm->setTemperature($this->temperature);
            
            if ($this->maxTokens) {
                // Aumentar maxTokens para batch (factor conservador: 2x)
                $llm->setMaxTokens($this->maxTokens * 2);
            }
            
            $prompt = $this->buildBatchPrompt($rawItems, $availableCategories);
            $llm->addContent($prompt, 'user');
            
            $response = $llm->exec();
            
            if ($response['status'] !== 200) {
                $error = $response['data']['data']['error'] ?? 'unknown';
                throw new \Exception("Error LLM exec: '$error' | http status code: {$response['status']}");
            }
            
            $content = $llm->getContent();
            
            if (empty($content)) {
                if ($this->verbose) {
                    Logger::log("LLM batch returned empty content");
                }
                return array_fill(0, count($rawItems), null);
            }
            
            $results = $this->parseBatchResponse($content, $availableCategories, $threshold);
            
            return $results;
        } catch (\Exception $e) {
            if ($this->verbose) {
                Logger::log("LLM Batch exception: " . $e->getMessage());
            }
            return array_fill(0, count($rawItems), null);
        }
    }

    /**
     * Construye el prompt para clasificación batch
     */
    protected function buildBatchPrompt(array $rawItems, array $availableCategories): string
    {
        $categoriesText = $this->formatCategoriesList($availableCategories);
        
        $itemsList = [];
        foreach ($rawItems as $idx => $raw) {
            $itemsList[] = "$idx: \"$raw\"";
        }
        $itemsText = implode("\n", $itemsList);
        
        $prompt = <<<PROMPT
You are an expert system for product classification in supermarkets and stores.

Classify the following items and return a JSON array with results:

Items to classify:
{$itemsText}

Available categories:
{$categoriesText}

CRITICAL RULES:
- Return ONLY a valid JSON array, no additional text before or after
- Each array element must have: {"index": <item_index>, "category": "<slug>" | null, "confidence": <0-100>, "reasoning": "brief explanation"}
- For low confidence (<75) or ambiguous items, use: {"index": <item_index>, "category": null, "confidence": <score>, "reasoning": "..."}
- ALWAYS respond in English or Spanish, NEVER in other languages
- confidence: number between 0 and 100 (be conservative, require HIGH confidence)
- Return NULL category if:
  * You're not confident (confidence < 85)
  * The text is just numbers or symbols
  * The text is ambiguous or meaningless
  
Example response:
[
  {"index": 0, "category": "bebidas", "confidence": 95, "reasoning": "es una bebida gaseosa"},
  {"index": 1, "category": "pastas", "confidence": 90, "reasoning": "fideos son pastas"},
  {"index": 2, "category": null, "confidence": 50, "reasoning": "ambiguous product"}
]

Respond ONLY with the JSON array.
PROMPT;
        
        return $prompt;
    }

    /**
     * Parsea la respuesta batch del LLM
     */
    protected function parseBatchResponse(string $content, array $availableCategories, float $threshold): array
    {
        $content = trim($content);
        
        // Extraer JSON array
        if (!str_starts_with($content, '[')) {
            $start = strpos($content, '[');
            $end = strrpos($content, ']');
            if ($start !== false && $end !== false) {
                $content = substr($content, $start, $end - $start + 1);
            }
        }
        
        $data = json_decode($content, true);
        
        if (!is_array($data)) {
            if ($this->verbose) {
                Logger::log("Failed to parse batch response as JSON array: $content");
            }
            return [];
        }
        
        $results = [];
        foreach ($data as $item) {
            $idx = $item['index'] ?? null;
            $category = $item['category'] ?? null;
            $confidence = $item['confidence'] ?? 0;
            $reasoning = $item['reasoning'] ?? '';
            
            if ($idx === null) {
                continue;
            }
            
            // Si el LLM devolvió categoría válida y confianza suficiente
            if ($category && isset($availableCategories[$category]) && $confidence >= ($threshold * 100)) {
                $results[$idx] = [
                    'category' => $category,
                    'score' => $confidence,
                    'reasoning' => $reasoning,
                    'is_new' => false,
                    'strategy' => 'llm'
                ];
            } else {
                $results[$idx] = null;
            }
        }
        
        return $results;
    }

    /**
     * Formatea la lista de categorías para el prompt
     */
    protected function formatCategoriesList(array $availableCategories): string
    {
        $categoriesList = [];
        foreach ($availableCategories as $slug => $categoryData) {
            $name = is_array($categoryData) ? ($categoryData['name'] ?? '') : ($categoryData->name ?? '');
            $parentSlug = is_array($categoryData) ? ($categoryData['parent_slug'] ?? null) : ($categoryData->parent_slug ?? null);
            
            $line = "- {$slug}: {$name}";
            if ($parentSlug) {
                $line .= " (subcategoría de {$parentSlug})";
            }
            $categoriesList[] = $line;
        }
        
        return implode("\n", $categoriesList);
    }
}
