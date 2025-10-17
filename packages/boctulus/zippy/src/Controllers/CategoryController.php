<?php

namespace Boctulus\Zippy\Controllers;

use Boctulus\Simplerest\Core\Libs\DB;
use Boctulus\Simplerest\Core\Controllers\Controller;
use Boctulus\Zippy\Libs\CategoryMapper;

class CategoryController extends Controller
{
    function __construct()
    {
        parent::__construct();
    }

    /**
     * List categories (existing)
     * Usage: php com zippycart category list
     */
    function list_categories()
    {
        DB::setConnection('zippy');

        $rows = DB::table('categories')->select(['id','slug','name','parent_slug'])->get();

        DB::closeConnection();

        dd($rows, 'Categories');
    }

    /**
     * Create a category
     * Usage: php com zippycart category create --name="Leche y derivados" --slug=leche --parent=food
     */
    function create_category($request)
    {
        DB::setConnection('zippy');

        $name = $request->getOption('name') ?? $request->getOption('n');
        $slug = $request->getOption('slug') ?? $request->getOption('s');
        $parent = $request->getOption('parent') ?? null;
        $image_url = $request->getOption('image_url') ?? null;
        $store_id = $request->getOption('store_id') ?? null;

        if (empty($name)) {
            dd(['error' => 'Missing --name'], 'Create category');
            return;
        }

        if (empty($slug)) {
            // normalizar nombre como slug si no se pasa slug
            $slug = \Boctulus\Simplerest\Core\Libs\Strings::normalize($name);
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
     * Create a mapping (alias)
     * Usage: php com zippycart category create_mapping --slug=leche --raw="Leche entera 1L" --source=mercado
     */
    function create_mapping($request)
    {
        $slug = $request->getOption('slug');
        $raw = $request->getOption('raw');
        $source = $request->getOption('source') ?? null;

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
     * Test resolve single raw slot
     * Usage: php com zippycart category resolve --text="Leche entera 1L marca tradicional"
     */
    function test_resolve($request)
    {
        $text = $request->getOption('text') ?? $request->getOption('t');

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
     * Test resolve product (multiple slots + description fallback)
     * Usage: php com zippycart category resolve_product --raw1="Leche entera" --raw2="" --description="Pack de 6 leches 1L"
     */
    function test_resolve_product($request)
    {
        $product = [
            'catego_raw1' => $request->getOption('raw1') ?? $request->getOption('r1') ?? null,
            'catego_raw2' => $request->getOption('raw2') ?? $request->getOption('r2') ?? null,
            'catego_raw3' => $request->getOption('raw3') ?? $request->getOption('r3') ?? null,
            'description' => $request->getOption('description') ?? $request->getOption('d') ?? null,
            'ean' => $request->getOption('ean') ?? null,
        ];

        CategoryMapper::configure([
            'strategies_order' => ['llm'],
            'thresholds' => ['llm' => 0.70]
        ]);

        $res = CategoryMapper::resolveProduct($product, true);

        dd($res, 'Resolve product result');
    }
}
