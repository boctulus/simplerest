<?php

namespace simplerest\models;

use simplerest\core\Model;

class MyModel extends Model 
{
    protected $createdBy = 'usu_intIdCreador';
	protected $updatedBy = 'usu_intIdActualizador';
	protected $createdAt = 'gen_dtimFechaActualizacion';
    
    function __construct(bool $connect = false, $schema = null, bool $load_config = true){
        parent::__construct($connect, $schema, $load_config);
    }
}