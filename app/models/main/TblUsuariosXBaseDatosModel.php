<?php

namespace simplerest\models\main;

use simplerest\models\MyModel;
use simplerest\core\Model;
use simplerest\core\libs\ValidationRules;
use simplerest\schemas\main\TblUsuariosXBaseDatosSchema;

class TblUsuariosXBaseDatosModel extends MyModel
{ 
	protected $hidden   = [];
	protected $not_fillable = [];

    function __construct(bool $connect = false){
        parent::__construct($connect, TblUsuariosXBaseDatosSchema::class);
	}	
}

