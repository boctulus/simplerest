<?php

namespace simplerest\controllers\demos;

use ReflectionClass;
use simplerest\core\controllers\Controller;
use simplerest\core\libs\DB;
use simplerest\core\libs\Factory;
use simplerest\core\libs\Paginator;
use simplerest\core\libs\Strings;
use simplerest\core\libs\Validator;
use simplerest\core\libs\VarDump;
use simplerest\core\Model;
use simplerest\core\Request;
use simplerest\core\Response;
use simplerest\models\az\AutomovilesModel;
use simplerest\models\az\BarModel;
use simplerest\models\az\ProductsModel;

class ModelController extends Controller
{
    
    function where_basico()
    {
        // ok
        $rows = DB::table('products')
            ->where(['size', '2L'])
            ->where(['cost', 100])
            ->get();

        dd(DB::getLog());

        // ok
        $rows = DB::table('products')
            ->where(['size' => '2L'])
            ->where(['cost' => 100])
            ->get();

        dd(DB::getLog());

        // No se recomienda (!)
        $rows = DB::table('products')
            ->where(['size' => '2L'])
            ->where(['cost', 100])
            ->get();

        dd(DB::getLog());
    }

    // function schema(){
    //     $m = (new ProductsModel());
    //     dd($m->getSchema::class);
    // }

    // function use_model(){
    //     $m = (new Model(true))
    //         ->table('products')  // <---------------- 
    //         ->select(['id', 'name', 'size'])
    //         ->where(['cost', 150, '>='])
    //         ->where(['id', 100, '<']);

    //     dd($m->get());

    //     // No hay Schema
    //     dd($m->getSchema::class);

    //     dd($m->dd());
    // }

    function get_bar0()
    {
        $m = (new Model(true))
            ->table('bar')
            ->where(['uuid', '0fefc2b1-f0d3-47aa-a875-5dbca85855f9']);

        dd($m->get());
    }

    function get_bar1()
    {
        $m = DB::table('bar')
            // ->assoc()
            ->where(['uuid', '0fefc2b1-f0d3-47aa-a875-5dbca85855f9']);

        dd($m->get());
    }

    function get_bar2()
    {
        $m = (new BarModel())
            ->connect()
            // ->assoc()
            ->where(['uuid', '0fefc2b1-f0d3-47aa-a875-5dbca85855f9']);

        dd($m->get());
    }
    
    
    // /*
    //     Se utiliza un modelo *sin* schema y sobre el cual no es posible hacer validaciones
    // */
    // function create_s(){
    //     $m = (new Model(true))
    //     ->table('super_cool_table');

    //     // No hay schema ?
    //     dd($m->getSchema::class);

    //     dd($m->create([
    //         'name' => 'SUPER',
    // 		'age' => 22,
    //     ]));
    // }

    /*
        Se utiliza un modelo *sin* schema y sobre el cual no es posible hacer validaciones
    */
    // function create_baz0(){
    //     $m = (new Model(true))
    //     ->table('baz');

    //     // No hay Schema
    //     dd($m->getSchema::class);

    //     dd($m->create([
    //         'id_baz' => 1800,
    //         'name' => 'BAZ',
    // 		'cost' => '100',
    //     ]));
    // }


    /*
        Se utiliza un modelo *sin* schema y sobre el cual no es posible hacer validaciones

        Tampoco funcionarán automáticamente los campos UUID
    */
    // function create_bar(){
    //     $m = (new Model(true))
    //     ->table('bar');

    //     // No hay Schema
    //     dd($m->getSchema::class);

    //     //$m->dontExec();

    //     dd($m->create([
    //         'name' => 'jkq',
    // 		'price' => '77.67',
    //     ]));

    //     //dd($m->dd());
    // }

    // function create_bar1(){
    //     $m = DB::table('bar');
    //     $m->setValidator(new Validator());

    //     // SI hay schema
    //     dd($m->getSchema::class);

    //     dd($m->create([
    //         'name' => 'gggggggggg',
    // 		'price' => '100',
    //     ]));
    // }

    function create_bar1()
    {
        $m = DB::table('bar');

        dd($m->create([
            'name' => 'gggggggggg',
            'price' => '100',
            'email' => 'a@b.com',
            'belongs_to' => 90
        ]));
    }


    function create_bar2()
    {
        $m = DB::table('bar');

        dd($m->create([
            'name' => 'gggggggggg',
            'price' => '100',
            'email' => 'a@b.com',
            'belongs_to' => 90,

            // JSON
            'attr' => [
                'precio_fraccion' => '4608',
                'principio_activo' => 'Alcanfor, Benzocaina, Mentol, Triclosán',
                'laboratorio' => 'Prater',
                'codigo_isp' => 'F-7345/16',
                'forma_farmaceutica' => 'Solución',
                'req_receta' => false,
            ]
        ]));
    }

    function getEnqueuedOperationsByCategoryId(int $cat_id){
        $sql = table('product_updates')
        ->whereRaw("JSON_CONTAINS(`categories`, '?')", [$cat_id])
        ->orderByRaw("id DESC")
        ->dd();

        dd($sql);
        //return DB::select($sql);
    }

    function get_products()
    {
        dd(DB::table('products')->get());
    }

    function get_products2()
    {
        dd(DB::table('products')->where(['size', '2L'])->get());
    }

    function create_p()
    {
        $m = DB::table('products');
        //$m->dontExec();

        $name = '';
        for ($i = 0; $i < 20; $i++)
            $name .= chr(rand(97, 122));

        $id = $m->create([
            'name' => $name,
            'description' => 'Esto es una prueba 77',
            'size' => '100L',
            'cost' => 66,
            'belongs_to' => 90,
            'digital_id' => 1
        ]);

        dd($m->debug(), 'SQL');

        return $id;
    }

    function create_baz($id = null)
    {

        $name = '';
        for ($i = 0; $i < 20; $i++)
            $name .= chr(rand(97, 122));

        $data = [
            'name' => $name,
            'cost' => 100
        ];

        if ($id != null) {
            $data['id'] = $id;
        }

        $id = DB::table('baz')->create($data);

        dd($id, 'las_inserted_id');
    }


    // implementada y funcionando en register() 
    function transaction()
    {
        DB::beginTransaction();

        try {
            $name = '';
            for ($i = 0; $i < 20; $i++)
                $name .= chr(rand(97, 122));

            $id = DB::table('products')->create([
                'name' => $name,
                'description' => 'bla bla bla',
                'size' => rand(1, 5) . 'L',
                'cost' => rand(0, 500),
                'belongs_to' => 90
            ]);

            //throw new \Exception("AAA"); 

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        } catch (\Throwable $e) {
            DB::rollback();
        }
    }

    // https://fideloper.com/laravel-database-transactions
    function transaction2()
    {
        DB::transaction(function () {
            $name = '';
            for ($i = 0; $i < 20; $i++)
                $name .= chr(rand(97, 122));

            $id = DB::table('products')->create([
                'name' => $name,
                'description' => 'Esto es una prueba',
                'size' => rand(1, 5) . 'L',
                'cost' => rand(0, 500),
                'belongs_to' => 90
            ]);

            throw new \Exception("AAA");
        });
    }

    function output_mutator()
    {
        $rows = DB::table('users')
            ->registerOutputMutator('username', function ($str) {
                return strtoupper($str);
            })
            ->get();

        dd($rows);
    }

    function output_mutator2()
    {
        $rows = DB::table('products')
            ->registerOutputMutator('size', function ($str) {
                return strtolower($str);
            })
            ->groupBy(['size'])
            ->having(['AVG(cost)', 150, '>='])
            ->select(['size'])
            ->selectRaw('AVG(cost)')
            ->get();

        dd($rows);
        dd(DB::getLog());
    }

    /*
        El problema de los campos ocultos es que rompen los transformers
        usar when() en su lugar

        https://laravel.com/docs/5.5/eloquent-resources
    */
    function transform()
    {
        //$this->is_admin = true;


        $t = new \simplerest\transformers\UsersTransformer();

        $rows = DB::table('users')
            ->registerTransformer($t, $this)
            ->get();

        dd($rows);
    }

    function transform_and_output_mutator()
    {
        $t = new \simplerest\transformers\UsersTransformer();

        $rows = DB::table('users')
            ->registerOutputMutator('username', function ($str) {
                return strtoupper($str);
            })
            ->registerTransformer($t)
            ->get();

        dd($rows);
    }

    function transform2()
    {
        $t = new \simplerest\transformers\ProductsTransformer();

        $rows = DB::table('products')
            ->where(['size' => '2L'])
            ->registerTransformer($t)
            ->get();

        dd($rows);
    }


    // 'SELECT id, name, cost FROM products WHERE (cost = 200) AND deleted_at IS NULL LIMIT 20, 10;'
    function g()
    {
        dd(DB::table('products')
            ->where(['cost', 200])
            ->limit(10)
            ->offset(20)
            ->get(['id', 'name', 'cost']));

        dd(DB::getLog());
    }

    function limit()
    {
        dd(DB::table('products')
            ->select(['id', 'name', 'cost'])
            ->offset(10)
            ->limit(5)
            ->get());

        dd(DB::getLog());
    }

    /*
        No esta pudiendo deshabilitarse el paginador -bug-
    */
    function limit0()
    {
        DB::getConnection('az');
        
        dd(DB::table('products')
            ->offset(20)
            ->select(['id', 'name', 'cost'])
            ->limit(10)
            ->setPaginator(false)
            ->get());

        dd(DB::getLog());

        dd(DB::table('products')->limit(10)->get());
        dd(DB::getLog());
    }

    ///
    function limite()
    {
        DB::table('products')->offset(20)->limit(10)->get();
        dd(DB::getLog());

        DB::table('products')->limit(10)->get();
        dd(DB::getLog());
    }

    function distinct()
    {
        dd(DB::table('products')->distinct()->get(['size']));

        // Or
        dd(DB::table('products')->distinct(['size'])->get());

        // Or
        dd(DB::table('products')->select(['size'])->distinct()->get());
    }

    function distinct1()
    {
        dd(DB::table('products')->select(['size', 'cost'])->distinct()->get());
    }

    function distinct2()
    {
        dd(DB::table('users')->distinct()->get());
    }

    function distinct3()
    {
        dd(DB::table('products')->distinct()->get());
    }

    function pluck()
    {
        $names = DB::table('products')->pluck('size');

        foreach ($names as $name) {
            dd($name);
        }
    }

    function pluck2($uid)
    {
        $perms = DB::table('user_sp_permissions')
            ->assoc()
            ->where(['user_id' => $uid])
            ->join('sp_permissions', 'user_sp_permissions.sp_permission_id', '=', 'sp_permissions.id')
            ->pluck('name');

        dd($perms);
    }

    function get_product($id)
    {
        // Include deleted items
        dd(DB::table('products')->find($id)->deleted()->dd());
    }

    function exists()
    {

        dd(DB::table('products')->where(['belongs_to' => 103])->exists());
        //dd(DB::getLog());

        dd(DB::table('products')->where([
            ['cost', 200, '<'],
            ['name', 'CocaCola']
        ])->exists());
        //dd(DB::  getLog());

        dd(DB::table('users')->where(['username' => 'boctulus'])->exists());
        //dd(DB::  getLog());
    }

    function first()
    {
        dd(DB::table('products')->where([
            ['cost', 100, '>='],
            ['cost', 150, '<'],
            ['belongs_to',  90]
        ])->select(['name', 'size', 'cost'])
            ->first());
    }

    function value()
    {
        dd(DB::table('products')->where([
            ['cost', 5000, '>=']
        ])->value('name'));

        dd(DB::getLog());
    }

    function value1()
    {
        dd(DB::table('products')->where([
            ['cost', 200, '>='],
            ['cost', 500, '<='],
            ['belongs_to',  90]
        ])->value('name'));

        dd(DB::getLog());
    }
    
