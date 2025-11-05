<?php

namespace Boctulus\Zippy\Commands;

use Boctulus\Simplerest\Core\Libs\DB;
use Boctulus\Simplerest\Core\Libs\StdOut;
use Boctulus\Simplerest\Core\Libs\Strings;
use Boctulus\Simplerest\Core\Interfaces\ICommand;
use Boctulus\Simplerest\Core\Traits\CommandTrait;
use Boctulus\Zippy\Libs\CategoryMapper;

/**
 * Zippy Command
 *
 * Comandos para gestionar categorías y productos del sistema Zippy
 */
class ZippyCommand implements ICommand
{
    use CommandTrait;

    public function __construct()
    {
        DB::setConnection('zippy');
    }

    /**
     * Procesa productos y actualiza sus categorías
     * 
     * Uso: php com zippy products_process_categories --limit=100 --dry-run
     */
    public function products_process_categories(...$options)
    {
        $opts = $this->parseOptions($options);
        $limit = $opts['limit'] ?? 100;
        $dryRun = $opts['dry_run'] ?? false;
        $strategy = $opts['strategy'] ?? null;

        DB::setConnection('zippy');
        
        CategoryMapper::configure([
            'default_strategy' => 'llm',
            'strategies_order' => ['llm', 'fuzzy'],
            'thresholds' => [
                'fuzzy' => 0.40,
                'llm' => 0.70,
            ]
        ]);

        $query = DB::table('products');
        
        if ($limit) {
            $query->limit((int)$limit);
        }

        $products = $query->get();
        $processed = 0;
        $errors = 0;

        $total = count($products);
        echo "Procesando $total productos...\n";

        foreach ($products as $product) {
            try {
                $productId = is_array($product) ? ($product['ean'] ?? $product['id']) : ($product->ean ?? $product->id);
                echo "[$processed/$total] Procesando producto ID/EAN: $productId\n";
                
                $categories = CategoryMapper::resolveProduct($product, true);
                
                if (!empty($categories)) {
                    echo "  → Categorías asignadas: " . implode(', ', $categories) . "\n";
                    
                    if (!$dryRun) {
                        DB::table('products')
                            ->where('ean', $productId)
                            ->update([
                                'categories' => json_encode($categories)
                            ]);
                    }
                } else {
                    echo "  → No se encontraron categorías\n";
                }
                
                $processed++;
                
            } catch (\Exception $e) {
                $errors++;
                dd($e->getMessage(), "→ ERROR");
                continue;
            }
        }

        echo "\nResumen:\n";
        echo "- Productos procesados: $processed\n";
        echo "- Errores: $errors\n";
        
        if ($dryRun) {
            echo "- MODO SIMULACIÓN: No se realizaron cambios en la BD\n";
        }
        
        DB::closeConnection();
    }

    /**
     * Procesa categorías de productos en batch
     */
    public function process_categories(...$options)
    {
        $opts = $this->parseOptions($options);
        $limit = $opts['limit'] ?? null;
        $offset = $opts['offset'] ?? 0;
        $onlyUnmapped = $opts['only_unmapped'] ?? false;
        $dryRun = $opts['dry_run'] ?? false;

        StdOut::print("=== Procesando categorías de productos ===\n");

        if ($dryRun) {
            StdOut::print("⚠ Modo DRY-RUN activado: no se guardarán cambios\n");
        }

        $query = DB::table('products');

        if ($onlyUnmapped) {
            $query->where(function($q) {
                $q->whereNull('categories')
                  ->orWhereRaw("JSON_LENGTH(categories) = 0");
            });
        }

        if ($limit) {
            $query->limit($limit);
        }

        if ($offset) {
            $query->offset($offset);
        }

        $products = $query->get();
        $total = count($products);

        if ($total === 0) {
            StdOut::print("No hay productos para procesar.\n");
            return;
        }

        StdOut::print("Productos a procesar: {$total}\n");

        $processed = 0;
        $updated = 0;
        $errors = 0;

        foreach ($products as $product) {
            $processed++;
            $productId = is_array($product) ? ($product['id'] ?? null) : ($product->id ?? null);

            try {
                $categories = CategoryMapper::resolveProduct($product, true);

                if (empty($categories)) {
                    StdOut::print("[{$processed}/{$total}] Producto ID {$productId}: Sin categorías detectadas\n");
                    continue;
                }

                $categoriesJson = json_encode($categories);

                StdOut::print("[{$processed}/{$total}] Producto ID {$productId}: " . implode(', ', $categories) . "\n");

                if (!$dryRun) {
                    DB::table('products')
                        ->where(['id', $productId])
                        ->update([
                            'categories' => $categoriesJson
                        ]);
                    $updated++;
                }
            } catch (\Exception $e) {
                StdOut::print("[{$processed}/{$total}] ERROR en producto ID {$productId}: " . $e->getMessage() . "\n");
                $errors++;
            }
        }

        StdOut::print("\n=== Resumen ===\n");
        StdOut::print("Procesados: {$processed}\n");
        StdOut::print("Actualizados: " . ($dryRun ? "0 (dry-run)" : $updated) . "\n");
        StdOut::print("Errores: {$errors}\n");

        StdOut::print("\n");
        $this->map_stats();
    }

