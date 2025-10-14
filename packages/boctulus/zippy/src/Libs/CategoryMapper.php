<?php

namespace Boctulus\Zippy\Libs;

use Boctulus\Simplerest\Core\Libs\DB;
use Boctulus\Simplerest\Core\Libs\Strings;
use Boctulus\Zippy\Contracts\CategoryMatchingStrategyInterface;
use Boctulus\Zippy\Strategies\LLMMatchingStrategy;
use Boctulus\Zippy\Strategies\FuzzyMatchingStrategy;

/**
 * CategoryMapper
 *
 * Sistema inteligente de mapeo de categorías desde valores raw (scraping)
 * hacia categorías normalizadas en la BD usando patrón estrategia.
 *
 * Estrategias de matching (en orden de prioridad):
 * 1. Exact mapping en category_mappings
 * 2. Direct match en categories (name/slug)
 * 3. Token/keyword heuristics
 * 4. Estrategias configurables (LLM, Fuzzy, etc.)
 * 5. Fallback a unmapped
 */
class CategoryMapper
{
    protected static $cache = [];
    protected static $categoriesCache = null;
    protected static $keywordMap = null;
    protected static $strategies = [];
    protected static $config = [];

    // Thresholds configurables
    const FUZZY_THRESHOLD = 0.40; // 40% similarity mínima
    const HIGH_CONFIDENCE = 70;   // 70% para auto-aplicar fuzzy/LLM
    const LOW_CONFIDENCE = 50;    // <50% requiere revisión manual

