<?php

namespace simplerest\models\legion;

use simplerest\models\MyModel;
use simplerest\core\Model;
use simplerest\core\libs\ValidationRules;
use simplerest\schemas\legion\TblFacturaSchema;

class TblFacturaModel extends MyModel
{ 
	protected $hidden   = [];
	protected $not_fillable = [];

	protected $createdBy = 'usu_intIdCreador';

    function __construct(bool $connect = false){
        parent::__construct($connect, TblFacturaSchema::class);
	}	
}