    /**
     * Prueba el mapeo de una categoría raw sin guardar
     */
    public function test_mapping(...$options)
    {
        $opts = $this->parseOptions($options);
        $raw = $opts['raw'] ?? null;
        $strategy = $opts['strategy'] ?? 'llm';

        if (empty($raw)) {
            StdOut::print("Error: Debes proporcionar un valor raw con --raw=\"valor\"\n");
            StdOut::print("Ejemplo: php com zippy test_mapping --raw=\"Aceites Y Condimentos\"\n");
            return;
        }

        CategoryMapper::configure([
            'default_strategy' => 'llm',
            'strategies_order' => ['llm', 'fuzzy'],
            'llm_model' => 'qwen2.5:3b',
            'llm_temperature' => 0.2,
            'thresholds' => [
                'fuzzy' => 0.40,
                'llm' => 0.70,
            ]
        ]);

        StdOut::print("Probando mapeo para: \"$raw\"\n");
        StdOut::print("Estrategia: $strategy\n\n");

        $result = CategoryMapper::resolve($raw, false, $strategy);

        if (!empty($result)) {
            StdOut::print("✅ Categoría asignada: " . implode(', ', $result) . "\n");
        } else {
            StdOut::print("❌ No se pudo asignar categoría\n");
        }

        // TODO: Implementar getStats() en CategoryMapper
        // $stats = CategoryMapper::getStats();
        // StdOut::print("\nEstadísticas de mapeo:\n");
        // foreach ($stats as $key => $value) {
        //     StdOut::print("- " . ucfirst(str_replace('_', ' ', $key)) . ": $value\n");
        // }
    }

    /**
     * Limpia el caché de CategoryMapper
     */
    public function clear_cache()
    {
        // TODO: Implementar clearCache() en CategoryMapper
        StdOut::print("⚠ Función clearCache() aún no implementada en CategoryMapper.\n");
    }

        /**
     * Setea el parent_slug de una categoría existente
     *
     * Uso:
     *  php com zippy category set --slug=dairy.milk --parent=dairy
     *  php com zippy category set --slug=dairy.milk --parent=NULL   // desempareja
     */
    protected function category_set(...$options)
    {
        DB::setConnection('zippy');

        $opts = $this->parseOptions($options);

        $slug = $opts['slug'] ?? $opts['s'] ?? null;
        $parent = array_key_exists('parent', $opts) ? $opts['parent'] : (array_key_exists('p', $opts) ? $opts['p'] : null);

        if (empty($slug)) {
            dd(['error' => 'Missing --slug'], 'Set category parent');
            DB::closeConnection();
            return;
        }

        // Normalizar caso especial para NULL (permitir NULL o 'NULL' o 'null')
        if (is_string($parent) && (strtoupper($parent) === 'NULL' || $parent === 'null')) {
            $parent = null;
        }

        // Verificar que la categoría destino exista
        $cat = DB::selectOne("SELECT id, slug, name, parent_slug FROM categories WHERE slug = ? AND deleted_at IS NULL LIMIT 1", [$slug]);
        if (!$cat) {
            dd(['error' => 'Category not found', 'slug' => $slug], 'Set category parent');
            DB::closeConnection();
            return;
        }

        // Si se pasó un parent no-nulo, verificar que exista (si se pasó vacío string lo tratamos como error)
        if ($parent !== null && $parent !== '') {
            $parentExists = DB::selectOne("SELECT id, slug FROM categories WHERE slug = ? AND deleted_at IS NULL LIMIT 1", [$parent]);
            if (!$parentExists) {
                dd(['error' => 'Parent category not found', 'parent' => $parent], 'Set category parent');
                DB::closeConnection();
                return;
            }
        }

        // Ejecutar update (usar NULL en la BD si $parent === null)
        if ($parent === null) {
            DB::update("UPDATE categories SET parent_slug = NULL, updated_at = NOW() WHERE slug = ? AND deleted_at IS NULL", [$slug]);
        } else {
            DB::update("UPDATE categories SET parent_slug = ?, updated_at = NOW() WHERE slug = ? AND deleted_at IS NULL", [$parent, $slug]);
        }

        DB::closeConnection();

        dd([
            'ok' => true,
            'slug' => $slug,
            'parent' => $parent
        ], 'Category parent updated');
    }

