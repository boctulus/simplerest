<?php

namespace simplerest\libs;

use simplerest\core\interfaces\AIChat;
use simplerest\core\libs\Strings;

/*
    Ej de uso:

    $params = [
            "max_tokens"      => 100,
            "temperature"     => 0.5
        ];

        $chat = new ChatGPT();
        $chat->setModel('gpt-4o-mini'); # Opcional

        $rev = new ReviewMaker($chat, $params);

        $res = $rev->getFromOne('{
            "id": 182,
            "name": "PARAGOLPES delantero patrol gr y61",
            "sku": "",
            "description": "Paragolpes delantero para soldar para Nissan patrol gr y61. Cortado y plegado a falta de soldar.",
            "short_description": "Paragolpes delantero para soldar para Nissan patrol gr y61. Cortado y plegado a falta de soldar."
        }', 1);

        dd($res);
*/
class ReviewMaker 
{
    protected AIChat $client;

    protected $notes = [];

    /*
        En el constructor deberia poder inyectar la IA a usar
        como ChatGPT o Claude
    */
    function __construct(AIChat $client, $params = []) {
        $this->client = $client;

        if (!empty($params)){
            $this->client->setParams($params);
        }
    }

    function addPromptNote($text){
        $this->notes[] = $text;
    }

    function getFromOne($product, int $qty = 1)
    {
        if (is_array($product)){
            $product = json_encode($product);
        }

        $prompt = "Escribe {$qty} reviews para el producto. El review debe de ser positivo, concistir de un párrafo de 20 a 60 palabras. El resultado debe limitarse a un JSON con el siguiente formato:
        {
            \"review\": \"{contenido}\"
        }

        Producto: $product";  
        
        if (!empty($this->notes)){
            $extra   = 'Notas: ' . implode("\r\n", $this->notes);
            $prompt .= "\r\n" . $extra;
        }

        $this->client->addContent($prompt);
        
        $this->client->exec();

        if (!$this->client->isComplete()){
            return false;
        }

        $res = $this->client->getContent(true); 
        
        if (isset($res['review'])){
            return $res['review'];
        } else {
            if (is_string($res)){
                return $res;
            }
            
            return array_column($res, 'review');
        }
    }

    /*
        Cada producto debe tener al menos un "id"
        
        Ej:

        {
            "id": 182,
            "name": "PARAGOLPES delantero patrol gr y61",
            "sku": "",
            "description": "Paragolpes delantero para soldar para Nissan patrol gr y61. Cortado y plegado a falta de soldar.",
            "short_description": "Paragolpes delantero para soldar para Nissan patrol gr y61. Cortado y plegado a falta de soldar."
        },
        
    */
    function get(array $products, $offset, $limit)
    {   
        $products = array_slice($products, $offset, $limit);

        $json_products = json_encode($products);

        $prompt = "Escribe un reviews para cada producto. El review debe de ser positivo, concistir de un párrafo de 20 a 60 palabras. El resultado debe limitarse a un JSON con el siguiente formato:
        {
            \"product_id\": \"{id}\",
            \"review\": \"{contenido}\"
        }

        Productos: $json_products";     

        if (!empty($this->notes)){
            $extra   = 'Notas: ' . implode("\r\n", $this->notes);
            $prompt .= "\r\n" . $extra;
        }
        
        $this->client->addContent($prompt);
        
        $this->client->exec();

        if (!$this->client->isComplete()){
            return false;
        }

        return $this->client->getContent(true);         
    }

}

