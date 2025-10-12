<?php

namespace Boctulus\Zippy\Controllers;

use Boctulus\Simplerest\Core\Controllers\Controller;
use Boctulus\Simplerest\Core\Libs\Strings;
use Boctulus\Simplerest\Core\Libs\DB;
use Boctulus\Simplerest\Core\Traits\TimeExecutionTrait;
use Exception;

class AdminTasksController extends Controller
{
    use TimeExecutionTrait;

    /**
     * Connection name to use for DB operations.
     * @var string
     */
    protected $connection = 'zippy';

    function __construct()
    {
        parent::__construct();
    }

    /**
     * Index - will run the categories insertion.
     */
    function index()
    {
        $result = $this->insertCategories();
        dd($result);
    }

    /**
     * Insert the given categories into the categories table using the zippy connection.
     *
     * This method:
     *  - sets the DB connection
     *  - converts Firestore-like Timestamps to Y-m-d H:i:s
     *  - inserts each category (wrapped in try/catch per row)
     *  - closes the DB connection
     *
     * @return array Summary with inserted count and any errors.
     */
    protected function insertCategories()
    {
        // ensure connection property exists
        $connection = $this->connection ?? 'zippy';

        // Set DB connection as requested
        DB::setConnection($connection);

        // Source categories data (converted to PHP array).
        // Note: Timestamps converted using the provided _seconds values.
        $categories = [
            [
                'id' => '0SYTGDIKzIljOHSBQgAQ',
                'name' => 'Aperitivos',
                'slug' => 'aperitivos',
                'image_url' => null,
                'store_id' => null,
                'parent_id' => 'CtYqb4eWfeZjXszLsdI3',
                'parent_slug' => 'bebidas',
                'deleted_at' => null,
                'updated_at' => date('Y-m-d H:i:s', 1759313465),
                'created_at' => date('Y-m-d H:i:s', 1759313465),
            ],
            [
                'id' => 'CtYqb4eWfeZjXszLsdI3',
                'name' => 'Bebidas',
                'slug' => 'bebidas',
                'image_url' => null,
                'store_id' => null,
                'parent_id' => null,
                'parent_slug' => null,
                'deleted_at' => null,
                'updated_at' => date('Y-m-d H:i:s', 1759313437),
                'created_at' => date('Y-m-d H:i:s', 1759313437),
            ],
            [
                'id' => 'Js4ff8ZC2BGd7nOvWhQV',
                'name' => 'Verdulería',
                'slug' => 'verduleria',
                'image_url' => null,
                'store_id' => null,
                'parent_id' => null,
                'parent_slug' => null,
                'deleted_at' => null,
                'updated_at' => date('Y-m-d H:i:s', 1759313451),
                'created_at' => date('Y-m-d H:i:s', 1759313451),
            ],
            [
                'id' => 'L9ACKOG6bVE7OjKLJWN4',
                'name' => 'Galletitas',
                'slug' => 'galletitas',
                'image_url' => null,
                'store_id' => null,
                'parent_id' => null,
                'parent_slug' => null,
                'deleted_at' => null,
                'updated_at' => date('Y-m-d H:i:s', 1759313431),
                'created_at' => date('Y-m-d H:i:s', 1759313431),
            ],
            [
                'id' => 'MCTRQRVtJUQAbOnOEXYS',
                'name' => 'Perfumes',
                'slug' => 'perfumes',
                'image_url' => null,
                'store_id' => null,
                'parent_id' => null,
                'parent_slug' => null,
                'deleted_at' => null,
                'updated_at' => date('Y-m-d H:i:s', 1759313423),
                'created_at' => date('Y-m-d H:i:s', 1759313423),
            ],
            [
                'id' => 'MxPymMktwtD3erICZfF1',
                'name' => 'Electro',
                'slug' => 'electro',
                'image_url' => null,
                'store_id' => null,
                'parent_id' => 'Pnszc7sXaeXIpn4vK3nC',
                'parent_slug' => 'hogar-y-bazar',
                'deleted_at' => null,
                'updated_at' => date('Y-m-d H:i:s', 1759313467),
                'created_at' => date('Y-m-d H:i:s', 1759313467),
            ],
            [
                'id' => 'Pnszc7sXaeXIpn4vK3nC',
                'name' => 'Hogar y bazar',
                'slug' => 'hogar-y-bazar',
                'image_url' => null,
                'store_id' => null,
                'parent_id' => null,
                'parent_slug' => null,
                'deleted_at' => null,
                'updated_at' => date('Y-m-d H:i:s', 1759313443),
                'created_at' => date('Y-m-d H:i:s', 1759313443),
            ],
            [
                'id' => 'QJ4IL7yrSzPSdNpidQTh',
                'name' => 'Infusiones',
                'slug' => 'infusiones',
                'image_url' => null,
                'store_id' => null,
                'parent_id' => 'CtYqb4eWfeZjXszLsdI3',
                'parent_slug' => 'bebidas',
                'deleted_at' => null,
                'updated_at' => date('Y-m-d H:i:s', 1759313463),
                'created_at' => date('Y-m-d H:i:s', 1759313463),
            ],
            [
                'id' => 'TJXzDWsJMRfpPiXRvHOJ',
                'name' => 'Dietéticas',
                'slug' => 'dieteticas',
                'image_url' => null,
                'store_id' => null,
                'parent_id' => null,
                'parent_slug' => null,
                'deleted_at' => null,
                'updated_at' => date('Y-m-d H:i:s', 1759313447),
                'created_at' => date('Y-m-d H:i:s', 1759313447),
            ],
            [
                'id' => 'UjMgQMJo1zW3H4Ui68ll',
                'name' => 'Limpieza',
                'slug' => 'limpieza',
                'image_url' => null,
                'store_id' => null,
                'parent_id' => null,
                'parent_slug' => null,
                'deleted_at' => null,
                'updated_at' => date('Y-m-d H:i:s', 1759313428),
                'created_at' => date('Y-m-d H:i:s', 1759313428),
            ],
            [
                'id' => 'Wgi46GthyUh6EDFkSmGa',
                'name' => 'Otros',
                'slug' => 'otros',
                'image_url' => null,
                'store_id' => null,
                'parent_id' => null,
                'parent_slug' => null,
                'deleted_at' => null,
                'updated_at' => date('Y-m-d H:i:s', 1759313455),
                'created_at' => date('Y-m-d H:i:s', 1759313455),
            ],
            [
                'id' => 'ZpwymDL7eTonQnBHmwtb',
                'name' => 'Congelados',
                'slug' => 'congelados',
                'image_url' => null,
                'store_id' => null,
                'parent_id' => null,
                'parent_slug' => null,
                'deleted_at' => null,
                'updated_at' => date('Y-m-d H:i:s', 1759313445),
                'created_at' => date('Y-m-d H:i:s', 1759313445),
            ],
            [
                'id' => 'aIeulU5MIIEdghGjPE1h',
                'name' => 'Carnes',
                'slug' => 'carnes',
                'image_url' => null,
                'store_id' => null,
                'parent_id' => 'wgOfrUAAUQ42GEKZrV8H',
                'parent_slug' => 'frescos',
                'deleted_at' => null,
                'updated_at' => date('Y-m-d H:i:s', 1759313469),
                'created_at' => date('Y-m-d H:i:s', 1759313469),
            ],
            [
                'id' => 'daJvPmGBeEKeA0MyrN6T',
                'name' => 'Golosinas',
                'slug' => 'golosinas',
                'image_url' => null,
                'store_id' => null,
                'parent_id' => null,
                'parent_slug' => null,
                'deleted_at' => null,
                'updated_at' => date('Y-m-d H:i:s', 1759313433),
                'created_at' => date('Y-m-d H:i:s', 1759313433),
            ],
            [
                'id' => 'lkbcTdKfcMqdH4xwwsem',
                'name' => 'Higiene',
                'slug' => 'higiene',
                'image_url' => null,
                'store_id' => null,
                'parent_id' => null,
                'parent_slug' => null,
                'deleted_at' => null,
                'updated_at' => date('Y-m-d H:i:s', 1759313427),
                'created_at' => date('Y-m-d H:i:s', 1759313427),
            ],
            [
                'id' => 'lvAcsIo5dWOT8PGTTf3w',
                'name' => 'Alfajores',
                'slug' => 'alfajores',
                'image_url' => null,
                'store_id' => null,
                'parent_id' => 'daJvPmGBeEKeA0MyrN6T',
                'parent_slug' => 'golosinas',
                'deleted_at' => null,
                'updated_at' => date('Y-m-d H:i:s', 1759313461),
                'created_at' => date('Y-m-d H:i:s', 1759313461),
            ],
            [
                'id' => 'q6VVl4KevoGlM7CIA4x8',
                'name' => 'Ferretería',
                'slug' => 'ferreteria',
                'image_url' => null,
                'store_id' => null,
                'parent_id' => null,
                'parent_slug' => null,
                'deleted_at' => null,
                'updated_at' => date('Y-m-d H:i:s', 1759313441),
                'created_at' => date('Y-m-d H:i:s', 1759313441),
            ],
            [
                'id' => 'qATvTHpN9XbVOZj7MzG3',
                'name' => 'Juguetería',
                'slug' => 'jugueteria',
                'image_url' => null,
                'store_id' => null,
                'parent_id' => null,
                'parent_slug' => null,
                'deleted_at' => null,
                'updated_at' => date('Y-m-d H:i:s', 1759313439),
                'created_at' => date('Y-m-d H:i:s', 1759313439),
            ],
            [
                'id' => 'qauEVGIiOMlMNPsuEtVC',
                'name' => 'Almacén',
                'slug' => 'almacen',
                'image_url' => null,
                'store_id' => null,
                'parent_id' => null,
                'parent_slug' => null,
                'deleted_at' => null,
                'updated_at' => date('Y-m-d H:i:s', 1759313424),
                'created_at' => date('Y-m-d H:i:s', 1759313424),
            ],
            [
                'id' => 'tk5fXILfVy4RrD30dv1Z',
                'name' => 'Gastronómicos',
                'slug' => 'gastronomicos',
                'image_url' => null,
                'store_id' => null,
                'parent_id' => null,
                'parent_slug' => null,
                'deleted_at' => null,
                'updated_at' => date('Y-m-d H:i:s', 1759313453),
                'created_at' => date('Y-m-d H:i:s', 1759313453),
            ],
            [
                'id' => 'uOFGOZzUIhUjpW5P23bW',
                'name' => 'Bombones',
                'slug' => 'bombones',
                'image_url' => null,
                'store_id' => null,
                'parent_id' => 'daJvPmGBeEKeA0MyrN6T',
                'parent_slug' => 'golosinas',
                'deleted_at' => null,
                'updated_at' => date('Y-m-d H:i:s', 1759313459),
                'created_at' => date('Y-m-d H:i:s', 1759313459),
            ],
            [
                'id' => 'vCniBIdQPmHZXoDCOXlx',
                'name' => 'Embutidos',
                'slug' => 'embutidos',
                'image_url' => null,
                'store_id' => null,
                'parent_id' => 'wgOfrUAAUQ42GEKZrV8H',
                'parent_slug' => 'frescos',
                'deleted_at' => null,
                'updated_at' => date('Y-m-d H:i:s', 1759313467),
                'created_at' => date('Y-m-d H:i:s', 1759313467),
            ],
            [
                'id' => 'vHdGAZvePcrzQdKw1cKn',
                'name' => 'Librería',
                'slug' => 'libreria',
                'image_url' => null,
                'store_id' => null,
                'parent_id' => null,
                'parent_slug' => null,
                'deleted_at' => null,
                'updated_at' => date('Y-m-d H:i:s', 1759313435),
                'created_at' => date('Y-m-d H:i:s', 1759313435),
            ],
            [
                'id' => 'wgOfrUAAUQ42GEKZrV8H',
                'name' => 'Frescos',
                'slug' => 'frescos',
                'image_url' => null,
                'store_id' => null,
                'parent_id' => null,
                'parent_slug' => null,
                'deleted_at' => null,
                'updated_at' => date('Y-m-d H:i:s', 1759313449),
                'created_at' => date('Y-m-d H:i:s', 1759313449),
            ],
            [
                'id' => 'x9G5mkCdPSMGVz1s4hRm',
                'name' => 'Mascotas',
                'slug' => 'mascotas',
                'image_url' => null,
                'store_id' => null,
                'parent_id' => null,
                'parent_slug' => null,
                'deleted_at' => null,
                'updated_at' => date('Y-m-d H:i:s', 1759313457),
                'created_at' => date('Y-m-d H:i:s', 1759313457),
            ],
            [
                'id' => 'zaaKm9cHrNsiRokS5kls',
                'name' => 'Lácteos',
                'slug' => 'lacteos',
                'image_url' => null,
                'store_id' => null,
                'parent_id' => 'wgOfrUAAUQ42GEKZrV8H',
                'parent_slug' => 'frescos',
                'deleted_at' => null,
                'updated_at' => date('Y-m-d H:i:s', 1759313468),
                'created_at' => date('Y-m-d H:i:s', 1759313468),
            ],
        ];

        $inserted = 0;
        $errors = [];

        foreach ($categories as $row) {
            try {
                // Use table() and not DB:table()
                table('categories')->insert($row);
                $inserted++;
            } catch (Exception $e) {
                // Collect error but continue with next rows
                $errors[] = [
                    'id' => $row['id'],
                    'slug' => $row['slug'],
                    'message' => $e->getMessage(),
                ];
            }
        }

        // Close DB connection as requested
        DB::closeConnection($connection);

        return [
            'connection' => $connection,
            'inserted' => $inserted,
            'total' => count($categories),
            'errors' => $errors,
        ];
    }
}
