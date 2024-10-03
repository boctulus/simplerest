<?php

namespace simplerest\controllers;

use simplerest\core\libs\DB;
use simplerest\core\libs\Files;
use simplerest\libs\ReviewMaker;
use simplerest\core\libs\ChatGPT;
use simplerest\core\libs\Strings;
use simplerest\core\controllers\Controller;
use simplerest\core\traits\TimeExecutionTrait;
use simplerest\core\controllers\ConsoleController;

class ReviewMakerController extends Controller
{

    function test_1(){        
        // Parametros de entrada

        $row = '{
            "id": 182,
            "name": "PARAGOLPES delantero patrol gr y61",
            "sku": "",
            "description": "Paragolpes delantero para soldar para Nissan patrol gr y61. Cortado y plegado a falta de soldar.",
            "short_description": "Paragolpes delantero para soldar para Nissan patrol gr y61. Cortado y plegado a falta de soldar."
        }';

        $qty = 5;        
        $tokens_per_row = 64;
        $json_output_path = 'D:\www\4x4\wp-content\plugins\4x4-central\etc\reviews.jsonl';

        // Uso:

        $chat = new ChatGPT();

        // // Testing
        // $chat->getClient()
        // ->enablePostRequestCache()
        // ->setCache(3600 * 24 * 7); // <--- 24 HORAS
        
        $chat->setModel('gpt-4o-mini'); /* Opcional */
        
        // Cantidad maxima de tokens de salida ("completion tokens")
        $chat->setMaxTokens($tokens_per_row * $qty);

        $rev = new ReviewMaker($chat);

        $res = $rev->getFromOne($row, $qty);

        dd($res, 'ANSWER');

        dd($chat->getTokenUsage(), 'TOKEN USAGE');

        $json_output = [
            'id'      => json_decode($row, true)['id'],
            'reviews' => $res
        ];
    
        // Open file in append mode, or create if not exists
		$file = fopen($json_output_path,  'a');
	
        $jsonLine = json_encode($json_output) . PHP_EOL; // Encode to JSON and add newline
        $ok = fwrite($file, $jsonLine); // Write JSON line to file

		// Close file after writing
		fclose($file);

        dd($ok, 'Written?');
    }

    function test_2(){        
        // Parametros de entrada
        
        $qty = 3;        
        $tokens_per_row = 90;
        $json_input_path  = 'D:\www\4x4\wp-content\plugins\4x4-central\etc\products_untouched.json';
        $json_output_path = 'D:\www\4x4\wp-content\plugins\4x4-central\etc\reviews.jsonl';

        $json_data = Files::getContentOrFail($json_input_path);
        $data      = json_decode($json_data, true);

        $chat = new ChatGPT();
        $chat->getClient()->setTimeOut(180);

        // Testing
        // $chat->getClient()
        // ->enablePostRequestCache()
        // ->setCache(3600 * 24); // <--- 24 HORAS

        $chat->setModel('gpt-4o-mini'); /* Opcional */

        // Cantidad maxima de tokens de salida ("completion tokens")
        $chat->setMaxTokens($tokens_per_row * $qty);

        $rev = new ReviewMaker($chat);
    
        $rev->addPromptNote("Decide al azar si incluir el nombre completo del producto o abreviarlo para sonar mas natural.");

        if (rand(1,5) >= 4){
            $rev->addPromptNote("No comiences la frase nombrando el producto. Ej: 'El XYZ es ...'");
        }        

        // Open file in append mode, or create if not exists
        $file = fopen($json_output_path,  'a');

        // Randomizo el orden por si se detiene
        shuffle($data);

        foreach ($data as $row){
            // Descarto campos de producto para reducir INPUT TOKENS
            $row  = [
                'id'   => $row['id'],
                'name' => $row['name']
            ];

            $res = $rev->getFromOne($row, $qty);

            if ($res === false){
                dd(
                    $chat->getFinishReason(), "Finish Reason for product_id={$row['id']}"
                );
                continue;
            }

            dd($res, 'ANSWER');

            dd($chat->getTokenUsage(), 'TOKEN USAGE');

            if (empty($res)){
                continue;
            }

            $json_output = [
                'id'     => $row['id'],
                'review' => $res
            ];
        
            $jsonLine = json_encode($json_output) . PHP_EOL; // Encode to JSON and add newline
            $ok       = fwrite($file, $jsonLine); // Write JSON line to file
    
            dd($ok, 'Written?');

            // break; //
        }      

        // Close file after writing
        fclose($file);

       
    }

    function test_3(){        
        /*
            Basado en test_1() implementar con ReviewMaker::get()
        */
    }

}