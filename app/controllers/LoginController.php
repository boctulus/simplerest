<?php

namespace simplerest\controllers;

use simplerest\core\controllers\Controller;
use simplerest\core\libs\DB;

class LoginController extends Controller
{
	/*
		Nombres de los campos en la tabla "users"
	*/
	
    protected $__email;
    protected $__username;
    protected $__password;
    protected $__confirmed_email;
    protected $__active;

	function __construct()
    { 
        parent::__construct();

		/*
			Recibo los nombres de las keys con que se deben armar los JSON
			para pegarle a los endpoints. Posteriormente los pasaré a cada vista
		*/

        $model = get_user_model_name();  
           
        $this->__email           = $model::$email;
        $this->__username        = $model::$username;
        $this->__password        = $model::$password;
		$this->__id 			 = get_id_name($this->config['users_table']);
    }

	function index(){
		$this->login();
	}
	
	function login(){	
		/*
			Cargo vista y paso variables
		*/
		
		$this->__view('userlogin/login.php', [ 
			'title'      =>'Ingreso', 
			'hidenav'    => true,
			'__email'    => $this->__email,
			'__username' => $this->__username,
			'__password' => $this->__password
		]);
	}
	
	function register(){
		$this->__view('userlogin/register.php', [
			'title'      =>'Registro', 
			'hidenav'    => true,
			'__email'    => $this->__email,
			'__username' => $this->__username,
			'__password' => $this->__password
		]);
	}

	/*
		callback
	*/
	function google_login()
	{
		$google_ctrl = new GoogleController();
		$res = $google_ctrl->login_or_register();

		if (isset($res['data'])){
			$this->__view('userlogin/blank.php', [
				'title'=>'Google login', 
				'hidenav'=> true,
				'access_token' => $res['data']['access_token'],
				'expires_in' => $res['data']['expires_in'],
				'refresh_token' => $res['data']['refresh_token']
			]);
		}else {
			$this->__view('userlogin/blank.php', [
				'title'=>'Google login', 
				'hidenav'=> false,
				'error' => $res['error']
			]);
		}		
	}
	
	/*
		callback
	*/
	function fb_login(){

		$fb_ctrl = new FacebookController();
		$res = $fb_ctrl->login_or_register();

		session_destroy();

		if (isset($res['data'])){
			$this->__view('userlogin/blank.php', [
				'title'=>'Facebook login', 
				'hidenav'=> true,
				'access_token' => $res['data']['access_token'],
				'expires_in' => $res['data']['expires_in'],
				'refresh_token' => $res['data']['refresh_token']
			]);
		}else {
			$this->__view('userlogin/blank.php', [
				'title'=>'Facebook login', 
				'hidenav'=> false,
				'error' => $res['error']
			]);
		}		
		
	}

	function rememberme(){
		$this->__view('userlogin/rememberme.php', [
			'title'=>'Recuérdame', 
			'hidenav'=> true,
			'__email'    => $this->__email,
			'__username' => $this->__username,
			'__password' => $this->__password
		]);
	}

	
	function rememberme_mail_sent(){
		$this->__view('userlogin/rememberme_mail_sent.php', [
			'title'=>'Recuérdame', 
			'hidenav'=> true,
			'__email'    => $this->__email,
			'__username' => $this->__username,
			'__password' => $this->__password
			]
		);
	}

	function confirm_email($jwt, $exp)
	{
		/// ...

		//$access  = 
		//$refresh = 

		/*
		if ($cond)			
			$this->__view('blank.php', [
				'title'=>'Confirmación de correo', 
				'hidenav'=> true,
				'access_token' => $access,
				'expires_in' => $this->config['email_token']['expires_in'],
				'refresh_token' => $refresh
			]);
	
		} else {
			$this->__view('blank.php', [
				'title'=>'Confirmación de correo fallida', 
				'hidenav'=> false,
				'error' => $error
			]);
		}	
		*/

	}

	function change_pass_by_link($jwt, $exp)
	{
		// Es menos costoso veririficar así en principio
		if ((int) $exp < time()){
			$error = 'Link is outdated';
		}else{
			if($jwt != null)
			{
				try {
					// Checking for token invalidation or outdated token
					
					$payload = \Firebase\JWT\JWT::decode($jwt, $this->config['email_token']['secret_key'], [ $this->config['email_token']['encryption'] ]);
					
					if (empty($payload))
						$error = 'Unauthorized!';                     

					if (empty($payload->uid)){
						$error = 'uid is needed';
					}

					if ($payload->exp < time())
						$error = 'Token expired';

				} catch (\Exception $e) {
					/*
					* the token was not able to be decoded.
					* this is likely because the signature was not able to be verified (tampered token)
					*
					* reach this point if token is empty or invalid
					*/
					$error = $e->getMessage();
				}	
			}else{
				$error = 'Authorization jwt token not found';
			}     
		}	

		
		if (!isset($error)){						
			//
			// Cargar vista 
			// donde poder setear una nueva contraseña
			//

			$email = DB::table($this->users_table)->where([$this->__id => $payload->uid])->value($this->__email);

			$this->__view('userlogin/update_pass.php', [
				'title'		 =>'Recuperación de contraseña', 
				'hidenav'	 => true,
				'__email'    => $this->__email,
				'__username' => $this->__username,
				'__password' => $this->__password
			]);
	
		}else {
			$this->__view('userlogin/blank.php', [
				'title'=>'Recuperación de contraseña', 
				'hidenav'=> false,
				'error' => $error,
				'__email'    => $this->__email,
				'__username' => $this->__username,
				'__password' => $this->__password
			]);
		}
	}


}


