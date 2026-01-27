<?php

namespace Boctulus\Simplerest\tests\Api;

require_once __DIR__ . '/ApiTestCase.php';

use Boctulus\Simplerest\Core\Libs\DB;

/**
 * Pagination tests for /api/v1/products endpoint
 */
class ProductsPaginationTest extends ApiTestCase
{
    public function testPageSizeDefault()
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
        $error_msg = curl_error($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if (curl_errno($ch)) {
            throw new \Exception("$error_msg ($http_code)");
        }

        if ($http_code != 200)
            throw new \Exception("Unexpected http code ($http_code)");

        $res = json_decode($response, true);

        $this->assertTrue(
            isset($res['data']) && isset($res['error']) && isset($res['paginator'])
        );

        $this->assertTrue(
            empty($res['error'])
        );

        $cnt = DB::table('products')->count();

        curl_close($ch);

        $this->assertTrue(
            count($res['data']) == min($this->config['paginator']['default_limit'], $cnt)
        );
    }

    public function testPageSizeCustom()
    {
        $ch = curl_init();

        curl_setopt_array($ch, array(
            CURLOPT_URL => BASE_URL . "api/v1/products?limit=5",
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
            isset($res['data']) && isset($res['error']) && isset($res['paginator'])
        );

        $this->assertTrue(
            empty($res['error'])
        );

        $cnt = DB::table('products')->count();

        // Handle different response structures depending on API version
        if (isset($res['data']['products'])) {
            // Nested structure when using limit/pageSize
            $responseData = $res['data']['products'];
        } else {
            // Direct structure
            $responseData = $res['data'];
        }

        curl_close($ch);

        $this->assertTrue(
            count($responseData) == min(5, $cnt)
        );
    }
}