    /**
     * Lista categorías raw detectadas en products
     * Muestra entre corchetes la categoria padre si existe para la categoría mapeada (si se puede resolver)
     *
     * Uso: php com zippy category_list --limit=100
     */
    public function category_list(...$options)
    {
        $opts = $this->parseOptions($options);
        $limit = $opts['limit'] ?? 100;

        StdOut::print("=== Categorías raw detectadas en productos ===\n");

        DB::setConnection('zippy');

        $raw1 = DB::table('products')
            ->selectRaw('DISTINCT catego_raw1 as raw')
            ->whereNotNull('catego_raw1')
            ->whereRaw("catego_raw1 != ''")
            ->limit($limit)
            ->get();

        $raw2 = DB::table('products')
            ->selectRaw('DISTINCT catego_raw2 as raw')
            ->whereNotNull('catego_raw2')
            ->whereRaw("catego_raw2 != ''")
            ->limit($limit)
            ->get();

        $raw3 = DB::table('products')
            ->selectRaw('DISTINCT catego_raw3 as raw')
            ->whereNotNull('catego_raw3')
            ->whereRaw("catego_raw3 != ''")
            ->limit($limit)
            ->get();

        $all = array_merge(
            array_column((array)$raw1, 'raw'),
            array_column((array)$raw2, 'raw'),
            array_column((array)$raw3, 'raw')
        );

        $unique = array_unique(array_filter($all));
        sort($unique);

        StdOut::print("Categorías únicas encontradas: " . count($unique) . "\n\n");

        foreach ($unique as $idx => $raw) {
            $parentDisplay = '';

            // Intentar resolver la categoría usando CategoryMapper (si está disponible)
            try {
                // resolve devuelve un array de slug(s) o vacío
                $resolved = \Boctulus\Zippy\Libs\CategoryMapper::resolve($raw, false);

                if (!empty($resolved) && is_array($resolved)) {
                    // Tomamos el primer slug resuelto para mostrar su parent (si existe)
                    $mappedSlug = $resolved[0] ?? null;

                    if ($mappedSlug) {
                        $cat = DB::selectOne("SELECT parent_slug FROM categories WHERE slug = ? AND deleted_at IS NULL LIMIT 1", [$mappedSlug]);
                        $parentSlug = is_array($cat) ? ($cat['parent_slug'] ?? null) : ($cat->parent_slug ?? null);

                        if (!empty($parentSlug)) {
                            $parentDisplay = " [{$parentSlug}]";
                        }
                    }
                }
            } catch (\Throwable $e) {
                // En caso de error con CategoryMapper, seguimos sin parentDisplay
                $parentDisplay = '';
            }

            StdOut::print("[" . ($idx + 1) . "] {$raw}{$parentDisplay}\n");
        }

        DB::closeConnection();
    }

    /**
     * Router para comandos de categorías
     *
     * Uso: php com zippy category <subcomando> [options]
     */
    public function category($subcommand = null, ...$options)
    {
        if (empty($subcommand)) {
            StdOut::print("Error: Se requiere un subcomando.\n");
            StdOut::print("Uso: php com zippy category <subcomando> [options]\n");
            StdOut::print("Ejecuta 'php com zippy help' para ver todos los subcomandos disponibles.\n");
            return;
        }

        // Mapear subcomandos a métodos
        $method = 'category_' . $subcommand;

        if (!method_exists($this, $method)) {
            StdOut::print("Error: Subcomando '$subcommand' no existe.\n");
            StdOut::print("Ejecuta 'php com zippy help' para ver los subcomandos disponibles.\n");
            return;
        }

        // Llamar al método correspondiente
        $this->$method(...$options);
    }

