<?php

namespace Boctulus\Zippy\Controllers;

use Boctulus\Simplerest\Core\Libs\DB;
use Boctulus\Simplerest\Core\Controllers\Controller;
use Boctulus\Simplerest\Core\Request;
use Boctulus\Zippy\Libs\CategoryMapper;

class CategoryMappingTestController extends Controller
{
    function __construct()
    {
        parent::__construct();
    }

    /**
     * Encuentra categorías padre que se referencian pero no existen
     *
     * Busca en la tabla categories todos los parent_slug que se usan
     * pero que no existen como slug de ninguna categoría.
     *
     * Usage: php com zippycart category find_missing_parents
     */
    function find_missing_parents()
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
     * Retorna todas las categorías que tienen un parent_slug
     * pero ese padre no existe en la tabla categories.
     *
     * Usage: php com zippycart category find_orphans
     */
    function find_orphan_categories()
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
     * Muestra tanto las categorías huérfanas como los padres faltantes
     * para tener una vista completa del problema.
     *
     * Usage: php com zippycart category report_issues
     */
    function report_category_issues()
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
     * Analiza las categorías huérfanas y genera los comandos php com
     * necesarios para crear sus categorías padre.
     *
     * Usage: php com zippycart category generate_create_commands
     */
    function generate_create_commands()
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
                    'php com zippycart category create --name="%s" --slug=%s',
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
}
