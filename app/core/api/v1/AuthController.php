<?php

namespace simplerest\core\api\v1;

use Exception;
use simplerest\core\controllers\Controller;
use simplerest\core\Request;
use simplerest\core\Response;
use simplerest\core\libs\Factory;
use simplerest\core\libs\DB;
use simplerest\core\libs\Strings;
use simplerest\core\Acl;
use simplerest\core\libs\Validator;
use simplerest\core\exceptions\InvalidValidationException;
use simplerest\core\libs\Files;

use simplerest\core\interfaces\IAuth;
use simplerest\core\interfaces\IDbAccess;


class AuthController extends Controller implements IAuth
{
    protected $role_field;
    
    protected $__email;
    protected $__username;
    protected $__password;
    protected $__confirmed_email;
    protected $__active;

    static protected $current_user_uid; //
    static protected $current_user_permissions = []; //
    static protected $current_user_roles = []; //

    function __construct()
    { 
        cors();

        parent::__construct();

        $model = get_user_model_name();    
           
        $this->__email           = $model::$email;
        $this->__username        = $model::$username;
        $this->__password        = $model::$password;
        $this->__confirmed_email = $model::$confirmed_email;
        $this->__active          = $model::$is_active;

        $this->__id = get_id_name($this->users_table);
    }
       
    protected function gen_jwt(array $props, string $token_type, int $expires_in = null){
        $time = time();

        $payload = [
            'alg' => $this->config[$token_type]['encryption'],
            'typ' => 'JWT',
            'iat' => $time, 
            'exp' => $time + ($expires_in != null ? $expires_in : $this->config[$token_type]['expiration_time']),
            'ip'  => Request::ip(),
            'user_agent' => Request::user_agent()
        ];
        
        $payload = array_merge($payload, $props);

        return \Firebase\JWT\JWT::encode($payload, $this->config[$token_type]['secret_key'],  $this->config[$token_type]['encryption']);
    }

    protected function gen_jwt_rememberme($uid){
        $time = time();

        $payload = [
            'alg' => $this->config['email_token']['encryption'],
            'typ' => 'JWT',
            'iat' => $time, 
            'exp' => $time + $this->config['email_token']['expires_in'],
            'ip'  => Request::ip(),
            'user_agent' => Request::user_agent(),
            'uid' => $uid,
            'db_access' => $this->getDbAccess($uid)
         ];

        return \Firebase\JWT\JWT::encode($payload, $this->config['email_token']['secret_key'],  $this->config['email_token']['encryption']);
    }

    protected function setUID($uid){
        static::$current_user_uid = $uid;
    }

    function uid(){
        return static::$current_user_uid;
    }

    protected function setPermissions(Array $perms){
        static::$current_user_permissions = $perms;
    }

    function getPermissions(){
        return static::$current_user_permissions;
    }

    protected function setRoles(Array $roles){
        static::$current_user_roles = $roles;
    }

    public function getRoles(){
        return static::$current_user_roles;
    }

    public function isGuest() : bool {
        return auth()->getRoles() == [ acl()->getGuest() ];
    }

    public function isRegistered() : bool {
        return !$this->isGuest();
    }

