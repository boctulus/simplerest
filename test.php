<?php declare(strict_types=1);

use simplerest\core\libs\Strings;
use simplerest\core\libs\TemporaryExceptionHandler;

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

if (php_sapi_name() != "cli"){
	return; 
}

require_once __DIR__ . '/app.php';

////////////////////////////////////////////////

class CategoryHelper {
    /*
     * Transforms a multidimensional associative array of categories into a flat array
     * with each category having a name, slug, and parent_slug.
     *
     * @param array $categories The multidimensional associative array of categories.
     * @return array The flattened array of categories with name, slug, and parent_slug.
     * 
        Convierte un array como
      
        [
            'Suspensión' => [
                'Kit suspensión', 
                'Silentblock', 
                'Otros'
            ],
            'Carrocería/luces' => [
                'Snorkel', 
                'Aletines'
            ],
            'Llantas/neumáticos' => [
                'Llantas', 
                'Neumáticos', 
            ]
	    ];

        ... en uno como...

        Array
        (
            [0] => Array
                (
                    [name] => Suspensión
                    [slug] => suspension
                    [parent_slug] =>
                )

            [1] => Array
                (
                    [name] => Kit suspensión
                    [slug] => kit-suspension
                    [parent_slug] => suspension
                )

            [2] => Array
                (
                    [name] => Silentblock
                    [slug] => silentblock
                    [parent_slug] => suspension
                )
     */
    public static function categoryFlattener(array $categories): array {
        $flattened = [];

        foreach ($categories as $parent => $children) {
            // Add the parent category
            $parent_slug = Strings::slug(str_replace('/', '-', $parent));
            $flattened[] = [
                'name' => $parent,
                'slug' => $parent_slug,
                'parent_slug' => null
            ];

            // Add the child categories
            foreach ($children as $child) {
                $flattened[] = [
                    'name' => $child,
                    'slug' => Strings::slug(str_replace('/', '-', $child)),
                    'parent_slug' => $parent_slug
                ];
            }
        }

        return $flattened;
    }
}

// Instancia de la clase temporal
$handler = new TemporaryExceptionHandler();

try {
    // Example usage:
	$categories = [
		'Suspensión' => [
			'Kit suspensión', 
			'Silentblock', 
			'Otros'
		],
		'Carrocería/luces' => [
			'Snorkel', 
			'Aletines'
		],
		'Llantas/neumáticos' => [
			'Llantas', 
			'Neumáticos', 
		]
	];

	$flattenedCategories = CategoryHelper::categoryFlattener($categories);
	dd($flattenedCategories);

} catch (\Exception $e) {
    // Llama al método del trait
    $handler->exception_handler($e);
}