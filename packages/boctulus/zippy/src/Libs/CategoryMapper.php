<?php

namespace Boctulus\Zippy\Libs;

use Boctulus\Simplerest\Core\Libs\DB;
use Boctulus\Simplerest\Core\Libs\Strings;
use Boctulus\Zippy\Strategies\LLMMatchingStrategy;
use Kreait\Firebase\Database\Query\Filter\StartAt;

/*
    Notas:

    - `Strings::normalize()` se usa para homogeneizar alias y búsqueda.  
    - `saveCategoryAlias()` previene duplicados y actualiza `source` si cambia.  
    - `findCategory()` busca primero en `categories`, luego en `category_mappings`.  
    - Devuelve un array con `category_id`, `category_slug`, y un flag `found_in` para saber si vino de `categories` o de `mappings`.  

    https://chatgpt.com/c/68f07059-9de4-8324-b62d-9611fbe709c9
*/

class CategoryMapper
{
    protected $strategies = [];
    protected $config = [];

    // Thresholds configurables
    const FUZZY_THRESHOLD = 0.40; // 40% similarity mínima
    const HIGH_CONFIDENCE = 70;   // 70% para auto-aplicar fuzzy/LLM
    const LOW_CONFIDENCE = 50;    // <50% requiere revisión manual

    static function init(){
        DB::setConnection('zippy');
    }

    static function getCategories(){
        static::init();

        return DB::table('categories')
        ->get();
    }

    /**
     * Guarda un alias entre un nombre alternativo de categoría y una categoría existente
     */
    public function saveCategoryAlias(string $category_slug, string $raw_value, ?string $source = null): void
    {
        $normalized = Strings::normalize($raw_value);

        static::init();

        // Check si ya existe
        $exists = DB::selectOne("
        SELECT id FROM category_mappings 
        WHERE normalized = ? 
          AND category_slug = ?
          AND deleted_at IS NULL
        LIMIT 1
    ", [$normalized, $category_slug]);

        if ($exists) {
            // Actualizar source si cambia
            if ($source) {
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
    static function getCategoryAliases(string $category_slug): array
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
    static function findCategory(string $category): ?array
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
     * (de momento usar directamente LLMMatchingStrategy estaria bien)
     *
     * @param string $raw Valor raw de categoría
     * @return ?string slug de categoria en `categories`
     */
    static function resolve(string $raw): array
    {
        if (empty($raw)) {
            return [];
        }

        /*
            Lo que envia es un slot (palabra) que corresponderia a una categoria
            que no hace match con ninguna al usar findCategory() a una strategy
            que puede devolver:
            
            a) Que la categoria no es nueva (is_new = false).
            
            Esto implica crear el "mapping" en `category_mappings`

            b) Que la categoria *es* nueva (is_new = true)
            
            Recibes el nombre sugerido de la categoria y el slug de la categoria padre
            para que la crees en `categories`
            
            ---

            Detalles de respuesta del LLM en LLMMatchingStrategy::buildPrompt()
            
            En todos los casos devuelves el slug en `categories` ya sea de la categoria
            encontrada o creada
        */

        return [];
    }
    
    /**
     * Resuelve categorías para un producto completo
     * Lee catego_raw1, catego_raw2, catego_raw3 y opcionalmente description
     *
     * @param array|object $product Producto con campos catego_rawX
     * @param bool $useDescription Si debe analizar description como fallback
     * @return array Array de slugs únicos de categorias a las que pertenece
     */
    static function resolveProduct($product, bool $useDescription = false): array
    {
        $category_slots = ['catego_raw1', 'catego_raw2', 'catego_raw3'];

        $slots = $category_slots;
        if ($useDescription){
            $slots = array_merge($slots, ['description']);
        }

        // Solo dejo slots que contienen informacion
        foreach ($slots as $ix => $slot){
            if (!isset($product[$slot]) || empty($product[$slot])){
                unset($slots[$ix]);
            }
        }

        $result = [];

        /*
            Usar static::findCategory() con los $category_slots
            Si hay resultado, retornarlo (no seguir)
        */

        /*
            Usar LLMMatchingStrategy para enviando todos los campos en $slots
            conjuntamente con las categorias existentes devueltas por static::getCategories()

            El prompt a usar debe pedir que devuelva una o mas categorias con nivel de confidence alto
            o null / array vacio ninguna haria match.
        */

        return $result;
    }


     /**
     * Configura las estrategias de matching
     */
    static function configure(array $config = []): void
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
    static function setStrategies(array $strategies): void
    {
        self::$strategies = $strategies;
    }

    /**
     * Obtiene las estrategias configuradas
     */
    static function getStrategies(): array
    {
        if (empty(self::$strategies)) {
            self::configure(); // Inicializar con valores por defecto
        }
        return self::$strategies;
    }
}
