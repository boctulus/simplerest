<?php

namespace Boctulus\Simplerest\Models\Mpo;

use Boctulus\Simplerest\Models\MyModel;
use Boctulus\Simplerest\Schemas\mpp\TBL_BARRIOSSchema; //

class BarriosModel extends MyModel
{

	protected $hidden   = [];
	protected $not_fillable = [];

    protected $table = 'TBL_BARRIOS';

    function __construct(bool $connect = false){
        parent::__construct($connect, TBL_BARRIOSSchema::class);
	}	
}

