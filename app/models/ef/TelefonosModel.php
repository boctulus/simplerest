<?php

namespace simplerest\models\ef;


use simplerest\models\MyModel;
use simplerest\schemas\ef\TelefonosSchema;

class TelefonosModel extends MyModel
{
	
	

	protected $hidden   = [];
	protected $not_fillable = [];

    function __construct(bool $connect = false){
        parent::__construct($connect, TelefonosSchema::class);
	}	
}

