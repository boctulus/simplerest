<?php

namespace simplerest\controllers\test_apis;

use simplerest\core\libs\DB;
use simplerest\core\libs\Time;
use simplerest\core\libs\Strings;
use simplerest\core\libs\ApiClient;
use simplerest\core\controllers\Controller;

class RelmotorController extends Controller
{
    /*
        MUY lento por alguna razon

        Ej:

        GET http://relmotor.lan/dynamic_prices/get_price_native/106614/325
    */
    function get_price_native(){        
        $product_id = 106614;
        $user_id    = 325;

        $url  = "http://relmotor.lan/dynamic_prices/get_price_native/$product_id/$user_id";

        $client = new ApiClient();

        $client
            ->setHeaders([
                "Content-type"  => "Application/json"
            ])
            ->disableSSL()
            ->get($url);

        dd($client->status(), 'STATUS');        
        dd($client->error(), 'ERROR');
        dd($client
        ->decode()
        ->data(), 'DATA');
    }       

    /*
        Ej:

        POST http://relmotor.lan/dynamic_prices/get_prices_native_in_bulk

        {
            "product_ids": [ 106614, 82249, 40793 ],
            "user_id": 325
        }
    */
    function get_price_massive_native()
    {
        $t = Time::exec(function(){
            $url  = 'http://relmotor.lan/dynamic_prices/get_prices_native_in_bulk';

            $body = '{
                "product_ids": [ 106614, 82249, 40793 ],
                "user_id": 325
            }';

            $client = new ApiClient();

            $client
                ->setBody($body)
                ->setHeaders([
                    "Content-type"  => "Application/json"
                ])
                ->disableSSL()
                ->post($url);

            dd($client->status(), 'STATUS');
            dd($client->error(), 'ERROR');
            dd($client
            ->decode()
            ->data(), 'DATA');     
        });

        dd($t);              
    }

    /*
        Ej:

        POST http://relmotor.lan/dynamic_prices/get_prices_native_in_bulk

        {
            "product_ids": [ 106614, 82249, 40793 ],
            "user_id": 325
        }
    */
    function get_price_massive()
    {
        $t = Time::exec(function(){
            $url  = 'http://relmotor.lan/dynamic_prices/get_prices';

            $body = '{
                "product_ids": [ 106614, 82249, 40793 ],
                "user_id": 325
            }';

            $client = new ApiClient();

            $client
                ->setBody($body)
                ->setHeaders([
                    "Content-type"  => "Application/json"
                ])
                ->disableSSL()
                ->post($url);

            dd($client->status(), 'STATUS');
            dd($client->error(), 'ERROR');
            dd($client
            ->decode()
            ->data(), 'DATA');     
        });

        dd($t);              
    }
}

