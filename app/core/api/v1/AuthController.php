<?php

namespace simplerest\core\api\v1;

use Exception;
use simplerest\core\Controller;
use simplerest\core\interfaces\IAuth;
use simplerest\core\Request;
use simplerest\libs\Factory;
use simplerest\libs\DB;
use simplerest\libs\Utils;
use simplerest\libs\Debug;
use simplerest\libs\Validator;
use simplerest\core\exceptions\InvalidValidationException;
use simplerest\libs\Files;


class AuthController extends Controller implements IAuth
{
    protected $users_table = 'users';
    protected $role_field  = 'rol';
    public $uid;

    function __construct()
    { 
        header('Access-Control-Allow-Credentials: True');
        header('Access-Control-Allow-Headers: Origin,Content-Type,X-Auth-Token,AccountKey,X-requested-with,Authorization,Accept, Client-Security-Token,Host,Date,Cookie,Cookie2'); 
        header('Access-Control-Allow-Methods: POST,OPTIONS'); 
        header('Access-Control-Allow-Origin: *');
        header('Content-Type: application/json; charset=UTF-8');

        parent::__construct();
    }
       
    protected function gen_jwt(array $props, string $token_type, int $expires_in = null){
        $time = time();

        $payload = [
            'alg' => $this->config[$token_type]['encryption'],
            'typ' => 'JWT',
            'iat' => $time, 
            'exp' => $time + ($expires_in != null ? $expires_in : $this->config[$token_type]['expiration_time']),
            'ip'  => Request::ip()
        ];
        
        $payload = array_merge($payload, $props);

        return \Firebase\JWT\JWT::encode($payload, $this->config[$token_type]['secret_key'],  $this->config[$token_type]['encryption']);
    }

    protected function gen_jwt_email_conf(string $email, array $roles, array $perms){
        $time = time();

        $payload = [
            'alg' => $this->config['email_token']['encryption'],
            'typ' => 'JWT',
            'iat' => $time, 
            'exp' => $time + $this->config['email_token']['expires_in'],
            'ip'  => Request::ip(),
            'email' => $email,
            'roles' => $roles,
            'permissions' => $perms
         ];

        return \Firebase\JWT\JWT::encode($payload, $this->config['email_token']['secret_key'],  $this->config['email_token']['encryption']);
    }

    protected function gen_jwt_rememberme($uid){
        $time = time();

        $payload = [
            'alg' => $this->config['email_token']['encryption'],
            'typ' => 'JWT',
            'iat' => $time, 
            'exp' => $time + $this->config['email_token']['expires_in'],
            'ip'  => Request::ip(),
            'uid' => $uid
         ];

        return \Firebase\JWT\JWT::encode($payload, $this->config['email_token']['secret_key'],  $this->config['email_token']['encryption']);
    }

    function login()
    {
        if (!in_array($_SERVER['REQUEST_METHOD'], ['POST','OPTIONS']))
            Factory::response()->sendError('Incorrect verb ('.$_SERVER['REQUEST_METHOD'].'), expecting POST',405);

        $data  = Factory::request()->getBody();

        if ($data == null)
            return;
            
        $email = $data->email ?? null;
        $username = $data->username ?? null;  
        $password = $data->password ?? null;         
        
        if (empty($email) && empty($username) ){
            Factory::response()->sendError('email or username are required',400);
        }else if (empty($password)){
            Factory::response()->sendError('password is required',400);
        }

        try {              
            $u = DB::table($this->users_table);

            $row = $u->assoc()->unhide(['password'])
            ->where([ 'email'=> $email, 'username' => $username ], 'OR')
            ->setValidator((new Validator())->setRequired(false))  
            ->first();

            if (!$row)
                throw new Exception("Incorrect username / email or password");

            $hash = $row['password'];

            if (!password_verify($password, $hash))
                Factory::response()->sendError('Incorrect username / email or password', 401);

            $active = 1;    
            if ($u->inSchema(['active'])){
                $active = $row['active']; 

                if ($active == null) {

                    if ($row['confirmed_email'] === "0") {
                        Factory::response()->sendError('Non authorized', 403, 'Please confirm your e-mail');
                    } else {
                        Factory::response()->sendError('Non authorized', 403, 'Account pending for activation');
                    }
                }

                if ($active == 0 || (string) $active === "0") {
                    Factory::response()->sendError('Non authorized', 403, 'Deactivated account !');
                } 
            }                

            // Fetch roles && permissions
            $acl   = Factory::acl();

            $uid   = $row[$u->getIdName()];            
            $roles = $u->inSchema([$this->role_field]) ? $row[$this->role_field] : $acl->fetchRoles($uid); 
            $perms = $acl->fetchPermissions($uid);

            //var_export($perms);

            $access  = $this->gen_jwt([ 'uid' => $uid, 
                                        'roles' => $roles, 
                                        'permissions' => $perms,
                                        'active' => $active, 
            ], 'access_token');

            // el refresh no debe llevar ni roles ni permisos por seguridad !
            $refresh = $this->gen_jwt([ 'uid' => $uid
            ], 'refresh_token');

            Factory::response()->send([ 
                                        'access_token'=> $access,
                                        'token_type' => 'bearer', 
                                        'expires_in' => $this->config['access_token']['expiration_time'],
                                        'refresh_token' => $refresh,   
                                        'roles' => $roles,
                                        'uid' => $uid
                                        ]);
          
        } catch (InvalidValidationException $e) { 
            Factory::response()->sendError('Validation Error', 400, json_decode($e->getMessage()));
        } catch(\Exception $e){
            Factory::response()->sendError($e->getMessage());
        }	
        
    }


