<?php

namespace simplerest\models;

use simplerest\core\Model;
use simplerest\libs\ValidationRules;
use simplerest\models\schemas\TblUsuarioSchema;

class TblUsuarioModel extends Model
{ 
	protected $hidden    	= ['usu_varPassword'];
	protected $not_fillable = ['est_intIdEstado' /* ,'est_intIdConfirmEmail' */];

	protected $createdAt 	= 'usu_dtimFechaCreacion';
	protected $updatedAt 	= 'usu_dtimFechaActualizacion';
	//protected $deletedAt  = 'deleted_at'; 
	//protected $createdBy 	= 'usu_intIdCreador';
	//protected $updatedBy 	= 'usu_intIdActualizador';
	//protected $deletedBy  = 'deleted_by'; 
	//protected $locked     = 'locked';
	//protected $belongsTo  = 'belongs_to';

	public static $active	= 'est_intIdEstado';
	public static $username	= 'usu_varNroIdentificacion';  // es así?
	public static $email	= 'usu_varEmail';
	public static $password = 'usu_varPassword';
	//public static $confirmed_email = 'est_intIdConfirmEmail';

	
    function __construct(bool $connect = false){
		$this->registerInputMutator(self::$password, function($pass){ 
			return password_hash($pass, PASSWORD_DEFAULT); 
		}, function($op, $dato){
			return ($dato !== null);
		});

		//$this->registerOutputMutator('usu_varPassword', function($pass){ return '******'; } );
        parent::__construct($connect, new TblUsuarioSchema());
	}	
}

