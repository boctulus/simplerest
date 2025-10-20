<?php

namespace Boctulus\Simplerest\Models\pos_laravel;

use Boctulus\Simplerest\Models\MyModel;
use Boctulus\Simplerest\Schemas\pos_laravel\VentaPackSchema;

class VentaPackModel extends MyModel
{
	protected $hidden       = [];
	protected $not_fillable = [];

	protected $field_names  = [];
	protected $formatters    = [];

    function __construct(bool $connect = false){
        parent::__construct($connect, VentaPackSchema::class);
	}	
}

