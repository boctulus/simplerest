<?php

namespace simplerest\models;

use simplerest\core\Model;
use simplerest\libs\ValidationRules;
use simplerest\models\schemas\legion\TblConsecutivoSchema;

class TblConsecutivoModel extends MyModel
{	
	protected $hidden   = [];
	protected $not_fillable = [];

	protected $createdBy = 'usu_intIdCreador';

    function __construct(bool $connect = false){
        parent::__construct($connect, new TblConsecutivoSchema());
	}	
}

