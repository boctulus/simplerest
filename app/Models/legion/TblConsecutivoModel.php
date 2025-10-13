<?php

namespace Boctulus\Simplerest\Models\legion;

use Boctulus\Simplerest\Models\MyModel;
use Boctulus\Simplerest\Core\Model;
use Boctulus\Simplerest\Core\Libs\ValidationRules;
use Boctulus\Simplerest\Schemas\legion\TblConsecutivoSchema;

class TblConsecutivoModel extends MyModel
{	
	protected $hidden   = [];
	protected $not_fillable = [];

	protected $createdBy = 'usu_intIdCreador';

    function __construct(bool $connect = false){
        parent::__construct($connect, TblConsecutivoSchema::class);
	}	
}

