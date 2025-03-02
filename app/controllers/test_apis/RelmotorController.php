<?php

namespace simplerest\controllers\test_apis;

use simplerest\core\controllers\Controller;
use simplerest\core\libs\ApiClient;
use simplerest\core\libs\Config;
use simplerest\core\libs\DB;
use simplerest\core\libs\Paginator;
use simplerest\core\libs\Strings;
use simplerest\core\libs\Time;

class RelmotorController extends Controller
{
    /*
        La recibir resultados paginados, tener en cuenta la estructura de $data es 

        [
            "paginator" => [
                "total"       => {num},  // row_count 
				"count"       => {num},  // number of rows in the current page
				"last_page"   => {num},
				"total_pages" => {num},
				"page_size"   => {num}
            ],
            "rows"      => [
                // ..
            ]
        ]
    */

    function table()
    {   
        /*
            Ej de forma de paginar del lado de SR / SW
        */

        // En WordPress por ejemplo, no puedo usar ?page=
        $page_key   = Config::get()['paginator']['params']['page'] ?? 'page';
    
        $page_size = $_GET['size'] ?? 10;
        $page      = $_GET[$page_key] ?? 1;

        $offset = Paginator::calcOffset($page, $page_size);

        DB::getConnection();

        $rows = table('star_rating')
        ->take($page_size)
        ->offset($offset)
        ->get();

        $row_count = table('star_rating')->count();

        $paginator = Paginator::calc($page, $page_size, $row_count);
        $last_page = $paginator['totalPages'];

        $data = [
            "paginator" => [
                "current_page" => $page,
                "last_page"    => $last_page,
                "page_size"    => $page_size,
            ],
            "rows" => $rows
        ];

        return $data;
    }


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

}

