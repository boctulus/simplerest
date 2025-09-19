<?php

namespace Boctulus\Simplerest\Models\az;


use Boctulus\Simplerest\Models\MyModel;
use Boctulus\Simplerest\Schemas\az\FilesSchema;

class FilesModel extends MyModel
{
	protected $hidden   = [];
	protected $not_fillable = [];

    function __construct(bool $connect = false){
        parent::__construct($connect, FilesSchema::class);
	}	
}

