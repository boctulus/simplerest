<?php

namespace Boctulus\Simplerest\Models\eb;


use Boctulus\Simplerest\Models\MyModel;
use Boctulus\Simplerest\Schemas\eb\AsignadoSchema;

class AsignadoModel extends MyModel
{
	
	

	protected $hidden   = [];
	protected $not_fillable = [];

    function __construct(bool $connect = false){
        parent::__construct($connect, AsignadoSchema::class);
	}	
}

