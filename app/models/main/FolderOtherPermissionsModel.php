<?php

namespace simplerest\models\main;

use simplerest\models\MyModel;
use simplerest\core\libs\ValidationRules;
use simplerest\schemas\main\FolderOtherPermissionsSchema;

class FolderOtherPermissionsModel extends MyModel
{ 
	protected $hidden   = [];
	protected $not_fillable = [];

    function __construct(bool $connect = false){
        parent::__construct($connect, FolderOtherPermissionsSchema::class);
	}	
}

