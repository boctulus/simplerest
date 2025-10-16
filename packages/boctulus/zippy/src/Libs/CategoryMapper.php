<?php

namespace Boctulus\Zippy\Libs;

use Boctulus\Simplerest\Core\Libs\DB;
use Boctulus\Simplerest\Core\Libs\Strings;

/*
    https://chatgpt.com/c/68f07059-9de4-8324-b62d-9611fbe709c9
*/
class CategoryMapper
{
    public function __construct()
    {
        DB::setConnection('zippy');
    }

    /**
 * Guarda un alias entre un nombre alternativo de categoría y una categoría existente
 */
public function saveCategoryAlias(string $category_slug, string $raw_value, ?string $source = null): void
{
    $normalized = Strings::normalize($raw_value);

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
public function getCategoryAliases(string $category_slug): array
{
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
public function findCategory(string $category): ?array
{
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

}