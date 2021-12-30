<?php

namespace simplerest\models\az;


use simplerest\models\MyModel;
use simplerest\schemas\az\Tbc2Schema;

class Tbc2Model extends MyModel
{
	
	

	protected $hidden   = [];
	protected $not_fillable = [];

    function __construct(bool $connect = false){
        parent::__construct($connect, Tbc2Schema::class);
	}	
}

