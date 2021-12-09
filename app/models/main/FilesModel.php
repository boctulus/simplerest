<?php

namespace simplerest\models\main;

use simplerest\models\MyModel;
use simplerest\libs\ValidationRules;
use simplerest\schemas\main\FilesSchema;
use simplerest\traits\Uuids; 

class FilesModel extends MyModel
{ 
	use Uuids; 

	protected $hidden   = [];
	protected $not_fillable = [];

	protected $createdAt = 'created_at';

    function __construct(bool $connect = false){
        parent::__construct($connect, FilesSchema::class);
	}	
}

