<?php

/**
 * Configuración del package LLM Providers
 *
 * Este archivo puede contener configuraciones personalizadas
 * para los proveedores de LLM.
 */

return [
    /*
    |--------------------------------------------------------------------------
    | Default Provider
    |--------------------------------------------------------------------------
    |
    | El proveedor LLM predeterminado a utilizar cuando no se especifique uno.
    | Opciones: 'openai', 'claude'
    |
    */
    'default_provider' => env('LLM_DEFAULT_PROVIDER', 'openai'),

    /*
    |--------------------------------------------------------------------------
    | Provider API Keys
    |--------------------------------------------------------------------------
    |
    | Las API keys para cada proveedor. Se recomienda usar variables de entorno
    | en lugar de colocarlas directamente aquí.
    |
    */
    'providers' => [
        'openai' => [
            'api_key' => env('OPENAI_API_KEY'),
            'default_model' => 'gpt-4o-mini',
        ],

        'claude' => [
            'api_key' => env('CLAUDE_API_KEY'),
            'api_version' => '2023-06-01',
            'default_model' => 'claude-3-sonnet-20240229',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Default Parameters
    |--------------------------------------------------------------------------
    |
    | Parámetros predeterminados para las solicitudes a los LLMs.
    |
    */
    'default_params' => [
        'max_tokens' => 1000,
        'temperature' => 0.7,
    ],
];
