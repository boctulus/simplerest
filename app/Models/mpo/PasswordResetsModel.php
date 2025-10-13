<?php

namespace Boctulus\Simplerest\Models\mpo;


use Boctulus\Simplerest\Models\MyModel;
use Boctulus\Simplerest\Schemas\mpo\PasswordResetsSchema;

class PasswordResetsModel extends MyModel
{
	
	

	protected $hidden   = [];
	protected $not_fillable = [];

    function __construct(bool $connect = false){
        parent::__construct($connect, PasswordResetsSchema::class);
	}	
}

