<?php

namespace simplerest\models;

use simplerest\core\Model;
use simplerest\libs\ValidationRules;
use simplerest\schemas\main\EmailNotificationsSchema;

class EmailNotificationsModel extends MyModel
{ 
	protected $createdAt = 'created_at';	// 

	protected $hidden   = [];
	protected $not_fillable = [];

    function __construct(bool $connect = false){
        parent::__construct($connect, EmailNotificationsSchema::class);
	}	
}

