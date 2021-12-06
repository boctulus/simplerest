<?php

namespace simplerest\models;

use simplerest\core\Model;
use simplerest\libs\ValidationRules;
use simplerest\models\MyModel;	
use simplerest\models\schemas\legion\TblEmpresaSchema;

class TblEmpresaModel extends MyModel
{ 
	protected $hidden   = [];
	protected $not_fillable = [];

    function __construct(bool $connect = false){
        parent::__construct($connect, new TblEmpresaSchema());
	}	
}