    /**
     * Lista categorías existentes en la tabla categories
     *
     * Uso: php com zippy category list_all
     */
    protected function category_list_all()
    {
        DB::setConnection('zippy');

        $rows = DB::table('categories')->select('id','slug','name','parent_slug')->get();

        DB::closeConnection();

        dd($rows, 'Categories');
    }

    /**
     * Crea una categoría
     *
     * Uso: php com zippy category create --name="Leche y derivados" --slug=dairy.milk --parent=dairy
     */
    protected function category_create(...$options)
    {
        DB::setConnection('zippy');

        $opts = $this->parseOptions($options);

        $name = $opts['name'] ?? $opts['n'] ?? null;
        $slug = $opts['slug'] ?? $opts['s'] ?? null;
        $parent = $opts['parent'] ?? null;
        $image_url = $opts['image_url'] ?? null;
        $store_id = $opts['store_id'] ?? null;

        if (empty($name)) {
            dd(['error' => 'Missing --name'], 'Create category');
            return;
        }

        if (empty($slug)) {
            // normalizar nombre como slug si no se pasa slug
            $slug = Strings::normalize($name);
        }

        // check exists
        $exists = DB::selectOne("SELECT id FROM categories WHERE slug = ? AND deleted_at IS NULL LIMIT 1", [$slug]);
        if ($exists) {
            dd(['error' => 'Category slug already exists', 'slug' => $slug, 'id' => $exists['id']], 'Create category');
            DB::closeConnection();
            return;
        }

        $id = uniqid('cat_');

        DB::insert("INSERT INTO categories (id, name, slug, parent_slug, image_url, store_id, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, NOW(), NOW())", [
            $id, $name, $slug, $parent, $image_url, $store_id
        ]);

        DB::closeConnection();

        dd(['ok' => true, 'id' => $id, 'slug' => $slug, 'name' => $name], 'Created category');
    }

    /**
     * Crea un mapping (alias) de categoría
     *
     * Uso: php com zippy category create_mapping --slug=dairy.milk --raw="Leche entera 1L" --source=mercado
     */
    protected function category_create_mapping(...$options)
    {
        $opts = $this->parseOptions($options);

        $slug = $opts['slug'] ?? null;
        $raw = $opts['raw'] ?? null;
        $source = $opts['source'] ?? null;

        if (empty($slug) || empty($raw)) {
            dd(['error' => 'Missing --slug or --raw'], 'Create mapping');
            return;
        }

        CategoryMapper::saveCategoryAlias($slug, $raw, $source);

        dd([
            'ok' => true,
            'slug' => $slug,
            'raw' => $raw,
            'source' => $source
        ], 'Created mapping');
    }

    /**
     * Prueba resolver con texto suelto (invoca LLM)
     *
     * Uso: php com zippy category resolve --text="Leche entera 1L marca tradicional"
     */
    protected function category_resolve(...$options)
    {
        $opts = $this->parseOptions($options);

        $text = $opts['text'] ?? $opts['t'] ?? null;

        if (empty($text)) {
            dd(['error' => 'Missing --text'], 'Resolve test');
            return;
        }

        // Configure mapper (uses default LLM thresholds, override if needed)
        CategoryMapper::configure([
            'strategies_order' => ['llm'],
            'thresholds' => ['llm' => 0.70]
        ]);

        $res = CategoryMapper::resolve($text);

        dd($res, 'Resolve result (single)');
    }

    /**
     * Prueba resolver para un producto (slots + description)
     *
     * Uso: php com zippy category resolve_product --raw1="Leche entera" --raw2="" --description="Pack de 6 leches 1L"
     */
    protected function category_resolve_product(...$options)
    {
        $opts = $this->parseOptions($options);

        $product = [
            'catego_raw1' => $opts['raw1'] ?? $opts['r1'] ?? null,
            'catego_raw2' => $opts['raw2'] ?? $opts['r2'] ?? null,
            'catego_raw3' => $opts['raw3'] ?? $opts['r3'] ?? null,
            'description' => $opts['description'] ?? $opts['d'] ?? null,
            'ean' => $opts['ean'] ?? null,
        ];

        CategoryMapper::configure([
            'strategies_order' => ['llm'],
            'thresholds' => ['llm' => 0.70]
        ]);

        $res = CategoryMapper::resolveProduct($product, true);

        dd($res, 'Resolve product result');
    }

