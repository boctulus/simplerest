<?php

namespace Boctulus\Simplerest\Models\main;

use Boctulus\Simplerest\Models\MyModel;
use Boctulus\Simplerest\Core\Libs\ValidationRules;
use Boctulus\Simplerest\Schemas\main\UserTbPermissionsSchema;

class UserTbPermissionsModel extends MyModel
{ 
	protected $hidden   = [];
	protected $not_fillable = [];

	protected $createdAt = 'created_at';
	protected $createdBy = 'created_by';
	protected $updatedAt = 'updated_at';
	protected $updatedBy = 'updated_by';

    function __construct(bool $connect = false){
        parent::__construct($connect, UserTbPermissionsSchema::class);
	}	
}

