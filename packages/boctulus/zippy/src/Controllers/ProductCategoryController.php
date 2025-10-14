<?php
// Crear este archivo: D:\laragon\www\simplerest\packages\boctulus\zippy\src\Controllers\ProductCategoryController.php

namespace Boctulus\Zippy\Controllers;

use Boctulus\Simplerest\Core\Controllers\Controller;
use Boctulus\Simplerest\Core\Libs\DB;
use Boctulus\Zippy\Libs\CategoryMapper;

class ProductCategoryController extends Controller
{
    /**
     * Procesa productos y actualiza sus categorías
     * 
     * Uso: php com zippy process_products --limit=100 --dry-run
     */
    public function process_products($request)
    {
        $limit = $request->getOption('limit', 100);
        $dryRun = $request->getOption('dry-run', false);
        $strategy = $request->getOption('strategy', null);

        DB::setConnection('zippy');
        
        // Configurar CategoryMapper
        CategoryMapper::configure([
            'default_strategy' => 'llm',
            'strategies_order' => ['llm', 'fuzzy'],
            'thresholds' => [
                'fuzzy' => 0.40,
                'llm' => 0.70,
            ]
        ]);

        // Obtener productos sin categorías procesadas o para reprocesar
        $query = "SELECT * FROM products WHERE 1=1";
        
        if ($limit) {
            $query .= " LIMIT " . (int)$limit;
        }

        $products = DB::select($query);
        $processed = 0;
        $errors = 0;

        foreach ($products as $product) {
            try {
                echo "[{$processed}/" . count($products) . "] Procesando producto ID/EAN: " . ($product->ean ?? $product->id) . "\n";
                
                // Resolver categorías usando CategoryMapper
                $categories = CategoryMapper::resolveProduct($product, true);
                
                if (!empty($categories)) {
                    echo "  → Categorías asignadas: " . implode(', ', $categories) . "\n";
                    
                    if (!$dryRun) {
                        // Actualizar el campo categories (JSON)
                        DB::update(
                            "UPDATE products SET categories = ? WHERE ean = ?",
                            [json_encode($categories), $product->ean ?? $product->id]
                        );
                    }
                } else {
                    echo "  → No se encontraron categorías\n";
                }
                
                $processed++;
                
            } catch (\Exception $e) {
                $errors++;
                echo "  → ERROR: " . $e->getMessage() . "\n";
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
     * Procesa solo productos sin categorías
     */
    public function process_uncategorized($request)
    {
        $limit = $request->getOption('limit', 100);
        $dryRun = $request->getOption('dry-run', false);

        DB::setConnection('zippy');
        
        CategoryMapper::configure([
            'default_strategy' => 'llm',
            'strategies_order' => ['llm', 'fuzzy']
        ]);

        // Productos sin categorías o con categorías vacías
        $query = "SELECT * FROM products 
                 WHERE categories IS NULL 
                 OR categories = '[]' 
                 OR categories = 'null'";
        
        if ($limit) {
            $query .= " LIMIT " . (int)$limit;
        }

        $products = DB::select($query);
        // ... resto del código similar a process_products
    }
}