    function value_plus_casting(){
        $city = 'Santiago';
        
        $gmt = DB::table('timezones')
        ->where([
            'city' => $city
        ])
        ->value('gmt', 'float');

        dd($gmt, 'GMT');
    }

    function oldest()
    {
        // oldest first
        dd(DB::table('products')->oldest()->first());
        dd(DB::getLog());
    }

    function newest()
    {
        // newest, first result
        dd(DB::table('products')->newest()->first());
        dd(DB::getLog());
    }

    function newest2()
    {
        dd(DB::table('products')->where([
            ['cost', 100, '>='],
            ['cost', 150, '<'],
            ['belongs_to',  90]
        ])->select(['name', 'size', 'cost', 'created_at'])
            ->newest()
            ->first());
    }



    // random or rand
    function random()
    {
        //dd(DB::table('products')->random()->get(['id', 'name']), 'ALL');
        dd(DB::table('products')->random()->select(['id', 'name'])->get(), 'ALL');

        dd(DB::table('products')->random()->limit(5)->get(['id', 'name']), 'N RESULTS');

        dd(DB::table('products')->random()->select(['id', 'name'])->first(), 'FIRST');
    }

    function count()
    {
        $c = DB::table('products')
            ->where(['belongs_to' => 90])
            ->count();

        dd($c);
    }

    function count1()
    {
        $c = DB::table('products')
            //->assoc()
            ->where(['belongs_to' => 90])
            ->count('*', 'count');

        dd($c);
        dd(DB::getLog());
    }

    function count1b()
    {
        // SELECT COUNT(*) FROM products WHERE cost >= 100 AND size = '1L' AND belongs_to = 90 AND deleted_at IS NULL 

        $res =  DB::table('products')
            ->where([['cost', 100, '>='], ['size', '1L'], ['belongs_to', 90]])
            ->count();

        dd($res);
        dd(DB::getLog());
    }

    // SELECT COUNT(DISTINCT( ...
    function count2()
    {
        // SELECT COUNT(DISTINCT description) FROM products WHERE cost >= 100 AND size = '1L' AND belongs_to = 90 AND deleted_at IS NULL  

        $res = DB::table('products')
            ->where([['cost', 100, '>='], ['size', '1L'], ['belongs_to', 90]])
            ->distinct()
            ->count('description');

        dd($res);
        dd(DB::getLog());
    }

    // SELECT COUNT(DISTINCT( ...
    function count2b()
    {
        /*
             SELECT COUNT(DISTINCT description) FROM products WHERE cost >= 100 AND size = '1L' AND belongs_to = 90 AND deleted_at IS NULL  
        */
        $res = DB::table('products')
            ->where([['cost', 100, '>='], ['size', '1L'], ['belongs_to', 90]])
            ->distinct()
            ->count('description', 'count');

        dd($res);
        dd(DB::getLog());
    }

    function count3()
    {
        $uid = 90;

        $count = (int) DB::table('user_roles')
            ->where(['user_id' => $uid])
            ->setFetchMode('COLUMN')
            ->count();

        dd($count);
        dd(DB::getLog());
    }

    function avg()
    {
        // SELECT AVG(cost) FROM products WHERE cost >= 100 AND size = '1L' AND belongs_to = 90 AND deleted_at IS NULL; 

        $res = DB::table('products')
            ->where([['cost', 100, '>='], ['size', '1L'], ['belongs_to', 90]])
            ->avg('cost', 'prom');

        dd($res);
    }

    function sum()
    {
        // SELECT SUM(cost) FROM products WHERE cost >= 100 AND size = '1L' AND belongs_to = 90 AND deleted_at IS NULL; 

        $res = DB::table('products')
            ->where([['cost', 100, '>='], ['size', '1L'], ['belongs_to', 90]])
            ->sum('cost', 'suma');

        dd($res);
        dd(DB::getLog());
    }

    function min()
    {
        $res = DB::table('products')
            ->where([['cost', 100, '>='], ['size', '1L'], ['belongs_to', 90]])
            ->min('cost', 'minimo');

        dd($res);
    }

    function max()
    {
        $res =  DB::table('products')
            ->where([['cost', 100, '>='], ['size', '1L'], ['belongs_to', 90]])
            ->max('cost', 'maximo');

        dd($res);
        dd(DB::getLog());
    }

    // select + max
    function max1()
    {
        /*
            SELECT 
            products.name, 
            MAX(cost) as maximo 
          
            FROM 
            products 
          
            WHERE 
            (
                products.cost >= 100 
                AND products.size = '1L' 
                AND products.belongs_to = 90
            ) 
            AND products.deleted_at IS NULL;
        */
        $res =  DB::table('products')
            ->where([['cost', 100, '>='], ['size', '1L'], ['belongs_to', 90]])
            ->select(['name'])
            ->max('cost', 'maximo');

        dd($res);
        dd(DB::getLog());
    }

    /*
        select + addSelect
    */
    function select()
    {
        dd(DB::table('products')
            ->random()
            ->select(['id', 'name'])
            ->addSelect('is_active')
            ->where(['is_active', true])
            ->first());

        dd(DB::getLog());
    }

    // RAW Select
    function select1r()
    {
        $m = DB::table('products')
            ->random()
            ->select(['id', 'name'])
            ->addSelect('is_active')
            ->addSelect('cost')
            ->selectRaw('cost * ? as cost_after_inc', [1.05])
            ->where(['is_active', true]);

        dd($m->first());
        dd($m->dd());
    }

    /*
        RAW select

        pluck() no se puede usar con selectRaw() si posee un "as" pero la forma de lograr lo mismo
        es seteando el "fetch mode" en "COLUMN"

        Investigar como funciona el pluck() de Larvel
        https://stackoverflow.com/a/40964361/980631
    */
    function select2()
    {
        $m = DB::table('products')->setFetchMode('COLUMN')
            ->selectRaw('cost * ? as cost_after_inc', [1.05]);

        dd($m->get());
        dd($m->dd());
    }

    function select3()
    {
        $m = DB::table('products')->setFetchMode('COLUMN')
            ->where([['cost', 100, '>='], ['size', '1L'], ['belongs_to', 90]])
            ->selectRaw('cost * ? as cost_after_inc', [1.05]);

        dd($m->get());
        dd($m->dd());
    }

    // DISTINCT -- ok
    function select30()
    {
        $m = DB::table('products')
            ->where([['cost', 100, '>=']])
            ->select(['name', 'cost'])
            ->distinct();

        dd($m->get());
        dd($m->dd());;
    }

    // DISTINCT
    function select3a()
    {
        $m = DB::table('products')
            ->where([['cost', 100, '>=']])
            ->selectRaw('cost * ? as cost_after_inc', [1.05])
            ->distinct();

        dd($m->get());
        dd($m->dd());
    }

    // DISTINCT + fetch mode = COLUMN
    function select3b()
    {
        $m = DB::table('products')
            ->setFetchMode('COLUMN')
            ->where([['cost', 100, '>=']])
            ->selectRaw('cost * ? as cost_after_inc', [1.05])
            ->distinct();

        dd($m->get());
        dd($m->dd());
    }

    // select + selectRaw
    function select4()
    {
        $rows  = DB::table('products')
            ->where([['cost', 100, '>='], ['size', '1L'], ['belongs_to', 90]])
            ->selectRaw('cost * ? as cost_after_inc', [1.05])
            ->addSelect('name')
            ->addSelect('cost')
            ->get();

        dd($rows);
        dd(DB::getLog());
    }

    // select + selectRaw
    function select4b()
    {
        $rows  = DB::table('products')
            ->where([['cost', 50, '>='], ['size', '1L'], ['belongs_to', 90]])
            ->selectRaw('cost * ? as cost_after_inc', [1.05])
            ->addSelect('name')
            ->addSelect('cost')
            ->distinct()
            ->get();

        dd($rows);
        dd(DB::getLog());
    }

    // selectRaw + fetch mode = COLUMN
    function select4c()
    {
        $rows  = DB::table('products')
            ->setFetchMode('COLUMN')
            ->where([['cost', 50, '>='], ['size', '1L'], ['belongs_to', 90]])
            ->selectRaw('cost * ? as cost_after_inc', [1.05])
            ->distinct()
            ->get();

        dd($rows);
        dd(DB::getLog());
    }

    /*
        La ventaja de usar select() - por sobre usar get() - es que se ejecuta antes que count() permitiendo combinar selección de campos con COUNT() 

        SELECT size, COUNT(*) FROM products GROUP BY size
    */
    function select_group_count()
    {
        dd(DB::table('products')->deleted()
            ->groupBy(['size'])
            ->select(['size'])
            ->count());

        dd(DB::getLog());
    }

    /*
        SELECT size, AVG(cost) FROM products GROUP BY size
    */
    function select_group_avg()
    {
        dd(DB::table('products')->deleted()
            ->groupBy(['size'])
            ->select(['size'])
            ->avg('cost'));

        dd(DB::getLog());
    }

    function filter_products1()
    {
        dd(DB::table('products')->deleted()->where([
            ['size', '2L']
        ])->get());
    }

    function filter_products2()
    {
        $m = DB::table('products')
            ->where([
                ['name', ['Vodka', 'Wisky', 'Tekila', 'CocaCola']], // IN 
                ['is_locked', 0],
                ['belongs_to', 90]
            ])
            ->whereNotNull('description');

        dd($m->get());
        var_dump(DB::getLog());
        //var_dump($m->dd());
    }

    // SELECT * FROM products WHERE name IN ('CocaCola', 'PesiLoca') OR cost IN (100, 200)  OR cost >= 550 AND deleted_at IS NULL
    function filter_products3()
    {
        dd(DB::table('products')->where([
            ['name', ['CocaCola', 'PesiLoca']],
            ['cost', 550, '>='],
            ['cost', [100, 200]]
        ], 'OR')->get());
    }

    function filter_products4()
    {
        dd(DB::table('products')->where([
            ['name', ['CocaCola', 'PesiLoca', 'Wisky', 'Vodka'], 'NOT IN']
        ])->get());
    }

    function filter_products5()
    {
        // implicit 'AND'
        dd(DB::table('products')->where([
            ['cost', 200, '<'],
            ['name', 'CocaCola']
        ])->get());

        dd(DB::getLog());
    }

    function filter_products6()
    {
        dd(DB::table('products')->where([
            ['cost', 200, '>='],
            ['cost', 270, '<=']
        ])->get());

        dd(DB::getLog());
    }

    // WHERE IN
    function where1()
    {
        dd(DB::table('products')->where(['size', ['0.5L', '3L'], 'IN'])->get());

        dd(DB::getLog());
    }

    // WHERE IN
    function where2()
    {
        dd(DB::table('products')->where(['size', ['0.5L', '3L']])->get());

        dd(DB::getLog());
    }

    // WHERE IN
    function where3()
    {
        dd(DB::table('products')
            //->dontQualify()
            ->whereIn('size', ['0.5L', '3L'])->get());

        dd(DB::getLog());
    }

    //WHERE NOT IN
    function where4()
    {
        $m = DB::table('products')
            //->dontQualify()
            ->where(['size', ['0.5L', '3L', '1L'], 'NOT IN']);
        $m->dd();

        dd($m->get());
        dd(DB::getLog());
    }

    //WHERE NOT IN
    function where5()
    {
        dd(DB::table('products')->whereNotIn('size', ['0.5L', '3L'])->get());
    }

    // WHERE NULL
    function where6()
    {
        dd(DB::table('products')->where(['workspace', null])->get());
    }

    // WHERE NULL
    function where7()
    {
        dd(DB::table('products')->whereNull('workspace')->dd());
    }

    // WHERE NOT NULL
    function where8()
    {
        dd(DB::table('products')->where(['workspace', null, 'IS NOT'])->get());
    }

