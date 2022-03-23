<?php

namespace simplerest\controllers;

use simplerest\models\az\BarModel;
use simplerest\models\az\ProductsModel;
use simplerest\core\controllers\Controller;
use simplerest\core\Model;
use simplerest\core\Request;
use simplerest\core\Route;
use simplerest\core\Acl;
use simplerest\core\controllers\MakeControllerBase;
use simplerest\core\libs\Factory;;
use simplerest\core\libs\DB;
use simplerest\core\libs\Mails;
use simplerest\core\libs\Strings;
use simplerest\core\libs\Hardware;
use simplerest\core\libs\Validator;
use simplerest\core\libs\Date;
//use GuzzleHttp\Client;
//use Guzzle\Http\Message\Request;
//use Symfony\Component\Uid\Uuid;
use simplerest\core\libs\Files;
use simplerest\core\libs\Time;
use simplerest\core\libs\Url;
use simplerest\core\libs\Schema;
use simplerest\core\libs\Config;
use simplerest\core\libs\StdOut;
use simplerest\core\libs\Env;
use simplerest\core\libs\Update;
use simplerest\controllers\api\Products;
use simplerest\controllers\api\TblPersona;
use simplerest\core\libs\System;
use simplerest\core\libs\Supervisor;

use simplerest\core\libs\Reflector;
use simplerest\libs\Foo;
use simplerest\core\Container;
use simplerest\core\libs\JobQueue;
use simplerest\core\libs\Task;

use simplerest\core\libs\HtmlBuilder\Form;
use simplerest\core\libs\HtmlBuilder\Bt5Form;
use simplerest\core\libs\HtmlBuilder\Html;
use simplerest\core\libs\HtmlBuilder\Tag;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;



class DumbController extends Controller
{
    function __construct()
    {
        parent::__construct();
        DB::getConnection('az');
    }

    function test_logger(){
        Files::logger('Holaaa mundo');
        Files::logger('R.I.P.');
    }

    function test_dd(){
        dd([4,5, 7], "My Array", true);
        dd('hola!', null,true);
        dd(677.55, 'x');
        dd(true, 'My bool');
    }

    function test_ssl(){
        dd(Url::has_ssl('simplerest.pulque.ro'));
    }

        /*
        https://www.programmerall.com/article/553939151/
    */
    function test_queue(){
        $queue = new \SplQueue();

        //$queue->setIteratorMode(\SplDoublyLinkedList::IT_MODE_FIFO | \SplDoublyLinkedList::IT_MODE_DELETE);

        $queue->enqueue('a');
        $queue->enqueue('b');
        $queue->enqueue('c');

        // shift
        d($queue->dequeue());
        
        // lista elementos
        foreach($queue as $item) {
            d($item);
        }

        //d($queue);
    }

    // ok
    function test504(){
        $vals = DB::table('products')
        ->setFetchMode('COLUMN')
        ->selectRaw('cost * 1.05 as cost_after_inc')->get();

        dd($vals);
        dd(DB::getLog());
    }

    // fails en PSQL
    function test505(){
        $vals = DB::table('products')
        ->setFetchMode('COLUMN')
        ->selectRaw('cost * ? as cost_after_inc', [1.05])->get();

        dd($vals);
        dd(DB::getLog());
    }

    function test506()
    {
        $con = DB::getConnection();
        $sth = $con->prepare('SELECT cost * ? as cost_after_inc FROM products');
        
        $sth->bindValue(1, 1.05, \PDO::PARAM_INT);
        $sth->execute(); 
       
        $res = $sth->fetch();
        dd($res);
    }


    function test507()
    {
        //dd(is_float('1.25'));
        //exit;

        $con = DB::getConnection();
        $sth = $con->prepare('SELECT cost * CAST(? AS DOUBLE PRECISION) as cost_after_inc FROM products');
        
        $sth->bindValue(1, 1.05, \PDO::PARAM_INT);

        $sth->execute(); // ok
       
        $res = $sth->fetch();
        dd($res);
    }
    

    /*
        Falla en POSTGRES:

        SELECT COUNT(*) as c, name FROM products GROUP BY name HAVING COUNT(*) > 3

        Es como si en pgsql se evaluara primero el HAVING y luego el SELECT.
    */
    function alias(){
        $rows = DB::table('products')
        ->deleted()
        ->groupBy(['name'])
        ->having(['c', 3, '>'])
        ->select(['name'])
        ->selectRaw('COUNT(*) as c')
        ->dontExec()
        ->get();

        dd(DB::getLog());
    }


    /*
        Corrige problema
    */
    function alias2(){
        $rows = DB::table('products')
        ->deleted()
        ->groupBy(['name'])
        ->select(['name'])
        ->selectRaw('COUNT(*) as c')
        ->havingRaw('c > ?', [3])
        ->get();

        dd($rows);
        dd(DB::getLog());
    }

    function index(){
        return 'INDEX';
    }

    function add($a, $b){
        var_dump($a);  // string

        $res = (int) $a + (int) $b;
        return  "$a + $b = " . $res;
    }

    function mul(){
        $req = Request::getInstance();
        $res = (int) $req[0] * (int) $req[1];
        return "$req[0] * $req[1] = " . $res . PHP_EOL;
    }

    function div(){
        $request = request();
        $res = $request->getParam(0) / $request->getParam(1);

        //dd($res);
        //
        // hacer un return en vez de un "echo" me habilita a manipular
        // la "respuesta", conviertiendola a JSON por ejemplo 
        //

        return [
            'result' => $res
        ];
    }

    function inc($val){
        $res = (float) $val +1;
        //response()->send($res);
        return $res;
    }

    function login(){
		view('login.php');
    }
    
    function casa_cambio(){
        view('casa_cambio/home.htm', [], 'casa_cambio/layout.php', 'casa_cambio/app.htm' );
    }

    /*
    function xyz(){
        DB::getConnection('db3');
        
        $curs = DB::table('countries')
        ->distinct(['currency'])->get();

        $groups = [];

        $rows = DB::table('countries')
        ->orderBy(['currency' => 'ASC'])
        ->get();

        foreach ($rows as $row){
            $groups[$row['currency']][] = $row;
        }

        $m2 = DB::table('currencies');
        $at = $m2->getAttr();

        //dd($groups);
        //exit;

        foreach ($groups as $curr_code => $g){
            $alpha2 = [];
            $alpha3 = [];
            $langCS = [];
            $langDE = [];
            $langEN = [];
            $langES = [];
            $langFR = [];
            $langIT = [];
            $langNL = [];
            foreach ($g as $c){
                $alpha2[] = $c['alpha2'];
                $alpha3[] = $c['alpha3'];
                $langCS[] = $c['langCS'];
                $langDE[] = $c['langDE'];
                $langEN[] = $c['langEN'];
                $langES[] = $c['langES'];
                $langFR[] = $c['langFR'];
                $langIT[] = $c['langIT'];
                $langNL[] = $c['langNL'];
                //dd($country);
            }

            $alpha2_main = count($alpha2) == 1 ? $alpha2[0] : NULL;
            $alpha3_main = count($alpha3) == 1 ? $alpha3[0] : NULL;

            $alpha2 = json_encode($alpha2);
            $alpha3 = json_encode($alpha3);
            $langCS = json_encode($langCS);
            $langDE = json_encode($langDE);
            $langEN = json_encode($langEN);
            $langES = json_encode($langES);
            $langFR = json_encode($langFR);
            $langIT = json_encode($langIT);
            $langNL = json_encode($langNL);

         
            $data = array_combine($at, [$alpha2_main, $alpha3_main, $alpha2, $alpha3, $langCS, $langDE, $langEN, $langES, $langFR, $langIT, $langNL, $curr_code]);
            
            //exit;
            $m2->create($data);        
        }

        //dd($not_grouped);
        //dd($groups);
        //dd($alpha2);
        //dd(count($groups));
    }
    */


    /*
    function csv(){
        DB::getConnection('db3');
        $m = (new Model())->connect()->table('currencies');

        $file = file_get_contents(UPLOADS_PATH . 'iso_4217.csv');
        $lines = explode("\n", $file);
        
        $regs = [];

        foreach($lines as $line){
            $r = explode(';', $line);
            $r[4] = explode(',', $r[4]);
            array_walk($r[4], function(&$str){ $str =  trim($str);});
            $r[4] = json_encode($r[4]);
            //dd($r);
            
            $reg = array_combine(['code', 'num', 'digits', 'cur_name', 'locations'], $r);
            dd($reg);

            dd($m->create($reg));
        }
    }
    */


    /*
    function mul(Request $req){
        $res = (int) $req[0] * (int) $req[1];
        echo "$req[0] + $req[1] = " . $res;
    }
    */    