    // Recibe un refresh_token y en el body un campo "impersonate" 
    function impersonate()
    {
        if (!in_array($_SERVER['REQUEST_METHOD'], ['POST','OPTIONS']))
            Factory::response()->sendError('Incorrect verb ('.$_SERVER['REQUEST_METHOD'].'), expecting POST',405);

        $data  = Factory::request()->getBody();

        if ($data == null)
            return;
            
        if (!isset($data->uid) && !isset($data->role))
            Factory::response()->sendError('Bad request', 400, 'Nothing to impersonate');

        $request = Factory::request();

        $headers = $request->headers();
        $auth = $headers['Authorization'] ?? $headers['authorization'] ?? null;

        if (empty($auth)){
            Factory::response()->sendError('Authorization not found',400);
        }

        //print_r($auth);

        try 
        {                                      
            list($refresh) = sscanf($auth, 'Bearer %s');

            $payload = \Firebase\JWT\JWT::decode($refresh, $this->config['refresh_token']['secret_key'], [ $this->config['refresh_token']['encryption'] ]);
            
            if (empty($payload))
                Factory::response()->sendError('Unauthorized!',401);                     

            if (empty($payload->uid)){
                Factory::response()->sendError('uid is needed',400);
            }

            $acl   = Factory::acl();
            $u     = DB::table($this->users_table);

            if ($u->inSchema([$this->role_field])){
                $roles = [ $acl->getRoleName($u->find($payload->uid)->value($this->role_field)) ];
            } else {
                $roles = $acl->fetchRoles($payload->uid);
            }

            if (!$acl->hasSpecialPermission("impersonate", $roles) && !(isset($payload->impersonated_by) && !empty($payload->impersonated_by)) ){
                Factory::response()->sendError('Unauthorized!',401, 'Impersonate requires elevated privileges');
            }    

            $guest_role = $acl->getGuest();

            $impersonate_user = $data->uid ?? null;
            $impersonate_role = $data->role ?? null;
            
            if (!empty($impersonate_role)){
                if ($impersonate_role == $guest_role){
                    $uid = -1;
                    $roles = [$guest_role];
                    $perms = [];
                    $active = null;
                } else {

                    if (!$acl->roleExists($impersonate_role)){
                        Factory::response()->sendError("Bad request", 400, "Role $impersonate_role is not valid");
                    }

                    $uid = $payload->uid; // sigo siendo yo (el admin)
                    $roles = [$impersonate_role]; 
                    $perms = []; // permisos inalterados (rol puro)
                    $active = 1; // asumo está activo
                }    
            }


            if (!empty($impersonate_user)){ 
                $uid = $impersonate_user;

                $u = DB::table($this->users_table);

                $row = $u->assoc()
                ->find($uid) 
                ->first();

                if (!$row)
                    throw new Exception("User to impersonate does not exist");

                $active = true;    
                if ($u->inSchema(['active'])){
                    $active = $row['active'];

                    if ($active === NULL) {
                        Factory::response()->sendError('Account to be impersonated is pending for activation', 500);
                    } elseif (((string) $active === "0")) {
                        Factory::response()->sendError('User account to be impersonated is deactivated', 500);
                    }  
                }
                
                $roles = $u->inSchema([$this->role_field]) ? $row[$this->role_field] : $acl->fetchRoles($uid);
                $perms = $acl->fetchPermissions($uid);
            }    

            $impersonated_by = $payload->impersonated_by ?? $payload->uid;

            $access  = $this->gen_jwt([ 'uid' => $uid, 
                                        'roles' => $roles, 
                                        'permissions' => $perms,
                                        'impersonated_by' => $impersonated_by,
                                        'active' => $active,
            ], 'access_token');

            $refresh  = $this->gen_jwt(['uid' => $uid, 
                                        'impersonated_by' => $impersonated_by
            ], 'refresh_token');

            $res = [ 
                'access_token'=> $access,
                'refresh_token' => $refresh,
                'token_type' => 'bearer', 
                'expires_in' => $this->config['access_token']['expiration_time'],
                'roles' => $roles,
                'uid' => $uid,
                'impersonated_by' => $impersonated_by
            ];

    
            Factory::response()->send($res);      

        } catch (\Exception $e) {
            Factory::response()->sendError($e->getMessage(), 400);
        }	
                                                    
    }

