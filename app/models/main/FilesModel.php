<?php

namespace simplerest\models;

use simplerest\core\Model;
use simplerest\libs\ValidationRules;
use simplerest\models\schemas\main\FilesSchema;
use simplerest\traits\Uuids; // falta al generar

class FilesModel extends MyModel
{ 
	use Uuids; // falta al generar

	protected $hidden   = [];
	protected $not_fillable = [];

	protected $createdAt = 'created_at';

    function __construct(bool $connect = false){
        parent::__construct($connect, new FilesSchema());
	}	
}

