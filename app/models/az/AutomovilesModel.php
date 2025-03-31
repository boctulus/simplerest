<?php

namespace Boctulus\Simplerest\Models\az;

use Boctulus\Simplerest\Models\MyModel;
use Boctulus\Simplerest\Schemas\az\AutomovilesSchema;

class AutomovilesModel extends MyModel
{
	protected $hidden   = [
		// "created_at"
	];

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
		"quality"       => "star",
		"how_popular"   => "progress",
	];

    function __construct(bool $connect = false){
        parent::__construct($connect, AutomovilesSchema::class);
	}	
}

