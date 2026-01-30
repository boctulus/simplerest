<?php

namespace Boctulus\Simplerest\tests;

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

require_once __DIR__ . '../../vendor/autoload.php';

if (php_sapi_name() != "cli"){
	return; 
}

require_once __DIR__ . '/../app.php';

use PHPUnit\Framework\TestCase;
use Boctulus\Simplerest\Core\Libs\DB;
use Boctulus\Simplerest\Core\Libs\Strings;
use Boctulus\Simplerest\Core\Model;
use Boctulus\Simplerest\Core\Traits\UnitTestCaseSQLTrait;
use Boctulus\Simplerest\Core\Libs\Validator;


define('HOST', parse_url($config['app_url'], PHP_URL_HOST));
define('BASE_URL', rtrim($config['app_url'], '/') . '/');

/**
 * @group refactor
 */
// API UNIT TEST
class ApiTest extends TestCase
{   
    private $uid;
    private $at;
    private $rt;
    protected $config;

	private function login($credentials){
		$ch = curl_init();

        $data = json_encode($credentials);

        curl_setopt_array($ch, array(
        CURLOPT_URL => BASE_URL . "api/v1/auth/login",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_POSTFIELDS => $data,
        CURLOPT_HTTPHEADER => array(
            "Accept: */*",
            "Accept-Encoding: gzip, deflate",
            "Cache-Control: no-cache",
            "Connection: keep-alive",
            "Content-Length: ". strlen($data),
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

        if (isset($res['error']) && !empty($res['error']))
            throw new \Exception($res['error']);

        $access_token = $res['data']['access_token'];
        $refresh_token = $res['data']['refresh_token'];

        curl_close($ch);

        return [$access_token, $refresh_token];
    }

    private function get_me(string $at){
        $ch = curl_init();

        curl_setopt_array($ch, array(
            CURLOPT_URL => BASE_URL . "api/v1/me",
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


        if (!isset($res['data']['id']) || !isset($res['data']['email']))
            throw new \Exception("Empty uid or email");

        $data = $res['data'];

        curl_close($ch);

        // [id, username, emai,... ]
        return $data;
    }

    protected function setUp(): void {
        parent::setUp();
        $this->config = \Boctulus\Simplerest\Core\Libs\Config::get();

        list($this->at, $this->rt) = $this->login(['email' => "tester3@g.c", "password" => "gogogo"]);
        $this->uid = $this->get_me($this->at)['id'];
    }

    /*
        /api/v1/me
        Case: OK
    */
	function testgetme()
    {
        $ch = curl_init();

        curl_setopt_array($ch, array(
            CURLOPT_URL => BASE_URL . "api/v1/me",
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
            isset($res['data']['id']) && isset($res['data']['email'])
        );

    curl_close($ch);
    }
    
    /*
        /api/v1/products
    */
	function testgetproducts()
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

        $this->assertEquals($model_arr,$res['data']);

    curl_close($ch);
    }
    
    
    /*
        get
        Case: found
    */
    function testgetproduct()
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
        //dd(DB::getQueryLog());

        //dd($item);
        //dd($res['data']);

        $this->assertEquals($item, $res['data']);

    curl_close($ch);
    }

    /*
        get
        Case: not found
    */
    function testgetproductnotfound()
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
        
        $this->assertEquals($http_code, 404);

    curl_close($ch);
    }

    function testpagesize1a()
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

        $this->assertTrue(
            count($res['data']) == min($this->config['paginator']['default_limit'], $cnt)
        );

    curl_close($ch);
    }

    function testpagesize1b()
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

        $this->assertTrue(
            count($responseData) == min(5, $cnt)
        );

    curl_close($ch);
    }

    private function get_rand_fields($table, $count = 1, $nullables = false){
        $m = DB::table($table);

        $ff = $m->getNotHidden();
        $ff = array_diff($ff, ['belongs_to', 'created_at', 'deleted_at', 'updated_at', 'id']);

        if (!$nullables)
            $ff = array_intersect($ff, $m->getNotNullables());

        $ixs  = array_rand($ff, $count);

        $fields = [];
        foreach ((array) $ixs as $ix){
            $fields[] = $ff[$ix];
        }

        return $fields;
    }

    private function get_rand_vals($table, $field, $count = 1, $user_id = null){
        $m = DB::table($table);

        if ($user_id != null)
            $m->where(['belongs_to', $user_id]);

        return $m->random()->limit($count)->pluck($field);
    }

    private function get_rand($table, $num_values = 1, $nullables = false){
        $m = DB::table($table);

        $ff = $m->getNotHidden();
        $ff = array_diff($ff, ['belongs_to', 'deleted_at', 'id']);
        
        if ($nullables)
            $ff = array_intersect($ff, $m->getNullables());

        $values = [];

        $field  = $ff[array_rand($ff, 1)];

        while (count($values) < $num_values){
            $stuck  = false;
            $val    = null; 

            while ($val == null || $stuck){
                $i = 0;
                $stuck = false;

                if ($stuck){
                    $field = $ff[array_rand($ff, 1)];
                }
                
                $val = DB::table($table)->whereNotNull($field)->random()->value($field);
                while(trim($val) == ''){
                    $i++;
                    $val = DB::table($table)->whereNotNull($field)->random()->value($field);
                    if ($i>5){
                        $stuck = true;
                        break;
                    }                
                }
                //
                $ff = array_diff($ff, [$field]);
                $values[] = $val;
            }

        }
            
        return [$field, $values];
    }

    function testfilter001()
    {
        list($field, $vals) =  $this->get_rand('products');

        $ch = curl_init();

        curl_setopt_array($ch, array(
            CURLOPT_URL => BASE_URL . "api/v1/products?$field=". urlencode($vals[0]),
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

        $model_arr = DB::table('products')
        ->where(['belongs_to' => $this->uid, $field => $vals[0]])->assoc()->limit($this->config['paginator']['default_limit'])->get();

        $this->assertEquals($model_arr,$res['data']);

    curl_close($ch);
    }

    function testfilter001b()
    {
        list($field, $vals) = $this->get_rand('products');

        $ch = curl_init();

        curl_setopt_array($ch, array(
            CURLOPT_URL => BASE_URL . "api/v1/products?{$field}[eq]=". urlencode($vals[0]),
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
            isset($res['data']) && (isset($res['paginator']) || isset($res['products']))
        );

        $model_arr = DB::table('products')
        ->where(['belongs_to' => $this->uid, $field => $vals[0]])->assoc()->limit($this->config['paginator']['default_limit'])->get();

        // Handle different response structures depending on API version
        if (isset($res['data']['products'])) {
            // Nested structure when using filters
            $responseData = $res['data']['products'];
        } else {
            // Direct structure
            $responseData = $res['data'];
        }

        if ($model_arr != $responseData){
            dd(DB::getLog());
            dd($model_arr, 'MODELO:');
            dd(BASE_URL . "api/v1/products?{$field}[eq]=". urlencode($vals[0]));
            dd($responseData, 'API response:');
        }

        $this->assertEquals($model_arr, $responseData);

    curl_close($ch);
    }

    function testfilter002()
    {
        $fields = $this->get_rand_fields('products', 2);

        $values = DB::table('products')
        ->random()
        ->where(['belongs_to', $this->uid])
        ->select($fields)->first();

        //dd($values);

        $fv = [];
        $w  = [];
        
        foreach ($fields as $field){
            $fv[] = $field . '=' . urlencode($values->$field);

            if ($values->$field == NULL){
                $op = 'IS NULL';
            }else
                $op = '=';                

            $w[] = [$field, $values->$field, $op]; 
        }
        
        $url_params = implode('&', $fv);
        //dd($url_params);

        $ch = curl_init();

        curl_setopt_array($ch, array(
            CURLOPT_URL => BASE_URL . "api/v1/products?$url_params",
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

        $model_arr = DB::table('products')
        ->where(['belongs_to', $this->uid])
        ->where($w)->assoc()->limit($this->config['paginator']['default_limit'])->get();

        //dd(DB::getQueryLog()); 

        $this->assertEquals($model_arr,$res['data']);

    curl_close($ch);
    }

    function testfilter003()
    {
        $field  = $this->get_rand_fields('products')[0];
        $values = $this->get_rand_vals('products', $field, 2, $this->uid);

        $values_str = implode(',', array_map('urlencode',$values));
        //dd("api/v1/products?{$field}[in]=$values_str");

        $ch = curl_init();

        curl_setopt_array($ch, array(
            CURLOPT_URL => BASE_URL . "api/v1/products?{$field}[in]=$values_str",
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

        $model_arr = DB::table('products')->where([
            ['belongs_to', $this->uid], 
            [$field, $values ]
        ])
        ->assoc()->limit($this->config['paginator']['default_limit'])->get();

        $this->assertEquals($model_arr, $res['data']);

    curl_close($ch);
    }

    function testfilter003b()
    {
        $field  = $this->get_rand_fields('products')[0];
        $values = $this->get_rand_vals('products', $field, 2, $this->uid);

        $values_str = implode(',', array_map('urlencode',$values));
        //dd("api/v1/products?$field=$values_str");

        $ch = curl_init();

        curl_setopt_array($ch, array(
            CURLOPT_URL => BASE_URL . "api/v1/products?$field=$values_str",
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

        $model_arr = DB::table('products')->where([
            ['belongs_to', $this->uid], 
            [$field, $values ]
        ])
        ->assoc()->limit($this->config['paginator']['default_limit'])->get();

        $this->assertEquals($model_arr, $res['data']);

    curl_close($ch);
    }

    function testfilter004()
    {
        $field  = $this->get_rand_fields('products')[0];
        $values = $this->get_rand_vals('products', $field, 2, $this->uid);

        $values_str = implode(',', array_map('urlencode',$values));
        //dd("api/v1/products?{$field}[in]=$values_str");

        $ch = curl_init();

        curl_setopt_array($ch, array(
            CURLOPT_URL => BASE_URL . "api/v1/products?{$field}[notIn]=$values_str",
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

        $model_arr = DB::table('products')->where([
            ['belongs_to', $this->uid], 
            [$field, $values, 'NOT IN' ]
        ])
        ->assoc()->limit($this->config['paginator']['default_limit'])->get();

        $this->assertEquals($model_arr, $res['data']);

    curl_close($ch);
    }

    function testfilter006()
    {
        $ch = curl_init();

        curl_setopt_array($ch, array(
            CURLOPT_URL => BASE_URL . "api/v1/products?name[startsWith]=Caja%20",
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

        $model_arr = DB::table('products')->where([
            ['belongs_to', $this->uid], 
            ['name', 'Caja %', 'LIKE']
        ])
        ->assoc()->limit($this->config['paginator']['default_limit'])->get();

        $this->assertEquals($model_arr,$res['data']);

    curl_close($ch);
    }

    function testfilter007()
    {
        $ch = curl_init();

        curl_setopt_array($ch, array(
            CURLOPT_URL => BASE_URL . "api/v1/products?name[notStartsWith]=Caja%20",
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

        $model_arr = DB::table('products')->where([
            ['belongs_to', $this->uid], 
            ['name', 'Caja %', 'NOT LIKE']
        ])
        ->assoc()->limit($this->config['paginator']['default_limit'])->get();

        $this->assertEquals($model_arr,$res['data']);

    curl_close($ch);
    }

    function testfilter008()
    {
        $ch = curl_init();

        curl_setopt_array($ch, array(
            CURLOPT_URL => BASE_URL . "api/v1/products?name[endsWith]=ora",
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
        $res = json_decode($response, true);

        //dd($res, 'Response:');

        if (curl_errno($ch)) {
            $error_msg = curl_error($ch);

            if (isset($res['error']))
                dd($res['error']);

            throw new \Exception("$error_msg ($http_code)");
        }
        
        if ($http_code != 200)
            throw new \Exception("Unexpected http code ($http_code)");

        $this->assertTrue(
            isset($res['data']) && isset($res['paginator'])
        );

        $model_arr = DB::table('products')->where([
            ['belongs_to', $this->uid], 
            ['name', '%ora', 'LIKE']
        ])
        ->assoc()->limit($this->config['paginator']['default_limit'])->get();

        $this->assertEquals($model_arr,$res['data']);

    curl_close($ch);
    }

    function testfilter009()
    {
        $ch = curl_init();

        curl_setopt_array($ch, array(
            CURLOPT_URL => BASE_URL . "api/v1/products?name[notEndsWith]=ora",
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
        $res = json_decode($response, true);

        //dd($res, 'Response:');

        if (curl_errno($ch)) {
            $error_msg = curl_error($ch);

            if (isset($res['error']))
                dd($res['error']);

            throw new \Exception("$error_msg ($http_code)");
        }
        
        if ($http_code != 200)
            throw new \Exception("Unexpected http code ($http_code)");

        $this->assertTrue(
            isset($res['data']) && isset($res['paginator'])
        );

        $model_arr = DB::table('products')->where([
            ['belongs_to', $this->uid], 
            ['name', '%ora', 'NOT LIKE']
        ])
        ->assoc()->limit($this->config['paginator']['default_limit'])->get();

        $this->assertEquals($model_arr,$res['data']);

    curl_close($ch);
    }

    function testfilter010()
    {
        $ch = curl_init();

        curl_setopt_array($ch, array(
            CURLOPT_URL => BASE_URL . "api/v1/products?name[contains]=%20de%20",
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
        $res = json_decode($response, true);

        if (curl_errno($ch)) {
            $error_msg = curl_error($ch);

            if (isset($res['error']))
                dd($res['error']);

            throw new \Exception("$error_msg ($http_code)");
        }
        
        if ($http_code != 200)
            throw new \Exception("Unexpected http code ($http_code)");

        $this->assertTrue(
            isset($res['data']) && isset($res['paginator'])
        );

        $model_arr = DB::table('products')->where([
            ['belongs_to', $this->uid], 
            ['name', '% de %', 'LIKE']
        ])
        ->assoc()->limit($this->config['paginator']['default_limit'])->get();

        $this->assertEquals($model_arr,$res['data']);

    curl_close($ch);
    }

    function testfilter011()
    {
        $ch = curl_init();

        curl_setopt_array($ch, array(
            CURLOPT_URL => BASE_URL . "api/v1/products?name[notContains]=%20de%20",
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
        $res = json_decode($response, true);

        if (curl_errno($ch)) {
            $error_msg = curl_error($ch);

            if (isset($res['error']))
                dd($res['error']);

            throw new \Exception("$error_msg ($http_code)");
        }
        
        if ($http_code != 200)
            throw new \Exception("Unexpected http code ($http_code)");

        $this->assertTrue(
            isset($res['data']) && isset($res['paginator'])
        );

        $model_arr = DB::table('products')->where([
            ['belongs_to', $this->uid], 
            ['name', '% de %', 'NOT LIKE']
        ])
        ->assoc()->limit($this->config['paginator']['default_limit'])->get();

        $this->assertEquals($model_arr,$res['data']);

    curl_close($ch);
    }

    /*
        Comparadores numéricos
    */

    function testfilter012()
    {
        $ch = curl_init();

        curl_setopt_array($ch, array(
            CURLOPT_URL => BASE_URL . "api/v1/products?cost[neq]=100",
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
        $res = json_decode($response, true);

        if (curl_errno($ch)) {
            $error_msg = curl_error($ch);

            if (isset($res['error']))
                dd($res['error']);

            throw new \Exception("$error_msg ($http_code)");
        }
        
        if ($http_code != 200)
            throw new \Exception("Unexpected http code ($http_code)");

        $this->assertTrue(
            isset($res['data']) && isset($res['paginator'])
        );

        $model_arr = DB::table('products')->where([
            ['belongs_to', $this->uid], 
            ['cost', 100, '!=']
        ])
        ->assoc()->limit($this->config['paginator']['default_limit'])->get();

        $this->assertEquals($model_arr,$res['data']);

    curl_close($ch);
    }

    function testfilter013()
    {
        $ch = curl_init();

        curl_setopt_array($ch, array(
            CURLOPT_URL => BASE_URL . "api/v1/products?cost[gt]=100",
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
        $res = json_decode($response, true);

        if (curl_errno($ch)) {
            $error_msg = curl_error($ch);

            if (isset($res['error']))
                dd($res['error']);

            throw new \Exception("$error_msg ($http_code)");
        }
        
        if ($http_code != 200)
            throw new \Exception("Unexpected http code ($http_code)");

        $this->assertTrue(
            isset($res['data']) && isset($res['paginator'])
        );

        $model_arr = DB::table('products')->where([
            ['belongs_to', $this->uid], 
            ['cost', 100, '>']
        ])
        ->assoc()->limit($this->config['paginator']['default_limit'])->get();

        $this->assertEquals($model_arr,$res['data']);

    curl_close($ch);
    }

    function testfilter014()
    {
        $ch = curl_init();

        curl_setopt_array($ch, array(
            CURLOPT_URL => BASE_URL . "api/v1/products?cost[lt]=100",
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
        $res = json_decode($response, true);

        if (curl_errno($ch)) {
            $error_msg = curl_error($ch);

            if (isset($res['error']))
                dd($res['error']);

            throw new \Exception("$error_msg ($http_code)");
        }
        
        if ($http_code != 200)
            throw new \Exception("Unexpected http code ($http_code)");

        $this->assertTrue(
            isset($res['data']) && isset($res['paginator'])
        );

        $model_arr = DB::table('products')->where([
            ['belongs_to', $this->uid], 
            ['cost', 100, '<']
        ])
        ->assoc()->limit($this->config['paginator']['default_limit'])->get();

        $this->assertEquals($model_arr,$res['data']);

    curl_close($ch);
    }

    function testfilter015()
    {
        $ch = curl_init();

        curl_setopt_array($ch, array(
            CURLOPT_URL => BASE_URL . "api/v1/products?cost[gteq]=100",
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
        $res = json_decode($response, true);

        if (curl_errno($ch)) {
            $error_msg = curl_error($ch);

            if (isset($res['error']))
                dd($res['error']);

            throw new \Exception("$error_msg ($http_code)");
        }
        
        if ($http_code != 200)
            throw new \Exception("Unexpected http code ($http_code)");

        $this->assertTrue(
            isset($res['data']) && isset($res['paginator'])
        );

        $model_arr = DB::table('products')->where([
            ['belongs_to', $this->uid], 
            ['cost', 100, '>=']
        ])
        ->assoc()->limit($this->config['paginator']['default_limit'])->get();

        $this->assertEquals($model_arr,$res['data']);

    curl_close($ch);
    }

    function testfilter016()
    {
        $ch = curl_init();

        curl_setopt_array($ch, array(
            CURLOPT_URL => BASE_URL . "api/v1/products?cost[lteq]=100",
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
        $res = json_decode($response, true);

        if (curl_errno($ch)) {
            $error_msg = curl_error($ch);

            if (isset($res['error']))
                dd($res['error']);

            throw new \Exception("$error_msg ($http_code)");
        }
        
        if ($http_code != 200)
            throw new \Exception("Unexpected http code ($http_code)");

        $this->assertTrue(
            isset($res['data']) && isset($res['paginator'])
        );

        $model_arr = DB::table('products')->where([
            ['belongs_to', $this->uid], 
            ['cost', 100, '<=']
        ])
        ->assoc()->limit($this->config['paginator']['default_limit'])->get();

        $this->assertEquals($model_arr,$res['data']);

    curl_close($ch);
    }

    function testfilter017()
    {
        $ch = curl_init();

        curl_setopt_array($ch, array(
            CURLOPT_URL => BASE_URL . "api/v1/products?order[cost]=ASC&cost[between]=50,100",
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
        $res = json_decode($response, true);

        if (curl_errno($ch)) {
            $error_msg = curl_error($ch);

            if (isset($res['error']))
                dd($res['error']);

            throw new \Exception("$error_msg ($http_code)");
        }
        
        if ($http_code != 200)
            throw new \Exception("Unexpected http code ($http_code)");

        $this->assertTrue(
            isset($res['data']) && isset($res['paginator'])
        );

        $model_arr = DB::table('products')->where([
            ['belongs_to', $this->uid], 
            ['cost', 100, '<='],
            ['cost', 50, '>=']
        ])
        ->assoc()->limit($this->config['paginator']['default_limit'])->orderBy(['cost' => 'ASC'])->get();

        $this->assertEquals($model_arr,$res['data']);

    curl_close($ch);
    }

    function testfilter018()
    {
        $ch = curl_init();

        curl_setopt_array($ch, array(
            CURLOPT_URL => BASE_URL . "api/v1/products?order[created_at]=ASC&created_at[between]=2019-11-02+16%3A07%3A10,2019-11-03+21%3A33%3A20",
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
        $res = json_decode($response, true);

        if (curl_errno($ch)) {
            $error_msg = curl_error($ch);

            if (isset($res['error']))
                dd($res['error']);

            throw new \Exception("$error_msg ($http_code)");
        }
        
        if ($http_code != 200)
            throw new \Exception("Unexpected http code ($http_code)");

        $this->assertTrue(
            isset($res['data']) && isset($res['paginator'])
        );

        $model_arr = DB::table('products')->where([
            ['belongs_to', $this->uid], 
            ['created_at', '2019-11-02 16:07:10', '>='],
            ['created_at', '2019-11-03 21:33:20', '<=']
        ])
        ->assoc()->limit($this->config['paginator']['default_limit'])->orderBy(['created_at' => 'ASC'])->get();

        $this->assertEquals($model_arr,$res['data']);

    curl_close($ch);
    }

    function testfilter019()
    {
        /*
            Elegir N campos al azar.....
        */
        $not_hidden = DB::table('products')->getNotHidden();
        $cnt = count($not_hidden);
        $cnt = $cnt > 1 ? ceil($cnt / 2)  : $cnt;
        $ixs = array_rand($not_hidden, $cnt);

        $fields = [];
        foreach ($ixs as $ix){
            $fields[] = $not_hidden[$ix];
        }
        
        $fields_str = implode(',',$fields);

        $ch = curl_init();

        curl_setopt_array($ch, array(
            CURLOPT_URL => BASE_URL . "api/v1/products?fields=$fields_str",
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
        $res = json_decode($response, true);

        if (curl_errno($ch)) {
            $error_msg = curl_error($ch);

            if (isset($res['error']))
                dd($res['error']);

            throw new \Exception("$error_msg ($http_code)");
        }
        
        if ($http_code != 200)
            throw new \Exception("Unexpected http code ($http_code)");

        $this->assertTrue(
            isset($res['data']) && isset($res['paginator'])
        );

        $model_arr = DB::table('products')->where([
            ['belongs_to', $this->uid]
        ])
        ->assoc()->limit($this->config['paginator']['default_limit'])->get($fields);

        $this->assertEquals($model_arr,$res['data']);

    curl_close($ch);
    }

    function testfilter020()
    {
        $id  = DB::table('products')->where([
            ['belongs_to', $this->uid]
        ])->random()->value('id');

        /*
            Elegir N campos al azar.....
        */
        $not_hidden = DB::table('products')->getNotHidden();
        $cnt = count($not_hidden);
        $cnt = $cnt > 1 ? ceil($cnt / 2)  : $cnt;
        $ixs = array_rand($not_hidden, $cnt);

        $fields = [];
        foreach ($ixs as $ix){
            $fields[] = $not_hidden[$ix];
        }

        $fields_str = implode(',',$fields);

        $ch = curl_init();

        curl_setopt_array($ch, array(
            CURLOPT_URL => BASE_URL . "api/v1/products/$id?fields=$fields_str",
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
        $res = json_decode($response, true);

        if (curl_errno($ch)) {
            $error_msg = curl_error($ch);

            if (isset($res['error']))
                dd($res['error']);

            throw new \Exception("$error_msg ($http_code)");
        }
        
        if ($http_code != 200)
            throw new \Exception("Unexpected http code ($http_code)");

        $model_arr = DB::table('products')->where([
            'id' => $id
        ])
        ->assoc()->first($fields);

        $this->assertEquals($model_arr,$res['data']);

    curl_close($ch);
    }

    function testfilter021()
    {
        $id  = DB::table('products')->where([
            ['belongs_to', $this->uid]
        ])->random()->value('id');

        $not_hidden = DB::table('products')->getNotHidden();
        $field = $not_hidden[array_rand($not_hidden,1)];

        $ch = curl_init();

        curl_setopt_array($ch, array(
            CURLOPT_URL => BASE_URL . "api/v1/products/$id?exclude=$field",
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
        $res = json_decode($response, true);

        if (curl_errno($ch)) {
            $error_msg = curl_error($ch);

            if (isset($res['error']))
                dd($res['error']);

            throw new \Exception("$error_msg ($http_code)");
        }
        
        if ($http_code != 200)
            throw new \Exception("Unexpected http code ($http_code)");

        $model_arr = DB::table('products')->where([
            'id' => $id
        ])
        ->assoc()->hide([$field])->first();

        //dd(DB::getQueryLog()); 

        $this->assertEquals($model_arr,$res['data']);

    curl_close($ch);
    }

    function testfilter022()
    {
        $nullables = DB::table('products')->getNullables();
        if (count($nullables) == 0)
            return;

        $ch = curl_init();

        curl_setopt_array($ch, array(
            CURLOPT_URL => BASE_URL . "api/v1/products?{$nullables[0]}=NULL",
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
        $res = json_decode($response, true);

        if (curl_errno($ch)) {
            $error_msg = curl_error($ch);

            if (isset($res['error']))
                dd($res['error']);

            throw new \Exception("$error_msg ($http_code)");
        }
        
        if ($http_code != 200)
            throw new \Exception("Unexpected http code ($http_code)");

        $model_arr = DB::table('products')
        ->where(['belongs_to' => $this->uid])
        ->where([ 
            $nullables[0] => NULL
        ])
        ->assoc()->get();

        //dd(DB::getQueryLog()); 

        $this->assertEquals($model_arr,$res['data']);

    curl_close($ch);
    }

    function testfilter023()
    {
        $nullables = DB::table('products')->getNullables();
        if (count($nullables) == 0)
            return;

        $ch = curl_init();

        curl_setopt_array($ch, array(
            CURLOPT_URL => BASE_URL . "api/v1/products?{$nullables[0]}[neq]=NULL",
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
        $res = json_decode($response, true);

        if (curl_errno($ch)) {
            $error_msg = curl_error($ch);

            if (isset($res['error']))
                dd($res['error']);

            throw new \Exception("$error_msg ($http_code)");
        }
        
        if ($http_code != 200)
            throw new \Exception("Unexpected http code ($http_code)");

        $model_arr = DB::table('products')
        ->where(['belongs_to', $this->uid])
        ->where([ 
            $nullables[0],  NULL, 'IS NOT'
        ])
        ->assoc()->limit($this->config['paginator']['default_limit'])->get();

        //dd(DB::getQueryLog()); 
        //dd($model_arr);
        //dd($res['data']);

        $this->assertEquals($model_arr, $res['data']);

    curl_close($ch);
    }

    /*
        Test for null! operator bug fix
        GET /api/v1/products?comment=null!
        Should find products where comment IS NULL

        Note: Using 'comment' field because it's nullable in the schema
    */
    function testNullOperator()
    {
        $ch = curl_init();

        // First, create a product with NULL comment for testing
        $product_data = json_encode([
            'name' => 'Test Product Null Comment',
            'cost' => 100,
            'description' => 'Test description',
            'slug' => 'test-null-comment-' . time(),
            'images' => '[]',
            'comment' => null
        ]);

        curl_setopt_array($ch, array(
            CURLOPT_URL => BASE_URL . "api/v1/products",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => $product_data,
            CURLOPT_HTTPHEADER => array(
                "Authorization: Bearer $this->at",
                "Content-Type: application/json",
                "Host: " . HOST,
            ),
        ));

        $response = curl_exec($ch);
        $res = json_decode($response, true);
        $product_id = $res['data']['id'] ?? null;

        $this->assertNotNull($product_id, 'Product should be created');

        // Now test searching with null! operator
        curl_setopt_array($ch, array(
            CURLOPT_URL => BASE_URL . "api/v1/products?comment=null!&limit=100",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => array(
                "Authorization: Bearer $this->at",
                "Content-Type: application/json",
                "Host: " . HOST,
            ),
        ));

        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $res = json_decode($response, true);

        $this->assertEquals(200, $http_code, 'Should return 200 OK');
        $this->assertIsArray($res['data'], 'Should return array of products');

        // Verify our created product is in the results
        $found = false;
        foreach ($res['data'] as $product) {
            if ($product['id'] == $product_id) {
                $found = true;
                $this->assertNull($product['comment'], 'Product comment should be NULL');
                break;
            }
        }

        $this->assertTrue($found, 'Created product with NULL comment should be found');

        // Cleanup: delete test product
        curl_setopt_array($ch, array(
            CURLOPT_URL => BASE_URL . "api/v1/products/$product_id",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => "DELETE",
            CURLOPT_HTTPHEADER => array(
                "Authorization: Bearer $this->at",
                "Content-Type: application/json",
                "Host: " . HOST,
            ),
        ));

        curl_exec($ch);
        curl_close($ch);
    }

    /*
        Test for empty string search bug fix
        GET /api/v1/products?comment=
        Should find products where comment = ''

        Note: Using 'comment' field because it's nullable in the schema
    */
    function testEmptyStringSearch()
    {
        $ch = curl_init();

        // First, create a product with empty comment for testing
        $product_data = json_encode([
            'name' => 'Test Product Empty Comment',
            'cost' => 100,
            'description' => 'Test description',
            'slug' => 'test-empty-comment-' . time(),
            'images' => '[]',
            'comment' => ''
        ]);

        curl_setopt_array($ch, array(
            CURLOPT_URL => BASE_URL . "api/v1/products",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => $product_data,
            CURLOPT_HTTPHEADER => array(
                "Authorization: Bearer $this->at",
                "Content-Type: application/json",
                "Host: " . HOST,
            ),
        ));

        $response = curl_exec($ch);
        $res = json_decode($response, true);
        $product_id = $res['data']['id'] ?? null;

        $this->assertNotNull($product_id, 'Product should be created');

        // Now test searching with empty string
        curl_setopt_array($ch, array(
            CURLOPT_URL => BASE_URL . "api/v1/products?comment=&limit=100",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => array(
                "Authorization: Bearer $this->at",
                "Content-Type: application/json",
                "Host: " . HOST,
            ),
        ));

        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $res = json_decode($response, true);

        $this->assertEquals(200, $http_code, 'Should return 200 OK');
        $this->assertIsArray($res['data'], 'Should return array of products');

        // Verify our created product is in the results
        $found = false;
        foreach ($res['data'] as $product) {
            if ($product['id'] == $product_id) {
                $found = true;
                $this->assertEquals('', $product['comment'], 'Product comment should be empty string');
                break;
            }
        }

        $this->assertTrue($found, 'Created product with empty comment should be found');

        // Cleanup: delete test product
        curl_setopt_array($ch, array(
            CURLOPT_URL => BASE_URL . "api/v1/products/$product_id",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => "DELETE",
            CURLOPT_HTTPHEADER => array(
                "Authorization: Bearer $this->at",
                "Content-Type: application/json",
                "Host: " . HOST,
            ),
        ));

        curl_exec($ch);
        curl_close($ch);
    }

    /*
        Test for IN operator with comma-separated values bug fix
        GET /api/v1/products?name=ProductA,ProductB,ProductC
        Should find products where name IN ('ProductA', 'ProductB', 'ProductC')
    */
    function testInOperatorWithCommas()
    {
        $ch = curl_init();

        // Create test products
        $test_names = ['TestProdA', 'TestProdB', 'TestProdC'];
        $created_ids = [];

        foreach ($test_names as $idx => $name) {
            $product_data = json_encode([
                'name' => $name,
                'cost' => 100,
                'description' => 'Test product for IN operator',
                'slug' => 'test-in-op-' . $idx . '-' . time(),
                'images' => '[]'
            ]);

            curl_setopt_array($ch, array(
                CURLOPT_URL => BASE_URL . "api/v1/products",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_CUSTOMREQUEST => "POST",
                CURLOPT_POSTFIELDS => $product_data,
                CURLOPT_HTTPHEADER => array(
                    "Authorization: Bearer $this->at",
                    "Content-Type: application/json",
                    "Host: " . HOST,
                ),
            ));

            $response = curl_exec($ch);
            $res = json_decode($response, true);
            $created_ids[] = $res['data']['id'] ?? null;
        }

        // Verify all products were created
        foreach ($created_ids as $id) {
            $this->assertNotNull($id, 'Product should be created');
        }

        // Now test searching with comma-separated values (auto IN operator)
        $search_names = implode(',', $test_names);
        curl_setopt_array($ch, array(
            CURLOPT_URL => BASE_URL . "api/v1/products?name=$search_names&limit=100",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => array(
                "Authorization: Bearer $this->at",
                "Content-Type: application/json",
                "Host: " . HOST,
            ),
        ));

        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $res = json_decode($response, true);

        $this->assertEquals(200, $http_code, 'Should return 200 OK');
        $this->assertIsArray($res['data'], 'Should return array of products');

        // Verify all created products are in the results
        $found_count = 0;
        foreach ($res['data'] as $product) {
            if (in_array($product['id'], $created_ids)) {
                $found_count++;
                $this->assertContains($product['name'], $test_names, 'Product name should be one of the search names');
            }
        }

        $this->assertEquals(count($test_names), $found_count, 'Should find all created products');

        // Cleanup: delete test products
        foreach ($created_ids as $id) {
            curl_setopt_array($ch, array(
                CURLOPT_URL => BASE_URL . "api/v1/products/$id",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_CUSTOMREQUEST => "DELETE",
                CURLOPT_HTTPHEADER => array(
                    "Authorization: Bearer $this->at",
                    "Content-Type: application/json",
                    "Host: " . HOST,
                ),
            ));

            curl_exec($ch);
        }

        curl_close($ch);
    }

    /*
        Test for explicit IN operator with [in] syntax
        GET /api/v1/products?name[in]=ProductA,ProductB,ProductC
        Should find products where name IN ('ProductA', 'ProductB', 'ProductC')
    */
    function testExplicitInOperator()
    {
        $ch = curl_init();

        // Create test products
        $test_names = ['ExplicitProdA', 'ExplicitProdB', 'ExplicitProdC', 'ExplicitProdD', 'ExplicitProdE'];
        $created_ids = [];

        foreach ($test_names as $idx => $name) {
            $product_data = json_encode([
                'name' => $name,
                'cost' => 100 + $idx * 10,
                'description' => 'Test product for explicit IN operator',
                'slug' => strtolower($name) . '-' . time() . '-' . $idx,
                'images' => '[]'
            ]);

            curl_setopt_array($ch, array(
                CURLOPT_URL => BASE_URL . "api/v1/products",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_CUSTOMREQUEST => "POST",
                CURLOPT_POSTFIELDS => $product_data,
                CURLOPT_HTTPHEADER => array(
                    "Authorization: Bearer $this->at",
                    "Content-Type: application/json",
                    "Host: " . HOST,
                ),
            ));

            $response = curl_exec($ch);
            $res = json_decode($response, true);
            $created_ids[] = $res['data']['id'] ?? null;
        }

        // Verify all products were created
        foreach ($created_ids as $idx => $id) {
            $this->assertNotNull($id, "Product {$test_names[$idx]} should be created");
        }

        // Test with explicit [in] syntax - search for first 3 products
        $search_names = implode(',', array_slice($test_names, 0, 3));
        $url = BASE_URL . "api/v1/products?name[in]=" . urlencode($search_names) . "&limit=100";

        curl_setopt_array($ch, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => array(
                "Authorization: Bearer $this->at",
                "Content-Type: application/json",
                "Host: " . HOST,
            ),
        ));

        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $res = json_decode($response, true);

        $this->assertEquals(200, $http_code, 'Should return 200 OK');
        $this->assertIsArray($res['data'], 'Should return array of products');

        // Verify only the first 3 products are in the results
        $found_ids = [];
        foreach ($res['data'] as $product) {
            if (in_array($product['id'], $created_ids)) {
                $found_ids[] = $product['id'];
                $this->assertContains($product['name'], array_slice($test_names, 0, 3),
                    'Product name should be one of the first 3 search names');
            }
        }

        $this->assertEquals(3, count($found_ids), 'Should find exactly 3 products');

        // Cleanup: delete all test products
        foreach ($created_ids as $id) {
            curl_setopt_array($ch, array(
                CURLOPT_URL => BASE_URL . "api/v1/products/$id",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_CUSTOMREQUEST => "DELETE",
                CURLOPT_HTTPHEADER => array(
                    "Authorization: Bearer $this->at",
                    "Content-Type: application/json",
                    "Host: " . HOST,
                ),
            ));

            curl_exec($ch);
        }

        curl_close($ch);
    }

    /*
        Test for auto-detected IN operator (comma-separated without [in])
        Verifies both implicit and explicit IN syntax work identically
    */
    function testAutoAndExplicitInComparison()
    {
        $ch = curl_init();

        // Create more test products to ensure robust testing
        $test_names = ['InTestA', 'InTestB', 'InTestC', 'InTestD', 'InTestE', 'InTestF'];
        $created_ids = [];

        foreach ($test_names as $idx => $name) {
            $product_data = json_encode([
                'name' => $name,
                'cost' => 200 + $idx * 5,
                'description' => 'Test product for IN comparison',
                'slug' => strtolower($name) . '-comp-' . time() . '-' . $idx,
                'images' => '[]'
            ]);

            curl_setopt_array($ch, array(
                CURLOPT_URL => BASE_URL . "api/v1/products",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_CUSTOMREQUEST => "POST",
                CURLOPT_POSTFIELDS => $product_data,
                CURLOPT_HTTPHEADER => array(
                    "Authorization: Bearer $this->at",
                    "Content-Type: application/json",
                    "Host: " . HOST,
                ),
            ));

            $response = curl_exec($ch);
            $res = json_decode($response, true);
            $created_ids[$name] = $res['data']['id'] ?? null;
        }

        // Verify all products were created
        foreach ($test_names as $name) {
            $this->assertNotNull($created_ids[$name], "Product $name should be created");
        }

        // Search names - select 4 out of 6
        $search_names = ['InTestB', 'InTestD', 'InTestE', 'InTestF'];
        $search_str = implode(',', $search_names);

        // Test 1: Auto-detected IN (comma-separated)
        curl_setopt_array($ch, array(
            CURLOPT_URL => BASE_URL . "api/v1/products?name=$search_str&limit=100",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => array(
                "Authorization: Bearer $this->at",
                "Content-Type: application/json",
                "Host: " . HOST,
            ),
        ));

        $response_auto = curl_exec($ch);
        $res_auto = json_decode($response_auto, true);

        // Test 2: Explicit IN with [in] syntax
        $url_explicit = BASE_URL . "api/v1/products?name[in]=" . urlencode($search_str) . "&limit=100";
        curl_setopt_array($ch, array(
            CURLOPT_URL => $url_explicit,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => array(
                "Authorization: Bearer $this->at",
                "Content-Type: application/json",
                "Host: " . HOST,
            ),
        ));

        $response_explicit = curl_exec($ch);
        $res_explicit = json_decode($response_explicit, true);

        // Both should return 200
        $this->assertIsArray($res_auto['data'], 'Auto IN should return array');
        $this->assertIsArray($res_explicit['data'], 'Explicit IN should return array');

        // Extract IDs from both results
        $ids_auto = [];
        $ids_explicit = [];

        foreach ($res_auto['data'] as $product) {
            if (in_array($product['name'], $test_names)) {
                $ids_auto[] = $product['id'];
            }
        }

        foreach ($res_explicit['data'] as $product) {
            if (in_array($product['name'], $test_names)) {
                $ids_explicit[] = $product['id'];
            }
        }

        // Both methods should find exactly 4 products
        $this->assertEquals(4, count($ids_auto), 'Auto IN should find 4 products');
        $this->assertEquals(4, count($ids_explicit), 'Explicit IN should find 4 products');

        // Both methods should return the same products
        sort($ids_auto);
        sort($ids_explicit);
        $this->assertEquals($ids_auto, $ids_explicit,
            'Auto IN and explicit IN should return identical results');

        // Verify the found products are exactly the ones we searched for
        foreach ($search_names as $name) {
            $this->assertContains($created_ids[$name], $ids_auto,
                "Product $name should be found by auto IN");
            $this->assertContains($created_ids[$name], $ids_explicit,
                "Product $name should be found by explicit IN");
        }

        // Verify products NOT in search are NOT returned
        $non_search_names = ['InTestA', 'InTestC'];
        foreach ($non_search_names as $name) {
            $this->assertNotContains($created_ids[$name], $ids_auto,
                "Product $name should NOT be found (not in search list)");
        }

        // Cleanup: delete all test products
        foreach ($created_ids as $name => $id) {
            curl_setopt_array($ch, array(
                CURLOPT_URL => BASE_URL . "api/v1/products/$id",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_CUSTOMREQUEST => "DELETE",
                CURLOPT_HTTPHEADER => array(
                    "Authorization: Bearer $this->at",
                    "Content-Type: application/json",
                    "Host: " . HOST,
                ),
            ));

            curl_exec($ch);
        }

        curl_close($ch);
    }

}
