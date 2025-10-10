<?php

namespace SimplerestTeam\Mymodule\Controllers;

use Boctulus\Simplerest\Core\Controller;
use Boctulus\Simplerest\Core\Response;

class TestController extends Controller
{
    /**
     * Página principal del módulo
     */
    public function index()
    {
        return get_view(__DIR__ . '/../../views/index.php', [
            'title' => 'MyModule Test',
            'message' => 'Welcome to MyModule!'
        ]);
    }

    /**
     * Endpoint API para obtener datos
     */
    public function getData()
    {
        return Response::json([
            'status' => 'success',
            'data' => [
                'module' => 'myModule',
                'version' => '1.0.0',
                'items' => [
                    ['id' => 1, 'name' => 'Item 1', 'active' => true],
                    ['id' => 2, 'name' => 'Item 2', 'active' => false],
                    ['id' => 3, 'name' => 'Item 3', 'active' => true],
                ]
            ]
        ]);
    }

    /**
     * Endpoint API para crear datos
     */
    public function store()
    {
        $data = request()->getBody();

        return Response::json([
            'status' => 'success',
            'message' => 'Data created successfully',
            'data' => $data
        ], 201);
    }

    /**
     * Mostrar información del módulo
     */
    public function about()
    {
        return Response::json([
            'module' => 'myModule',
            'description' => 'Este es un módulo de prueba creado para demostrar la funcionalidad de SimpleREST',
            'author' => 'SimpleREST Team',
            'version' => '1.0.0',
            'features' => [
                'Controladores',
                'Rutas',
                'Vistas',
                'Migraciones',
                'Modelos'
            ]
        ]);
    }
}
