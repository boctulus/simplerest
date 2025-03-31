<?php

namespace Boctulus\Simplerest\Models\legion;

use Boctulus\Simplerest\Models\MyModel;
use Boctulus\Simplerest\Core\Model;
use Boctulus\Simplerest\Core\Libs\ValidationRules;
use Boctulus\Simplerest\Schemas\legion\TblEstadoSchema;

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

