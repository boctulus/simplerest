<?php

namespace Boctulus\Simplerest\Models\az;

use Boctulus\Simplerest\Models\MyModel;


class MediosTransporteModel extends MyModel
{
	protected $hidden   = [];
	protected $not_fillable = [];
	protected $table_name = 'medios_transporte';

    function __construct(bool $connect = false){
        parent::__construct($connect);
	}	
}