    // a diferencia de token() si bien renueva el access_token no lo hace a partir de ....
    function stop_impersonate() 
    {
        if (!in_array($_SERVER['REQUEST_METHOD'], ['POST','OPTIONS']))
            Factory::response()->sendError('Incorrect verb ('.$_SERVER['REQUEST_METHOD'].'), expecting POST',405);

        $request = Factory::request();

        $headers = $request->headers();
        $auth = $headers['Authorization'] ?? $headers['authorization'] ?? null;

        if (empty($auth)){
            Factory::response()->sendError('Authorization not found',400);
        }

        try {                                      
            list($refresh) = sscanf($auth, 'Bearer %s');

            $payload = \Firebase\JWT\JWT::decode($refresh, $this->config['refresh_token']['secret_key'], [ $this->config['refresh_token']['encryption'] ]);
            
            if (empty($payload))
                Factory::response()->sendError('Unauthorized!',401);                     

            if (empty($payload->uid)){
                Factory::response()->sendError('uid is needed',400);
            }

            if (empty($payload->impersonated_by)){
                Factory::response()->sendError('Unauthorized!', 401, 'There is no admin behind this');
            }
            
        } catch (\Exception $e) {
            Factory::response()->sendError($e->getMessage(), 400);
        }	

        $uid = $payload->impersonated_by;
        
        $acl   = Factory::acl();
        $roles = $acl->fetchRoles($uid);
        $perms = $acl->fetchPermissions($uid);

        //////
        
        try {              
            
            $access  = $this->gen_jwt([ 'uid' => $uid, 
                                        'roles' => $roles, 
                                        'permissions' => $perms,
                                        'active' => 1
            ], 'access_token');

            $refresh = $this->gen_jwt([ 'uid' => $uid,
            ], 'refresh_token');

            Factory::response()->send([ 
                                        'uid' => $uid,           
                                        'access_token'=> $access,
                                        'token_type' => 'bearer', 
                                        'expires_in' => $this->config['access_token']['expiration_time'],
                                        'refresh_token' => $refresh,   
                                        'roles' => $roles
                                    ]);
          
        } catch (InvalidValidationException $e) { 
            Factory::response()->sendError('Validation Error', 400, json_decode($e->getMessage()));
        } catch(\Exception $e){
            Factory::response()->sendError($e->getMessage());
        }	
    }

