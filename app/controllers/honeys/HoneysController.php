<?php

namespace Boctulus\Simplerest\Controllers\honeys;

use Boctulus\Simplerest\Core\Libs\DB;
use Boctulus\Simplerest\Core\Libs\Strings;
use Boctulus\Simplerest\Core\Libs\ApiClient;
use Boctulus\Simplerest\Core\Controllers\Controller;

/*
    http://ws.honeysplace.com/ws/xml/honeysinventoryv2_0.xml - XML Data Feed
    http://ws.honeysplace.com/ws/xml/honeysinventory_v_1.0.txt - Tab Delimited Data Feed
*/
class HoneysController extends Controller
{
    protected $endpoints = [
        'products_xml' => 'http://ws.honeysplace.com/ws/xml/honeysinventoryv2_0.xml',
        'products_txt' => 'http://ws.honeysplace.com/ws/xml/honeysinventory_v_1.0.txt'
    ];

    function index()
    {
        $token  = 'GWP8BTI1WMRU0DZB';

        $client = ApiClient::instance()
        ->setHeaders(
            [
                "Content-type" => "text/xml",
                "Accept"       => "text/xml",
                "authToken" => "$token"
            ]
        )
        //->setBody($body)
        ->disableSSL()
        ->redirect()
        ->url($this->endpoints['products_xml']);

        $res = $client       
        ->get()
        ->getResponse();    
        
        dd($res, 'RES');
    }

    function test2(){
        $url = "https://www.honeysplace.com/ws/";

        $user = '23033DS';
        $pass = 'GWP8BTI1WMRU0DZB';

        $post_string = '<?xml version="1.0" encoding="UTF-8"?>
        <HPEnvelope>
            <account>' .$user.'</account>
            <password>'.$pass.'</password>
            <stockcheck>
                <sku>SE1101202</sku>
            </stockcheck>
        </HPEnvelope>';

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 4);
        curl_setopt ($ch, CURLOPT_POST, true);
        curl_setopt ($ch, CURLOPT_POSTFIELDS, "xmldata=".$post_string);

        $data = curl_exec($ch);
        $info = curl_getinfo($ch);

        if ($data === false || $info['http_code'] != 200) {
            $data = "No cURL data returned for $url [". $info['http_code']. "]";
            if (curl_error($ch)) {
                $data .= "\n". curl_error($ch);
            }
            echo $data;
            exit;
        }
        header("Content-Type: text/xml; charset=utf-8");
        echo $data;
    }
}