    function login()
    {
        if (!in_array($_SERVER['REQUEST_METHOD'], ['POST','OPTIONS']))
            error('Incorrect verb ('.$_SERVER['REQUEST_METHOD'].'), expecting POST',405);

        $data  = request()->getBodyDecoded();

        if ($data == null)
            return;

        $email    = $data[$this->__email]    ?? null;
        $username = $data[$this->__username] ?? null;  
        $password = $data[$this->__password] ?? null;         

        
        if (empty($email) && empty($username) ){
            error("{$this->__username} or {$this->__email} are required",400);
        }else if (empty($password)){
            error($this->__password . ' is required',400);
        }

        $this->onLogin($data);

        try {              
            $u = DB::table($this->users_table);

            $row = $u->assoc()->unhide([$this->__password])
            ->where([$this->__email => $email, $this->__username => $username ], 'OR')
            ->setValidator((new Validator())->setRequired(false))  
            ->first();

            if (!$row)
                error('Incorrect username / email or password', 401);

            $hash = $row[$this->__password];

            if (!password_verify($password, $hash))
                error('Incorrect username / email or password', 401);

            $is_active = 1;    
            if ($u->inSchema([$this->__active])){
                $is_active = $row[$this->__active]; 

                if ($is_active == null) {

                    if ($row[$this->__confirmed_email] === "0") {
                        error('Non authorized', 403, 'Please confirm your e-mail');
                    } else {
                        error('Non authorized', 403, 'Account pending for activation');
                    }
                }

                if ($is_active == 0 || (string) $is_active === "0") {
                    error('Non authorized', 403, 'Deactivated account !');
                } 
            }                

            // Fetch roles && permissions

            $uid       = $row[$u->getIdName()];            
            $roles     = $u->inSchema([$this->role_field]) ? $row[$this->role_field] : $this->fetchRoles($uid); 
            $perms     = $this->fetchPermissions($uid);
            $db_access = $this->getDbAccess($uid);
       
            static::setRoles($roles); //

            $access  = $this->gen_jwt([ 
                'uid' => $uid, 
                'roles' => $roles, 
                'permissions' => $perms,
                'is_active' => $is_active, 
                'db_access' => $db_access
            ], 'access_token');

            // el refresh no debe llevar ni roles ni permisos por seguridad !
            $refresh = $this->gen_jwt([ 'uid' => $uid
            ], 'refresh_token');

            $this->onLogged($data, $uid, $is_active, $roles, $perms);

            response()->send([ 
                'access_token'=> $access,
                'token_type' => 'bearer', 
                'expires_in' => $this->config['access_token']['expiration_time'],
                'refresh_token' => $refresh,   
                'roles' => $roles,
                'uid' => $uid,
                'db_access' => $db_access
            ]);
          
        } catch (InvalidValidationException $e) { 
            error('Validation Error', 400, json_decode($e->getMessage()));
        } catch(\Exception $e){
            error($e->getMessage());
        }	
    }

