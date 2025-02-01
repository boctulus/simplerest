<?php

namespace simplerest\core\libs;

use simplerest\core\Model;
use simplerest\core\libs\DB;
use simplerest\core\libs\Factory;

/*
    PostmanGenerator API Collection Generator ver 1.00

    TO-DO:

    - Usar opcionalmente los schemas de SimpleRest para poder tener datos con que rellenar el body
    de POST y PATCH

*/

class PostmanGenerator
{   
    static protected $resource_output_path;
    static protected $base_url = '';
    static protected $port;
    static protected $collection_name = '';
    static protected $jwt = '';
    static protected $endpoints = [];
    static protected $segment;

    const GET    = 'GET';
    const POST   = 'POST';
    const PATCH  = 'PATCH';
    const DELETE = 'DELETE';

    static function setDestPath(string $path){
        $path = Files::addTrailingSlash($path);
        static::$resource_output_path = $path;
        Files::mkDirOrFail(static::$resource_output_path, false);
    }

    static function setCollectionName(string $collection_name){
        static::$collection_name = $collection_name;
    }

    static function setBaseUrl(string $base_url){
        static::$base_url = Files::removeTrailingSlash($base_url);
    }

    static function setPort(string $port){
        static::$base_url .= ":$port";
    }

    static function setToken(string $jwt){
        static::$jwt = $jwt;
    }

    static function setSegment(string $segment){
        static::$segment =  Files::removeTrailingSlash($segment) . '/';
    }

    static function addEndpoints(Array $endpoints, Array $operations, bool $folder = false){
        foreach ($operations as $op){
            if (!in_array($op, [static::GET, static::POST, static::DELETE, static::PATCH])){
                throw new \InvalidArgumentException("Invalid operation '$op'");
            }
        }

        foreach ($endpoints as $ept){
            static::$endpoints[] = [
                'resource' => $ept, 
                'op'       => $operations,
                '_folder'  => $folder
            ];
        }
    }

    static protected function header(string $version = '2.1.0'){
        $postman_id = Strings::randomHexaString(8) . '-' . 
                     Strings::randomHexaString(4) . '-' . 
                     Strings::randomHexaString(4) . '-' . 
                     Strings::randomHexaString(4) . '-' . 
                     Strings::randomHexaString(12); 

        return [
            '_postman_id' => $postman_id,
            'name' => static::$collection_name,
            'schema' => "https://schema.getpostman.com/json/collection/v{$version}/collection.json",
            'description' => "Collection for " . static::$collection_name . " API endpoints",
            '_exporter_id' => '2650147'
        ];
    }

    static function generate() : bool {
        $base_url  = static::$base_url;
        $protocol  = Url::getProtocol($base_url);
        $port      = static::$port;
        $auth      = null;
        $_host_env = false;
    
        if (Strings::startsWith("{", $base_url) && Strings::endsWith("}", $base_url)) {
            $hostname  = $base_url;
            $_host_env = true;
        } else {
            $hostname = Url::getHostname($base_url);
        }
    
        $data = [];
        $data["info"] = static::header();
        $groupedItems = [];
    
        foreach (static::$endpoints as $endpoint) {
            $ep_name = $endpoint['resource'];
    
            if (static::$jwt != null) {
                $auth = [
                    'type' => 'bearer',
                    'bearer' => [
                        'token' => static::$jwt
                    ]
                ];
            }
    
            foreach ($endpoint['op'] as $op) {
                $raw = static::$base_url . '/' . static::$segment . $ep_name;
    
                if ($_host_env) {
                    $raw = Strings::after($raw, '}}');
                }
    
                $path = Url::getSlugs($raw);
    
                $url = [
                    "raw" => $raw,
                ];
    
                if (!empty($protocol)) {
                    $url["protocol"] = $protocol;
                }
    
                if (!empty($hostname)) {
                    $url["host"] = is_array($hostname) ? $hostname : [$hostname];
                }
    
                if (!empty($port)) {
                    $url["port"] = $port;
                }
    
                if (!empty($path)) {
                    $url["path"] = $path;
                }
    
                $request = [
                    "method" => $op,
                    "header" => [],
                    "description" => ucfirst(strtolower($op)) . " " . $ep_name,
                    "url" => $url
                ];
    
                if (!empty($auth)) {
                    $request["auth"] = $auth;
                }
    
                $item = [
                    'name' => "$op $ep_name",
                    'request' => $request,
                    'response' => []
                ];
    
                // Agrupar en carpetas por tabla (recurso)
                if (!isset($groupedItems[$ep_name])) {
                    $groupedItems[$ep_name] = [
                        'name' => $ep_name,
                        'item' => []
                    ];
                }
    
                $groupedItems[$ep_name]['item'][] = $item;
            }
        }
    
        // Convertir agrupación a lista de elementos
        $data["item"] = array_values($groupedItems);
    
        $path = static::$resource_output_path . static::$collection_name . ".postman_collection.json";
        Files::writableOrFail($path);
    
        return Files::write($path, json_encode($data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
    }
    
}