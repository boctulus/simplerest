<?php

namespace simplerest\transformers;

use simplerest\core\controllers\Controller;

class ProductsTransformer implements \simplerest\core\interfaces\ITransformer
{
	public function transform(object $product, Controller $controller = NULL)
    {
        return [
			'id' => $product->id,
			'name' => $product->name,
			'description' => $product->description,
			'size' => $product->size,
			'cost' => $product->cost,
			'created_at' => $product->created_at,
			'created_by' => $product->created_by,
			'updated_at' => $product->updated_at,
			'updated_by' => $product->updated_by,
			'deleted_at' => $product->deleted_at,
			'is_active' => $product->is_active ? 'true' : 'false',	
			'is_locked' => $product->is_locked ? 'true' : 'false',			
			'belongs_to' => $product->belongs_to
        ];
	}
}







