<?php

namespace simplerest\models\mpo;


use simplerest\models\MyModel;
use simplerest\schemas\mpo\TiposOrganismosOrgComunalesSchema;

class TiposOrganismosOrgComunalesModel extends MyModel
{
	
	

	protected $hidden   = [];
	protected $not_fillable = [];

    function __construct(bool $connect = false){
        parent::__construct($connect, TiposOrganismosOrgComunalesSchema::class);
	}	
}

