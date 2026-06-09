<?php

namespace Boctulus\Simplerest\tests\Api;

require_once __DIR__ . '/ApiTestCase.php';

use Boctulus\Simplerest\Core\Libs\DB;

/**
 * Basic GET tests for /api/v1/products endpoint
 */
class ProductsBasicTest extends ApiTestCase
{
    /*
        /api/v1/products
    */
	public function testGetProducts()
    {
        $ch = curl_init();

        curl_setopt_array($ch, array(
            CURLOPT_URL => BASE_URL . "api/v1/products",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => array(
                "Accept: */*",
                "Accept-Encoding: gzip, deflate",
                "Cache-Control: no-cache",
                "Connection: keep-alive",
                "Authorization: Bearer $this->at",
                "Content-Type: application/json",
                "Host: " . HOST,
                "cache-control: no-cache"
                ),
            ));

        curl_setopt($ch, CURLOPT_FAILONERROR, true);

        $response = curl_exec($ch);
        $err = curl_error($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if (curl_errno($ch)) {
            $error_msg = curl_error($ch);
            throw new \Exception("$error_msg ($http_code)");
        }

        if ($http_code != 200)
            throw new \Exception("Unexpected http code ($http_code)");

        $res = json_decode($response, true);

        $this->assertTrue(
            isset($res['data']) && isset($res['paginator'])
        );

        $model_arr = DB::table('products')->where(['belongs_to', $this->uid])->assoc()->limit($this->config['paginator']['default_limit'])->get();

        curl_close($ch);

        $this->assertEquals($model_arr,$res['data']);
    }

    /*
        get
        Case: found
    */
    public function testGetProduct()
    {
        $ch = curl_init();

        $id  = DB::table('products')->where(['belongs_to' => $this->uid])->random()->value('id');

        curl_setopt_array($ch, array(
            CURLOPT_URL => BASE_URL . "api/v1/products/$id",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => array(
                "Accept: */*",
                "Accept-Encoding: gzip, deflate",
                "Cache-Control: no-cache",
                "Connection: keep-alive",
                "Authorization: Bearer $this->at",
                "Content-Type: application/json",
                "Host: " . HOST,
                "cache-control: no-cache"
                ),
            ));

        curl_setopt($ch, CURLOPT_FAILONERROR, true);

        $response = curl_exec($ch);
        $error_msg = curl_error($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if (curl_errno($ch)) {
            throw new \Exception("$error_msg ($http_code)");
        }

        if ($http_code != 200)
            throw new \Exception("Unexpected http code ($http_code)");

        $res = json_decode($response, true);

        $this->assertTrue(
            isset($res['data']) && isset($res['error']) && !isset($res['paginator'])
        );

        $this->assertTrue(
            empty($res['error'])
        );

        $item = DB::table('products')->where(['id', $id])->assoc()->first();

        curl_close($ch);

        $this->assertEquals($item, $res['data']);
    }

    /*
        get
        Case: not found
    */
    public function testGetProductNotFound()
    {
        $model = DB::table('products');
        $idn = $model->getIdName();
        $non_existing_id = $model->max($idn) + 1;

        $ch = curl_init();

        curl_setopt_array($ch, array(
            CURLOPT_URL => BASE_URL . "api/v1/products/$non_existing_id",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => array(
                "Accept: */*",
                "Accept-Encoding: gzip, deflate",
                "Cache-Control: no-cache",
                "Connection: keep-alive",
                "Authorization: Bearer $this->at",
                "Content-Type: application/json",
                "Host: " . HOST,
                "cache-control: no-cache"
                ),
            ));

        curl_setopt($ch, CURLOPT_FAILONERROR, true);

        $response = curl_exec($ch);
        $error_msg = curl_error($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        curl_close($ch);

        $this->assertEquals($http_code, 404);
    }
}