    // WHERE NOT NULL
    function where9()
    {
        dd(DB::table('products')->whereNotNull('workspace')->get());
    }

    // WHERE BETWEEN
    function where10()
    {
        dd(DB::table('products')
            ->select(['name', 'cost'])
            ->whereBetween('cost', [100, 250])->get());
    }

    // WHERE BETWEEN
    function where11()
    {
        dd(DB::table('products')
            ->select(['name', 'cost'])
            ->whereNotBetween('cost', [100, 250])->get());
    }

    function where12()
    {
        dd(DB::table('products')
            ->find(145)->first());
    }

    function where_like()
    {
        DB::getConnection('az');

        $m = (new Model())
        ->table('products')
        ->where(['name', '%a%', 'LIKE'])
        ->select(['id', 'name']);

        dd($m->get());
        var_dump($m->dd());
    }

    function where_like_2()
    {
        DB::getConnection('az');

        $m = (new Model())
        ->table('products')
        ->whereLike('name', '%a%')
        ->select(['id', 'name']);

        dd($m->get());
        var_dump($m->dd());
    }

    function where13()
    {
        dd(DB::table('products')
            ->where(['cost', 150])
            ->value('name'));
    }

    /*
        SELECT  name, cost, id FROM products WHERE belongs_to = '90' AND (cost >= 100 AND cost < 500) AND description IS NOT NULL
    */
    function where14()
    {
        dd(DB::table('products')->deleted()
            ->select(['name', 'cost', 'id'])
            ->where(['belongs_to', 90])
            ->where([
                ['cost', 100, '>='],
                ['cost', 500, '<']
            ])
            ->whereNotNull('description')
            ->get());
    }


    /* 
        A OR B OR (C AND D)

       SELECT name, cost, id FROM products WHERE 
       belongs_to = 90 OR 
       name IN (\'CocaCola\', \'PesiLoca\') OR 
       (cost <= 550 AND cost >= 100)
    */
    function or_where()
    {
        $q = DB::table('products')->deleted()
            ->select(['name', 'cost', 'id'])
            ->where(['belongs_to', 90])
            ->orWhere(['name', ['CocaCola', 'PesiLoca']])
            ->orWhere([
                ['cost', 550, '<='],
                ['cost', 100, '>=']
            ]);

        dd($q->get());
        dd($q->dd());
    }

    // A OR (B AND C)
    function or_where2()
    {
        $q = DB::table('products')->deleted()
            ->select(['name', 'cost', 'id', 'description'])
            ->whereNotNull('description')
            ->orWhere([
                ['cost', 100, '>='],
                ['cost', 500, '<']
            ]);

        dd($q->get());
        dd($q->dd());
    }


    /*
        SELECT  name, cost, id FROM products WHERE 
        belongs_to = '90' AND 
        (
            name IN ('CocaCola', 'PesiLoca') OR 
            cost >= 550 OR 
            cost < 100
        ) AND 
        description IS NOT NULL
    */
    function where_or()
    {
        $q = DB::table('products')->deleted()
            ->select(['name', 'cost', 'id'])
            ->where(['belongs_to', 90])
            ->where([                           // <--- whereOr() === where([], 'OR')
                ['name', ['CocaCola', 'PesiLoca']],
                ['cost', 550, '>='],
                ['cost', 100, '<']
            ], 'OR')
            ->whereNotNull('description');

        dd($q->get());
        dd($q->dd());
    }

    /*
        SELECT  name, cost, id FROM products WHERE 
        belongs_to = '90' AND 
        (
            name IN ('CocaCola', 'PesiLoca') OR 
            cost >= 550 OR 
            cost < 100
        ) AND 
        description IS NOT NULL
    */
    function where_or1()
    {
        $q = DB::table('products')->deleted()
            ->select(['name', 'cost', 'id'])
            ->where(['belongs_to', 90])
            ->whereOr([
                ['name', ['CocaCola', 'PesiLoca']],
                ['cost', 550, '>='],
                ['cost', 100, '<']
            ])
            ->whereNotNull('description');

        dd($q->get());
        dd($q->dd());
    }

    /*
        SELECT  name, cost, id FROM products WHERE (belongs_to = '90' AND (name IN ('CocaCola', 'PesiLoca')  OR cost >= 550 OR cost < 100) AND description IS NOT NULL) AND deleted_at IS NULL OR  (cost >= 100 AND cost < 500)
    */
    function where_or2()
    {
        dd(DB::table('products')
            ->select(['id', 'name', 'cost', 'description'])
            ->where(['belongs_to', 90])
            ->where([
                ['name', ['CocaCola', 'PesiLoca']],
                ['cost', 550, '>='],
                ['cost', 100, '<']
            ], 'OR')
            ->whereNotNull('description')
            ->get());
    }

    // SELECT * FROM users WHERE (email = 'nano@g.c' OR  username = 'nano') AND deleted_at IS NULL
    function or_where3()
    {
        $email = 'nano@g.c';
        $username = 'nano';

        $rows = DB::table('users')->assoc()->unhide(['password'])
            ->where([
                'email' => $email,
                'username' => $username
            ], 'OR')
            ->setValidator((new Validator())->setRequired(false))
            ->get();

        dd($rows);
    }

    // SELECT * FROM users WHERE (email = 'nano@g.c' OR  username = 'nano') AND deleted_at IS NULL
    function or_where3b()
    {
        $email = 'nano@g.c';
        $username = 'nano';

        $rows = DB::table('users')->assoc()
            ->where(['email' => $email])
            ->orWhere(['username' => $username])
            ->setValidator((new Validator())->setRequired(false))
            ->first();

        dd($rows);
    }


    /*
    array (
        'op' => 'and,
        'q' => array (
            array (
                'op' => 'or',
                'q' => array (
                        array (
                            0 => ' cost > ?',
                            1 => ' id < ',
                        ),        

                        array (
                            0 => ' cost <= ?',
                            1 => ' description IS NOT ?',
                        )
                )
            ),

            array(
                0 => 'id = ?'
            )
        )
    )
    */

    /*
        SSELECT id, cost, size, description, belongs_to FROM products WHERE 
        
        (name LIKE '%a%') AND 
        (cost > 100 AND id < 50) AND 
        (
            active = 1 OR 
            (cost <= 100 AND description IS NOT NULL)
        ) 
        AND belongs_to > 150;

        O sea...

        [
            'AND' => [
                ['name', '%a%', 'LIKE'],
                [
                    'AND' => [
                        ['cost', 100, '>'],
                        ['id', 50, '<']
                    ]
                ],
                [
                    'OR' => [
                        [is_active, 1],
                        [
                            'AND' => [
                                ['cost', 100, '<='],
                                ['description', 'NOT NULL', 'IS']
                            ]
                        ]
                    ]
                ],
                ['belongs_to', 150, '>']		
            ]	
        ]
    */
    function where_adv()
    {
        $m = (new Model())
            ->table('products')

            ->where([
                ['cost', 100, '>'], // AND
                ['id', 50, '<']
            ])
            // AND
            ->whereRaw('name LIKE ?', ['%a%'])
            // AND
            ->group(function ($q) {
                $q->where(['is_active', 1])
                    // OR
                    ->orWhere([
                        ['cost', 100, '<='],
                        ['description', NULL, 'IS NOT']
                    ]);
            })
            // AND
            ->where(['belongs_to', 150, '>'])
            //->dontExec()
            ->select(['id', 'cost', 'size', 'description', 'belongs_to']);

        dd($m->get());
        var_dump($m->dd());
    }

    /*
        SELECT id, cost, size, description, belongs_to FROM products WHERE 
        
            (cost > 100 AND id < 50) OR <--- Ok
            (
                (name LIKE '%a') AND 
                (cost <= 100 AND description IS NOT NULL)
            ) AND 
            belongs_to > 150;
    */
    function where_adv2()
    {
        $m = (new Model())
            ->table('products')

            ->where([
                ['cost', 100, '>'], // AND
                ['id', 50, '<']
            ])
            // OR
            ->or(function ($q) {
                $q->whereRaw('name LIKE ?', ['%a'])
                    // AND  
                    ->where([
                        ['cost', 100, '<='],
                        ['description', NULL, 'IS NOT']
                    ]);
            })
            // AND
            ->where(['belongs_to', 150, '>'])

            ->select(['id', 'cost', 'size', 'description', 'belongs_to']);

        //dd($m->get()); 
        var_dump($m->dd());
    }

    /*
        Negador de wheres

        SELECT 
            products.id, 
            products.cost, 
            products.size, 
            products.description, 
            products.belongs_to, 
            cost * 1.05 as cost_after_inc 
            FROM 
            products 
            WHERE 
            (
                NOT (
                (
                    products.cost > 100 
                    AND products.id < 50
                ) 
                OR (
                    products.cost <= 100 
                    AND products.description IS NOT NULL
                )
                ) 
                AND products.belongs_to > 150
            ) 
            AND products.deleted_at IS NULL;
    */
    function not()
    {
        $m = DB::table('products')

            ->not(function ($q) {  // <-- group *
                $q->where([
                    ['cost', 100, '>'],
                    ['id', 50, '<']
                ])
                    // OR
                    ->orWhere([
                        ['cost', 100, '<='],
                        ['description', NULL, 'IS NOT']
                    ]);
            })
            // AND
            ->where(['belongs_to', 150, '>'])

            ->select(['id', 'cost', 'size', 'description', 'belongs_to'])
            ->selectRaw('cost * 1.05 as cost_after_inc');

        dd($m->get());
        var_dump($m->dd());
    }

    // ok
    function notor()
    {
        $m = DB::table('products')

            ->where(['belongs_to', 150, '>'])
            ->not(function ($q) {
                $q->where(['name', 'a$'])
                    ->or(function ($q) {
                        $q->where([
                            ['cost', 100, '<='],
                            ['description', NULL, 'IS NOT']
                        ]);
                    });
            })
            ->dontExec()
            ->where(['size', '1L', '>=']);

        //dd($m->get());
        dd($m->dd());
    }


    /*
        SELECT * FROM products WHERE 
        
        (
            belongs_to > 150 AND 
            NOT (
                    (name REGEXP 'a$') OR
                    ((cost <= 100 AND 
                        description IS NOT NULL
                    ))
                ) AND 
            size >= \'1L\'
        ) AND 
        deleted_at IS NULL;

    */
    function notor_whereraw()
    {
        $m = DB::table('products')

            ->where(['belongs_to', 150, '>'])
            ->not(function ($q) {
                $q->whereRegEx('name', 'a$')
                    ->or(function ($q) {
                        $q->where([
                            ['cost', 100, '<='],
                            ['description', NULL, 'IS NOT']
                        ]);
                    });
            })
            ->dontExec()
            ->where(['size', '1L', '>=']);

        //dd($m->get());
        dd($m->dd());
    }

    // ok
    function or_problematico()
    {
        $m = DB::table('products')

            ->whereRegEx('name', 'a$')
            ->or(function ($q) {
                $q->where(['cost', 100, '<=']);
            })
            ->deleted()
            ->dontExec();

        //dd($m->get());
        dd($m->dd());
    }

    // ok
    function or__problematico_b()
    {
        $m = DB::table('products')

            ->whereRegEx('name', 'a$')
            ->or(function ($q) {
                $q->group(function ($q) {
                    $q->where(['cost', 100, '<='])
                        ->orWhere(['id', 90]);
                });
            })
            ->deleted()
            ->dontExec();

        //dd($m->get());
        dd($m->dd());
    }

    // ok
    function or_otro20()
    {
        $m = DB::table('products')

            ->whereRegEx('name', 'a$')
            ->orWhere(['description', NULL, 'IS NOT'])

            ->deleted()
            ->dontExec();

        //dd($m->get());
        dd($m->dd());
    }

