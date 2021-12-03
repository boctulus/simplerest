<?php

namespace simplerest\controllers;

use Exception;
use simplerest\libs\Factory;
use simplerest\core\Controller;
use simplerest\libs\DB;
use Facebook\Exceptions\FacebookResponseException;
use Facebook\Exceptions\FacebookSDKException;
use simplerest\models\UsersModel;
use simplerest\models\UserRolesModel;
use simplerest\models\RolesModel;
use simplerest\libs\Debug;
use simplerest\libs\Strings;

class FacebookController extends Controller
{
    protected $client;

    protected $__email;
    protected $__username;
    protected $__password;

    function __construct()
    {
        parent::__construct();

        $this->u_class    = get_user_model_name();               
        $this->__email    = $this->u_class::$email;
        $this->__username = $this->u_class::$username;
        $this->__password = $this->u_class::$password;

        if (!session_id()) {
            session_start();
        }

        $fb = new \Facebook\Facebook([
            'app_id' => $this->config['facebook_auth']['app_id'], 
            'app_secret' => $this->config['facebook_auth']['app_secret'],
            'default_graph_version' => 'v3.2', 
        ]);

        $this->client = $fb;
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
    
    function login_or_register(){
        $fb      = $this->getClient();	
        $helper  = $fb->getRedirectLoginHelper();
		
		try {
			$access_token = $helper->getAccessToken();
		} catch(FacebookResponseException $e) {
			// When Graph returns an error
			Factory::response()->SendError('Graph returned an error: ' . $e->getMessage(), 400);
		} catch(FacebookSDKException $e) {
			// When validation fails or other local issues
			Factory::response()->SendError('Facebook SDK returned an error: ' . $e->getMessage(), 400);
			exit;
		}
			
		if (isset($access_token)) {
			$obj = $fb->get('/me?fields=id,first_name,last_name,email', $access_token);
			$me  = $obj->getGraphUser();
			
			//print_r($obj->getGraphUser());

            $fb_id     = $me->getId();
            $username  = $me->getName();
            $email     = $me->getEmail();
            $firstname = $me->getFirstName();
            $lastname  = $me->getLastName();

            /*
			var_dump($email);
			var_dump($firstname);
            var_dump($lastname);
            */
            
            DB::beginTransaction();

            try 
            {        
                $conn = $this->getConnection();	
                $u = new $this->u_class();

                $user = $u->where(['email', $email])->first();
                $uid  = $user[$this->__id];

                if (!empty($uid))
                { 
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

                    $is_active = $user['is_active'];

                }else{
                    $data['email']     = $email;
                    $data['firstname'] = $firstname ?? NULL;
                    $data['lastname']  = $lastname ?? NULL;
            
                    $exists = DB::table($this->users_table)
                    ->where(['username', $username])
                    ->exists();

                    if ($exists){
                        $username = Strings::match($email, '/[^@]+/');
                    }

                    $exists = DB::table($this->users_table)
                    ->where(['username', $username])
                    ->exists();

                    // quizás deba validar la longitud contra el schema de UsersModel
                    
                    if ($exists){
                        $_username = $username;
                        $append = 1;

                        while($exists){
                            $_username = $username . $append;
                            $exists = DB::table($this->users_table)->where(['username', $_username])->exists();
                            $append++;
                        }

                        $username = $_username;
                    }         
            
                    $data['username'] = $username;
                    ///
                
                    $uid = $u->create($data);
                    if (empty($uid))
                        throw new \Exception('Error in user registration!');
        
                    if ($u->inSchema(['belongs_to'])){
                        DB::table($this->users_table)
                        ->where(['id', $uid])
                        ->update(['belongs_to' => $uid]);
                    }

                    /*
                    $r = new RolesModel();
                    
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

			
		} else {
			if ($helper->getError()) {
				Factory::response()->SendError('Unauthorized', 401, $helper->getErrorDescription());
				/*				
				echo "Error: " . $helper->getError() . "\n";
				echo "Error Code: " . $helper->getErrorCode() . "\n";
				echo "Error Reason: " . $helper->getErrorReason() . "\n";
				echo "Error Description: " . $helper->getErrorDescription() . "\n";
				*/
			} else {
				Factory::response()->SendError('Bad request', 400);
			}
			exit;
		}
    }

    ///////////////////////////////////////////////

    function login(){
		$fb   = $this->getClient();
		$helper = $fb->getRedirectLoginHelper();
		
		$permissions = ['email'];
		$auth_url = $helper->getLoginUrl($this->config['facebook_auth']['callback_url'], $permissions);
        
        header("Location: $auth_url");
    }
	
}