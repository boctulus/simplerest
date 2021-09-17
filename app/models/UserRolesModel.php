<?php

namespace simplerest\models;

use simplerest\core\Model;
use simplerest\libs\ValidationRules;
use simplerest\models\schemas\main\UserRolesSchema;

class UserRolesModel extends Model
{
	protected $hidden   = [];
	protected $not_fillable = [];

    function __construct(bool $connect = false){
        parent::__construct($connect, new UserRolesSchema());
	}	

	/*
	function onReading()
	{
		$this->join('roles')
		->select(['roles.id', 'role_id as role', 'name', 'user_id', 'created_by', 'created_at', 'updated_by', 'updated_at']);
	}
	*/
}

