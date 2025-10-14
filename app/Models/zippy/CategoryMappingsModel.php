<?php

namespace Boctulus\Simplerest\Models\zippy;

use Boctulus\Simplerest\Models\MyModel;

class CategoryMappingsModel extends MyModel
{
    protected $table_name  = 'category_mappings';
    protected $id_name     = 'id';
    protected $connection  = 'zippy';

    protected $hidden = [];

    protected $not_fillable = ['id'];

    protected $field_names = [
        'raw_value' => 'Raw Value',
        'normalized' => 'Normalized',
        'category_id' => 'Category ID',
        'category_slug' => 'Category Slug',
        'mapping_type' => 'Mapping Type',
        'confidence' => 'Confidence',
        'notes' => 'Notes',
        'is_reviewed' => 'Reviewed',
        'reviewed_at' => 'Reviewed At',
        'created_at' => 'Created At',
        'updated_at' => 'Updated At',
        'deleted_at' => 'Deleted At',
    ];

    function __construct(bool $connect = false)
    {
        parent::__construct($connect, null, true);
    }
}
