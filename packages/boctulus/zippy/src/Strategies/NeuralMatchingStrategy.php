<?php

namespace Boctulus\Zippy\Strategies;

use Boctulus\Simplerest\Core\Libs\DB;
use Boctulus\Simplerest\Core\Libs\Strings;
use Boctulus\Simplerest\Core\Libs\Logger;

/**
 * Neural Matching Strategy
 *
 * Utiliza perceptrones simples con pesos ajustables para clasificar productos
 * basándose en palabras clave individuales.
 *
 * Arquitectura:
 * - Input: Palabras tokenizadas del producto
 * - Weights: Cada palabra tiene un peso por categoría
 * - Activation: Suma ponderada de pesos
 * - Output: Categoría con mayor score > threshold
 *
 * @author Pablo Bozzolo (boctulus)
 */
class NeuralMatchingStrategy
{
    protected $weights = [];
    protected $stopWords = [];
    protected $threshold = 0.50;

    public function __construct()
    {
        // Cargar stop words desde archivo
        $this->loadStopWords();

        // Agregar stop words específicas del dominio (medidas, abreviaturas)
        $domainStopWords = [
            'kg', 'gr', 'cm3', 'ml', 'unidad', 'pack', 'x',
            'c/', 's/', 'd/', 'trbk', 'pers', 'may', 'med',
        ];

        $this->stopWords = array_merge($this->stopWords, $domainStopWords);

        $this->loadWeights();
    }

    /**
     * Carga stop words desde archivo
     */
    protected function loadStopWords()
    {
        $stopWordsFile = __DIR__ . '/../../etc/stop-words-es.txt';

        if (!file_exists($stopWordsFile)) {
            Logger::log("NeuralMatchingStrategy: Stop words file not found: $stopWordsFile");
            $this->stopWords = [];
            return;
        }

        $content = file_get_contents($stopWordsFile);
        $words = explode("\n", $content);

        // Filtrar líneas vacías y normalizar
        $this->stopWords = array_filter(array_map('trim', $words), function($word) {
            return !empty($word) && strlen($word) > 0;
        });

        Logger::log("NeuralMatchingStrategy: Loaded " . count($this->stopWords) . " stop words from file");
    }

