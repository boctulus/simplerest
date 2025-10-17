<?php

namespace Boctulus\Zippy\Libs;

use Boctulus\Simplerest\Core\Libs\DB;
use Boctulus\Simplerest\Core\Libs\Strings;
use Boctulus\Zippy\Strategies\LLMMatchingStrategy;
use Boctulus\Simplerest\Core\Libs\Logger;

/*
    CategoryMapper

    - Normaliza y busca en categories y category_mappings.
    - Si no encuentra, consulta estrategias (LLM por defecto).
    - Si LLM devuelve is_new=true, crea la categoría y el mapping.
    - Si LLM devuelve un slug existente, crea mapping y devuelve.
*/

class CategoryMapper
{
    // Make these static because we use them from static methods
    protected static $strategies = [];
    protected static $config = [];

    // Thresholds configurables
    const FUZZY_THRESHOLD = 0.40; // 40% similarity mínima
    const HIGH_CONFIDENCE = 70;   // 70% para auto-aplicar fuzzy/LLM
    const LOW_CONFIDENCE = 50;    // <50% requiere revisión manual

    static function init(){
        DB::setConnection('zippy');
    }

    static function getCategories(){
        static::init();

        $rows = DB::table('categories')->get();

        $result = [];
        foreach ($rows as $r) {
            // $r puede ser objeto o array según DB wrapper
            $slug = is_array($r) ? $r['slug'] : ($r->slug ?? null);
            $name = is_array($r) ? $r['name'] : ($r->name ?? null);
            $parent_slug = is_array($r) ? ($r['parent_slug'] ?? null) : ($r->parent_slug ?? null);
            $id = is_array($r) ? ($r['id'] ?? null) : ($r->id ?? null);

            if ($slug) {
                $result[$slug] = [
                    'id' => $id,
                    'name' => $name,
                    'parent_slug' => $parent_slug
                ];
            }
        }

        return $result;
    }

