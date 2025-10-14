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
 * hacia categorías normalizadas en la BD.
 *
 * Estrategias de matching (en orden de prioridad):
 * 1. Exact mapping en category_mappings
 * 2. Direct match en categories (name/slug)
 * 3. Token/keyword heuristics
 * 4. Fuzzy matching (similarity)
 * 5. Fallback a 'otros' o marcar unmapped
 */
class CategoryMapper
{
    protected static $cache = [];
    protected static $categoriesCache = null;
    protected static $keywordMap = null;
    protected static ?CategoryMatchingStrategyInterface $matchingStrategy = null;

    // Thresholds configurables
    const FUZZY_THRESHOLD = 0.40; // 40% similarity mínima
    const HIGH_CONFIDENCE = 70;   // 70% para auto-aplicar fuzzy/LLM
    const LOW_CONFIDENCE = 50;    // <50% requiere revisión manual

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
     * Resuelve una categoría raw -> [slug1, slug2, ...] usando todas las estrategias
     *
     * @param string $raw Valor raw de categoría
     * @param bool $autoSave Si debe guardar mappings automáticamente
     * @return array Array de slugs encontrados
     */
    public static function resolve(string $raw, bool $autoSave = true): array
    {
        if (empty($raw)) {
            return [];
        }

        $result = [];

        // 1) Buscar en category_mappings
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

        // 4) Fuzzy matching
        $fuzzy = self::fuzzyMatch($raw);
        if ($fuzzy) {
            $confidence = $fuzzy['score'];
            $cat = $fuzzy['category'];

            if ($confidence >= self::HIGH_CONFIDENCE) {
                // Alta confianza: auto-aplicar
                if ($autoSave) {
                    self::saveMapping([
                        'raw_value' => $raw,
                        'category_id' => $cat->id,
                        'category_slug' => $cat->slug,
                        'mapping_type' => 'fuzzy',
                        'confidence' => $confidence,
                        'notes' => 'High confidence fuzzy match',
                        'is_reviewed' => false,
                    ]);
                }
                return [$cat->slug];
            } elseif ($confidence >= self::LOW_CONFIDENCE) {
                // Confianza media: guardar para revisión pero usar
                if ($autoSave) {
                    self::saveMapping([
                        'raw_value' => $raw,
                        'category_id' => $cat->id,
                        'category_slug' => $cat->slug,
                        'mapping_type' => 'fuzzy',
                        'confidence' => $confidence,
                        'notes' => 'Medium confidence - requires review',
                        'is_reviewed' => false,
                    ]);
                }
                return [$cat->slug];
            }
            // else: confianza baja, no usar
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

        return []; // Retornar vacío en lugar de 'otros' - caller decide fallback
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