    /*
        Access Token renewal
    */	
    function token()
    {
        if (!in_array($_SERVER['REQUEST_METHOD'], ['POST','OPTIONS']))
            Factory::response()->sendError('Incorrect verb ('.$_SERVER['REQUEST_METHOD'].'), expecting POST',405);

        $request = Factory::request();

        $headers = $request->headers();
        $auth = $headers['Authorization'] ?? $headers['authorization'] ?? null;

        if (empty($auth)){
            Factory::response()->sendError('Authorization not found',400);
        }

        //print_r($auth);

        try {                                      
            // refresh token
            list($refresh) = sscanf($auth, 'Bearer %s');

            $payload = \Firebase\JWT\JWT::decode($refresh, $this->config['refresh_token']['secret_key'], [ $this->config['refresh_token']['encryption'] ]);

            if (empty($payload))
                Factory::response()->sendError('Unauthorized!',401);                     

            if (!isset($payload->uid) || empty($payload->uid)){
                Factory::response()->sendError('uid is needed',400);
            }

            if ($payload->exp < time())
                Factory::response()->sendError('Token expired, please log in',401);


            $uid = $payload->uid;
            $impersonated_by = $payload->impersonated_by ?? null;
            $impersonated_by_role = null;

            if ($impersonated_by) {
                // guest
                if ($payload->uid == -1) 
                {
                    $acl   = Factory::acl();

                    $active = false;
                    $roles = [$acl->getGuest()];
                    $perms = [];
                } else {
                    $impersonated_by_role = true;
                }
            }     

            if (!$impersonated_by || $impersonated_by_role) 
            {
                $u = DB::table($this->users_table);

                $row = $u->assoc()
                ->where([$u->getIdName() => $payload->uid])->first();

                if (!$row)
                    throw new \Exception("User not found");

                $active = 1;    
                if ($u->inSchema(['active'])){
                    $active = $row['active']; 

                    if ($active == 0 || (string) $active === "0") {
                        Factory::response()
                        ->sendError('Non authorized', 403, 'Deactivated account !');
                    }
                }

                $acl   = $acl ?? Factory::acl();
                $roles = $u->inSchema([$this->role_field]) ? $row[$this->role_field] : $acl->fetchRoles($uid); 
                $perms = $acl->fetchPermissions($uid);
            }            

          
            $access  = $this->gen_jwt([ 'uid' => $payload->uid,
                                        'roles' => $roles, 
                                        'permissions' => $perms, 
                                        'impersonated_by' => $impersonated_by,
                                        'active' => $active
                                    ], 
            'access_token');

            ///////////
            $res = [ 
                'uid' => $payload->uid,
                'access_token'=> $access,
                'token_type' => 'bearer', 
                'expires_in' => $this->config['access_token']['expiration_time'],
                'roles' => $roles
            ];

            if (isset($payload->impersonated_by) && $payload->impersonated_by != null){
                $res['impersonated_by'] = $impersonated_by;
            }

            Factory::response()->send($res);
            
        } catch (\Exception $e) {
            Factory::response()->sendError($e->getMessage(), 400);
        }	
    }

    /*
        Minimizar la cantidad de instancias de UsersModel !!!!!!!!!
    */
    function register()
    {
        global $api_version;

        if (!in_array($_SERVER['REQUEST_METHOD'], ['POST','OPTIONS']))
            Factory::response()->sendError('Incorrect verb ('.$_SERVER['REQUEST_METHOD'].'), expecting POST',405);
            
        DB::beginTransaction();

        try {
            $data  = Factory::request()->getBody(false);

            if ($data == null)
                Factory::response()->sendError('Bad request',400, 'Invalid JSON');
 
        
            $acl = Factory::acl();
            $u   = DB::table($this->users_table);

            $many_to_many = false;

            // un campo 'rol' o similar
            if ($u->inSchema([$this->role_field])){
                if (!empty($data[$this->role_field])) {
                    if (isset($this->config['auto_approval_roles']) && !empty($this->config['auto_approval_roles'])) {
                    
                        if (!in_array($acl->getRoleName($data[$this->role_field]), $this->config['auto_approval_roles'])) {
                            throw new \Exception("Role {$data[$this->role_field]} is not auto-approved");
                        }

                    }    
                } else {
                    // chequear si es requerido antes
                    Factory::response()->sendError("rol is required", 400);
                }  

                $roles = [ $data[$this->role_field] ];   
            } else {
                // una tabla 'roles' en relación muchos a muchos (debería segurarme)
                $many_to_many = true; // debería ser pre-condición

                $roles = [];    
                if (!empty($data['roles'])) {
                    if (isset($this->config['auto_approval_roles']) && !empty($this->config['auto_approval_roles'])) {
    
                        if (!is_array($data['roles'])){
                            $data['roles'] = [ $data['roles'] ];
                        }

                        foreach ($data['roles'] as $r){
                            if (!in_array($r, $this->config['auto_approval_roles'])) {
                                throw new \Exception("Role $r is not auto-approved");
                            }
    
                            $roles[] = $r;
                        }                    
                    }    
                
                    unset($data['roles']);
                }        
            }

            $missing = $u->getMissing($data);
            if (!empty($missing))
                Factory::response()->sendError('Bad request', 400, 'There are missing attributes in your request: '.implode(',',$missing));

            $email_in_schema = $u->inSchema(['email']);

            if ($email_in_schema)
            {
                // se hace en el modelo pero es más rápido hacer chequeos acá

                if (empty($data['email']))
                    throw new Exception("Email is empty");
                    
                if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL))
                    throw new Exception("Invalid email");  

                if (DB::table($this->users_table)->where(['email', $data['email']])->exists())
                    Factory::response()->sendError('Email already exists');    
            }            