    /**
     * Carga pesos desde category_mappings y crea pesos basados en frecuencia
     */
    protected function loadWeights()
    {
        DB::setConnection('zippy');

        // Obtener mappings manuales (peso alto)
        $mappings = DB::select("
            SELECT raw_value, normalized, category_slug, category_id
            FROM category_mappings
            WHERE deleted_at IS NULL
        ");

        foreach ($mappings as $mapping) {
            $word = is_array($mapping) ? strtolower($mapping['normalized']) : strtolower($mapping->normalized);
            $categorySlug = is_array($mapping) ? $mapping['category_slug'] : $mapping->category_slug;
            $categoryId = is_array($mapping) ? $mapping['category_id'] : $mapping->category_id;

            // Peso alto para mappings manuales
            if (!isset($this->weights[$word])) {
                $this->weights[$word] = [];
            }

            $this->weights[$word][$categorySlug] = [
                'weight' => 1.0,  // Peso máximo para mappings confirmados
                'category_id' => $categoryId
            ];
        }

        // Agregar pesos adicionales para palabras clave conocidas
        $this->addKeywordWeights();

        Logger::log("NeuralMatchingStrategy: Loaded " . count($this->weights) . " word weights");
    }

    /**
     * Agrega pesos para palabras clave conocidas por categoría
     */
    protected function addKeywordWeights()
    {
        DB::setConnection('zippy');

        // Obtener IDs de categorías
        $categories = DB::select("
            SELECT id, slug, name
            FROM categories
            WHERE deleted_at IS NULL
        ");

        $categoryMap = [];
        foreach ($categories as $cat) {
            $slug = is_array($cat) ? $cat['slug'] : $cat->slug;
            $id = is_array($cat) ? $cat['id'] : $cat->id;
            $categoryMap[$slug] = $id;
        }

        // Pesos por categoría (peso 0.8 para palabras clave fuertes)
        $keywordWeights = [
            'electro' => [
                'calefactor' => 0.9,
                'notebook' => 0.9,
                'computadora' => 0.9,
                'celular' => 0.9,
                'telefono' => 0.9,
                'monitor' => 0.9,
                'teclado' => 0.8,
                'mouse' => 0.8,
                'aire' => 0.7,  // aire acondicionado
                'heladera' => 0.9,
                'freezer' => 0.9,
                'lavarropas' => 0.9,
                'microondas' => 0.9,
                'smart' => 0.7,
                'tv' => 0.9,
                'funda' => 0.6,  // funda de celular
                'cargador' => 0.7,
                'cable' => 0.6,
            ],
            'panaderia' => [
                'integral' => 0.7,
                'lactal' => 0.8,
                'molde' => 0.7,
                'sandwich' => 0.8,
                'arabes' => 0.8,
                'salvado' => 0.7,
                'semillas' => 0.7,
                'saborizado' => 0.6,
                'galletitas' => 0.8,
                'galletas' => 0.8,
                'pasta' => 0.6,
                'pascualina' => 0.8,
                'empanada' => 0.8,
                'hojaldre' => 0.7,
                'matera' => 0.7,  // torta matera
            ],
            'bebidas' => [
                'tinto' => 0.8,
                'blanco' => 0.7,
                'rosado' => 0.8,
                'cerveza' => 0.9,
                'agua' => 0.8,
                'gaseosa' => 0.9,
                'jugo' => 0.9,
                'cola' => 0.8,
                'limonada' => 0.8,
                'naranja' => 0.6,  // jugo de naranja
            ],
            'embutidos' => [
                'frankfurt' => 0.9,
                'viena' => 0.9,
                'aleman' => 0.7,  // chorizo aleman
                'parrillero' => 0.8,
                'salame' => 0.9,
                'jamon' => 0.9,
                'mortadela' => 0.9,
                'longaniza' => 0.9,
            ],
            'congelados' => [
                'cong' => 0.8,  // abreviatura
                'congelado' => 0.9,
                'frozen' => 0.9,
                'pollo' => 0.7,
                'carne' => 0.6,
                'vacuna' => 0.7,
                'cerdo' => 0.8,
                'pescado' => 0.8,
                'mozzarella' => 0.6,  // mozzarella congelada
            ],
            'almacen' => [
                'arroz' => 0.9,
                'azucar' => 0.9,
                'harina' => 0.9,
                'aceite' => 0.9,
                'sal' => 0.7,
                'vinagre' => 0.9,
                'pasta' => 0.7,
                'salsa' => 0.8,
                'pure' => 0.8,
                'conserva' => 0.8,
                'lata' => 0.6,
                'trit' => 0.7,  // tomate triturado
                'tritado' => 0.8,
                'perita' => 0.7,  // tomate perita
            ],
            'golosinas' => [
                'caramelo' => 0.9,
                'chupet' => 0.9,
                'chupetín' => 0.9,
                'chicle' => 0.9,
                'chocolate' => 0.8,
                'choco' => 0.7,
                'cacao' => 0.7,
                'cereal' => 0.7,  // barra de cereal
                'barra' => 0.6,
                'oblea' => 0.8,
                'wafer' => 0.8,
            ],
            'frutas-y-verduras' => [
                'membrillo' => 0.9,
                'batata' => 0.9,
                'cayote' => 0.9,
                'arandano' => 0.8,
                'frutilla' => 0.8,
                'durazno' => 0.8,
                'manzana' => 0.7,
                'ciruela' => 0.8,
            ],
            'limpieza' => [
                'detergente' => 0.9,
                'lavandina' => 0.9,
                'jabon' => 0.8,
                'esponja' => 0.9,
                'trapo' => 0.8,
                'rejilla' => 0.8,
                'limpiador' => 0.9,
                'desinfectante' => 0.9,
                'cloro' => 0.8,
                'negra' => 0.6,  // bolsa negra
            ],
        ];

        foreach ($keywordWeights as $categorySlug => $words) {
            if (!isset($categoryMap[$categorySlug])) {
                continue;
            }

            $categoryId = $categoryMap[$categorySlug];

            foreach ($words as $word => $weight) {
                if (!isset($this->weights[$word])) {
                    $this->weights[$word] = [];
                }

                // Solo agregar si no existe o tiene menor peso
                if (!isset($this->weights[$word][$categorySlug]) ||
                    $this->weights[$word][$categorySlug]['weight'] < $weight) {
                    $this->weights[$word][$categorySlug] = [
                        'weight' => $weight,
                        'category_id' => $categoryId
                    ];
                }
            }
        }
    }

    /**
     * Tokeniza y limpia el texto de entrada
     */
    protected function tokenize(string $text): array
    {
        // Convertir a minúsculas
        $text = strtolower($text);

        // Remover caracteres especiales pero mantener letras con acento
        $text = preg_replace('/[^a-záéíóúñü\s]/u', ' ', $text);

        // Dividir en palabras
        $words = preg_split('/\s+/', $text, -1, PREG_SPLIT_NO_EMPTY);

        // Filtrar stop words y palabras muy cortas
        $words = array_filter($words, function($word) {
            return strlen($word) >= 3 && !in_array($word, $this->stopWords);
        });

        return array_values($words);
    }

    /**
     * Calcula el score para cada categoría basándose en las palabras
     */
    protected function calculateScores(array $words, array $availableCategories): array
    {
        $scores = [];

        // Inicializar scores en 0
        foreach ($availableCategories as $slug => $data) {
            $scores[$slug] = [
                'score' => 0.0,
                'matched_words' => [],
                'category_id' => $data['id'] ?? null
            ];
        }

        // Calcular scores basados en pesos
        foreach ($words as $word) {
            if (!isset($this->weights[$word])) {
                continue;
            }

            foreach ($this->weights[$word] as $categorySlug => $data) {
                if (isset($scores[$categorySlug])) {
                    $scores[$categorySlug]['score'] += $data['weight'];
                    $scores[$categorySlug]['matched_words'][] = $word;
                }
            }
        }

        // Normalizar scores por cantidad de palabras (evita que textos largos tengan ventaja injusta)
        $wordCount = max(count($words), 1);
        foreach ($scores as $slug => &$data) {
            $data['normalized_score'] = $data['score'] / $wordCount;
        }

        // Ordenar por score
        uasort($scores, function($a, $b) {
            return $b['score'] <=> $a['score'];
        });

        return $scores;
    }

    /**
     * Match principal de la estrategia
     */
    public function match(string $text, array $availableCategories, float $threshold = 0.50): ?array
    {
        $this->threshold = $threshold;

        // Tokenizar
        $words = $this->tokenize($text);

        if (empty($words)) {
            Logger::log("NeuralMatchingStrategy: No valid words found in text: $text");
            return null;
        }

        Logger::log("NeuralMatchingStrategy: Tokenized words: " . implode(', ', $words));

        // Calcular scores
        $scores = $this->calculateScores($words, $availableCategories);

        // Obtener mejor match
        $bestMatch = reset($scores);
        $bestSlug = key($scores);

        if (!$bestMatch || $bestMatch['score'] < $this->threshold) {
            Logger::log("NeuralMatchingStrategy: Best score ({$bestMatch['score']}) below threshold ({$this->threshold})");
            return null;
        }

        Logger::log("NeuralMatchingStrategy: Match found - Category: $bestSlug, Score: {$bestMatch['score']}, Words: " . implode(', ', $bestMatch['matched_words']));

        return [
            'category' => $bestSlug,
            'category_id' => $bestMatch['category_id'],
            'score' => $bestMatch['score'],
            'normalized_score' => $bestMatch['normalized_score'],
            'matched_words' => $bestMatch['matched_words'],
            'reasoning' => "Neural matching based on keywords: " . implode(', ', $bestMatch['matched_words']),
            'is_new' => false,
            'source' => 'neural'
        ];
    }
}
