<?php

namespace simplerest\models\az;

use simplerest\models\MyModel;


class MediosTransporteModel extends MyModel
{
	protected $hidden   = [];
	protected $not_fillable = [];
	protected $table_name = 'medios_transporte';

    function __construct(bool $connect = false){
        parent::__construct($connect);
	}	
}

