<?php

namespace simplerest\models\mpo;


use simplerest\models\MyModel;
use simplerest\schemas\mpo\PersonalAccessTokensSchema;

class PersonalAccessTokensModel extends MyModel
{
	
	

	protected $hidden   = [];
	protected $not_fillable = [];

    function __construct(bool $connect = false){
        parent::__construct($connect, PersonalAccessTokensSchema::class);
	}	
}

