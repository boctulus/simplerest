<?php

namespace Boctulus\Simplerest\Models\legion;

use Boctulus\Simplerest\Models\MyModel;
use Boctulus\Simplerest\Core\Model;
use Boctulus\Simplerest\Core\Libs\ValidationRules;
use Boctulus\Simplerest\Schemas\legion\TblCategoriaPersonaPersonaSchema;

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

