<?php

namespace simplerest\controllers\tests;

use simplerest\core\libs\DB;
use simplerest\core\Request;
use simplerest\core\libs\Url;
use simplerest\core\Response;
use simplerest\core\libs\Factory;
use simplerest\core\libs\ApiClient;
use simplerest\controllers\MyController;

class ApiClientController extends MyController
{
    function test_api01a()
    {
        $res = consume_api('http://34.204.139.241:8084/api/Home', 'GET', null, [
            'Accept' => 'text/plain'
        ]);
        dd($res);
    }

    function test_api01b()
    {
        $res = consume_api('http://34.204.139.241:8084/api/Home', 'GET', null, null, null, false);
        dd($res);
    }

    function test_api02()
    {
        $data = '{
            "userId": 1,
            "title": "Some title",
            "body": "Some long description"
          }';

        $res = consume_api('https://jsonplaceholder.typicode.com/posts', 'POST', $data);
        dd($res);
    }

    
    function test_api03()
    {
        $data = [
            "userId" => 1,
            "title" => "Some title",
            "body" => "Other long description"
        ];

        $options = [
            CURLOPT_SSL_VERIFYHOST => 0,
            CURLOPT_SSL_VERIFYPEER => 0
        ];

        $res = consume_api('https://jsonplaceholder.typicode.com/posts', 'POST', $data, null, $options);
        dd($res);
    }


    function test_api04()
    {
        $xml_file = file_get_contents(ETC_PATH . 'ad00148980970002000000067.xml');

        $response = consume_api('http://localhost/pruebas/get_xml.php', 'POST', $xml_file, [
            "Content-type" => "text/xml"
        ]);

        dd($response, 'RES');
    }

    // debe responderme con erro y un body de respuesta -- ok
    function test_api05()
    {
        $response = consume_api('http://localhost/pruebas/get_error.php', 'POST', null, [
            "Content-type" => "text/xml"
        ]);

        dd($response, 'RES');
    }

    function test_api06()
    {
        $response = consume_api(
            "https://onesignal.com/api/v1/notifications",
            'POST',
            ['x' => 'y'],
            [
                'Content-Type: application/json',
                'Authorization: Basic ' . 'xxxxxxxxxxxx'
            ],

            [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_HEADER => false
            ]
        );

        dd($response, 'RES');
    }

    function easy_sintax(){
        $base_url = "https://produzione.familyintale.com/create-personalized-tale_p/";

        $params = array ( 
            'name_b' => 'Andrea', 
            'name_p' => 'Pablo', 
            'genderkids' => 'm', 
            'genderparents' => 'm', 
            'characterkids' => 'bfb', 
            'characterparents' => 'gfb', 
            'tale_language' => 'es', 
            'tale_story' => 'gu', 
        );

        $url = Url::buildUrl($base_url, $params);

        $client = ApiClient::instance()
        ->disableSSL()
        ->redirect()
        ->get($url);

        if ($client->status() != 200){
            throw new \Exception($client->error());
        }

        dd(
            $client->data()         
        );
    }

    function new_sintax(){
        $base_url = "https://produzione.familyintale.com/create-personalized-tale_p/";

        $params = array ( 
            'name_b' => 'Andrea', 
            'name_p' => 'Pablo', 
            'genderkids' => 'm', 
            'genderparents' => 'm', 
            'characterkids' => 'bfb', 
            'characterparents' => 'gfb', 
            'tale_language' => 'es', 
            'tale_story' => 'gu', 
        );

        $url = Url::buildUrl($base_url, $params);

        $client = ApiClient::instance()
        ->disableSSL()
        ->redirect()
        ->setUrl($url)
        //->setBody($body)
        ->setMethod(ApiClient::HTTP_METH_GET);

        try {
            $client->send();

            if ($client->status() != 200){
                throw new \Exception($client->error());
            }

            dd(
                $client->getBody()
            );
        } catch (\Exception $ex) {
            dd($ex);
        }
       
    }

}

