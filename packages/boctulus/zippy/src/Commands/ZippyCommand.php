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

        $stats = CategoryMapper::getStats();
        StdOut::print("\nEstadísticas de mapeo:\n");
        foreach ($stats as $key => $value) {
            StdOut::print("- " . ucfirst(str_replace('_', ' ', $key)) . ": $value\n");
        }
    }

    /**
     * Limpia el caché de CategoryMapper
     */
    public function clear_cache()
    {
        CategoryMapper::clearCache();
        StdOut::print("✓ Caché de CategoryMapper limpiado.\n");
    }

    /**
     * Lista categorías raw detectadas en products
     * 
     * Son categorias unicas (se eliminan duplicados de la lista de resultados)
     */
    public function category_list(...$options)
    {
        $opts = $this->parseOptions($options);
        $limit = $opts['limit'] ?? 100;

        StdOut::print("=== Categorías raw detectadas en productos ===\n");

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
            StdOut::print("[" . ($idx + 1) . "] {$raw}\n");
        }
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

Comandos disponibles:

  process_categories [options]
    Procesa categorías de productos en batch
    Opciones:
      --limit=N           Limitar cantidad
      --offset=N          Offset para paginación
      --only-unmapped     Solo productos sin categories
      --dry-run           No guardar cambios

  map_stats
    Muestra estadísticas de mappings

  show_unmapped [options]
    Muestra mappings que necesitan revisión
    Opciones:
      --limit=N     Limitar resultados (default: 20)
      --type=X      unmapped|fuzzy|all (default: unmapped)

  review_mapping --id=<id> [options]
    Revisa y confirma un mapping
    Opciones:
      --id=N        ID del mapping (requerido)
      --slug=X      Slug de categoría a asignar
      --reject      Rechazar el mapping

  test_mapping --raw="<value>"
    Prueba el mapeo de una categoría sin guardar


  category_list [--limit=100]
    Lista categorías raw encontradas en productos

  clear_cache
    Limpia el caché de CategoryMapper

Ejemplos:

  php com zippy process_categories --limit=100 --dry-run
  php com zippy map_stats
  php com zippy show_unmapped --limit=50 --type=fuzzy
  php com zippy review_mapping --id=5 --slug=almacen
  php com zippy test_mapping --raw="Aceites Y Condimentos"
  php com zippy import_initial_mappings

STR;

        dd($str);
    }
}