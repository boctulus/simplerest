<?php

namespace simplerest\controllers;

use Exception;
use simplerest\core\controllers\Controller;
use simplerest\core\libs\DB;
use simplerest\models\UsersModel;
use simplerest\models\UserRolesModel;
use simplerest\models\RolesModel;
use simplerest\libs\Debug;

class GoogleController extends Controller
{
    protected $client;

    protected $__email;
    protected $__username;
    protected $__password;

    function __construct()
    {
        parent::__construct();

        $this->u_class = get_user_model_name();    
           
        $this->__email      = $this->u_class::$email;
        $this->__username   = $this->u_class::$username;
        $this->__password   = $this->u_class::$password;
		$this->__id 		= get_name_id($this->config['users_table']);

        $client = new \Google_Client();
        $client->setApplicationName(env('APP_NAME'));
        $client->setClientId($this->config['google_auth']['client_id']);
        $client->setClientSecret($this->config['google_auth']['client_secret']);
        $client->setRedirectUri($this->config['google_auth']['callback_url']);

        $client->setScopes('https://www.googleapis.com/auth/userinfo.email');
        #$client->addScope("https://www.googleapis.com/auth/drive");
        ## these two lines is important to get refresh token from google api
        $client->setAccessType('offline');
        // Set this to force to consent form to display.
        $client->setApprovalPrompt('force');
        $client->setIncludeGrantedScopes(true);

        $this->client = $client;
    }    

    function getClient(){
        return $this->client;
    }

    protected function gen_jwt(array $props, string $token_type){
        $time = time();

        $payload = [
            'alg' => $this->config[$token_type]['encryption'],
            'typ' => 'JWT',
            'iat' => $time, 
            'exp' => $time + $this->config[$token_type]['expiration_time'],
            'ip'  => $_SERVER['REMOTE_ADDR']
        ];
        
        $payload = array_merge($payload, $props);

        return \Firebase\JWT\JWT::encode($payload, $this->config[$token_type]['secret_key'],  $this->config[$token_type]['encryption']);
    }
    
