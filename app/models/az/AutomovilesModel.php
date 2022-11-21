<?php

namespace simplerest\models\az;

use simplerest\models\MyModel;
use simplerest\schemas\az\AutomovilesSchema;

class AutomovilesModel extends MyModel
{
	protected $hidden   = [];
	protected $not_fillable = [];

	protected $field_names = [
		"is_locked"   => "Locked?",
        "created_at"  => "Creation Date",
        "updated_at"  => "Update Date", 
		"how_popular" => "Popularity"
    ];

	protected $formatters = [
		"is_locked"     => "checkbox",
		"active"        => "radio",
		"rating"        => "starts",
		"how_popular"   => "progress"
	];

    function __construct(bool $connect = false){
        parent::__construct($connect, AutomovilesSchema::class);
	}	
}

