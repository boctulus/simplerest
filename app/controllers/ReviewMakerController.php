<?php

namespace simplerest\controllers;

use simplerest\core\controllers\ConsoleController;
use simplerest\core\controllers\Controller;
use simplerest\core\libs\ChatGPT;
use simplerest\core\libs\Strings;
use simplerest\core\libs\DB;
use simplerest\core\traits\TimeExecutionTrait;
use simplerest\libs\ReviewMaker;

class ReviewMakerController extends Controller
{
    function test_1(){
        $params = [
            "max_tokens"      => 100,
            "temperature"     => 0.5
        ];

        $chat = new ChatGPT();

        // Testing
        $chat->getClient()
        ->enablePostRequestCache()
        ->setCache(3600);
        
        $chat->setModel('gpt-4o-mini'); /* Opcional */
        
        /*
            Implementar
        */
        $chat->dynamicTokenUsage();

        $rev = new ReviewMaker($chat, $params);

        $res = $rev->getFromOne('{
            "id": 182,
            "name": "PARAGOLPES delantero patrol gr y61",
            "sku": "",
            "description": "Paragolpes delantero para soldar para Nissan patrol gr y61. Cortado y plegado a falta de soldar.",
            "short_description": "Paragolpes delantero para soldar para Nissan patrol gr y61. Cortado y plegado a falta de soldar."
        }', 1);

        dd($res);
    }

}