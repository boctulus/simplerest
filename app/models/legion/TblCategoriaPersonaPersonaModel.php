<?php

namespace simplerest\models\legion;

use simplerest\models\MyModel;
use simplerest\core\Model;
use simplerest\core\libs\ValidationRules;
use simplerest\schemas\legion\TblCategoriaPersonaPersonaSchema;

class TblCategoriaPersonaPersonaModel extends MyModel
{ 
	protected $hidden   = [];
	protected $not_fillable = [];

	protected $createdAt = 'cat_dtimFechaCreacion';
	protected $createdBy = null;
	protected $updatedAt = null;
	protected $updatedBy = null;

    function __construct(bool $connect = false){
        parent::__construct($connect, TblCategoriaPersonaPersonaSchema::class);
	}	
}

