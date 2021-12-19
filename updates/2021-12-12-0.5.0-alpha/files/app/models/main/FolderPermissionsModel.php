<?php

namespace simplerest\models\main;

use simplerest\models\MyModel;
use simplerest\libs\ValidationRules;
use simplerest\schemas\main\FolderPermissionsSchema;

class FolderPermissionsModel extends MyModel
{ 
	protected $hidden   = [];
	protected $not_fillable = [];

    function __construct(bool $connect = false){
        parent::__construct($connect, FolderPermissionsSchema::class);
	}	
}

