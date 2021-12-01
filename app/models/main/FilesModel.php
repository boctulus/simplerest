<?php

namespace simplerest\models\main;

use simplerest\models\MyModel;
use simplerest\core\Model;
use simplerest\libs\ValidationRules;
use simplerest\schemas\main\FilesSchema;
use simplerest\traits\Uuids; // falta al generar

class FilesModel extends MyModel
{ 
	use Uuids; // falta al generar

	protected $hidden   = [];
	protected $not_fillable = [];

	protected $createdAt = 'created_at';

    function __construct(bool $connect = false){
        parent::__construct($connect, FilesSchema::class);
	}	
}

