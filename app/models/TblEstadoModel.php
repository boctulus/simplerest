<?php

namespace simplerest\models;

use simplerest\core\Model;
use simplerest\libs\ValidationRules;
use simplerest\models\schemas\TblEstadoSchema;

class TblEstadoModel extends MyModel
{ 
	protected $hidden   = [];
	protected $not_fillable = [];

	protected $createdAt = "est_dtimFechaCreacion";
	protected $updatedAt = "est_dtimFechaActualizacion";

    function __construct(bool $connect = false){
        parent::__construct($connect, new TblEstadoSchema());
	}	
}
