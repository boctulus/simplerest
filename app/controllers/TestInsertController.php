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

    function test_insert_struct(){
        DB::getConnection('edu');

        $data = [
            'title' => 'Mathematics for Phisicists',
            'categories' => [
                'name' => 'Extra'
            ],
            'users' => [
                ['name' => 'Bob Smith', 'role' => 'professor', 'email' => 'a@y.com', 'created_at' => now()],
                ['name' => 'Diana White', 'role' => 'student', 'email' => 'a@x.com', 'created_at' => now()]
            ]
        ];        
        
        $course_id = (DB::table('courses'))   
            ->connectTo(['categories', 'users'])        
            ->insertStruct($data); 

        // dd(
        //     DB::getLog(), 'SQL' 
        // );

        // dd($course_id);
    }

    function test_insert_struct_2(){
        DB::getConnection('complex01');

        $data = [
            'users' => [
                [
                    'name' => 'Juan Pérez',
                    'email' => 'juan.perez@ejemplo.com',
                    'sellers' => [
                        [
                            'products' => [
                                [
                                    'name' => 'Smartphone XYZ',
                                    'price' => 499.99,
                                    'categories' => [
                                        'name' => 'Electrónica'
                                    ],
                                    'tags' => [
                                        ['name' => 'Nuevo'],
                                        ['name' => 'Popular']
                                    ]
                                ],
                                [
                                    'name' => 'Tablet Pro',
                                    'price' => 349.99,
                                    'categories' => [
                                        'name' => 'Electrónica'
                                    ],
                                    'tags' => [
                                        ['name' => 'Oferta']
                                    ]
                                ],
                                [
                                    'name' => 'Balón de Fútbol',
                                    'price' => 29.99,
                                    'categories' => [
                                        'name' => 'Deportes'
                                    ],
                                    'tags' => [
                                        ['name' => 'Popular']
                                    ]
                                ]
                            ]
                        ]
                    ]
                ],
                [
                    'name' => 'María López',
                    'email' => 'maria.lopez@ejemplo.com',
                    'sellers' => [
                        [
                            'referred_by' => 'Juan Pérez',
                            'products' => [
                                [
                                    'name' => 'Camiseta Casual',
                                    'price' => 19.99,
                                    'categories' => [
                                        'name' => 'Ropa'
                                    ],
                                    'tags' => [
                                        ['name' => 'Limitado']
                                    ]
                                ],
                                [
                                    'name' => 'Lámpara de Mesa',
                                    'price' => 45.50,
                                    'categories' => [
                                        'name' => 'Hogar'
                                    ]
                                ]
                            ]
                        ]
                    ]
                ],
                [
                    'name' => 'Carlos Rodríguez',
                    'email' => 'carlos.rodriguez@ejemplo.com',
                    'customers' => [
                        [
                            'assigned_seller' => 'Juan Pérez',
                            'details' => [
                                'address' => 'Calle Falsa 123, Ciudad',
                                'phone' => '+34 600 123 456'
                            ],
                            'orders' => [
                                [
                                    'seller' => 'Juan Pérez',
                                    'created_at' => '2025-02-15 10:30:00',
                                    'items' => [
                                        [
                                            'product' => 'Smartphone XYZ',
                                            'quantity' => 1
                                        ],
                                        [
                                            'product' => 'Tablet Pro',
                                            'quantity' => 1
                                        ]
                                    ]
                                ]
                            ],
                            'support_tickets' => [
                                [
                                    'category' => 'Problema técnico',
                                    'description' => 'Mi smartphone no enciende correctamente.',
                                    'created_at' => '2025-02-18 14:20:00'
                                ]
                            ]
                        ]
                    ]
                ],
                [
                    'name' => 'Ana Martínez',
                    'email' => 'ana.martinez@ejemplo.com',
                    'customers' => [
                        [
                            'assigned_seller' => 'María López',
                            'details' => [
                                'address' => 'Avenida Principal 45, Pueblo',
                                'phone' => '+34 611 234 567'
                            ],
                            'orders' => [
                                [
                                    'seller' => 'María López',
                                    'created_at' => '2025-02-20 15:45:00',
                                    'items' => [
                                        [
                                            'product' => 'Camiseta Casual',
                                            'quantity' => 2
                                        ]
                                    ]
                                ]
                            ],
                            'support_tickets' => [
                                [
                                    'category' => 'Devolución',
                                    'description' => 'Quiero devolver una camiseta que me queda pequeña.',
                                    'created_at' => '2025-02-25 11:10:00'
                                ]
                            ]
                        ]
                    ]
                ],
                [
                    'name' => 'Roberto Sánchez',
                    'email' => 'roberto.sanchez@ejemplo.com',
                    'customers' => [
                        [
                            'assigned_seller' => 'Juan Pérez',
                            'details' => [
                                'address' => 'Plaza Mayor 7, Villa',
                                'phone' => '+34 622 345 678'
                            ],
                            'orders' => [
                                [
                                    'seller' => 'Juan Pérez',
                                    'created_at' => '2025-03-01 09:15:00',
                                    'items' => [
                                        [
                                            'product' => 'Balón de Fútbol',
                                            'quantity' => 1
                                        ],
                                        [
                                            'product' => 'Lámpara de Mesa',
                                            'quantity' => 1
                                        ]
                                    ]
                                ]
                            ],
                            'support_tickets' => [
                                [
                                    'category' => 'Consulta general',
                                    'description' => '¿Cuánto tiempo tarda el envío a mi dirección?',
                                    'created_at' => '2025-03-03 16:30:00'
                                ]
                            ]
                        ]
                    ]
                ]
            ],
            'support_categories' => [
                ['name' => 'Problema técnico'],
                ['name' => 'Devolución'],
                ['name' => 'Consulta general'],
                ['name' => 'Reclamación']
            ]
        ];
        
        $ret = (new Model())
            // ->connectTo(['categories', 'users'])
            //->dontExec()             
            ->insertStruct($data); 

        // dd(
        //     DB::getLog(), 'SQL' 
        // );

        dd($ret, 'RET');
    }
}

