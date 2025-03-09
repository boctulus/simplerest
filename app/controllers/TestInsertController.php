<?php

namespace simplerest\controllers;

use simplerest\core\controllers\Controller;
use simplerest\core\libs\Arrays;
use simplerest\core\libs\DB;
use simplerest\core\libs\Strings;
use simplerest\core\libs\VarDump;
use simplerest\core\Model;
use simplerest\core\traits\SubResourceHandler;

class TestInsertController extends Controller
{
    /*
        https://grok.com/chat/102388e4-47d3-4860-a889-f7b015e9ab77
    */
    function test_insertion_order(){
        $m = new Model();

        $tables = ['tags', 'courses', 'categories', 'users'];
        $tenant_id = 'edu';

        DB::getConnection($tenant_id);
        $order = $m->getInsertionOrder($tables);

        dd($order);
    }

    function test_insertion_order_2(){
        $m = new Model();
        $tenant_id = 'complex01';

        $tables = <<<TABLES
        customer_details  
        customers
        order_items
        orders
        products
        seller_products
        sellers
        support_categories
        support_tickets
        users
        tags
        categories
        TABLES;

        $tables = Arrays::stringToArray($tables);
        
        dd($tables, 'Tables');
        // exit;
        
        DB::getConnection($tenant_id);       
        
        $order = $m->getInsertionOrder($tables);

        dd($order, 'Order of tables for database insertion');
    }

    /*
        Uso de insert() implica insercion o fallo para el recurso principal

        Uso de insertOrUpdate() implica insercion o actualizacion para el recurso principal:
        si se envia el id para el recurso principal se actualiza, caso contrario inserta

        Definir 'masterTables' implica que se van a rechazar inserciones sobre esas tablas a nivel de validacion
    */
    
    function test_01(){
        DB::getConnection('{alguna conexion}');

        $data = json_encode('
        {
            "name": "John Doe",
            "posts": [
                { "title": "First Post", content: "This is my first post" },
                { "title": "Second Post", content: "This is another post" }
            ]
        }');
        
        $ret = DB::table('users')
            ->connectTo(['posts'])                    
            ->insert($data); 

        // dd(
        //     DB::getLog(), 'SQL' 
        // );

        dd($ret, 'RET');
    }

    /*       
        carts <-- cart_items <--- products
          |
        users
    */
    function test_02(){
        DB::getConnection('{alguna conexion}');
    
        $data = json_encode([
            [   
                "users" => ["id" => 1], // El usuario está asociado al carrito
                "cart_items" => [
                    [
                        "products" => ["id" => 2],              
                        "quantity" => 2
                    ],
                    [
                        "products" => ["id" => 3],              
                        "quantity" => 1
                    ]
                ]
            ]       
        ]);
                
        $ret = DB::table('carts')
            ->connectTo(['users', 'cart_items' => ['products']]) // Relaciones correctas
            ->insert($data); 
    
        dd($ret, 'RET');
    }
    
}

