<?php

namespace simplerest\models\schemas;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class Schema implements ISchema
{ 
	### TRAITS
	
	function get(){
		return [
			'table_name'	=> '',

			'id_name'		=> NULL,

			'attr_types'	=> [

			],

			'nullable'		=> [],

			'rules' 		=> [

			],

			'relationships' => [
				'users' => [
					['books.author_id','authors.id'],
					['books.editor_id','editors.id']
],
				'book_reviews' => [
					['book_reviews.book_id','books.id']
]
			]
		];
	}	
}

