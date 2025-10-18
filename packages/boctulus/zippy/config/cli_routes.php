<?php

use Boctulus\Simplerest\Core\CliRouter;
use Boctulus\Zippy\Strategies\LLMMatchingStrategy;

/*
    Zippy Package CLI Routes
*/

// Grouped commands under 'zippy'
CliRouter::group('zippycart', function() {
    // OLLAMA
    CliRouter::group('ollama', function(){
        // php com zippycart ollama test_strategy
        CliRouter::command('test_strategy', function(){
            $models = LLMMatchingStrategy::getAvailableModels();
            dd($models, 'OLLAMA models');
        });

        // php com zippycart ollama hard_tests
        CliRouter::command('hard_tests', function(){
            // Textos hardcodeados para prueba
            $tests = [
                'Leche entera 1L marca tradicional',
                'Pan de molde integral 500g',
                'Cereal de maíz con chocolate 250g',
                'Pasta dental blanqueadora 75ml',
                'Jugo de naranja 1L sin azúcar',
                'Detergente líquido para ropa 3L',
            ];

            // Categorías hardcodeadas (formato que LLMMatchingStrategy espera: slug => [name, parent_slug?])
            $availableCategories = [
                'dairy.milk' => ['name' => 'Leche y derivados', 'parent_slug' => 'dairy'],
                'bakery.bread' => ['name' => 'Panadería', 'parent_slug' => 'bakery'],
                'breakfast.cereal' => ['name' => 'Cereales y desayuno', 'parent_slug' => 'breakfast'],
                'personalcare.toothpaste' => ['name' => 'Cuidado personal / Pasta dental', 'parent_slug' => 'personalcare'],
                'beverages.juice' => ['name' => 'Bebidas / Jugos', 'parent_slug' => 'beverages'],
                'home.detergent' => ['name' => 'Limpieza del hogar / Detergentes', 'parent_slug' => 'home'],
                // puedes añadir más categorías aquí si lo deseas
            ];

            // Verificar disponibilidad Ollama
            if (!\Boctulus\Zippy\Strategies\LLMMatchingStrategy::isAvailable()) {
                dd([
                    'error' => 'Ollama no disponible',
                    'hint' => 'Asegúrate de que Ollama esté corriendo en localhost:' . \Boctulus\LLMProviders\Providers\OllamaProvider::DEFAULT_PORT
                ], 'LLM availability');
            }

            // Instanciar estrategia (ajusta modelo/temperature/maxTokens/verbose aquí si quieres)
            $strategy = new \Boctulus\Zippy\Strategies\LLMMatchingStrategy(
                'qwen2.5:1.5b', // modelo
                0.2,           // temperatura
                500,           // max tokens
                true           // verbose: útil en debugging
            );

            $threshold = 0.70; // 70% threshold

            $results = [];

            foreach ($tests as $text) {
                $res = null;
                try {
                    $res = $strategy->match($text, $availableCategories, $threshold);
                } catch (\Throwable $e) {
                    $res = ['error' => 'exception', 'message' => $e->getMessage()];
                }

                // dd($res, 'LLM Response');

                // Normalizar salida para inspección: si hay match, extraer slug posible
                $matched_slug = null;
                $matched_name = null;
                $confidence = null;
                $reasoning = null;

                if (is_array($res) && isset($res['category'])) {
                    // recordar: parseResponse devuelve la data de category tal como en $availableCategories[$slug]
                    // pero no devuelve el slug directamente; intentamos inferirlo buscando la referencia en availableCategories
                    foreach ($availableCategories as $slug => $catData) {
                        // comparar por referencia de nombre (funciona con este ejemplo sencillo)
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
                    // en caso de null o error dejamos los campos como null o mensaje de error
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

            // Mostrar todo en una sola salida para inspección
            dd($results, 'Hardcoded classification tests (OLLAMA LLMMatchingStrategy)');
        });

    });

    // Importer commands
    CliRouter::group('importer', function() {
        CliRouter::command('import:coto', 'Boctulus\Zippy\Controllers\CotoProductImportController@import_zippy_csv');
        CliRouter::command('check_dupes:coto', 'Boctulus\Zippy\Controllers\CotoProductImportController@check_dupes');
        CliRouter::command('index:coto', 'Boctulus\Zippy\Controllers\CotoProductImportController@index');

        CliRouter::command('import:carrefour', 'Boctulus\Zippy\Controllers\CarrefourProductImportController@import_zippy_csv');
        CliRouter::command('check_dupes:carrefour', 'Boctulus\Zippy\Controllers\CarrefourProductImportController@check_dupes');
        CliRouter::command('index:carrefour', 'Boctulus\Zippy\Controllers\CarrefourProductImportController@index');
    });

    // CSV reader commands
    CliRouter::group('csv', function() {
        CliRouter::command('comercio', 'Boctulus\Zippy\Controllers\CSVTestController@read_csv_comercio');
        CliRouter::command('products', 'Boctulus\Zippy\Controllers\CSVTestController@read_csv_products');
        CliRouter::command('sucursales', 'Boctulus\Zippy\Controllers\CSVTestController@read_csv_sucursales');
    });

    // Users commands
    CliRouter::group('users', function() {
        CliRouter::command('index', 'Boctulus\Zippy\Controllers\FirebaseTestController@index');
        CliRouter::command('login', 'Boctulus\Zippy\Controllers\FirebaseTestController@login');
    });

    // Nuevos comandos de procesamiento de productos
    CliRouter::group('products', function() {
        CliRouter::command('process_categories', 'Boctulus\Zippy\Controllers\ProductCategoryController@process_products');
        CliRouter::command('process_uncategorized', 'Boctulus\Zippy\Controllers\ProductCategoryController@process_uncategorized');
    });

    // Category commands
    CliRouter::group('category', function() {
        // Existing
        CliRouter::command('list', 'Boctulus\Zippy\Controllers\CategoryController@list_categories'); 
        CliRouter::command('import', 'Boctulus\Zippy\Controllers\AdminTasksController@insertCategories');

        // New helpers in CategoryController
        CliRouter::command('create', 'Boctulus\Zippy\Controllers\CategoryController@create_category'); // --name --slug --parent
        CliRouter::command('create_mapping', 'Boctulus\Zippy\Controllers\CategoryController@create_mapping'); // --slug --raw --source
        CliRouter::command('resolve', 'Boctulus\Zippy\Controllers\CategoryController@test_resolve'); // --text
        CliRouter::command('resolve_product', 'Boctulus\Zippy\Controllers\CategoryController@test_resolve_product'); // --raw1 --raw2 --description

        // Category integrity checks and diagnostics
        CliRouter::command('find_missing_parents', 'Boctulus\Zippy\Controllers\CategoryMappingTestController@find_missing_parents');
        CliRouter::command('find_orphans', 'Boctulus\Zippy\Controllers\CategoryMappingTestController@find_orphan_categories');
        CliRouter::command('report_issues', 'Boctulus\Zippy\Controllers\CategoryMappingTestController@report_category_issues');
        CliRouter::command('generate_create_commands', 'Boctulus\Zippy\Controllers\CategoryMappingTestController@generate_create_commands');
    });
});