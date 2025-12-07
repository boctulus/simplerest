<?php

namespace Boctulus\Zippy\Libs;

use Boctulus\Simplerest\Core\Libs\DB;
use Boctulus\Simplerest\Core\Libs\Strings;

class CategoryUtils
{
    /**
     * Given a category id, returns breadcrumb string from root to the category.
     * Example: "A > A2 > A2-1 > A2-1a"
     *
     * @param mixed $cat_id Category id (string or int)
     * @param string $separator
     * @return string
     */
    public static function breadcrumb($cat_id, string $separator = ' > '): string
    {
        // Protect against malformed input
        if ($cat_id === null || $cat_id === '') {
            return '';
        }


        $visited = [];
        $path = [];
        $current = $cat_id;
        $maxDepth = 200; // safe-guard against cycles
        $depth = 0;


        while ($current !== null && $current !== '' && $depth < $maxDepth) {
            // Prevent infinite loops if data contains cycles
            if (in_array((string)$current, $visited, true)) {
                break;
            }


            $visited[] = (string)$current;


            $row = table('categories')
                ->select('id', 'name', 'parent_id')
                ->where('id', $current)
                ->first();


            if (!$row) {
                // If the current id is not found, stop traversing
                break;
            }


            // Prepend name to path later by collecting then reversing
            $path[] = $row['name'];


            // Move to parent
            $parent = $row['parent_id'];


            // If parent is null, empty string or 0 (depending on schema), we reached root
            if ($parent === null || $parent === '' || $parent === 0 || $parent === '0') {
                break;
            }


            $current = $parent;
            $depth++;
        }


        if (empty($path)) {
            return '';
        }


        $path = array_reverse($path);


        return implode($separator, $path);
    }

    /**
     * Fusiona múltiples categorías en una sola (la primera)
     *
     * Proceso:
     * 1. La primera categoría ($category_ids[0]) se convierte en la categoría definitiva
     * 2. Las demás categorías se eliminan (soft delete)
     * 3. Todas las referencias (por ID o slug) se actualizan a la categoría definitiva en:
     *    - category_mappings
     *    - brand_categories
     *    - products (campo JSON 'categories')
     *
     * @param string ...$category_ids IDs de categorías a fusionar (primera = definitiva)
     * @return array Resultado con estadísticas de la fusión
     * @throws \Exception Si menos de 2 categorías o si la primera no existe
     */
    public static function merge(string ...$category_ids): array
    {
        if (count($category_ids) < 2) {
            throw new \Exception("Se requieren al menos 2 categorías para fusionar");
        }

        DB::getConnection('zippy');

        $target_id = $category_ids[0];
        $source_ids = array_slice($category_ids, 1);

        // Verificar que la categoría destino existe
        $target = table('categories')
            ->where(['id' => $target_id])
            ->whereNull('deleted_at')
            ->first();

        if (!$target) {
            throw new \Exception("La categoría destino (ID: $target_id) no existe o está eliminada");
        }

        $target_slug = $target['slug'];

        // Obtener información de categorías origen
        $sources = table('categories')
            ->whereIn('id', $source_ids)
            ->whereNull('deleted_at')
            ->get();

        if (count($sources) !== count($source_ids)) {
            throw new \Exception("Una o más categorías origen no existen o están eliminadas");
        }

        $source_slugs = array_column($sources, 'slug');

        $stats = [
            'target_category' => $target['name'] . " (ID: $target_id, slug: $target_slug)",
            'merged_categories' => [],
            'category_mappings_updated' => 0,
            'brand_categories_updated' => 0,
            'products_updated' => 0,
            'categories_deleted' => 0,
        ];

        foreach ($sources as $source) {
            $stats['merged_categories'][] = $source['name'] . " (ID: {$source['id']}, slug: {$source['slug']})";
        }

        // PASO 1: Actualizar category_mappings
        // Actualizar referencias por category_slug
        foreach ($source_slugs as $source_slug) {
            $count = table('category_mappings')
                ->where(['category_slug' => $source_slug])
                ->whereNull('deleted_at')
                ->count();

            if ($count > 0) {
                DB::update(
                    "UPDATE category_mappings SET category_slug = ?, updated_at = NOW() WHERE category_slug = ? AND deleted_at IS NULL",
                    [$target_slug, $source_slug]
                );
                $stats['category_mappings_updated'] += $count;
            }
        }

        // Actualizar referencias por category_id
        foreach ($source_ids as $source_id) {
            DB::update(
                "UPDATE category_mappings SET category_id = ?, updated_at = NOW() WHERE category_id = ? AND deleted_at IS NULL",
                [$target_id, $source_id]
            );
        }

        // PASO 2: Actualizar brand_categories (usa category_id, no slug)
        foreach ($source_ids as $source_id) {
            $count = table('brand_categories')
                ->where(['category_id' => $source_id])
                ->whereNull('deleted_at')
                ->count();

            if ($count > 0) {
                DB::update(
                    "UPDATE brand_categories SET category_id = ?, updated_at = NOW() WHERE category_id = ? AND deleted_at IS NULL",
                    [$target_id, $source_id]
                );
                $stats['brand_categories_updated'] += $count;
            }
        }

        // PASO 3: Actualizar productos (campo JSON 'categories')
        // Obtener todos los productos que contengan alguno de los slugs a eliminar
        $all_slugs_to_replace = array_merge([$target_slug], $source_slugs);

        $products = table('products')
            ->whereNotNull('categories')
            ->where('categories', '!=', '[]')
            ->where('categories', '!=', 'null')
            ->get();

        $products_updated = 0;

        foreach ($products as $product) {
            $categories = json_decode($product['categories'], true);

            if (!is_array($categories) || empty($categories)) {
                continue;
            }

            $original_categories = $categories;
            $modified = false;

            // Reemplazar slugs de origen por slug destino
            foreach ($source_slugs as $source_slug) {
                if (in_array($source_slug, $categories)) {
                    // Reemplazar todas las ocurrencias
                    $categories = array_map(function($cat) use ($source_slug, $target_slug) {
                        return $cat === $source_slug ? $target_slug : $cat;
                    }, $categories);
                    $modified = true;
                }
            }

            // Eliminar duplicados (si quedó el target_slug duplicado)
            if ($modified) {
                $categories = array_values(array_unique($categories));

                // Solo actualizar si realmente cambió algo
                if ($categories !== $original_categories) {
                    table('products')
                        ->where(['id' => $product['id']])
                        ->update([
                            'categories' => json_encode($categories),
                            'updated_at' => date('Y-m-d H:i:s')
                        ]);

                    $products_updated++;
                }
            }
        }

        $stats['products_updated'] = $products_updated;

        // PASO 4: Soft delete de las categorías origen
        foreach ($source_ids as $source_id) {
            DB::update(
                "UPDATE categories SET deleted_at = NOW() WHERE id = ?",
                [$source_id]
            );
            $stats['categories_deleted']++;
        }

        return $stats;
    }
}