    function or_otro()
    {
        $m = DB::table('products')

            ->group(function ($q) {
                $q->whereRegEx('name', 'a$');
            })

            ->orWhere(['description', NULL, 'IS NOT'])

            ->deleted()
            ->dontExec();

        //dd($m->get());
        dd($m->dd());
    }

    function or_otro2()
    {
        $m = DB::table('products')

            ->group(function ($q) {
                $q->whereRegEx('name', 'a$');
            })

            ->or(function ($q) {
                $q->where(['cost', 100, '<=']);
            })


            ->deleted()
            ->dontExec();

        //dd($m->get());
        dd($m->dd());
    }

    function test000001()
    {
        $m = DB::table('products')

            ->group(function ($q) {
                $q->where(['description', NULL, 'IS NOT'])
                    ->where(['id', 90]);
            })

            ->or(function ($q) {
                $q->where(['cost', 100, '<=']);
            })
            ->deleted()
            ->dontExec();

        //dd($m->get());
        dd($m->dd());
    }

    /*
        SELECT * FROM products 
        
        WHERE 
        (
            products.belongs_to > 150 AND 
            NOT (
                    (products.cost <= 100 AND products.description IS NOT NULL) OR (name REGEXP 'a$')
                ) AND 
            products.size >= '1L'
        ) 
        
        AND products.deleted_at IS NULL;
    */
    function notor_whereraw2()
    {
        $m = DB::table('products')

            ->where(['belongs_to', 150, '>'])
            ->not(function ($q) {
                $q->where([
                    ['cost', 100, '<='],
                    ['description', NULL, 'IS NOT']
                ])
                    ->or(function ($q) {
                        $q->whereRegEx('name', 'a$');
                    });
            })
            //->dontExec()
            ->where(['size', '1L', '>=']);

        dd($m->get());
        var_dump($m->dd());
    }


    /*
        SELECT id, name, cost, size, description, belongs_to FROM products 
        WHERE 
        (
            (p.cost > 50 AND p.id <= 190) AND 
            (p.is_active = 1 OR (name LIKE '%a%')) 
            AND p.belongs_to > 1
        ) AND p.deleted_at IS NULL;"
    */
    function or_whereraw()
    {
        $m = DB::table('products', 'p')

            ->where([
                ['cost', 50, '>'], // AND
                ['id', 190, '<=']
            ])
            // AND
            ->group(function ($q) {
                $q->where(['is_active', 1])
                    // OR
                    ->orWhereRaw('name LIKE ?', ['%a%']);
            })
            // AND
            ->where(['belongs_to', 1, '>'])

            ->select(['id', 'name', 'cost', 'size', 'description', 'belongs_to']);

        dd($m->get());

        var_dump($m
            ->dd());
    }

    function where_raw_where_in()
    {
        $m = DB::table('products')

            ->group(function ($q) {  // <-- group *
                $q->whereIn('cost', [100, 200])
                    // OR
                    ->orWhere([
                        ['id', 150, '<='],
                        ['size', 'grande']
                    ]);
            });

        dd($m->get());
        dd($m->dd());
    }


    function where_raw_where_in2a()
    {
        $m = DB::table('products');
        $m->where([
            ['id', 150, '<='],
            ['size', 'grande']
        ]);

        $m
            ->dontExec()
            ->delete();

        $sql = $m->getLog();
        dd($sql, 'SQL');

        dd(DB::statement($sql), 'AFFECTED ROWS');
    }


    function where_raw_where_in2b()
    {
        $m = DB::table('products');
        $m->whereIn('cost', [100, 200]);

        $m
            //->dontExec()
            ->delete();

        $sql = $m->getLog();
        dd($sql, 'SQL');

        dd(DB::statement($sql), 'AFFECTED ROWS');
    }

    function where_raw_where_in2()
    {
        $m = DB::table('products')

            ->group(function ($q) {  // <-- group *
                $q->whereIn('cost', [100, 200])
                    // OR
                    ->orWhere([
                        ['id', 150, '<='],
                        ['size', 'grande']
                    ]);
            });

        dd($m
            ->dontExec()
            ->delete());

        $sql = $m->dd();
        dd($sql);

        dd(DB::statement($sql));
    }

    function when()
    {
        $lastname = 'Bozzo';

        $m = DB::table('users')
            ->when($lastname, function ($q) use ($lastname) {
                $q->where(['lastname', $lastname]);
            });

        dd($m->get());
        dd($m->dd());
    }

    function when2()
    {
        $sortBy = ['name' => 'ASC'];

        $m = DB::table('products')
            ->when($sortBy, function ($q) use ($sortBy) {
                $q->orderBy($sortBy);
            }, function ($q) {
                $q->orderBy(['id' => 'DESC']);
            });

        dd($m->get());
        dd($m->dd());
    }


    function where_col()
    {
        $m = (DB::table('users'))
            ->whereColumn('firstname', 'lastname', '=');

        dd($m->get());
        var_dump($m->dd());
    }


    // SELECT * FROM products WHERE ((cost < IF(size = "1L", 300, 100) AND size = '1L' ) AND belongs_to = 90) AND deleted_at IS NULL ORDER BY cost ASC
    function where_raw()
    {
        $m = DB::table('products')
            ->where(['belongs_to' => 90])
            ->whereRaw('cost < IF(size = "1L", ?, 100) AND size = ?', [300, '1L'])
            ->orderBy(['cost' => 'ASC']);

        dd($m->get());
        var_dump($m->dd());
    }

    /*
        SELECT * FROM products WHERE 

        (
            cost < IF(size = "1L", 300, 100) AND 
            size = '1L'
        ) AND 

        belongs_to = 90 AND 

        (
            size = '1L' OR (cost <= 550 AND cost >= 100)
        ) AND 

        deleted_at IS NULL 


        ORDER BY cost ASC;

    */
    function where_raw1()
    {
        $m = DB::table('products')

            ->where(['belongs_to', 90])

            ->group(function ($q) {
                $q->where(['size', '1L'])
                    ->orWhere([
                        ['cost', 550, '<='],
                        ['cost', 100, '>=']
                    ]);
            })

            // AND WHERE(...)
            ->whereRaw('cost < IF(size = "1L", ?, 100) AND size = ?', [300, '1L'])

            ->orderBy(['cost' => 'ASC']);

        dd($m->get());
        var_dump($m->dd());
    }

    function where_raw1b()
    {
        $m = (new Model())
            ->table('products')

            ->group(function ($q) {  // <-- group *
                $q->where([
                    ['cost', 100, '>'],
                    ['id', 50, '<']
                ])
                    // OR
                    ->orWhere([
                        ['cost', 100, '<='],
                        ['description', NULL, 'IS NOT']
                    ]);
            })

            // AND
            ->where(['belongs_to', 150, '>'])

            // AND WHERE (...)
            ->whereRaw('cost < IF(size = "1L", ?, 100) AND size = ?', [300, '1L'])

            ->select(['id', 'cost', 'size', 'description', 'belongs_to']);

        dd($m->get());
        var_dump($m->dd());
    }

    /*
        SELECT id, cost, size, description, belongs_to FROM products 

        WHERE (

            (cost < IF(size = "1L", 300, 100) AND size = '1L') OR 
            (products.cost <= 100 AND products.description IS NOT NULL)

        ) 

        AND products.belongs_to > 15
    */
    function where_raw1c()
    {
        $m = (new Model())
            ->table('products')

            ->group(function ($q) {  // <-- group *
                $q->whereRaw('cost < IF(size = "1L", ?, 100) AND size = ?', [300, '1L']) // falla porque no agrega luego un OR
                    // OR
                    ->orWhere([
                        ['cost', 100, '<='],
                        ['description', NULL, 'IS NOT']
                    ]);
            })

            // AND
            ->where(['belongs_to', 150, '>'])

            ->select(['id', 'cost', 'size', 'description', 'belongs_to']);

        dd($m->get());
        var_dump($m->dd());
    }


    function where_raw1x()
    {
        $m = (new Model())
            ->table('products')

            ->group(function ($q) {  // <-- group *
                $q->whereRaw('cost < IF(size = "1L", ?, 100)', [300])
                    // OR
                    ->orWhere([
                        ['cost', 100, '<=']
                    ]);
            });

        dd($m->get());
        var_dump($m->dd());
    }




    /*
        SELECT * FROM products WHERE EXISTS (SELECT 1 FROM users WHERE products.belongs_to = users.id AND users.lastname IS NOT NULL);
    */
    function where_raw2()
    {
        dd(DB::table('products')->deleted()
            ->whereRaw('EXISTS (SELECT 1 FROM users WHERE products.belongs_to = users.id AND users.lastname = ?  )', ['AB'])
            ->get());
    }

    function regex()
    {
        $m = DB::table('products')
            ->whereRegEx('name', 'a$');

        dd($m->get());
        dd($m->dd());
    }

    function regex2()
    {
        $m = DB::table('products')
            ->whereNotRegEx('name', 'a$');

        dd($m->get());
        dd($m->dd());
    }


    /*
        WHERE EXISTS

        SELECT * FROM products WHERE EXISTS (SELECT 1 FROM users WHERE products.belongs_to = users.id AND users.lastname = 'AB')
    */
    function where_exists()
    {
        $m = DB::table('products')->deleted()
            ->whereExists('(SELECT 1 FROM users WHERE products.belongs_to = users.id AND users.lastname = ?)', ['AB']);

        dd($m->get());
        dd($m->dd());
    }


    function test_where_date()
    {
        $facturas = DB::table('facturas')
            ->whereDate('created_at', '2021-12-29')
            ->get();

        dd($facturas);

        $facturas = DB::table('facturas')
            ->whereDate('created_at', '2021-12-29 19:42:08')
            ->get();

        dd($facturas);

        $testx   = DB::table('testx')
            ->whereDate('fecha', '2022-01-12')
            ->get();

        dd($testx);

        $testx   = DB::table('testx')
            ->whereDate('fecha', '2022-01-12 20:10:18')
            ->get();

        dd($testx);
    }

    function test_where_date2()
    {
        $facturas = DB::table('facturas')
            ->whereDate('created_at', '2021-12-29', '>')
            ->get();

        dd($facturas);
    }

    function test_where_date3()
    {
        $testx   = DB::table('testx')
            ->whereDate('fecha', '2022-01-12', '>')
            ->get();

        dd($testx);
    }

    /*
        SELECT * FROM products WHERE 1 = 1 AND deleted_at IS NULL ORDER BY cost ASC, id DESC LIMIT 1, 4
    */
    function order()
    {
        dd(DB::table('products')->orderBy(['cost' => 'ASC', 'id' => 'DESC'])->take(4)->offset(1)->get());

        dd(DB::table('products')->orderBy(['cost' => 'ASC'])->orderBy(['id' => 'DESC'])->take(4)->offset(1)->get());

        dd(DB::table('products')->orderBy(['cost' => 'ASC'])->take(4)->offset(1)->get(null, ['id' => 'DESC']));

        dd(DB::table('products')->orderBy(['cost' => 'ASC'])->orderBy(['id' => 'DESC'])->take(4)->offset(1)->get());

        dd(DB::table('products')->take(4)->offset(1)->get(null, ['cost' => 'ASC', 'id' => 'DESC']));
    }

    /*
        RAW
        
        SELECT * FROM products WHERE 1 = 1 AND deleted_at IS NULL ORDER BY is_locked + is_active ASC
    */
    function order2()
    {
        dd(DB::table('products')->orderByRaw('is_locked * is_active DESC')->get());
    }

    function grouping()
    {
        dd(DB::table('products')->where([
            ['cost', 100, '>=']
        ])->orderBy(['size' => 'DESC'])
            ->groupBy(['size'])
            ->select(['size'])
            //->take(5)
            //->offset(5)
            ->avg('cost'));
    }