            if (DB::table($this->users_table)->where(['username', $data['username']])->exists())
                Factory::response()->sendError('Username already exists');

            if ($u->inSchema(['active'])){  
                $u->fill(['active']);      
                $data['active'] = $this->config['pre_activated'] ?? false;
            }

            $uid = $u
            ->setValidator(new Validator())
            ->create($data);

            if (empty($uid))
                throw new \Exception('Error creating user');
            
            if ($many_to_many && !empty($roles))
            {
                $acl->addUserRoles($roles, $uid);
            }     
            
            /* 
                Email confirmation

                (debería ser con un hook y enviar correo)
            */  
            if (!$this->config['pre_activated']){  
                $email_confirmation = $email_in_schema && $u->inSchema(['confirmed_email']);

                if ($email_confirmation)
                {                 
                    $exp = time() + $this->config['email_token']['expires_in'];
                    $base_url =  HTTP_PROTOCOL . '://' . $_SERVER['HTTP_HOST'] . ($this->config['BASE_URL'] == '/' ? '/' : $this->config['BASE_URL']) ;
                    $token = $this->gen_jwt_email_conf($data['email'], $roles, []);
                    $url = $base_url . (!$this->config['REMOVE_API_SLUG'] ? "api/$api_version" : $api_version) . '/auth/confirm_email/' . $token . '/' . $exp; 
                } 
            }
                
            $access  = $this->gen_jwt([
                                        'uid' => $uid, 
                                        'roles' => $roles,
                                        'permissions' => [],
                                        'active' => $this->config['pre_activated'] ? true : null
            ], 'access_token');

            $refresh = $this->gen_jwt([
                                        'uid' => $uid
            ], 'refresh_token');

            $res = [ 
                'uid' => $uid,
                'access_token'=> $access,
                'token_type' => 'bearer', 
                'expires_in' => $this->config['access_token']['expiration_time'],
                'refresh_token' => $refresh,
                'roles' => $roles
            ];

            
            if (isset($email_confirmation) && $email_confirmation){
                Files::logger("Email confirmation link $url");
                //dd($url, 'email_confirmation_link');
            }
                