    /**
     * Configura las estrategias de matching
     */
    public static function configure(array $config = []): void
    {
        self::$config = array_merge([
            'default_strategy' => 'fuzzy',
            'fallback_strategy' => 'fuzzy',
            'strategies_order' => ['fuzzy'],
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
                'fuzzy' => new FuzzyMatchingStrategy(),
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

    /**
     * Obtiene una estrategia específica por nombre
     */
    public static function getStrategy(string $name): ?CategoryMatchingStrategyInterface
    {
        $strategies = self::getStrategies();
        return $strategies[$name] ?? null;
    }

    /**
     * Registra una nueva estrategia
     */
    public static function registerStrategy(string $name, CategoryMatchingStrategyInterface $strategy): void
    {
        self::$strategies[$name] = $strategy;
    }

    /**
     * Obtiene las categorías disponibles en formato para las estrategias
     */
    protected static function getAvailableCategories(): array
    {
        if (self::$categoriesCache === null) {
            DB::setConnection('zippy');
            self::$categoriesCache = DB::select("SELECT * FROM categories WHERE deleted_at IS NULL");
        }

        $availableCategories = [];
        foreach (self::$categoriesCache as $cat) {
            $availableCategories[$cat->slug] = $cat;
        }

        return $availableCategories;
    }

    /**
     * Verifica si una estrategia está disponible
     */
    public static function isStrategyAvailable(CategoryMatchingStrategyInterface $strategy): bool
    {
        if (!$strategy->requiresExternalService()) {
            return true;
        }

        // Verificación especial para LLM
        if ($strategy instanceof LLMMatchingStrategy) {
            return LLMMatchingStrategy::isAvailable();
        }

        return true;
    }

    /**
     * Busca mapping exacto en category_mappings (caché incluido)
     */
    public static function findMapping(string $raw): ?array
    {
        $norm = Strings::normalize($raw);

        if (isset(self::$cache[$norm])) {
            return self::$cache[$norm];
        }

        DB::setConnection('zippy');
        $row = DB::select("SELECT * FROM category_mappings WHERE normalized = ? AND deleted_at IS NULL LIMIT 1", [$norm], 'ASSOC', null, true);

        $result = $row ? (array)$row : null;
        self::$cache[$norm] = $result;

        return $result;
    }

    /**
     * Busca coincidencia directa en categories por name o slug
     */
    public static function findCategoryByNameOrSlug(string $raw): ?object
    {
        $norm = Strings::normalize($raw);
        $slug = str_replace(' ', '-', $norm);

        // Lazy load categories cache
        if (self::$categoriesCache === null) {
            DB::setConnection('zippy');
            self::$categoriesCache = DB::select("SELECT * FROM categories WHERE deleted_at IS NULL");
        }

        foreach (self::$categoriesCache as $cat) {
            $catNameNorm = Strings::normalize($cat->name);
            $catSlugNorm = Strings::normalize($cat->slug);

            if ($catNameNorm === $norm || $catSlugNorm === $slug || $catSlugNorm === $norm) {
                return $cat;
            }
        }

        return null;
    }

    /**
     * Mapa de keywords -> category slug para matching heurístico
     */
    protected static function getKeywordMap(): array
    {
        if (self::$keywordMap !== null) {
            return self::$keywordMap;
        }

        self::$keywordMap = [
            // Almacén
            'aceite' => 'almacen',
            'aceites' => 'almacen',
            'condimento' => 'almacen',
            'condimentos' => 'almacen',
            'salsa' => 'almacen',
            'salsas' => 'almacen',
            'aderezo' => 'almacen',
            'aderezos' => 'almacen',
            'arroz' => 'almacen',
            'legumbre' => 'almacen',
            'legumbres' => 'almacen',
            'cereal' => 'almacen',
            'cereales' => 'almacen',
            'conserva' => 'almacen',
            'conservas' => 'almacen',
            'encurtido' => 'almacen',
            'encurtidos' => 'almacen',
            'especia' => 'almacen',
            'especias' => 'almacen',
            'harina' => 'almacen',
            'harinas' => 'almacen',
            'pasta' => 'almacen',
            'pastas' => 'almacen',
            'rebozador' => 'almacen',
            'rallado' => 'almacen',
            'sopa' => 'almacen',
            'sopas' => 'almacen',
            'caldo' => 'almacen',
            'caldos' => 'almacen',
            'pure' => 'almacen',
            'tomate' => 'almacen',
            'mermelada' => 'almacen',
            'mermeladas' => 'almacen',
            'dulce' => 'almacen',
            'dulces' => 'almacen',

            // Bebidas e infusiones
            'infusion' => 'infusiones',
            'infusiones' => 'infusiones',
            'te' => 'infusiones',
            'cafe' => 'infusiones',
            'mate' => 'infusiones',
            'bebida' => 'bebidas',
            'bebidas' => 'bebidas',
            'jugo' => 'bebidas',
            'jugos' => 'bebidas',
            'gaseosa' => 'bebidas',
            'gaseosas' => 'bebidas',
            'agua' => 'bebidas',
            'aperitivo' => 'aperitivos',
            'aperitivos' => 'aperitivos',

            // Golosinas
            'golosina' => 'golosinas',
            'golosinas' => 'golosinas',
            'caramelo' => 'golosinas',
            'caramelos' => 'golosinas',
            'chocolate' => 'golosinas',
            'chocolates' => 'golosinas',
            'alfajor' => 'alfajores',
            'alfajores' => 'alfajores',
            'bombon' => 'bombones',
            'bombones' => 'bombones',
            'snack' => 'aperitivos',
            'snacks' => 'aperitivos',

            // Galletitas
            'galletita' => 'galletitas',
            'galletitas' => 'galletitas',
            'galleta' => 'galletitas',
            'galletas' => 'galletitas',

            // Lácteos y frescos
            'leche' => 'lacteos',
            'lacteo' => 'lacteos',
            'lacteos' => 'lacteos',
            'queso' => 'lacteos',
            'quesos' => 'lacteos',
            'yogur' => 'lacteos',
            'yogurt' => 'lacteos',
            'manteca' => 'lacteos',
            'crema' => 'lacteos',
            'fresco' => 'frescos',
            'frescos' => 'frescos',
            'carne' => 'carnes',
            'carnes' => 'carnes',
            'embutido' => 'embutidos',
            'embutidos' => 'embutidos',
            'jamon' => 'embutidos',
            'salame' => 'embutidos',
            'mortadela' => 'embutidos',

            // Verdulería
            'verdura' => 'verduleria',
            'verduras' => 'verduleria',
            'fruta' => 'verduleria',
            'frutas' => 'verduleria',
            'vegetal' => 'verduleria',
            'vegetales' => 'verduleria',

            // Dietéticas
            'dietetica' => 'dieteticas',
            'dieteticas' => 'dieteticas',
            'light' => 'dieteticas',
            'diet' => 'dieteticas',
            'organico' => 'dieteticas',
            'organicos' => 'dieteticas',
            'natural' => 'dieteticas',
            'integral' => 'dieteticas',
            'suplemento' => 'dieteticas',
            'suplementos' => 'dieteticas',
            'endulzante' => 'dieteticas',
            'endulzantes' => 'dieteticas',
            'edulcorante' => 'dieteticas',

            // Higiene y limpieza
            'higiene' => 'higiene',
            'shampoo' => 'higiene',
            'jabon' => 'higiene',
            'crema' => 'higiene',
            'desodorante' => 'higiene',
            'limpieza' => 'limpieza',
            'detergente' => 'limpieza',
            'lavandina' => 'limpieza',
            'suavizante' => 'limpieza',

            // Hogar y bazar
            'hogar' => 'hogar-y-bazar',
            'bazar' => 'hogar-y-bazar',
            'electro' => 'electro',
            'electrodomestico' => 'electro',

            // Otros
            'perfume' => 'perfumes',
            'perfumes' => 'perfumes',
            'fragancia' => 'perfumes',
            'juguete' => 'jugueteria',
            'juguetes' => 'jugueteria',
            'libro' => 'libreria',
            'libros' => 'libreria',
            'cuaderno' => 'libreria',
            'lapiz' => 'libreria',
            'ferreteria' => 'ferreteria',
            'herramienta' => 'ferreteria',
            'mascota' => 'mascotas',
            'mascotas' => 'mascotas',
            'perro' => 'mascotas',
            'gato' => 'mascotas',
            'congelado' => 'congelados',
            'congelados' => 'congelados',
            'helado' => 'congelados',
            'gastronomico' => 'gastronomicos',
            'gastronomicos' => 'gastronomicos',
            'bebe' => 'almacen', // o crear categoría específica
            'bebes' => 'almacen',
            'nino' => 'almacen',
            'ninos' => 'almacen',
            'pañal' => 'higiene',
            'panal' => 'higiene',
        ];

        return self::$keywordMap;
    }

    /**
     * Intenta matching por tokens/keywords en el raw
     * Retorna [slug, token_matched] o null
     */
    public static function matchByTokens(string $raw): ?array
    {
        $norm = Strings::normalize($raw);
        $tokens = preg_split('/[\s,\/\-]+/', $norm, -1, PREG_SPLIT_NO_EMPTY);

        $kwMap = self::getKeywordMap();

        foreach ($tokens as $token) {
            if (isset($kwMap[$token])) {
                return ['slug' => $kwMap[$token], 'token' => $token];
            }
        }

        return null;
    }

    /**
     * Fuzzy matching: similarity con todas las categorías
     * Retorna ['category' => object, 'score' => float] o null
     */
    public static function fuzzyMatch(string $raw, ?float $threshold = null): ?array
    {
        $threshold = $threshold ?? self::FUZZY_THRESHOLD;
        $norm = Strings::normalize($raw);

        if (self::$categoriesCache === null) {
            DB::setConnection('zippy');
            self::$categoriesCache = DB::select("SELECT * FROM categories WHERE deleted_at IS NULL");
        }

        $best = null;
        $bestScore = 0;

        foreach (self::$categoriesCache as $cat) {
            $nameNorm = Strings::normalize($cat->name);
            $slugNorm = Strings::normalize($cat->slug);

            // Probar con name
            similar_text($norm, $nameNorm, $percName);
            similar_text($norm, $slugNorm, $percSlug);

            $perc = max($percName, $percSlug);

            if ($perc > $bestScore) {
                $bestScore = $perc;
                $best = $cat;
            }
        }

        // Convertir threshold de 0-1 a 0-100 si es necesario
        $thresholdPercent = ($threshold < 1) ? $threshold * 100 : $threshold;

        if ($bestScore >= $thresholdPercent) {
            return ['category' => $best, 'score' => $bestScore];
        }

        return null;
    }

    /**
     * Guarda un nuevo mapping en category_mappings
     */
    protected static function saveMapping(array $data): void
    {
        $normalized = Strings::normalize($data['raw_value']);

        DB::setConnection('zippy');

        // Check si ya existe
        $exists = DB::select("SELECT * FROM category_mappings WHERE normalized = ? AND deleted_at IS NULL LIMIT 1", [$normalized], 'ASSOC', null, true);

        if ($exists) {
            // Actualizar si cambió
            $sql = "UPDATE category_mappings SET
                    category_id = ?,
                    category_slug = ?,
                    mapping_type = ?,
                    confidence = ?,
                    notes = ?,
                    updated_at = ?
                    WHERE id = ?";

            DB::update($sql, [
                $data['category_id'] ?? null,
                $data['category_slug'] ?? null,
                $data['mapping_type'] ?? 'manual',
                $data['confidence'] ?? null,
                $data['notes'] ?? null,
                date('Y-m-d H:i:s'),
                $exists['id']
            ]);
        } else {
            $sql = "INSERT INTO category_mappings (raw_value, normalized, category_id, category_slug, mapping_type, confidence, notes, is_reviewed, created_at, updated_at)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

            DB::insert($sql, [
                $data['raw_value'],
                $normalized,
                $data['category_id'] ?? null,
                $data['category_slug'] ?? null,
                $data['mapping_type'] ?? 'manual',
                $data['confidence'] ?? null,
                $data['notes'] ?? null,
                $data['is_reviewed'] ?? false,
                date('Y-m-d H:i:s'),
                date('Y-m-d H:i:s'),
            ]);
        }

        // Invalidar caché
        self::$cache[$normalized] = null;
    }

    /**
     * Resuelve una categoría raw -> [slug1, slug2, ...] usando estrategias configuradas
     *
     * @param string $raw Valor raw de categoría
     * @param bool $autoSave Si debe guardar mappings automáticamente
     * @param string|null $strategyName Estrategia específica a usar, o null para usar orden configurado
     * @return array Array de slugs encontrados
     */
    public static function resolve(string $raw, bool $autoSave = true, ?string $strategyName = null): array
    {
        if (empty($raw)) {
            return [];
        }

        // 1) Buscar en category_mappings (caché)
        $mapping = self::findMapping($raw);
        if ($mapping && !empty($mapping['category_id'])) {
            DB::setConnection('zippy');
            $cat = DB::select("SELECT * FROM categories WHERE id = ? AND deleted_at IS NULL LIMIT 1", [$mapping['category_id']], 'ASSOC', null, true);

            if ($cat) {
                return [$cat['slug']];
            }
        }

        if ($mapping && !empty($mapping['category_slug'])) {
            return [$mapping['category_slug']];
        }

        // 2) Coincidencia directa en categories
        $cat = self::findCategoryByNameOrSlug($raw);
        if ($cat) {
            if ($autoSave) {
                self::saveMapping([
                    'raw_value' => $raw,
                    'category_id' => $cat->id,
                    'category_slug' => $cat->slug,
                    'mapping_type' => 'normalized',
                    'notes' => 'Auto-matched by direct name/slug',
                    'is_reviewed' => false,
                ]);
            }
            return [$cat->slug];
        }

        // 3) Token/keyword matching
        $tokenMatch = self::matchByTokens($raw);
        if ($tokenMatch) {
            if ($autoSave) {
                self::saveMapping([
                    'raw_value' => $raw,
                    'category_slug' => $tokenMatch['slug'],
                    'mapping_type' => 'substring',
                    'notes' => 'Matched by token: ' . $tokenMatch['token'],
                    'is_reviewed' => false,
                ]);
            }
            return [$tokenMatch['slug']];
        }

        // 4) Usar estrategias configuradas
        $availableCategories = self::getAvailableCategories();
        
        if ($strategyName) {
            // Usar estrategia específica
            $result = self::tryStrategy($raw, $strategyName, $availableCategories, $autoSave);
            if ($result) {
                return $result;
            }
        } else {
            // Usar orden de estrategias configurado
            self::configure(); // Asegurar que esté configurado
            $strategiesOrder = self::$config['strategies_order'] ?? ['fuzzy'];
            
            foreach ($strategiesOrder as $name) {
                $result = self::tryStrategy($raw, $name, $availableCategories, $autoSave);
                if ($result) {
                    return $result;
                }
            }
        }

        // 5) No match: marcar unmapped
        if ($autoSave) {
            self::saveMapping([
                'raw_value' => $raw,
                'mapping_type' => 'unmapped',
                'notes' => 'No match found - requires manual review',
                'is_reviewed' => false,
            ]);
        }

        return []; // Retornar vacío - caller decide fallback
    }

    /**
     * Intenta resolver usando una estrategia específica
     */
    protected static function tryStrategy(string $raw, string $strategyName, array $availableCategories, bool $autoSave): ?array
    {
        $strategy = self::getStrategy($strategyName);
        if (!$strategy) {
            return null;
        }

        // Verificar si la estrategia está disponible
        if (!self::isStrategyAvailable($strategy)) {
            return null;
        }

        try {
            $thresholds = self::$config['thresholds'] ?? [];
            $threshold = $thresholds[$strategyName] ?? null;
            
            $match = $strategy->match($raw, $availableCategories, $threshold);
            
            if (!$match) {
                return null;
            }

            $category = $match['category'];
            $score = $match['score'];
            $reasoning = $match['reasoning'] ?? '';
            $strategyUsed = $match['strategy'] ?? $strategyName;

            // Convertir a objeto si es array
            if (is_array($category)) {
                $category = (object)$category;
            }

            // Verificar que la categoría existe en BD
            $catInDb = self::findCategoryByNameOrSlug($category->slug ?? $category->name);
            if (!$catInDb) {
                return null;
            }

            $slug = $catInDb->slug;

            // Determinar confianza para auto-aplicar
            $confidence = $score;
            $shouldApply = true;
            $notes = "Matched by {$strategy->getName()}";
            
            if ($reasoning) {
                $notes .= " - {$reasoning}";
            }

            if ($confidence >= self::HIGH_CONFIDENCE) {
                $notes .= ' (High confidence)';
                $isReviewed = false;
            } elseif ($confidence >= self::LOW_CONFIDENCE) {
                $notes .= ' (Medium confidence - requires review)';
                $isReviewed = false;
            } else {
                $notes .= ' (Low confidence - requires review)';
                $isReviewed = false;
                // Aún se aplica pero se marca para revisión
            }

            if ($autoSave && $shouldApply) {
                self::saveMapping([
                    'raw_value' => $raw,
                    'category_id' => $catInDb->id,
                    'category_slug' => $slug,
                    'mapping_type' => $strategyUsed,
                    'confidence' => $confidence,
                    'notes' => $notes,
                    'is_reviewed' => $isReviewed,
                ]);
            }

            return [$slug];

        } catch (\Exception $e) {
            // Log error pero no fallar completamente
            error_log("Strategy {$strategyName} failed: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Resuelve múltiples categorías en lote
     */
    public static function resolveBatch(array $rawValues, bool $autoSave = true, ?string $strategyName = null): array
    {
        $results = [];
        $batchSize = self::$config['batch_size'] ?? 1;

        // Procesar en lotes
        $batches = array_chunk($rawValues, $batchSize);
        
        foreach ($batches as $batch) {
            foreach ($batch as $raw) {
                $results[$raw] = self::resolve($raw, $autoSave, $strategyName);
            }
            
            // Pausa pequeña entre lotes si hay múltiples
            if (count($batches) > 1) {
                usleep(100000); // 100ms
            }
        }

        return $results;
    }

    /**
     * Resuelve categorías para un producto completo
     * Lee catego_raw1, catego_raw2, catego_raw3 y opcionalmente description
     *
     * @param array|object $product Producto con campos catego_rawX
     * @param bool $useDescription Si debe analizar description como fallback
     * @return array Array de slugs únicos
     */
    public static function resolveProduct($product, bool $useDescription = false): array
    {
        $product = (array)$product;
        $slots = ['catego_raw1', 'catego_raw2', 'catego_raw3'];
        $result = [];

        foreach ($slots as $slot) {
            $raw = $product[$slot] ?? null;
            if (empty($raw)) continue;

            $slugs = self::resolve($raw, true);
            foreach ($slugs as $slug) {
                if (!in_array($slug, $result)) {
                    $result[] = $slug;
                }
            }
        }

        // Fallback: analizar description por keywords
        if (empty($result) && $useDescription && !empty($product['description'])) {
            $desc = $product['description'];
            $tokenMatch = self::matchByTokens($desc);
            if ($tokenMatch) {
                $result[] = $tokenMatch['slug'];
            }
        }

        // Fallback final: 'otros' si sigue vacío
        if (empty($result)) {
            $result[] = 'otros';
        }

        return array_unique($result);
    }

    /**
     * Limpia el caché interno
     */
    public static function clearCache(): void
    {
        self::$cache = [];
        self::$categoriesCache = null;
        self::$keywordMap = null;
    }

    /**
     * Obtiene estadísticas de mappings
     */
    public static function getStats(): array
    {
        DB::setConnection('zippy');

        $total = DB::select("SELECT COUNT(*) as count FROM category_mappings WHERE deleted_at IS NULL")[0]['count'];
        $mapped = DB::select("SELECT COUNT(*) as count FROM category_mappings WHERE deleted_at IS NULL AND category_id IS NOT NULL")[0]['count'];
        $unmapped = DB::select("SELECT COUNT(*) as count FROM category_mappings WHERE deleted_at IS NULL AND mapping_type = 'unmapped'")[0]['count'];
        $reviewed = DB::select("SELECT COUNT(*) as count FROM category_mappings WHERE deleted_at IS NULL AND is_reviewed = 1")[0]['count'];
        $needsReview = DB::select("SELECT COUNT(*) as count FROM category_mappings WHERE deleted_at IS NULL AND is_reviewed = 0 AND mapping_type IN ('fuzzy', 'unmapped')")[0]['count'];

        return [
            'total' => (int)$total,
            'mapped' => (int)$mapped,
            'unmapped' => (int)$unmapped,
            'reviewed' => (int)$reviewed,
            'needs_review' => (int)$needsReview,
            'mapping_rate' => $total > 0 ? round(($mapped / $total) * 100, 2) : 0,
        ];
    }
}