    function where()
    {

        // Ok
        dd(DB::table('products')->where([
            ['cost', 200, '>='],
            ['cost', 270, '<='],
            ['belongs_to',  90]
        ])->get());


        /*    
        // No es posible mezclar arrays asociativos y no-asociativos 
        dd(DB::table('products')->where([ 
            ['cost', 200, '>='],
            ['cost', 270, '<='],
            ['belongs_to' =>  90]
        ])->get());
        */

        // Ok
        dd(DB::table('products')
            ->where([
                ['cost', 150, '>='],
                ['cost', 270, '<=']
            ])
            ->where(['belongs_to' =>  90])->get());
    }

    function having()
    {
        dd(
            DB::table('products')
                //->setStrictModeHaving(true)
                ->select(['size'])
                ->selectRaw('AVG(cost)')
                //->dontExec()
                ->groupBy(['size'])
                ->having(['AVG(cost)', 150, '>='])
                ->get()
        );

        dd(DB::getLog());
    }

    /*
		Array
		(
			[0] => stdClass Object
				(
					[c] => 3
					[name] => Agua
				)

			[1] => stdClass Object
				(
					[c] => 5
					[name] => Vodka
				)

		)
		
		SELECT COUNT(name) as c, name 
        FROM products 
        WHERE deleted_at IS NULL 
        GROUP BY name 
        HAVING c >= 3
	*/
    function having0()
    {
        DB::getConnection('az');

        $m = DB::table('products')
            //->dontExec()
            ->groupBy(['name'])
            ->having(['c', 3, '>='])
            ->select(['name'])
            ->selectRaw('COUNT(name) as c');

        dd($m->get());
        dd($m->dd());
        //dd(DB::getLog()); 
    }

    /*
		Array
		(
			[0] => stdClass Object
				(
					[c] => 5
					[name] => Agua 
				)

			[1] => stdClass Object
				(
					[c] => 3
					[name] => Ron
				)

			[2] => stdClass Object
				(
					[c] => 9
					[name] => Vodka
				)

		)

		SELECT COUNT(name) as c, name FROM products GROUP BY name HAVING c >= 3
	*/
    function havingx()
    {
        dd(DB::table('products')->deleted()
            //->dontExec()
            ->groupBy(['name'])
            ->having(['c', 3, '>='])
            ->select(['name'])
            ->selectRaw('COUNT(name) as c')
            ->get());

        dd(DB::getLog());
    }

    /*       
        En caso de tener múltiples condiciones se debe enviar un 
        array de arrays pero para una sola condición basta con enviar un simple array

        Cuando la condición es por igualdad (ejemplo: HAVING cost = 100), no es necesario
        enviar el operador "=" ya que es implícito y en este caso se puede usar un array asociativo:

            ->having(['cost' => 100])

        en vez de

            ->having(['cost', 100])

        En el caso de múltiples condiciones estas se concatenan implícitamente con "AND" excepto 
        se espcifique "OR" como segundo parámetro de having()    
    */

    /*
		SELECT cost, size FROM products WHERE deleted_at IS NULL GROUP BY cost,size HAVING cost = 100
	*/
    function having1()
    {
        $m = DB::table('products')
            //->dontQualify()
            ->groupBy(['cost', 'size'])
            ->having(['cost', 100])
            ->select(['cost', 'size']);

        //dd($m->get());
        dd($m->dd());;
    }

    function having1_ta()
    {
        $m = DB::table('products', 'p')
            //->dontQualify()
            ->groupBy(['cost', 'size'])
            ->having(['cost', 100])
            ->select(['cost', 'size']);

        dd($m->get());
        dd($m->dd());
    }

    // SELECT cost, size FROM products GROUP BY cost,size HAVING cost = 100
    function having1b()
    {
        dd(DB::table('products')->deleted()
            ->groupBy(['cost', 'size'])
            ->having(['cost', 100])
            ->get(['cost', 'size']));

        dd(DB::getLog());
    }

    function having1c()
    {
        dd(DB::table('products')->deleted()
            ->groupBy(['cost', 'size'])
            ->having(['cost', 100, '>='])
            ->get(['cost', 'size']));

        dd(DB::getLog());
    }

    /*
        HAVING ... OR ... OR ...

        SELECT  cost, size, belongs_to FROM products WHERE deleted_at IS NULL GROUP BY cost,size,belongs_to HAVING belongs_to = 90 AND (cost >= 100 OR size = '1L') ORDER BY size DESC
    */
    function having2()
    {
        dd(
            DB::table('products')
                ->groupBy(['cost', 'size', 'belongs_to'])
                ->having(['belongs_to', 90])
                ->having(
                    [
                        ['cost', 100, '>='],
                        ['size' => '1L']
                    ],
                    'OR'
                )
                ->orderBy(['size' => 'DESC'])
                ->select(['cost', 'size', 'belongs_to'])
                ->get()
        );

        dd(DB::getLog());
    }

    /*
        OR HAVING
    
        SELECT  cost, size, belongs_to FROM products WHERE deleted_at IS NULL GROUP BY cost,size,belongs_to HAVING  belongs_to = 90 OR  cost >= 100 OR  size = '1L'  ORDER BY size DESC
    */
    function having2b()
    {
        dd(DB::table('products')
            ->groupBy(['cost', 'size', 'belongs_to'])
            ->having(['belongs_to', 90])
            ->orHaving(['cost', 100, '>='])
            ->orHaving(['size' => '1L'])
            ->orderBy(['size' => 'DESC'])
            ->get(['cost', 'size', 'belongs_to']));

        dd(DB::getLog());
    }

    /*
        SELECT  cost, size, belongs_to FROM products WHERE deleted_at IS NULL GROUP BY cost,size,belongs_to HAVING  belongs_to = 90 OR  (cost >= 100 AND size = '1L')  ORDER BY size DESC
    */
    function having2c()
    {
        dd(DB::table('products')
            ->groupBy(['cost', 'size', 'belongs_to'])
            ->having(['belongs_to', 90])
            ->orHaving(
                [
                    ['cost', 100, '>='],
                    ['size' => '1L']
                ]
            )
            ->orderBy(['size' => 'DESC'])
            ->get(['cost', 'size', 'belongs_to']));

        dd(DB::getLog());
    }

    /*
        RAW HAVING
    */
    function having3()
    {
        dd(DB::table('products')
            ->selectRaw('SUM(cost) as total_cost')
            ->where(['size', '1L'])
            ->groupBy(['belongs_to'])
            ->havingRaw('SUM(cost) > ?', [500])
            ->limit(3)
            ->offset(1)
            ->get());

        dd(DB::getLog());
    }

    /*
        SELECT * FROM other_permissions as op 
        
        INNER JOIN folders ON op.folder_id=folders.id 
        INNER JOIN users ON folders.belongs_to=users.id 
        INNER JOIN user_roles ON users.id=user_roles.user_id 
        
        WHERE (guest = 1 AND r = 1) 
        ORDER BY users.id DESC;
    */
    function joins()
    {
        DB::getConnection();

        $m = (new Model())->table('folder_other_permissions', 'op')
            ->join('folders', 'op.folder_id', '=',  'folders.id')
            ->join('users', 'folders.belongs_to', '=', 'users.id')
            ->join('user_roles', 'users.id', '=', 'user_roles.user_id')
            ->where([
                ['guest', 1],
                ['r', 1]
            ])
            ->orderByRaw('users.id DESC');

        $sql = $m->dd();

        dd($sql);

        dd(
            DB::select($sql)
        );
    }

    function jx()
    {
        $m = DB::table('products')
            ->join('product_categories');

        dd($m->dd(true));
    }

    function j()
    {
        $m = DB::table('products')
            ->join('products_product_categories', 'products.id', '=',  'products_product_categories.product_id')
            ->join('product_comments', 'products.id', '=', 'product_comments.product_id');

        dd($m->dd(true));
    }

    function j_auto()
    {
        $m = DB::table('products')
            ->join('product_categories')
            ->leftJoin('product_comments');

        dd($m->get());
        dd($m->dd(true));
    }

    /*
        Auto-join with alias (as)
    */
    function j_auto1()
    {
        $m = DB::table('products')
            ->join('product_categories as pc');

        dd($m->get());
        dd($m->dd(true));
    }

    function j_auto1b()
    {
        $m = DB::table('products')
            ->dontExec()
            ->join('product_categories as product_categories')
            ->where(['product_categories.name_catego' => 'frutas']);

        dd(DB::select($m->dd()));
        dd($m->dd(true));
    }

    function j1()
    {
        $m = DB::table('books')
            ->join('book_reviews', 'book_reviews.book_id', '=',  'books.id')
            ->join('users as authors', 'authors.id', '=', 'books.author_id')
            ->join('users as editors', 'editors.id', '=', 'books.editor_id');

        dd($m->get());
        dd($m->dd());

        /*
        SELECT * FROM books 
            INNER JOIN book_reviews ON book_reviews.book_id = books.id 
            INNER JOIN users as authors ON authors.id = books.author_id 
            INNER JOIN users as editors ON editors.id = books.editor_id;
        */
    }

    function j1_auto()
    {

        $m = DB::table('books')
            ->join('book_reviews')
            ->join('users');

        dd($m->get());
        dd($m->dd(true));

        /*
            SELECT * FROM books 
            INNER JOIN book_reviews     ON book_reviews.book_id=books.id 
            INNER JOIN users as authors ON authors.id=books.author_id 
            INNER JOIN users as editors ON editors.id=books.editor_id;
        */
    }

    function j1_auto2()
    {
        DB::getConnection('db_flor');

        $m = DB::table('tbl_categoria_persona')
            ->join('tbl_usuario');

        dd($m->get());
        dd($m->dd(true));

        /*
            SELECT 
            * 
            FROM 
            tbl_categoria_persona 
            INNER JOIN tbl_usuario as __usu_intIdActualizador ON __usu_intIdActualizador.usu_intId = tbl_categoria_persona.usu_intIdActualizador 
            INNER JOIN tbl_usuario as __usu_intIdCreador ON __usu_intIdCreador.usu_intId = tbl_categoria_persona.usu_intIdCreador
        */
    }

    function j2()
    {
        $m = DB::table('users')
            ->join('user_sp_permissions', 'users.id', '=',  'user_sp_permissions.user_id')
            ->join('sp_permissions', 'sp_permissions.id', '=', 'user_sp_permissions.id')

            ->select(['sp_permissions.name as perm', 'username', 'is_active']);

        dd($m->get());
        dd($m->dd());
    }


    function j2a()
    {
        $m = DB::table('users')
            ->alias('u')
            ->join('user_sp_permissions', 'u.id', '=',  'user_sp_permissions.user_id')
            ->join('sp_permissions', 'sp_permissions.id', '=', 'user_sp_permissions.id')

            //->deleted()
            //->dontExec()
            ->select(['sp_permissions.name as perm', 'username', 'is_active']);

        dd($m->get());
        dd($m->dd());
    }


    function j2b()
    {
        $m = DB::table('users', 'u')
            ->join('user_sp_permissions', 'u.id', '=',  'user_sp_permissions.user_id')
            ->join('sp_permissions', 'sp_permissions.id', '=', 'user_sp_permissions.id')

            //->deleted()
            //->dontExec()
            ->select(['sp_permissions.name as perm', 'username', 'is_active']);

        dd($m->get());
        dd($m->dd(true));
    }

    /*
        Es importante notar que *no* debe hacerse el JOIN() con la tabla puente y la table relacionada
        por esta porque en tal caso la relación con la tabla puente quedaría duplicada.
    */
    function j2_auto()
    {
        $m = DB::table('users')
            //->join('user_sp_permissions');
            ->join('sp_permissions');

        $m->select(['sp_permissions.name as perm', 'username', 'is_active']);

        dd($m->get());
        dd($m->dd());
    }

    function join2c()
    {
        $rows = DB::table('users', 'u')
            ->join('products')
            ->join('roles')
            ->unhideAll()
            ->deleted()
            //->dontExec()
            ->get();

        dd($rows);
        dd(DB::getLog());
    }