    /**
     * Encuentra categorías padre que se referencian pero no existen
     *
     * Uso: php com zippy category find_missing_parents
     */
    protected function category_find_missing_parents()
    {
        DB::setConnection('zippy');

        // Obtener todos los parent_slug únicos que se están usando
        $usedParents = DB::select("
            SELECT DISTINCT parent_slug
            FROM categories
            WHERE parent_slug IS NOT NULL
              AND parent_slug != ''
              AND deleted_at IS NULL
        ");

        $missingParents = [];

        foreach ($usedParents as $row) {
            $parentSlug = is_array($row) ? $row['parent_slug'] : $row->parent_slug;

            // Verificar si existe una categoría con ese slug
            $exists = DB::selectOne("
                SELECT id, slug, name
                FROM categories
                WHERE slug = ?
                  AND deleted_at IS NULL
                LIMIT 1
            ", [$parentSlug]);

            if (!$exists) {
                // Contar cuántas categorías tienen este padre inexistente
                $count = DB::selectOne("
                    SELECT COUNT(*) as total
                    FROM categories
                    WHERE parent_slug = ?
                      AND deleted_at IS NULL
                ", [$parentSlug]);

                $total = is_array($count) ? $count['total'] : $count->total;

                $missingParents[] = [
                    'parent_slug' => $parentSlug,
                    'children_count' => $total,
                    'status' => 'MISSING - Should be created',
                ];
            }
        }

        DB::closeConnection();

        if (empty($missingParents)) {
            dd(['message' => 'No missing parent categories found. All parent_slug values exist!'], 'Missing Parents');
        } else {
            dd($missingParents, 'Missing Parent Categories (Should be created)');
        }
    }

    /**
     * Encuentra categorías huérfanas (cuyo padre no existe)
     *
     * Uso: php com zippy category find_orphans
     */
    protected function category_find_orphans()
    {
        DB::setConnection('zippy');

        // Obtener todas las categorías que tienen parent_slug
        $categoriesWithParent = DB::select("
            SELECT id, slug, name, parent_slug
            FROM categories
            WHERE parent_slug IS NOT NULL
              AND parent_slug != ''
              AND deleted_at IS NULL
            ORDER BY parent_slug, slug
        ");

        $orphans = [];

        foreach ($categoriesWithParent as $row) {
            $id = is_array($row) ? $row['id'] : $row->id;
            $slug = is_array($row) ? $row['slug'] : $row->slug;
            $name = is_array($row) ? $row['name'] : $row->name;
            $parentSlug = is_array($row) ? $row['parent_slug'] : $row->parent_slug;

            // Verificar si el padre existe
            $parentExists = DB::selectOne("
                SELECT id, name
                FROM categories
                WHERE slug = ?
                  AND deleted_at IS NULL
                LIMIT 1
            ", [$parentSlug]);

            if (!$parentExists) {
                $orphans[] = [
                    'id' => $id,
                    'slug' => $slug,
                    'name' => $name,
                    'parent_slug' => $parentSlug,
                    'status' => 'ORPHAN - Parent does not exist',
                ];
            }
        }

        DB::closeConnection();

        if (empty($orphans)) {
            dd(['message' => 'No orphan categories found. All categories have valid parents!'], 'Orphan Categories');
        } else {
            dd($orphans, 'Orphan Categories (Invalid parent_slug)');
        }
    }

    /**
     * Genera un reporte combinado de problemas de categorías
     *
     * Uso: php com zippy category report_issues
     */
    protected function category_report_issues()
    {
        DB::setConnection('zippy');

        $report = [
            'missing_parents' => [],
            'orphan_categories' => [],
            'summary' => []
        ];

        // 1. Encontrar padres faltantes
        $usedParents = DB::select("
            SELECT DISTINCT parent_slug
            FROM categories
            WHERE parent_slug IS NOT NULL
              AND parent_slug != ''
              AND deleted_at IS NULL
        ");

        foreach ($usedParents as $row) {
            $parentSlug = is_array($row) ? $row['parent_slug'] : $row->parent_slug;

            $exists = DB::selectOne("
                SELECT id, slug, name
                FROM categories
                WHERE slug = ?
                  AND deleted_at IS NULL
                LIMIT 1
            ", [$parentSlug]);

            if (!$exists) {
                $count = DB::selectOne("
                    SELECT COUNT(*) as total
                    FROM categories
                    WHERE parent_slug = ?
                      AND deleted_at IS NULL
                ", [$parentSlug]);

                $total = is_array($count) ? $count['total'] : $count->total;

                $report['missing_parents'][] = [
                    'parent_slug' => $parentSlug,
                    'children_count' => $total,
                ];
            }
        }

        // 2. Encontrar categorías huérfanas
        $categoriesWithParent = DB::select("
            SELECT id, slug, name, parent_slug
            FROM categories
            WHERE parent_slug IS NOT NULL
              AND parent_slug != ''
              AND deleted_at IS NULL
            ORDER BY parent_slug, slug
        ");

        foreach ($categoriesWithParent as $row) {
            $id = is_array($row) ? $row['id'] : $row->id;
            $slug = is_array($row) ? $row['slug'] : $row->slug;
            $name = is_array($row) ? $row['name'] : $row->name;
            $parentSlug = is_array($row) ? $row['parent_slug'] : $row->parent_slug;

            $parentExists = DB::selectOne("
                SELECT id, name
                FROM categories
                WHERE slug = ?
                  AND deleted_at IS NULL
                LIMIT 1
            ", [$parentSlug]);

            if (!$parentExists) {
                $report['orphan_categories'][] = [
                    'id' => $id,
                    'slug' => $slug,
                    'name' => $name,
                    'parent_slug' => $parentSlug,
                ];
            }
        }

        // 3. Resumen
        $report['summary'] = [
            'total_missing_parents' => count($report['missing_parents']),
            'total_orphan_categories' => count($report['orphan_categories']),
            'status' => (count($report['missing_parents']) > 0 || count($report['orphan_categories']) > 0)
                ? 'ISSUES FOUND'
                : 'ALL OK',
        ];

        DB::closeConnection();

        dd($report, 'Category Integrity Report');
    }

    /**
     * Genera comandos para crear las categorías padre faltantes
     *
     * Uso: php com zippy category generate_create_commands
     */
    protected function category_generate_create_commands()
    {
        DB::setConnection('zippy');

        // Obtener todos los parent_slug únicos que se están usando
        $usedParents = DB::select("
            SELECT DISTINCT parent_slug
            FROM categories
            WHERE parent_slug IS NOT NULL
              AND parent_slug != ''
              AND deleted_at IS NULL
        ");

        $commands = [];

        foreach ($usedParents as $row) {
            $parentSlug = is_array($row) ? $row['parent_slug'] : $row->parent_slug;

            // Verificar si existe una categoría con ese slug
            $exists = DB::selectOne("
                SELECT id
                FROM categories
                WHERE slug = ?
                  AND deleted_at IS NULL
                LIMIT 1
            ", [$parentSlug]);

            if (!$exists) {
                // Generar un nombre capitalizado basado en el slug
                $suggestedName = ucwords(str_replace(['-', '_', '.'], ' ', $parentSlug));

                $commands[] = sprintf(
                    'php com zippy category_create --name="%s" --slug=%s',
                    $suggestedName,
                    $parentSlug
                );
            }
        }

        DB::closeConnection();

        if (empty($commands)) {
            dd(['message' => 'No commands needed. All parent categories exist!'], 'Create Commands');
        } else {
            echo "# Commands to create missing parent categories:\n\n";
            foreach ($commands as $cmd) {
                echo $cmd . "\n";
            }
            echo "\n# Total commands: " . count($commands) . "\n";
        }
    }

    /**
     * Router para comandos de Ollama
     *
     * Uso: php com zippy ollama <subcomando> [options]
     */
    public function ollama($subcommand = null, ...$options)
    {
        if (empty($subcommand)) {
            StdOut::print("Error: Se requiere un subcomando.\n");
            StdOut::print("Uso: php com zippy ollama <subcomando> [options]\n");
            StdOut::print("Ejecuta 'php com zippy help' para ver todos los subcomandos disponibles.\n");
            return;
        }

        // Mapear subcomandos a métodos
        $method = 'ollama_' . $subcommand;

        if (!method_exists($this, $method)) {
            StdOut::print("Error: Subcomando '$subcommand' no existe.\n");
            StdOut::print("Ejecuta 'php com zippy help' para ver los subcomandos disponibles.\n");
            return;
        }

        // Llamar al método correspondiente
        $this->$method(...$options);
    }

    /**
     * Prueba modelos disponibles en Ollama
     *
     * Uso: php com zippy ollama test_strategy
     */
    protected function ollama_test_strategy()
    {
        $models = \Boctulus\Zippy\Strategies\LLMMatchingStrategy::getAvailableModels();
        dd($models, 'OLLAMA models');
    }

    /**
     * Ejecuta pruebas hardcodeadas del LLM
     *
     * Uso: php com zippy ollama hard_tests
     */
    protected function ollama_hard_tests()
    {
        // Textos hardcodeados para prueba
        $tests = [
            'Leche entera 1L marca tradicional',
            'Pan de molde integral 500g',
            'Cereal de maíz con chocolate 250g',
            'Pasta dental blanqueadora 75ml',
            'Jugo de naranja 1L sin azúcar',
            'Detergente líquido para ropa 3L',
        ];

        // Categorías hardcodeadas (formato que LLMMatchingStrategy espera: slug => [name, parent_slug?])
        $availableCategories = [
            'dairy.milk' => ['name' => 'Leche y derivados', 'parent_slug' => 'dairy'],
            'bakery.bread' => ['name' => 'Panadería', 'parent_slug' => 'bakery'],
            'breakfast.cereal' => ['name' => 'Cereales y desayuno', 'parent_slug' => 'breakfast'],
            'personalcare.toothpaste' => ['name' => 'Cuidado personal / Pasta dental', 'parent_slug' => 'personalcare'],
            'beverages.juice' => ['name' => 'Bebidas / Jugos', 'parent_slug' => 'beverages'],
            'home.detergent' => ['name' => 'Limpieza del hogar / Detergentes', 'parent_slug' => 'home'],
            // puedes añadir más categorías aquí si lo deseas
        ];

        // Verificar disponibilidad Ollama
        if (!\Boctulus\Zippy\Strategies\LLMMatchingStrategy::isAvailable()) {
            dd([
                'error' => 'Ollama no disponible',
                'hint' => 'Asegúrate de que Ollama esté corriendo en localhost:' . \Boctulus\LLMProviders\Providers\OllamaProvider::DEFAULT_PORT
            ], 'LLM availability');
        }

        // Instanciar estrategia (ajusta modelo/temperature/maxTokens/verbose aquí si quieres)
        $strategy = new \Boctulus\Zippy\Strategies\LLMMatchingStrategy(
            'qwen2.5:1.5b', // modelo
            0.2,           // temperatura
            500,           // max tokens
            true           // verbose: útil en debugging
        );

        $threshold = 0.70; // 70% threshold

        $results = [];

        foreach ($tests as $text) {
            $res = null;
            try {
                $res = $strategy->match($text, $availableCategories, $threshold);
            } catch (\Throwable $e) {
                $res = ['error' => 'exception', 'message' => $e->getMessage()];
            }

            // dd($res, 'LLM Response');

            // Normalizar salida para inspección: si hay match, extraer slug posible
            $matched_slug = null;
            $matched_name = null;
            $confidence = null;
            $reasoning = null;

            if (is_array($res) && isset($res['category'])) {
                // recordar: parseResponse devuelve la data de category tal como en $availableCategories[$slug]
                // pero no devuelve el slug directamente; intentamos inferirlo buscando la referencia en availableCategories
                foreach ($availableCategories as $slug => $catData) {
                    // comparar por referencia de nombre (funciona con este ejemplo sencillo)
                    if (
                        (is_array($res['category']) && isset($res['category']['name']) && $res['category']['name'] === $catData['name'])
                        || (is_object($res['category']) && (($res['category']->name ?? null) === $catData['name']))
                    ) {
                        $matched_slug = $slug;
                        $matched_name = $catData['name'];
                        break;
                    }
                }

                $confidence = $res['score'] ?? null;
                $reasoning = $res['reasoning'] ?? null;
            } else {
                // en caso de null o error dejamos los campos como null o mensaje de error
                if (is_array($res) && isset($res['error'])) {
                    $reasoning = $res['message'] ?? ($res['error'] ?? 'unknown error');
                } else {
                    $reasoning = 'No match (confidence < threshold o parse error)';
                }
            }

            $results[] = [
                'text' => $text,
                'matched_slug' => $matched_slug,
                'matched_name' => $matched_name,
                'confidence' => $confidence,
                'reasoning' => $reasoning,
                'raw' => $res
            ];
        }

        // Mostrar todo en una sola salida para inspección
        dd($results, 'Hardcoded classification tests (OLLAMA LLMMatchingStrategy)');
    }

    // parseOptions() ahora está disponible en CommandTrait

    /**
     * Muestra estadísticas de mappings de categorías (cant de categorias mapeadas, sin revisar, etc)
     */
    public function map_stats()
    {
        // DE MOMENTO --ANULADO--
    }

    /**
     * Muestra mappings que necesitan revisión
     */
    public function show_unmapped(...$options)
    {
        // DE MOMENTO --ANULADO--
    }

    /**
     * Marca un mapping como revisado y opcionalmente cambia la categoría
     */
    public function review_mapping(...$options)
    {
        // DE MOMENTO --ANULADO--
    }

    /**
     * Ayuda del comando
     */
    public function help($name = null, ...$args)
    {
        $str = <<<STR

ZIPPY COMMAND - Gestión de categorías y productos

═══════════════════════════════════════════════════════════════
COMANDOS DE PROCESAMIENTO DE PRODUCTOS
═══════════════════════════════════════════════════════════════

  process_categories [options]
    Procesa categorías de productos en batch
    Opciones:
      --limit=N           Limitar cantidad
      --offset=N          Offset para paginación
      --only-unmapped     Solo productos sin categories
      --dry-run           No guardar cambios

  products_process_categories [options]
    Procesa productos y actualiza sus categorías
    Opciones:
      --limit=N           Limitar cantidad (default: 100)
      --dry-run           No guardar cambios
      --strategy=X        Estrategia a usar

  test_mapping --raw="<value>" [options]
    Prueba el mapeo de una categoría sin guardar
    Opciones:
      --raw="valor"       Texto a probar (requerido)
      --strategy=X        llm|fuzzy (default: llm)

  category_list [--limit=100]
    Lista categorías raw encontradas en productos

  clear_cache
    Limpia el caché de CategoryMapper

═══════════════════════════════════════════════════════════════
COMANDOS DE GESTIÓN DE CATEGORÍAS
═══════════════════════════════════════════════════════════════

  category list_all
    Lista todas las categorías existentes en la tabla categories

  category create --name="<nombre>" [options]
    Crea una nueva categoría
    Opciones:
      --name="X"          Nombre de la categoría (requerido)
      --slug=X            Slug (opcional, se genera del nombre)
      --parent=X          Slug del padre (opcional)
      --image_url=X       URL de imagen (opcional)
      --store_id=X        ID de tienda (opcional)

  category create_mapping --slug=<slug> --raw="<texto>" [options]
    Crea un mapping (alias) de categoría
    Opciones:
      --slug=X            Slug de categoría existente (requerido)
      --raw="texto"       Texto raw a mapear (requerido)
      --source=X          Fuente del mapping (opcional)

  category resolve --text="<texto>"
    Prueba resolver con texto suelto (invoca LLM)
    Opciones:
      --text="X"          Texto a resolver (requerido)

  category resolve_product [options]
    Prueba resolver para un producto (slots + description)
    Opciones:
      --raw1="X"          Categoría raw 1
      --raw2="X"          Categoría raw 2
      --raw3="X"          Categoría raw 3
      --description="X"   Descripción del producto
      --ean=X             EAN del producto

═══════════════════════════════════════════════════════════════
COMANDOS DE DIAGNÓSTICO DE CATEGORÍAS
═══════════════════════════════════════════════════════════════

  category find_missing_parents
    Encuentra categorías padre que se referencian pero no existen

  category find_orphans
    Encuentra categorías huérfanas (cuyo padre no existe)

  category report_issues
    Genera un reporte combinado de problemas de categorías

  category generate_create_commands
    Genera comandos para crear las categorías padre faltantes

═══════════════════════════════════════════════════════════════
COMANDOS DE PRUEBA OLLAMA/LLM
═══════════════════════════════════════════════════════════════

  ollama test_strategy
    Prueba modelos disponibles en Ollama

  ollama hard_tests
    Ejecuta pruebas hardcodeadas del LLM

═══════════════════════════════════════════════════════════════
EJEMPLOS DE USO
═══════════════════════════════════════════════════════════════

  # Procesamiento de productos
  php com zippy process_categories --limit=100 --dry-run
  php com zippy products_process_categories --limit=50
  php com zippy test_mapping --raw="Aceites Y Condimentos"

  # Gestión de categorías
  php com zippy category list_all
  php com zippy category create --name="Leche y derivados" --slug=dairy.milk --parent=dairy
  php com zippy category create_mapping --slug=dairy.milk --raw="Leche entera 1L" --source=mercado
  php com zippy category resolve --text="Leche entera 1L marca tradicional"

  # Diagnóstico
  php com zippy category find_missing_parents
  php com zippy category report_issues
  php com zippy category generate_create_commands

  # Pruebas LLM
  php com zippy ollama test_strategy
  php com zippy ollama hard_tests

STR;

        dd($str);
    }
}