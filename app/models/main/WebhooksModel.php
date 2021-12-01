<?php

namespace simplerest\models\main;

use simplerest\models\MyModel;
use simplerest\core\Model;
use simplerest\libs\ValidationRules;
use simplerest\schemas\main\WebhooksSchema;

class WebhooksModel extends MyModel
{ 
	protected $hidden   = [];
	protected $not_fillable = [];

    function __construct(bool $connect = false){
        parent::__construct($connect, WebhooksSchema::class);
	}	
}

