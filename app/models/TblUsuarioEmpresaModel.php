<?php

namespace simplerest\models;

use simplerest\core\Model;
use simplerest\libs\ValidationRules;
use simplerest\models\schemas\main\TblUsuarioEmpresaSchema;

class TblUsuarioEmpresaModel extends Model
{ 
	protected $hidden    	= ['use_decPassword'];
	protected $not_fillable = ['est_intIdEstado'];

	protected $createdAt 	= 'use_dtimFechaCreacion';
	protected $updatedAt 	= 'use_dtimFechaActualizacion';
	//protected $deletedAt  = 'deleted_at'; 
	protected $createdBy 	= 'usu_intIdCreador';
	protected $updatedBy 	= 'usu_intIdActualizador';
	//protected $deletedBy  = 'deleted_by'; 
	//protected $locked     = 'locked';
	//protected $belongsTo  = 'belongs_to';

	public static $active	= 'est_intIdEstado';
	public static $username	= 'use_varUsuario';
	public static $email	= 'use_varEmail';
	public static $password = 'use_decPassword';
	public static $confirmed_email;


    function __construct(bool $connect = false){
		$this->registerInputMutator(self::$password, function($pass){ 
			return password_hash($pass, PASSWORD_DEFAULT); 
		}, function($op, $dato){
			return ($dato !== null);
		});

		//$this->registerOutputMutator('password', function($pass){ return '******'; } );
        parent::__construct($connect, new TblUsuarioEmpresaSchema());
	}
	
	/*
	function onUpdating(&$data) {
		if ($this->isDirty('email')) {
			$this->fill(['confirmed_email'])->update(['confirmed_email' => 0]);
		}
	}
	*/
}

