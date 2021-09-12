<?php

namespace simplerest\models;

use simplerest\core\Model;
use simplerest\libs\DB;
use simplerest\models\schemas\WebhooksSchema;
use simplerest\libs\Factory;
use simplerest\libs\Validator;

class WebhooksModel extends Model
{ 
	protected $hidden   = [];
	protected $not_fillable = [];

    function __construct(bool $connect = false){
        parent::__construct($connect, new WebhooksSchema());
	}	

	function onCreating(array &$data)
	{
		parse_str($data['conditions'], $conditions);

		$entity = $data['entity'];
		$entity_instance = DB::table($data['entity']);
		
		// podría directamente preguntar al schema
		$fields = $entity_instance->getAttr();

		$allops = ['eq', 'gt', 'gteq', 'lteq', 'lt', 'neq', 'in', 'notIn', 'contains', 'notContains', 'startsWith', 'notStartsWith', 'endsWith', 'notEndsWith', 'containsWord', 'notContainsWord', 'between', 'notBetween'];

		$rules  = $entity_instance->getRules();

		foreach($conditions as $field => $v){
			// valido que los campos existan
			if (!in_array($field, $fields)){
				Factory::response()->sendError('Data validation error', 400, "Some condition refers to '$field' but it's unknown in $entity");
			}

			$type = $rules[$field]['type'];
			
			if (is_array($v)){
				foreach ($v as $op => $val){		
					// valido tipos		
					if (!Validator::isType($val, $type)){
						Factory::response()->sendError('Data validation error', 400, "Dato '$val' is not a valid $type");
					}
					
					// valido operadores
					if (!in_array($op, $allops)){
						Factory::response()->sendError('Data validation error', 400, "Unknown '$op' operator");
					}
				}
			} else {
				// el operador implicito es 'eq'

				// valido tipos
				if (!Validator::isType($v, $type)){
					Factory::response()->sendError('Data validation error', 400, "Dato '$v' is not a valid $type");
				}
			}

		}
	}

}

