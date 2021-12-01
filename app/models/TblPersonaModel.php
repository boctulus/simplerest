<?php

namespace simplerest\models;

use simplerest\core\Model;
use simplerest\libs\ValidationRules;
use simplerest\models\schemas\legion\TblPersonaSchema;

class TblPersonaModel extends MyModel
{ 
	protected $hidden   = [];
	protected $not_fillable = [];

    function __construct(bool $connect = false){
        parent::__construct($connect, new TblPersonaSchema());
		// $this->fillable[] = 'tbl_categoria_persona_persona';
	}	

	// function onCreating(array &$data)
	// {
	// 	global $tbl_categoria_persona_persona;
	// 	$tbl_categoria_persona_persona = $data['tbl_categoria_persona_persona'];
			
	// 	unset($data['tbl_categoria_persona_persona']);
	// }

	// function onCreated(array &$data, $last_inserted_id)
	// {	
	// 	global $tbl_categoria_persona_persona;

	// 	dd($tbl_categoria_persona_persona);	
	// }
}

