<?php

namespace Boctulus\Simplerest\Models\az;

use Boctulus\Simplerest\Models\MyModel;
use Boctulus\Simplerest\Core\Libs\ValidationRules;
use Boctulus\Simplerest\Schemas\main\SpPermissionsSchema;

class SpPermissionsModel extends MyModel
{ 
	protected $hidden   = [];
	protected $not_fillable = [];

    function __construct(bool $connect = false){
        parent::__construct($connect, SpPermissionsSchema::class);
	}	
}

