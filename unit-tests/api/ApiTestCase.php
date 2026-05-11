<?php

namespace Boctulus\Simplerest\tests\Api;

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

require_once __DIR__ . '/../../vendor/autoload.php';

if (php_sapi_name() != "cli"){
	return;
}

require_once __DIR__ . '/../../app.php';

use PHPUnit\Framework\TestCase;
use Boctulus\Simplerest\Core\Libs\DB;
use Boctulus\Simplerest\Core\Libs\Config;

// Load config and define constants
$_config = Config::get();

if (!defined('HOST')) {
    define('HOST', parse_url($_config['app_url'], PHP_URL_HOST));
}

if (!defined('BASE_URL')) {
    define('BASE_URL', rtrim($_config['app_url'], '/') . '/');
}

/**
 * Base class for API tests
 * Contains common setup and helper methods
 */
abstract class ApiTestCase extends TestCase
{
    protected $uid;
    protected $at;
    protected $rt;
    protected $config;

    protected function login($credentials){
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

    protected function get_me(string $at){
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

    protected function get_rand_fields($table, $count = 1, $nullables = false){
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

    protected function get_rand_vals($table, $field, $count = 1, $user_id = null){
        $m = DB::table($table);

        if ($user_id != null)
            $m->where(['belongs_to', $user_id]);

        return $m->random()->limit($count)->pluck($field);
    }

    protected function get_rand($table, $num_values = 1, $nullables = false){
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

    protected function setUp(): void {
        parent::setUp();
        $this->config = Config::get();

        list($this->at, $this->rt) = $this->login(['email' => "tester3@g.c", "password" => "gogogo"]);
        $this->uid = $this->get_me($this->at)['id'];
    }
}
