<?php

namespace simplerest\models;

use simplerest\core\Model;
use simplerest\libs\ValidationRules;
use simplerest\models\schemas\main\UserRolesSchema;

class UserRolesModel extends MyModel
{ 
	protected $hidden   = [];
	protected $not_fillable = [];

	protected $createdAt = 'created_at';
	protected $createdBy = 'created_by';
	protected $updatedAt = 'updated_at';
	protected $updatedBy = 'updated_by';

	protected $connect_to= [
		//'roles',
		// 'tbl_usuario_empresa'
	];

    function __construct(bool $connect = false){
        parent::__construct($connect, new UserRolesSchema());
	}	
}

