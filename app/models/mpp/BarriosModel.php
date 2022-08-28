<?php

namespace simplerest\models\mpp;

use simplerest\models\MyModel;
use simplerest\schemas\mpp\TBL_BARRIOSSchema; //

class BarriosModel extends MyModel
{

	protected $hidden   = [];
	protected $not_fillable = [];

    protected $table = 'TBL_BARRIOS';

    function __construct(bool $connect = false){
        parent::__construct($connect, TBL_BARRIOSSchema::class);
	}	
}

