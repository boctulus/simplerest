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
    function product_process_one(...$options)
    {
        // Buscar el EAN como primer argumento posicional (numérico)
        $ean = null;
        $remainingOpts = [];

        foreach ($options as $opt) {
            if (is_numeric($opt) && $ean === null) {
                $ean = $opt;
            } else {
                $remainingOpts[] = $opt;
            }
        }

        // Parsear opciones restantes
        $opts = $this->parseOptions($remainingOpts);
        $dryRun = $opts['dry_run'] ?? false;

        // Si no encontramos EAN posicional, buscar en opciones
        if ($ean === null) {
            $ean = $opts['ean'] ?? null;
        }

        CategoryMapper::configure([
            'default_strategy' => 'llm',
            'strategies_order' => ['llm', 'fuzzy'],
            'llm_model' => 'qwen2.5:1.5b',
            'thresholds' => [
                'fuzzy' => 0.40,
                'llm' => 0.90,
            ]
        ]);

        $product = DB::table('products')
        ->where('ean', $ean)->getOne();

        dd($product, "Procesando producto con EAN: $ean" . ($dryRun ? " (DRY-RUN)" : ""));

        $categories = CategoryMapper::resolveProduct($product, true);

        dd($categories, 'Categorias resueltas');

        if ($categories) {
            if (!$dryRun) {
                DB::table('products')
                ->where('ean', $ean)
                ->update([
                    'categories' => json_encode($categories)
                ]);
                echo "  ✅ Categorías guardadas: " . implode(', ', $categories) . "\n";
            } else {
                echo "  ℹ️  DRY-RUN: Categorías que se asignarían: " . implode(', ', $categories) . "\n";
            }
        } else {
            dd("  → No se encontraron categorías\n\n---------------------------------------\n");
        }
    }

    /**
     * Procesa productos y actualiza sus categorías
     * 
     * Uso: 
     *   php com zippy product process --limit=100 --dry-run
     *   php com zippy product process --limit=50 --batch-size=10  (modo batch)
     */
    protected function product_process(...$options)
    {
        $opts     = $this->parseOptions($options);
        $limit    = $opts['limit'] ?? 100;
        $dryRun   = $opts['dry_run'] ?? false;
        $batchSize = $opts['batch_size'] ?? 1; // Default: 1 (secuencial)
        $strategy = $opts['strategy'] ?? null;

        DB::setConnection('zippy');

        CategoryMapper::configure([
            'default_strategy' => 'neural',
            'strategies_order' => ['neural', 'llm'],
            'llm_model' => 'qwen2.5:1.5b',
            'thresholds' => [
                'neural' => 0.50,  // Estrategia neural con perceptrones
                'fuzzy' => 0.40,
                'llm' => 0.70,     // LLM como fallback
            ]
        ]);

        $query = DB::table('products');

        if (isset($opts['ean'])) {
            $query->where('ean', $opts['ean']);
        }

        if ($limit) {
            $query->limit((int) $limit);
        }

        $products = $query->get();
        $total = count($products);
        
        echo "Procesando $total productos...\n";

        if ($batchSize > 1) {
            echo "🚀 Modo BATCH activado (batch_size=$batchSize)\n\n";
            $this->processProductsInBatch($products, $batchSize, $dryRun);
        } else {
            echo "Modo SECUENCIAL\n\n";
            $this->processProductsSequential($products, $dryRun);
        }

        DB::closeConnection();
    }

    /**
     * Procesa productos en modo batch
     */
    protected function processProductsInBatch($products, int $batchSize, bool $dryRun)
    {
        $total = count($products);
        $processed = 0;
        $errors = 0;
        
        // Convertir objetos a arrays para compatibilidad
        $productsArray = [];
        foreach ($products as $product) {
            $productsArray[] = is_array($product) ? $product : (array)$product;
        }
        
        echo "Procesando en batches de $batchSize...\n";
        $results = CategoryMapper::resolveBatch($productsArray, $batchSize);
        
        foreach ($productsArray as $idx => $product) {
            $ean = $product['ean'];
            $categories = $results[$idx] ?? [];
            
            echo "[$processed/$total] EAN: $ean\n";
            
            if (!empty($categories)) {
                echo "  → Categorías: " . implode(', ', $categories) . "\n";
                
                if (!$dryRun) {
                    DB::table('products')
                        ->where('ean', $ean)
                        ->update(['categories' => json_encode($categories)]);
                }
            } else {
                echo "  → No se encontraron categorías\n";
            }
            
            $processed++;
        }
        
        echo "\nResumen:\n";
        echo "- Productos procesados: $processed\n";
        echo "- Errores: $errors\n";
        
        if ($dryRun) {
            echo "- MODO SIMULACIÓN: No se realizaron cambios en la BD\n";
        }
    }

    /**
     * Procesa productos en modo secuencial (original)
     */
    protected function processProductsSequential($products, bool $dryRun)
    {
        $processed = 0;
        $errors = 0;
        $total = count($products);

        foreach ($products as $product) {
            try {
                $ean = is_array($product) ? $product['ean'] : $product->ean;
                echo "[$processed/$total] Procesando producto con EAN: $ean\n";

                $categories = CategoryMapper::resolveProduct($product, true);

                if (!empty($categories)) {
                    echo "  → Categorías asignadas: " . implode(', ', $categories) . "\n";

                    if (!$dryRun) {
                        DB::table('products')
                            ->where('ean', $ean)
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
                echo "→ ERROR: " . $e->getMessage() . "\n";
                continue;
            }
        }

        echo "\nResumen:\n";
        echo "- Productos procesados: $processed\n";
        echo "- Errores: $errors\n";

        if ($dryRun) {
            echo "- MODO SIMULACIÓN: No se realizaron cambios en la BD\n";
        }
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
            // Filtrar productos sin categorías o con array vacío
            $query->whereRaw("(categories IS NULL OR categories = '[]' OR categories = '' OR JSON_LENGTH(categories) = 0)");
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
            $ean = is_array($product) ? ($product['ean'] ?? null) : ($product->ean ?? null);

            try {
                $categories = CategoryMapper::resolveProduct($product, true);

                if (empty($categories)) {
                    StdOut::print("[{$processed}/{$total}] Producto EAN {$ean}: Sin categorías detectadas\n");
                    continue;
                }

                $categoriesJson = json_encode($categories);

                StdOut::print("[{$processed}/{$total}] Producto EAN {$ean}: " . implode(', ', $categories) . "\n");

                if (!$dryRun) {
                    DB::table('products')
                        ->where('ean', '=', $ean)
                        ->update([
                            'categories' => $categoriesJson
                        ]);
                    $updated++;
                }
            } catch (\Exception $e) {
                StdOut::print("[{$processed}/{$total}] ERROR en producto ID {$ean}: " . $e->getMessage() . "\n");
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
     * Router para comandos de marcas
     *
     * Uso: php com zippy brand <subcomando> [options]
     */
    public function brand($subcommand = null, ...$options)
    {
        if (empty($subcommand)) {
            StdOut::print("Error: Se requiere un subcomando.\n");
            StdOut::print("Uso: php com zippy brand <subcomando> [options]\n");
            StdOut::print("Ejecuta 'php com zippy help' para ver todos los subcomandos disponibles.\n");
            return;
        }

        $method = 'brand_' . $subcommand;

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
            'default_strategy' => 'neural',
            'strategies_order' => ['neural', 'llm'],
            'llm_model' => 'qwen2.5:3b',
            'llm_temperature' => 0.2,
            'thresholds' => [
                'neural' => 0.50,
                'fuzzy' => 0.40,
                'llm' => 0.90,
            ]
        ]);

        StdOut::print("Probando mapeo para: \"$raw\"\n");
        StdOut::print("Estrategia: neural + llm fallback\n\n");

        $result = CategoryMapper::resolve($raw);

        if (!empty($result)) {
            StdOut::print("✅ Resultado del mapeo:\n");
            StdOut::print("   • Slug: " . ($result['category_slug'] ?? 'N/A') . "\n");
            StdOut::print("   • ID: " . ($result['category_id'] ?? 'N/A') . "\n");
            StdOut::print("   • Creada: " . (($result['created'] ?? false) ? 'Sí' : 'No') . "\n");
            StdOut::print("   • Score: " . ($result['score'] ?? 0) . "\n");
            StdOut::print("   • Razón: " . ($result['reasoning'] ?? 'N/A') . "\n");
            if (isset($result['found_in'])) {
                StdOut::print("   • Encontrada en: " . $result['found_in'] . "\n");
            }
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
                $resolved = \Boctulus\Zippy\Libs\CategoryMapper::resolve($raw);

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
            // Normalizar manualmente si no hay slug
            $slug = mb_strtolower($name, 'UTF-8');
            $slug = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $slug);
            $slug = preg_replace('/[^a-z0-9]+/', '-', $slug);
            $slug = preg_replace('/-+/', '-', $slug);
            $slug = trim($slug, '-');
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
            'strategies_order' => ['neural', 'llm'],
            'thresholds' => [
                'neural' => 0.50,
                'llm' => 0.70
            ]
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
            'strategies_order' => ['neural', 'llm'],
            'thresholds' => [
                'neural' => 0.50,
                'llm' => 0.70
            ]
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

    /**
     * Muestra las categorías como un árbol
     * Genera el string del breadcrumb para cada categoría, ordena alfabéticamente y los muestra
     *
     * Uso: php com zippy category tree
     */

    protected function category_tree()
    {
        DB::setConnection('zippy');

        // Obtener todas las categorías desde la base de datos usando CategoryMapper
        $categoryMap = CategoryMapper::getCategories();

        if (empty($categoryMap)) {
            StdOut::print("No hay categorías para mostrar.\n");
            DB::closeConnection();
            return;
        }

        // Generar breadcrumbs para cada categoría
        $breadcrumbs = [];
        foreach ($categoryMap as $slug => $category) {
            $breadcrumb = CategoryMapper::getBreadcrumb($slug, $categoryMap);
            $breadcrumbs[] = [
                'breadcrumb' => $breadcrumb,
                'slug'       => $slug,
                'category'   => $category
            ];
        }

        // Ordenar el array de breadcrumbs alfabéticamente
        usort($breadcrumbs, function ($a, $b) {
            return strcmp($a['breadcrumb'], $b['breadcrumb']);
        });

        StdOut::print("=== Árbol de Categorías (ordenado alfabéticamente por breadcrumb) ===\n\n");
        StdOut::print("Total: " . count($breadcrumbs) . " categorías\n\n");

        // Mostrar breadcrumbs ordenados
        foreach ($breadcrumbs as $index => $item) {
             $slug = $item['slug'];
            StdOut::print(($index + 1) . ". " . $item['breadcrumb'] . " (slug: " . $slug . ")\n");
        }

        DB::closeConnection();
    }

    /**
     * Lista todas las marcas únicas del campo brand en la tabla products
     *
     * Uso: php com zippy brand list_raw [--limit=100]
     */
    protected function brand_list_raw(...$options)
    {
        $opts = $this->parseOptions($options);
        $limit = $opts['limit'] ?? null;

        StdOut::print("=== Marcas detectadas en productos ===\n");

        DB::setConnection('zippy');

        $sql = "SELECT DISTINCT brand FROM products WHERE brand IS NOT NULL AND brand != '' ORDER BY brand ASC";
        
        if ($limit !== null) {
            $sql .= " LIMIT " . (int)$limit;
        }

        $brands = DB::select($sql);

        DB::closeConnection();

        $total = count($brands);
        StdOut::print("Marcas únicas encontradas: {$total}\n\n");

        foreach ($brands as $idx => $row) {
            $brand = is_array($row) ? $row['brand'] : $row->brand;
            $displayLine = "[" . ($idx + 1) . "] {$brand}";
            StdOut::print($displayLine . "\n");
        }
    }

    /**
     * Sincroniza/puebla la tabla brands con todas las marcas encontradas en products
     *
     * Uso: php com zippy brand sync
     */
    protected function brand_sync(...$options)
    {
        StdOut::print("=== Sincronizando tabla brands con productos ===\n\n");

        DB::setConnection('zippy');

        // Obtener todas las marcas únicas de la tabla products
        $sql = "SELECT DISTINCT brand FROM products WHERE brand IS NOT NULL AND brand != '' ORDER BY brand ASC";
        $brands = DB::select($sql);
        $total = count($brands);

        StdOut::print("Marcas únicas encontradas en products: {$total}\n\n");

        $inserted = 0;
        $skipped = 0;
        $invalid = 0;

        foreach ($brands as $row) {
            $brand = is_array($row) ? $row['brand'] : $row->brand;

            // Validar si la marca es válida
            if (!static::isValidBrand($brand)) {
                $invalid++;
                continue;
            }

            $normalizedBrand = Strings::normalize($brand);

            // Verificar si ya existe
            $exists = DB::selectOne("SELECT id FROM brands WHERE brand = ? AND deleted_at IS NULL LIMIT 1", [$brand]);

            if ($exists) {
                $skipped++;
                continue;
            }

            // Insertar nueva marca
            DB::insert("INSERT INTO brands (brand, normalized_brand, created_at, updated_at) VALUES (?, ?, NOW(), NOW())", [$brand, $normalizedBrand]);
            $inserted++;

            if ($inserted % 50 == 0) {
                StdOut::print("  Procesadas {$inserted} marcas...\n");
            }
        }

        DB::closeConnection();

        StdOut::print("\n=== Resumen ===\n");
        StdOut::print("Total de marcas: {$total}\n");
        StdOut::print("Insertadas: {$inserted}\n");
        StdOut::print("Ya existían: {$skipped}\n");
        StdOut::print("Inválidas (excluidas): {$invalid}\n");
    }

    /**
     * Valida si una marca es válida para ser categorizada
     *
     * @param string $brand
     * @return bool
     */
    protected static function isValidBrand(string $brand): bool
    {
        $brand = trim($brand);

        // Rechazar marcas vacías
        if (empty($brand)) {
            return false;
        }

        // Rechazar marcas de 1 solo carácter
        if (mb_strlen($brand) < 2) {
            return false;
        }

        // Rechazar marcas que son 100% números (usando regex)
        if (preg_match('/^[0-9]+$/', $brand)) {
            return false;
        }

        // Rechazar marcas que son solo símbolos (sin letras ni números)
        if (!preg_match('/[a-zA-ZáéíóúÁÉÍÓÚñÑ0-9]/', $brand)) {
            return false;
        }

        // Contar cuántos caracteres alfabéticos tiene
        $alphaCount = preg_match_all('/[a-zA-ZáéíóúÁÉÍÓÚñÑ]/u', $brand);

        // Si no tiene al menos 2 caracteres alfabéticos, rechazar
        if ($alphaCount < 2) {
            return false;
        }

        return true;
    }

    /**
     * Categoriza cada marca usando LLM y crea registros en brand_categories
     *
     * Uso: 
     *   php com zippy brand categorize [--limit=100] [--dry-run]
     *   php com zippy brand categorize --limit=50 --batch-size=10  (modo batch)
     */
    protected function brand_categorize(...$options)
    {
        $opts = $this->parseOptions($options);
        $limit = $opts['limit'] ?? null;
        $dryRun = $opts['dry_run'] ?? false;
        $batchSize = $opts['batch_size'] ?? 1; // Default: 1 (secuencial)

        StdOut::print("=== Categorizando marcas con LLM ===\n");

        if ($dryRun) {
            StdOut::print("⚠ Modo DRY-RUN activado: no se guardarán cambios\n\n");
        }

        DB::setConnection('zippy');

        // Obtener marcas desde la tabla brands (ya filtradas)
        $sql = "SELECT brand FROM brands WHERE deleted_at IS NULL ORDER BY brand ASC";

        if ($limit !== null) {
            $sql .= " LIMIT " . (int)$limit;
        }

        $brands = DB::select($sql);
        $total = count($brands);

        StdOut::print("Marcas a procesar: {$total}\n\n");

        // Configurar CategoryMapper para usar LLM
        CategoryMapper::configure([
            'default_strategy' => 'neural',
            'strategies_order' => ['neural', 'llm'],
            'llm_model' => 'qwen2.5-coder:7b-instruct-q4_K_M',
            'llm_temperature' => 0.2,
            'llm_verbose' => false,
            'thresholds' => [
                'neural' => 0.50,  // Estrategia neural con perceptrones
                'llm' => 0.70,     // LLM como fallback
            ]
        ]);

        if ($batchSize > 1) {
            StdOut::print("🚀 Modo BATCH activado (batch_size=$batchSize)\n\n");
            $this->processBrandsInBatch($brands, $batchSize, $dryRun);
        } else {
            StdOut::print("Modo SECUENCIAL\n\n");
            $this->processBrandsSequential($brands, $dryRun);
        }

        DB::closeConnection();
    }

    /**
     * Procesa marcas en modo batch
     */
    protected function processBrandsInBatch($brands, int $batchSize, bool $dryRun)
    {
        $total = count($brands);
        $processed = 0;
        $saved = 0;
        $errors = 0;

        // Convertir a array plano de marcas
        $brandNames = [];
        foreach ($brands as $row) {
            $brandNames[] = is_array($row) ? $row['brand'] : $row->brand;
        }

        // Obtener categorías disponibles
        $availableCategories = CategoryMapper::getCategories();
        
        // Obtener estrategia LLM
        $strategies = CategoryMapper::getStrategies();
        $llmStrategy = $strategies['llm'] ?? null;
        
        if (!$llmStrategy) {
            StdOut::print("ERROR: No hay estrategia LLM disponible\n");
            return;
        }

        $threshold = 0.50;  // Neural strategy threshold

        // Procesar en batches
        $batches = array_chunk($brandNames, $batchSize, true);

        foreach ($batches as $batch) {
            try {
                // Llamada batch al LLM
                $batchResults = $llmStrategy->matchBatch($batch, $availableCategories, $threshold);

                // Procesar resultados
                foreach ($batch as $idx => $brand) {
                    $processed++;
                    StdOut::print("[{$processed}/{$total}] Procesando marca: {$brand}\n");

                    if (isset($batchResults[$idx]) && $batchResults[$idx]) {
                        $result = $batchResults[$idx];
                        $categorySlug = $result['category'];
                        $score = $result['score'] ?? 0;
                        $reasoning = $result['reasoning'] ?? 'N/A';

                        $confidenceLevel = static::determineConfidenceLevel($brand, $score, $categorySlug);

                        StdOut::print("  → Categoría: {$categorySlug}\n");
                        StdOut::print("  → Score: {$score}\n");
                        StdOut::print("  → Confianza: {$confidenceLevel}\n");
                        StdOut::print("  → Razón: {$reasoning}\n");

                        if (!$dryRun) {
                            if ($this->saveBrandCategorization($brand, $categorySlug, $confidenceLevel)) {
                                $saved++;
                            }
                        } else {
                            StdOut::print("  ℹ️  DRY-RUN: No se guardó\n\n");
                        }
                    } else {
                        StdOut::print("  ⚠ No se pudo categorizar\n\n");
                    }
                }
            } catch (\Exception $e) {
                $errors++;
                StdOut::print("  ❌ ERROR en batch: " . $e->getMessage() . "\n\n");
                // Continuar con siguiente batch
            }
        }

        StdOut::print("\n=== Resumen ===\n");
        StdOut::print("Procesadas: {$processed}\n");
        StdOut::print("Guardadas: " . ($dryRun ? "0 (dry-run)" : $saved) . "\n");
        StdOut::print("Errores: {$errors}\n");
    }

    /**
     * Procesa marcas en modo secuencial (original)
     */
    protected function processBrandsSequential($brands, bool $dryRun)
    {
        $total = count($brands);
        $processed = 0;
        $saved = 0;
        $errors = 0;

        foreach ($brands as $row) {
            $processed++;
            $brand = is_array($row) ? $row['brand'] : $row->brand;

            try {
                StdOut::print("[{$processed}/{$total}] Procesando marca: {$brand}\n");

                // Resolver categoría usando el LLM
                $result = CategoryMapper::resolve($brand);

                if (empty($result) || !isset($result['category_slug'])) {
                    StdOut::print("  ⚠ No se pudo categorizar\n\n");
                    continue;
                }

                $categorySlug = $result['category_slug'];
                $score = $result['score'] ?? 0;
                $reasoning = $result['reasoning'] ?? 'N/A';

                // Determinar confidence_level basándose en score y marca conocida
                $confidenceLevel = static::determineConfidenceLevel($brand, $score, $categorySlug);

                StdOut::print("  → Categoría: {$categorySlug}\n");
                StdOut::print("  → Score: {$score}\n");
                StdOut::print("  → Confianza: {$confidenceLevel}\n");
                StdOut::print("  → Razón: {$reasoning}\n");

                if (!$dryRun) {
                    if ($this->saveBrandCategorization($brand, $categorySlug, $confidenceLevel)) {
                        $saved++;
                    }
                } else {
                    StdOut::print("  ℹ️  DRY-RUN: No se guardó\n\n");
                }

            } catch (\Exception $e) {
                $errors++;
                StdOut::print("  ❌ ERROR: " . $e->getMessage() . "\n\n");
                continue;
            }
        }

        StdOut::print("\n=== Resumen ===\n");
        StdOut::print("Procesadas: {$processed}\n");
        StdOut::print("Guardadas: " . ($dryRun ? "0 (dry-run)" : $saved) . "\n");
        StdOut::print("Errores: {$errors}\n");
    }

    /**
     * Guarda la categorización de una marca
     */
    protected function saveBrandCategorization(string $brand, string $categorySlug, string $confidenceLevel): bool
    {
        // Buscar brand_id en la tabla brands
        $brandRecord = DB::selectOne("SELECT id FROM brands WHERE brand = ? AND deleted_at IS NULL LIMIT 1", [$brand]);

        if (!$brandRecord) {
            // Crear registro en brands si no existe
            $normalizedBrand = Strings::normalize($brand);
            $brandId = DB::insert("INSERT INTO brands (brand, normalized_brand, created_at, updated_at) VALUES (?, ?, NOW(), NOW())", [$brand, $normalizedBrand]);
        } else {
            $brandId = is_array($brandRecord) ? $brandRecord['id'] : $brandRecord->id;
        }

        // Buscar category_id
        $categoryRecord = DB::selectOne("SELECT id FROM categories WHERE slug = ? AND deleted_at IS NULL LIMIT 1", [$categorySlug]);

        if (!$categoryRecord) {
            StdOut::print("  ⚠ Categoría {$categorySlug} no existe en la tabla categories\n\n");
            return false;
        }

        $categoryId = is_array($categoryRecord) ? $categoryRecord['id'] : $categoryRecord->id;

        // Verificar si ya existe el registro en brand_categories
        $exists = DB::selectOne("SELECT id FROM brand_categories WHERE brand_id = ? AND category_id = ? AND deleted_at IS NULL LIMIT 1", [$brandId, $categoryId]);

        if (!$exists) {
            // Crear registro en brand_categories con confidence_level
            DB::insert("INSERT INTO brand_categories (brand_id, category_id, confidence_level, created_at, updated_at) VALUES (?, ?, ?, NOW(), NOW())", [$brandId, $categoryId, $confidenceLevel]);

            StdOut::print("  ✅ Registro guardado en brand_categories\n\n");
            return true;
        } else {
            StdOut::print("  ℹ️  Registro ya existe en brand_categories\n\n");
            return false;
        }
    }

    /**
     * Determina el nivel de confianza para la categorización de una marca
     *
     * @param string $brand Nombre de la marca
     * @param float $score Score del LLM (0-100)
     * @param string $categorySlug Slug de la categoría asignada
     * @return string 'high', 'medium', 'low', o 'doubtful'
     */
    protected static function determineConfidenceLevel(string $brand, float $score, string $categorySlug): string
    {
        $brandLower = strtolower($brand);

        // Marcas conocidas con alta confianza por categoría
        $wellKnownBrands = [
            'tecnologia' => ['xiaomi', 'samsung', 'apple', 'dell', 'hp', 'lenovo', 'acer', 'asus',
                            'lg', 'sony', 'microsoft', 'intel', 'amd', 'nvidia', 'canon', 'nikon',
                            'philips', 'motorola', 'huawei', 'nokia', 'alcatel', 'bgh', 'noblex'],
            'electrodomesticos' => ['bgh', 'whirlpool', 'philco', 'samsung', 'lg', 'drean', 'philips',
                                   'electrolux', 'ariston', 'longvie', 'ctz', 'atma', 'liliana'],
            'bebidas' => ['coca-cola', 'pepsi', 'fanta', 'sprite', 'quilmes', 'brahma', 'heineken',
                         'budweiser', 'corona', 'stella artois', 'schneider', 'andes', 'isenbeck'],
            'lacteos' => ['la serenisima', 'sancor', 'ilolay', 'milkaut', 'tregar', 'danone',
                         'mastellone', 'verónica', 'williner'],
            'golosinas' => ['arcor', 'bonafide', 'cadbury', 'nestle', 'ferrero', 'mars', 'toblerone',
                           'milka', 'kit kat', 'snickers', 'twix', 'aguila', 'georgalos', 'esnaola'],
            'panificados' => ['bimbo', 'fargo', 'lactal', 'pan blanco', 'don satur', 'bagley'],
            'pastas' => ['la juventud', 'la salteña', 'matarazzo', 'lucchetti', 'don vicente'],
            'alimentos' => ['knorr', 'maggi', 'hellmanns', 'natura', 'danica', 'molto', 'lucchetti',
                          'matarazzo', 'marolio', 'molinos', 'arcor', 'la virginia'],
        ];

        // Verificar si es una marca conocida
        $isWellKnown = false;
        foreach ($wellKnownBrands as $cat => $brands) {
            if (in_array($brandLower, $brands)) {
                $isWellKnown = true;
                break;
            }
        }

        // Lógica de determinación de confianza:
        // 1. Score >= 85 y marca conocida: high
        // 2. Score >= 70: medium
        // 3. Score >= 50 pero < 70: low
        // 4. Score < 50: doubtful

        if ($score >= 85 && $isWellKnown) {
            return 'high';
        } elseif ($score >= 70) {
            return 'medium';
        } elseif ($score >= 50) {
            return 'low';
        } else {
            return 'doubtful';
        }
    }

    // ================================================================
    // ROUTER: BRAND_CATEGORIES
    // ================================================================

    /**
     * Router para comandos de brand_categories
     *
     * Uso: php com zippy brand_categories <subcomando> [options]
     */
    public function brand_categories($subcommand = null, ...$options)
    {
        if (empty($subcommand)) {
            StdOut::print("Error: Se requiere un subcomando.\n");
            StdOut::print("Uso: php com zippy brand_categories <subcomando> [options]\n");
            StdOut::print("Ejecuta 'php com zippy help' para ver todos los subcomandos disponibles.\n");
            return;
        }

        $method = 'brand_categories_' . $subcommand;

        if (!method_exists($this, $method)) {
            StdOut::print("Error: Subcomando '$subcommand' no existe.\n");
            StdOut::print("Ejecuta 'php com zippy help' para ver los subcomandos disponibles.\n");
            return;
        }

        $this->$method(...$options);
    }

    /**
     * Lista las relaciones marca-categoría con nombres legibles
     *
     * Uso: php com zippy brand_categories list [--limit=N] [--offset=N]
     */
    protected function brand_categories_list(...$options)
    {
        $opts = $this->parseOptions($options);
        $limit = $opts['limit'] ?? 100;
        $offset = $opts['offset'] ?? 0;

        StdOut::print("=== Relaciones Marca-Categoría ===\n\n");

        DB::setConnection('zippy');

        $sql = "
            SELECT 
                bc.id as bc_id,
                b.id as brand_id,
                b.brand,
                c.id as category_id,
                c.name as category_name,
                c.slug as category_slug,
                bc.confidence_level,
                bc.created_at
            FROM brand_categories bc
            JOIN brands b ON bc.brand_id = b.id
            JOIN categories c ON bc.category_id = c.id
            WHERE bc.deleted_at IS NULL
              AND b.deleted_at IS NULL
              AND c.deleted_at IS NULL
            ORDER BY b.brand ASC, c.name ASC
            LIMIT ? OFFSET ?
        ";

        $results = DB::select($sql, [$limit, $offset]);

        DB::closeConnection();

        $total = count($results);
        StdOut::print("Mostrando {$total} relaciones (limit={$limit}, offset={$offset})\n\n");

        if ($total === 0) {
            StdOut::print("No hay relaciones marca-categoría registradas.\n");
            return;
        }

        foreach ($results as $idx => $row) {
            $num = $offset + $idx + 1;
            $brand = is_array($row) ? $row['brand'] : $row->brand;
            $categoryName = is_array($row) ? $row['category_name'] : $row->category_name;
            $categorySlug = is_array($row) ? $row['category_slug'] : $row->category_slug;
            $confidenceLevel = is_array($row) ? $row['confidence_level'] : $row->confidence_level;
            $brandId = is_array($row) ? $row['brand_id'] : $row->brand_id;
            $categoryId = is_array($row) ? $row['category_id'] : $row->category_id;
            $bcId = is_array($row) ? $row['bc_id'] : $row->bc_id;

            // Formatear confidence level con emoji
            $confidenceEmoji = match($confidenceLevel) {
                'high' => '🟢',
                'medium' => '🟡',
                'low' => '🟠',
                'doubtful' => '🔴',
                default => '⚪'
            };

            StdOut::print("[{$num}] {$brand} [brand_id: {$brandId}] → {$categoryName} [category_id: {$categoryId}]\n");
            StdOut::print("     Slug: {$categorySlug} | Confianza: {$confidenceEmoji} {$confidenceLevel} | ID: {$bcId}\n\n");
        }

        if ($total === $limit) {
            StdOut::print("\n💡 Tip: Hay más resultados. Usa --offset=" . ($offset + $limit) . " para ver la siguiente página.\n");
        }
    }

    /**
     * Counts the total number of brand-category relationships
     *
     * Uso: php com zippy brand_categories count
     */
    protected function brand_categories_count()
    {
        StdOut::print("=== Conteo de Relaciones Marca-Categoría ===\n\n");

        DB::setConnection('zippy');

        // Contar todas las relaciones activas en brand_categories
        $sql = "
            SELECT
                COUNT(*) as total
            FROM brand_categories bc
            JOIN brands b ON bc.brand_id = b.id
            JOIN categories c ON bc.category_id = c.id
            WHERE bc.deleted_at IS NULL
              AND b.deleted_at IS NULL
              AND c.deleted_at IS NULL
        ";

        $result = DB::selectOne($sql);
        $total = is_array($result) ? $result['total'] : $result->total;

        // Contar relaciones por nivel de confianza
        $byConfidence = DB::select("
            SELECT
                bc.confidence_level,
                COUNT(*) as count
            FROM brand_categories bc
            JOIN brands b ON bc.brand_id = b.id
            JOIN categories c ON bc.category_id = c.id
            WHERE bc.deleted_at IS NULL
              AND b.deleted_at IS NULL
              AND c.deleted_at IS NULL
            GROUP BY bc.confidence_level
            ORDER BY bc.confidence_level
        ");

        DB::closeConnection();

        StdOut::print("Total de relaciones marca-categoría: {$total}\n\n");

        if (!empty($byConfidence)) {
            StdOut::print("Distribución por nivel de confianza:\n");
            foreach ($byConfidence as $row) {
                $level = is_array($row) ? $row['confidence_level'] : $row->confidence_level;
                $count = is_array($row) ? $row['count'] : $row->count;

                // Formatear confidence level con emoji
                $confidenceEmoji = match($level) {
                    'high' => '🟢',
                    'medium' => '🟡',
                    'low' => '🟠',
                    'doubtful' => '🔴',
                    default => '⚪'
                };

                StdOut::print("  {$confidenceEmoji} {$level}: {$count} relaciones\n");
            }
            StdOut::print("\n");
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

        $threshold = 0.50;  // Neural strategy threshold

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

  category tree
    Muestra las categorías como un árbol en formato breadcrumb ordenado alfabéticamente

    Ejemplo:
      php com zippy category tree

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
🏷️  COMANDOS DE MARCAS - GESTIÓN Y CONSULTA
═══════════════════════════════════════════════════════════════════════════

  brand_categories list [--limit=N] [--offset=N]
    Lista todas las relaciones entre marcas y categorías
    Muestra marca, categoría asignada, nivel de confianza y metadatos

    Opciones:
      --limit=N           Cantidad de relaciones a mostrar (default: 100)
      --offset=N          Offset para paginación (default: 0)

    Ejemplo:
      php com zippy brand_categories list
      php com zippy brand_categories list --limit=50 --offset=100

  brand_categories count
    Muestra el conteo total de relaciones entre marcas y categorías
    Incluye distribución por nivel de confianza

    Ejemplo:
      php com zippy brand_categories count

═══════════════════════════════════════════════════════════════════════════
🧪 COMANDOS DE MARCAS - CATEGORIZACIÓN AUTOMÁTICA
═══════════════════════════════════════════════════════════════════════════

  brand list_raw [--limit=N]
    Lista todas las marcas únicas encontradas en la tabla products

    Opciones:
      --limit=N           Cantidad máxima a mostrar (opcional)

    Ejemplo:
      php com zippy brand list_raw
      php com zippy brand list_raw --limit=50

  brand sync
    Sincroniza/puebla la tabla brands con todas las marcas de products
    Crea registros en brands para marcas que no existen

    Ejemplo:
      php com zippy brand sync

  brand categorize [--limit=N] [--dry-run]
    Categoriza marcas automáticamente usando IA/ML

    Opciones:
      --limit=N           Cantidad de marcas a categorizar
      --dry-run           Modo simulación, no guarda cambios

    Ejemplos:
      # Categorizar las primeras 10 marcas en modo simulación
      php com zippy brand categorize --limit=10 --dry-run

      # Categorizar todas las marcas y guardar en BD
      php com zippy brand categorize

      # Categorizar las primeras 50 marcas
      php com zippy brand categorize --limit=50

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
🧠 COMANDOS DE PESOS NEURONALES (Neural Weights)
═══════════════════════════════════════════════════════════════════════════

  weights seed [--force]
    Pobla la tabla neural_weights con pesos hardcoded
    Útil para inicializar el sistema de categorización por keywords

    Opciones:
      --force             Sobrescribe pesos existentes (default: no)

    Ejemplo:
      php com zippy weights seed
      php com zippy weights seed --force

  weights list [--category=slug] [--limit=N]
    Lista los pesos almacenados en neural_weights

    Opciones:
      --category=slug     Filtrar por categoría específica
      --limit=N           Cantidad máxima a mostrar (default: 100)

    Ejemplo:
      php com zippy weights list
      php com zippy weights list --category=electro
      php com zippy weights list --limit=50

  weights clear [--confirm]
    Limpia todos los pesos de la tabla neural_weights
    ADVERTENCIA: Operación destructiva

    Opciones:
      --confirm           Confirmar la eliminación (REQUERIDO)

    Ejemplo:
      php com zippy weights clear --confirm

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

🔹 FLUJO 2: Exploración y testing con marcas  [ REVISAR ]
   1. php com zippy brand list_raw
   2. php com zippy brand categorize --limit=10
   3. php com zippy brand categorize

🔹 FLUJO 3: Exploración y testing con categorias
   1. php com zippy category list_raw --limit=100
   2. php com zippy category test --raw="Aceites Y Condimentos"
   3. php com zippy category resolve --text="Leche entera 1L"

🔹 FLUJO 4: Procesamiento en producción
   1. php com zippy category report_issues
   2. php com zippy product process_one {ean_code} [ --dry-run ]
   3. php com zippy product process --limit=10 [ --dry-run ]
   4. php com zippy product batch --limit=1000 [ --only-unmapped ]

🔹 FLUJO 5: Validación de LLM
   1. php com zippy ollama test_strategy
   2. php com zippy ollama hard_tests
   3. php com zippy category test --raw="..." --strategy=llm
   4. php com zippy category resolve_product --raw1="..."

═══════════════════════════════════════════════════════════════════════════

STR;

        dd($str);
    }

    // ================================================================
    // ROUTER: WEIGHTS (Neural Network Weights Management)
    // ================================================================

    /**
     * Router para comandos de pesos de red neuronal
     *
     * Uso: php com zippy weights <subcomando> [options]
     */
    public function weights($subcommand = null, ...$options)
    {
        if (empty($subcommand)) {
            StdOut::print("Error: Se requiere un subcomando.\n");
            StdOut::print("Uso: php com zippy weights <subcomando> [options]\n");
            StdOut::print("Subcomandos disponibles:\n");
            StdOut::print("  seed      - Poblar tabla neural_weights desde definición hardcoded\n");
            StdOut::print("  list      - Listar todos los pesos en BD\n");
            StdOut::print("  clear     - Limpiar tabla neural_weights\n");
            return;
        }

        $method = 'weights_' . $subcommand;

        if (!method_exists($this, $method)) {
            StdOut::print("Error: Subcomando '$subcommand' no existe.\n");
            StdOut::print("Ejecuta 'php com zippy weights' para ver los subcomandos disponibles.\n");
            return;
        }

        $this->$method(...$options);
    }

    /**
     * Pobla la tabla neural_weights con los pesos hardcoded
     *
     * Uso: php com zippy weights seed [--force]
     */
    protected function weights_seed(...$options)
    {
        $opts = $this->parseOptions($options);
        $force = $opts['force'] ?? false;

        DB::setConnection('zippy');

        // Verificar si ya hay datos
        $existingCount = DB::select("SELECT COUNT(*) as count FROM neural_weights")[0]->count ?? 0;

        if ($existingCount > 0 && !$force) {
            StdOut::print("⚠️  La tabla neural_weights ya tiene {$existingCount} registros.", 'yellow');
            StdOut::print("   Usa --force para sobrescribir.", 'yellow');
            StdOut::print("\n   php com zippy weights seed --force\n");
            return;
        }

        if ($force && $existingCount > 0) {
            StdOut::print("🗑️  Limpiando {$existingCount} registros existentes...", 'yellow');
            DB::statement("TRUNCATE TABLE neural_weights");
        }

        StdOut::print("\n🧠 Poblando tabla neural_weights...\n", 'green');

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

        // Definición de pesos (hardcoded)
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
                'aire' => 0.7,
                'heladera' => 0.9,
                'helad' => 0.9, // Abreviatura común
                'freezer' => 0.9,
                'lavarropas' => 0.9,
                'microondas' => 0.9,
                'smart' => 0.7,
                'tv' => 0.9,
                'televisor' => 0.9,
                'funda' => 0.6,
                'cargador' => 0.7,
                'cable' => 0.6,
            ],
            'hogar-y-bazar' => [
                'cocina' => 0.9,
                'horno' => 0.9,
                'anafe' => 0.9,
                'bazar' => 0.8,
                'cama' => 0.9,
                'colchon' => 0.9,
                'sarten' => 0.8,
                'olla' => 0.8,
            ],
            'panaderia' => [
                'pan' => 0.8,
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
                'matera' => 0.7,
            ],
            'bebidas' => [
                'tinto' => 0.8,
                'blanco' => 0.7,
                'rosado' => 0.8,
                'vino' => 0.9,
                'cerveza' => 0.9,
                'agua' => 0.8,
                'gaseosa' => 0.9,
                'jugo' => 0.9,
                'cola' => 0.8,
                'limonada' => 0.8,
                'naranja' => 0.6,
            ],
            'embutidos' => [
                'frankfurt' => 0.9,
                'viena' => 0.9,
                'aleman' => 0.7,
                'parrillero' => 0.8,
                'salame' => 0.9,
                'jamon' => 0.9,
                'mortadela' => 0.9,
                'longaniza' => 0.9,
                'chorizo' => 0.9,
            ],
            'congelados' => [
                'cong' => 0.8,
                'congelado' => 0.9,
                'frozen' => 0.9,
                'pollo' => 0.7,
                'carne' => 0.6,
                'vacuna' => 0.7,
                'cerdo' => 0.8,
                'pescado' => 0.8,
                'mozzarella' => 0.6,
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
                'trit' => 0.7,
                'tritado' => 0.8,
                'perita' => 0.7,
                'sopa' => 0.9,
                'ramen' => 0.9,
                'caldo' => 0.9,
                'fideo' => 0.8,
                'membrillo' => 0.9,
                'batata' => 0.9,
                'cayote' => 0.9,
                'veloce' => 0.7,
                'mermelada' => 0.9,
                'dulce' => 0.8,
                'condimento' => 0.8,
                'aderezo' => 0.8,
                'polvo' => 0.7,
            ],
            'golosinas' => [
                'caramelo' => 0.9,
                'chupet' => 0.9,
                'chupetín' => 0.9,
                'chicle' => 0.9,
                'chocolate' => 0.8,
                'choco' => 0.7,
                'cacao' => 0.7,
                'cereal' => 0.7,
                'barra' => 0.6,
                'oblea' => 0.8,
                'wafer' => 0.8,
            ],
            'frutas-y-verduras' => [
                'fruta' => 0.8,
                'frutas' => 0.8,
                'verdura' => 0.8,
                'verduras' => 0.8,
                'arandano' => 0.8,
                'frutilla' => 0.8,
                'durazno' => 0.8,
                'manzana' => 0.7,
                'ciruela' => 0.8,
                'banana' => 0.8,
                'naranja' => 0.7,
                'mandarina' => 0.8,
                'limon' => 0.7,
                'pera' => 0.8,
                'uva' => 0.8,
            ],
            'limpieza' => [
                'det' => 0.95, // Peso muy alto para desambiguar
                'detergente' => 0.95,
                'lavandina' => 0.9,
                'jabon' => 0.9,
                'suavizante' => 0.9,
                'desodorante' => 0.8,
                'limpiador' => 0.9,
                'trapo' => 0.8,
                'esponja' => 0.8,
                'escoba' => 0.8,
                'mopa' => 0.8,
                'papel' => 0.7,
                'rollo' => 0.7,
            ],
        ];

        $totalInserted = 0;
        $now = date('Y-m-d H:i:s');

        foreach ($keywordWeights as $categorySlug => $words) {
            if (!isset($categoryMap[$categorySlug])) {
                StdOut::print("⚠️  Categoría '$categorySlug' no encontrada en BD, saltando...", 'yellow');
                continue;
            }

            $categoryId = $categoryMap[$categorySlug];
            $categoryName = $categories[array_search($categorySlug, array_column($categories, 'slug'))]->name ?? $categorySlug;

            StdOut::print("📂 $categoryName ($categorySlug): ", 'cyan');

            $inserted = 0;
            foreach ($words as $word => $weight) {
                DB::statement("
                    INSERT INTO neural_weights
                    (word, category_slug, weight, source, usage_count, last_used_at, created_at, updated_at)
                    VALUES (?, ?, ?, 'hardcoded', 0, NULL, ?, ?)
                ", [$word, $categorySlug, $weight, $now, $now]);
                $inserted++;
                $totalInserted++;
            }

            StdOut::print("{$inserted} palabras\n", 'green');
        }

        StdOut::print("\n✅ Seed completado: {$totalInserted} pesos insertados\n", 'green');
        StdOut::print("   Total categorías procesadas: " . count($keywordWeights) . "\n");
    }

    /**
     * Lista todos los pesos en la tabla neural_weights
     *
     * Uso: php com zippy weights list [--category=slug] [--limit=N]
     */
    protected function weights_list(...$options)
    {
        $opts = $this->parseOptions($options);
        $category = $opts['category'] ?? null;
        $limit = $opts['limit'] ?? 100;

        DB::setConnection('zippy');

        $whereClause = $category ? "WHERE category_slug = ?" : "";
        $params = $category ? [$category] : [];

        $weights = DB::select("
            SELECT * FROM neural_weights
            $whereClause
            LIMIT $limit
        ", $params);

        $total = DB::select("SELECT COUNT(*) as count FROM neural_weights")[0]->count ?? 0;

        StdOut::print("\n📊 Pesos en neural_weights\n", 'cyan');
        StdOut::print("─────────────────────────────────────────\n");

        if (empty($weights)) {
            StdOut::print("⚠️  No hay pesos en la tabla\n", 'yellow');
            StdOut::print("   Ejecuta: php com zippy weights seed\n");
            return;
        }

        foreach ($weights as $w) {
            $word = is_array($w) ? $w['word'] : $w->word;
            $cat = is_array($w) ? $w['category_slug'] : $w->category_slug;
            $weight = is_array($w) ? $w['weight'] : $w->weight;
            $source = is_array($w) ? $w['source'] : $w->source;
            $usage = is_array($w) ? $w['usage_count'] : $w->usage_count;

            StdOut::print(sprintf(
                "%-20s %-25s %s (source: %s, used: %dx)\n",
                $word,
                $cat,
                $weight,
                $source,
                $usage
            ));
        }

        StdOut::print("\n📈 Total en BD: {$total} pesos\n", 'green');
        if ($total > $limit) {
            StdOut::print("   (Mostrando primeros {$limit}. Usa --limit=N para ver más)\n", 'yellow');
        }
    }

    /**
     * Entrena la red neuronal analizando productos ya categorizados
     * 
     * Uso: php com zippy weights train [--limit=5000] [--min-freq=3]
     */
    protected function weights_train(...$options)
    {
        $opts = $this->parseOptions($options);
        $limit = $opts['limit'] ?? 5000;
        $minFreq = $opts['min_freq'] ?? 2; // Reducido a 2 para capturar más palabras
        
        DB::setConnection('zippy');
        
        StdOut::print("🧠 Iniciando entrenamiento de red neuronal...\n");
        
        // 1. Obtener productos categorizados
        $products = DB::table('products')
            ->select('description', 'categories')
            ->whereNotNull('categories')
            ->whereRaw("categories != '[]' AND categories != ''")
            ->limit($limit)
            ->get();
            
        $totalProducts = count($products);
        StdOut::print("Analizando {$totalProducts} productos categorizados.\n");
        
        if ($totalProducts === 0) {
            StdOut::print("❌ No hay productos categorizados para entrenar.\n");
            return;
        }

        // 2. Construir corpus
        $categoryWords = []; // [category => [word => count]]
        $wordGlobalFreq = []; // [word => count]
        $totalWords = 0;
        
        foreach ($products as $p) {
            // Manejar objeto o array
            $categoriesRaw = is_array($p) ? ($p['categories'] ?? null) : ($p->categories ?? null);
            $descriptionRaw = is_array($p) ? ($p['description'] ?? null) : ($p->description ?? null);

            if (empty($categoriesRaw)) continue;

            $cats = json_decode($categoriesRaw, true);
            if (!is_array($cats)) continue;
            
            // Normalizar descripción
            $desc = (string)$descriptionRaw;
            // Usar una normalización más suave para palabras clave (mantener espacios para separar)
            $desc = mb_strtolower($desc, 'UTF-8');
            $desc = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $desc);
            $desc = preg_replace('/[^a-z0-9\s]+/', '', $desc); // Solo letras, números y espacios
            
            $words = array_filter(explode(' ', $desc), function($w) {
                return strlen($w) > 2; // Ignorar palabras muy cortas
            });
            
            foreach ($cats as $catSlug) {
                if (!isset($categoryWords[$catSlug])) {
                    $categoryWords[$catSlug] = [];
                }
                
                foreach ($words as $word) {
                    if (!isset($categoryWords[$catSlug][$word])) {
                        $categoryWords[$catSlug][$word] = 0;
                    }
                    $categoryWords[$catSlug][$word]++;
                    
                    if (!isset($wordGlobalFreq[$word])) {
                        $wordGlobalFreq[$word] = 0;
                    }
                    $wordGlobalFreq[$word]++;
                    $totalWords++;
                }
            }
        }
        
        // 3. Calcular pesos y guardar
        // Peso = (Freq en Cat / Total Freq en Cat) * (1 / (Freq Global / Total Words)) 
        // Simplificado: Relevancia = Freq en Cat / Freq Global
        // Si una palabra aparece 10 veces en total, y 9 son en "electro", es muy relevante para electro (0.9)
        
        $newWeights = [];
        $count = 0;
        
        StdOut::print("Calculando pesos...\n");
        
        foreach ($categoryWords as $catSlug => $words) {
            foreach ($words as $word => $freqInCat) {
                if ($freqInCat < $minFreq) continue;
                
                $freqGlobal = $wordGlobalFreq[$word];
                
                // Probabilidad condicional: P(Cat|Word)
                // Qué probabilidad hay de que sea esta categoría dado que aparece esta palabra
                $weight = $freqInCat / $freqGlobal;
                
                // Penalizar palabras que aparecen en demasiadas categorías distintas (stopwords de dominio)
                // (Opcional, por ahora confiamos en el ratio)
                
                if ($weight >= 0.5) { // Solo guardar si es medianamente relevante
                    $newWeights[] = [
                        'word' => $word,
                        'category_slug' => $catSlug,
                        'weight' => number_format($weight, 3),
                        'source' => 'trained',
                        'usage_count' => 0,
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s')
                    ];
                    $count++;
                }
            }
        }
        
        if (empty($newWeights)) {
            StdOut::print("⚠ No se generaron pesos nuevos (quizás aumentar dataset o bajar min-freq).\n");
            return;
        }
        
        // Guardar en BD
        StdOut::print("Guardando {$count} nuevos pesos...\n");
        
        // Insertar en chunks usando SQL directo para evitar problemas con Modelos
        $chunks = array_chunk($newWeights, 500);
        foreach ($chunks as $chunk) {
            $values = [];
            $bindings = [];
            
            foreach ($chunk as $row) {
                $values[] = "(?, ?, ?, ?, ?, ?, ?)";
                $bindings[] = $row['word'];
                $bindings[] = $row['category_slug'];
                $bindings[] = $row['weight'];
                $bindings[] = $row['source'];
                $bindings[] = $row['usage_count'];
                $bindings[] = $row['created_at'];
                $bindings[] = $row['updated_at'];
            }
            
            $sql = "INSERT INTO neural_weights (word, category_slug, weight, source, usage_count, created_at, updated_at) VALUES " . implode(', ', $values);
            $sql .= " ON DUPLICATE KEY UPDATE weight = VALUES(weight), source = VALUES(source), updated_at = VALUES(updated_at)";
            
            DB::statement($sql, $bindings);
        }
        
        StdOut::print("✅ Entrenamiento finalizado.\n");
    }

    /**
     * Limpia la tabla neural_weights
     *
     * Uso: php com zippy weights clear [--confirm]
     */
    protected function weights_clear(...$options)
    {
        $opts = $this->parseOptions($options);
        $confirm = $opts['confirm'] ?? false;

        if (!$confirm) {
            StdOut::print("⚠️  ADVERTENCIA: Esto eliminará TODOS los pesos de la tabla neural_weights\n", 'yellow');
            StdOut::print("   Para confirmar, ejecuta:\n");
            StdOut::print("   php com zippy weights clear --confirm\n");
            return;
        }

        DB::setConnection('zippy');

        $count = DB::select("SELECT COUNT(*) as count FROM neural_weights")[0]->count ?? 0;

        if ($count === 0) {
            StdOut::print("ℹ️  La tabla neural_weights ya está vacía\n", 'cyan');
            return;
        }

        DB::statement("TRUNCATE TABLE neural_weights");

        StdOut::print("✅ Tabla neural_weights limpiada ({$count} registros eliminados)\n", 'green');
    }

    /**
     * Shows diagnostic statistics for product categories
     * Shows how many products have and don't have assigned categories
     *
     * Uso: php com zippy product stats_categories
     */
    protected function product_stats_categories()
    {
        StdOut::print("=== Diagnóstico de Categorías de Productos ===\n\n");

        DB::setConnection('zippy');

        // Total products
        $totalProducts = DB::table('products')->count();

        // Products with categories (not null, not empty string, not empty JSON array)
        $withCategories = DB::selectOne("
            SELECT COUNT(*) as count
            FROM products
            WHERE categories IS NOT NULL
              AND categories != ''
              AND categories != '[]'
              AND JSON_LENGTH(categories) > 0
        ");

        // Products without categories (null, empty string, or empty JSON array)
        $withoutCategories = DB::selectOne("
            SELECT COUNT(*) as count
            FROM products
            WHERE categories IS NULL
               OR categories = ''
               OR categories = '[]'
               OR JSON_LENGTH(categories) = 0
        ");

        $withCatCount = is_array($withCategories) ? $withCategories['count'] : $withCategories->count;
        $withoutCatCount = is_array($withoutCategories) ? $withoutCategories['count'] : $withoutCategories->count;

        // Calculate percentages
        $withCatPct = $totalProducts > 0 ? round(($withCatCount / $totalProducts) * 100, 2) : 0;
        $withoutCatPct = $totalProducts > 0 ? round(($withoutCatCount / $totalProducts) * 100, 2) : 0;

        StdOut::print("📊 ESTADÍSTICAS DE CATEGORÍAS DE PRODUCTOS\n");
        StdOut::print("─────────────────────────────────────────\n");
        StdOut::print("Total de productos: {$totalProducts}\n\n");

        StdOut::print("✅ Con categorías asignadas: {$withCatCount} ({$withCatPct}%)\n");
        StdOut::print("❌ Sin categorías asignadas: {$withoutCatCount} ({$withoutCatPct}%)\n\n");

        // Additional breakdown by raw categories (catego_raw1, catego_raw2, catego_raw3)
        StdOut::print("🔍 ANÁLISIS ADICIONAL DE CATEGORÍAS RAW\n");
        StdOut::print("─────────────────────────────────────────\n");

        // Count products with raw categories
        $rawCat1Count = DB::table('products')->whereNotNull('catego_raw1')->where('catego_raw1', '!=', '')->count();
        $rawCat2Count = DB::table('products')->whereNotNull('catego_raw2')->where('catego_raw2', '!=', '')->count();
        $rawCat3Count = DB::table('products')->whereNotNull('catego_raw3')->where('catego_raw3', '!=', '')->count();

        StdOut::print("Con catego_raw1: {$rawCat1Count}\n");
        StdOut::print("Con catego_raw2: {$rawCat2Count}\n");
        StdOut::print("Con catego_raw3: {$rawCat3Count}\n\n");

        DB::closeConnection();
    }

    /**
     * Parsea las opciones pasadas al comando
     *
     * Soporta formatos:
     *   --key=value
     *   --key:value
     *   --flag (devuelve true)
     *
     * @param array $args Argumentos del comando
     * @return array Opciones parseadas
     */
    protected function parseOptions(array $args): array
    {
        $options = [];

        foreach ($args as $arg) {
            // Formato: --key=value o --key:value
            if (preg_match('/^--([^=:]+)[=:](.+)$/', $arg, $matches)) {
                $key = str_replace('-', '_', $matches[1]);
                $value = $matches[2];

                // Convertir a booleano si es necesario
                if (strtolower($value) === 'true') {
                    $value = true;
                } elseif (strtolower($value) === 'false') {
                    $value = false;
                } elseif (is_numeric($value)) {
                    $value = (int)$value;
                }

                $options[$key] = $value;
            }
            // Formato: --flag (sin valor)
            elseif (preg_match('/^--([^=:]+)$/', $arg, $matches)) {
                $key = str_replace('-', '_', $matches[1]);
                $options[$key] = true;
            }
            // Formato: -k (flag corto)
            elseif (preg_match('/^-([a-z])$/i', $arg, $matches)) {
                $options[$matches[1]] = true;
            }
        }

        return $options;
    }
}