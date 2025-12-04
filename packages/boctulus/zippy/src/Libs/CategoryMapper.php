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
     * Si hay resultado, lo evalúa antes de retornarlo para evitar mapeos incorrectos
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

        // Inicializar variable para evitar undefined variable
        $isSuspiciousMapping = false;

        // 1) Buscar coincidencia exacta en categories / mappings
        $found = static::findCategory($raw);
        if ($found) {
            // Antes de retornar la coincidencia exacta, verificar si es razonable
            // Comparar si la coincidencia exacta tiene sentido dada la descripción del producto
            $normalizedRaw = strtolower(Strings::normalize($raw));
            $categorySlug = $found['category_slug'];

            // Verificar si hay palabras en el producto que no encajan con la categoría encontrada
            // Por ejemplo, si el producto contiene "PC", "COMPUTER", "CELULAR", etc. pero está categorizado como "golosinas"
            $isSuspiciousMapping = static::isSuspiciousMapping($normalizedRaw, $categorySlug);

            if ($isSuspiciousMapping) {
                // Si la coincidencia parece incorrecta, intentar LLM antes de usar el mapping
                Logger::log("Suspicious mapping detected: '$raw' -> '$categorySlug'. Trying LLM analysis instead.");
            } else {
                // La coincidencia parece razonable, retornarla
                return [
                    'category_slug' => $found['category_slug'],
                    'category_id' => $found['category_id'] ?? null,
                    'found_in' => $found['found_in'],
                    'created' => false,
                    'score' => 100,
                    'reasoning' => 'Exact match in ' . $found['found_in']
                ];
            }
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

        // Si no se pudo resolver con LLM pero había una coincidencia anterior, usarla
        // pero solo si no fue marcada como sospechosa
        if ($found && !$isSuspiciousMapping) {
            return [
                'category_slug' => $found['category_slug'],
                'category_id' => $found['category_id'] ?? null,
                'found_in' => $found['found_in'],
                'created' => false,
                'score' => 100,
                'reasoning' => 'Exact match in ' . $found['found_in']
            ];
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
     * Verifica si un mapeo es sospechoso comparando palabras clave
     */
    private static function isSuspiciousMapping(string $normalizedRaw, string $categorySlug): bool
    {
        // Palabras clave que indican categorías tecnológicas/electrónicas
        $techKeywords = ['pc', 'comput', 'laptop', 'notebook', 'celular', 'telefono', 'smartphone',
                        'tablet', 'smart', 'teclado', 'mouse', 'monitor', 'cpu', 'ram', 'hdd',
                        'ssd', 'camara', 'fono', 'audifon', 'cargador', 'parlante', 'impresor', 'fono', 'cam',
                        'dell', 'acer', 'lenovo', 'hp', 'note', 'lat', 'i7', 'i5', 'i3', 'intel', 'amd',
                        'aire acon', 'aireacond', 'aireacondicionado', 'heladera', 'led', 'bgh', 'ctz',
                        'calefactor', 'calef', 'electro', 'electronic', 'tech', 'tecnologia', 'aire',
                        'acondicionado', 'bgh', 'bgw', 'calefon', 'calefón', 'termotanque', 'electrodomestico',
                        'electrodoméstico', 'aparato', 'electronic'];

        // Palabras clave para embutidos/carnes que no deberían estar en 'golosinas' o 'frutas-y-verduras' o 'gourmetfood'
        $meatKeywords = ['salchi', 'chori', 'embuti', 'jamon', 'jamón', 'mortadela', 'salame', 'peperoni',
                        'vienna', 'viena', 'frankfurt', 'knackwurst', 'hamburguesa', 'medallon', 'medallón',
                        'tocino', 'pavo', 'pollo', 'carne', 'vacuna', 'porcina', 'cerdo', 'embut', 'embuti',
                        'salami', 'salam', 'chori', 'churri', 'bondiola', 'morcilla', 'salame'];

        // Palabras clave que indican categorías de ropa/vestimenta
        $clothingKeywords = ['remera', 'pantalon', 'campera', 'zapat', 'calzado', 'vestido',
                            'short', 'buzo', 'camisa', 'medias', 'calza', 'abrig', 'sombrer', 'gorro'];

        // Palabras clave que indican categorías de hogar/bazar
        $homeKeywords = ['utensilio', 'cocina', 'plato', 'vaso', 'cuchara', 'tenedor', 'cuchillo',
                        'sarten', 'olla', 'jarra', 'copa', 'taza', 'cubiertos', 'menaje'];

        // Palabras clave para identificar productos frescos que no deberían estar en otras categorías
        // REMOVED 'dce' from here
        $freshKeywords = ['batata', 'papa', 'membrillo', 'fruta', 'verdura', 'zanahoria',
                         'cebolla', 'tomate', 'lechuga', 'frut', 'verd', 'fresc', 'naranja', 'manzana'];

        // Palabras clave para dulces/golosinas
        $sweetKeywords = ['dce/', 'dce ', 'dce', 'dulce de', 'dulce', 'mermelada', 'jalea', 'chocolate', 'caramelo', 'alfajor', 'golosina'];

        // Palabras clave para panadería que no debería estar en 'frescos'
        $bakeryKeywords = ['pan ', 'pana', 'panb', 'pani', 'panl', 'panr', 'pano', 'panc', 'panet',
                          'panque', 'bizco', 'pionono', 'gallet', 'tostada', 'harina', 'grisin', 't/emp', 'tapas', 'empanada'];

        // Palabras clave para pastas que no deberían estar en 'frescos' o 'frutas-y-verduras'
        $pastaKeywords = ['fid', 'fideos', 'fideo', 'pasta', 'tallarin', 'tallarines', 'raviole', 'ravioles',
                         'ñoquis', 'canelones', 'lasagna', 'lasaña', 'spaguetti', 'espagueti', 'macarron',
                         'penne', 'rigatoni', 'fetuccini', 'capeletti'];

        // Palabras clave para productos de bebidas
        $drinkKeywords = ['agua', 'gaseosa', 'bebida', 'cerveza', 'vino', 'whisky', 'coca', 'pepsi',
                         'jugo', 'soda', 'tonica', 'gintonic', 'fernet', 'vodka', 'ron', 'bebidas'];

        // Obtener todas las palabras del texto normalizado
        $words = explode(' ', $normalizedRaw);

        // Verificar si hay coincidencias de palabras clave
        foreach ($words as $word) {
            // Si el producto parece tecnológico pero está en categorías de comida, es sospechoso
            foreach ($techKeywords as $techWord) {
                if (strpos($word, $techWord) !== false) {
                    if (in_array($categorySlug, ['golosinas', 'bebidas', 'frescos', 'panaderia', 'lacteos', 'carnes', 'verduleria', 'almacen', 'gourmetfood', 'premium snacks and treats', 'gourmet food', 'frutas-y-verduras', 'frutas y verduras'])) {
                        return true;
                    }
                }
            }

            // Si el producto parece de embutidos pero está en categorías incorrectas, es sospechoso
            foreach ($meatKeywords as $meatWord) {
                if (strpos($word, $meatWord) !== false) {
                    if (in_array($categorySlug, ['golosinas', 'frutas-y-verduras', 'frutas y verduras', 'gourmetfood', 'bebidas', 'panaderia'])) {
                        return true;
                    }
                }
            }

            // Si el producto parece de ropa pero está en comestibles, es sospechoso
            foreach ($clothingKeywords as $clothingWord) {
                if (strpos($word, $clothingWord) !== false) {
                    if (in_array($categorySlug, ['golosinas', 'bebidas', 'frescos', 'panaderia', 'lacteos', 'carnes', 'verduleria', 'almacen', 'gourmetfood', 'frutas-y-verduras'])) {
                        return true;
                    }
                }
            }

            // Si el producto parece de hogar/bazar pero está en comestibles, es sospechoso
            foreach ($homeKeywords as $homeWord) {
                if (strpos($word, $homeWord) !== false) {
                    if (in_array($categorySlug, ['golosinas', 'bebidas', 'frescos', 'panaderia', 'lacteos', 'carnes', 'verduleria', 'almacen', 'gourmetfood', 'frutas-y-verduras'])) {
                        return true;
                    }
                }
            }

            // Si el producto parece fresco pero está en categorías incorrectas
            foreach ($freshKeywords as $freshWord) {
                if (strpos($word, $freshWord) !== false) {
                    if (in_array($categorySlug, ['golosinas', 'bebidas', 'galletitas', 'almacen', 'lacteos', 'carnes', 'embutidos', 'gourmetfood']) && !in_array($categorySlug, ['frescos', 'verduleria', 'frutas-y-verduras'])) {
                        return true;
                    }
                }
            }

            // Si el producto parece dulce pero está en frescos/verduras
            foreach ($sweetKeywords as $sweetWord) {
                if (strpos($word, $sweetWord) !== false) {
                    if (in_array($categorySlug, ['frescos', 'verduleria', 'frutas-y-verduras', 'carnes', 'bebidas'])) {
                        return true;
                    }
                }
            }

            // Si el producto parece de panadería pero está en "frescos" o categorías incorrectas
            foreach ($bakeryKeywords as $bakeryWord) {
                if (strpos($word, $bakeryWord) !== false) {
                    if (in_array($categorySlug, ['frescos', 'verduleria', 'frutas-y-verduras', 'carnes', 'bebidas'])) {
                        return true;
                    }
                }
            }

            // Si el producto parece bebida pero no está en bebidas
            foreach ($drinkKeywords as $drinkWord) {
                if (strpos($word, $drinkWord) !== false) {
                    // Si el producto parece bebida pero NO está en bebidas, es sospechoso
                    if ($categorySlug !== 'bebidas' && in_array($categorySlug, ['golosinas', 'frescos', 'panaderia', 'gourmetfood', 'frutas-y-verduras', 'galletitas'])) {
                        return true;
                    }
                }
            }

            // Si el producto parece pasta pero está en categorías incorrectas
            foreach ($pastaKeywords as $pastaWord) {
                if (strpos($word, $pastaWord) !== false) {
                    if (in_array($categorySlug, ['frescos', 'verduleria', 'frutas-y-verduras', 'carnes', 'bebidas', 'golosinas'])) {
                        return true;
                    }
                }
            }
        }

        return false;
    }

    /**
     * Resuelve categorías para un producto completo
     *
     * @param array|object $product Producto con campos catego_raw1 / catego_raw2 / catego_raw3 y opcional description y brand
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
        $brandCategorySlug = null;
        $brandCategoryId = null;

        // 0) Verificar si el producto tiene marca y si esa marca tiene categoría en brand_categories
        if (!empty($product['brand'])) {
            $brand = $product['brand'];

            // Buscar la marca en la tabla brands
            $brandRecord = DB::selectOne("
                SELECT b.id
                FROM brands b
                WHERE b.brand = ? AND b.deleted_at IS NULL
                LIMIT 1
            ", [$brand]);

            if ($brandRecord) {
                $brandId = is_array($brandRecord) ? $brandRecord['id'] : $brandRecord->id;

                // Buscar la categoría de la marca con mejor confidence_level
                $brandCategoryRecord = DB::selectOne("
                    SELECT bc.category_id, c.slug, bc.confidence_level
                    FROM brand_categories bc
                    JOIN categories c ON bc.category_id = c.id
                    WHERE bc.brand_id = ? AND bc.deleted_at IS NULL AND c.deleted_at IS NULL
                    ORDER BY
                        CASE bc.confidence_level
                            WHEN 'high' THEN 1
                            WHEN 'medium' THEN 2
                            WHEN 'low' THEN 3
                            WHEN 'doubtful' THEN 4
                            ELSE 5
                        END
                    LIMIT 1
                ", [$brandId]);

                if ($brandCategoryRecord) {
                    $brandCategorySlug = is_array($brandCategoryRecord) ? $brandCategoryRecord['slug'] : $brandCategoryRecord->slug;
                    $brandCategoryId = is_array($brandCategoryRecord) ? $brandCategoryRecord['category_id'] : $brandCategoryRecord->category_id;

                    // Usar la categoría de la marca como categoría padre
                    $resultSlugs[] = $brandCategorySlug;
                }
            }
        }

        // 1) Intentar coincidencias directas (findCategory) para cada slot
        foreach ($category_slots as $slot) {
            if (!empty($product[$slot])) {
                $found = static::findCategory($product[$slot]);
                if ($found && !empty($found['category_slug'])) {
                    $candidateSlug = $found['category_slug'];

                    // Si tenemos una categoría de marca, validar que la categoría encontrada sea compatible
                    if ($brandCategorySlug && !static::isCategoryCompatible($candidateSlug, $brandCategorySlug)) {
                        // La categoría no es compatible, intentar resolver con estrategias
                        $resolved = static::resolve($product[$slot]);
                        if (!empty($resolved['category_slug']) && static::isCategoryCompatible($resolved['category_slug'], $brandCategorySlug)) {
                            $resultSlugs[] = $resolved['category_slug'];
                        }
                        continue;
                    }

                    $resultSlugs[] = $candidateSlug;
                    continue;
                }

                // Si no hay coincidencia directa, intentar resolver con estrategias
                $resolved = static::resolve($product[$slot]);
                if (!empty($resolved['category_slug'])) {
                    $candidateSlug = $resolved['category_slug'];

                    // Si tenemos una categoría de marca, validar compatibilidad
                    if ($brandCategorySlug && !static::isCategoryCompatible($candidateSlug, $brandCategorySlug)) {
                        continue; // Saltar categorías no compatibles
                    }

                    $resultSlugs[] = $candidateSlug;
                }
            }
        }

        // 2) Si no encontramos nada y queremos usar description como fallback
        if (empty($resultSlugs) && $useDescription && !empty($product['description'])) {
            $resolved = static::resolve($product['description']);
            if (!empty($resolved['category_slug'])) {
                $candidateSlug = $resolved['category_slug'];

                // Si tenemos una categoría de marca, validar compatibilidad
                if (!$brandCategorySlug || static::isCategoryCompatible($candidateSlug, $brandCategorySlug)) {
                    $resultSlugs[] = $candidateSlug;
                }
            }
        }

        // Unificar y devolver
        $unique = array_values(array_unique(array_filter($resultSlugs)));

        return $unique;
    }

    /**
     * Verifica si una categoría es compatible con la categoría de la marca
     * Una categoría es compatible si es la misma, es hija, nieta, etc. de la categoría de la marca
     *
     * @param string $categorySlug Slug de la categoría a validar
     * @param string $brandCategorySlug Slug de la categoría de la marca
     * @return bool
     */
    private static function isCategoryCompatible(string $categorySlug, string $brandCategorySlug): bool
    {
        // Si son la misma categoría, es compatible
        if ($categorySlug === $brandCategorySlug) {
            return true;
        }

        static::init();

        // Buscar la categoría candidata
        $category = DB::selectOne("
            SELECT parent_slug
            FROM categories
            WHERE slug = ? AND deleted_at IS NULL
            LIMIT 1
        ", [$categorySlug]);

        if (!$category) {
            return false;
        }

        $parentSlug = is_array($category) ? ($category['parent_slug'] ?? null) : ($category->parent_slug ?? null);

        // Si no tiene padre, no es compatible (a menos que sean la misma, ya verificado arriba)
        if (!$parentSlug) {
            return false;
        }

        // Si el padre es la categoría de la marca, es compatible
        if ($parentSlug === $brandCategorySlug) {
            return true;
        }

        // Recursivamente verificar si algún ancestro es la categoría de la marca
        return static::isCategoryCompatible($parentSlug, $brandCategorySlug);
    }

    /**
     * Resuelve múltiples productos en batch usando LLM
     *
     * @param array $products Array de productos
     * @param int $batch_size Tamaño del batch (default: 10)
     * @return array Categorías resueltas indexadas por posición
     */
    public static function resolveBatch(array $products, int $batchSize = 10): array
    {
        static::init();
        
        if (empty(self::$config)) {
            static::configure();
        }
        
        $availableCategories = static::getCategories();
        $threshold = self::$config['thresholds']['llm'] ?? 0.70;
        
        $strategies = static::getStrategies();
        $llmStrategy = $strategies['llm'] ?? null;
        
        if (!$llmStrategy) {
            // Fallback to sequential processing
            $results = [];
            foreach ($products as $idx => $product) {
                $results[$idx] = static::resolveProduct($product);
            }
            return $results;
        }
        
        $results = [];
        $batches = array_chunk($products, $batchSize, true);
        
        foreach ($batches as $batch) {
            $rawTexts = [];
            $indices = [];
            
            foreach ($batch as $idx => $product) {
                // Combinar catego_raw1, catego_raw2, catego_raw3, description
                $parts = array_filter([
                    $product['catego_raw1'] ?? '',
                    $product['catego_raw2'] ?? '',
                    $product['catego_raw3'] ?? '',
                    $product['description'] ?? ''
                ]);
                $text = implode(' | ', $parts);
                
                $rawTexts[] = $text;
                $indices[] = $idx;
            }
            
            // Llamada batch al LLM
            $batchResults = $llmStrategy->matchBatch($rawTexts, $availableCategories, $threshold);
            
            // Mapear resultados
            foreach ($indices as $i => $originalIdx) {
                if (isset($batchResults[$i]) && $batchResults[$i]) {
                    $results[$originalIdx] = [$batchResults[$i]['category']];
                } else {
                    $results[$originalIdx] = [];
                }
            }
        }
        
        return $results;
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
                'llm' => 0.70,  // Reducido de 0.85 a 0.70 para ser menos estricto
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
