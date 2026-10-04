<?php

namespace Boctulus\Simplerest\Models\main;

use Boctulus\Simplerest\Models\MyModel;
use Boctulus\Simplerest\Schemas\main\WebhooksSchema;

class WebhooksModel extends MyModel
{
	protected $hidden   = ['secret'];
	protected $not_fillable = ['secret'];

	protected $createdAt = 'created_at';
	protected $createdBy = 'created_by';
	protected $updatedAt = 'updated_at';
	protected $updatedBy = 'updated_by';
	protected $deletedAt = 'deleted_at';
	protected $deletedBy = 'deleted_by';

    function __construct(bool $connect = false){
        parent::__construct($connect, WebhooksSchema::class);
	}	
}

