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
 * 
 * Ver 2.0 
 * 
 */
class ZippyCommand implements ICommand
{
    use CommandTrait;

    public function __construct()
    {
        DB::setConnection('zippy');
    }

    // ================================================================
    // ROUTER: PRODUCT
    // ================================================================

    /**
     * Router para comandos de productos
     *
     * Uso: php com zippy product <subcomando> [options]
     */
    public function product($subcommand = null, ...$options)
    {
        if (empty($subcommand)) {
            StdOut::print("Error: Se requiere un subcomando.\n");
            StdOut::print("Uso: php com zippy product <subcomando> [options]\n");
            StdOut::print("Ejecuta 'php com zippy help' para ver todos los subcomandos disponibles.\n");
            return;
        }

        $method = 'product_' . $subcommand;

        if (!method_exists($this, $method)) {
            StdOut::print("Error: Subcomando '$subcommand' no existe.\n");
            StdOut::print("Ejecuta 'php com zippy help' para ver los subcomandos disponibles.\n");
            return;
        }

        $this->$method(...$options);
    }

    // Test product processing
    function product_test(...$options)
    {
        if (is_array($options) && (count($options) == 1) && is_numeric($options[0])) {
            $productId = $options[0];
        } else {
            $opts      = $this->parseOptions($options);
            $productId = $opts['id'] ?? null;
        }

        CategoryMapper::configure([
            'default_strategy' => 'llm',
            'strategies_order' => ['llm', 'fuzzy'],
            'thresholds' => [
                'fuzzy' => 0.40,
                'llm' => 0.70,
            ]
        ]);

        $product = DB::table('products')
        ->find($productId);

        dd("Procesando producto ID/EAN: $productId");

        $categories = CategoryMapper::resolveProduct($product, true);

        dd($categories, 'Categorias resueltas');

        if (!empty($categories)) {
            dd("  → No se encontraron categorías\n\n---------------------------------------\n");
        }
    }

    /**
     * Procesa productos y actualiza sus categorías (proceso individual)
     * 
     * Uso: php com zippy product process --limit=100 --dry-run
     */
    protected function product_process(...$options)
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
            dd($product, 'P');

