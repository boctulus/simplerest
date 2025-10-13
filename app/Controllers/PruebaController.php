<?php

namespace Boctulus\Simplerest\Controllers;

use Boctulus\Simplerest\Core\Controllers\Controller;
use Boctulus\Simplerest\Core\Request;
use Boctulus\Simplerest\Core\Response;
use Boctulus\Simplerest\Core\Libs\DB;
use Boctulus\Simplerest\Core\Traits\TimeExecutionTrait;

class PruebaController extends Controller
{
    function __construct() { parent::__construct(); }

    function prices($product_ids = null, $user_id = null)
    {
        $req  = Request::getInstance();

        if ($req->method() == 'POST'){            
            $data = $req
            ->getBodyDecoded();

            $product_ids = $data['product_ids'] ?? null;
            $user_id     = $data['user_id'] ?? null;
        }

        if (!is_array($product_ids)){
            $product_ids = explode(',', $product_ids);
        }
        
        if (empty($product_ids)){
            error('Parameter `product_ids` is required', 400);
        }

        if (empty($user_id)){
            error('Parameter `user_id` is required', 400);
        }

        $data   = [];
        $errors = [];
        foreach ($product_ids as $pid){
            $p = (bool) rand(0,1);           

            if ($p === false){
                $errors[] = Response::formatError("Product with product_id=$pid not found.", 404);
                continue;
            }

            $price = rand(100, 500);
            $salep = $price * 0.8;

            $data[] = [
                'normal_price' => $price,
                'sale_price'   => $salep  
            ];
        }           
       
        return Response::format($data, 200, $errors);
    }
}

