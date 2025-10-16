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
     * Muestra estadísticas de mappings de categorías
     */
    public function map_stats()
    {
        StdOut::print("=== Estadísticas de Category Mappings ===\n");

        $stats = CategoryMapper::getStats();

        StdOut::print("Total mappings: {$stats['total']}\n");
        StdOut::print("Mapeados: {$stats['mapped']} ({$stats['mapping_rate']}%)\n");
        StdOut::print("Sin mapear: {$stats['unmapped']}\n");
        StdOut::print("Revisados: {$stats['reviewed']}\n");
        StdOut::print("Necesitan revisión: {$stats['needs_review']}\n");

        $types = DB::table('category_mappings')
            ->selectRaw('mapping_type, COUNT(*) as count')
            ->whereNull('deleted_at')
            ->groupBy('mapping_type')
            ->get();

        if (!empty($types)) {
            StdOut::print("\n--- Desglose por tipo ---\n");
            foreach ($types as $type) {
                $mappingType = is_array($type) ? $type['mapping_type'] : $type->mapping_type;
                $count = is_array($type) ? $type['count'] : $type->count;
                StdOut::print("  {$mappingType}: {$count}\n");
            }
        }
    }

    /**
     * Muestra mappings que necesitan revisión
     */
    public function show_unmapped(...$options)
    {
        $opts = $this->parseOptions($options);
        $limit = $opts['limit'] ?? 20;
        $type = $opts['type'] ?? 'unmapped';

        StdOut::print("=== Mappings que necesitan revisión ===\n");

        $query = DB::table('category_mappings')
            ->whereNull('deleted_at')
            ->where('is_reviewed', 0);

        if ($type === 'unmapped') {
            $query->where('mapping_type', 'unmapped');
        } elseif ($type === 'fuzzy') {
            $query->where('mapping_type', 'fuzzy');
        }

        $query->orderBy('created_at', 'DESC');

        if ($limit) {
            $query->limit($limit);
        }

        $mappings = $query->get();

        if (empty($mappings)) {
            StdOut::print("No hay mappings pendientes de revisión.\n");
            return;
        }

        StdOut::print("Encontrados: " . count($mappings) . "\n\n");

        foreach ($mappings as $m) {
            $mArray = is_array($m) ? $m : (array)$m;
            StdOut::print("ID: {$mArray['id']}\n");
            StdOut::print("  Raw: {$mArray['raw_value']}\n");
            StdOut::print("  Normalized: {$mArray['normalized']}\n");
            StdOut::print("  Mapped to: " . ($mArray['category_slug'] ?? 'NULL') . "\n");
            StdOut::print("  Type: {$mArray['mapping_type']}\n");
            if (!empty($mArray['confidence'])) {
                StdOut::print("  Confidence: {$mArray['confidence']}%\n");
            }
            if (!empty($mArray['notes'])) {
                StdOut::print("  Notes: {$mArray['notes']}\n");
            }
            StdOut::print("\n");
        }

        StdOut::print("Para revisar un mapping usa:\n");
        StdOut::print("  php com zippy review_mapping --id=<id> --slug=<slug>\n");
    }

    /**
     * Marca un mapping como revisado y opcionalmente cambia la categoría
     */
    public function review_mapping(...$options)
    {
        $opts = $this->parseOptions($options);
        $id = $opts['id'] ?? null;
        $slug = $opts['slug'] ?? null;
        $reject = $opts['reject'] ?? false;

        if (!$id) {
            StdOut::print("Error: Se requiere --id=<id>\n");
            StdOut::print("Uso: php com zippy review_mapping --id=<id> [--slug=<slug>] [--reject]\n");
            return;
        }

        $mapping = DB::table('category_mappings')
            ->where('id', $id)
            ->whereNull('deleted_at')
            ->first();

        if (!$mapping) {
            StdOut::print("Error: Mapping con ID {$id} no encontrado.\n");
            return;
        }

        $mappingArray = is_array($mapping) ? $mapping : (array)$mapping;

        if ($reject) {
            $notes = ($mappingArray['notes'] ?? '') . ' | Rejected by manual review';
            
            DB::table('category_mappings')
                ->where('id', $id)
                ->update([
                    'is_reviewed' => 1,
                    'reviewed_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                    'category_id' => null,
                    'category_slug' => null,
                    'mapping_type' => 'unmapped',
                    'notes' => $notes
                ]);

            StdOut::print("✓ Mapping ID {$id} revisado y rechazado.\n");
            StdOut::print("  Raw: {$mappingArray['raw_value']}\n");

        } elseif ($slug) {
            $category = DB::table('categories')
                ->where('slug', $slug)
                ->whereNull('deleted_at')
                ->first();

            if (!$category) {
                StdOut::print("Error: Categoría con slug '{$slug}' no encontrada.\n");
                return;
            }

            $catArray = is_array($category) ? $category : (array)$category;
            $notes = ($mappingArray['notes'] ?? '') . ' | Confirmed by manual review';

            DB::table('category_mappings')
                ->where('id', $id)
                ->update([
                    'is_reviewed' => 1,
                    'reviewed_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                    'category_id' => $catArray['id'],
                    'category_slug' => $catArray['slug'],
                    'mapping_type' => 'manual',
                    'notes' => $notes
                ]);

            StdOut::print("✓ Mapping ID {$id} revisado y confirmado.\n");
            StdOut::print("  Raw: {$mappingArray['raw_value']}\n");
            StdOut::print("  Nueva categoría: {$slug}\n");
        } else {
            DB::table('category_mappings')
                ->where('id', $id)
                ->update([
                    'is_reviewed' => 1,
                    'reviewed_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ]);

            StdOut::print("✓ Mapping ID {$id} marcado como revisado.\n");
            StdOut::print("  Raw: {$mappingArray['raw_value']}\n");
        }
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
     * Importa mappings iniciales desde SQL
     */
    public function import_initial_mappings(...$options)
    {
        $opts = $this->parseOptions($options);
        $force = $opts['force'] ?? false;

        StdOut::print("=== Importando mappings iniciales ===\n");

        $initialMappings = [
            ['raw' => 'Aceites Y Condimentos', 'slug' => 'almacen', 'type' => 'manual'],
            ['raw' => 'Aderezos Y Salsas', 'slug' => 'almacen', 'type' => 'manual'],
            ['raw' => 'Alimento De Bebés Y Niños', 'slug' => 'almacen', 'type' => 'manual'],
            ['raw' => 'Arroz Y Legumbres', 'slug' => 'almacen', 'type' => 'manual'],
            ['raw' => 'Cereales', 'slug' => 'almacen', 'type' => 'manual'],
            ['raw' => 'Conservas', 'slug' => 'almacen', 'type' => 'manual'],
            ['raw' => 'Encurtidos', 'slug' => 'almacen', 'type' => 'manual'],
            ['raw' => 'Endulzantes', 'slug' => 'dieteticas', 'type' => 'manual'],
            ['raw' => 'Especias', 'slug' => 'almacen', 'type' => 'manual'],
            ['raw' => 'Golosinas', 'slug' => 'golosinas', 'type' => 'exact'],
            ['raw' => 'Harinas', 'slug' => 'almacen', 'type' => 'manual'],
            ['raw' => 'Infusiones', 'slug' => 'infusiones', 'type' => 'exact'],
            ['raw' => 'Leche En Polvo', 'slug' => 'lacteos', 'type' => 'manual'],
            ['raw' => 'Mermeladas Y Dulces', 'slug' => 'golosinas', 'type' => 'manual'],
            ['raw' => 'Panaderia', 'slug' => 'almacen', 'type' => 'manual', 'notes' => 'Considerar crear subcategoría'],
            ['raw' => 'Pasta Seca, Lista Y Rellenas', 'slug' => 'almacen', 'type' => 'manual'],
            ['raw' => 'Polvo Para Postres Y Reposteria', 'slug' => 'almacen', 'type' => 'manual', 'notes' => 'Considerar subcategoría repostería'],
            ['raw' => 'Productos Orgánicos', 'slug' => 'dieteticas', 'type' => 'manual'],
            ['raw' => 'Rebozador Y Pan Rallado', 'slug' => 'almacen', 'type' => 'manual'],
            ['raw' => 'Salsas Y Puré De Tomate', 'slug' => 'almacen', 'type' => 'manual'],
            ['raw' => 'Snacks', 'slug' => 'aperitivos', 'type' => 'manual'],
            ['raw' => 'Sopas, Caldos, Puré Y Saborizantes', 'slug' => 'almacen', 'type' => 'manual'],
            ['raw' => 'Suplementos Dietarios', 'slug' => 'dieteticas', 'type' => 'manual'],
        ];

        $imported = 0;
        $skipped = 0;

        foreach ($initialMappings as $mapping) {
            $normalized = Strings::normalize($mapping['raw']);

            $exists = DB::table('category_mappings')
                ->where('normalized', $normalized)
                ->whereNull('deleted_at')
                ->first();

            if ($exists && !$force) {
                StdOut::print("⊘ Skipped: {$mapping['raw']} (ya existe)\n");
                $skipped++;
                continue;
            }

            $category = DB::table('categories')
                ->where('slug', $mapping['slug'])
                ->whereNull('deleted_at')
                ->first();

            if (!$category) {
                StdOut::print("⚠ Warning: Categoría '{$mapping['slug']}' no encontrada para: {$mapping['raw']}\n");
                continue;
            }

            $catArray = is_array($category) ? $category : (array)$category;

            if ($exists && $force) {
                $existsArray = is_array($exists) ? $exists : (array)$exists;
                
                DB::table('category_mappings')
                    ->where('id', $existsArray['id'])
                    ->update([
                        'category_id' => $catArray['id'],
                        'category_slug' => $catArray['slug'],
                        'mapping_type' => $mapping['type'],
                        'notes' => $mapping['notes'] ?? 'Initial mapping',
                        'is_reviewed' => true,
                        'reviewed_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s')
                    ]);
                    
                StdOut::print("↻ Updated: {$mapping['raw']} → {$mapping['slug']}\n");
            } else {
                DB::table('category_mappings')->insert([
                    'raw_value' => $mapping['raw'],
                    'normalized' => $normalized,
                    'category_id' => $catArray['id'],
                    'category_slug' => $catArray['slug'],
                    'mapping_type' => $mapping['type'],
                    'notes' => $mapping['notes'] ?? 'Initial mapping',
                    'is_reviewed' => true,
                    'reviewed_at' => date('Y-m-d H:i:s'),
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);
                
                StdOut::print("✓ Imported: {$mapping['raw']} → {$mapping['slug']}\n");
            }

            $imported++;
        }

        StdOut::print("\n=== Resumen ===\n");
        StdOut::print("Importados: {$imported}\n");
        StdOut::print("Omitidos: {$skipped}\n");
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

    /**
     * Parsea opciones del comando
     */
    protected function parseOptions(array $args): array
    {
        $options = [];

        foreach ($args as $arg) {
            if (preg_match('/^--([^=:]+)[=:](.+)$/', $arg, $matches)) {
                $key = str_replace('-', '_', $matches[1]);
                $value = trim($matches[2], '"\'');
                $options[$key] = $value;
            } elseif (preg_match('/^--(.+)$/', $arg, $matches)) {
                $key = str_replace('-', '_', $matches[1]);
                $options[$key] = true;
            }
        }

        return $options;
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

  import_initial_mappings [--force]
    Importa los 23 mappings iniciales predefinidos

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