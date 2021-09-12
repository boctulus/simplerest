<?php

namespace simplerest\models;

use simplerest\core\Model;
use simplerest\libs\ValidationRules;
use simplerest\models\schemas\FilesSchema;
use simplerest\traits\Uuids;

class FilesModel extends Model
 { 
	use Uuids;

	protected $hidden   = [];
	protected $not_fillable = ['filename_as_stored']; 

    function __construct(bool $connect = false){
        parent::__construct($connect, new FilesSchema());
	}	
}