    function where_basico(){
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

    function get_bar0(){
        $m = (new Model(true))
            ->table('bar')  
            ->where(['uuid', '0fefc2b1-f0d3-47aa-a875-5dbca85855f9']);
            
        dd($m->get());
    }

    function get_bar1(){
        $m = DB::table('bar')
         // ->assoc()
        ->where(['uuid', '0fefc2b1-f0d3-47aa-a875-5dbca85855f9']);
       
        dd($m->get());
    }

    function get_bar2(){
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

    function create_bar2(){
        $m = DB::table('bar');
        
        dd($m->create([
            'name' => 'gggggggggg',
			'price' => '100',
            'email' => 'a@b.com',
            'belongs_to' => 90
        ]));
    }

    function get_products(){
        dd(DB::table('products')->get());
    }

    function get_products2(){
        dd(DB::table('products')->where(['size', '2L'])->get());
    }

    function create_p(){

        $name = '';
        for ($i=0;$i<20;$i++)
            $name .= chr(rand(97,122));

        $id = DB::table('products')->create([ 
            'name' => $name, 
            'description' => 'Esto es una prueba 77', 
            'size' => '100L',
            'cost' => 66,
            'belongs_to' => 90,
            'digital_id' => 1
        ]);   
        
        return $id;
    }

    function create_baz($id = null){

        $name = '';
        for ($i=0;$i<20;$i++)
            $name .= chr(rand(97,122));

        $data = [ 
            'name' => $name,
            'cost' => 100
        ];

        if ($id != null){
            $data['id'] = $id;
        }

        $id = DB::table('baz')->create($data);    

        dd($id, 'las_inserted_id');
    }

    
    // implementada y funcionando en register() 
    function transaction(){
        DB::beginTransaction();

        try {
            $name = '';
            for ($i=0;$i<20;$i++)
                $name .= chr(rand(97,122));

            $id = DB::table('products')->create([ 
                'name' => $name, 
                'description' => 'bla bla bla', 
                'size' => rand(1,5).'L',
                'cost' => rand(0,500),
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
    function transaction2(){
        DB::transaction(function(){
            $name = '';
            for ($i=0;$i<20;$i++)
                $name .= chr(rand(97,122));

            $id = DB::table('products')->create([ 
                'name' => $name, 
                'description' => 'Esto es una prueba', 
                'size' => rand(1,5).'L',
                'cost' => rand(0,500),
                'belongs_to' => 90
            ]);   

            throw new \Exception("AAA"); 
        });      
    }
    
    function output_mutator(){
        $rows = DB::table('users')
        ->registerOutputMutator('username', function($str){ return strtoupper($str); })
        ->get();

        dd($rows);
    }

    function output_mutator2(){
        $rows = DB::table('products')
        ->registerOutputMutator('size', function($str){ return strtolower($str); })
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
    function transform(){
        //$this->is_admin = true;


        $t = new \simplerest\transformers\UsersTransformer();

        $rows = DB::table('users')
        ->registerTransformer($t, $this)
        ->get();

        dd($rows);
    }

    function transform_and_output_mutator(){
        $t = new \simplerest\transformers\UsersTransformer();

        $rows = DB::table('users')
        ->registerOutputMutator('username', function($str){ return strtoupper($str); })
        ->registerTransformer($t)
        ->get();

        dd($rows);
    }

    function transform2(){
        $t = new \simplerest\transformers\ProductsTransformer();

        $rows = DB::table('products')
        ->where(['size'=>'2L'])
        ->registerTransformer($t)
        ->get();

        dd($rows);
    }


    // 'SELECT id, name, cost FROM products WHERE (cost = 200) AND deleted_at IS NULL LIMIT 20, 10;'
    function g(){
        dd(DB::table('products')
        ->where(['cost', 200])
        ->limit(10)
        ->offset(20)
        ->get(['id', 'name', 'cost']));
        
        dd(DB::getLog());
    }

    function limit(){
        dd(DB::table('products')
        ->select(['id', 'name', 'cost'])
        ->offset(10)
        ->limit(5)
        ->get());

        dd(DB::getLog());
    }

    function limit0(){
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
    function limite(){
        DB::table('products')->offset(20)->limit(10)->get();
        dd(DB::getLog());

        DB::table('products')->limit(10)->get();
        dd(DB::getLog());
    }

    function distinct(){
        dd(DB::table('products')->distinct()->get(['size']));

        // Or
        dd(DB::table('products')->distinct(['size'])->get());

        // Or
        dd(DB::table('products')->select(['size'])->distinct()->get());
    }

    function distinct1(){
        dd(DB::table('products')->select(['size', 'cost'])->distinct()->get());
    }

    function distinct2(){
        dd(DB::table('users')->distinct()->get());
    }

    function distinct3(){
        dd(DB::table('products')->distinct()->get());
    }

    function pluck(){
        $names = DB::table('products')->pluck('size');

        foreach ($names as $name) {
            dd($name);
        }
    }

    function pluck2($uid) {
        $perms = DB::table('user_sp_permissions')
        ->assoc()
        ->where(['user_id' => $uid])
        ->join('sp_permissions', 'user_sp_permissions.sp_permission_id', '=', 'sp_permissions.id')
        ->pluck('name');

        dd($perms);
    }

    function get_product($id){       
        // Include deleted items
        dd(DB::table('products')->find($id)->deleted()->dd());
    }
    
    function exists(){
       
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
           
    function first(){
        dd(DB::table('products')->where([ 
            ['cost', 100, '>='],
            ['cost', 150, '<'],
            ['belongs_to',  90]
        ])->select(['name', 'size', 'cost'])
        ->first()); 
    }

    function value(){
		dd(DB::table('products')->where([ 
            ['cost', 5000, '>=']
        ])->value('name')); 

        dd(DB::getLog());
    }

    function value1(){
        dd(DB::table('products')->where([ 
            ['cost', 200, '>='],
            ['cost', 500, '<='],
            ['belongs_to',  90]
        ])->value('name'));
        
        dd(DB::getLog());
    }

    function oldest(){
        // oldest first
        dd(DB::table('products')->oldest()->first());
        dd(DB::getLog());
    }

    function newest(){
        // newest, first result
        dd(DB::table('products')->newest()->first());
        dd(DB::getLog());
    }

    function newest2(){     
        dd(DB::table('products')->where([ 
            ['cost', 100, '>='],
            ['cost', 150, '<'],
            ['belongs_to',  90]
        ])->select(['name', 'size', 'cost', 'created_at'])
        ->newest()
        ->first());
    }


    
    // random or rand
    function random(){
        //dd(DB::table('products')->random()->get(['id', 'name']), 'ALL');
        dd(DB::table('products')->random()->select(['id', 'name'])->get(), 'ALL');

        dd(DB::table('products')->random()->limit(5)->get(['id', 'name']), 'N RESULTS');

        dd(DB::table('products')->random()->select(['id', 'name'])->first(), 'FIRST');
    }

    function count(){
        $c = DB::table('products')
        ->where([ 'belongs_to'=> 90] )
        ->count();

        dd($c);
    }

    function count1(){
        $c = DB::table('products')
        //->assoc()
        ->where([ 'belongs_to'=> 90] )
        ->count('*', 'count');

        dd($c);
        dd(DB::getLog());
    }

    function count1b(){
        // SELECT COUNT(*) FROM products WHERE cost >= 100 AND size = '1L' AND belongs_to = 90 AND deleted_at IS NULL 

        $res =  DB::table('products')
        ->where([ ['cost', 100, '>='], ['size', '1L'], ['belongs_to', 90] ])
        ->count();

        dd($res);
        dd(DB::getLog());
    } 

    // SELECT COUNT(DISTINCT( ...
    function count2(){
        // SELECT COUNT(DISTINCT description) FROM products WHERE cost >= 100 AND size = '1L' AND belongs_to = 90 AND deleted_at IS NULL  

        $res = DB::table('products')
        ->where([ ['cost', 100, '>='], ['size', '1L'], ['belongs_to', 90] ])
        ->distinct()
        ->count('description');

        dd($res);
        dd(DB::getLog());
    }

    // SELECT COUNT(DISTINCT( ...
    function count2b(){
        /*
             SELECT COUNT(DISTINCT description) FROM products WHERE cost >= 100 AND size = '1L' AND belongs_to = 90 AND deleted_at IS NULL  
        */
        $res = DB::table('products')
        ->where([ ['cost', 100, '>='], ['size', '1L'], ['belongs_to', 90] ])
        ->distinct()
        ->count('description', 'count');

        dd($res);
        dd(DB::getLog());
    }

    function count3(){
        $uid = 90;

        $count = (int) DB::table('user_roles')
		->where(['user_id' => $uid])
        ->setFetchMode('COLUMN')
		->count();
		
        dd($count);
        dd(DB::getLog());
    }

    function avg(){
        // SELECT AVG(cost) FROM products WHERE cost >= 100 AND size = '1L' AND belongs_to = 90 AND deleted_at IS NULL; 

        $res = DB::table('products')
        ->where([ ['cost', 100, '>='], ['size', '1L'], ['belongs_to', 90] ])
        ->avg('cost', 'prom');

        dd($res);
    }

    function sum(){
        // SELECT SUM(cost) FROM products WHERE cost >= 100 AND size = '1L' AND belongs_to = 90 AND deleted_at IS NULL; 

        $res = DB::table('products')
        ->where([ ['cost', 100, '>='], ['size', '1L'], ['belongs_to', 90] ])
        ->sum('cost', 'suma');

        dd($res);
        dd(DB::getLog());
    }

    function min(){
        $res = DB::table('products')
        ->where([ ['cost', 100, '>='], ['size', '1L'], ['belongs_to', 90] ])
        ->min('cost', 'minimo');

        dd($res);
    }

    function max(){ 
        $res =  DB::table('products')
        ->where([ ['cost', 100, '>='], ['size', '1L'], ['belongs_to', 90] ])
        ->max('cost', 'maximo');

        dd($res);
        dd(DB::getLog());
    }

    // select + max
    function max1(){
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
        ->where([ ['cost', 100, '>='], ['size', '1L'], ['belongs_to', 90] ])
        ->select(['name'])
        ->max('cost', 'maximo');

        dd($res);
        dd(DB::getLog());
    }

    /*
        select + addSelect
    */
    function select() {
        dd(DB::table('products')
        ->random()
        ->select(['id', 'name'])
        ->addSelect('is_active')
        ->where(['is_active', true])
        ->first());

        dd(DB::getLog());
    }

    // RAW Select
    function select1r() {
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
    function select2() {
        $m = DB::table('products')->setFetchMode('COLUMN')
        ->selectRaw('cost * ? as cost_after_inc', [1.05]);

        dd($m->get());
        dd($m->dd());
    }

    function select3() {
        $m = DB::table('products')->setFetchMode('COLUMN')
        ->where([ ['cost', 100, '>='], ['size', '1L'], ['belongs_to', 90] ])
        ->selectRaw('cost * ? as cost_after_inc', [1.05]);

        dd($m->get());
        dd($m->dd());
    }

    // DISTINCT -- ok
    function select30() {
        $m = DB::table('products')
        ->where([ ['cost', 100, '>='] ])
        ->select(['name', 'cost'])
        ->distinct();

        dd($m->get());
        dd($m->dd());;
    }

    // DISTINCT
    function select3a() {
        $m = DB::table('products')
        ->where([ ['cost', 100, '>='] ])
        ->selectRaw('cost * ? as cost_after_inc', [1.05])
        ->distinct();

        dd($m->get());
        dd($m->dd());
    }

    // DISTINCT + fetch mode = COLUMN
    function select3b() {
        $m = DB::table('products')
        ->setFetchMode('COLUMN')
        ->where([ ['cost', 100, '>='] ])
        ->selectRaw('cost * ? as cost_after_inc', [1.05])
        ->distinct();

        dd($m->get());
        dd($m->dd());
    }

    // select + selectRaw
    function select4() {
        $rows  = DB::table('products')
        ->where([ ['cost', 100, '>='], ['size', '1L'], ['belongs_to', 90] ])
        ->selectRaw('cost * ? as cost_after_inc', [1.05])
        ->addSelect('name')
        ->addSelect('cost')
        ->get();

        dd($rows);
        dd(DB::getLog()); 
    }

    // select + selectRaw
    function select4b() {
        $rows  = DB::table('products')
        ->where([ ['cost', 50, '>='], ['size', '1L'], ['belongs_to', 90] ])
        ->selectRaw('cost * ? as cost_after_inc', [1.05])
        ->addSelect('name')
        ->addSelect('cost')
        ->distinct()
        ->get();

        dd($rows);
        dd(DB::getLog()); 
    }

    // selectRaw + fetch mode = COLUMN
    function select4c() {
        $rows  = DB::table('products')
        ->setFetchMode('COLUMN')
        ->where([ ['cost', 50, '>='], ['size', '1L'], ['belongs_to', 90] ])
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
    function select_group_count(){
        dd(DB::table('products')->deleted()
        ->groupBy(['size'])
        ->select(['size'])
        ->count());

		dd(DB::getLog());
    }

    /*
        SELECT size, AVG(cost) FROM products GROUP BY size
    */
    function select_group_avg(){
        dd(DB::table('products')->deleted()
        ->groupBy(['size'])
        ->select(['size'])
        ->avg('cost'));  
        
        dd(DB::getLog());
    }

    function filter_products1(){
        dd(DB::table('products')->deleted()->where([ 
            ['size', '2L']
        ])->get());
    }
    
    function filter_products2(){
        $m = DB::table('products')
        ->where([ 
            ['name', ['Vodka', 'Wisky', 'Tekila','CocaCola']], // IN 
            ['is_locked', 0],
            ['belongs_to', 90]
        ])
        ->whereNotNull('description');

        dd($m->get());
        var_dump(DB::getLog());
        //var_dump($m->dd());
    }

    // SELECT * FROM products WHERE name IN ('CocaCola', 'PesiLoca') OR cost IN (100, 200)  OR cost >= 550 AND deleted_at IS NULL
    function filter_products3(){

        dd(DB::table('products')->where([ 
            ['name', ['CocaCola', 'PesiLoca']], 
            ['cost', 550, '>='],
            ['cost', [100, 200]]
        ], 'OR')->get());    
    }

    function filter_products4(){    
        dd(DB::table('products')->where([ 
            ['name', ['CocaCola', 'PesiLoca', 'Wisky', 'Vodka'], 'NOT IN']
        ])->get());
    }

    function filter_products5(){
        // implicit 'AND'
        dd(DB::table('products')->where([ 
            ['cost', 200, '<'],
            ['name', 'CocaCola'] 
        ])->get());        

        dd(DB::getLog());
    }

    function filter_products6(){
        dd(DB::table('products')->where([ 
            ['cost', 200, '>='],
            ['cost', 270, '<=']
        ])->get());  
        
        dd(DB::getLog());
    }

    // WHERE IN
    function where1(){
        dd(DB::table('products')->where(['size', ['0.5L', '3L'], 'IN'])->get());

        dd(DB::getLog());
    }

    // WHERE IN
    function where2(){
        dd(DB::table('products')->where(['size', ['0.5L', '3L']])->get());

        dd(DB::getLog());
    }

    // WHERE IN
    function where3(){
        dd(DB::table('products')
        //->dontQualify()
        ->whereIn('size', ['0.5L', '3L'])->get());

        dd(DB::getLog());
    }

    //WHERE NOT IN
    function where4(){
        $m = DB::table('products')
        //->dontQualify()
        ->where(['size', ['0.5L', '3L', '1L'], 'NOT IN']);
        $m->dd();

        dd($m->get());
        dd(DB::getLog());
    }

    //WHERE NOT IN
    function where5(){
        dd(DB::table('products')->whereNotIn('size', ['0.5L', '3L'])->get());
    }

    // WHERE NULL
    function where6(){  
        dd(DB::table('products')->where(['workspace', null])->get());   
    }

    // WHERE NULL
    function where7(){  
        dd(DB::table('products')->whereNull('workspace')->get());
    }

    // WHERE NOT NULL
    function where8(){  
        dd(DB::table('products')->where(['workspace', null, 'IS NOT'])->get());   
    }

    // WHERE NOT NULL
    function where9(){  
        dd(DB::table('products')->whereNotNull('workspace')->get());
    }

    // WHERE BETWEEN
    function where10(){
        dd(DB::table('products')
        ->select(['name', 'cost'])
        ->whereBetween('cost', [100, 250])->get());
    }

    // WHERE BETWEEN
    function where11(){
        dd(DB::table('products')
        ->select(['name', 'cost'])
        ->whereNotBetween('cost', [100, 250])->get());
    }
    
    function where12(){
        dd(DB::table('products')
        ->find(145)->first());
    }

    function where13(){
        dd(DB::table('products')
        ->where(['cost', 150])
        ->value('name'));
    }

    /*
        SELECT  name, cost, id FROM products WHERE belongs_to = '90' AND (cost >= 100 AND cost < 500) AND description IS NOT NULL
    */
    function where14(){
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
    function or_where(){
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
    function or_where2(){
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
    function where_or(){
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
    function where_or1(){
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
    function where_or2(){
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
    function or_where3(){
        $email = 'nano@g.c';
        $username = 'nano';

        $rows = DB::table('users')->assoc()->unhide(['password'])
            ->where([ 'email'=> $email, 
                      'username' => $username 
            ], 'OR') 
            ->setValidator((new Validator())->setRequired(false))  
            ->get();

        dd($rows);
    }

    // SELECT * FROM users WHERE (email = 'nano@g.c' OR  username = 'nano') AND deleted_at IS NULL
    function or_where3b(){
        $email = 'nano@g.c';
        $username = 'nano';

        $rows = DB::table('users')->assoc()
            ->where([ 'email'=> $email ]) 
            ->orWhere(['username' => $username ])
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
            is_active = 1 OR 
            (cost <= 100 AND description IS NOT NULL)
        ) 
        AND belongs_to > 150;
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
        ->group(function($q){  
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
        ->or(function($q){  
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
    function not(){
        $m = DB::table('products')

        ->not(function($q){  // <-- group *
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
    function notor(){
        $m = DB::table('products')

        ->where(['belongs_to', 150, '>'])
        ->not(function($q) {
            $q->where(['name', 'a$'])
            ->or(function($q){
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
    function notor_whereraw(){
        $m = DB::table('products')

        ->where(['belongs_to', 150, '>'])
        ->not(function($q) {
            $q->whereRegEx('name', 'a$')
            ->or(function($q){ 
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
    function or_problematico(){
        $m = DB::table('products')
    
        ->whereRegEx('name', 'a$')
        ->or(function($q){
            $q->where(['cost', 100, '<=']);
        })     
        ->deleted()        
        ->dontExec();

        //dd($m->get());
        dd($m->dd());
    }

    // ok
    function or__problematico_b(){
        $m = DB::table('products')
    
        ->whereRegEx('name', 'a$')
        ->or(function($q){
            $q->group(function($q){
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
    function or_otro20(){
        $m = DB::table('products')    

        ->whereRegEx('name', 'a$') 
        ->orWhere(['description', NULL, 'IS NOT'])
        
        ->deleted()        
        ->dontExec();

        //dd($m->get());
        dd($m->dd());
    }

    function or_otro(){
        $m = DB::table('products')    

        ->group(function($q){
            $q->whereRegEx('name', 'a$');
        })
        
        ->orWhere(['description', NULL, 'IS NOT'])
        
        ->deleted()        
        ->dontExec();

        //dd($m->get());
        dd($m->dd());
    }

    function or_otro2(){
        $m = DB::table('products')
    
        ->group(function($q){
            $q->whereRegEx('name', 'a$');
        })

        ->or(function($q){
            $q->where(['cost', 100, '<=']);
        })   
        
        
        ->deleted()        
        ->dontExec();

        //dd($m->get());
        dd($m->dd());
    }

    function test000001(){
        $m = DB::table('products')
    
        ->group(function($q){
            $q->where(['description', NULL, 'IS NOT'])
            ->where(['id', 90]);
        })
        
        ->or(function($q){
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
    function notor_whereraw2(){
        $m = DB::table('products')

        ->where(['belongs_to', 150, '>'])
        ->not(function($q) {
            $q->where([
                ['cost', 100, '<='],
                ['description', NULL, 'IS NOT']
            ])
            ->or(function($q){
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
        ->group(function($q){  
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

        ->group(function($q){  // <-- group *
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
        d($sql, 'SQL');

        d(DB::statement($sql), 'AFFECTED ROWS');
    }  


    function where_raw_where_in2b()
    {
        $m = DB::table('products');
        $m->whereIn('cost', [100, 200]);
        
        $m
        //->dontExec()
        ->delete(); 

        $sql = $m->getLog();
        d($sql, 'SQL');

        d(DB::statement($sql), 'AFFECTED ROWS');
    }  

    function where_raw_where_in2()
    {
        $m = DB::table('products')

        ->group(function($q){  // <-- group *
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

        d(DB::statement($sql));
    }  

    function when(){
        $lastname = 'Bozzo';

        $m = DB::table('users')
        ->when($lastname, function ($q) use ($lastname) {
            $q->where(['lastname', $lastname]);
        });

        dd($m->get());
        dd($m->dd());
    }

    function when2(){
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
    

    function where_col(){
        $m = (DB::table('users'))
        ->whereColumn('firstname', 'lastname', '=');  

        dd($m->get()); 
	    var_dump($m->dd());
    }
   

    // SELECT * FROM products WHERE ((cost < IF(size = "1L", 300, 100) AND size = '1L' ) AND belongs_to = 90) AND deleted_at IS NULL ORDER BY cost ASC
    function where_raw(){
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
    function where_raw1(){
        $m = DB::table('products')
        
        ->where(['belongs_to', 90])

        ->group(function($q){
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

        ->group(function($q){  // <-- group *
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

        ->group(function($q){  // <-- group *
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

        ->group(function($q){  // <-- group *
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
    function where_raw2(){
        dd(DB::table('products')->deleted()
        ->whereRaw('EXISTS (SELECT 1 FROM users WHERE products.belongs_to = users.id AND users.lastname = ?  )', ['AB'])
        ->get());
    }

    function regex(){
        $m = DB::table('products')
        ->whereRegEx('name', 'a$');

        dd($m->get());
        dd($m->dd());
    }

    function regex2(){
        $m = DB::table('products')
        ->whereNotRegEx('name', 'a$');

        dd($m->get());
        dd($m->dd());
    }


    /*
        WHERE EXISTS

        SELECT * FROM products WHERE EXISTS (SELECT 1 FROM users WHERE products.belongs_to = users.id AND users.lastname = 'AB')
    */
    function where_exists(){
        $m = DB::table('products')->deleted()
        ->whereExists('(SELECT 1 FROM users WHERE products.belongs_to = users.id AND users.lastname = ?)', ['AB']);

        dd($m->get());
        dd($m->dd());
    }


    function test_where_date(){
        $facturas = DB::table('facturas')
        ->whereDate('created_at', '2021-12-29')
        ->get();

        d($facturas);

        $facturas = DB::table('facturas')
        ->whereDate('created_at', '2021-12-29 19:42:08')
        ->get();

        d($facturas);

        $testx   = DB::table('testx')
        ->whereDate('fecha', '2022-01-12')
        ->get();

        d($testx);

        $testx   = DB::table('testx')
        ->whereDate('fecha', '2022-01-12 20:10:18')
        ->get();

        d($testx);
    }

    function test_where_date2(){
        $facturas = DB::table('facturas')
        ->whereDate('created_at', '2021-12-29', '>')
        ->get();

        d($facturas);
    }

    function test_where_date3(){
        $testx   = DB::table('testx')
        ->whereDate('fecha', '2022-01-12', '>')
        ->get();

        d($testx);
    }

    /*
        SELECT * FROM products WHERE 1 = 1 AND deleted_at IS NULL ORDER BY cost ASC, id DESC LIMIT 1, 4
    */
    function order(){    
        dd(DB::table('products')->orderBy(['cost'=>'ASC', 'id'=>'DESC'])->take(4)->offset(1)->get());

        dd(DB::table('products')->orderBy(['cost'=>'ASC'])->orderBy(['id'=>'DESC'])->take(4)->offset(1)->get());

        dd(DB::table('products')->orderBy(['cost'=>'ASC'])->take(4)->offset(1)->get(null, ['id'=>'DESC']));

        dd(DB::table('products')->orderBy(['cost'=>'ASC'])->orderBy(['id'=>'DESC'])->take(4)->offset(1)->get());

        dd(DB::table('products')->take(4)->offset(1)->get(null, ['cost'=>'ASC', 'id'=>'DESC']));
    }

    /*
        RAW
        
        SELECT * FROM products WHERE 1 = 1 AND deleted_at IS NULL ORDER BY is_locked + is_active ASC
    */
    function order2(){
        dd(DB::table('products')->orderByRaw('is_locked * is_active DESC')->get()); 
    }

    function grouping(){
        dd(DB::table('products')->where([ 
            ['cost', 100, '>=']
        ])->orderBy(['size' => 'DESC'])
        ->groupBy(['size'])
        ->select(['size'])
        //->take(5)
        //->offset(5)
        ->avg('cost'));
    }


    function where(){        

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
        
    function having(){ 
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
	function having0(){  
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
	function havingx(){  
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
    function having1(){        
        $m = DB::table('products')
            //->dontQualify()
            ->groupBy(['cost', 'size'])
            ->having(['cost', 100])
            ->select(['cost', 'size']);
		
        //dd($m->get());
		dd($m->dd()); ; 
    }    
	
    function having1_ta(){        
        $m = DB::table('products', 'p')
            //->dontQualify()
            ->groupBy(['cost', 'size'])
            ->having(['cost', 100])
            ->select(['cost', 'size']);
		
        dd($m->get());
		dd($m->dd()); 
    }   

	// SELECT cost, size FROM products GROUP BY cost,size HAVING cost = 100
	function having1b(){        
        dd(DB::table('products')->deleted()
            ->groupBy(['cost', 'size'])
            ->having(['cost', 100])
            ->get(['cost', 'size']));
		
		dd(DB::getLog()); 
    }   

    function having1c(){        
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
    function having2(){
        dd(DB::table('products')
            ->groupBy(['cost', 'size', 'belongs_to'])
            ->having(['belongs_to', 90])
            ->having([  
                        ['cost', 100, '>='],
                        ['size' => '1L'] ], 
            'OR')
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
    function having2b(){
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
    function having2c(){
        dd(DB::table('products')
            ->groupBy(['cost', 'size', 'belongs_to'])
            ->having(['belongs_to', 90])
            ->orHaving([  
                        ['cost', 100, '>='],
                        ['size' => '1L'] ] 
            )
            ->orderBy(['size' => 'DESC'])
            ->get(['cost', 'size', 'belongs_to'])); 

        dd(DB::getLog());
    }

    /*
        RAW HAVING
    */
    function having3(){
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
        
        WHERE (guest = 1 AND table = \'products\' AND r = 1) 
        ORDER BY users.id DESC;
    */
    function joins(){
        $m = (new Model())->table('other_permissions', 'op')
        ->join('folders', 'op.folder_id', '=',  'folders.id')
        ->join('users', 'folders.belongs_to', '=', 'users.id')
        ->join('user_roles', 'users.id', '=', 'user_roles.user_id')
        ->where([
            ['guest', 1],
            ['table', 'products'],
            ['r', 1]
        ])
        ->orderByRaw('users.id DESC');

        dd($m->dd(true)); 
    }

    function jx(){
        $m = DB::table('products')
        ->join('product_categories');
 
        dd($m->dd(true)); 
    }  
    
    function j(){
        $m = DB::table('products')
        ->join('products_product_categories', 'products.id', '=',  'products_product_categories.product_id')
        ->join('product_comments', 'products.id', '=', 'product_comments.product_id');
 
        dd($m->dd(true)); 
    }    

    function j_auto(){
        $m = DB::table('products')
        ->join('product_categories')
        ->leftJoin('product_comments');

        dd($m->get()); 
        dd($m->dd(true)); 
    }

    /*
        Auto-join with alias (as)
    */
    function j_auto1(){
        $m = DB::table('products')
        ->join('product_categories as pc');

        dd($m->get()); 
        dd($m->dd(true)); 
    }

    function j_auto1b(){
        $m = DB::table('products')
        ->dontExec()
        ->join('product_categories as product_categories')
        ->where(['product_categories.name_catego' => 'frutas']);
 
        dd(DB::select($m->dd())); 
        dd($m->dd(true));       
    }

    function j1(){
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

    function j1_auto(){

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

    function j1_auto2(){
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

    function j2(){
        $m = DB::table('users')
        ->join('user_sp_permissions', 'users.id', '=',  'user_sp_permissions.user_id')
        ->join('sp_permissions', 'sp_permissions.id', '=', 'user_sp_permissions.id')

        ->select(['sp_permissions.name as perm', 'username', 'is_active']);

        dd($m->get()); 
        dd($m->dd()); 
    }


    function j2a(){
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


    function j2b(){
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
    function j2_auto(){
        $m = DB::table('users')
        //->join('user_sp_permissions');
        ->join('sp_permissions');

        $m->select(['sp_permissions.name as perm', 'username', 'is_active']);

        dd($m->get()); 
        dd($m->dd()); 
    }

    function join2c(){
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


    function join2d(){
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


    function join2e(){
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

    function join2(){
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

    function join2b(){
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
    function j3_auto(){
        DB::setConnection('db_flor');

        $m = DB::table('tbl_cliente')
        ->join('tbl_cliente_informacion_tributaria');

        dd($m->dd(true));
    }

    function j4_auto1(){
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


    function j4_auto2(){
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

    function j4_auto3(){
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
    function leftjoin(){
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
    function crossjoin(){
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

    function naturaljoin(){
        $m = (new Model())->table('employee')
        ->naturalJoin('department')
        ->unhideAll()
        ->deleted()
        ->dontExec();
        
        dd($m->dd());    
    }
 
    // SELECT COUNT(*) from users CROSS JOIN products CROSS JOIN roles;
    function crossjoin2(){
        DB::table('users')
        ->crossJoin('products')
        ->crossJoin('roles')
        ->unhideAll()
        ->deleted()
        ->dontExec()->get();
        
        dd(DB::getLog());    
    }

    // SELECT * FROM users CROSS JOIN products CROSS JOIN roles WHERE users.id = 90;'
    function crossjoin2b(){
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
    function crossjoin3(){
        $rows = DB::table('users')->crossJoin('products')->crossJoin('roles')
        ->join('user_sp_permissions', 'users.id', '=', 'user_sp_permissions.user_id')
        ->unhideAll()
        ->deleted()
        //->dontExec()
        ->get();
        
        dd($rows);
        dd(DB::getLog());    
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


    function get_nulls(){    
        // Get products where workspace IS NULL
        dd(DB::table('products')->where(['workspace', null])->get());   
   
        // Or
        dd(DB::table('products')->whereNull('workspace')->get());
    }

    /*
        Debug without exec the query
    */
    function dontExec(){
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
    function get_users(){
        $array = DB::table('users')->orderBy(['id'=>'DESC'])->get();

        echo '<pre>';
        Factory::response()
        ->setPretty(true)
        ->send($array);
        echo '</pre>';
    }

    function get_userdata(){
        //d(Acl::getCurrentUid());

        $data = [];
        $data['email'] = 'xxx@g.com';

        DB::getDefaultConnection();

        $u = get_user_model_name();
        $m = new $u();

        $userdata = ($m)
        ->where([$u::$email => $data['email'] ])
        ->first();

        d($userdata);
    }

    function get_userdata2(){
        //$uid = Acl::getCurrentUid();

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

        d($userdata);
        d($m->dd());
    }

    function get_user($id){
        $u = DB::table('users');
        $u->unhide(['password']);
        $u->hide(['id', 'username', 'confirmed_email', 'firstname','lastname', 'deleted_at', 'belongs_to']);
        $u->where(['id'=>$id]);

        dd($u->get());
        dd($u->getLog());
    }

    function del_user($id){
        $u = DB::table('users');
        $ok = (bool) $u->where(['id' => $id])->setSoftDelete(false)->delete();
        
        dd($ok);
    }

 
    function update_user($id) {
        $u = DB::table('users');

        $count = $u->where(['firstname' => 'HHH', 'lastname' => 'AAA', 'id' => 17])->update(['firstname'=>'Nico', 'lastname'=>'Buzzi', 'belongs_to' => 17]);
        
        dd($count);
    }

    function update_user2() 
    {
        $firstname = '';
        for ($i=0;$i<20;$i++)
            $firstname .= chr(rand(97,122));

        $lastname = strtoupper($firstname);    

        $u = DB::table('users');

        $ok = $u->where([ [ 'email', 'nano@'], ['deleted_at', NULL] ])
        ->update([ 
                    'firstname' => $firstname, 
                    'lastname' => $lastname
        ]);
        
        dd($ok);
    }

    function update_users() {
        $u = DB::table('users');
        $count = $u->where([ ['lastname', ['AAA', 'Buzzi']] ])->update(['firstname'=>'Nicos']);
        
        dd($count);
    }

    function test_touch_model(){
        DB::table('products')
        ->find(145)
        ->touch();

        $p = DB::table('products')
        ->find(145)
        ->first();

        d($p);
    }

    function create_user($username, $email, $password, $firstname, $lastname)
     {        
        for ($i=0;$i<20;$i++)
            $email = chr(rand(97,122)) . $email;
        
        $u = DB::table('users');
        $u->fill(['email']);
        //$u->unfill(['password']);
        $id = $u->create(['username' => $username, 'email'=>$email, 'password'=>$password, 'firstname'=>$firstname, 'lastname'=>$lastname]);
        
        dd($id);
        dd(DB::getLog());
    }

    function fillables(){
        $m = DB::table('files');
        $affected = $m->where(['id' => 240])->update([
            "filename_as_stored" => "xxxxxxxxxxxxxxxxx.jpg"
        ]);

        dd($affected, 'Affected:');

        // Show result
        $rows = DB::table('files')->where(['id' => 240])->get();
        dd($rows);
    }

    function update_products() {
        $p = DB::table('products');
        $count = $p->where([['cost', 100, '<'], ['belongs_to', 90]])->update(['description' => 'x_x']);
        
        dd($count);
    }

    function test_find_or_fail(){
        d(
            DB::table('products')
            ->findOrFail(1000)
            ->first()
        );
    }

    function test_update_or_fail(){
        d(
            DB::table('products')
            ->updateOrFail(['description' => 'abc'])
        );
    }
   
    /*
        Habilitar:

        https://myaccount.google.com/lesssecureapps

        e IMAP

        https://www.arclab.com/en/kb/email/how-to-enable-imap-pop3-smtp-gmail-account.html
    */
    function sender(){
        dd(Mails::sendMail('boctulus@gmail.com', 'Pablo', 'Pruebita 001JRB', 'Hola!<p/>Esto es una más <b>prueba</b> con el server de JuamMa<p/>Chau'));     
    }

    // from cotizacion@brimell.cl
    function sender_b(){
        //$path = ETC_PATH . 'example.sql';

        dd(Mails::sendMail('mueblesultra@gmail.com', '', 'Prueba B3', 'HEY!!!!!!!!!!!<p/>Esto es una más <b>prueba</b> con el SMTP de <i>Brimell</i><p/>Chau', null, null, 'cotizacion@brimell.cl', 'Brimell', 'boctulus@gmail.com'));     
    }

    // function sender_v8(){
    //     dd(
    //         Mails::sendMail(
    //             to_email:'boctulus@gmail.com', 
    //             subject:'Prueba B8',
    //             body:'HEY!!!!<p/>Esto es una más <b>prueba</b> con el SMTP de <i>Brimell</i><p/>Chau'
    //         )
    //     );     
    // }

    /*
        https://github.com/sendgrid/sendgrid-php
    */
    function sendgrid(){
        $email = new \SendGrid\Mail\Mail();
        $email->setFrom("boctulus@gmail.com", "boctulus");
        $email->setSubject("Probando SendGrid");
        $email->addTo("boctulus@gmail.com", "boctulus");
        $email->addContent("text/plain", "Probando el envio,...");
        $email->addContent(
            "text/html", "<strong>and easy to do anywhere, even with PHP</strong>"
        );
        $sendgrid = new \SendGrid(env('SENDGRID_API_KEY'));
        try {
            $response = $sendgrid->send($email);
            print $response->statusCode() . "\n";
            print_r($response->headers());
            print $response->body() . "\n";
        } catch (\Exception $e) {
            echo 'Caught exception: '. $e->getMessage() ."\n";
        }
    }

    function sender2(){
        //Create a new PHPMailer instance
        $mail = new PHPMailer();

        //Tell PHPMailer to use SMTP
        $mail->isSMTP();

        //Enable SMTP debugging
        //SMTP::DEBUG_OFF = off (for production use)
        //SMTP::DEBUG_CLIENT = client messages
        //SMTP::DEBUG_SERVER = client and server messages
        $mail->SMTPDebug = SMTP::DEBUG_SERVER;

        //Set the hostname of the mail server
        $mail->Host = 'smtp.gmail.com';
        //Use `$mail->Host = gethostbyname('smtp.gmail.com');`
        //if your network does not support SMTP over IPv6,
        //though this may cause issues with TLS

        //Set the SMTP port number:
        // - 465 for SMTP with implicit TLS, a.k.a. RFC8314 SMTPS or
        // - 587 for SMTP+STARTTLS
        $mail->Port = 465;

        //Set the encryption mechanism to use:
        // - SMTPS (implicit TLS on port 465) or
        // - STARTTLS (explicit TLS on port 587)
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;

        //Whether to use SMTP authentication
        $mail->SMTPAuth = true;

        //Username to use for SMTP authentication - use full email address for gmail
        $mail->Username = 'pbozzolo22@gmail.com';

        //Password to use for SMTP authentication
        $mail->Password = 'Q2w3e4r5t6';

        //Set who the message is to be sent from
        //Note that with gmail you can only use your account address (same as `Username`)
        //or predefined aliases that you have configured within your account.
        //Do not use user-submitted addresses in here
        $mail->setFrom('pbozzolo22@gmail.com', 'Pablo Bozzolo');

        //Set an alternative reply-to address
        //This is a good place to put user-submitted addresses
        #$mail->addReplyTo('replyto@example.com', 'First Last');

        //Set who the message is to be sent to
        $mail->addAddress('mueblesultra@gmail.com', 'Muebles Ultra');

        //Set the subject line
        $mail->Subject = 'Prueba de envio';

        //Read an HTML message body from an external file, convert referenced images to embedded,
        //convert HTML into a basic plain-text alternative body
        #$mail->msgHTML(file_get_contents('contents.html'), __DIR__);

        $mail->msgHTML('<b>Hola</b>  Espero que este mensaje llegue');

        //Replace the plain text body with one created manually
        $mail->AltBody = 'This is a plain-text message body';

        //Attach an image file
        #$mail->addAttachment('images/phpmailer_mini.png');

        //send the message, check for errors
        if (!$mail->send()) {
            echo 'Mailer Error: ' . $mail->ErrorInfo;
        } else {
            echo 'Message sent!';
            //Section 2: IMAP
            //Uncomment these to save your message in the 'Sent Mail' folder.
            #if (save_mail($mail)) {
            #    echo "Message saved!";
            #}
        }
    }


    function validation_test(){
        $rules = [
            'nombre' => ['type' => 'alpha_spaces_utf8', 'min'=>3, 'max'=>40],
            'username' => ['type' => 'alpha_dash', 'min'=> 3, 'max' => '15'],
            'rol' => ['type' => 'int', 'not_in' => [2, 4, 5]],
            'poder' => ['not_between' => [4,7] ],
            'edad' => ['between' => [18, 100]],
            'magia' => [ 'in' => [3,21,81] ],
            'is_active' => ['type' => 'bool', 'messages' => [ 'type' => 'Value should be 0 or 1'] ]
        ];
        
        $data = [
            'nombre' => 'Juan Español',
            'username' => 'juan_el_mejor',
            'rol' => 5,
            'poder' => 6,
            'edad' => 101,
            'magia' => 22,
            'is_active' => 3
        ];

        $v = new Validator();
        dd($v->validate($rules,$data));
    }

    function validacion(){
        $u = DB::table('users');
        dd($u->where(['username' => 'nano_'])->get());
    }

    function validacion1(){
        $u = DB::table('users')->setValidator(new Validator());
        $affected = $u->where(['email' => 'nano@'])->update(['firstname' => 'NA']);
    }

    function validacion2(){
        $u = DB::table('users')->setValidator(new Validator());
        $affected = $u->where(['email' => 'nano@'])->update(['firstname' => 'NA']);
    }

    function validacion3(){
        $p = DB::table('products')->setValidator(new Validator());
        $rows = $p->where(['cost' => '100X', 'belongs_to' => 90])->get();

        dd($rows);
    }

    function validacion4(){
        $p = DB::table('products')->setValidator(new Validator());
        $affected = $p->where(['cost' => '100X', 'belongs_to' => 90])->delete();

        dd($affected);
    }
  
    /*
        Intento #1 de sub-consultas en el WHERE

        SELECT id, name, size, cost, belongs_to FROM products WHERE belongs_to IN (SELECT id FROM users WHERE password IS NULL);

    */
    function sub(){
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
    function sub2(){
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
    function sub3(){
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
    function sub3b(){
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

    function sub3c(){
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

    function sub4(){
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

        } catch (\Exception $e){
            dd($e->getMessage());
            dd($m->dd());
        }
    }

    // SELECT  COUNT(*) FROM (SELECT  id, name, size FROM products  WHERE (cost >= ?) AND deleted_at IS NULL) as sub
    function sub4a(){
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

        } catch (\Exception $e){
            dd($e->getMessage());
            dd($m->dd());
        }    
    }


    function sub4b(){
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
    function sub4c(){
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
    function sub4d(){
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
    function sub5(){
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
    function union(){
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

    function union2(){
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
    function union_all(){
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
        ->limit(5)
        ;

        dd($dos->get());
        dd($dos->dd());
    }
       
    function insert_messages() {
        function get_words($sentence, $count = 10) {
            preg_match("/(?:\w+(?:\W+|$)){0,$count}/", $sentence, $matches);
            return $matches[0];
        }

        $m = DB::table('messages');

        for ($i=0; $i<1500; $i++){

            $name = '';
            for ($i=0;$i<10;$i++){
                $name .= chr(rand(97,122));
            }   

            $email = '';
            for ($i=0;$i<20;$i++){
                $email .= chr(rand(97,122));
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
    function some_test() {
       ignore_user_abort(true);
       fastcgi_finish_request();

       echo json_encode(['data' => 'Proceso terminado']);
       header('Connection: close');

       sleep(10);
       file_put_contents('output.txt', date('l jS \of F Y h:i:s A')."\n", FILE_APPEND);
    }

    function json(){
        $id = DB::table('collections')->create([
            'entity' => 'messages',
            'refs' => json_encode([195,196]),
            'belongs_to' => 332
        ]);

        Factory::response()->sendJson($id);
    }


    function get_env(){
        dd($_ENV['APP_NAME']);
        dd($_ENV['APP_URL']);
    }


    function test_get(){
        dd(DB::table('products')->first(), 'FIRST'); 
        dd(DB::getLog(), 'QUERY');
    }

    function test_get_raw(){
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

    function get_role_permissions(){
        $acl = Factory::acl();

        dd($acl->hasResourcePermission('show_all', ['guest'], 'products'));
        //var_export($acl->getRolePermissions());
    }

    function boom(){
        throw new \Exception('BOOOOM');
    }

    function ops(){
        $this->boom();
    }

    function hi($name = null){
        return 'hi ' . $name;
    }

    function speed(){
        Time::setUnit('MILI');
        
        $t1 = Time::exec(function(){ 
            $ttl_res = Url::consume_api('https://mindicador.cl/api', 'GET');
        }); 
        
        dd("Time: $t1 ms");
    }

    function get_con(){
        DB::setConnection('db2');       
        $conn = DB::getConnection();

        $m = new ProductsModel($conn);
    }

    /*
        MySql: show status where `variable_name` = 'Threads_connected
        MySql: show processlist;
    */
    function test_active_connnections(){
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

    function show_databases(){
        $res = DB::select('SHOW DATABASES', null, 'COLUMN');
        dd($res);
    }

    function test_db_select000(){
        DB::getConnection('az');
        
        $tb = 'files';
        $fields = DB::select("SHOW COLUMNS FROM $tb");

        dd($fields);
    }

    function read_table(){
        $tb = 'products';

        $fields = DB::select("SHOW COLUMNS FROM $tb");
        
        $field_names = [];
        $nullables = [];

        foreach ($fields as $field){
            $field_names[] = $field['Field'];
            if ($field['Null']  == 'YES') { $nullables[] = $field['Field']; }
            if ($field['Extra'] == 'auto_increment') { $not_fillable[] = $field['Field']; }
        }

        dd($field_names);
    }

    function zzz(){
        $arr = ['el', 'dia', 'que', 'me', 'quieras'];
        $arr = array_map(function($x){ return "'$x'"; }, $arr);
        
        dd($arr);
        
        //echo implode('-', $arr);
    }

    function speed2(){

        Time::setUnit('MILI');
        //Time::noOutput();
        
        $conn = DB::getConnection();
        $t = Time::exec(function() use ($conn){         
            $sql = "INSERT INTO `baz2` (`name`, `cost`) VALUES ('hhh', '789')";
            $conn->exec($sql);
        }, 1);  
        dd("Time: $t ms");    

        exit;

        $m = (new Model(true))
        ->table('baz2');
        $t = Time::exec(function() use ($m){             
            //$m->setValidator(new Validator());
            //$m->dontExec();

            $id = $m->create([
                'name' => 'BAZ',
                'cost' => '100',
            ]);

        }, 1);  
        dd("Time: $t ms");
        dd($m->getLog());

        /*
        Time::setUnit('MILI');
        //Time::noOutput();

        $this->model_name  = null;
        $this->table_name = 'users';

        $t = Time::exec(function(){ 
            
            $id = DB::table('collections')->create([
                'entity' => 'messages',
                'refs' => json_encode([195,196]),
                'belongs_to' => 332
            ]);

        }, 1);       
        dd("Time: $t ms");
        */
    }


    function speed_show()
    {
        Time::setUnit('MILI');

        $m = (new Model(true))
            ->table('bar')  
            ->where(['uuid', '0fefc2b1-f0d3-47aa-a875-5dbca85855f9'])
            ->select(['uuid', 'price']);

        //dd($m->dd());
        //exit;     

        $t = Time::exec(function() use($m) {
            $row = $m->get();
        }, 1);

        //dd("Time: $t ms");
        Files::logger("Time(show) : $t ms");
    }

    function speed_list()
    {
        Time::setUnit('MILI');

        $m = (new Model(true))
            ->table('bar')  
            ->select(['uuid', 'price'])
            ->take(10);

        //dd($m->dd());
        //exit;         

        $t = Time::exec(function() use($m) {
            $row = $m->get();
        }, 1);

        //dd("Time: $t ms");
        Files::logger("Time(list) : $t ms");
    }

    function get_bulk(){
        $t1a = [];
        $t2a = [];

        Time::setUnit('MILI');

        $m1 = (new Model(true))
            ->table('bar')  
            ->select(['uuid', 'price'])
            ->take(10);

        $m2 = (new Model(true))
            ->table('bar')  
            ->where(['uuid', '0fefc2b1-f0d3-47aa-a875-5dbca85855f9'])
            ->select(['uuid', 'price']);    

        //dd($m->dd());
        //exit;         

        $m3 = DB::select("SELECT AVG(price) FROM bar;");

        for ($i=0; $i<4; $i++){
            $t1a[] = Time::exec(function() use($m1) {
                $m1->get();
            }, 500);

            $t2a[] = Time::exec(function() use($m2) {
                $m2->get();
            }, 500);
        }    
            
        foreach ($t1a as $t1){
            Files::logger("Time(list) : $t1 ms");
        }

        foreach ($t2a as $t2){
            Files::logger("Time(show) : $t2 ms");;
        }    
    }

    /*
        Genera excepción con 
        
        PDO::ATTR_EMULATE_PREPARES] = false

    */
    function test000002(){
        $m = DB::table('products')
        ->where([ 
            ['name', ['Vodka', 'Wisky', 'Tekila','CocaCola']], // IN 
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
    function test000003(){
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

    function test_config(){
        dd(Config::get('db_connection_default'));
        Config::set('db_connection_default', 'db_flor');
        dd(Config::get('db_connection_default'));

        DB::getDefaultConnection(); // -- ok
        dd(DB::select('SELECT * FROM tbl_usuario'));
    }

    /*

        https://www.w3resource.com/mysql/mysql-data-types.php
        https://manuales.guebs.com/mysql-5.0/spatial-extensions.html

    */
    function create_table()
    {       
        //config()['db_connection_default'] = 'db2';
        $sc = (new Schema('facturas'))

        ->setEngine('InnoDB')
        ->setCharset('utf8')
        ->setCollation('utf8mb4_unicode_ci')

        ->integer('id')->auto()->unsigned()->pri()
        ->int('edad')->unsigned()
        ->varchar('firstname')
        ->varchar('lastname')->nullable()->charset('utf8')->collation('utf8_unicode_ci')
        ->varchar('username')->unique()
        ->varchar('password', 128)
        ->char('password_char')->nullable()
        ->varbinary('texto_vb', 300)

        // BLOB and TEXT columns cannot have DEFAULT values.
        ->text('texto')
        ->tinytext('texto_tiny')
        ->mediumtext('texto_md')
        ->longtext('texto_long')
        ->blob('codigo')
        ->tinyblob('blob_tiny')
        ->mediumblob('blob_md')
        ->longblob('blob_long')
        ->binary('bb', 255)
        ->json('json_str')

        
        ->int('karma')->default(100)
        ->int('code')->zeroFill()
        ->bigint('big_num')
        ->bigint('ubig')->unsigned()
        ->mediumint('medium')
        ->smallint('small')
        ->tinyint('tiny')
        ->decimal('saldo')
        ->float('flotante')
        ->double('doble_p')
        ->real('num_real')

        ->bit('some_bits', 3)->index()
        ->boolean('is_active')->default(1)
        ->boolean('paused')->default(true)

        ->set('flavors', ['strawberry', 'vanilla'])
        ->enum('role', ['admin', 'normal'])


        /*
            The major difference between DATETIME and TIMESTAMP is that TIMESTAMP values are converted from the current time zone to UTC while storing, and converted back from UTC to the current time zone when accessd. The datetime data type value is unchanged.
        */

        ->time('hora')
        ->year('birth_year')
        ->date('fecha')
        ->datetime('vencimiento')->nullable()->after('num_real') /* no está funcionando el AFTER */
        ->timestamp('ts')->currentTimestamp()->comment('some comment') // solo un first


        ->softDeletes() // agrega DATETIME deleted_at 
        ->datetimes()  // agrega DATETIME(s) no-nullables created_at y deleted_at

        ->varchar('correo')->unique()

        ->int('user_id')->index()
        ->foreign('user_id')->references('id')->on('users')->onDelete('cascade')
        //->foreign('user_id')->references('id')->on('users')->constraint('fk_uid')->onDelete('cascade')->onUpdate('restrict')
        
        ;

        //dd($sc->getSchema(), 'SCHEMA');
        /////exit;

        $res = $sc->create();
        dd($res, 'Succeded?');
        //var_dump($sc->dd());
    }    

    function alter_table()
    { 
        Schema::FKcheck(false);
        
        $sc = new Schema('facturas');
        //var_dump($sc->columnExists('correo'));
       
        $res = $sc

        
        //->timestamp('vencimiento')
        //->varchar('lastname', 50)->collate('utf8_esperanto_ci')
        //->varchar('username', 50)
        //->column('ts')->nullable()
        //->field('deleted_at')->nullable()
        //->column('correo')->unique()
        // ->field('correo')->default(false)->nullable(true)
        

        //->renameColumn('karma', 'carma')
        ->field('id')->index()
        //->renameIndex('id', 'idx')
        //->dropColumn('saldo')
        //->dropIndex('correo')
        //->dropPrimary('id')
        //->renameTable('boletas')
        //->dropTable()

        //->field('password_char')->default(false)->nullable(false)
        

        /*
         creo campos nuevos
        */
        
        //->varchar('nuevo_campito', 50)->nullable()->after('ts')
        //->text('aaa')->first()->nullable()

        //->dropFK('facturas_ibfk_1')
        //->foreign('user_id')->references('id')->on('users')->onDelete('cascade')->onUpdate('restrict')
                
        //->field('id')->auto(false)
        

        ->change();

        Schema::FKcheck(true);
        dd($sc->dd());
    }

    function has_table(){
        dd(Schema::hasTable('users'));
        dd((new Schema('users'))->tableExists());
    }

    function xxy(){
        $str = "`lastname` varchar(60) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT 'NN',
        ";
        dd($str, 'STR');

        $charset    = Strings::slice($str, '/CHARACTER SET ([a-z0-9_]+)/');
        dd($charset, 'CHARSET');
        dd($str, 'STR');

        $collation  = Strings::slice($str, '/COLLATE ([a-z0-9_]+)/');
        dd($collation, 'COLLATION');
        dd($str, 'STR');
        
        $default    = Strings::slice($str, '/DEFAULT (\'?[a-zA-Z0-9_]+\'?)/');
        dd($default, "DEFAULT");
        dd($str, 'STR');

        $nullable   = Strings::slice($str, '/(NOT NULL)/') == NULL;
        dd($nullable, "NULLABLE");
        dd($str, 'STR');

        $auto       = Strings::slice($str, '/(AUTO_INCREMENT)/') == 'AUTO_INCREMENT';
        dd($auto, "AUTO");
        dd($str, 'STR');
    }

    function xxxz(){
        $str = 'CONSTRAINT `facturas_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE';
        dd($str, 'STR');

        $constraint = Strings::slice($str, '/CONSTRAINT `([a-zA-Z0-9_]+)` /', function($s){
            //var_dump($s);
                return ($s == null) ? 'DEFAULT' : $s;
            }); 

        dd($constraint, 'CONSTRAINT');    

        //dd($constraint);
        //exit; //
        dd($str, 'STR');

        $primary = Strings::slice($str, '/PRIMARY KEY \(([a-zA-Z0-9_`,]+)\)/');
        dd($str, 'STR');	
        dd($primary, 'PRIMARY');

        /*

        Compuesto:
        UNIQUE KEY `correo` (`correo`,`hora`) USING BTREE,

        */
        $unique  = Strings::sliceAll($str, '/UNIQUE KEY `([a-zA-Z0-9_]+)` \(([a-zA-Z0-9_`,]+)\)/');
        dd($str, 'STR');	
        dd($unique, 'UNIQUE');					

        /*
            CONSTRAINT `facturas_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCAD

        */
        $fk     = Strings::slice($str, '/FOREIGN KEY \(`([a-zA-Z0-9_]+)`\)/');
        $fk_ref = Strings::slice($str, '/REFERENCES `([a-zA-Z0-9_]+)`/');

        dd($str, 'STR');	
        dd($fk, 'FK');
        dd($fk, 'REFERENCES');

        /*
        IDEM
        */
        $index   = Strings::sliceAll($str, '/KEY `([a-zA-Z0-9_]+)` \(([a-zA-Z0-9_`,]+)\)/');
        dd($str, 'STR');
        dd($index, 'INDEX');


    }

    function test_get_conn(){
        DB::setConnection('db_flor');
        dd(DB::select('SELECT * FROM tbl_usuario'));
    }

    function test_conn2(){
        config()['db_connection_default'] = 'db2';

        $sc = new Schema('cables');

        $sc
        ->int('id')->unsigned()->auto()->pri()
        ->varchar('nombre', 40)
        ->float('calibre')

        ->create();
    }

    function test_route(){
        /*
        Route::get('dumbo/kalc', function(){
            echo 'Hello from Kalc!';
        })->name('dumbo.kalc');
        
        
        Route::get('has_table', 'DumbController@has_table')
        ->name('dumbo.has_table');
        */
    
        //dd(route('dumbo.has_table'), 'URL');
        //dd(route('dumbo.kalc'), 'URL');
    }

    function curl(){
        define('HOST', $this->config['APP_URL']);
        define('BASE_URL', HOST .'/');

        $url = BASE_URL . "api/v1/auth/login";

        $credentials = [
            'email' => "tester3@g.c",
            'password' => "gogogo8"
        ];

        if ($credentials == []){
            throw new \Exception("Empty credentials");
        }

		$data = json_encode($credentials);

        $com = <<<HDOC
        curl -s --location --request POST '$url' \
        --header 'Content-Type: text/plain' \
        --data-raw '$data' /tmp/output.html
        HDOC;

        $response = json_decode(exec($com), true);

        $data      = $response['data'] ?? [];
        $http_code = $response['status_code'];
        $error_msg = $response['error'];

       dd($data, 'data');
       dd($http_code, 'http code');
       dd($error_msg, 'error');
    }       

    function respuesta(){
        Factory::response()->sendError('Acceso no autorizado', 401, 'Header vacio');
    }
 
    function test_error(){
        Factory::response()->sendError("No encontrado", 404, "El recurso no existe");  
    }

    function test_get_rels(){
        $table = 'books';
        
        $relations = Schema::getRelations($table);
        dd($relations);
    }

    function testsp(){
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

        $stmt->bindParam(1, $data['p_nombre'], \PDO::PARAM_STR, 50);
        $stmt->bindParam(2, $data['p_email'], \PDO::PARAM_STR, 60);
        $stmt->bindParam(3, $data['p_usuario'], \PDO::PARAM_STR, 50);
        $stmt->bindParam(4, $data['p_password'], \PDO::PARAM_STR, 50);
        $stmt->bindParam(5, $data['p_basedatos'], \PDO::PARAM_STR, 20);
   
        $res = $stmt->execute();
  
        if (!$res){
            throw new \Exception("No se pudo crear usuario {$data['p_usuario']}");
        }

        $sql = 'CALL sp_ejecucion_script(?)';
        $stmt = $conn->prepare($sql);

        $stmt->bindParam(1, $data['p_email'], \PDO::PARAM_STR, 60);
        $res = $stmt->execute();

        dd($res);
    }
   

    /*
        Basic GET, https
    */
    function test_api00(){
        $res = Url::consume_api('https://jsonplaceholder.typicode.com/posts', 'GET', null, null, null, true);
        dd($res);  
    }

    /*
        Basic GET, http
    */
    function test_api01(){
        $res = Url::consume_api('http://jsonplaceholder.typicode.com/posts', 'GET', null, null, null, true);
        dd($res);  
    }

    function test_api01a(){
        $res = Url::consume_api('http://34.204.139.241:8084/api/Home', 'GET', null, [
            'Accept' => 'text/plain'
        ]);
        dd($res);  
    }

    function test_api01b(){
        $res = Url::consume_api('http://34.204.139.241:8084/api/Home', 'GET', null, null, null, false);
        dd($res);   
    }

    function test_api02(){
        $data = '{
            "userId": 1,
            "title": "Some title",
            "body": "Some long description"
          }';
    
        $res = Url::consume_api('https://jsonplaceholder.typicode.com/posts', 'POST', $data);
        dd($res);  
    }

    function test_api03(){
        $data = [
            "userId" => 1,
            "title" => "Some title",
            "body" => "Other long description"
        ];
    
        $res = Url::consume_api('https://jsonplaceholder.typicode.com/posts', 'POST', $data);
        dd($res);  
    }

    function test_api04(){
        $xml_file = file_get_contents(ETC_PATH . 'ad00148980970002000000067.xml');

        $response = Url::consume_api('http://localhost/pruebas/get_xml.php', 'POST', $xml_file, [
            "Content-type" => "text/xml"
        ]);

        dd($response, 'RES');
    }

    // debe responderme con erro y un body de respuesta -- ok
    function test_api05(){
        $response = Url::consume_api('http://localhost/pruebas/get_error.php', 'POST', null, [
            "Content-type" => "text/xml"
        ]);

        dd($response, 'RES');
    }

    function test_api06(){
        $response = Url::consume_api("https://onesignal.com/api/v1/notifications", 'POST', ['x' => 'y'], 
            [             
                'Content-Type: application/json',
                'Authorization: Basic ' . 'xxxxxxxxxxxx'
            ],  
            
            [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_HEADER => false
            ]
        );

        dd($response, 'RES');
    }

    function parse_class(){
        $path = MIGRATIONS_PATH . '2021_09_14_27905675_user_sp_permissions.php';
        $file = file_get_contents($path);

        dd(Strings::getClassNameByFileName($file));
    }

   
    function rr(){
        // DB::getConnection('db_flor');
        // $rels = Schema::getRelations('tbl_estado_civil');

        DB::getConnection('az');
        $rels = Schema::getRelations('books');        

        dd($rels);
    }

    function rels(){
        // DB::getConnection('db_flor');
        // $rels = Schema::getAllRelations('tbl_estado_civil', false);

        // DB::getConnection('az');
        // $rels = Schema::getAllRelations('books', false);   
        
        DB::getConnection('db_flor');
        $rels = Schema::getAllRelations('tbl_sub_cuenta_contable', false);  

        dd($rels);
    }

    function autojoins(){
        DB::getConnection('db_flor');

        $rows = DB::table('tbl_estado_civil')
        ->join('tbl_usuario')
        ->join('tbl_estado')
        ->get();

        dd($rows);
    }

    function serve_assets(){
        view('test_assets.php');
    }

    function test_base_url(){
        dd(Url::currentUrl());
        dd(Url::getBaseUrl());
    }
    

    function x(){
        dd(asset("jota.jpg"));
    }

    function dir(){
        $path = '/home/www/simplerest/app/migrations';

        $dir  = new \RecursiveDirectoryIterator($path, \RecursiveDirectoryIterator::SKIP_DOTS);
        $files = new \RecursiveIteratorIterator($dir, \RecursiveIteratorIterator::SELF_FIRST);

        echo "[$path]\n";
        foreach ($files as $file) {
            $indent = str_repeat('   ', $files->getDepth());
            echo $indent, " ├ $file\n";
        }
    }

    /*
        Genera migraciones a partir de la tabla 'tbl_scritp_tablas'
    */
    function gen_scripts(){
        $mk = new MakeControllerBase();

        $rows = DB::table('tbl_scritp_tablas')
        ->orderBy(['scr_intOrden' => 'ASC'])
        ->get();

        foreach ($rows as $row){
            $orden = str_pad($row['scr_intOrden'], 4, "0", STR_PAD_LEFT);
            $name  = strtolower("$orden-{$row['scr_varNombre']}-{$row['scr_varModulo']}");
            $script = $row['scr_lonScritp'];

            $folder = "compania";

            $class_name = Strings::snakeToCamel("{$row['scr_varNombre']}_{$row['scr_varModulo']}_{$row['scr_intOrden']}");

            $mk->migration("$name", "--dir=$folder", "--from_script=\"$script\"", "--class_name=$class_name");
        }
    }

    function mid(){
        return "Hello World!";        
    }

    function update(){
        $data = [ "est_varColor" => "rojo" ];

        DB::setConnection('db_flor');

        $affected = DB::table('tbl_estado')->where(["est_intId" => 1])->update($data);
        dd($affected);
    }

    function migrate(){
        $mgr = new MigrationsController();

        $folder = 'compania';
        $tenant = 'db_100';

        StdOut::hideResponse();

        $mgr->migrate("--dir=$folder", "--to=$tenant");
    }

    function get_pks(){
        d(Schema::getPKs('boletas'));
    }

    function get_db(){
        d(Schema::getCurrentDatabase());    
    }

    function get_autoinc(){
        d(Schema::getAutoIncrement('book_reviews'));
        d(Schema::getAutoIncrement('bar'));
        d(Schema::hasAutoIncrement('bar'));
    }

    function test_alter_table(){
        DB::setConnection('az');

		//$sc = new Schema('boletas');        
        //$sc->field('id')->primary();
        
        $sc = new Schema('bar');
		$sc
        ->field('ts')
        ->renameColumnTo('times2');

        $sc->field('f1')->primary();
        $sc->field('f2')->primary();
        $sc->field('f3')->primary();
        
        $sc->dontExec();
        $sc->alter();

        d($sc->getSchema(), 'SCHEMA');
        d($sc->dd(), 'SQL');
    }

    function test_alter_table2(){
        DB::setConnection('az');
        
        $sc = new Schema('boletas');

        $sc->field('f1')->primary();
        $sc->field('f2')->primary();

        $sc->dontExec();
        $sc->alter();

        d($sc->getSchema(), 'SCHEMA');
        d($sc->dd(true), 'SQL');
    }
    
    function test_alter_table3(){
        DB::setConnection('az');

		$sc = new Schema('boletas');        
        
        $sc->dropPrimary();

        //$sc->dontExec();
        $sc->alter();

        d($sc->getSchema(), 'SCHEMA');
        d($sc->dd(true), 'SQL');
    }

    function test_alter_table4(){
        $sc = new Schema('boletas');
        $sc
        ->dontExec()
        ->dropAuto()
        ->alter();

        dd($sc->dd(), 'SQL');
    }

    function get_auto_field(){
        d(Schema::getAutoIncrementField('book_reviews'));
        d(Schema::getAutoIncrementField('bar'));
    }
    
    function mk(){
        $tenant = "db_100";
        
        StdOut::hideResponse();

        $mk = new MakeControllerBase();
		$mk->any("all", "-s", "-m", "--from:$tenant");        
    }

    function error(){
        response()->sendError("Todo mal", 400);
    }

    function is_type(){ 
        dd(Validator::isType('8', 'str'));
        dd(Validator::isType(8, 'str'));
    }

    function validate_data($str){
        dd(Validator::isType($str, 'date'));
    }

    function get_pri(){
        return get_primary_key('products', 'az');
    }

    function test_sub_res(){
        DB::getConnection('az');

        global $api_version;
        
        $api_version = 'v1';
        
        $connect_to = Products::getConnectable();
        $instance   = null;
        $table = 'products';

        $res = Products::getSubResources($table, $connect_to, $instance, null, 145);
        dd($res);
    }

    function test_sub_res2(){
        DB::getConnection('az');

        global $api_version;
        
        $api_version = 'v1';
        
        $connect_to = Products::getConnectable();
        $instance   = null;
        $table = 'products';

        $res = Products::getSubResources($table, $connect_to, $instance);
        dd($res);
    }

    function test_sub_res3(){
        DB::getConnection('db_flor');

        global $api_version;
        
        $api_version = 'v1';
        
        $connect_to = TblPersona::getConnectable();
        $instance   = null;
        $table = 'tbl_persona';

        $res = Products::getSubResources($table, $connect_to, $instance);
        dd($res);
    }

    
    function test_create_user(){
        $m = DB::table('users')
        ->setValidator(new Validator())
        ->fill(['password', 'created_at']);

        // dd($m->getFillables(), 'FILLABLES');
        // dd($m->getNotFillables(), 'NOT FILLABLES');
         
        $data = json_decode('{
            "username": "u200",
            "email": "u200@mail.com",
            "password": "gogogo",
            "created_at": "2019-02-02 10:00:00"
        }', true);


        $ok = $m
        ->create($data);

        dd($ok);
    }

    /*
        create()
    */
    function create000(){
        DB::getConnection('az');

        $data = array (
            'name' => 'bbb',
            'comment' => 'positivo',
            'product_id' => 100
        );

        $id = DB::table('product_tags')
        ->create($data);

        dd($id);
        dd(DB::getLog());
    }

    /*
        create()

        De momento cada INSERT se ejecuta por separado
    */
    function create_as_insert_mul(){
        DB::getConnection('az');

        $data = [
            array (
                'name' => 'N1x',
                'comment' => 'P1x',
                'product_id' => 100
            ),

            array (
                'name' => 'N2x',
                'comment' => 'P2x',
                'product_id' => 103
            ),

            array (
                'name' => 'N3x',
                'comment' => 'P3x',
                'product_id' => 105
            )
        ];

        $m = DB::table('product_tags');

        $id = $m
        ->create($data);

        dd($id);
        d($m->getLog());
    }

    /*
        create()
    */
    function create_as_insert_mul2(){
        DB::getConnection('az');

        $data = [
            array (
                'name' => 'N1',
                'comment' => 'P1',
                'product_id' => 100
            ),

            array (
                'name' => 'N2',
                'comment' => 'P2',
                'product_id' => 103
            ),

            array (
                'name' => 'N3',
                'comment' => 'P3',
                'product_id' => 105
            ),

            array (
                'name' => 'N4',
                'comment' => '',
                'product_id' => 105
            )
        ];

        $id = DB::table('product_tags')
        ->create($data);

        dd($id);
    }

    /*
        insert()

        1 row
    */
    function insert_base(){
        DB::getConnection('az');

        $data = array (
            'name' => 'bbb',
            'comment' => 'positivoOOo',
            'product_id' => 100
        );

        $m = DB::table('product_tags');

        $id = $m
        ->insert($data, false, false);

        dd($id);
        d($m->getLog());
    }

    /*
        insert()

        varios rows
    */        
    function insert_base2(){
        DB::getConnection('az');

        $data = [
            array (
                'name' => 'N1',
                'comment' => 'P1',
                'product_id' => 100
            ),

            array (
                'name' => 'N2',
                //'comment' => 'P2',
                'product_id' => 103
            ),

            array (
                'name' => 'N3',
                'comment' => 'P3',
                'product_id' => 105
            ),

            array (
                'name' => 'N4',
                'comment' => '',
                'product_id' => 105
            )
        ];

        $m = DB::table('product_tags');

        $id = $m
        ->insert($data, false, false);

        dd($id);
        d($m->getLog());
    }

    /*
        insert()
    */
    function insert_no_base2(){
        DB::getConnection('az');

        $data = [
            array (
                'name' => 'N1',
                'comment' => 'P1',
                'product_id' => 100
            ),

            array (
                'name' => 'N2',
                //'comment' => 'P2',
                'product_id' => 103
            ),

            array (
                'name' => 'N3',
                'comment' => 'P3',
                'product_id' => 105
            ),

            array (
                'name' => 'N4',
                'comment' => '',
                'product_id' => 105
            )
        ];

        $id = DB::table('product_tags')
        ->insert($data, true);

        dd($id);
    }

    // intento de inserción en tabla puente
    function test_insert_bridge()
    {
        $data = array (
            'product_id' => '145',
            'valoracion_id' => '9',
            'created_at' => at()
        );

        $mbr = DB::table('product_valoraciones');
        $ok = $mbr->create($data);

        d($ok, 'Ok?');
    }

    /*
        insert()
    */
    function insert_mul(){
        DB::getConnection('az');

        $m = DB::table('bar');

        $data = [];
        for ($i=0; $i<5; $i++){
            $name = '    ';
            for ($i=0;$i<46;$i++)
                $name .= chr(rand(97,122));

            $name = str_shuffle($name);

            $email = '@';
            $cnt = rand(10,78);
            for ($i=0;$i<$cnt;$i++)
                $email .= chr(rand(97,122));    

            $email =  chr(rand(97,122)) . str_shuffle($email);

            $data[] = [
                'name' => $name,
                'price' => rand(5,999) . '.' . rand(0,99),
                'email' => $email,
                'belongs_to' => 1
            ];
        }

        $id = $m->insert($data);
        
        dd($data, 'DATA');
        dd($id, 'ID');
    }

    /*
        Inserción de un solo row
        con insert()
    */
    function insert_one_row(){
        DB::getConnection('az');

        $name = '';
        for ($i=0;$i<20;$i++){
            $name .= chr(rand(97,122));
        }

        $m = DB::table('products');

        $id = $m
        ->insert([ 
            'name' => $name, 
            'description' => 'Esto es una prueba 770', 
            'size' => '100L',
            'cost' => 66,
            'belongs_to' => 90,
            'digital_id' => 2   
        ]);   
        
        dd($id);
        dd($m->dd(true));
    }

    /*
        Inserción de múltiples rows
        con insert()
    */
    function insert_mul_rows(){
        DB::getConnection('az');

        $data = [];
        for ($j=0;$j<5;$j++){
            $name = '';

            for ($i=0;$i<20;$i++){
                $name .= chr(rand(97,122));
            }    

            $desc = str_shuffle($name);
            $cost = rand(50,150);

            /*
                Preparo múltiples rows a ser insertadas
            */
            $data[] = [ 
                'name' => $name, 
                'description' => $desc, 
                'size' => '100L',
                'cost' => $cost,
                'belongs_to' => 90,
                'digital_id' => 1
            ];
        }

        $id = DB::table('products')
        ->insert($data, true); 

        dd($id);
    }

    function get_all_rels(){
        DB::getConnection('db_flor');
        $data = Schema::getAllRelations('tbl_producto');

        dd($data);
    }

    function pivot(){
        dd(get_pivot(['products', 'comments'], 'az'));
        //dd(get_pivot(['products', 'comments']));
        dd(get_pivot(['roles', 'tbl_usuario_empresa'], 'main'));
    }

    function show_dbs(){
        DB::getDefaultConnection();

        dd(DB::getConnectionConfig());

        $rows = DB::table('tbl_base_datos')
        ->get();

        dd($rows);
    }

    function show_dbs2(){
        $conn = DB::getDefaultConnection();
    
        $m = (new Model(false, null, false))
        ->setConn($conn);
        
        $bases = $m->table('tbl_base_datos')
        ->pluck('dba_varNombre');

        dd($bases);
    }

    function get_tables(){
        DB::getConnection('db_flor');

        $tables = Schema::getTables();
        d($tables, 'TABLES');
    }

    function drop_all_tables(string $db_conn_id){
        dd("DROPPING ALL TABLES FROM `$db_conn_id`");

        DB::getConnection($db_conn_id);

        $tables = Schema::getTables();

        DB::disableForeignKeyConstraints();

        foreach ($tables as $tb){
            $ok = Schema::dropIfExists($tb);
            dd($ok, "DROP TABLE $tb");
        }
        
        DB::enableForeignKeyConstraints();
    }

    function drop_all_tables_last_db(){
        $conn = DB::getDefaultConnection();
       
        $db = DB::table('tbl_base_datos')
        ->orderBy(['dba_intId' => 'DESC'])
        ->first(['dba_varNombre'])['dba_varNombre'];

        $this->drop_all_tables($db);
    }

    function gr(){
        // $tenant = 'az';
        // $tb = 'products';

        // $tenant = 'db_flor';
        // $tb = 'tbl_producto';

        $tenant = 'db_flor';
        $tb = 'tbl_tipo_persona';


        DB::getConnection($tenant);
        //dd(Schema::getFKs($tb));
        dd(Schema::getAllRelations($tb, true, false));
    }


    function pivote(){
        $tenant_id = 'az';

        // $t1 = 'products';
        // $t2 = 'valoraciones';

        // $pivot = get_pivot([
        //     $t1, $t2
        // ], $tenant_id);

        // dd($pivot, "PIVOT for $t1~$t2");

        // /////////////////////////////////

        // $t1 = 'valoraciones';
        // $t2 = 'products';

        // $pivot = get_pivot([
        //     $t1, $t2
        // ], $tenant_id);

        // dd($pivot, "PIVOT for $t1~$t2");

        /////////////////////////////////


        // $t1 = 'products';
        // $t2 = 'product_comments';

        // $pivot = get_pivot([
        //     $t1, $t2
        // ], $tenant_id);

        // dd($pivot, "PIVOT for $t1~$t2");

        /////////////////////////////////


        /// Acá se confunde !!!!!!!!!!!!!
        $tenant_id = 'db_flor';

        $t1 = 'tbl_persona';
        $t2 = 'tbl_usuario';

        $pivot = get_pivot([
            $t1, $t2
        ], $tenant_id);

        dd($pivot, "PIVOT for $t1~$t2");

        /////////////////////////////////

    }


    function get_rels(){
        $tenant_id = 'az';

        $t1 = 'products';
        $t2 = 'users';
        $rel_str = null;

        dd(get_rels($t1, $t2, '1:1',  $tenant_id), "1:1 para $t1~$t2"); 
        dd(get_rels($t1, $t2, '1:n',  $tenant_id), "1:n para $t1~$t2"); 
        dd(get_rels($t1, $t2, 'n:1',  $tenant_id), "n:1 para $t1~$t2"); 
        dd(get_rels($t1, $t2, 'n:m',  $tenant_id), "n:m para $t1~$t2");
        dd(get_rel_type($t1, $t2, $rel_str, $tenant_id), 'REL TYPE');

        dd('----------------------x----------------------');
        exit;///////

        
        $t1 = 'u';
        $t2 = 'u_settings';
        dd(get_rels($t1, $t2, '1:1',  $tenant_id), "1:1 para $t1~$t2");
        dd(get_rels($t1, $t2, '1:n',  $tenant_id), "1:n para $t1~$t2");
        dd(get_rels($t1, $t2, 'n:m',  $tenant_id), "n:m para $t1~$t2");

        dd('----------------------x----------------------');

        $t1 = 'u_settings';
        $t2 = 'u';
        dd(get_rels($t1, $t2, '1:1',  $tenant_id), "1:1 para $t1~$t2");
        dd(get_rels($t1, $t2, '1:n',  $tenant_id), "1:n para $t1~$t2");
        dd(get_rels($t1, $t2, 'n:m',  $tenant_id), "n:m para $t1~$t2");

        dd('----------------------x----------------------');


        $t1 = 'products';
        $t2 = 'product_categories';

        dd(get_rels($t1, $t2, '1:1',  $tenant_id), "1:1 para $t1~$t2"); 
        dd(get_rels($t1, $t2, '1:n',  $tenant_id), "1:n para $t1~$t2"); 
        dd(get_rels($t1, $t2, 'n:1',  $tenant_id), "n:1 para $t1~$t2"); 
        dd(get_rels($t1, $t2, 'n:m',  $tenant_id), "n:m para $t1~$t2");
        dd(get_rel_type($t1, $t2, null, $tenant_id), 'REL TYPE');

        dd('----------------------x----------------------');

        $t1 = 'product_categories';
        $t2 = 'products';
        
        dd(get_rels($t1, $t2, '1:1',  $tenant_id), "1:1 para $t1~$t2"); 
        dd(get_rels($t1, $t2, '1:n',  $tenant_id), "1:n para $t1~$t2"); 
        dd(get_rels($t1, $t2, 'n:1',  $tenant_id), "n:1 para $t1~$t2"); 
        dd(get_rels($t1, $t2, 'n:m',  $tenant_id), "n:m para $t1~$t2");
        dd(get_rel_type($t1, $t2, null, $tenant_id), 'REL TYPE');

        dd('----------------------x----------------------');

        //Ahora especificiando la relación

        $t1 = 'product_categories';
        $t2 = 'products';
        $rel_str = 'product_categories.id_catego=products.category';
        
        dd(get_rels($t1, $t2, '1:1',  $tenant_id, $rel_str), "1:1 para $t1~$t2"); 
        dd(get_rels($t1, $t2, '1:n',  $tenant_id, $rel_str), "1:n para $t1~$t2"); 
        dd(get_rels($t1, $t2, 'n:1',  $tenant_id, $rel_str), "n:1 para $t1~$t2"); 
        dd(get_rels($t1, $t2, 'n:m',  $tenant_id, $rel_str), "n:m para $t1~$t2");
        dd(get_rel_type($t1, $t2, null, $tenant_id), 'REL TYPE');

        dd('----------------------x----------------------');


        //Ahora especificiando la relación pero de forma inversa

        $t1 = 'product_categories';
        $t2 = 'products';
        $rel_str = 'products.category=product_categories.id_catego';
        
        dd(get_rels($t1, $t2, '1:1',  $tenant_id, $rel_str), "1:1 para $t1~$t2"); 
        dd(get_rels($t1, $t2, '1:n',  $tenant_id, $rel_str), "1:n para $t1~$t2"); 
        dd(get_rels($t1, $t2, 'n:1',  $tenant_id, $rel_str), "n:1 para $t1~$t2"); 
        dd(get_rels($t1, $t2, 'n:m',  $tenant_id, $rel_str), "n:m para $t1~$t2");
        dd(get_rel_type($t1, $t2, null, $tenant_id), 'REL TYPE');

        dd('----------------------x----------------------');


        //Ahora especificiando la relación pero trocando $t1 y $t2

        $t1 = 'products';
        $t2 = 'product_categories';
        $rel_str = 'product_categories.id_catego=products.category';
        
        dd(get_rels($t1, $t2, '1:1',  $tenant_id, $rel_str), "1:1 para $t1~$t2"); 
        dd(get_rels($t1, $t2, '1:n',  $tenant_id, $rel_str), "1:n para $t1~$t2"); 
        dd(get_rels($t1, $t2, 'n:1',  $tenant_id, $rel_str), "n:1 para $t1~$t2"); 
        dd(get_rels($t1, $t2, 'n:m',  $tenant_id, $rel_str), "n:m para $t1~$t2");
        dd(get_rel_type($t1, $t2, null, $tenant_id), 'REL TYPE');

        dd('----------------------x----------------------');


        //Ahora especificiando la relación pero de forma inversa ero trocando $t1 y $t2

        $t1 = 'products';
        $t2 = 'product_categories';
        $rel_str = 'products.category=product_categories.id_catego';
        
        dd(get_rels($t1, $t2, '1:1',  $tenant_id, $rel_str), "1:1 para $t1~$t2"); 
        dd(get_rels($t1, $t2, '1:n',  $tenant_id, $rel_str), "1:n para $t1~$t2"); 
        dd(get_rels($t1, $t2, 'n:1',  $tenant_id, $rel_str), "n:1 para $t1~$t2"); 
        dd(get_rels($t1, $t2, 'n:m',  $tenant_id, $rel_str), "n:m para $t1~$t2");
        dd(get_rel_type($t1, $t2, null, $tenant_id), 'REL TYPE');

        dd('----------------------x----------------------');



        $tenant_id = 'db_flor';
        $t1 = 'tbl_producto';
        $t2 = 'tbl_sub_cuenta_contable';

        dd(get_rels($t1, $t2, '1:1',  $tenant_id), "1:1 para $t1~$t2"); 
        dd(get_rels($t1, $t2, '1:n',  $tenant_id), "1:n para $t1~$t2"); 
        dd(get_rels($t1, $t2, 'n:1',  $tenant_id), "n:1 para $t1~$t2"); 
        dd(get_rels($t1, $t2, 'n:m',  $tenant_id), "n:m para $t1~$t2");
        dd(get_rel_type($t1, $t2, null, $tenant_id), 'REL TYPE');

        dd('----------------------x----------------------');

        // órden de tablas trocado

        $t2 = 'tbl_sub_cuenta_contable';
        $t1 = 'tbl_producto';
        
        dd(get_rels($t1, $t2, '1:1',  $tenant_id), "1:1 para $t1~$t2"); 
        dd(get_rels($t1, $t2, '1:n',  $tenant_id), "1:n para $t1~$t2"); 
        dd(get_rels($t1, $t2, 'n:1',  $tenant_id), "n:1 para $t1~$t2"); 
        dd(get_rels($t1, $t2, 'n:m',  $tenant_id), "n:m para $t1~$t2");
        dd(get_rel_type($t1, $t2, null, $tenant_id), 'REL TYPE');

        dd('----------------------x----------------------');

        //Ahora especificiando la relación

        $t1 = 'tbl_producto';
        $t2 = 'tbl_sub_cuenta_contable';
        $rel_str = 'tbl_producto.sub_intIdCuentaContableCompra=tbl_sub_cuenta_contable.sub_intId';
        
        dd(get_rels($t1, $t2, '1:1',  $tenant_id, $rel_str), "1:1 para $t1~$t2"); 
        dd(get_rels($t1, $t2, '1:n',  $tenant_id, $rel_str), "1:n para $t1~$t2"); 
        dd(get_rels($t1, $t2, 'n:1',  $tenant_id, $rel_str), "n:1 para $t1~$t2"); 
        dd(get_rels($t1, $t2, 'n:m',  $tenant_id, $rel_str), "n:m para $t1~$t2");
        dd(get_rel_type($t1, $t2, $rel_str, $tenant_id), 'REL TYPE');

        dd('----------------------x----------------------');


        //Ahora especificiando la relación pero de forma inversa

        $t1 = 'tbl_producto';
        $t2 = 'tbl_sub_cuenta_contable';
        $rel_str = 'tbl_sub_cuenta_contable.sub_intId=tbl_producto.sub_intIdCuentaContableCompra';
        
        dd(get_rels($t1, $t2, '1:1',  $tenant_id, $rel_str), "1:1 para $t1~$t2"); 
        dd(get_rels($t1, $t2, '1:n',  $tenant_id, $rel_str), "1:n para $t1~$t2"); 
        dd(get_rels($t1, $t2, 'n:1',  $tenant_id, $rel_str), "n:1 para $t1~$t2"); 
        dd(get_rels($t1, $t2, 'n:m',  $tenant_id, $rel_str), "n:m para $t1~$t2");
        dd(get_rel_type($t1, $t2, $rel_str, $tenant_id), 'REL TYPE');

        dd('----------------------x----------------------');


        // IDEM anterior por con otra relación entre las mismas tablas

        $t1 = 'tbl_producto';
        $t2 = 'tbl_sub_cuenta_contable';
        $rel_str = 'tbl_sub_cuenta_contable.sub_intId=tbl_producto.sub_intIdCuentaContableVenta';
        
        dd(get_rels($t1, $t2, '1:1',  $tenant_id, $rel_str), "1:1 para $t1~$t2"); 
        dd(get_rels($t1, $t2, '1:n',  $tenant_id, $rel_str), "1:n para $t1~$t2"); 
        dd(get_rels($t1, $t2, 'n:1',  $tenant_id, $rel_str), "n:1 para $t1~$t2"); 
        dd(get_rels($t1, $t2, 'n:m',  $tenant_id, $rel_str), "n:m para $t1~$t2");
        dd(get_rel_type($t1, $t2, $rel_str, $tenant_id), 'REL TYPE');

        dd('----------------------x----------------------');



        // Trocando $t1 y $t2

        $t1 = 'tbl_sub_cuenta_contable';
        $t2 = 'tbl_producto';
        $rel_str = null;
        
        dd(get_rels($t1, $t2, '1:1',  $tenant_id, $rel_str), "1:1 para $t1~$t2"); 
        dd(get_rels($t1, $t2, '1:n',  $tenant_id, $rel_str), "1:n para $t1~$t2"); 
        dd(get_rels($t1, $t2, 'n:1',  $tenant_id, $rel_str), "n:1 para $t1~$t2"); 
        dd(get_rels($t1, $t2, 'n:m',  $tenant_id, $rel_str), "n:m para $t1~$t2");
        dd(get_rel_type($t1, $t2, null, $tenant_id), 'REL TYPE');

        dd('----------------------x----------------------');

        $tenant_id = 'az';

        $t1 = 'products';
        $t2 = 'valoraciones';
        $rel_str = null;

        dd(get_rels($t1, $t2, '1:1',  $tenant_id), "1:1 para $t1~$t2"); 
        dd(get_rels($t1, $t2, '1:n',  $tenant_id), "1:n para $t1~$t2"); 
        dd(get_rels($t1, $t2, 'n:1',  $tenant_id), "n:1 para $t1~$t2"); 
        dd(get_rels($t1, $t2, 'n:m',  $tenant_id), "n:m para $t1~$t2");
        dd(get_rel_type($t1, $t2, $rel_str, $tenant_id), 'REL TYPE');

        dd('----------------------x----------------------');

        $t1 = 'roles';
        $t2 = 'user_roles';
        $rel_str = null;

        dd(get_rels($t1, $t2, '1:1',  $tenant_id), "1:1 para $t1~$t2"); 
        dd(get_rels($t1, $t2, '1:n',  $tenant_id), "1:n para $t1~$t2"); 
        dd(get_rels($t1, $t2, 'n:1',  $tenant_id), "n:1 para $t1~$t2"); 
        dd(get_rels($t1, $t2, 'n:m',  $tenant_id), "n:m para $t1~$t2");
        dd(get_rel_type($t1, $t2, $rel_str, $tenant_id), 'REL TYPE');

        dd('----------------------x----------------------');

        $tenant_id = 'db_flor';

        $t1 = 'tbl_persona';
        $t2 = 'tbl_usuario';
        $rel_str = null;

        dd(get_rels($t1, $t2, '1:1',  $tenant_id), "1:1 para $t1~$t2"); 
        dd(get_rels($t1, $t2, '1:n',  $tenant_id), "1:n para $t1~$t2"); 
        dd(get_rels($t1, $t2, 'n:1',  $tenant_id), "n:1 para $t1~$t2"); 
        dd(get_rels($t1, $t2, 'n:m',  $tenant_id), "n:m para $t1~$t2");
        dd(get_rel_type($t1, $t2, $rel_str, $tenant_id), 'REL TYPE');

        dd('----------------------x----------------------');

        $t1 = 'tbl_producto';
        $t2 = 'tbl_unidadmedida';
        $rel_str = null;

        dd(get_rels($t1, $t2, '1:1',  $tenant_id), "1:1 para $t1~$t2"); 
        dd(get_rels($t1, $t2, '1:n',  $tenant_id), "1:n para $t1~$t2"); 
        dd(get_rels($t1, $t2, 'n:1',  $tenant_id), "n:1 para $t1~$t2"); 
        dd(get_rels($t1, $t2, 'n:m',  $tenant_id), "n:m para $t1~$t2");
        dd(get_rel_type($t1, $t2, $rel_str, $tenant_id), 'REL TYPE');
        
        dd('----------------------x----------------------');

        $t1 = 'tbl_sub_cuenta_contable';
        $t2 = 'tbl_iva_cuentacontable';
        $rel_str = null;

        dd(get_rels($t1, $t2, '1:1',  $tenant_id), "1:1 para $t1~$t2"); 
        dd(get_rels($t1, $t2, '1:n',  $tenant_id), "1:n para $t1~$t2"); 
        dd(get_rels($t1, $t2, 'n:1',  $tenant_id), "n:1 para $t1~$t2"); 
        dd(get_rels($t1, $t2, 'n:m',  $tenant_id), "n:m para $t1~$t2");
        dd(get_rel_type($t1, $t2, $rel_str, $tenant_id), 'REL TYPE');
        
        dd('----------------------x----------------------');


        $t1 = 'tbl_sub_cuenta_contable';
        $t2 = 'tbl_producto';
        $rel_str = null;

        dd(get_rels($t1, $t2, '1:1',  $tenant_id), "1:1 para $t1~$t2"); 
        dd(get_rels($t1, $t2, '1:n',  $tenant_id), "1:n para $t1~$t2"); 
        dd(get_rels($t1, $t2, 'n:1',  $tenant_id), "n:1 para $t1~$t2"); 
        dd(get_rels($t1, $t2, 'n:m',  $tenant_id), "n:m para $t1~$t2");
        dd(get_rel_type($t1, $t2, $rel_str, $tenant_id), 'REL TYPE');

        dd('----------------------x----------------------');

        $t1 = 'tbl_categoria_persona';
        $t2 = 'tbl_usuario';
        $rel_str = null;

        dd(get_rels($t1, $t2, '1:1',  $tenant_id), "1:1 para $t1~$t2"); 
        dd(get_rels($t1, $t2, '1:n',  $tenant_id), "1:n para $t1~$t2"); 
        dd(get_rels($t1, $t2, 'n:1',  $tenant_id), "n:1 para $t1~$t2"); 
        dd(get_rels($t1, $t2, 'n:m',  $tenant_id), "n:m para $t1~$t2");

        dd(get_rel_type($t1, $t2, $rel_str, $tenant_id), 'REL TYPE');

    }

    function is_rel(){
        $tenant_id = 'az';

        // $t1 = 'u';
        // $t2 = 'u_settings';

        // dd(is_1_1($t1, $t2, null, $tenant_id), "All relations for $t1~$t2 are 1:1 ?"); 
        // dd(is_1_n($t1, $t2, null, $tenant_id), "All relations for $t1~$t2 are 1:n ?"); 
        // dd(is_1_n($t1, $t2, null, $tenant_id), "All relations for $t1~$t2 are n:1 ?");
        // dd(is_n_m($t1, $t2, null, $tenant_id), "All relations for $t1~$t2 are n:m ?"); 
     

        // $t1 = 'products';
        // $t2 = 'product_categories';

        // dd(is_1_1($t1, $t2, null, $tenant_id), "All relations for $t1~$t2 are 1:1 ?"); 
        // dd(is_1_n($t1, $t2, null, $tenant_id), "All relations for $t1~$t2 are 1:n ?"); 
        // dd(is_n_1($t1, $t2, null, $tenant_id), "All relations for $t1~$t2 are n:1 ?");
        // dd(is_n_m($t1, $t2, null, $tenant_id), "All relations for $t1~$t2 are n:m ?");

        // dd('------------------------------------------------------------');

        $t1 = 'products';
        $t2 = 'users';

        // dd(get_rels($t1, $t2, '1:1',  $tenant_id), "1:1 para $t1~$t2"); 
        // dd(get_rels($t1, $t2, '1:n',  $tenant_id), "1:n para $t1~$t2"); 
        // dd(get_rels($t1, $t2, 'n:1',  $tenant_id), "n:1 para $t1~$t2"); 
        // dd(get_rels($t1, $t2, 'n:m',  $tenant_id), "n:m para $t1~$t2");

        dd(is_1_1($t1, $t2, null, $tenant_id), "All relations for $t1~$t2 are 1:1 ?"); 
        dd(is_1_n($t1, $t2, null, $tenant_id), "All relations for $t1~$t2 are 1:n ?"); 
        dd(is_n_1($t1, $t2, null, $tenant_id), "All relations for $t1~$t2 are n:1 ?");
        dd(is_n_m($t1, $t2, null, $tenant_id), "All relations for $t1~$t2 are n:m ?"); 

        dd('------------------------------------------------------------');

        exit;

        $tenant_id = 'db_flor';
        $t1 = 'tbl_persona';
        $t2 = 'tbl_usuario';


        dd(is_1_1($t1, $t2, null, $tenant_id), "All relations for $t1~$t2 are 1:1 ?"); 
        dd(is_1_n($t1, $t2, null, $tenant_id), "All relations for $t1~$t2 are 1:n ?"); 
        dd(is_n_1($t1, $t2, null, $tenant_id), "All relations for $t1~$t2 are n:1 ?");
        dd(is_n_m($t1, $t2, null, $tenant_id), "All relations for $t1~$t2 are n:m ?");
    }

    function which_rel(){
        $tenant_id = 'db_legion';

        $t1 = 'tbl_arl';
        $t2 = 'tbl_empresa_nomina';

        dd(get_rel_type($t1, $t2, null, $tenant_id), "$t1~$t2");

        exit; //

        $tenant_id = 'az';

        $t1 = 'products';
        $t2 = 'product_categories';

        dd(get_rel_type($t1, $t2, null, $tenant_id), "$t1~$t2");

        $t1 = 'products';
        $t2 = 'product_tags';

        dd(get_rel_type($t1, $t2, null, $tenant_id), "$t1~$t2");

        $t1 = 'products';
        $t2 = 'valoraciones';

        dd(get_rel_type($t1, $t2, null, $tenant_id), "$t1~$t2");


        $t1 = 'valoraciones';
        $t2 = 'products';

        dd(get_rel_type($t1, $t2, null, $tenant_id), "$t1~$t2");


        $t1 = 'valoraciones';
        $t2 = 'users';

        dd(get_rel_type($t1, $t2, null, $tenant_id), "$t1~$t2");


        $t1 = 'products';
        $t2 = 'users';

        dd(get_rel_type($t1, $t2, null, $tenant_id), "$t1~$t2");       
       
       
        $t1 = 'users';
        $t2 = 'products';

        dd(get_rel_type($t1, $t2, null, $tenant_id), "$t1~$t2");       
        
        $t1 = 'users';
        $t2 = 'book_reviews';

        dd(get_rel_type($t1, $t2, null, $tenant_id), "$t1~$t2"); 


        $t1 = 'book_reviews';
        $t2 = 'users';

        dd(get_rel_type($t1, $t2, null, $tenant_id), "$t1~$t2");  

        $t1 = 'roles';
        $t2 = 'users';

        dd(get_rel_type($t1, $t2, null, $tenant_id), "$t1~$t2"); 

        $t1 = 'user_roles';
        $t2 = 'roles';

        dd(get_rel_type($t1, $t2, null, $tenant_id), "$t1~$t2");  

        $t1 = 'roles';
        $t2 = 'user_roles';

        dd(get_rel_type($t1, $t2, null, $tenant_id), "$t1~$t2"); 

        $t1 = 'product_tags';
        $t2 = 'products';

        dd(get_rel_type($t1, $t2, null, $tenant_id), "$t1~$t2");


        $t1 = 'products';
        $t2 = 'product_tags';
 
        dd(get_rel_type($t1, $t2, null, $tenant_id), "$t1~$t2");

        $tenant_id = 'az';
        $t1 = 'u';
        $t2 = 'u_settings';
 
        dd(get_rel_type($t1, $t2, null, $tenant_id), "$t1~$t2");

        $t1 = 'u_settings';
        $t2 = 'u';
        
        dd(get_rel_type($t1, $t2, null, $tenant_id), "$t1~$t2");

        $t1 = 'tbl_persona';
        $t2 = 'tbl_pais';
        
        dd(get_rel_type($t1, $t2, null, 'db_flor'), "$t1~$t2");

        $t1 = 'tbl_estado';
        $t2 = 'tbl_categoria_persona';
        
        dd(get_rel_type($t1, $t2, null, 'db_flor'), "$t1~$t2");

        $t1 = 'tbl_sub_cuenta_contable';
        $t2 = 'tbl_iva_cuentacontable';

        dd(get_rel_type($t1, $t2, null, 'db_flor'), "$t1~$t2");


        $t1 = 'tbl_sub_cuenta_contable';
        $t2 = 'tbl_iva';

        dd(get_rel_type($t1, $t2, null, 'db_flor'), "$t1~$t2");


        $t1 = 'job_tbl';
        $t2 = 'usr_tbl';

        dd(get_rel_type($t1, $t2, null, 'az'), "$t1~$t2");

        $t1 = 'usr_tbl';
        $t2 = 'job_tbl';
        
        dd(get_rel_type($t1, $t2, null, 'az'), "$t1~$t2");


        $t1 = 'tbc1';
        $t2 = 'tbc2';

        // 1:n  -- diría que está OK
        dd(get_rel_type($t1, $t2, null, $tenant_id), "$t1~$t2");


        // 1:n -- no estoy seguro
        $tenant_id = 'az'; 

        $t1 = 'tbc2';
        $t2 = 'tbc1';

        dd(get_rel_type($t1, $t2, null, $tenant_id), "$t1~$t2");


        $t1 = 'tbl_sub_cuenta_contable';
        $t2 = 'tbl_producto';

        dd(get_rel_type($t1, $t2, null, 'db_flor'), "$t1~$t2");

        
        $t1 = 'tbl_producto';
        $t2 = 'tbl_sub_cuenta_contable';

        dd(get_rel_type($t1, $t2, null, 'db_flor'), "$t1~$t2");

        $tenant_id = 'az';

        $t1 = 'ur';
        $t2 = 'ur_settings';
 
        dd(get_rel_type($t1, $t2, null, $tenant_id), "$t1~$t2");

        $t1 = 'ur_settings';
        $t2 = 'ur';
        
        dd(get_rel_type($t1, $t2, null, $tenant_id), "$t1~$t2");

        $tenant_id = 'db_flor';

        $t1 = 'tbl_producto';
        $t2 = 'tbl_unidadmedida';

        $relation_str = null;
        
        //dd(get_rel_type($t1, $t2, null, $tenant_id), "$t1~$t2");

        $rel_type = get_rel_type($t1, $t2, $relation_str, $tenant_id);
    }


    function exx(){
        $m = DB::table('products')
        ->deleted()
        //->dontQualify()
        ->groupBy(['cost', 'size', 'belongs_to'])
        ->having(['cost', 100, '>='])
        ->or(function($q){
            $q->havingRaw('SUM(cost) > ?', [500])
            ->having(['size' => '1L']);
        })
        ->orderBy(['size' => 'DESC'])
        ->select(['cost', 'size', 'belongs_to']);

        dd($m->get());
        dd(
            $m
            //->setPaginator(false)
            ->sqlFormaterOn()
            ->dd()
        );
    }

    // OK
    function ex(){
        $m = DB::table('products')
        ->dontBind()   // <--- here 
        ->dontExec()   // <--- here 
        ->select(['size'])
        ->selectRaw('AVG(cost)')
        ->groupBy(['size'])
        ->having(['AVG(cost)', null, '<']);

        $sql = $m->toSql();

        dd(DB::select($sql, [50]));
        dd($sql, 'pre-compiled SQL');
        dd(DB::getLog(), 'excecuted SQL');    
    }

    function ex1a(){
        $m = DB::table('products')
        ->dontBind()   // <--- here 
        ->dontExec()   // <--- here 
        ->select(['size', 'cost'])
        ->groupBy(['size'])        
        ->having(['cost', null, '>='])
        ->having(['size' => null]);

        $sql = $m->toSql();

        dd(DB::select($sql, [5, '1L']));
        dd($sql, 'pre-compiled SQL');
        dd(DB::getLog(), 'excecuted SQL');     
    }

    function ex1b(){
        $m = DB::table('products')
        ->dontBind()   // <--- here 
        ->dontExec()   // <--- here 
        ->select(['size'])
        ->selectRaw('AVG(cost)')
        ->groupBy(['size'])        
        ->havingRaw('MIN(cost) = ? AND AVG(cost) > ?', [null, null]);

        $sql = $m->toSql();

        dd(DB::select($sql, [5, '1L']));
        dd($sql, 'pre-compiled SQL');
        dd(DB::getLog(), 'excecuted SQL');     
    }

    function ex2(){
        $m = DB::table('products')
        ->dontBind()   // <--- here 
        ->dontExec()   // <--- here 
        ->select(['size'])
        ->selectRaw('AVG(cost)')
        ->where(['cost', null, '>'])
        ->groupBy(['size'])
        ->having(['AVG(cost)', null, '<']);

        $sql = $m->toSql();

        dd(DB::select($sql, [50, 100]));
        dd($sql, 'pre-compiled SQL');
        dd(DB::getLog(), 'excecuted SQL');  
    }

    function test_raw(){
        $res = DB::select('SELECT * FROM baz');
        dd($res);
        dd(DB::getLog());
    }

    function test_raw1(){
        dd(DB::getCurrentConnectionId());
        
        $res = DB::select('SELECT * FROM tbl_departamento', [], null, 'db_flor');
        dd($res);
        dd(DB::getLog());

        dd(DB::getCurrentConnectionId());
    }

    function test_raw2(){
        $res = DB::select('SELECT * FROM products WHERE cost > ? AND size = ?', [550, '1 mm']);
        dd($res);
        dd(DB::getLog());
    }

    function test_raw2a(){
        $res = DB::select('SELECT cost FROM products WHERE cost > ? AND size = ?', [550, '1 mm'], 'COLUMN');
        dd($res);
        dd(DB::getLog());
    }

    function test_db_select(){
        DB::getDefaultConnection();

        $db_name = 'az';

        $data = DB::select("SELECT TABLE_NAME 
		FROM information_schema.tables
		WHERE table_schema = '$db_name'", [], 'COLUMN');

        dd($data, 'DATA');
        dd(DB::getLog());
    }
    
    function test_raw_insert(){
        dd(DB::insert('insert into `baz2` (name, cost) values (?, ?)', ['cool thing', '16.25']));
        dd(DB::getLog());
    }

    function test_raw_insert1(){
        DB::getConnection('main');

        dd(DB::getCurrentConnectionId());

        dd(DB::insert('insert into `baz2` (name, cost) values (?, ?)', ['cool thing', '16.25'], 'az'));
        dd(DB::getLog());

        dd(DB::getCurrentConnectionId());
    }

    // ID repetido *sin* ignore
    function test_raw_insert2a(){
        DB::getConnection('main');

        dd(DB::getCurrentConnectionId());

        try {
            dd(DB::insert('insert into `baz2` (id_baz2, name, cost) values (?, ?, ?)', [5000, 'cool thing', '16.25'], 'az'));
        } catch (\Exception $e){
            dd($e->getMessage());
            dd(DB::getLog());
        }
        
        dd(DB::getCurrentConnectionId());
    }

    function test_raw_insert2(){
        dd(DB::insert('insert ignore into `baz2` (id_baz2, name, cost) values (?, ?, ?)', [5000, 'cool thing', '16.25']));
        dd(DB::getLog());
    }

    function test_update_raw(){
        dd(DB::update('update `baz2` SET name = ?, cost = ? WHERE id_baz2 = ?', ['cool thing!!!!!!', '99.00', 5003]));
    }

    function test_statement(){
        DB::getConnection('db_flor');

        $res = DB::statement("INSERT INTO product_tags (name, comment, product_id) VALUES (?, ?, ?)", ['xyz', 'bla bla', 100], 'az');

        dd($res);
    }

    function gt(){
        DB::getConnection('az');
        $tables = Schema::getTables();

        dd($tables, 'TABLES');
    }

    function test_super_query(){
        $sql = file_get_contents(ETC_PATH . 'example.sql');
        dd(DB::select($sql, [], null, 'db_flor'));
    }
 
    function get_sql_client(){
        dd(DB::driver(), 'Driver');
        dd(DB::driverVersion(), 'Driver version');
        dd(DB::driverVersion(true), 'Driver version (num)');
        dd(DB::isMariaDB(), 'Is MariaDB');


        // $conn = DB::getConnection('az');

        // dd(DB::driverVersion());
        // dd(DB::isMariaDB(), 'Is MariaDB');

        // DB::getConnection('db_admin_mariadb_pablo');

        // dd(DB::driverVersion());
        // dd(DB::isMariaDB(), 'Is MariaDB');

        // $conn = DB::getConnection('az');

        // dd(DB::driverVersion());
        // dd(DB::isMariaDB(), 'Is MariaDB');
    }

    /*
        Sql formater habilitado via Model::sqlFormaterOn()
    */
    function test_sql_formater001(){
        $m = DB::table('products')
        ->deleted()
        ->groupBy(['cost', 'size', 'belongs_to'])
        ->having(['cost', 100, '>='])
        ->or(function($q){
            $q->havingRaw('SUM(cost) > ?', [500])
            ->having(['size' => '1L']);
        })
        ->orderBy(['size' => 'DESC'])
        ->select(['cost', 'size', 'belongs_to']);

        dd(
            $m
            ->sqlFormaterOn()   /* habilito */
            ->dd()
        );
    }

    /*
        Sql formater des-habilitado
    */
    function test_sql_formater002(){
        $m = DB::table('products')
        ->deleted()
        ->groupBy(['cost', 'size', 'belongs_to'])
        ->having(['cost', 100, '>='])
        ->or(function($q){
            $q->havingRaw('SUM(cost) > ?', [500])
            ->having(['size' => '1L']);
        })
        ->orderBy(['size' => 'DESC'])
        ->select(['cost', 'size', 'belongs_to']);

        dd($m->dd());
    }

    /*
        Sql formater habilitado via Model::dd()
    */
    function test_sql_formater003(){
        $m = DB::table('products')
        ->deleted()
        ->groupBy(['cost', 'size', 'belongs_to'])
        ->having(['cost', 100, '>='])
        ->or(function($q){
            $q->havingRaw('SUM(cost) > ?', [500])
            ->having(['size' => '1L']);
        })
        ->orderBy(['size' => 'DESC'])
        ->select(['cost', 'size', 'belongs_to']);

        dd(
            $m
            ->dd(true)
        );
    }

    /*
        Sql formateador es aplicado en un segundo paso
        y se parametriza para colorizar 
    */
    function test_sql_formater004(){
        $m = DB::table('products')
        ->deleted()
        ->groupBy(['cost', 'size', 'belongs_to'])
        ->having(['cost', 100, '>='])
        ->or(function($q){
            $q->havingRaw('SUM(cost) > ?', [500])
            ->having(['size' => '1L']);
        })
        ->orderBy(['size' => 'DESC'])
        ->select(['cost', 'size', 'belongs_to']);

        dd(
            Model::sqlFormatter($m->dd(), true)
        );
    }

    /*
        Sql formateador es aplicado en un segundo paso
        y se parametriza para colorizar pero usando el helper sql_formater 
    */
    function test_sql_formater005(){
        $m = DB::table('products')
        ->deleted()
        ->groupBy(['cost', 'size', 'belongs_to'])
        ->having(['cost', 100, '>='])
        ->or(function($q){
            $q->havingRaw('SUM(cost) > ?', [500])
            ->having(['size' => '1L']);
        })
        ->orderBy(['size' => 'DESC'])
        ->select(['cost', 'size', 'belongs_to']);

        dd(
            sql_formater($m->dd(), true)
        );
    }


    function test_middle(){
        $str = 'SELECT * 
            FROM 
            tbl_categoria_persona 
            INNER JOIN tbl_usuario as __usu_intIdActualizador ON __usu_intIdActualizador.usu_intId = tbl_categoria_persona.usu_intIdActualizador 
            INNER JOIN tbl_usuario as __usu_intIdCreador ON __usu_intIdCreador.usu_intId = tbl_categoria_persona.usu_intIdCreador 
            WHERE deleted_at IS NULL';

        $ini = strpos($str, 'INNER JOIN');
        $end = strpos($str, 'WHERE');
        
        $inners = Strings::middle($str, $ini, $end);
        dd($inners);
    }

    function test_match(){
        $o = '--name=xYz';
        d(Strings::match($o, '/^--name[=|:]([a-z][a-z0-9A-Z_]+)$/'));

        $o = '--namae=xYz';
        d(Strings::match($o, [
            '/^--name[=|:]([a-z][a-z0-9A-Z_]+)$/',
            '/^--namae[=|:]([a-z][a-z0-9A-Z_]+)$/', 
            '/^--nombre[=|:]([a-z][a-z0-9A-Z_]+)$/'
        ]));

        $o = '--dropColumn=toBeEarased';
        $dropColumn = Strings::matchParam($o, [
            'dropColumn',
            'removeColumn'
        ]);

        d($dropColumn);

        $o = '--renameColumn=aBcK,jU000w';
        d(Strings::match($o, '/^--renameColumn[=|:]([a-z0-9A-Z_-]+\,[a-z0-9A-Z_-]+)$/'));
    }

    /*  
        Strings::middle() works ok if $end is false
    */
    function test_middle2(){
        $str = 'SELECT * 
            FROM 
            tbl_categoria_persona 
            INNER JOIN tbl_usuario as __usu_intIdActualizador ON __usu_intIdActualizador.usu_intId = tbl_categoria_persona.usu_intIdActualizador 
            INNER JOIN tbl_usuario as __usu_intIdCreador ON __usu_intIdCreador.usu_intId = tbl_categoria_persona.usu_intIdCreador';

        $ini = strpos($str, 'INNER JOIN');
        $end = strpos($str, 'WHERE');

        dd($end);
        
        $inners = Strings::middle($str, $ini, $end);
        dd($inners);
    }

    function test_contains(){
        $string = 'The lazy fox jumped over the fence';

        if (Strings::contains('lazy', $string)) {
            echo "The string 'lazy' was found in the string\n";
        }

        if (Strings::contains('Lazy', $string)) {
            echo 'The string "Lazy" was found in the string';
        } else {
            echo '"Lazy" was not found because the case does not match';
        }

        if (Strings::contains('Lazy', $string, false)) {
            echo 'The string "Lazy" was found in the string';
        } else {
            echo '"Lazy" was not found because the case does not match';
        }
    }


    function vvv(){
        $sql = 'SELECT * 
            FROM 
            tbl_categoria_persona 
            INNER JOIN tbl_usuario as __usu_intIdActualizador ON __usu_intIdActualizador.usu_intId = tbl_categoria_persona.usu_intIdActualizador 
            INNER JOIN tbl_usuario as __usu_intIdCreador ON __usu_intIdCreador.usu_intId = tbl_categoria_persona.usu_intIdCreador 
            WHERE deleted_at IS NULL';

        $ini = strpos($sql, 'INNER JOIN');
        $end = strpos($sql, 'WHERE ');

        $inners = trim(Strings::middle($sql, $ini, $end));
        $in_arr = explode('INNER JOIN ', $inners);
        
        $aliases = [];
        foreach ($in_arr as $ix => $inner){
            if (empty($inner)){
                unset($in_arr[$ix]);
                continue;
            }

            if (!preg_match('/[a-zA-Z0-9_]+ as ([a-zA-Z0-9_]+) ON (.*)/', $inner, $matches)){
                throw new \Exception("SQL Error. Something was wrong");
            }

            $aliases[] = $matches[1];
            $ons[] = $matches[2];
        }

        dd($aliases);
        dd($ons);
    }

    // envio de archivo encodeado en base 64
    function test_numrot_dian(){
        $xml_file = file_get_contents(ETC_PATH . 'ad00148980970002000000067.xml');

        $xml_file_encoded = base64_encode($xml_file);

        $response = Url::consume_api('http://34.204.139.241:8084/api/SendDIAN', 'POST', $xml_file_encoded, [
            "Content-type"  => "text/plain",
            "Authorization" => "Bearer eyJhbGciOiJIUzM4NCIsInR5cCI6IkpXVCJ9.eyJlbWFpbCI6ImluZm9hZGFwdGFkb3JAbnVtcm90LmNvbSIsInJvbGUiOiJDbGllbnRlIiwibmJmIjoxNjM2MDQ0NTQ0LCJleHAiOjE2OTkxMTY1NDQsImlhdCI6MTYzNjA0NDU0NCwiaXNzIjoibnVtcm90IiwiYXVkIjoicmVhZGVycyJ9.yejhLDwaVb4enDgKPssXyf8SYP1AyrEEa5m99joo3EjG3bhMToUPnY5696sjU6Kb"
        ]);

        dd($response, 'RES');
    }

    function check_tenant_group(){
        $db = 'db_200';        
        dd(DB::getTenantGroupName($db), "Group name for tenant_id = $db");

        $db = 'az';        
        dd(DB::getTenantGroupName($db), "Group name for tenant_id = $db");

        $db = 'db_flor';        
        dd(DB::getTenantGroupName($db), "Group name for tenant_id = $db");
    }

    function check_schema_path(){
        dd(get_schema_path('tbl_arl', 'db_flor'));
        dd(get_schema_path('products', 'az'));
        dd(get_schema_path());
    }

    function test_remove_unnecesary_slashes(){
        dd(Strings::removeUnnecessarySlashes('/home/www/simplerest/docs//DOC Simplerest.txt'));
        dd(Strings::removeUnnecessarySlashes('c:\windows\\system32'));
    }

    function test_glob(){
        dd(glob('*.zip'));
    }

    function test_abs_path(){
        dd(Files::getAbsolutePath("docs/x.txt"));
    }

    function test_is_dir(){
        dd(is_dir('/home/www/simplerest/docsX/dev'));
    }

    function test_rglob(){
        dd(Files::recursiveGlob(ROOT_PATH . '/*.php'));
    }

    function test_mkdir_ignore(){
        dd(Files::mkDir('/home/feli/Desktop/UPDATE/config'));
    }

    function test_writable(){
        Files::writableOrFail(APP_PATH . 'some_dir');
    }

    function test_hardware_id(){
        dd(Hardware::UniqueMachineID());
    }

    function test_parse_ini(){
        dd(parse_ini_file(ROOT_PATH . '.env'));
    }

    function test_env(){
        dd(Env::get());
        dd(Env::get('MAIL_PORT'));
    }

    function test_slash_string_fns(){
        dd(Strings::removeUnnecessarySlashes('c:\\windows'));
        dd(Strings::removeTrailingSlash('/home/www/simplerest/'));
        dd(Strings::removeTrailingSlash('/home/www/simplerest'));
        dd(Strings::addTrailingSlash('/home/www/simplerest'));
        dd(Strings::addTrailingSlash('/home/www/simplerest/'));
    }


    function grouped_dbs(){
        dd(DB::getGroupedDatabases());
    }

    function group_names(){
        dd(DB::getTenantGroupNames());
    }

    function test_conn_ids(){
        dd(DB::getConnectionIds());
    }

    function test_get_current_conn(){
        DB::setConnection('db_flor');        
        dd(DB::getCurrentConnectionId());  
    }
    
    function testtt(){
        $t1 = 'api_keys';
        $t2 = 'users';
        $from_db = 'az';

        $mul = is_mul_rel($t1, $t2, null, $from_db);
        dd($mul);
    }

    function get_mul(){
        $tenant_id = 'az'; 

        $t1 = 'products';
        $t2 = 'product_categories';

        //dd(is_mul_rel_cached($t1, $t2, null, $tenant_id), "$t1~$t2");  ///
        dd(is_mul_rel($t1, $t2, null, $tenant_id), "$t1~$t2");

        $t1 = 'product_categories';
        $t2 = 'products';

        //dd(is_mul_rel_cached($t1, $t2, null, $tenant_id), "$t1~$t2");  ///
        dd(is_mul_rel($t1, $t2, null, $tenant_id), "$t1~$t2");
        

        $t1 = 'product_categories';
        $t2 = 'products';

        dd(is_mul_rel($t1, $t2, null, $tenant_id), "$t1~$t2");

        $t1 = 'u';
        $t2 = 'u_settings';

        //dd(get_rel_type($t1, $t2, null, $tenant_id), "$t1~$t2");
        dd(is_mul_rel($t1, $t2, null, $tenant_id), "$t1~$t2");


        $t1 = 'u_settings';
        $t2 = 'u';
        
        //dd(get_rel_type($t1, $t2, null, $tenant_id), "$t1~$t2");
        dd(is_mul_rel($t1, $t2, null, $tenant_id), "$t1~$t2");


        $t1 = 'products';
        $t2 = 'users';

        //dd(get_rel_type($t1, $t2, null, $tenant_id), "$t1~$t2");
        dd(is_mul_rel($t1, $t2, null, $tenant_id), "$t1~$t2");


        $t1 = 'products';
        $t2 = 'product_tags';

        //dd(get_rel_type($t1, $t2, null, $tenant_id), "$t1~$t2");
        dd(is_mul_rel($t1, $t2, null, $tenant_id), "$t1~$t2");


        $t1 = 'tbl_persona';
        $t2 = 'tbl_pais';

        dd(is_mul_rel($t1, $t2, null, 'db_flor'), "$t1~$t2");


        $t1 = 'tbc1';
        $t2 = 'tbc2';

        // true -- ok
        dd(is_mul_rel($t1, $t2, null, 'az'), "$t1~$t2");

        $t1 = 'tbc2';
        $t2 = 'tbc1';

        // false -- ok
        dd(is_mul_rel($t1, $t2, null, 'az'), "$t1~$t2");

        $t1 = 'job_tbl';
        $t2 = 'usr_tbl';

        dd(is_mul_rel($t1, $t2, null, 'az'), "$t1~$t2");

        $t1 = 'usr_tbl';
        $t2 = 'job_tbl';
    
        dd(is_mul_rel($t1, $t2, null, 'az'), "$t1~$t2");


        $t1 = 'tbl_sub_cuenta_contable';
        $t2 = 'tbl_producto';

        dd(is_mul_rel($t1, $t2, null, 'db_flor'), "$t1~$t2");

        $t1 = 'tbl_producto';
        $t2 = 'tbl_sub_cuenta_contable';
        
        dd(is_mul_rel($t1, $t2, null, 'db_flor'), "$t1~$t2");


        $t1 = 'ur';
        $t2 = 'ur_settings';

        dd(is_mul_rel($t1, $t2, null, 'az'), "$t1~$t2");

        $t1 = 'products';
        $t2 = 'valoraciones';

        dd(is_mul_rel($t1, $t2, null, 'az'), "$t1~$t2");
        //dd(is_mul_rel_cached($t1, $t2, null, 'az'), "$t1~$t2"); 
        
        $t1 = 'tbl_producto';
        $t2 = 'tbl_unidadmedida';

        dd(is_mul_rel($t1, $t2, null, 'db_flor'), "$t1~$t2");
    }

    function test_foo(){
        $rows = DB::table('foo')
        ->get();

        dd($rows);
    }

    function ungrouped(){
        dd(DB::getUngroupedDatabases());
    }

    function test_del(){
        dd(Files::delete('app/controllers/ShopiController.php'));
        //Files::deleteOrFail('app/controllers/ShopiController.php');
    }

    function test_cp(){
        Files::delete('app/controllers/ShopiController.php'); // me aseguro no exista en destino
        Files::cp('updates/2021-12-12-0.5.0-alpha/files/app/controllers/ShopiController.php',
        'app/controllers/ShopiController.php');        
    }

    /*
        Not file in destiny
    */
    function test_cp2(){
        Files::delete('app/controllers/ShopiController.php'); // me aseguro no exista en destino
        Files::cp('updates/2021-12-12-0.5.0-alpha/files/app/controllers/ShopiController.php',
        'app/controllers');        
    }

    /*
        Not file in destiny and trailing slash
    */
    function test_cp3(){
        Files::delete('app/controllers/ShopiController.php'); // me aseguro no exista en destino
        Files::cp('updates/2021-12-12-0.5.0-alpha/files/app/controllers/ShopiController.php',
        'app/controllers/');        
    }

    function test_cp_with_backup(){
        Files::setBackupDirectory(ROOT_PATH . 'backup'); 
        Files::cp('updates/2021-12-12-0.5.0-alpha/files/app/controllers/ShopiController.php',
        'app/controllers/ShopiController.php');        
    }

    /*
        Sin especificar archivo en destino -- ok
    */
    function test_cp_with_backup2(){
        Files::setBackupDirectory(ROOT_PATH . 'backup');
        Files::cp('updates/2021-12-12-0.5.0-alpha/files/app/controllers/ShopiController.php',
        'app/controllers');        
    }

    function test_copy0(){
        $ori = '/home/www/simplerest';
        $dst = '/home/feli/Desktop/UPDATE';

        // Antes de iniciar la prueba limpio el directorio destino
        Files::delTree($dst);
        
        Files::copy($ori, $dst, [
            'docs',
            '/home/www/simplerest/vendor/psr/http-client/src/ClientInterface.php',
            'config/config.php'
            //'/home/www/simplerest/config/config.php'
        ], 
        [
            'docs/dev',
            #'/home/www/simplerest/docs/dev/TODO Supra.txt',
            'docs/INSTALACION.txt'
        ]);
    }

    function test_copy01a0(){
        $ori = '/home/www/html/erp/updates/2021-12-12-0.5.0-alpha/files';
        $dst = '/home/www/html/erp/';

        Files::copy($ori, $dst, [
            'config/constants.php',
            'app/controllers/api/Me.php'
        ]);
    }


    function test_copy01a1(){
        $ori = '/home/www/html/erp/updates/2021-12-12-0.5.0-alpha/files';
        $dst = '/home/www/html/erp/';

        Files::copy($ori, $dst, [
            '/home/www/html/erp/updates/2021-12-12-0.5.0-alpha/files/config/constants.php',
            '/home/www/html/erp/updates/2021-12-12-0.5.0-alpha/files/app/controllers/api/Me.php'
        ]);
    }

    function test_copy01a2(){
        $ori = '/home/www/html/erp/updates/2021-12-12-0.5.0-alpha/files';
        $dst = '/home/www/html/erp/';

        Files::copy($ori, $dst);
    }

    function test_copy01b(){
        $ori = '/home/www/simplerest';
        $dst = '/home/feli/Desktop/UPDATE';

        // Antes de iniciar la prueba limpio el directorio destino
        Files::delTree($dst);
        
        Files::copy($ori, $dst, [
            'docs',
            'vendor/psr/http-client/src/'
        ], 
        [
            'docs/dev',
            '/home/www/simplerest/docs/dev/TODO Supra.txt',
            'docs/INSTALACION.txt'
        ]);
    }

    /*
        Con un directorio de ruta absoluta en $files
    */
    function test_copy01c(){
        $ori = '/home/www/simplerest';
        $dst = '/home/feli/Desktop/UPDATE';

        // Antes de iniciar la prueba limpio el directorio destino
        Files::delTree($dst);
        
        Files::copy($ori, $dst, [
            'docs',
            '/home/www/simplerest/vendor/psr/http-client/src/'     
        ], 
        [
            'docs/dev',
            '/home/www/simplerest/docs/dev/TODO Supra.txt',
            'docs/INSTALACION.txt'
        ]);
    }

    /*
        Con un directorio de ruta absoluta en $files
        y backup
    */
    function test_copy01d(){
        $ori = '/home/www/simplerest';
        $dst = '/home/feli/Desktop/UPDATE';

        Files::setBackupDirectory();
        
        Files::copy($ori, $dst, [
            'docs',
            '/home/www/simplerest/vendor/psr/http-client/src/'    
        ], 
        [
            'docs/dev',
            '/home/www/simplerest/docs/dev/TODO Supra.txt',
            'docs/INSTALACION.txt'
        ]);
    }

    function test_copy(){
        $ori = '/home/www/simplerest';
        $dst = '/home/feli/Desktop/UPDATE';

        // Antes de iniciar la prueba limpio el directorio destino
        Files::delTree($dst);

        $str_files = <<<'FILES'
        app/libs
        config/constants.php
        app/controllers
        FILES;

        $files = explode(PHP_EOL, $str_files);

        Files::copy($ori, $dst, $files, [
            'app/libs/db_dynamic_load.php',
            'app/controllers/PrepareUpdateController.php',
            'glob:*.zip'            
        ]);
    }

    function test_copy2(){
        $ori = '/home/www/simplerest';
        $dst = '/home/feli/Desktop/UPDATE';

        // Antes de iniciar la prueba limpio el directorio destino
        Files::delTree($dst);

        $str_files = <<<'FILES'
        app/libs
        glob:*.txt
        FILES;

        $files = explode(PHP_EOL, $str_files);

        Files::copy($ori, $dst, $files, [
            'app/libs/db_dynamic_load.php',
            'app/controllers/PrepareUpdateController.php',
            'glob:*.zip'            
        ]);
    }

    /*
        OJO !!! prodría ser una prueba peligrosa !
    */
    function test_copy_with_backup(){
        $ori = '/home/www/simplerest/updates/2021-12-12-0.5.0-alpha/files';
        $dst = '/home/www/simplerest_bk';

        Files::setBackupDirectory(ROOT_PATH . 'backup');

        Files::copy($ori, $dst, [
            'app'
        ], 
        [
            // except nothing
        ]);

    }

    /*
        $dst no está dentro de ROOT_PATH
    */  
    function test_copy_with_backup2(){
        $ori = '/home/www/simplerest';
        $dst = '/home/feli/Desktop/UPDATE';;

        Files::setBackupDirectory(ROOT_PATH . 'backup');

        Files::copy($ori, $dst, [
            'docs'
        ], 
        [
            'docs/dev',
            '/home/www/simplerest/docs/dev/TODO Supra.txt',
            'docs/INSTALACION.txt'
        ]);


        $str_files = <<<'FILES'
        app/libs
        config/constants.php
        app/controllers
        FILES;

        $files = explode(PHP_EOL, $str_files);

        Files::copy($ori, $dst, $files, [
            'app/libs/db_dynamic_load.php',
            'app/controllers/PrepareUpdateController.php',
            //'*.zip'            
        ]);
    }


    /*
        Debo agregar si no existe como primer use

        use simplerest\models\MyModel;
    */
    function test_sc(){
        $path = '/home/www/simplerest/app/models/BoletasModel.php';
        $file = file_get_contents($path);

        if (!Strings::contains('use simplerest\models\MyModel;', $file)){
            $lines = explode(PHP_EOL, $file);

            foreach ($lines as $ix => $line){
                $line = trim($line);

                if (Strings::startsWith('use ', $line)){
                    $lines[$ix] = 'use simplerest\models\MyModel;' . PHP_EOL . $line ;
                    break;
                }
            }

            $file = implode(PHP_EOL, $lines); 
            $ok = file_put_contents($path, $file);
        }
    }


    function prepare_default(){
        $ori = '/home/www/simplerest';
        $dst = '/home/feli/Desktop/UPDATE';

        // Solo para pruebas !!!!
        Files::delTree($dst);

        $str_files = <<<'FILES'
        app/libs
        app/core/MakeControllerBase.php
        app/controllers/MigrationsController.php
        docs
        config/constants.php
        FILES;

        $files = explode(PHP_EOL, $str_files);

        $except =  [
            'db_dynamic_load.php',
            'PrepareUpdateController.php',
            'docs/dev',
            'glob:*.zip'
        ];

        Files::copy($ori, $dst, $files, $except);
    }

    function test_at(){
        for ($i=0; $i<100000;$i++){
            d(at());
        }

        // esta vez debería ser un valor distinto
        d(at(false));
    }

    function test_dates(){
        //  mes 1-12
        d(datetime('n'));  

         // día 1-31
        d(datetime('j')); 

        // weekday (0-6)
        d(datetime('w'));

        // hour
        d(datetime('G'));

        // minutes
        d((int) datetime('i'));

        // seconds
        d((int) datetime('s'));
    }

    function test_next_dates(){
        d(Date::nextYearFirstDay());
        d(Date::nextMonthFirstDay());
        d(Date::nextWeek());
    }

    function test_get_fk(){
        $t1 = 'products';
        $t2 = 'product_categories';

        d(get_fks($t1, $t2), "FKs $t1 ->  $t2");


        $t1 = 'tbl_genero';
        $t2 = 'tbl_usuario';

        d(get_fks($t1, $t2, 'db_flor'), "FKs $t1 ->  $t2");
    }


    /*
        Dolar TRM - 
        DataSource: API Banco de la República (de Colombia)
    */
    function dolar(){
        $res = Url::consume_api('https://totoro.banrep.gov.co/estadisticas-economicas/rest/consultaDatosService/consultaMercadoCambiario', 'GET');
        
        if ($res['http_code'] != 200){
            throw new \Exception("Error: ". $res['code'] . ' -code: '. $res['code']);
        }

        $data  = $res['data'];
        $final = $data[count($data)-1];
        d($final[1], "DOLAR/COP (TRM) - VALOR FINAL ". date("Y-m-d H:i:s", substr($final[0], 0, 10)));
    }

    
    function euro(){
        $res = Url::consume_api('https://totoro.banrep.gov.co/estadisticas-economicas/rest/consultaDatosService/consultaMercadoCambiario', 'GET');
        
        if ($res['http_code'] != 200){
            throw new \Exception("Error: ". $res['code'] . ' -code: '. $res['code']);
        }

        $data    = $res['data'];
        $final  = $data[count($data)-1];
        $copusd = $final[1];
        
         // Build Swap
        $swap = (new \Swap\Builder())
        ->add('european_central_bank')
        ->add('national_bank_of_romania')
        ->add('central_bank_of_republic_turkey')
        ->add('central_bank_of_czech_republic')
        ->add('russian_central_bank')
        ->add('bulgarian_national_bank')
        ->add('webservicex')
        ->build();
            
        // Get the latest EUR/USD rate
        $rate = ($swap->latest('EUR/USD'))->getValue();

        $copeur = $copusd * $rate;

        d($copeur, "EUR/COP - VALOR FINAL ". date("Y-m-d H:i:s", substr($final[0], 0, 10)));
    }

    function swap(){

        // Build Swap
        $swap = (new \Swap\Builder())
            ->add('european_central_bank')
            ->add('national_bank_of_romania')
            ->add('central_bank_of_republic_turkey')
            ->add('central_bank_of_czech_republic')
            ->add('russian_central_bank')
            ->add('bulgarian_national_bank')
            ->add('webservicex')
        ->build();
            
        // Get the latest EUR/USD rate
        $rate = $swap->latest('EUR/USD');
        
        // 1.129
        d($rate->getValue(), 'EUR/USD');

        // 2016-08-26
        $rate->getDate()->format('Y-m-d');

        // Get the EUR/USD rate 15 days ago
        $rate = $swap->historical('EUR/USD', (new \DateTime())->modify('-15 days'));
    }

    function test_zip(){
        $ori = '/home/www/html/pruebas/drag';
        $dst = '/home/feli/Desktop/UPDATE/drag.zip';

           Files::zip($ori, $dst, [
            'file_to_be_ignored.txt',
            "$ori/jquery-ui-1.12.1.custom"
        ]);
    }

    function mkdir(){
        $tmp_dst = '/tmp/simplerest';
        
        if (is_dir($tmp_dst)){
            Files::delTree($tmp_dst, true); 
        }

        mkdir($tmp_dst);
    }

    function gt2(){
        DB::getConnection('db_flor');
        $tables = Schema::getTables();

        dd($tables, 'TABLES');
    }

    function test_sql_mode(){
        $dsn = "mysql:host=127.0.0.1;dbname=db_168;port=3306";
        $username = "boctulus";
        $password = 'gogogo#*$U&_441@#';

        $pdo_opt = [
            \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
            \PDO::MYSQL_ATTR_INIT_COMMAND => 'SET SESSION sql_mode="TRADITIONAL"'
        ];

        d($pdo_opt, 'PDO OPTIONS');

        $conn = new \PDO(
            $dsn, 
            $username, 
            $password, 
            $pdo_opt
       );

       $sql = "INSERT INTO tbl_categoria_persona(cap_intId, cap_varCategoriaPersona, cap_dtimFechaCreacion, cap_dtimFechaActualizacion, est_intIdEstado, usu_intIdCreador, usu_intIdActualizador) VALUES
        (1, 'Empleado', '2021-05-20 11:40:29', '2021-06-30 09:38:58', 1, 1, 1),
        (2, 'Tercero', '2021-05-20 11:40:58', '2021-07-21 21:44:46', 1, 1, 1),
        (3, 'Visitante', '2021-06-25 15:21:04', '1000-01-01 00:00:00', 1, 1, 1),
        (4, 'Cliente', '2021-08-04 16:48:49', '1000-01-01 00:00:00', 1, 1, 1),
        (5, 'Proveedor', '2021-08-04 16:48:58', '1000-01-01 00:00:00', 1, 1, 1),
        (6, 'Interesado', '2021-08-04 16:49:09', '1000-01-01 00:00:00', 1, 1, 1);";

        $stmt= $conn->prepare($sql);
        $res = $stmt->execute();

        d($res, 'RES');
    }

    function test_file_exits(){
        $full_path = '/home/www/simplerest/app/migrations/compania/2021_09_28_29110773_0010_tbl_genero_maestro.php';

        var_dump(file_exists($full_path), 'EXISTE?');

        if (!file_exists($full_path)){
            StdOut::pprint("Path '$full_path' does not exist !");
            exit;
        }
    }

    // from Yii
    function generateRandomString($length = 10) {
        $bytes = random_bytes($length);
        return substr(strtr(base64_encode($bytes), '+/', '-_'), 0, $length);
    }

    function test_dynamic_db_creation()
    {   
        $db_name = 'db_'. $this->generateRandomString();
		
        /*
            Voy a registrar la base de datos para su creación
        */

        $tenant = $db_name;
        $uid = 4;
        $at = at();

        DB::getDefaultConnection();

        $db_id = DB::table('tbl_base_datos')
        ->fill(['usu_intIdActualizador'])
        ->create([
            'dba_varNombre'    => $tenant,
            'usu_intIdCreador' => $uid,
            'usu_intIdActualizador' => $uid,
            'dba_dtimFechaCreacion' => $at,
            'dba_dtimFechaActualizacion' => $at
        ]); 


		// here(); 
        // exit; ///
		// DB::getConnection($db_name);
    }

    function test_last_update_dir(){
        d(Update::getLastVersionInDirectories());
    }

    function test_100(){
        $q = "SELECT * FROM products INNER JOIN product_categories as pc ON pc.id_catego=products.category WHERE (pc.name_catego = 'frutas') AND products.deleted_at IS NULL LIMIT 10";

        d(DB::select($q));
    }

    function test_101(){
        $q = "SELECT * FROM products INNER JOIN product_categories as pc ON pc.id_catego=products.category WHERE (pc.name_catego = ?)
        AND products.deleted_at IS NULL LIMIT ?";

        d(DB::select($q, ['frutas', 10]));
    }

    function test_102(){
        $conds = [
              'pc.name_catego',
              'frutas',
        ];

        $m = DB::table('products');
        $m
        ->where($conds)
        ->join('product_categories as pc');

        d($m->dd(true));
        
        $total = (int) (
            $m
            ->column()
            ->count()
        );

        d($total, 'total');
    }

    function test_update_cmp(){
        $v1 = '0.5.0';
        $v2 = '0.6.0';
        d(Update::compareVersionStrings($v1, $v2), "$v1 respecto de $v2");

        $v1 = '0.5.0';
        $v2 = '0.4.0';
        d(Update::compareVersionStrings($v1, $v2), "$v1 respecto de $v2");

        $v1 = '0.5.0-alpha';
        $v2 = '0.4.0';
        d(Update::compareVersionStrings($v1, $v2), "$v1 respecto de $v2");

        $v1 = '0.5.0';
        $v2 = '0.4.0-alpha';
        d(Update::compareVersionStrings($v1, $v2), "$v1 respecto de $v2");

        $v1 = '0.5.0-alpha';
        $v2 = '0.4.0-alpha';
        d(Update::compareVersionStrings($v1, $v2), "$v1 respecto de $v2");

        $v1 = '0.5.0-alpha';
        $v2 = '0.4.0-beta';
        d(Update::compareVersionStrings($v1, $v2), "$v1 respecto de $v2");

        $v1 = '0.5.0-alpha';
        $v2 = '0.5.0-beta';
        d(Update::compareVersionStrings($v1, $v2), "$v1 respecto de $v2");

        $v1 = '0.5.0-beta';
        $v2 = '0.5.0-alpha';
        d(Update::compareVersionStrings($v1, $v2), "$v1 respecto de $v2");
    }

    function get_random_user(){
        DB::getDefaultConnection();
        d(DB::table(get_users_table())->random()->first());
    }

    function test_390(){
        d(DB::table('super_cool_table')->id());
    }

    /*
        Obtengo el último registro creado
    */
    function test_400(){
        $m = DB::table('products');
        
        $row = $m
        ->deleted()
        ->orderBy([$m->createdAt() => 'DESC'])
        ->first();

        d($row);
        d($m->dd());
    }

    function m(){
        $is = DB::table('products')
        ->find(145)
        ->exists();

        if ($is){
            $row = DB::table('products')
            ->find(145)
            ->first();

            d($row, "La row existe");
            return;
        }

        d("Row fue borrada.");
    }

    function test_delete(){
        $m = DB::table('product_valoraciones');

        $m
        ->whereRaw("product_id = ?", [100])
        ->dontExec()
        ->delete();            
    
        d($m->getLog());
    }
    
    function test_delete_raw(){
        dd(DB::delete('DELETE FROM `baz2` WHERE id_baz2 = ?', [5000]));
    }
    
    function test_delete1(){
        $row = DB::table('products')
        ->findOrFail(145)
        ->delete();
    }

    function test_trashed(){
        $m = DB::table('products');
        
        d($m
        ->find(145)
        ->trashed());

        d($m->dd());
    }

    function test_undelete(){    
        $is = DB::table('products')
        ->find(145)
        ->exists();

        if ($is){
            $row = DB::table('products')
            ->find(145)
            ->first();

            d($row, "La row existe");
            return;
        }

        d("Row fue borrada. Intento restaurar");

        $m = DB::table('products');

        $row = $m
        ->find(145)
        ->undelete();

        d($m->getLog());

        $row = $m = DB::table('products')
        ->find(145)
        ->first();
        d($row);
    }

    function test_force_del(){
        $m = DB::table('products');
        $m->find(5512)
        ->forceDelete();
    }

    function get_products_no_filter(){
        $m = DB::table('products');
        $cnt = $m->count();

        d($cnt, 'regs');
    }

    function get_products_with_trashed(){
        $m = DB::table('products');
        $cnt = $m->withTrashed()->count();

        d($cnt, 'regs');
    }

    function get_products_only_trashed(){
        $m = DB::table('products');
        $cnt = $m->onlyTrashed()->count();

        d($cnt, 'regs');
    }

    function test_103(){
        d(DB::table('products')
        ->leftJoin("product_categories")
        ->leftJoin("product_tags")
        ->leftJoin("valoraciones")
        ->find(145)->first()    
        );
    }

    function test_104(){
        d(DB::table("product_tags")
        ->where(['product_id', 145])
        ->get()
        );
    }

    function test_105(){
        // $m = DB::table('product_valoraciones');
        // $m
        // ->whereRaw("product_id = ?", [145])
        // ->delete();            

        d(DB::table('product_valoraciones')
        ->join('valoraciones')
        ->where(['product_id', 145])
        ->get()
        );
    }


    function test_q(){
        $sql = file_get_contents(ETC_PATH . 'test.sql');
        d(DB::select($sql));
    }



    function test_desentrelazado(){
        $literal = strrev("SimpleRest framework created by Pablo Bozzolo <boctulus AT gmail.com>. All rights reserved.");
        
        // protect spaces
        $literal = str_replace(' ', '-', $literal);
        
        d(Strings::deinterlace($literal));
    }

    function test_entrelazado(){
        $str = [
            'SmlRs rmwr rae yPboBzoo<otlsA mi.o> l ihsrsre.',
            'ipeetfaeokcetdb al ozl bcuu Tgalcm.Alrgt eevd'
        ];

        return Strings::interlace($str);
    }

    function test_whois(){
        return DB::whois();
    }

    function test_unserialize(){
        $s_object = 'O:29:"simplerest\jobs\tasks\DosTask":0:{}';
        $s_params = 'a:2:{i:0;s:4:"Juan";i:1;i:39;}';

        $o = unserialize($s_object);
        $p = unserialize($s_params);

        $o->run(...$p);
    }

    function test_666(){
        // ahora copio los archivos ofuscados en el destino
        $ori = '/home/www/simplerest/tmp/yakpro-po/obfuscated';
        $dst = "updates/2021-12-20-0.7.0/";  

        Files::setCallback(function(string $content, string $path){
            return Strings::removeMultiLineComments($content);
        });

        Files::copy($ori, $dst . 'files/app/core'); // bug con glob:*
    }

    function test_remove_comments(){
        $file = file_get_contents('/home/www/simplerest/updates/2021-12-20-0.7.0/files/app/core/Container.php');
        d(Strings::removeMultiLineComments($file));
    }
   
    function test_date3(){
        // d(Date::nextNthMonthFirstDay(12));
        // d(Date::nextNthMonthFirstDay(1));
        // d(Date::nextNthMonthFirstDay(4));
        
        // d(Date::nextNthWeekDay(5));

        d(Date::nextNthMonthDay(5));
        d(Date::nextNthMonthDay(18));
    }

     /*
        Esto podría funcionar con el Router

        Route::get('/user/{id}', DumbController::class);

        Eso habilitaria: /dumb/6 
    */
    function __invoke(int $id)
    {
        d($id);
    }

    function test_refl(){
        d(Reflector::getConstructor(\simplerest\libs\Foo2::class));
    }

    function test_container(){
        Container::bind('foo', function(){
            return new Foo();
        });

        $foo = Container::make('foo');
        print_r($foo->bar());

        $foo = Container::make('foo');
        print_r($foo->bar());
    }

    function test_container2(){
        Container::bind('foo', Foo::class);

        $foo = Container::make('foo');
        print_r($foo->bar());

        $foo = Container::make('foo');
        print_r($foo->bar());
    }

    function test_container3(){
        Container::singleton('foo', Foo::class);

        $foo = Container::make('foo');
        print_r($foo->bar());
        
        $foo = Container::make('foo');
        print_r($foo->bar());
    }

    function test_container4(){
        Container::bind('car', \simplerest\libs\Car::class);

        $o = Container::makeWith('car', ['color' => 'blue', 'max_speed' => 200]);
        print_r($o->run());
        print_r($o);
    }

    /*
        Haciendo uso de Container::useContract(), intentar replicar:

        https://stackoverflow.com/a/52778193/980631
    */
    function test_container5(){
        
        // ....
    }


    function test_5055(){
        DB::getConnection('db_docker_php81_mysql');
    }


    function some_work(){
        for ($i=1; $i<=rand(5,10); $i++){
            d($i);
            sleep(1);
        }
    }

    /*
        Si se desea ser notificado cuando el job a terminado con éxito o un fallo,
        pueden hacerse:

        command && command-after-only-if-success &
        command || command-after-only-if-fail &

        https://superuser.com/a/345455/402377
    */
    function test_background_task(){
        $cmd = 'php com dumb some';
        $pid = System::runInBackground($cmd);

        d($pid, 'pid');
    }

    function test_supervisor_start(){
        Supervisor::start();
    }

    function test_supervisor_stop(){
        Supervisor::stop();
    }

    function test_is_job_running(){
        d(Supervisor::isRunning('some.php'));
    }

    function test_dispatch_q1(){
        $queue = new JobQueue("q1");
        $queue->dispatch(\simplerest\jobs\tasks\UnaTask::class);
        $queue->dispatch(\simplerest\jobs\tasks\UnaTask::class);
        $queue->dispatch(\simplerest\jobs\tasks\OtraTask::class);
        $queue->dispatch(\simplerest\jobs\tasks\OtraTask::class);
    }

    function test_dispatch_q2(){
        $queue = new JobQueue("q2");
        $queue->dispatch(\simplerest\jobs\tasks\DosTask::class, '1 - Juan', 39);
        $queue->dispatch(\simplerest\jobs\tasks\DosTask::class, '2 - Maria',21);
        $queue->dispatch(\simplerest\jobs\tasks\DosTask::class, '3 - Felipito', 10);
    }

    function test_worker_factory_q2(){
        $queue = new JobQueue("q2");
        $queue->addWorkers(30);
    }

    function test_worker_factory2(){
        $queue = new JobQueue();
        $queue->addWorkers(3);
    }

    function test_worker_factory_q1(){
        $queue = new JobQueue("q1");
        $queue->addWorkers(2);
    }

    function test_worker_stop(){
        JobQueue::stop();
    }

    function test_worker_stop_q1(){
        JobQueue::stop('q1');
    }

    function test_tag(){
        Tag::registerBuilder(\simplerest\core\libs\HtmlBuilder\Bt5Form::class);

        //Form::pretty();

        // echo Bt5Form::span(text:'Hi');
         
        // echo tag('text')->name('bozzolo')->placeholder('Su apellido');

        // echo tag('p')->text('Some paragraph')->class('mt-3')->textMuted();
        // echo tag('div')->content('Some content')->class('mt-3')->textMuted();

        // echo Bt5Form::group(
        //     content:[
        //         Bt5Form::span('@', [
        //             'id'    => 'basic-addon',
        //             'class' => 'input-group-text'
        //         ]),
        //         Bt5Form::inputText('nombre', [
        //             "placeholder" => "Username"
        //         ])
        //     ],
        //     tag: 'div',
        //     class:"input-group mb-3"
        // );

        // echo Bt5Form::div(
        //     content:
        //         Bt5Form::span('@', [
        //             'id'    => 'basic-addon',
        //             'class' => 'input-group-text'
        //         ]) .
        //         Bt5Form::inputText('nombre', [
        //             "placeholder" => "Username"
        //         ])
        //     ,
        //     class:"input-group mb-3"
        // );

        // echo Bt5Form::p();

        // echo Bt5Form::div(
        //     content:[
        //         Bt5Form::span('@', [
        //             'id'    => 'basic-addon',
        //             'class' => 'input-group-text'
        //         ]),

        //         Bt5Form::div(content:[
        //             Bt5Form::inputText('nombre', [
        //                 "placeholder" => "Username"
        //             ]),
        //             Bt5Form::p()
        //         ], class:'my_class')
        //     ],
        //     class:"input-group mb-3"
        // );


        // echo tag('select')->name('sexo')->options([
        //     'varon' => 1,
        //     'mujer' => 2
        // ])->default(1)->placeholder('Su sexo')->class('my-3');

        // echo tag('div')->content([
        //     Bt5Form::span('@', [
        //         'id'    => 'basic-addon',
        //         'class' => 'input-group-text'
        //     ]),
        //     Bt5Form::inputText('nombre', [
        //         "placeholder" => "Username"
        //     ])
        // ])->class("input-group mb-3");

        // echo Html::beautifier(
        //     tag('div')->content([
        //     tag('span')->text('@')->id('basic-addon')->class('input-group-text'),
        //     tag('text')->name('nombre')->placeholder('Username')
        // ])->class("input-group mb-3")
        // );

        // echo tag('color')->name('my_color')->text('Color')->id('c1');

        // echo Bt5Form::select(name:'size', options:[
        //     'L' => 'Large', 
        //     'S' => 'Small'
        // ], placeholder:'Pick a size...', default:'Large');

        //echo tag('button')->content('Botón rojo')->danger()->class('rounded-pill mx-3')->outline();

        // echo tag('card')->content(
        //     tag('cardBody')->content(
        //         tag('cardTitle')->text('Some title') .
        //         tag('cardSubtitle')->text('Some subtitle')->textMuted()
        //     )
        // )->class('mb-4');

        // echo tag('carousel')->content(
        //     tag('carouselInner')->content([
        //         tag('carouselItem')->content(
        //             tag('img')->class("d-block w-100")->src('https://solucionbinaria.com/assets/images/porfolio/elgrove.png')
        //         ),
        //         tag('carouselItem')->content(
        //             tag('img')->class("d-block w-100")->src('https://solucionbinaria.com/assets/images/porfolio/drivingcars-cl2.png')
        //         ),
        //         tag('carouselItem')->content(
        //             tag('img')->class("d-block w-100")->src('https://solucionbinaria.com/assets/images/porfolio/acrilicosxtchile-cl.png')
        //         ),
        //     ])
        // );


        // $class = "class1 class2";
        // Bt5Form::addClass("hide", $class);
        // var_dump($class);

        //$class = "class1 btn-secondary class2";
        // Bt5Form::addColor("primary", $class);
        // var_dump($class);


        // $class = "btn-secondary btn";
        // Bt5Form::addColor("primary", $class);
        // var_dump($class);


        // $class = "class1 btn-secondary class2";
        // Bt5Form::addColor("primary", $class, true);
        // var_dump($class);

        // $class = "class2";
        // Bt5Form::addColor("primary", $class, true);
        // var_dump($class);

        // $class = "";
        // Bt5Form::addColor("primary", $class, true);
        // var_dump($class);


        // $class = "class1 hide class2";
        // Bt5Form::addClass("hide", $class);
        // var_dump($class);

        // $class = "class1 hide class2";
        // Bt5Form::addClasses("hide otra", $class);
        // var_dump($class);
     
        // $class = "class1 hide class2";
        // Bt5Form::addClasses(["hide", "otra"], $class);
        // var_dump($class);

        // $class = "";
        // Bt5Form::addClasses(["hide", "otra"], $class);
        // var_dump($class);
     
        // var_dump(Bt5Form::addClass("hide", "class1 class2 hide"));
        // var_dump(Bt5Form::addClass("hide", "class1 class3"));
        // var_dump(Bt5Form::addClass("hide", "hide class1 class3"));
        // var_dump(Bt5Form::addClass("hide", ""));

        // d('');

        // var_dump(Bt5Form::removeClass("hide", "class1 class2"));
        // var_dump(Bt5Form::removeClass("hide", "hide class1 class2"));
        // var_dump(Bt5Form::removeClass("hide", "class1 hide class3"));
        // var_dump(Bt5Form::removeClass("hide", "class1 class4 hide"));

        // echo tag('modal')->class('fade')->content(
        //     tag('modalDialog')->content(
        //         tag('modalContent')->content(
        //             tag('modalHeader')->content(
        //                 tag('modalTitle')->text('Modal title') . 
        //                 tag('closeButton')->dataBsDismiss('modal')
        //             ) .
        //             tag('modalBody')->content(
        //                 tag('p')->text('Modal body text goes here.')
        //             ) 
        //         ) .

        //         tag('modalFooter')->content(
        //             tag('closeModal') .
        //             tag('button')->text('Save changes')
        //         )
        //     )
        // )->id('exampleModal');

        //echo tag('closeButton')->dataBsDismiss('modal')->content('');

        //echo tag('button')->class('btn btn-primary')->dataBsToogle('modal')->dataBsTarget("#exampleModal")->content('Launch demo modal');

       //echo tag('button')->success()->text('Save changes');

       //echo tag('closeModal');
        //    d('--');
        //    echo '<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>';


        // d(Form::hasColor("class1 btn-success class2", "success"));
        // d(Form::hasColor("class1 btn-success class2", "btn-success")); 
        // d(Form::hasColor("class1 btn-success class2", "danger")); 
        // d(Form::hasColor("class1 btn-success class2"));
        // d(Form::hasColor("class1 class2"));        

        //echo tag('button')->text('Save changes');
        // d('');
        //echo tag('basicButton')->class('btn-danger')->class('btn-success')->text('Save changes');

        //echo tag('button')->class('btn-danger')->success()->text('Save changes');         

        // d('');

        //echo tag('closeModal');

        // echo Bt5Form::div(
        //     content: [
        //         tag('span')->text('@')->id('basic-addon')->class('input-group-text'),
        //         tag('inputText')->name('nombre')->placeholder("Username")
        //     ],
        //     class: "input-group mb-3"
        // );

        // echo tag('modal')->content(
        //     tag('modalDialog')->content(
        //         tag('modalContent')->content(
        //             tag('modalHeader')->content(
        //                 tag('modalTitle')->text('Modal title') . 
        //                 tag('closeButton')->dataBsDismiss('modal')
        //             ) .
        //             tag('modalBody')->content(
        //                 tag('p')->text('Modal body text goes here.')
        //             ) . 
        //             tag('modalFooter')->content(
        //                 tag('closeModal') .
        //                 tag('button')->text('Save changes')
        //             ) 
        //         ) 
        //     )->large()
        // )->id('exampleModal');

        // echo tag('modal')
        // ->header(
        //     tag('modalTitle')->text('Modal title') . 
        //     tag('closeButton')->dataBsDismiss('modal')
        // )
        // ->body(
        //     tag('p')->text('Modal body text goes here!')
        // )
        // ->footer(
        //     tag('closeModal') .
        //     tag('button')->text('Save changes')
        // )
        // ->id('exampleModal');


        // echo tag('accordion')->items([
        //     [
        //         'id' => "flush-collapseOne",
        //         'title' => "Accordion Item #1",
        //         'body' => 'Placeholder content for this accordion, which is intended to demonstrate the <code>.accordion-flush</code> class. This is the first items accordion body.'
        //     ],
        //     [
        //         'id' => "flush-collapseTwo",
        //         'title' => "Accordion Item #2",
        //         'body' => 'Placeholder 2'
        //     ],
        //     [
        //         'id' => "flush-collapseThree",
        //         'title' => "Accordion Item #3",
        //         'body' =>  'Placeholder 3'
        //     ]
        // ])
        // ->id('accordionExample')
        // ->always_open(true)
        // ->attributes(['class' => 'accordion-flush']);

        // echo tag('carousel')->content(
        //     tag('carouselItem')->content(
        //         tag('carouselImg')->src(assets('img/slide-1.jpeg'))
        //     )->caption(
        //         '<h5>First slide label</h5>
        //         <p>Some representative placeholder content for the first slide.</p>'
        //     ),

        //     tag('carouselItem')->content(
        //         tag('carouselImg')->src(assets('img/slide-2.jpeg'))
        //     ),

        //     tag('carouselItem')->content(
        //         tag('carouselImg')->src(assets('img/slide-3.jpeg'))
        //     ),
        // )->id("carouselExampleControls")->withControls()->withIndicators()
        // // -->dark()
        // ;

        //echo tag('collapseButton')->target("#collapseExample")->content('Button with data-bs-target');
        //echo tag('dropdownLink')->id('dropdownMenuButton1')->href('#')->anchor('Dropdown button');

        //echo tag('dropdownButton')->id('dropdownMenuButton1')->content('Dropdown button')->danger()->large();

        // echo tag('formCheck')->content(
        //     tag('checkbox')->id("defaultCheck1").
        //     tag('label')->for("defaultCheck1")->text('Default checkbox')
        // )->class('my-3');

        // echo tag('formCheck')->content(
        //     tag('checkbox')->id("defaultCheck1")->disabled() .
        //     tag('formCheckLabel')->for("defaultCheck1")->placeholder('Disabled checkbox')
        // )->class('my-1');

        //echo tag('formCheck')->content('')->type('checkbox')->id("defaultCheck1")->text('Default checkbox')->class('me-2');

        // echo tag('listGroup')->content([
        //     tag('listGroupItem')->text('An item'),
        //     tag('listGroupItem')->text('An item #2'),
        //     tag('listGroupItem')->text('An item #3')
        // ])->class('mt-3');

        // echo tag('h3')->text("Datos")->class('mb-3');

        // echo tag('link')->href('#')->anchor('The Link')->title('Tool Tip!')->tooltip()->class('mt-3');

        // echo Bt5Form::link('The Link', '#', ...[
        //     'tooltip',
        //     'title' => 'My tooltip',
        //     'class' => 'mt-3'
        // ]);

        // echo tag('nav')->content([
        //     tag('navItem')->content(
        //         tag('navLink')->anchor('Active')
        //     ),
        //     tag('navItem')->content(
        //         tag('navLink')->anchor('Link')
        //     ),
        //     tag('navItem')->content(
        //         tag('navLink')->anchor('Link')
        //     ),
        //     tag('navItem')->content(
        //         tag('navLink')->anchor('Active')->disabled()
        //     )
        // ])->class('mb-3');     

        //Form::pretty();

        // echo tag('nav')->content([  
        // [
        //     'anchor' => 'Uno',
        //     'href'   => '#uno'
        // ],

        // [
        //     'anchor' => 'Dos',
        //     'href' => '#dos'
        // ],

        // [
        //     'anchor' => 'Tres',
        //     'href'   => '#tres'
        // ],
        // // tag('dropdown')->content(
        // //     tag('dropdownButton')->id('dropdownMenuButton')->content('Dropdown button') .    
        // //     tag('dropdownMenu')->ariaLabel('dropdownMenuButton')->content(
        // //         tag('dropdownItem')->href('#')->anchor('Action 1') .
        // //         tag('dropdownItem')->href('#')->anchor('Another action') .
        // //         tag('dropdownDivider') .
        // //         tag('dropdownItem')->href('#')->anchor('Something else here')
        // //     )
        // // ),
        // ])->class('mb-3')
        // ->justifyRight()
        // ->tabs()
        // ->role('tablist')
        // ->panes([
        //     'Textoooooooooo oo',
        //     'otroooooo',
        //     'y otro más'            
        // ]);  

        // echo tag('breadcrumb')->content([
        //     [
        //         'href' => '#',
        //         'anchor' => 'Home'
        //     ],

        //     [
        //         'href' => '#library',
        //         'anchor' => 'Library'
        //     ],

        //     [
        //         'anchor' => 'Data'
        //     ]
        // ]);

        /*
            Debería ser equivalente a:

            ->success()

        */
        //echo tag('button')->color('success')->value('Search');

        // echo tag('offcanvas')->id("offcanvasExample")->title('Offcanvas')->body([
        //     /*
        //         Body example
        //     */
        //     'Some text as placeholder. In real life you can have the elements you have chosen. Like, text, images, lists, etc.'
        // ])
        // ->backdropOff()
        // //->scroll()
        // ;

        //echo tag('spinner');
        
        // echo tag('div')->width(100)->content('
        // Some content,...
        // <p>
        // <p>
        // ')
        // //->border('top')
        // ->borderRad(2)
        // ->borderWidth(5)
        // ->borderColor('danger')
        // //->borderSub('top')
        // //->class('border border-top-0 border-5 border-danger rounded-3')
        // ;

        // echo tag('div')->width(100)->content('
        // Some content,...
        // <p>
        // <p>
        // ')
        // ->border('top left')
        // ->borderWidth(5)
        // ->borderRad(3)
        // ->borderColor('success')
        // ;

        // echo tag('div')->content(
        //     'Width 75%'
        //   )
        //   ->w(75)
        //   ->bg('warning')
        //   ->class('p-3');

        // echo tag('table')
        // ->rows([
        //   '#',
        //   'First',
        //   'Last',
        //   'Handle'
        // ])
        // ->cols([
        //   [
        //     1,
        //     'Mark',
        //     'Otto',
        //     '@mmd'
        //   ],
        //   [
        //     2,
        //     'Lara',
        //     'Cruz',
        //     '@fat'
        //   ],
        //   [
        //     3,
        //     'Lara',
        //     'Cruz',
        //     '@fat'
        //   ],
        //   [  
        //     4,
        //     'Feli',
        //     'Bozzolo',
        //     '@facebook'
        //   ]
        // ])->color('primary');
    
        // echo tag('h4')
        // ->text('Indigo!')
        // ->bg('indigo')
        // ->right();
        
        echo tag('inputGroup')
        ->content(
          tag('inputText')
        )
        ->prepend(
          tag('button')->danger()->content('Action')
        );

        // echo "\r\n\r\n\r\n";

    }

    function encoding(){
        dd(Factory::request()->gzip());
        dd(Factory::request()->acceptEncoding());
    }


    // function show_email_template(){
    //     $cols = [
    //         'Item',
    //         'Valor',
    //         'Moneda'
    //     ];

    //     $rows = [
    //         ['Valor de la compra en dólares', '$ 3000', 'USD'],
    //         ['Tarifa a cobrar por kilo', '$ 400', 'USD'],
    //         ['Kilos bruto', 2, 'Kg'],
    //         ['Flete aéreo (Miami-Santiago)', 0, 'USD'],
    //         ['Seguro (2%)',  0, 'USD'],
    //         ['Valor C.I.F',  0, 'USD'],
    //         ['Derechos (6%)', 0, 'USD'],
    //         ['Valor neto', 0, 'USD'],
    //         ['IVA', 0, 'USD'],
    //         ['Valor final', 0, 'USD'],
    //         ['TTL US$', 0, 'USD']
    //     ];

    //     $withs = [ 50, 30, 20 ];

    //     // config
    //     $locale = 'es_ES.UTF-8';

    //     $date     = new \Datetime();
    //     $datetime = ucfirst(\IntlDateFormatter::formatObject($date, 'EEEE', $locale)) .', '. ucfirst(\IntlDateFormatter::formatObject($date, 'dd-MM-Y', $locale));


    //     $image_header = [
    //         'src' => 'https://brimell.cl/wp-content/uploads/2022/01/cropped-cropped-BRIMELLtransparenteV01.png',
    //         'width' => "150",
    //         'height' => "150"
    //     ];

    //     $filecontents = file_get_contents($image_header['src']);
    //     $filecontents = base64_encode($filecontents);
    //     $image_header['src'] = "data:image/jpg;base64,".$filecontents;

    //     $footer = '<a href="https://brimell.cl/" style="color:#ffffff">Brimell</a> (2022) - Casilla internacional | Gestión de envíos';

    //     include ETC_PATH . 'email_template.php';
    // }

    // /*
    //     Usar postdrop.io para ensayar como se verían los mails

    //     Las imágenes deben tener:

    //     src
    //     alt
    //     width
    //     height
    //     border
    // */
    // function send_email_template(){
    //     $cols = [
    //         'Item',
    //         'Valor',
    //         'Moneda'
    //     ];

    //     $rows = [
    //         ['Valor de la compra en dólares', '$ 3000', 'USD'],
    //         ['Tarifa a cobrar por kilo', '$ 400', 'USD'],
    //         ['Kilos bruto', 2, 'Kg'],
    //         ['Flete aéreo (Miami-Santiago)', 0, 'USD'],
    //         ['Seguro (2%)',  0, 'USD'],
    //         ['Valor C.I.F',  0, 'USD'],
    //         ['Derechos (6%)', 0, 'USD'],
    //         ['Valor neto', 0, 'USD'],
    //         ['IVA', 0, 'USD'],
    //         ['Valor final', 0, 'USD'],
    //         ['TTL US$', 0, 'USD']
    //     ];

    //     $withs = [ 60, 25, 15 ];

    //     // config
    //     $locale = 'es_ES.UTF-8';

    //     $date     = new \Datetime();
    //     $datetime = ucfirst(\IntlDateFormatter::formatObject($date, 'EEEE', $locale)) .', '. ucfirst(\IntlDateFormatter::formatObject($date, 'dd-MM-Y', $locale));

    //     $image_header = [
    //         'src' => 'https://brimell.cl/wp-content/uploads/2022/01/cropped-cropped-BRIMELLtransparenteV01.png',
    //         'width' => "150",
    //         'height' => "150"
    //     ];

    //     $filecontents = file_get_contents($image_header['src']);
    //     $filecontents = base64_encode($filecontents);
    //     $image_header['src'] = "data:image/jpg;base64,".$filecontents;

    //     $footer = '<a href="https://brimell.cl/" style="color:#ffffff">Brimell</a> (2022) ~ Casilla internacional - Gestión de envíos';

    //     ob_start();
    //     include ETC_PATH . 'email_template.php';
    //     $content = ob_get_contents();
    //     ob_end_clean();


    //     dd(
    //         Mails::sendMail('boctulus@gmail.com', 'Pablo', 'Pruebita '. rand(99,999999), $content)
    //     );     
    // }

    // function cotiza($declarado_usd, $peso, $dim1, $dim2, $dim3, $unidad_long){
    //     // Las dimensiones ingresan en cm
    //     // peso en kilogramos

    //     // O se capturan excepciones o se genera directamente la respuesta JSON

    //     if (!in_array($unidad_long, ['cm', 'mt', 'pulg'])){
    //         throw new \InvalidArgumentException("Unidad de longitud puede ser solo cm, mt o pulg");
    //     }

    //     if ($peso > MAX_PESO){
    //         throw new \Exception("El peso máximo es de ". MAX_PESO ." Kg.");
    //     }

    //     if (max($dim1, $dim2, $dim3) > MAX_DIM){
    //         throw new \Exception("Ninguna dimensión puede superar los ". MAX_DIM." cm.");
    //     }

    //     $declarado_usd = max($declarado_usd ?? 0, MIN_DECLARADO);

    //     $dim1 = $dim1 ?? 0;
    //     $dim2 = $dim2 ?? 0;
    //     $dim3 = $dim3 ?? 0;

    //     $peso_ori = $peso;
    //     //dd($peso_ori, 'Peso original');

    //     $dim1_ori = $dim1;
    //     $dim2_ori = $dim2;
    //     $dim3_ori = $dim3;

    //     switch ($unidad_long){
    //         case 'mt': 
    //             $dim1 *=  100;
    //             $dim2 *=  100;
    //             $dim3 *=  100;
    //         break;
    //         case 'pulg':
    //             $dim1 *=  100/39.37;
    //             $dim2 *=  100/39.37;
    //             $dim3 *=  100/39.37;
    //         break;	
    //     }

    //     // dd($dim1, 'dim1');
    //     // dd($dim2, 'dim2');
    //     // dd($dim3, 'dim3');

    //     $peso_volumetrico = $dim1 * $dim2 * $dim3 * 0.000001;
    //     //d($peso_volumetrico, 'Peso vol');

    //     $peso_volumetrico_corregido = $peso_volumetrico * PV;
    //     //dd($peso_volumetrico_corregido, 'Peso vol corregido');

    //     $peso = max($peso, $peso_volumetrico_corregido, MIN_PESO);    
    //     //dd($peso, 'Peso considerado');

    //     // considerando solo kilos
    //     $transporte = $peso * KV;
    //     //dd($transporte, 'Transporte (flete)');

    //     // seguro
    //     $seguro = ($declarado_usd + $transporte) * SG * 0.01;
    //     //dd($seguro, 'Seguro');

    //     // aduana (valor CIF)
    //     $aduana = ($declarado_usd + $transporte + $seguro) * AD * 0.01;
    //     //dd($aduana, 'Aduana');

    //     // iva
    //     $iva = ($declarado_usd + $transporte + $seguro + $aduana) * IV * 0.01;

    //     $total_agencia_no_iva = $transporte + $seguro + $aduana; // neto

    //     $total_agencia        = $transporte + $seguro + $aduana + $iva;
    //     //dd($total_agencia, 'Total agencia');

    //     $valor_cif_no_iva     = ($declarado_usd + $transporte + $seguro);

    //     $valor_iva_de_cif     = $valor_cif_no_iva * IV * 0.01;

    //     $valor_cif            = $valor_cif_no_iva + $valor_iva_de_cif;

    //     $total_cliente = $total_agencia + $declarado_usd;
    //     //dd($total_cliente, 'Total cliente');


    //     return [
    //         /*
    //             Recibidos ajustados por mínimos
    //         */
    //         'declarado_usd' => $declarado_usd, 
    //         'ancho' => $dim1_ori, 
    //         'largo' => $dim2_ori, 
    //         'alto'  => $dim3_ori,
    //         'peso'  => $peso_ori,
    //         'unidad_long' => $unidad_long,

    //         'usd_x_kilo' => KV,

    //         /*
    //             Calculados
    //         */
    //         'peso_volumetrico' => $peso_volumetrico_corregido,
    //         'transporte' => round($transporte, 2),
    //         'seguro' => round($seguro, 2),
    //         'aduana' => round($aduana, 2),
    //         'iva' => round($iva, 2),
    //         'total_agencia' => round($total_agencia, 2),
    //         'total_neto' => round($total_agencia_no_iva, 2),
    //         'total_cliente' => round($total_cliente, 2),
    //         'valor_cif_no_iva' => round($valor_cif_no_iva, 2),
    //         'valor_cif' => round($valor_cif, 2)
    //     ];
    // }

    // function test_cotiza(){
    //     d($this->cotiza(10, 1, 50, 50, 50, 'cm'));
    //     d($this->cotiza(10, 1, 19.7, 19.7, 19.7, 'pulg'));
    // }

    /*
        Ver mejores soluciones como:

        https://github.com/php-gettext/Gettext

        Más
        https://stackoverflow.com/a/16744070/980631
    */
    function test_export_lang(){
        exportLangDef();
    }

    function test_trans(){
        setLang('es_AR');
        
        // i18n
        bindtextdomain('validator', LOCALE_PATH);
		textdomain('validator');

        // No se recibieron datos
        dd(_('No data'));
    }


    function test_format_num(){
        dd(Strings::formatNumber(4));
    }

    
}   