    /**
     * Guarda un alias entre un nombre alternativo de categoría y una categoría existente
     */
    public static function saveCategoryAlias(string $category_slug, string $raw_value, ?string $source = null): void
    {
        $normalized = Strings::normalize($raw_value);

        static::init();

        // Check si ya existe
        $exists = DB::selectOne("
        SELECT id, category_slug, source FROM category_mappings 
        WHERE normalized = ? 
          AND category_slug = ?
          AND deleted_at IS NULL
        LIMIT 1
    ", [$normalized, $category_slug]);

        if ($exists) {
            // Actualizar source si cambia
            if ($source && ($exists['source'] ?? null) !== $source) {
                DB::update("UPDATE category_mappings SET source = ?, updated_at = NOW() WHERE id = ?", [$source, $exists['id']]);
            }
            return;
        }

        // Insertar nuevo alias
        DB::insert("
        INSERT INTO category_mappings (raw_value, normalized, category_slug, source, created_at, updated_at)
        VALUES (?, ?, ?, ?, NOW(), NOW())
    ", [$raw_value, $normalized, $category_slug, $source]);
    }

    /**
     * Devuelve todos los aliases asociados a una categoría
     */
    public static function getCategoryAliases(string $category_slug): array
    {
        static::init();

        return DB::select("
        SELECT raw_value, normalized, source
        FROM category_mappings
        WHERE category_slug = ?
          AND deleted_at IS NULL
    ", [$category_slug]);
    }

    /**
     * Busca una categoría a partir de un string
     * - Primero busca en categories por slug
     * - Luego en category_mappings por normalized
     */
    public static function findCategory(string $category): ?array
    {
        static::init();

        $normalized = Strings::normalize($category);

        // Buscar en categories por slug exacto
        $cat = DB::selectOne("
        SELECT id, slug, name 
        FROM categories 
        WHERE slug = ? AND deleted_at IS NULL
        LIMIT 1
    ", [$normalized]);

        if ($cat) {
            return [
                'category_id' => $cat['id'],
                'category_slug' => $cat['slug'],
                'name' => $cat['name'],
                'found_in' => 'categories'
            ];
        }

        // Buscar en category_mappings por normalized
        $map = DB::selectOne("
        SELECT category_id, category_slug 
        FROM category_mappings
        WHERE normalized = ? AND deleted_at IS NULL
        LIMIT 1
    ", [$normalized]);

        if ($map) {
            return [
                'category_id' => $map['category_id'],
                'category_slug' => $map['category_slug'],
                'name' => null,
                'found_in' => 'mappings'
            ];
        }

        return null; // No encontrado
    }

     /**
     * Resuelve una categoría raw usando estrategia configurada
     *
     * Devuelve un array con:
     *  - category_slug
     *  - category_id (si existe)
     *  - created (bool) si la categoría fue creada
     *  - source (e.g. 'llm')
     *  - score (number)
     *  - reasoning (string)
     * 
     * - Utiliza primero static::findCategory() con los $category_slots 
     * Si hay resultado, retornarlo (no seguir) 
     * 
     * - Sino, utiliza la strategy por defecto (Ej: LLMMatchingStrategy) para enviando todos los campos en $slots conjuntamente 
     * con las *categorias existentes devueltas por static::getCategories() 
     * 
     * El prompt a usar pide que devuelva una categoria con nivel de confidence suficientemente alto o null / array. 
     * 
     */
    public static function resolve(string $raw): array
    {
        $raw = trim((string)$raw);
        if (empty($raw)) {
            return [];
        }

        static::init();

        // 1) Buscar coincidencia exacta en categories / mappings
        $found = static::findCategory($raw);
        if ($found) {
            return [
                'category_slug' => $found['category_slug'],
                'category_id' => $found['category_id'] ?? null,
                'found_in' => $found['found_in'],
                'created' => false,
                'score' => 100,
                'reasoning' => 'Exact match in ' . $found['found_in']
            ];
        }

        // 2) Preparar categorías disponibles
        $availableCategories = static::getCategories(); // slug => [id,name,parent_slug]

        // 3) Asegurar configuración/estrategias
        if (empty(self::$config)) {
            static::configure(); // carga valores por defecto
        }

        $strategiesOrder = self::$config['strategies_order'] ?? ['llm'];

        // 4) Iterar estrategias en orden
        foreach ($strategiesOrder as $strategyKey) {
            $strategies = static::getStrategies();
            if (!isset($strategies[$strategyKey])) {
                continue;
            }
            $strategy = $strategies[$strategyKey];

            $threshold = (self::$config['thresholds'][$strategyKey] ?? 0.7); // 0..1

            try {
                $res = $strategy->match($raw, $availableCategories, $threshold);
            } catch (\Throwable $e) {
                Logger::log("CategoryMapper: strategy {$strategyKey} exception: " . $e->getMessage());
                $res = null;
            }

            if (empty($res)) {
                // sin resultado -> intentar siguiente estrategia
                continue;
            }

            // Estructura esperada del resultado:
            // - ['category' => array|object|null, 'score'=>float, 'reasoning'=>string, 'is_new'=>bool, ...]
            $isNew = $res['is_new'] ?? false;
            $score = $res['score'] ?? 0;
            $reasoning = $res['reasoning'] ?? null;

            // Si LLM sugiere una nueva categoría
            if ($isNew) {
                $suggestedName = $res['sugested_name'] ?? null;
                $suggestedParentSlug = $res['sugested_parent_slug'] ?? null;

                if (empty($suggestedName)) {
                    // No podemos crear sin nombre sugerido: skip
                    continue;
                }

                // Crear slug propuesto (normalize)
                $newSlug = Strings::normalize($suggestedName);

                // Verificar que no exista
                $exists = DB::selectOne("SELECT id, slug FROM categories WHERE slug = ? AND deleted_at IS NULL LIMIT 1", [$newSlug]);
                if ($exists) {
                    // Ya existe (quizá LLM sugirió nombre distinto) -> crear mapping y devolver
                    static::saveCategoryAlias($newSlug, $raw, $strategyKey);
                    return [
                        'category_slug' => $exists['slug'],
                        'category_id' => $exists['id'],
                        'created' => false,
                        'source' => $strategyKey,
                        'score' => $score,
                        'reasoning' => "LLM suggested new but slug already existed. Created mapping.",
                        'is_new' => false
                    ];
                }

                // Crear nueva categoría en tabla categories
                $newId = uniqid('cat_');
                DB::insert("INSERT INTO categories (id, name, slug, parent_slug, created_at, updated_at) VALUES (?, ?, ?, ?, NOW(), NOW())", [
                    $newId,
                    $suggestedName,
                    $newSlug,
                    $suggestedParentSlug
                ]);

                // Crear mapping desde raw -> nueva categoria
                static::saveCategoryAlias($newSlug, $raw, $strategyKey);

                return [
                    'category_slug' => $newSlug,
                    'category_id' => $newId,
                    'created' => true,
                    'source' => $strategyKey,
                    'score' => $score,
                    'reasoning' => $reasoning,
                    'is_new' => true
                ];
            }

            // Si LLM devolvió un category existente (slug)
            $returnedSlug = $res['category'] ?? null;

            // Permitimos que 'category' venga como slug string o como array/object con name
            if (is_string($returnedSlug) && isset($availableCategories[$returnedSlug])) {
                // Crear mapping y devolver
                static::saveCategoryAlias($returnedSlug, $raw, $strategyKey);
                return [
                    'category_slug' => $returnedSlug,
                    'category_id' => $availableCategories[$returnedSlug]['id'] ?? null,
                    'created' => false,
                    'source' => $strategyKey,
                    'score' => $score,
                    'reasoning' => $reasoning,
                    'is_new' => false
                ];
            } elseif (is_array($returnedSlug) || is_object($returnedSlug)) {
                // Intentar inferir slug comparando por name con availableCategories
                $returnedName = is_array($returnedSlug) ? ($returnedSlug['name'] ?? null) : ($returnedSlug->name ?? null);
                if ($returnedName) {
                    foreach ($availableCategories as $slug => $cat) {
                        if ($cat['name'] === $returnedName) {
                            static::saveCategoryAlias($slug, $raw, $strategyKey);
                            return [
                                'category_slug' => $slug,
                                'category_id' => $cat['id'] ?? null,
                                'created' => false,
                                'source' => $strategyKey,
                                'score' => $score,
                                'reasoning' => $reasoning,
                                'is_new' => false
                            ];
                        }
                    }
                }
            }

            // Si ninguna de las condiciones anteriores, continuar con la siguiente estrategia
        }

        // No se resolvió
        return [
            'category_slug' => null,
            'category_id' => null,
            'created' => false,
            'source' => null,
            'score' => 0,
            'reasoning' => 'No match found',
            'is_new' => false
        ];
    }
    
    /**
     * Resuelve categorías para un producto completo
     *
     * @param array|object $product Producto con campos catego_raw1 / catego_raw2 / catego_raw3 y opcional description
     * @param bool $useDescription Si debe analizar description como fallback
     * @return array Array de slugs únicos de categorias a las que pertenece
     */
    public static function resolveProduct($product, bool $useDescription = false): array
    {
        static::init();

        // Normalizar objeto/array a array
        if (is_object($product)) {
            $product = (array)$product;
        } elseif (!is_array($product)) {
            return [];
        }

        $category_slots = ['catego_raw1', 'catego_raw2', 'catego_raw3'];

        $resultSlugs = [];

        // 1) Intentar coincidencias directas (findCategory) para cada slot
        foreach ($category_slots as $slot) {
            if (!empty($product[$slot])) {
                $found = static::findCategory($product[$slot]);
                if ($found && !empty($found['category_slug'])) {
                    $resultSlugs[] = $found['category_slug'];
                    // don't continue trying to resolve this slot
                    continue;
                }

                // Si no hay coincidencia directa, intentar resolver con estrategias
                $resolved = static::resolve($product[$slot]);
                if (!empty($resolved['category_slug'])) {
                    $resultSlugs[] = $resolved['category_slug'];
                }
            }
        }

        // 2) Si no encontramos nada y queremos usar description como fallback
        if (empty($resultSlugs) && $useDescription && !empty($product['description'])) {
            $resolved = static::resolve($product['description']);
            if (!empty($resolved['category_slug'])) {
                $resultSlugs[] = $resolved['category_slug'];
            }
        }

        // Unificar y devolver
        $unique = array_values(array_unique(array_filter($resultSlugs)));

        return $unique;
    }


     /**
     * Configura las estrategias de matching
     */
    public static function configure(array $config = []): void
    {
        self::$config = array_merge([
            'default_strategy' => 'llm',
            'fallback_strategy' => 'fuzzy',
            'strategies_order' => ['llm'],
            'batch_size' => 1,
            'llm_model' => 'qwen2.5:3b',
            'llm_temperature' => 0.2,
            'llm_max_tokens' => 500,
            'llm_verbose' => false,
            'thresholds' => [
                'fuzzy' => 0.40,
                'llm' => 0.70,
            ]
        ], $config);

        // Inicializar estrategias por defecto
        if (empty(self::$strategies)) {
            self::$strategies = [
                'llm' => new LLMMatchingStrategy(
                    self::$config['llm_model'],
                    self::$config['llm_temperature'],
                    self::$config['llm_max_tokens'],
                    self::$config['llm_verbose']
                )
            ];
        }
    }

    /**
     * Establece las estrategias de matching a usar
     */
    public static function setStrategies(array $strategies): void
    {
        self::$strategies = $strategies;
    }

    /**
     * Obtiene las estrategias configuradas
     */
    public static function getStrategies(): array
    {
        if (empty(self::$strategies)) {
            self::configure(); // Inicializar con valores por defecto
        }
        return self::$strategies;
    }
}