            try {
                $productId = is_array($product) ? ($product['ean'] ?? $product['id']) : ($product['ean'] ?? $product['id']);
                echo "[$processed/$total] Procesando producto ID/EAN: $productId\n";

                $categories = CategoryMapper::resolveProduct($product, true);

                dd($categories, 'Categorias resueltas');

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
                    echo "\n  → No se encontraron categorías\n\n---------------------------------------\n\n";
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
     * Procesa categorías de productos en batch (optimizado para grandes volúmenes)
     * 
     * Uso: php com zippy product batch --limit=1000 --dry-run
     */
    protected function product_batch(...$options)
    {
        $opts = $this->parseOptions($options);
        $limit = $opts['limit'] ?? null;
        $offset = $opts['offset'] ?? 0;
        $onlyUnmapped = $opts['only_unmapped'] ?? false;
        $dryRun = $opts['dry_run'] ?? false;

        StdOut::print("=== Procesamiento batch de productos ===\n");

        if ($dryRun) {
            StdOut::print("⚠ Modo DRY-RUN activado: no se guardarán cambios\n");
        }

        $query = DB::table('products');

        if ($onlyUnmapped) {
            $query->where(function ($q) {
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

    // ================================================================
    // ROUTER: CATEGORY
    // ================================================================

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

        $method = 'category_' . $subcommand;

        if (!method_exists($this, $method)) {
            StdOut::print("Error: Subcomando '$subcommand' no existe.\n");
            StdOut::print("Ejecuta 'php com zippy help' para ver los subcomandos disponibles.\n");
            return;
        }

        $this->$method(...$options);
    }

    /**
     * Prueba el mapeo de una categoría raw sin guardar
     * 
     * Uso: php com zippy category test --raw="Aceites Y Condimentos" --strategy=llm
     */
    protected function category_test(...$options)
    {
        $opts = $this->parseOptions($options);
        $raw = $opts['raw'] ?? null;
        $strategy = $opts['strategy'] ?? 'llm';

        if (empty($raw)) {
            StdOut::print("Error: Debes proporcionar un valor raw con --raw=\"valor\"\n");
            StdOut::print("Ejemplo: php com zippy category test --raw=\"Aceites Y Condimentos\"\n");
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
    }

    /**
     * Limpia el caché de CategoryMapper
     * 
     * Uso: php com zippy category clear_cache
     */
    protected function category_clear_cache()
    {
        // TODO: Implementar clearCache() en CategoryMapper
        StdOut::print("⚠ Función clearCache() aún no implementada en CategoryMapper.\n");
    }

    /**
     * Lista categorías raw detectadas en products
     * Muestra entre corchetes la categoria padre si existe
     *
     * Uso: php com zippy category list_raw --limit=100
     */
    protected function category_list_raw(...$options)
    {
        $opts = $this->parseOptions($options);
        $limit = $opts['limit'] ?? 100;

        dd("\r\nLimit: $limit");

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
            $displayLine = "[" . ($idx + 1) . "] {$raw}";

            try {
                $resolved = \Boctulus\Zippy\Libs\CategoryMapper::resolve($raw, false);

                if (!empty($resolved) && is_array($resolved) && $resolved['score'] >= 90) {
                    $mappedSlug = $resolved['category_slug'] ?? null;

                    if ($mappedSlug) {
                        $displayLine .= " → {$mappedSlug}";

                        $cat = DB::selectOne("SELECT parent_slug, parent_id FROM categories WHERE slug = ? AND deleted_at IS NULL LIMIT 1", [$mappedSlug]);

                        $parentSlug = is_array($cat) ? ($cat['parent_slug'] ?? null) : ($cat->parent_slug ?? null);

                        if (!empty($parentSlug)) {
                            $parentCat = DB::selectOne("SELECT name FROM categories WHERE slug = ? AND deleted_at IS NULL LIMIT 1", [$parentSlug]);

                            if ($parentCat) {
                                $parentName = is_array($parentCat) ? ($parentCat['name'] ?? $parentSlug) : ($parentCat->name ?? $parentSlug);
                                $displayLine .= " [{$parentName}]";
                            } else {
                                $displayLine .= " [{$parentSlug}]";
                            }
                        }
                    }
                }
            } catch (\Throwable $e) {
                // Continuar sin información adicional
            }

            StdOut::print($displayLine . "\n");
        }

        DB::closeConnection();
    }

    /**
     * Lista todas las categorías existentes en la tabla categories
     *
     * Uso: php com zippy category all
     */
    protected function category_all()
    {
        DB::setConnection('zippy');

        $rows = DB::table('categories')->select('id', 'slug', 'name', 'parent_slug')->get();

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
            $slug = Strings::normalize($name);
        }

        $exists = DB::selectOne("SELECT id FROM categories WHERE slug = ? AND deleted_at IS NULL LIMIT 1", [$slug]);
        if ($exists) {
            dd(['error' => 'Category slug already exists', 'slug' => $slug, 'id' => $exists['id']], 'Create category');
            DB::closeConnection();
            return;
        }

        $id = uniqid('cat_');

        DB::insert("INSERT INTO categories (id, name, slug, parent_slug, image_url, store_id, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, NOW(), NOW())", [
            $id,
            $name,
            $slug,
            $parent,
            $image_url,
            $store_id
        ]);

        DB::closeConnection();

        dd(['ok' => true, 'id' => $id, 'slug' => $slug, 'name' => $name], 'Created category');
    }

    /**
     * Setea el parent_slug de una categoría existente
     *
     * Uso: php com zippy category set --slug=dairy.milk --parent=dairy
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

        if (is_string($parent) && (strtoupper($parent) === 'NULL' || $parent === 'null')) {
            $parent = null;
        }

        $cat = DB::selectOne("SELECT id, slug, name, parent_slug FROM categories WHERE slug = ? AND deleted_at IS NULL LIMIT 1", [$slug]);
        if (!$cat) {
            dd(['error' => 'Category not found', 'slug' => $slug], 'Set category parent');
            DB::closeConnection();
            return;
        }

        $parentId = null;

        if ($parent !== null && $parent !== '') {
            $parentData = DB::selectOne("SELECT id, slug FROM categories WHERE slug = ? AND deleted_at IS NULL LIMIT 1", [$parent]);
            if (!$parentData) {
                dd(['error' => 'Parent category not found', 'parent' => $parent], 'Set category parent');
                DB::closeConnection();
                return;
            }
            $parentId = is_array($parentData) ? $parentData['id'] : $parentData->id;
        }

        if ($parent === null) {
            DB::update("UPDATE categories SET parent_slug = NULL, parent_id = NULL, updated_at = NOW() WHERE slug = ? AND deleted_at IS NULL", [$slug]);
        } else {
            DB::update("UPDATE categories SET parent_slug = ?, parent_id = ?, updated_at = NOW() WHERE slug = ? AND deleted_at IS NULL", [$parent, $parentId, $slug]);
        }

        DB::closeConnection();

        dd([
            'ok' => true,
            'slug' => $slug,
            'parent' => $parent,
            'parent_id' => $parentId
        ], 'Category parent updated');
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
     * Uso: php com zippy category resolve_product --raw1="Leche entera" --description="Pack de 6 leches 1L"
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

            $exists = DB::selectOne("
                SELECT id
                FROM categories
                WHERE slug = ?
                  AND deleted_at IS NULL
                LIMIT 1
            ", [$parentSlug]);

            if (!$exists) {
                $suggestedName = ucwords(str_replace(['-', '_', '.'], ' ', $parentSlug));

                $commands[] = sprintf(
                    'php com zippy category create --name="%s" --slug=%s',
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

    // ================================================================
    // ROUTER: OLLAMA
    // ================================================================

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

        $method = 'ollama_' . $subcommand;

        if (!method_exists($this, $method)) {
            StdOut::print("Error: Subcomando '$subcommand' no existe.\n");
            StdOut::print("Ejecuta 'php com zippy help' para ver los subcomandos disponibles.\n");
            return;
        }

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
        $tests = [
            'Leche entera 1L marca tradicional',
            'Pan de molde integral 500g',
            'Cereal de maíz con chocolate 250g',
            'Pasta dental blanqueadora 75ml',
            'Jugo de naranja 1L sin azúcar',
            'Detergente líquido para ropa 3L',
        ];

        $availableCategories = [
            'dairy.milk' => ['name' => 'Leche y derivados', 'parent_slug' => 'dairy'],
            'bakery.bread' => ['name' => 'Panadería', 'parent_slug' => 'bakery'],
            'breakfast.cereal' => ['name' => 'Cereales y desayuno', 'parent_slug' => 'breakfast'],
            'personalcare.toothpaste' => ['name' => 'Cuidado personal / Pasta dental', 'parent_slug' => 'personalcare'],
            'beverages.juice' => ['name' => 'Bebidas / Jugos', 'parent_slug' => 'beverages'],
            'home.detergent' => ['name' => 'Limpieza del hogar / Detergentes', 'parent_slug' => 'home'],
        ];

        if (!\Boctulus\Zippy\Strategies\LLMMatchingStrategy::isAvailable()) {
            dd([
                'error' => 'Ollama no disponible',
                'hint' => 'Asegúrate de que Ollama esté corriendo en localhost:' . \Boctulus\LLMProviders\Providers\OllamaProvider::DEFAULT_PORT
            ], 'LLM availability');
        }

        $strategy = new \Boctulus\Zippy\Strategies\LLMMatchingStrategy(
            'qwen2.5:1.5b',
            0.2,
            500,
            true
        );

        $threshold = 0.70;

        $results = [];

        foreach ($tests as $text) {
            $res = null;
            try {
                $res = $strategy->match($text, $availableCategories, $threshold);
            } catch (\Throwable $e) {
                $res = ['error' => 'exception', 'message' => $e->getMessage()];
            }

            $matched_slug = null;
            $matched_name = null;
            $confidence = null;
            $reasoning = null;

            if (is_array($res) && isset($res['category'])) {
                foreach ($availableCategories as $slug => $catData) {
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

        dd($results, 'Hardcoded classification tests (OLLAMA LLMMatchingStrategy)');
    }

    // ================================================================
    // UTILIDADES Y ESTADÍSTICAS
    // ================================================================

    /**
     * Muestra estadísticas de mappings de categorías
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

    // ================================================================
    // AYUDA
    // ================================================================

    /**
     * Ayuda del comando
     */
    public function help($name = null, ...$args)
    {
        $str = <<<STR

╔═══════════════════════════════════════════════════════════════════════════╗
║                         ZIPPY COMMAND HELP                                ║
║                  Gestión de categorías y productos                        ║
╚═══════════════════════════════════════════════════════════════════════════╝

═══════════════════════════════════════════════════════════════════════════
📦 COMANDOS DE PRODUCTOS
═══════════════════════════════════════════════════════════════════════════

  product process [options]
    Procesa productos individualmente y actualiza sus categorías
    
    Opciones:
      --limit=N           Cantidad de productos a procesar (default: 100)
      --dry-run           Modo simulación, no guarda cambios
      --strategy=X        Estrategia a usar (llm|fuzzy)
    
    Ejemplo:
      php com zippy product process --limit=50 --dry-run

  product batch [options]
    Procesamiento batch optimizado para grandes volúmenes
    
    Opciones:
      --limit=N           Cantidad de productos a procesar
      --offset=N          Offset para paginación
      --only-unmapped     Solo productos sin categorías asignadas
      --dry-run           Modo simulación, no guarda cambios
    
    Ejemplo:
      php com zippy product batch --limit=1000 --only-unmapped

═══════════════════════════════════════════════════════════════════════════
🏷️  COMANDOS DE CATEGORÍAS - GESTIÓN BÁSICA
═══════════════════════════════════════════════════════════════════════════

  category all
    Lista todas las categorías existentes en la tabla categories
    
    Ejemplo:
      php com zippy category all

  category list_raw [--limit=N]
    Lista categorías raw detectadas en productos (catego_raw1/2/3)
    Muestra la categoría mapeada y su padre entre corchetes
    
    Opciones:
      --limit=N           Cantidad máxima a mostrar (default: 100)
    
    Ejemplo:
      php com zippy category list_raw --limit=50

  category create --name="<nombre>" [options]
    Crea una nueva categoría
    
    Opciones:
      --name="X"          Nombre de la categoría (REQUERIDO)
      --slug=X            Slug (opcional, se genera automáticamente)
      --parent=X          Slug del padre (opcional)
      --image_url=X       URL de imagen (opcional)
      --store_id=X        ID de tienda (opcional)
    
    Ejemplo:
      php com zippy category create --name="Leche y derivados" --slug=dairy.milk --parent=dairy

  category set --slug=<slug> --parent=<parent>
    Establece o cambia el padre de una categoría existente
    
    Opciones:
      --slug=X            Slug de la categoría a modificar (REQUERIDO)
      --parent=X          Slug del nuevo padre (usar NULL para desemparentar)
    
    Ejemplo:
      php com zippy category set --slug=dairy.milk --parent=dairy
      php com zippy category set --slug=dairy.milk --parent=NULL

═══════════════════════════════════════════════════════════════════════════
🧪 COMANDOS DE CATEGORÍAS - PRUEBAS Y RESOLUCIÓN
═══════════════════════════════════════════════════════════════════════════

  category test --raw="<texto>" [--strategy=X]
    Prueba el mapeo de una categoría raw sin guardar
    
    Opciones:
      --raw="X"           Texto a probar (REQUERIDO)
      --strategy=X        Estrategia: llm|fuzzy (default: llm)
    
    Ejemplo:
      php com zippy category test --raw="Aceites Y Condimentos"

  category resolve --text="<texto>"
    Resuelve categoría usando LLM (texto suelto)
    
    Opciones:
      --text="X"          Texto a resolver (REQUERIDO)
    
    Ejemplo:
      php com zippy category resolve --text="Leche entera 1L marca tradicional"

  category resolve_product [options]
    Resuelve categorías para un producto completo (múltiples campos)
    
    Opciones:
      --raw1="X"          Categoría raw 1
      --raw2="X"          Categoría raw 2
      --raw3="X"          Categoría raw 3
      --description="X"   Descripción del producto
      --ean=X             EAN del producto
    
    Ejemplo:
      php com zippy category resolve_product --raw1="Leche" --description="Pack 6x1L"

  category create_mapping --slug=<slug> --raw="<texto>" [--source=X]
    Crea un mapping (alias) manual de categoría
    
    Opciones:
      --slug=X            Slug de categoría existente (REQUERIDO)
      --raw="X"           Texto raw a mapear (REQUERIDO)
      --source=X          Fuente del mapping (opcional)
    
    Ejemplo:
      php com zippy category create_mapping --slug=dairy.milk --raw="Leche entera 1L"

═══════════════════════════════════════════════════════════════════════════
🔍 COMANDOS DE DIAGNÓSTICO
═══════════════════════════════════════════════════════════════════════════

  category find_missing_parents
    Encuentra categorías padre referenciadas que no existen
    
    Ejemplo:
      php com zippy category find_missing_parents

  category find_orphans
    Encuentra categorías huérfanas (padre no existe)
    
    Ejemplo:
      php com zippy category find_orphans

  category report_issues
    Reporte completo: padres faltantes + categorías huérfanas
    
    Ejemplo:
      php com zippy category report_issues

  category generate_create_commands
    Genera comandos listos para crear categorías padre faltantes
    
    Ejemplo:
      php com zippy category generate_create_commands

═══════════════════════════════════════════════════════════════════════════
🤖 COMANDOS OLLAMA/LLM
═══════════════════════════════════════════════════════════════════════════

  ollama test_strategy
    Lista modelos Ollama disponibles
    
    Ejemplo:
      php com zippy ollama test_strategy

  ollama hard_tests
    Ejecuta pruebas hardcodeadas del LLM con categorías de ejemplo
    
    Ejemplo:
      php com zippy ollama hard_tests

═══════════════════════════════════════════════════════════════════════════
🛠️  UTILIDADES
═══════════════════════════════════════════════════════════════════════════

  category clear_cache
    Limpia el caché de CategoryMapper (pendiente implementar)
    
    Ejemplo:
      php com zippy category clear_cache

═══════════════════════════════════════════════════════════════════════════
📋 FLUJOS DE TRABAJO RECOMENDADOS
═══════════════════════════════════════════════════════════════════════════

🔹 FLUJO 1: Setup inicial y diagnóstico
   1. php com zippy category all
   2. php com zippy category find_missing_parents
   3. php com zippy category generate_create_commands
   4. php com zippy category create --name="..." --slug=...
   5. php com zippy category report_issues

🔹 FLUJO 2: Exploración y testing
   1. php com zippy category list_raw --limit=100
   2. php com zippy category test --raw="Aceites Y Condimentos"
   3. php com zippy category resolve --text="Leche entera 1L"
   4. php com zippy ollama hard_tests

🔹 FLUJO 3: Procesamiento en producción
   1. php com zippy category report_issues
   2. php com zippy product test --id={id}
   3. php com zippy product process --limit=10 --dry-run
   4. php com zippy product process --limit=100
   5. php com zippy product batch --limit=1000 --only-unmapped

🔹 FLUJO 4: Validación de LLM
   1. php com zippy ollama test_strategy
   2. php com zippy ollama hard_tests
   3. php com zippy category test --raw="..." --strategy=llm
   4. php com zippy category resolve_product --raw1="..."

═══════════════════════════════════════════════════════════════════════════

STR;

        dd($str);
    }
}