    function login_or_register()
    {
        if (isset($_GET['code'])) {
            $auth = $this->client->fetchAccessTokenWithAuthCode($_GET['code']);

            if (isset($auth['error']))
                return ['error' => $auth['error_description'], 'code' => 400];

            $this->client->setAccessToken($auth);
        }else
            return ['error' => 'Invalid', 'code' => 400];

        /*
        if ($this->client->isAccessTokenExpired() )    
            $auth = $this->client->getAccessToken();

            $access_token = $auth['access_token'];
            //
            ...
        }
        */

        //dd($auth);        
        /*
            Array
            (
                [access_token] => ya29.Il-iB22898tm...........

                [expires_in] => 3599

                [refresh_token] => 1//0hMcct5o95Tf-Cg.........
                
                [scope] => https://www.googleapis.com/auth/userinfo.email openid https://www.googleapis.com/auth/userinfo.profile https://www.googleapis.com/auth/drive
                
                [token_type] => Bearer
                
                [id_token] => eyJhbGciOiJSUzI1NiIsImtpZCI6IjhhNjNmZTcxZTUzMDY3NTI0Y2JiYzZhM2E1ODQ2M2IzODY0YzA3ODciLCJ0eXAiOiJKV1QifQ............
                
                [created] => 1571713360)
        */

        $payload = $this->client->verifyIdToken($auth['id_token'], $this->config['google_auth']['client_id']);
        /*
        array(14) { 
            ["iss"]=> string(27) "https://accounts.google.com" 
            ["azp"]=> string(72) "228180780767-4p8t6nvocukmu44ti57o60n1ck6sokpd.apps.googleusercontent.com" 
            ["aud"]=> string(72) "228180780767-4p8t6nvocukmu44ti57o60n1ck6sokpd.apps.googleusercontent.com" 
            ["sub"]=> string(21) "100334249456746149636" 
            ["email"]=> string(18) "boctulus@gmail.com" 
            ["email_verified"]=> bool(true) 
            ["at_hash"]=> string(22) "3nV4aKuMu_FNft90PLjXjg" ["name"]=> string(13) "Pablo Bozzolo" ["picture"]=> string(89) "https://lh3.googleusercontent.com/a-/AAuE7mATeEoHq5modzihrb_z1O-3_BhxhJ4Q9dXSKGtxNw=s96-c" 
            ["given_name"]=> string(5) "Pablo" 
            ["family_name"]=> string(7) "Bozzolo" 
            ["locale"]=> string(5) "en-GB" 
            ["iat"]=> int(1571714620) 
            ["exp"]=> int(1571718220) } 
        */

        //dd($payload);
        
        if (!$payload)
            exit;


        DB::beginTransaction();

        try 
        {        
            $u = (new $this->u_class())->assoc();

            $row = $u->where([$this->__email, $payload['email']])->first();

            // Email already exists
            if (!empty($row)){
                
                $uid = $row[$this->__id];
                $roles = DB::table('user_roles')->where(['user_id' => $uid]);
                           
                $_permissions = DB::table('user_tb_permissions')
                ->assoc()
                ->select([
                    'tb', 'can_create as c', 'can_show as r', 'can_update as u', 'can_delete as d', 'can_list as l'
                ])
                ->where(['user_id' => $uid])
                ->get();

                $perms = [];
                foreach ($_permissions as $p){
                    $tb = $p['tb'];
                    $perms[$tb] = $p['la'] * 64 + $p['ra'] * 32 +  $p['l'] * 16 + $p['r'] * 8 + $p['c'] * 4 + $p['u'] * 2 + $p['d'];
                }
                
                $is_active = $row['is_active'];

            } else {
                // Faltaría ver como se acomoda si la tabla "users" no tiene equivalente a firstname y lastname

                $data[$this->__email]     = $payload['email'];
                $data['firstname']        = $payload['given_name'] ?? NULL;
                $data['lastname']         = $payload['family_name'] ?? NULL;

                ///
                preg_match('/[^@]+/', $payload['email'], $matches);
                $username = substr($matches[0], 0, 12);
        
                $existe = DB::table($this->users_table)->where([$this->__username, $username])->exists();
                
                if ($existe){
                    $_username = $username;
                    $append = 1;
                    while($existe){
                        $_username = $username . $append;
                        $existe = DB::table($this->users_table)->where([$this->__username, $_username])->exists();
                        $append++;
                    }
                    $username = $_username;
                }         
        
                $data[$this->__username] = $username;
                ///
                
                $uid = $u->create($data);
                if (empty($uid))
                    return ['error' => 'Error in user registration!', 'code' => 500];
    
                if ($u->inSchema([$u->belongsTo()])){
                    DB::table($this->users_table)
                    ->where([$this->__id, $uid])
                    ->update([$u->belongsTo() => $uid]);
                }

                /*
                if (!empty($this->config['registration_role'])){
                    $role = $this->config['registration_role'];

                    $r  = new RolesModel();
                    $ur = DB::table('userRoles');

                    $role_id = $r->get_role_id($role);

                    if ($role_id == null){
                        throw new Exception('Invalid default registration role');
                    }

                    $id = $ur->create([ 
                                        'belongs_to' => $uid, 
                                        'role_id' => $role_id 
                                        ]);  

                    if (empty($id))
                        throw new Exception('Error registrating user role');          

                    $roles = [$role];  
                } else {
                    $roles = [];
                }   
                */
                
                $is_active = $this->config['pre_activated'] ? true : null;
            }  
            
            $roles = []; //
            $perms = [];

            $access  = $this->gen_jwt([
                                        'uid' => $uid, 
                                        'roles' => $roles,
                                        'tb_permissions' => $perms,
                                        'is_active' => $is_active
            ], 'access_token');

            $refresh = $this->gen_jwt([
                            'uid' => $uid
            ], 'refresh_token');

            // podría incluir google_auth' => $auth en el access_token

            DB::commit();

            return ['code' => 200,  
                    'data' => [ 
                                'uid' => $uid,
                                'access_token'=> $access,
                                'token_type' => 'bearer', 
                                'expires_in' => $this->config['access_token']['expiration_time'],
                                'refresh_token' => $refresh  
                    ],
                    'error' => ''
            ];
        }catch(\Exception $e){
            DB::rollback();

            return ['error' => $e->getMessage(), 'code' => 500];
        }	

    }       
    
    ///////////////////////////////////////////////

    function login(){
        $client   = $this->getClient();
        $auth_url = $client->createAuthUrl();
        
        header("Location: $auth_url");
    }

	
}