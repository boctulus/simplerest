<?php

namespace simplerest\controllers\demos;

use simplerest\core\libs\DB;
use simplerest\core\Request;
use simplerest\core\Response;
use simplerest\core\libs\Factory;
use simplerest\core\controllers\Controller;
use simplerest\core\libs\PostmanGenerator;

class PostmanGenController extends Controller
{
    /*
        Generacion de colecciones para Organizaciones

        TODO:

        Para DELETE y POST y PATCH agregar el :id
    */
    function gen_PostmanGenerator_collections(){
        PostmanGenerator::setCollectionName('Pruebita N1');

        PostmanGenerator::setDestPath('D:/www/org_no_docker' . '/PostmanGenerator');

        //PostmanGenerator::setBaseUrl('http://127.0.0.1:8889'); 
        PostmanGenerator::setBaseUrl('{{base_url}}'); 

        PostmanGenerator::setSegment('api');

        PostmanGenerator::setToken('{{token}}');

        PostmanGenerator::addEndpoints([
            'productos',
            'usuarios'
        ], [
            PostmanGenerator::GET
        ]);

        PostmanGenerator::addEndpoints([
            'tipoVinculo'
        ], [
            PostmanGenerator::GET,
            PostmanGenerator::POST,
            PostmanGenerator::PATCH,
            PostmanGenerator::DELETE,
        ], true);

        $ok = PostmanGenerator::generate();

        dd($ok, 'Generated?');
    }
}