    function join2d()
    {
        $rows = DB::table('products', 'p')
            ->join('users as u')
            ->unhideAll()
            ->qualify()
            //->deleted()
            //->dontExec()
            ->get();

        dd($rows);
        dd(DB::getLog());
    }


    function join2e()
    {
        $rows = DB::table('users', 'u')
            ->join('products as p')
            ->join('roles as r')
            ->unhideAll()
            ->qualify()
            //->deleted()
            //->dontExec()
            ->get();

        dd($rows);
        dd(DB::getLog());
    }

    function join2()
    {
        DB::getConnection('az');

        $rows = DB::table('users')
            ->join('products')
            ->join('roles')
            ->unhideAll()
            ->deleted()
            //->dontExec()
            ->get();

        dd($rows);
        dd(DB::getLog());
    }

    function join2b()
    {
        DB::getConnection('az');

        $rows = DB::table('roles')
            ->join('users')
            ->unhideAll()
            ->deleted()
            //->dontExec()
            ->get();

        dd($rows);
        dd(DB::getLog());
    }

    /*
        SELECT 
        * 
        FROM 
        tbl_cliente 
        INNER JOIN tbl_cliente_informacion_tributaria ON tbl_cliente_informacion_tributaria.cli_intIdCliente = tbl_cliente.cli_intId

        --ok
    */
    function j3_auto()
    {
        DB::setConnection('db_flor');

        $m = DB::table('tbl_cliente')
            ->join('tbl_cliente_informacion_tributaria');

        dd($m->dd(true));
    }

    function j4_auto1()
    {
        DB::getConnection('db_flor');

        $t1 = 'tbl_persona';
        $t2 = 'tbl_usuario';

        $m = DB::table($t1)
            ->join($t2);

        $sql = $m
            //->dontBind()
            //->dontExec()       
            ->dd(true);

        dd($m->get());
        dd($sql);
    }


    function j4_auto2()
    {
        DB::getConnection('db_flor');

        $t1 = 'tbl_sub_cuenta_contable';
        $t2 = 'tbl_producto';

        $m = DB::table($t1)
            ->join($t2);

        $sql = $m
            //->dontBind()
            //->dontExec()       
            ->dd(true);

        dd($m->get());
        dd($sql);
    }

    function j4_auto3()
    {
        DB::getConnection('db_flor');

        $t1 = 'tbl_sub_cuenta_contable';
        $t2 = 'tbl_cuenta_contable';

        $m = DB::table($t1)
            ->join($t2);

        $sql = $m
            //->dontBind()
            //->dontExec()       
            ->dd(true);

        dd($m->get());
        dd($sql);
    }

    // 'SELECT users.id, users.name, users.email, countries.name as country_name FROM users LEFT JOIN countries ON countries.id=users.country_id WHERE deleted_at IS NULL;'
    function leftjoin()
    {
        $users = DB::table('users')->select([
            "users.id",
            "users.name",
            "users.email",
            "countries.name as country_name"
        ])
            ->leftJoin("countries", "countries.id", "=", "users.country_id")
            ->dontExec()
            ->get();

        //dd($users);
        dd(DB::getLog());
    }

    /*
        Se generan ambiguedades sino especifican las tablas tanto en las cláuslas SELECT como el WHERE
    */
    function crossjoin()
    {
        $rows = DB::table('users')
            ->crossJoin('products')
            ->where(['users.id', 90])
            ->unhideAll()
            ->deleted()
            //->dontExec()
            ->get();

        dd($rows);
        dd(DB::getLog());
    }

    function naturaljoin()
    {
        $m = (new Model())->table('employee')
            ->naturalJoin('department')
            ->unhideAll()
            ->deleted()
            ->dontExec();

        dd($m->dd());
    }

    // SELECT COUNT(*) from users CROSS JOIN products CROSS JOIN roles;
    function crossjoin2()
    {
        DB::table('users')
            ->crossJoin('products')
            ->crossJoin('roles')
            ->unhideAll()
            ->deleted()
            ->dontExec()->get();

        dd(DB::getLog());
    }

    // SELECT * FROM users CROSS JOIN products CROSS JOIN roles WHERE users.id = 90;'
    function crossjoin2b()
    {
        $rows = DB::table('users')->crossJoin('products')->crossJoin('roles')
            ->where(['users.id', 90])
            ->unhideAll()
            ->deleted()
            //->dontExec()
            ->get();

        dd($rows);
        dd(DB::getLog());
    }


    // SELECT COUNT(*) from users CROSS JOIN products CROSS JOIN roles INNER JOIN user_sp_permissions ON users.id = user_sp_permissions.user_id;
    function crossjoin3()
    {
        $rows = DB::table('users')->crossJoin('products')->crossJoin('roles')
            ->join('user_sp_permissions', 'users.id', '=', 'user_sp_permissions.user_id')
            ->unhideAll()
            ->deleted()
            //->dontExec()
            ->get();

        dd($rows);
        dd(DB::getLog());
    }

    function j_test(){
        DB::getConnection('az');

        $autos = DB::table('automoviles')
        ->join('medios_transporte');
        
        dd(
            $autos->get()
        );
    }

    function j_test_2(){
        DB::getConnection('az');

        $autos = (new AutomovilesModel())
        ->join('medios_transporte', 'automoviles.id', '=', 'medios_transporte.id');
        
        dd(
            $autos->get()
        );
    }

    /*
        Para auto-joins si necesito los schemas
    */
    function j_test_3(){
        DB::getConnection('az');

        $autos = (new AutomovilesModel())
        ->join('medios_transporte');
        
        dd(
            $autos->get()
        );
    }

    /*

        SELECT ot.*, ld.distance FROM other_table AS ot 
        INNER JOIN location_distance ld ON (ld.fromLocid = ot.fromLocid OR ld.fromLocid = ot.toLocid) AND 
        (ld.toLocid = ot.fromLocid OR ld.toLocid = ot.fromLocid)

    */

    /*
        INNER JOIN location_distance ld1 ON ld1.fromLocid = ot.fromLocid AND ld1.toLocid = ot.toLocid
    */

    /*
        select ot.id,
        ot.fromlocid,
        ot.tolocid,
        ot.otherdata,
        coalesce(ld1.distance, ld2.distance) distance
        from other_table ot
        left join location_distance ld1
        on ld1.fromLocid = ot.toLocid
        and ld1.toLocid = ot.fromLocid 
        left join location_distance ld2
        on ld2.toLocid = ot.toLocid
        and ld2.fromLocid = ot.fromLocid 

        https://stackoverflow.com/questions/11702294/mysql-inner-join-with-or-condition#14824595
    */
    function get_nulls()
    {
        // Get products where workspace IS NULL
        dd(DB::table('products')->where(['workspace', null])->get());

        // Or
        dd(DB::table('products')->whereNull('workspace')->get());
    }

    /*
        Debug without exec the query
    */
    function dontExec()
    {
        DB::table('products')
            ->dontExec()
            ->where([
                ['cost', 150, '>='],
                ['cost', 270, '<=']
            ])
            ->where(['belongs_to' =>  90])->get();

        dd(DB::getLog());
    }

    /*
        Pretty response 
    */
    function get_users()
    {
        $array = DB::table('users')->orderBy(['id' => 'DESC'])->get();

        echo '<pre>';
        Factory::response()
            ->setPretty(true)
            ->send($array);
        echo '</pre>';
    }

    function get_userdata()
    {
        //dd(auth()->uid());

        $data = [];
        $data['email'] = 'xxx@g.com';

        DB::getDefaultConnection();

        $u = get_user_model_name();
        $m = new $u();

        $userdata = ($m)
            ->where([$u::$email => $data['email']])
            ->first();

        dd($userdata);
    }

    function get_userdata2()
    {
        //$uid = auth()->uid();

        $uid = 99;

        DB::getDefaultConnection();

        $u = get_user_model_name();
        $m = new $u();

        /*
            User data
        */
        $userdata = ($m)
            ->find($uid)
            ->first();

        dd($userdata);
        dd($m->dd());
    }

    function get_user($id)
    {
        $u = DB::table('users');
        $u->unhide(['password']);
        $u->hide(['id', 'username', 'confirmed_email', 'firstname', 'lastname', 'deleted_at', 'belongs_to']);
        $u->where(['id' => $id]);

        dd($u->get());
        dd($u->getLog());
    }

    function del_user($id)
    {
        $u = DB::table('users');
        $ok = (bool) $u->where(['id' => $id])->setSoftDelete(false)->delete();

        dd($ok);
    }


    function update_user($id)
    {
        $u = DB::table('users');

        $count = $u->where(['firstname' => 'HHH', 'lastname' => 'AAA', 'id' => 17])->update(['firstname' => 'Nico', 'lastname' => 'Buzzi', 'belongs_to' => 17]);

        dd($count);
    }

    function update_user2()
    {
        $firstname = '';
        for ($i = 0; $i < 20; $i++)
            $firstname .= chr(rand(97, 122));

        $lastname = strtoupper($firstname);

        $u = DB::table('users');

        $ok = $u->where([['email', 'nano@'], ['deleted_at', NULL]])
            ->update([
                'firstname' => $firstname,
                'lastname' => $lastname
            ]);

        dd($ok);
    }

    function update_users()
    {
        $u = DB::table('users');
        $count = $u->where([['lastname', ['AAA', 'Buzzi']]])->update(['firstname' => 'Nicos']);

        dd($count);
    }

    function test_touch_model()
    {
        DB::table('products')
            ->find(145)
            ->touch();

        $p = DB::table('products')
            ->find(145)
            ->first();

        dd($p);
    }

    function create_user($username, $email, $password, $firstname, $lastname)
    {
        for ($i = 0; $i < 20; $i++)
            $email = chr(rand(97, 122)) . $email;

        $u = DB::table('users');
        $u->fill(['email']);
        //$u->unfill(['password']);
        $id = $u->create(['username' => $username, 'email' => $email, 'password' => $password, 'firstname' => $firstname, 'lastname' => $lastname]);

        dd($id);
        dd(DB::getLog());
    }

    function fillables()
    {
        $m = DB::table('files');
        $affected = $m->where(['id' => 240])->update([
            "filename_as_stored" => "xxxxxxxxxxxxxxxxx.jpg"
        ]);

        dd($affected, 'Affected:');

        // Show result
        $rows = DB::table('files')->where(['id' => 240])->get();
        dd($rows);
    }

    function update_products()
    {
        $p = DB::table('products');
        $count = $p->where([['cost', 100, '<'], ['belongs_to', 90]])->update(['description' => 'x_x']);

        dd($count);
    }

    function test_find_or_fail()
    {
        DB::getConnection('az');
        
        dd(
            DB::table('products')
            ->findOrFail(1199)
            ->first() 
        );
    }

    function test_find_or()
    {
        DB::getConnection('az');
        
        dd(
            DB::table('products')
            ->findOr(11999, function($id) {
                die("No existe el registro con id = $id");
            })
            ->first() 
        );
    }

    function test_update_or_fail()
    {
        dd(
            DB::table('products')
            ->updateOrFail(['description' => 'abc'])
        );
    }

    function paginator(){
        header('Content-Type: application/json; charset=utf-8');

        $page_size = $_GET['size'] ?? 10;
        $page      = $_GET['page'] ?? 1;

        $offset = Paginator::calcOffset($page, $page_size);

        DB::getConnection('az');

        $rows = DB::table('products')
        ->take($page_size)
        ->offset($offset)
        ->get();

        $row_count = DB::table('products')->count();

        $paginator = Paginator::calc($page, $page_size, $row_count);
        $last_page = $paginator['totalPages'];

        return [
            "last_page" => $last_page, 
            "data" => $rows
        ];
    }

    

