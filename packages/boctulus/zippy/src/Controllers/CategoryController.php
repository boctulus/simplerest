<?php

namespace Boctulus\Zippy\Controllers;

use Boctulus\Simplerest\Core\Libs\DB;
use Boctulus\Simplerest\Core\Controllers\Controller;

class CategoryController extends Controller
{
    function __construct()
    {
        parent::__construct();
    }

    function list_categories()
    {
        // Usar la conexión zippy
        DB::setConnection('zippy');

        // Obtener todas las categorías únicas del campo catego_raw1
        $categories = table('products')  // no usar DB::table() sino table()
            ->select('catego_raw1')
            ->whereNotNull('catego_raw1')
            ->where('catego_raw1', '!=', '')
            ->distinct()
            ->get();

        // Extraer solo los valores en un array simple
        $categoryList = array_map(function($item) {
            return $item['catego_raw1'];
        }, $categories);

        // Ordenar alfabéticamente
        sort($categoryList);

        // Cerrar la conexión
        DB::closeConnection();

        // Mostrar el resultado
        dd($categoryList, 'Categorías en catego_raw1');
    }
}
