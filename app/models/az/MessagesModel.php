<?php

namespace simplerest\models\az;


use simplerest\models\MyModel;
use simplerest\schemas\az\MessagesSchema;

class MessagesModel extends MyModel
{
	
	

	protected $hidden   = [];
	protected $not_fillable = [];

    function __construct(bool $connect = false){
        parent::__construct($connect, MessagesSchema::class);
	}	
}