            DB::commit();    
            Factory::response()->send($res);

        } catch (InvalidValidationException $e) { 
            DB::rollback();
            Factory::response()->sendError('Validation Error', 400, json_decode($e->getMessage()));
        }catch(\Exception $e){
            DB::rollback();
            Factory::response()->sendError($e->getMessage());
        }	
            
    }

    private function jwtPayload(){
        $auth = Factory::request()->getAuth();

        if (empty($auth))
            return;
            
        list($jwt) = sscanf($auth, 'Bearer %s');

        if($jwt != null)
        {
            try{
                $payload = \Firebase\JWT\JWT::decode($jwt, $this->config['access_token']['secret_key'], [ $this->config['access_token']['encryption'] ]);
                
                if (empty($payload))
                    Factory::response()->sendError('Unauthorized!',401);             

                if (!isset($payload->ip) || empty($payload->ip))
                    Factory::response()->sendError('Unauthorized',401,'Lacks IP in web token');

                if ($payload->ip != Request::ip())
                    Factory::response()->sendError('Unauthorized!',401, 'IP change'); 

                if (!isset($payload->uid) || empty($payload->uid))
                    Factory::response()->sendError('Unauthorized',401,'Lacks id in web token');  

                // Lacks active status
                if (DB::table($this->users_table)->inSchema(['active']) && !isset($payload->active) && $payload->uid != -1){
                    Factory::response()->sendError('Unauthorized', 401, 'Lacks active status. Please log in.');
                }    

                if ($payload->active === false) {
                    Factory::response()->sendError('Non authorized', 403, 'Deactivated account');
                } 
                                                  
                if ($payload->exp < time())
                    Factory::response()->sendError('Token expired',401);

                //print_r($payload->roles);
                //fexit; 

                return json_decode(json_encode($payload),true);

            } catch (\Exception $e) {
                /*
                * the token was not able to be decoded.
                * this is likely because the signature was not able to be verified (tampered token)
                *
                * reach this point if token is empty or invalid
                */
                Factory::response()->sendError($e->getMessage(),401);
            }	
        }else{
            Factory::response()->sendError('Authorization jwt token not found',400);
        }

    }

    /* 
    Authorization checkin
    
        @return mixed array | null
    */
    function check() {
        static $ret;

        if ($ret != null)
            return $ret;

        switch (Factory::request()->authMethod()){
            case 'API_KEY': 
                $api_key = Factory::request()->getApiKey();

                $acl = Factory::acl();
                $uid = $acl->getUserIdFromApiKey($api_key);

                if ($uid == NULL){
                    Factory::response()->sendError('Invalid API Key', 401);
                }

                $u = DB::table($this->users_table);

                if ($u->inSchema([$this->role_field])){
                    $rid   = $u->where([$u->getIdName() => $uid])->value($this->role_field);
                    $roles = [ $acl->getRoleName($rid) ]; 
                } else {
                    $roles = $acl->fetchRoles($uid);
                }
                
                $ret = [
                    'uid'           => $uid,
                    'roles'         => $roles,
                    'permissions'   => $acl->fetchPermissions($uid),
                    'active'        => true //
                ];
            break;
            case 'JWT':
                $ret = $this->jwtPayload();

                if (DB::table($this->users_table)->inSchema([$this->role_field])){
                    $ret['roles'] = [ Factory::acl()->getRoleName($ret['roles']) ]; 
                } 
            break;
            default:
                $ret = [
                    'uid' => null,
                    'roles' => [Factory::acl()->getGuest()],
                    'permissions' => [],
                    'active' => null
                ];
        }

        $this->uid = $ret['uid'];

        return $ret;
    }

    
    /*
        Proviene de un link generado en register()

        Debería haber otro método que genere el mismo enlace
    */
	function confirm_email($jwt, $exp)
	{
		if (!in_array($_SERVER['REQUEST_METHOD'], ['GET','OPTIONS']))
            Factory::response()->sendError('Incorrect verb ('.$_SERVER['REQUEST_METHOD'].'), expecting GET',405);

		// Es menos costoso verificar así en principio
		if ((int) $exp < time()) {
            Factory::response()->sendError('Link is outdated', 400);
        }         

        if($jwt != null)
        {
            try {
                $payload = \Firebase\JWT\JWT::decode($jwt, $this->config['email_token']['secret_key'], [ $this->config['email_token']['encryption'] ]);
                
                if (empty($payload))
                    $error = 'Unauthorized!';                     

                if (!isset($payload->email) || empty($payload->email)){
                    $error = 'email is needed';
                }

                if ($payload->exp < time())
                    $error = 'Token expired';
                
                $u = DB::table($this->users_table);

                if (!$u->inSchema(['confirmed_email'])){
                    Factory::response()->sendError('Email confirmation is not implemented', 501);
                }    

                $rows = $u->assoc()
                ->select([$u->getIdName()])
                ->when($u->inSchema(['active']), function($q){
                    $q->addSelect('active');
                })
                ->where(['email', $payload->email])
                ->get();

                if (count($rows) == 0){
                    Factory::response()->sendError("Not found", 404, "Email not found");
                }

                if ($u->inSchema(['active'])){
                    if ((string) $rows[0]['active'] === "0") {
                        Factory::response()->sendError('Non authorized', 403, 'Deactivated account !');
                    }
                }
                
                $uid  = $rows[0][$u->getIdName()];
                
                $ok = $u
                ->fill(['confirmed_email'])
                ->update(['confirmed_email' => 1]);

            } catch (\Exception $e) {
                /*
                * the token was not able to be decoded.
                * this is likely because the signature was not able to be verified (tampered token)
                *
                * reach this point if token is empty or invalid
                */
                Factory::response()->sendError($e->getMessage(),401);
            }	
        }else{
            Factory::response()->sendError('Authorization jwt token not found',400);
        }     

        $roles = $payload->roles ?? [];
        $perms = $payload->permissions ?? [];

        $access  = $this->gen_jwt([ 'uid' => $uid,   
                                    'roles' => $roles, 
                                    'permissions' => $perms,
                                    'active' => 1                     // *
        ], 'access_token');

        $refresh = $this->gen_jwt(['uid' => $uid, 
        ], 'refresh_token');

        
        Factory::response()->send([
            'uid' => $uid,  
            'access_token' => $access,
            'token_type' => 'bearer', 
            'expires_in' => $this->config['access_token']['expiration_time'],
            'refresh_token' => $refresh,
            'roles' => $roles   
        ]);	

    }     
    
    /*
        Si el correo es válido debe generar y enviar por correo un enlance para cambiar el password
        sino no hacer nada.
    */
	function rememberme(){
		$data  = Factory::request()->getBody();

		if ($data == null)
			Factory::response()->sendError('Invalid JSON',400);

		$email = $data->email ?? null;

		if ($email == null)
			Factory::response()->sendError('Empty email', 400);

		try {	

			$u = (DB::table('users'))->assoc();
			$rows = $u->where(['email', $email])->get(['id', 'active']);

			if (count($rows) === 0){
                // Email not found
                Factory::response()->sendError('Please check your e-mail', 400); 
            }
		
            $uid = $rows[0]['id'];	//
            $exp = time() + $this->config['email_token']['expires_in'];	

            $active = $rows[0]['active'];

            if ((string) $active === "0") {
                Factory::response()->sendError('Non authorized', 403, 'Deactivated account !');
            }

            $base_url =  HTTP_PROTOCOL . '://' . $_SERVER['HTTP_HOST'] . ($this->config['BASE_URL'] == '/' ? '/' : $this->config['BASE_URL']) ;
            

            $token = $this->gen_jwt_rememberme($uid);
            
            $url = $base_url .'login/change_pass_by_link/' . $token . '/' . $exp; 	

		} catch (\Exception $e){
			Factory::response()->sendError($e->getMessage(), 500);
		}
    
        // Queue email
        $ok = (bool) DB::table('email_notifications')
        ->create([
            'to_addr'   => $email, 
            'to_name'    => '', 
            'subject'    => 'Cambio de contraseña', 
            'body'       => "Para cambiar la contraseña siga el enlace:<br/><a href='$url'>$url</a>"
        ]);

        /*
            Posteriormente leer la tabla email_notifications y....
            basado en un tamplate, hacer algo como:

            $mail_sent = Utils::send_mail($email, null, 'Recuperación de password', "Hola!<p/>Para re-establecer la el password siga el enlace</br>$url");
        */

        if (!$ok){
            Files::logger("remember-me error al agendar envio de correo a $email");
            exit;
        }

        Files::logger("remember-me $url");
        Factory::response()->send(['msg' => 'Por favor chequee su correo'], 200);         
    }
    

    /*
        Proviene de rememberme() y da la oportunidad de cambiar el pass otorgando tokens a tal fin
    */
    function change_pass_by_link($jwt = NULL, $exp = NULL){
        if (!in_array($_SERVER['REQUEST_METHOD'], ['GET','OPTIONS'])){
            Factory::response()->sendError('Incorrect verb ('.$_SERVER['REQUEST_METHOD'].'), expecting GET',405);
        }    

        if ($jwt == null || $exp == null){
            Factory::response()->sendError('Bad request', 400, 'Two paramters are expected');
        }

        // Es menos costoso verificar así en principio
        if ((int) $exp < time()) {
            Factory::response()->sendError('Link is outdated', 401);
        } else {

            if($jwt != null)
            {
                try {
                    $payload = \Firebase\JWT\JWT::decode($jwt, $this->config['email_token']['secret_key'], [ $this->config['email_token']['encryption'] ]);
                    
                    if (empty($payload))
                        Factory::response()->sendError('Unauthorized!',401);                     

                    if (empty($payload->uid)){
                        Factory::response()->sendError('uid is needed',400);
                    }

                    $uid = $payload->uid;

                    $acl   = Factory::acl();
                    

                    $u = DB::table($this->users_table);

                    if ($u->inSchema([$this->role_field])){
                        $rid   = $u->find($uid)->value($this->role_field);
                        $roles = [ $acl->getRoleName($rid) ]; 
                    } else {
                        $roles = $acl->fetchRoles($uid);
                    }
                    
                    $perms = $acl->fetchPermissions($uid);


                    $row = $u->assoc()
                    ->where([$u->getIdName() => $uid]) 
                    ->first();

                    if (!$row)
                        throw new Exception("Uid not found");

                    $active = true;    
                    if ($u->inSchema(['active'])){    
                        $active = $row['active'];                     

                        if ($active === false) {
                            Factory::response()->sendError('Non authorized', 403, 'Deactivated account');
                        }
                    }    

                    if ($payload->exp < time())
                        Factory::response()->sendError('Token expired, please log in',401);

                    $access  = $this->gen_jwt([ 'uid' => $uid,
                                                'roles' => $roles, 
                                                'permissions' => $perms, 
                                                'active' => $active
                    ], 'access_token');
                    
                    $refresh  = $this->gen_jwt([ 
                                                'uid' => $uid
                    ], 'refresh_token');

                    ///////////
                    Factory::response()->send([ 
                                    'uid' => $uid,
                                    'access_token'=> $access,
                                    'refresh_token'=> $refresh,
                                    'token_type' => 'bearer', 
                                    'expires_in' => $this->config['access_token']['expiration_time'],
                                    'roles' => $roles,
                                    'permissions' => $perms                                            
                    ]);
                    

                } catch (\Exception $e) {
                    /*
                    * the token was not able to be decoded.
                    * this is likely because the signature was not able to be verified (tampered token)
                    *
                    * reach this point if token is empty or invalid
                    */
                    Factory::response()->sendError($e->getMessage(),401);
                }	
            }else{
                Factory::response()->sendError('Authorization jwt token not found',400);
            }     
        }	

    }   
    
    function change_pass_process()
    {
        if (!in_array($_SERVER['REQUEST_METHOD'], ['POST','OPTIONS']))
            Factory::response()->sendError('Incorrect verb ('.$_SERVER['REQUEST_METHOD'].'), expecting POST',405);

        $data  = Factory::request()->getBody();

        if ($data == null)
            return;

        if (!isset($data->password) || empty($data->password))
            Factory::response()->sendError('Bad request', 400, 'Lacks password in request');

        $request = Factory::request();

        $headers = $request->headers();
        $auth = $headers['Authorization'] ?? $headers['authorization'] ?? null;

        if (empty($auth)){
            Factory::response()->sendError('Authorization not found',400);
        }

        try {                                             
            list($refresh) = sscanf($auth, 'Bearer %s');

            $payload = \Firebase\JWT\JWT::decode($refresh, $this->config['email_token']['secret_key'], [ $this->config['refresh_token']['encryption'] ]);
            
            if (empty($payload))
                Factory::response()->sendError('Unauthorized!',401);                     

            if (empty($payload->uid)){
                Factory::response()->sendError('uid is needed',400);
            }

            $ok = DB::table('users')
            ->find($payload->uid)
            ->update([
                'password' => $data->password
            ]);

            if (!$ok){
                Factory::response()->sendError("Unexpected error trying to update password", 500); 
            }

            $uid = $payload->uid;
            
            $u = DB::table($this->users_table);
            $row = $u->find($uid)->first();


            $active = 1;    
            if ($u->inSchema(['active'])){
                $active = $row['active']; 

                if ($active == null) {

                    if ($row['confirmed_email'] === "0") {
                        Factory::response()->sendError('Non authorized', 403, 'Please confirm your e-mail');
                    } else {
                        Factory::response()->sendError('Non authorized', 403, 'Account pending for activation');
                    }
                }

                if ($active == 0 || (string) $active === "0") {
                    Factory::response()->sendError('Non authorized', 403, 'Deactivated account !');
                } 
            }                

            // Fetch roles && permissions
            $acl   = Factory::acl();

            $uid   = $payload->uid;            
            $roles = $acl->fetchRoles($uid); 
            $perms = $acl->fetchPermissions($uid);

            //var_export($perms);

            $access  = $this->gen_jwt([ 'uid' => $uid, 
                                        'roles' => $roles, 
                                        'permissions' => $perms,
                                        'active' => $active, 
            ], 'access_token');

            // el refresh no debe llevar ni roles ni permisos por seguridad !
            $refresh = $this->gen_jwt([ 'uid' => $uid
            ], 'refresh_token');

            Factory::response()->send([ 
                                        'access_token'=> $access,
                                        'token_type' => 'bearer', 
                                        'expires_in' => $this->config['access_token']['expiration_time'],
                                        'refresh_token' => $refresh,   
                                        'roles' => $roles,
                                        'uid' => $uid
                                        ]);

        } catch (\Exception $e) {
            /*
            * the token was not able to be decoded.
            * this is likely because the signature was not able to be verified (tampered token)
            *
            * reach this point if token is empty or invalid
            */
            Factory::response()->sendError($e->getMessage(),401);
        }	

    }


}
