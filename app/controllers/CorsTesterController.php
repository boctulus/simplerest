<?php

namespace simplerest\controllers;

use simplerest\core\controllers\Controller;
use simplerest\core\libs\CorsHandler;

class CorsTesterController extends Controller
{
    private $cors_config = [
        'paths' => ['/api-test/*'], // Aplica a todas las rutas bajo el path
        'allowedMethods' => ['POST', 'OPTIONS'], 
        'allowedOrigins' => ['*'],
        'allowedOriginsPatterns' => [],
        'allowedHeaders' => ['*'],
        'exposedHeaders' => ['X-Custom-Header'],
        'maxAge' => 3600, // Cache de 1 hora
        'supportsCredentials' => false
    ];

    function __construct() {
        parent::__construct();
        
        $this->initCors();
    }

    private function initCors(): void {
        $cors = new CorsHandler();
        $cors->loadConfig($this->cors_config);
        $cors->handle(); // Auto-maneja preflight y aplica headers
    }

    public function index($id = null){
        return $this->get($id);
    }

    // Ejemplo endpoint GET
    public function get($id = null) {
        return [
            'data' => [
                'message' => 'CORS GET funcionando!',
                'origin' => $_SERVER['HTTP_ORIGIN'] ?? 'No Origin header'
            ],
            'headers' => [
                'X-Custom-Header' => 'Valor personalizado'
            ]
        ];
    }

    // Ejemplo endpoint POST
    public function post() {
        $input = request()->getBody();
        
        return [
            'data' => [
                'received_data' => $input,
                'status' => 'success'
            ]
        ];
    }

    // Ejemplo endpoint OPTIONS (no necesario, CorsHandler lo maneja)
    // Pero se puede implementar lógica adicional si se requiere
    public function options() {
        // El CORS handler ya hizo su trabajo
    }
}