    /*
        Intento #1 de sub-consultas en el WHERE

        SELECT id, name, size, cost, belongs_to FROM products WHERE belongs_to IN (SELECT id FROM users WHERE password IS NULL);

    */
    function sub()
    {
        $st = DB::table('products')->deleted()
            ->select(['id', 'name', 'size', 'cost', 'belongs_to'])
            ->whereRaw('belongs_to IN (SELECT id FROM users WHERE password IS NULL)')
            ->get();

        dd(DB::getLog());
        dd($st);
    }

    /*
        Intento #2 de sub-consultas en el WHERE

        SELECT id, name, size, cost, belongs_to FROM products WHERE belongs_to IN (SELECT id FROM users WHERE password IS NULL);

    */
    function sub2()
    {
        $sub = DB::table('users')
            ->select(['id'])
            ->whereRaw('password IS NULL');

        $st = DB::table('products')->deleted()
            ->select(['id', 'name', 'size', 'cost', 'belongs_to'])
            ->whereRaw("belongs_to IN ({$sub->toSql()})")
            ->get();

        dd(DB::getLog());
        dd($st);
    }

    /*
        Subconsultas en el WHERE --ok

        SELECT id, name, size, cost, belongs_to FROM products WHERE (belongs_to IN (SELECT id FROM users WHERE (confirmed_email = 1) AND password < 100)) AND size = \'1L\';
    */
    function sub3()
    {
        $sub = DB::table('users')->deleted()
            ->select(['id'])
            ->whereRaw('confirmed_email = 1')
            ->where(['password', 100, '<']);

        $res = DB::table('products')->deleted()
            ->mergeBindings($sub)
            ->select(['id', 'name', 'size', 'cost', 'belongs_to'])
            ->where(['size', '1L'])
            ->whereRaw("belongs_to IN ({$sub->toSql()})")
            ->get();

        dd($res);
        dd(DB::getLog());
    }

    /*
        SELECT  id, name, size, cost, belongs_to FROM products WHERE belongs_to IN (SELECT  users.id FROM users  INNER JOIN user_roles ON users.id=user_roles.user_id WHERE confirmed_email = 1  AND password < 100 AND role_id = 2  )  AND size = '1L' ORDER BY id DESC

    */
    function sub3b()
    {
        $sub = DB::table('users')->deleted()
            ->selectRaw('users.id')
            ->join('user_roles', 'users.id', '=', 'user_roles.user_id')
            ->whereRaw('confirmed_email = 1')
            ->where(['password', 100, '<'])
            ->where(['role_id', 2]);

        $res = DB::table('products')->deleted()
            ->mergeBindings($sub)
            ->select(['id', 'name', 'size', 'cost', 'belongs_to'])
            ->where(['size', '1L'])
            ->whereRaw("belongs_to IN ({$sub->toSql()})")
            ->orderBy(['id' => 'desc'])
            ->get();

        dd($res);
        dd(DB::getLog());
    }

    function sub3c()
    {
        $sub = DB::table('users')->deleted()
            ->selectRaw('users.id')
            ->join('user_roles', 'users.id', '=', 'user_roles.user_id')
            ->whereRaw('confirmed_email = 1')
            ->where(['password', 100, '<'])
            ->where(['role_id', 3]);

        $res = DB::table('products')->deleted()
            ->mergeBindings($sub)
            ->select(['size'])
            ->whereRaw("belongs_to IN ({$sub->toSql()})")
            ->groupBy(['size'])
            ->avg('cost');

        dd($res);
        dd(DB::getLog());
    }


    /*
        RAW select

    */

    function sub4()
    {
        // SELECT COUNT(*) FROM (SELECT  name, size FROM products  GROUP BY size ) as sub 
        //
        // <-- en SQL no tiene sentido.

        try {
            $sub = DB::table('products')
                ->select(['name', 'size'])
                ->groupBy(['size']);

            $m = new Model(true);
            $res = $m->fromRaw("({$sub->toSql()}) as sub")->dontExec()
                ->count();

            dd($sub->toSql(), 'toSql()');
            dd($m->getLastPrecompiledQuery(), 'getLastPrecompiledQuery()');
            dd(DB::getLog(), 'getLog()');
            dd($res, 'count');
        } catch (\Exception $e) {
            dd($e->getMessage());
            dd($m->dd());
        }
    }

    // SELECT  COUNT(*) FROM (SELECT  id, name, size FROM products  WHERE (cost >= ?) AND deleted_at IS NULL) as sub
    function sub4a()
    {
        try {
            $sub = DB::table('products')
                ->select(['id', 'name', 'size'])
                ->where(['cost', 150, '>=']);

            $m = new Model(true);
            $res = $m->fromRaw("({$sub->toSql()}) as sub")
                ->mergeBindings($sub)
                ->count();

            dd($sub->toSql(), 'toSql()');
            dd($m->getLastPrecompiledQuery(), 'getLastPrecompiledQuery()');
            dd(DB::getLog(), 'getLog()');
            dd($res, 'count');
        } catch (\Exception $e) {
            dd($e->getMessage());
            dd($m->dd());
        }
    }


    function sub4b()
    {
        $sub = DB::table('products')->deleted()
            ->select(['size'])
            ->groupBy(['size']);

        $m = new Model(true);
        $res = $m->fromRaw("({$sub->toSql()}) as sub")->count();

        dd($res);
    }

    /*
        SELECT  COUNT(*) FROM (SELECT  size FROM products WHERE belongs_to = 90 GROUP BY size ) as sub WHERE 1 = 1
    */
    function sub4c()
    {
        $sub = DB::table('products')->deleted()
            ->select(['size'])
            ->where(['belongs_to', 90])
            ->groupBy(['size']);

        $main = new \simplerest\core\Model(true);
        $res = $main
            ->fromRaw("({$sub->toSql()}) as sub")
            ->mergeBindings($sub)
            ->count();

        dd($res);
        dd($main->getLastPrecompiledQuery());
    }

    /*
        FROM RAW

        SELECT  COUNT(*) FROM (SELECT  size FROM products WHERE belongs_to = 90 GROUP BY size ) as sub WHERE 1 = 1
    */
    function sub4d()
    {
        $sub = DB::table('products')->deleted()
            ->select(['size'])
            ->where(['belongs_to', 90])
            ->groupBy(['size']);

        $res = DB::table("({$sub->toSql()}) as sub")
            ->mergeBindings($sub)
            ->count();

        dd($res);
    }

    /*
        Subconsulta (rudimentaria) en el SELECT
    */
    function sub5()
    {
        $m = DB::table('products')->deleted()
            ->select(['name', 'cost'])
            ->selectRaw('cost - (SELECT MAX(cost) FROM products) as diferencia')
            ->where(['belongs_to', 90]);

        $res = $m->get();

        dd($res);
        dd($m->getLastPrecompiledQuery());
        dd(DB::getLog());
    }

    /*
        UNION

        SELECT id, name, description, belongs_to FROM products WHERE belongs_to = 90 UNION SELECT id, name, description, belongs_to FROM products WHERE belongs_to = 4 ORDER by id ASC LIMIT 5;
    */
    function union()
    {
        $uno = DB::table('products')->deleted()
            ->select(['id', 'name', 'description', 'belongs_to'])
            ->where(['belongs_to', 90]);

        $dos = DB::table('products')->deleted()
            ->select(['id', 'name', 'description', 'belongs_to'])
            ->where(['belongs_to', 4])
            ->union($uno)
            ->orderBy(['id' => 'ASC'])
            ->limit(5)
            ->get();

        dd($dos);
    }

    function union2()
    {
        $uno = DB::table('products')->deleted()
            ->select(['id', 'name', 'description', 'belongs_to'])
            ->where(['belongs_to', 90]);

        $m2  = DB::table('products')->deleted();
        $dos = $m2
            ->select(['id', 'name', 'description', 'belongs_to'])
            ->where(['belongs_to', 4])
            ->where(['cost', 200, '>='])
            ->union($uno)
            ->orderBy(['id' => 'ASC'])
            ->get();

        //dd(DB::getLog());
        //dd($m2->getLastPrecompiledQuery());
        //dd($dos);
    }

    /*
        UNION ALL
    */
    function union_all()
    {
        $uno = DB::table('products')
            ->deleted()
            //->dontQualify()
            ->select(['id', 'name', 'description', 'belongs_to'])
            ->where(['belongs_to', 90]);

        $dos = DB::table('products')
            ->deleted()
            //->dontQualify()
            ->select(['id', 'name', 'description', 'belongs_to'])
            ->where(['cost', 200, '>='])
            ->unionAll($uno)
            //->orderBy(['id' => 'ASC'])
            ->limit(5);

        dd($dos->get());
        dd($dos->dd());
    }

    function insert_messages()
    {
        function get_words($sentence, $count = 10)
        {
            preg_match("/(?:\w+(?:\W+|$)){0,$count}/", $sentence, $matches);
            return $matches[0];
        }

        $m = DB::table('messages');

        for ($i = 0; $i < 1500; $i++) {

            $name = '';
            for ($i = 0; $i < 10; $i++) {
                $name .= chr(rand(97, 122));
            }

            $email = '';
            for ($i = 0; $i < 20; $i++) {
                $email .= chr(rand(97, 122));
            }

            $email .= '@gmail.com';

            $title = file_get_contents('http://loripsum.net/api/1/short/plaintext/short');
            $title = get_words($title, 10);

            $content = file_get_contents('http://loripsum.net/api/1/long/plaintext/short');

            $phone = '0000000000';

            $m->create([
                'name' => $name,
                'email' => $email,
                'phone' => $phone,
                'subject' => $title,
                'content' => $content
            ]);
        }
    }

    // utiliza FPM, sin probar
    function some_test()
    {
        ignore_user_abort(true);
        fastcgi_finish_request();

        echo json_encode(['data' => 'Proceso terminado']);
        header('Connection: close');

        sleep(10);
        file_put_contents('output.txt', date('l jS \of F Y h:i:s A') . "\n", FILE_APPEND);
    }

    function json()
    {
        $id = DB::table('collections')->create([
            'entity' => 'messages',
            'refs' => json_encode([195, 196]),
            'belongs_to' => 332
        ]);

        Factory::response()->sendJson($id);
    }


    function get_env()
    {
        dd($_ENV['APP_NAME']);
        dd($_ENV['APP_URL']);
    }


    function test_get()
    {
        dd(DB::table('products')->first(), 'FIRST');
        dd(DB::getLog(), 'QUERY');
    }

    function test_get_raw()
    {
        $raw_sql = 'SELECT * FROM baz';

        $conn = DB::getConnection();

        $st = $conn->prepare($raw_sql);
        $st->execute();

        $result = $st->fetch(\PDO::FETCH_ASSOC);

        // additional casting
        $result['cost'] = (float) $result['cost'];

        echo '<pre>';
        var_export($result);
        echo '</pre>';
    }

    function get_role_permissions()
    {
        $acl = acl();
        
        dd($acl->hasResourcePermission('show_all', 'products', ['guest'], 'products'), 'Has a "guest" a show_all permission for "products"?');
        dd($acl->getRolePermissions(), 'Role perm.');
    }

    function get_con()
    {
        DB::setConnection('db2');
        $conn = DB::getConnection();

        $m = new ProductsModel($conn);
    }

    /*
        MySql: show status where `variable_name` = 'Threads_connected
        MySql: show processlist;
    */
    function test_active_connnections()
    {
        dd(DB::countConnections(), 'Number of is_active connections');

        DB::setConnection('db2');
        dd(DB::table('users')->count(), 'Users DB2:');

        DB::setConnection('db1');
        dd(DB::table('users')->count(), 'Users DB1');

        DB::setConnection('db2');
        dd(DB::table('users')->first(), 'Users DB2:');

        dd(DB::countConnections(), 'Number of is_active connections'); // 2 y no 3 ;)

        DB::closeConnection();
        dd(DB::countConnections(), 'Number of is_active connections'); // 1

        DB::closeAllConnections();
        dd(DB::countConnections(), 'Number of is_active connections'); // 0
    }

