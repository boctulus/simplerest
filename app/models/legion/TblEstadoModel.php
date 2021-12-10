<?php

namespace simplerest\models\legion;

use simplerest\models\MyModel;
use simplerest\core\Model;
use simplerest\libs\ValidationRules;
use simplerest\schemas\legion\TblEstadoSchema;

class TblEstadoModel extends MyModel
{ 
	protected $hidden   = [];
	protected $not_fillable = [];

	protected $createdAt = "est_dtimFechaCreacion";
	protected $updatedAt = "est_dtimFechaActualizacion";

    function __construct(bool $connect = false){
        parent::__construct($connect, TblEstadoSchema::class);
	}	
}

