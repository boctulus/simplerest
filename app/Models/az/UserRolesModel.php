<?php

namespace Boctulus\Simplerest\Models\az;

use Boctulus\Simplerest\Models\MyModel;
use Boctulus\Simplerest\Core\Model;
use Boctulus\Simplerest\Core\Libs\ValidationRules;
use Boctulus\Simplerest\Schemas\main\UserRolesSchema;

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
        parent::__construct($connect, UserRolesSchema::class);
	}	
}

