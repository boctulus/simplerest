<?php

namespace Boctulus\Simplerest\Models;

use Boctulus\Simplerest\Core\Model;

/**
 * Base class for the application's models (generated models extend it).
 */
class MyModel extends Model
{
    function __construct(bool $connect = false, $schema = null, bool $load_config = true){
        parent::__construct($connect, $schema, $load_config);

        $this->field_names = [
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'deleted_at' => 'Deleted At'
        ];
    }

    protected function boot(){
    }

    protected function init(){
    }
}
