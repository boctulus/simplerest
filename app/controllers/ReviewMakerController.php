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
        // Parametros de entrada

        $data = '{
            "id": 182,
            "name": "PARAGOLPES delantero patrol gr y61",
            "sku": "",
            "description": "Paragolpes delantero para soldar para Nissan patrol gr y61. Cortado y plegado a falta de soldar.",
            "short_description": "Paragolpes delantero para soldar para Nissan patrol gr y61. Cortado y plegado a falta de soldar."
        }';

        $qty = 5;
        
        $tokens_per_row = 64;

        // Uso:

        $chat = new ChatGPT();

        // Testing
        $chat->getClient()
        ->enablePostRequestCache()
        ->setCache(3600 * 24); // <--- 24 HORAS
        
        $chat->setModel('gpt-4o-mini'); /* Opcional */
        
        // Cantidad maxima de tokens de salida ("completion tokens")
        $chat->setMaxTokens($tokens_per_row * $qty);

        $rev = new ReviewMaker($chat);

        $res = $rev->getFromOne($data, $qty);

        dd($res, 'ANSWER');

        dd($chat->getTokenUsage(), 'TOKEN USAGE');
    }

}