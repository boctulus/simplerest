<?php

namespace Boctulus\FriendlyposWeb\Models;

use Boctulus\Simplerest\Core\Model as MyModel;
use Boctulus\FriendlyposWeb\Schemas\UsuarioSchema;

class UsuarioModel extends MyModel
{
	protected $hidden       = [];
	protected $not_fillable = [];

	protected $field_names  = [];
	protected $formatters    = [];

    function __construct(bool $connect = false){
        parent::__construct($connect, UsuarioSchema::class);
	}	
}