    // Recibe un refresh_token y en el body un campo "impersonate" 
    function impersonate()
    {
        if (!in_array($_SERVER['REQUEST_METHOD'], ['POST','OPTIONS']))
            error('Incorrect verb ('.$_SERVER['REQUEST_METHOD'].'), expecting POST',405);

        $data  = request()->getBodyDecoded();

        if ($data === null)
            return;
            
        if (!isset($data['uid']) && !isset($data['role']))
            error('Bad request', 400, 'Nothing to impersonate');

        $request = request();

        $headers = $request->headers();
        $auth = $headers['Authorization'] ?? $headers['authorization'] ?? null;

        if (empty($auth)){
            error('Authorization not found',400);
        }

        try 
        {                                      
            list($refresh) = sscanf($auth, 'Bearer %s');

            $payload = \Firebase\JWT\JWT::decode($refresh, $this->config['refresh_token']['secret_key'], [ $this->config['refresh_token']['encryption'] ]);
            
            if (empty($payload))
                error('Unauthorized!',401);                     

            if (empty($payload->uid)){
                error('uid is needed',400);
            }

            $acl   = acl();
            $u     = DB::table($this->users_table);

            if ($u->inSchema([$this->role_field])){
                $roles = [ $acl->getRoleName($u->find($payload->uid)->value($this->role_field)) ];
            } else {
                $roles = $this->fetchRoles($payload->uid);
            }

            if (!$acl->hasSpecialPermission("impersonate", $roles) && !(isset($payload->impersonated_by) && !empty($payload->impersonated_by)) ){
                error('Unauthorized!',401, 'Impersonate requires elevated privileges');
            }    

            $guest_role = $acl->getGuest();

            $impersonate_user = $data['uid'] ?? null;
            $impersonate_role = $data['role'] ?? null;
            
            if (!empty($impersonate_role)){
                if ($impersonate_role == $guest_role){
                    $uid = -1;
                    $roles = [$guest_role];
                    $perms = [];
                    $is_active = null;
                } else {

                    if (!$acl->roleExists($impersonate_role)){
                        error("Bad request", 400, "Role $impersonate_role is not valid");
                    }

                    $uid = $payload->uid; // sigo siendo yo (el admin)
                    $roles = [$impersonate_role]; 
                    $perms = []; // permisos inalterados (rol puro)
                    $is_active = 1; // asumo está activo
                }    
            }


            if (!empty($impersonate_user)){ 
                $uid = $impersonate_user;

                $u = DB::table($this->users_table);

                $row = $u->assoc()
                ->find($uid) 
                ->first();

                if (!$row)
                    throw new \Exception("User to impersonate does not exist");

                $is_active = true;    
                if ($u->inSchema([$this->__active])){
                    $is_active = $row[$this->__active];

                    if ($is_active === null) {
                        error('Account to be impersonated is pending for activation', 500);
                    } elseif (((string) $is_active === "0")) {
                        error('User account to be impersonated is deactivated', 500);
                    }  
                }
                
                $roles = $u->inSchema([$this->role_field]) ? $row[$this->role_field] : $this->fetchRoles($uid);
                $perms = $this->fetchPermissions($uid);
            }    

            $impersonated_by = $payload->impersonated_by ?? $payload->uid;

            $db_access = $this->getDbAccess($uid);

            $access  = $this->gen_jwt([ 
                'uid' => $uid, 
                'roles' => $roles, 
                'permissions' => $perms,
                'impersonated_by' => $impersonated_by,
                'is_active' => $is_active,
                'db_access' => $db_access
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
                'db_access' => $db_access,
                'impersonated_by' => $impersonated_by
            ];

            $this->onImpersonated($data, $uid, $is_active, $roles, $perms, $impersonated_by);
    
            response()->send($res);      

        } catch (\Exception $e) {
            error($e->getMessage(), 400);
        }	
                                                    
    }

    // a diferencia de token() si bien renueva el access_token no lo hace a partir de ....
    function stop_impersonate() 
    {
        if (!in_array($_SERVER['REQUEST_METHOD'], ['POST','OPTIONS']))
            error('Incorrect verb ('.$_SERVER['REQUEST_METHOD'].'), expecting POST',405);

        $request = request();

        $headers = $request->headers();
        $auth = $headers['Authorization'] ?? $headers['authorization'] ?? null;

        if (empty($auth)){
            error('Authorization not found',400);
        }

        try {                                      
            list($refresh) = sscanf($auth, 'Bearer %s');

            $payload = \Firebase\JWT\JWT::decode($refresh, $this->config['refresh_token']['secret_key'], [ $this->config['refresh_token']['encryption'] ]);
            
            if (empty($payload))
                error('Unauthorized!',401);                     

            if (empty($payload->uid)){
                error('uid is needed',400);
            }

            if (empty($payload->impersonated_by)){
                error('Unauthorized!', 401, 'There is no admin behind this');
            }
            
        } catch (\Exception $e) {
            error($e->getMessage(), 400);
        }	

        $uid   = $payload->impersonated_by;        
        $roles = $this->fetchRoles($uid);
        $perms = $this->fetchPermissions($uid);

        //////
        
        try {              
            $db_access = $this->getDbAccess($uid);
            
            $access  = $this->gen_jwt([ 
                'uid'         => $uid, 
                'roles'       => $roles, 
                'permissions' => $perms,
                'is_active'   => 1,
                'db_access'   => $db_access
            ], 'access_token');

            $refresh = $this->gen_jwt([ 'uid' => $uid,
            ], 'refresh_token');

            response()->send([ 
                'uid' => $uid,           
                'access_token'  => $access,
                'token_type'    => 'bearer', 
                'expires_in'    => $this->config['access_token']['expiration_time'],
                'refresh_token' => $refresh,   
                'roles'         => $roles,
                'db_access'     => $db_access
            ]);
          
        } catch (InvalidValidationException $e) { 
            error('Validation Error', 400, json_decode($e->getMessage()));
        } catch(\Exception $e){
            error($e->getMessage());
        }	
    }

    /*
        Access Token renewal
    */	
    function token()
    {
        if (!in_array($_SERVER['REQUEST_METHOD'], ['POST','OPTIONS']))
            error('Incorrect verb ('.$_SERVER['REQUEST_METHOD'].'), expecting POST',405);

        $request = request();

        $headers = $request->headers();
        $auth = $headers['Authorization'] ?? $headers['authorization'] ?? null;

        if (empty($auth)){
            error('Authorization not found',400);
        }

        try {                                      
            // refresh token
            list($refresh) = sscanf($auth, 'Bearer %s');

            $payload = \Firebase\JWT\JWT::decode($refresh, $this->config['refresh_token']['secret_key'], [ $this->config['refresh_token']['encryption'] ]);

            if (empty($payload))
                error('Unauthorized!',401);                     

            if (!isset($payload->uid) || empty($payload->uid)){
                error('uid is needed',400);
            }

            if ($payload->exp < time())
                error('Token expired, please log in',401);


            $uid = $payload->uid;
            $impersonated_by = $payload->impersonated_by ?? null;
            $impersonated_by_role = null;

            if ($impersonated_by) {
                // guest
                if ($payload->uid == -1) 
                {
                    $acl   = Factory::acl();

                    $is_active = false;
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

                $is_active = 1;    
                if ($u->inSchema([$this->__active])){
                    $is_active = $row[$this->__active]; 

                    if ($is_active == 0 || (string) $is_active === "0") {
                        response()
                        ->error('Unauthorized', 403, 'Deactivated account !');
                    }
                }

                $acl   = $acl ?? Factory::acl();
                $roles = $u->inSchema([$this->role_field]) ? $row[$this->role_field] : $this->fetchRoles($uid); 
                $perms = $this->fetchPermissions($uid);
            }            

            $db_access = $this->getDbAccess($uid);
          
            $access  = $this->gen_jwt([ 
                'uid' => $payload->uid,
                'roles' => $roles, 
                'permissions' => $perms, 
                'impersonated_by' => $impersonated_by,
                'is_active' => $is_active,
                'db_access' => $db_access
            ], 
            'access_token');

            $res = [ 
                'uid' => $payload->uid,
                'access_token'=> $access,
                'token_type' => 'bearer', 
                'expires_in' => $this->config['access_token']['expiration_time'],
                'roles' => $roles,
                'db_access' => $db_access
            ];

            if (isset($payload->impersonated_by) && $payload->impersonated_by != null){
                $res['impersonated_by'] = $impersonated_by;
            }

            response()->send($res);
            
        } catch (\Exception $e) {
            error($e->getMessage(), 400);
        }	
    }

    /*
        Minimizar la cantidad de instancias de UsersModel !!!!!!!!!
    */
    function register()
    {
        if (!in_array($_SERVER['REQUEST_METHOD'], ['POST','OPTIONS']))
            error('Incorrect verb ('.$_SERVER['REQUEST_METHOD'].'), expecting POST',405);

        //DB::beginTransaction();

        try {
            $data  = request()->getBodyDecoded();

            if ($data == null)
                error('Bad request',400, 'Invalid JSON');
 
            // Hook
            $this->onRegister($data);        
            
            $u = DB::table($this->users_table);

            $many_to_many = false;

            // un campo 'rol' o similar
            if ($u->inSchema([$this->role_field])){
                if (!empty($data[$this->role_field])) {
                    if (isset($this->config['auto_approval_roles']) && !empty($this->config['auto_approval_roles'])) {
                    
                        $acl = acl();
                        if (!in_array($acl->getRoleName($data[$this->role_field]), $this->config['auto_approval_roles'])) {
                            throw new \Exception("Role {$data[$this->role_field]} is not auto-approved");
                        }

                    }    
                } else {
                    // chequear si es requerido antes
                    error("rol is required", 400);
                }  

                $roles = [ $data[$this->role_field] ];   
            } else {
                // una tabla 'roles' en relación muchos a muchos (debería asegurarme)
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
                error('Bad request', 400, 'There are missing attributes in your request: '.implode(',',$missing));

            $email_in_schema = $u->inSchema([$this->__email]);

            if ($email_in_schema)
            {
                // se hace en el modelo pero es más rápido hacer chequeos acá

                if (empty($data[$this->__email]))
                    throw new \Exception("Email is empty");
                    
                if (!filter_var($data[$this->__email], FILTER_VALIDATE_EMAIL))
                    throw new \Exception("Invalid email");  

                if (DB::table($this->users_table)->where([$this->__email, $data[$this->__email]])->exists())
                    error('Email already exists');    
            }            

            if (DB::table($this->users_table)->where([$this->__username , $data[$this->__username]])->exists())
                error('Username already exists');

            if ($u->inSchema([$this->__active])){  
                $u->fill([$this->__active]);      
                $data[$this->__active] = $this->config['pre_activated'] ?? false;
            }

            /*
                Creo "usuario"
            */

            $uid = $u
            ->setValidator(new Validator())
            ->create($data);

            if (empty($uid))
                throw new \Exception('Error on user creation');
            
            if (empty($roles)){
                $roles = [
                    config()['default_role'] ?? acl()->getRegistered()
                ];
            }

            if ($many_to_many && !empty($roles))
            {
                $this->addUserRoles($roles, $uid);
            }     
            
            $is_active = $this->config['pre_activated'] ? true : null;
            $db_access = $this->getDbAccess($uid);

            static::setRoles($roles); //

            // Hook
            $this->onRegistered($data, $uid, $is_active, $roles);
                
            $access  = $this->gen_jwt([
                'uid'           => $uid, 
                'roles'         => $roles,
                'permissions'   => [],
                'is_active'     => $is_active,
                'db_access'     => $db_access
            ], 'access_token');

            $refresh = $this->gen_jwt([
                'uid' => $uid
            ], 'refresh_token');

            $res = [ 
                'uid'           => $uid,
                'access_token'  => $access,
                'token_type'    => 'bearer', 
                'expires_in'    => $this->config['access_token']['expiration_time'],
                'refresh_token' => $refresh,
                'roles'         => $roles,
                'db_access'     => $db_access
            ];    

            //DB::commit();    
            response()->send($res);

        } catch (InvalidValidationException $e) { 
            //DB::rollback();           
            error('Validation Error', 400, json_decode($e->getMessage()));
        }catch(\Exception $e){
            //DB::rollback();
            error($e->getMessage());
        }	
            
    }

    private function jwtPayload(){
        $auth = request()->getAuth();

        if (empty($auth))
            return;
            
        list($jwt) = sscanf($auth, 'Bearer %s');

        if($jwt != null)
        {
            try{
                $payload = \Firebase\JWT\JWT::decode($jwt, $this->config['access_token']['secret_key'], [ $this->config['access_token']['encryption'] ]);
                
                $config = config();
                
                if (empty($payload))
                    error('Unauthorized!',401);             

                if (isset($config['restrict_by_ip']) && $config['restrict_by_ip']){
                    if (!isset($payload->ip) || empty($payload->ip))
                        error('Unauthorized',401,'Lacks IP in web token');

                    if ($payload->ip != Request::ip())
                        error('Unauthorized!',401, 'IP change');
                }        

                if (isset($config['restrict_by_user_agent']) && $config['restrict_by_user_agent']){
                    if (!isset($payload->user_agent) || empty($payload->ip))
                        error('Unauthorized',401,'Lacks user agent in web token');

                    if ($payload->user_agent != Request::user_agent())
                        error('Unauthorized!',401, 'You can only use one device at time'); 
                }    

                if (!isset($payload->uid) || empty($payload->uid))
                    error('Unauthorized',401,'Lacks id in web token');  

                // Lacks is_active status
                if (DB::table($this->users_table)->inSchema(['is_active']) && !isset($payload->is_active) && $payload->uid != -1){
                    error('Unauthorized', 401, 'Lacks is_active status. Please log in.');
                }    

                // temporal:  active => is_active
                $is_active = $payload->is_active ?? $payload->active;

                if ($is_active === false) {
                    error('Unauthorized', 403, 'Deactivated account');
                } 
                                                  
                if ($payload->exp < time())
                    error('Expired token',401);

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
                error($e->getMessage(),401);
            }	
        }else{
            error('Authorization jwt token not found',400);
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

        $is_active = null;
        $perms  = [];
        $roles  = [];

        $auth_method = request()->authMethod();    

        switch ($auth_method){
            case 'API_KEY': 
                $api_key = request()->getApiKey();

                $acl = Factory::acl();
                $uid = $this->getUserIdFromApiKey($api_key);

                if ($uid == null){
                    error('Invalid API Key', 401);
                }

                $u = DB::table($this->users_table);

                if ($u->inSchema([$this->role_field])){
                    $rid   = $u->where([$u->getIdName() => $uid])->value($this->role_field);
                    $roles = [ $acl->getRoleName($rid) ]; 
                } else {
                    $roles = $this->fetchRoles($uid);
                }

                $is_active = true;
                $perms  = $this->fetchPermissions($uid);
                
                static::setRoles($ret['roles']); //

                $ret = [
                    'uid'           => $uid,
                    'roles'         => $roles,
                    'permissions'   => $perms,
                    'is_active'     => $is_active 
                ];
            break;
            case 'JWT':
                $ret = $this->jwtPayload();

                if (DB::table($this->users_table)->inSchema([$this->role_field])){
                    $ret['roles'] = [ Factory::acl()->getRoleName($ret['roles']) ]; 
                } else {
                    if ($this->isRegistered()){
                        // sino preguntara sobre-escribiría roles
                        if (empty($ret['roles'])){
                            $ret['roles'] = [ Factory::acl()->getRegistered()];
                        }                        
                    }
                } 

                static::setRoles($ret['roles']); //

                $tenantid = request()->getTenantId();

                if ($tenantid !== null){
                    $db_access = $ret['db_access'] ?? [];   
                   
                    if (config()['restrict_by_tenant']){
                        if (!in_array($tenantid, $db_access)){
                            //dd($ret['roles']);
                            //dd(acl()->getRolePermissions());

                            // Si tiene el permiso especial "read_all" le doy acceso a cualquier DB !
                            if (!acl()->hasSpecialPermission('read_all', $ret['roles'])){
                                error("Forbidden", 403, "No db access");
                            }
                        }
                    }
                }

            break;
            default:
                $perms = []; 
                $roles = [Factory::acl()->getGuest()];

                static::setRoles($roles); //

                $ret = [
                    'uid' => null,
                    'roles' => $roles,
                    'permissions' => $perms,
                    'is_active' => $is_active
                ];
        }

        static::setUID($ret['uid']) ;

        // Hook
        $this->onChecked($ret['uid'], $is_active, $roles, $perms, $auth_method);

        return $ret;
    }

    
    /*
        Proviene de un link generado en register()

        Debería haber otro método que genere el mismo enlace
    */
	function confirm_email($jwt, $exp)
	{
		if (!in_array($_SERVER['REQUEST_METHOD'], ['GET','OPTIONS']))
            error('Incorrect verb ('.$_SERVER['REQUEST_METHOD'].'), expecting GET',405);

		// Es menos costoso verificar así en principio
		if ((int) $exp < time()) {
            error('Link is outdated', 400);
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

                if (!$u->inSchema([$this->__confirmed_email])){
                    error('Email confirmation is not implemented', 501);
                }    

                $rows = $u->assoc()
                ->select([$u->getIdName()])
                ->when($u->inSchema([$this->__active]), function($q){
                    $q->addSelect($this->__active);
                })
                ->where([$this->__email, $payload->email])
                ->get();

                if (count($rows) == 0){
                    error("Not found", 404, "Email not found");
                }

                if ($u->inSchema([$this->__active])){
                    if ((string) $rows[0][$this->__active] === "0") {
                        error('Non authorized', 403, 'Deactivated account !');
                    }
                }
                
                $uid  = $rows[0][$u->getIdName()];
                
                $ok = $u
                ->fill([$this->__confirmed_email])
                ->update([$this->__confirmed_email => 1]);

            } catch (\Exception $e) {
                /*
                * the token was not able to be decoded.
                * this is likely because the signature was not able to be verified (tampered token)
                *
                * reach this point if token is empty or invalid
                */
                error($e->getMessage(),401);
            }	
        }else{
            error('Authorization jwt token not found',400);
        }     

        $roles = $payload->roles ?? [];
        $perms = $payload->permissions ?? [];
        $db_access = $this->getDbAccess($uid);

        static::setRoles($roles); //

        $access  = $this->gen_jwt([ 
            'uid' => $uid,   
            'roles' => $roles, 
            'permissions' => $perms,
            'is_active' => 1,                     // *
            'db_access' => $db_access
        ], 'access_token');

        $refresh = $this->gen_jwt(['uid' => $uid, 
        ], 'refresh_token');

        // Hook
        $this->onConfirmedEmail($uid, $roles, $perms);
        
        response()->send([
            'uid' => $uid,  
            'access_token' => $access,
            'token_type' => 'bearer', 
            'expires_in' => $this->config['access_token']['expiration_time'],
            'refresh_token' => $refresh,
            'roles' => $roles,
            'db_access' => $db_access 
        ]);	

    }     
    
    /*
        Si el correo es válido debe generar y enviar por correo un enlance para cambiar el password
        sino no hacer nada.
    */
	function rememberme(){
		$data  = request()->getBodyDecoded();

		if ($data == null)
			error('Invalid JSON',400);

		$email = $data[$this->__email] ?? null;

		if ($email == null)
			error($this->__email . ' is required', 400);

		try {	
			$u    = DB::table($this->users_table)->assoc();
			$rows = $u->where([$this->__email, $email])->get([$this->__id, $this->__active]);

			if (count($rows) === 0){
                // Email not found
                error('Please very your e-mail is correct', 400); 
            }

            // Hook
            $this->onRemember($data);
		
            $uid = $rows[0][$this->__id];	//
            $exp = time() + $this->config['email_token']['expires_in'];	

            $is_active = $rows[0][$this->__active];

            if ((string) $is_active === "0") {
                error('Non authorized', 403, 'Deactivated account !');
            }

            $base_url = base_url();            

            $token    = $this->gen_jwt_rememberme($uid);

            $url      = $base_url . (!Strings::endsWith(DIRECTORY_SEPARATOR, $base_url) ? '/' : '') 
            .'login/change_pass_by_link/' . $token . '/' . $exp; 	

		} catch (\Exception $e){
			error($e->getMessage(), 500);
		}
    
        // Hook
        $this->onRemembered($data, $url);

        /*
            Si en el hook onRemembered() no hubo respuesta, no la dejo vacia
        */
        if (response()->isEmpty()){
            response([
                'message' => 'OK'
            ]);  
        }
    }
    
    /*
        Proviene de rememberme() y da la oportunidad de cambiar el pass otorgando tokens a tal fin
    */
    function change_pass_by_link($jwt = null, $exp = null){
        if (!in_array($_SERVER['REQUEST_METHOD'], ['GET','OPTIONS'])){
            error('Incorrect verb ('.$_SERVER['REQUEST_METHOD'].'), expecting GET',405);
        }    

        if ($jwt == null || $exp == null){
            error('Bad request', 400, 'Two paramters are expected');
        }

        // Es menos costoso verificar así en principio
        if ((int) $exp < time()) {
            error('Link is outdated', 401);
        } else {

            if($jwt != null)
            {
                try {
                    $payload = \Firebase\JWT\JWT::decode($jwt, $this->config['email_token']['secret_key'], [ $this->config['email_token']['encryption'] ]);
                    
                    if (empty($payload))
                        error('Unauthorized!',401);                     

                    if (empty($payload->uid)){
                        error('uid is needed',400);
                    }

                    $uid = $payload->uid; 

                    $acl   = Factory::acl();                    

                    $u = DB::table($this->users_table);

                    if ($u->inSchema([$this->role_field])){
                        $rid   = $u->find($uid)->value($this->role_field);
                        $roles = [ $acl->getRoleName($rid) ]; 
                    } else {
                        $roles = $this->fetchRoles($uid);
                    }
                    
                    $perms = $this->fetchPermissions($uid);

                    $row = $u->assoc()
                    ->where([$u->getIdName() => $uid]) 
                    ->first();

                    if (!$row)
                        throw new Exception("Uid not found");

                    $is_active = true;    
                    if ($u->inSchema([$this->__active])){    
                        $is_active = $row[$this->__active];                     

                        if ($is_active === false) {
                            error('Non authorized', 403, 'Deactivated account');
                        }
                    }    

                    if ($payload->exp < time())
                        error('Token expired, please log in',401);

                    $db_access = $this->getDbAccess($uid);

                    $access  = $this->gen_jwt([ 
                        'uid' => $uid,
                        'roles' => $roles, 
                        'permissions' => $perms, 
                        'is_active' => $is_active,
                        'db_access' => $db_access
                    ], 'access_token');
                    
                    $refresh  = $this->gen_jwt([ 
                                                'uid' => $uid
                    ], 'refresh_token');


                    static::setRoles($roles); //

                    ///////////
                    response()->send([ 
                        'uid' => $uid,
                        'access_token'=> $access,
                        'refresh_token'=> $refresh,
                        'token_type' => 'bearer', 
                        'expires_in' => $this->config['access_token']['expiration_time'],
                        'roles' => $roles,
                        'permissions' => $perms,
                        'db_access' => $db_access                                            
                    ]);
                    

                } catch (\Exception $e) {
                    /*
                    * the token was not able to be decoded.
                    * this is likely because the signature was not able to be verified (tampered token)
                    *
                    * reach this point if token is empty or invalid
                    */
                    error($e->getMessage(),401);
                }	
            }else{
                error('Authorization jwt token not found',400);
            }     
        }	

    }   
    
    function change_pass_process()
    {
        if (!in_array($_SERVER['REQUEST_METHOD'], ['POST','OPTIONS']))
            error('Incorrect verb ('.$_SERVER['REQUEST_METHOD'].'), expecting POST',405);

        $data  = request()->getBody();

        if ($data == null)
            return;

        if (!isset($data->password) || empty($data->password))
            error('Bad request', 400, 'Lacks password in request');

        $request = request();

        $headers = $request->headers();
        $auth    = $headers['Authorization'] ?? $headers['authorization'] ?? null;

        if (empty($auth)){
            error('Authorization not found',400);
        }

        try {                                             
            list($refresh) = sscanf($auth, 'Bearer %s');

            $payload = \Firebase\JWT\JWT::decode($refresh, $this->config['email_token']['secret_key'], [ $this->config['refresh_token']['encryption'] ]);
            
            if (empty($payload))
                error('Unauthorized!',401);                     

            if (empty($payload->uid)){
                error('uid is required',400);
            }

            $ok = DB::table($this->users_table)
            ->find($payload->uid)
            ->update([
                $this->__password => $data->password
            ]);

            if (!$ok){
                error("Unexpected error trying to update password", 500); 
            }

            $uid = $payload->uid;
            
            $u = DB::table($this->users_table);
            $row = $u->find($uid)->first();


            $is_active = 1;    
            if ($u->inSchema([$this->__active])){
                $is_active = $row[$this->__active]; 

                if ($is_active == null) {

                    if ($row[$this->__confirmed_email] === "0") {
                        error('Non authorized', 403, 'Please confirm your e-mail');
                    } else {
                        error('Non authorized', 403, 'Account pending for activation');
                    }
                }

                if ($is_active == 0 || (string) $is_active === "0") {
                    error('Non authorized', 403, 'Deactivated account !');
                } 
            }                

            // Fetch roles && permissions

            $uid       = $payload->uid;            
            $roles     = $this->fetchRoles($uid); 
            $perms     = $this->fetchPermissions($uid);
            $db_access = $this->getDbAccess($uid);

            static::setRoles($roles); //

            $access  = $this->gen_jwt([ 
                'uid' => $uid, 
                'roles' => $roles, 
                'permissions' => $perms,
                'is_active' => $is_active, 
                'db_access' => $db_access
            ], 'access_token');

            // el refresh no debe llevar ni roles ni permisos por seguridad !
            $refresh = $this->gen_jwt([ 'uid' => $uid
            ], 'refresh_token');

            // Hook
            $this->onChangedPassword($uid, $roles, $perms);

            response()->send([ 
                'access_token'=> $access,
                'token_type' => 'bearer', 
                'expires_in' => $this->config['access_token']['expiration_time'],
                'refresh_token' => $refresh,   
                'roles' => $roles,
                'uid' => $uid,
                'db_access' => $db_access
            ]);

        } catch (\Exception $e) {
            /*
            * the token was not able to be decoded.
            * this is likely because the signature was not able to be verified (tampered token)
            *
            * reach this point if token is empty or invalid
            */
            error($e->getMessage(),401);
        }	
    }


    /*
    
        Related with AuthController and ACL

    */

    protected function getUserIdFromApiKey($api_key){
        $uid = DB::table('api_keys')
        ->where(['value', $api_key])
        ->value('user_id');

        return $uid;
    }

    protected function fetchRoles($uid) : Array {
        $rows = DB::table('user_roles')
        ->assoc()
        ->where(['user_id', $uid])
        ->select(['role_id as role'])
        ->get();	

        $acl = Factory::acl();

        $roles = [];
        if (count($rows) != 0){
            foreach ($rows as $row){
                $roles[] = $acl->getRoleName($row['role']);
            }
        }

        return $roles;
    }

    protected function fetchTbPermissions($uid) : Array {
        $_permissions = DB::table('user_tb_permissions')
        ->assoc()
        ->select([  
            'tb', 
            'can_list_all as la',
            'can_show_all as ra', 
            'can_list as l',
            'can_show as r',
            'can_create as c',
            'can_update as u',
            'can_delete as d'])
        ->where(['user_id' => $uid])
        ->get();

        $perms = [];
        foreach ((array) $_permissions as $p){
            $tb = $p['tb'];
            $perms[$tb] =  $p['la'] * 64 + $p['ra'] * 32 +  $p['l'] * 16 + $p['r'] * 8 + $p['c'] * 4 + $p['u'] * 2 + $p['d'];
        }

        return $perms;
    }

    protected function fetchSpPermissions($uid) : Array {
        $perms = DB::table('user_sp_permissions')
        ->assoc()
        ->where(['user_id' => $uid])
        ->join('sp_permissions', 'user_sp_permissions.sp_permission_id', '=', 'sp_permissions.id')
        ->pluck('name');

        return $perms ?? [];
    }

    protected function fetchPermissions($uid) : Array { 
        return [
            'tb' => $this->fetchTbPermissions($uid), 
            'sp' => $this->fetchSpPermissions($uid) 
        ];
    }

    protected function addUserRoles(Array $roles, $uid) {
        foreach ($roles as $role) {
            $role_id = acl()->getRoleId($role);

            if ($role_id == null){
                throw new \Exception("Role $role is invalid");
            }

            try {
                // lo ideal es validar los roles y obtener los ids para luego hacer un "INSERT in bulk"
                $ur_id = DB::table('user_roles')
                ->where(['id' => $uid])
                ->create(['user_id' => $uid, 'role_id' => $role_id]);
            } catch (\Exception $e){
                Files::logger($e->getMessage());
            }            

            if (empty($ur_id))
                throw new \Exception("Error registrating user role $role");             
        }         
    }

    function hasDbAccess($user_id, string $db_connection){
        return in_array($db_connection, $this->getDbAccess($user_id));
    }
    
    /*
        Event Hooks
    */

    function onRegister(Array $data){ }
    function onRegistered(Array $data, $uid, $is_active, $roles){ }
    function onRemember(Array $data){}
    function onRemembered(Array $data, $link_url){}
    function onLogin(Array $data){}
    function onLogged(Array $data, $uid, $is_active, $roles, $perms){}
    function onImpersonated(Array $data, $uid, $is_active, $roles, $perms, $impersonated_by){}	
    function onChecked($uid, $is_active, $roles, $perms, $auth_method){}
    function onConfirmedEmail($uid, $roles, $perms){}
    function onChangedPassword($uid, $roles, $perms){}

    function getDbAccess($uid) : Array { return []; }
}