<?php

namespace Boctulus\Zippy\Models;

use Boctulus\Simplerest\Core\Libs\DB;
use Boctulus\Zippy\Schemas\BrandSchema;
use Boctulus\Simplerest\Core\Model as MyModel;

class Brand extends MyModel
{
	protected static $table = 'brands';

	protected $hidden       = [];
	protected $not_fillable = [];

	protected $field_names  = [];
	protected $formatters    = [];

    function __construct(bool $connect = false){
        parent::__construct($connect);

		// Set table name explicitly
		$this->table_name = 'brands';

		// Set connection to zippy database
		$this->setConn(DB::getConnection('zippy'));
	}
}

