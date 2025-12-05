<?php

namespace Boctulus\Zippy\Models;

use Boctulus\Simplerest\Core\Model as MyModel;
use Boctulus\Zippy\Schemas\BrandCategorySchema;

class BrandCategory extends MyModel
{
	protected static $table = 'brand_categories';

	protected $hidden       = [];
	protected $not_fillable = [];

	protected $field_names  = [];
	protected $formatters    = [];

    function __construct(bool $connect = false){
        parent::__construct($connect);

		// Set table name explicitly
		$this->table_name = 'brand_categories';

		// Set connection to zippy database
		$this->setConn(\Boctulus\Simplerest\Core\Libs\DB::getConnection('zippy'));
	}
}