    function show_databases()
    {
        $res = DB::select('SHOW DATABASES', null, 'COLUMN');
        dd($res);
    }

    function test_db_select000()
    {
        DB::getConnection('az');

        $tb = 'files';
        $fields = DB::select("SHOW COLUMNS FROM $tb");

        dd($fields);
    }

    function read_table()
    {
        $tb = 'products';

        $fields = DB::select("SHOW COLUMNS FROM $tb");

        $field_names = [];
        $nullables = [];

        foreach ($fields as $field) {
            $field_names[] = $field['Field'];
            if ($field['Null']  == 'YES') {
                $nullables[] = $field['Field'];
            }
            if ($field['Extra'] == 'auto_increment') {
                $not_fillable[] = $field['Field'];
            }
        }

        dd($field_names);
    }

    /*
        Genera excepción con 
        
        PDO::ATTR_EMULATE_PREPARES] = false

    */
    function test000002()
    {
        $m = DB::table('products')
            ->where([
                ['name', ['Vodka', 'Wisky', 'Tekila', 'CocaCola']], // IN 
                ['is_locked', 0],
                ['belongs_to', 90]
            ])
            ->whereNotNull('description');

        dd($m->get());
        var_dump(DB::getLog());
        //var_dump($m->dd());
    }

    /*
        Genera excepción con 
        
        PDO::ATTR_EMULATE_PREPARES] = false

    */
    function test000003()
    {
        $m = DB::table('products')
            /*
        ->where([ 
            ['name', ['Vodka', 'Wisky', 'Tekila','CocaCola']], // IN 
            ['is_locked', 0],
            ['belongs_to', 90]
        ])
        */
            ->deleted()
            //->whereNotNull('description');
            ->where(['description', NULL]);

        dd($m->first());
        var_dump(DB::getLog());
        //var_dump($m->dd());
    }

    function call_sp()
    {
        $data = [
            'p_nombre' => 'Florencia P.',
            'p_email' => 'flor1@gmail.com',
            'p_usuario' => 'flor1',
            'p_password' => '1234',
            'p_basedatos' => 'db_flor'
        ];

        DB::setConnection('db_admin_dsi');
        $conn = DB::getConnection();

        $sql = 'CALL sp_crear_nuevo_usuario(?, ?, ?, ?, ?)';
        $stmt = $conn->prepare($sql);

        $stmt->bindParam(1, $data['p_nombre'],    \PDO::PARAM_STR, 50);
        $stmt->bindParam(2, $data['p_email'],     \PDO::PARAM_STR, 60);
        $stmt->bindParam(3, $data['p_usuario'],   \PDO::PARAM_STR, 50);
        $stmt->bindParam(4, $data['p_password'],  \PDO::PARAM_STR, 50);
        $stmt->bindParam(5, $data['p_basedatos'], \PDO::PARAM_STR, 20);

        $res = $stmt->execute();

        if (!$res) {
            throw new \Exception("No se pudo crear usuario {$data['p_usuario']}");
        }

        $sql = 'CALL sp_ejecucion_script(?)';
        $stmt = $conn->prepare($sql);

        $stmt->bindParam(1, $data['p_email'], \PDO::PARAM_STR, 60);
        $res = $stmt->execute();

        dd($res);
    }
    function autojoins()
    {
        DB::getConnection('db_flor');

        $rows = DB::table('tbl_estado_civil')
            ->join('tbl_usuario')
            ->join('tbl_estado')
            ->get();

        dd($rows);
    }
    function test_scopes(){
        DB::getConnection('az');  
        
        dd(
          DB::table('products')
          ->where(['id', 200, '>'])
          ->count(),
          'NORMAL'
        );
    
        dd(
          DB::table('products')
          ->where(['id', 200, '>'])
          ->costScope()
          ->count(),
          'SCOPE costScope'
        );
    }
    
    function test_json_search(){
        DB::getConnection('woo3');

        $sql = table('product_updates')
        ->whereRaw("JSON_CONTAINS(`categories`, '?')", [17])
        ->dd();

        dd($sql, 'SQL');

        dd(
            DB::select($sql)
        );
    }

    /*
        Objetivo:

       [
        'AND' => [
            [
                'OR' => [
                    'OR' => [
                        ['cost', 100, '<='],
                        ['description', NULL, 'IS NOT']
                    ],

                    ['name', '%Pablo', 'LIKE']
                ]
            ],

            ['stars', 5]
        ]    
    ]

    */
    function test_555(){
        $m = table('my_tb');

        // $r = $m
        // ->group(function($q){
        //     $q->whereOr([
        //         array (
        //             0 => 'cost',
        //             1 => 100,
        //             2 => '<=',
        //         ),
        //         array (
        //             0 => 'description',
        //             1 => 'NOT NULL',
        //             2 => 'IS',
        //         )
        //   ])
        //     ->orWhere(array(
        //         0 => 'name',
        //         1 => '%Pablo',
        //         2 => 'LIKE',
        //     ));
        // })
        // ->where(['stars', 5]);

        // <-------------------- seguir intentando lograr el mismo resultado

        // // prueba
        $r = $m

        ->group(function ($q) {$q->group(function ($q) {$q->group(function ($q) {$q->whereOr(array (
            0 =>
            array (
              0 => 'cost',
              1 => 100,
              2 => '<=',
            ),
            1 =>
            array (
              0 => 'description',
              1 => NULL,
              2 => 'IS NOT',
            ),
          ));
          $q->orWhere(array (
            0 => 'name',
            1 => '%Pablo',
            2 => 'LIKE',
          ));
          });
          });
          $q->where(array (
            0 => 'stars',
            1 => 5,
          ));
          });
        
        dd(
            $r->dd()
        );
    }


    function test_where_ay(){
        $ay = [
            'AND' => [
                [
                    'OR' => [
                        'AND' => [
                            ['cost', 100, '<='],
                            ['description', NULL, 'IS NOT']
                        ],
    
                        ['name', '%Pablo', 'LIKE']
                    ]
                ],
    
                ['belongs_to', 5]
            ]    
        ];
        

        $q = Model::where_array($ay);

        $code = Strings::beforeLast("table('products')$q", ';') . '->dd();';

        dd(pre($code));

        DB::getConnection("az");

        dd(
            eval("return $code")
        );
    }


    function test_soft_delete(){
        DB::table('products')
        ->deleted()
        ->groupBy(['cost', 'size', 'belongs_to'])
        ->having(['belongs_to', 90])
        ->or(function ($q) {
            $q->having(['cost', 100, '>='])
            ->having(['size' => '1L']);
        })
        ->orderBy(['size' => 'DESC'])
        ->dontExec()
        ->get(['cost', 'size', 'belongs_to']);

        dd(
            DB::getLog()
        );
    }


    // OK
    function testFindTableByAlias() {
        // Conexion a base de datos en particular
        DB::getConnection('edu');

        // A encontrar
        $alias = 'professor';
        // $alias = 'category';
        // $alias = 'student';
        // $alias = 'nonexistent';

        // Crear una instancia al Modelo para la tabla 'courses'
        $model = DB::table('courses');
        
        // Usamos Reflection para acceder al método protegido
        $reflection = new ReflectionClass($model);
        $method = $reflection->getMethod('findTableByAlias');
        $method->setAccessible(true);
        
        // Llamar al método
        $result = $method->invoke($model, $alias);
        
        dd($result);
    }


    // OK
    function test_with_0()
    {
        DB::getConnection('edu');

        $rows = DB::table('courses')
        // ->qualify()
        ->connectTo(['categories', 'tags'])                
        ->get();

        dd($rows);
    }

    /*  
        Trae tablas relacionadas --ok

        TO-DO:

        - Poder cualificar las columnas a traer de las tablas relacionadas

        Ej:

        ->where(['category.name', 'Mathematics']) 

        pero tambien:

        ->where(['professor.name', 'Bob Smith']) 

        - Curiosamente, el dd() sobre el objeto $m no muestra las tablas relacionadas

        DB::table('courses')
        ->qualify()
        ->where(['title', 'Calculus I'])            
        ->connectTo(['categories', 'users', 'tags']) 
        ->dd();

        <-- comprender porque y documentarlo
    */
    function test_with()
    {
        DB::getConnection('edu');

        $rows = DB::table('courses')
            ->where(['title', 'Calculus I'])            
            ->connectTo(['categories', 'users', 'tags']) 
            ->get();

        dd($rows);

        dd(
            DB::getLog()
        );
    }    

    function test_where_with_join()
    {
        DB::getConnection('edu');

        $rows = DB::table('courses')
		->where(['title', 'Calculus I'])            
		->joinTo(['categories', 'users', 'tags']) 
		->get();

        dd($rows);
    }

    function test_with_1()
    {
        DB::getConnection('edu');

        $m = DB::table('courses');

        $m
        ->connectTo(['categories', 'users', 'tags'])
        ->where(['categories.name', 'Mathematics'])
        ->where(['users.name', 'Bob Smith'])
        //->dontExec()
        ->get();

        $sql = DB::getLog();

        dd(
            $sql
        );

        dd(
            DB::select($sql)
        );
    }    

    function test_with_1b()
    {
        DB::getConnection('edu');

        $m = DB::table('courses');

        $rows = $m
        ->connectTo(['categories', 'users', 'tags'])
        ->where(['categories.name', 'Mathematics'])
        ->where(['users.name', 'Bob Smith'])
        //->dontExec()
        ->get();

        // $sql = DB::getLog();

        // dd(
        //     $sql
        // );

        dd(
            $rows
        );
    }   

    function test_with_2_manual_join()
    {
        DB::getConnection('edu');

        $sql = DB::table('courses')
        ->join('categories')
        ->where(['categories.name', 'Mathematics'])
        ->dd();

        /*
            --| MAKING JOIN
            Array
            (
                [table] => categories
                [on1] =>
                [op] => =
                [on2] =>
                [type] => INNER JOIN
            )
        */

        // SELECT * FROM `courses` INNER JOIN categories ON categories.id=courses.category_id WHERE categories.name = 'Mathematics'
        dd($sql);

        // ok
        dd(DB::select($sql));
    }    

    function test_with_2()
    {
        DB::getConnection('edu');

        $sql = DB::table('courses')            
        ->connectTo(['categories', 'users', 'tags']) 
        // ->join('categories')
        ->where(['categories.name', 'Mathematics'])              
        ->dd();

        dd($sql);

        dd(DB::select($sql));
    }    

    /*
        Para buscar un "professor" llamado "Bob Smith| que enseñe "Mathematics"
    */
    function test_with_3()
    {
        DB::getConnection('edu');

        $m = DB::table('courses');

        $rows = $m
        ->connectTo(['categories', 'users', 'tags'])
        ->where(['categories.name', 'Mathematics'])
        // ->where(['users.name', 'Bob Smith'])
        // ->where(['users.role', 'professor'])
        //->dontExec()

        ->get();
        // ->first();

        dd(
            $rows
        );
    }    


    ////////////////////////// INSERTS ///////////////////////////

    function test_insert_struct(){
        DB::getConnection('edu');

        $data = [
            'title' => 'Mathematics for Phisicists',
            'categories' => [
                'name' => 'Mathematics'
            ],
            'users' => [
                ['name' => 'Bob Smith', 'role' => 'professor'],
                ['name' => 'Diana White', 'role' => 'student']
            ]
        ];

        VarDump::showTrace();
        
        $course_id = DB::table('courses')
            ->connectTo(['categories', 'users'])
            ->dontExec() //
            ->insertStruct($data); 

        dd(
            DB::getLog()
        );

        // dd($course_id);
    }

    

}

