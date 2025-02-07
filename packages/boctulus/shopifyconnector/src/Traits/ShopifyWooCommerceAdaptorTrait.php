<?php

namespace simplerest\traits;

trait ShopifyWooCommerceAdaptorTrait
{
    protected $shopify_to_woocommerce_eq = [
		'Title'                     => 'name',
		'Body (HTML)'               => 'description',
		'Quantity'                  => 'stock_quantity',
		'Variant Inventory Qty'     => 'stock_quantity',
		'Image Src'                 => 'images',
		'Variant Image'             => 'images',
		'Variant SKU'               => 'sku',
		'Variant Price'             => 'sale_price',
		'Variant Compare At Price'  => 'regular_price',
		'Type'                      => 'category_ids',
		'Tags'                      => 'tag_ids_spaces',
		'Variant Grams'             => 'weight',
		'Variant Requires Shipping' => 'meta:shopify_requires_shipping',
		'Variant Taxable'           => 'tax_status',
    ];
    
     /*
        Convierte la estructura de productos de Shopify a la de WooCommerce

        La estructura puede haber cambiado revisa el array que proviene del source code de WooCommerce:

        $this->shopify_to_woocommerce_eq
    */
    function adaptToWooCommerce(Array $a, $shop, $api_key, $api_secret, $api_ver){

        $pid = $a['id'];

        $a['name'] = $a['title'];
        $a['description'] = $a['body_html'];

        // Visibility ('hidden', 'visible', 'search' or 'catalog')
        if ($a['published_scope'] == 'web'){
            $a['visibility'] = 'visible';
        } else {
            $a['visibility'] = 'hidden';
        }	

        /*
            status

            En WooCommerce puede ser publish, draft, pending
            En Shopify serían active, draft, archived
        */

        $a['status'] = $this->convertProductStatusFromShopifyToWooCommerce($a['status']);
        
        /*
            tags

            En WooCommerce es un array
            En Shopify están separados por ", "
        */

        $tags = explode(',', $a['tags']);
        
        foreach ($tags as $k => $tag){
            $tags[$k] = trim($tag);
        }

        $a['tags'] = $tags;


        // Categories

        $a['categories'] = [];

        if (isset($a["product_type"]) && !empty($a["product_type"])){
            $a['categories'][] = 	$a["product_type"];
        }

        $a['categories'] = array_merge($a['categories'], $this->getCollectionsByProductId($shop, $pid, $api_key, $api_secret, $api_ver) ?? []);

        /*
            Variations as simple products
        */

        $vars = [];
        foreach($a['variants'] as $k => $v){

            // atributos

            /*
                Deben seguir la estructura de los atributos para productos simples de WooCommerce:

                'attributes' => 
                    array (
                        'pa_talla' => 
                        array (
                        ),
                        'pa_color' => 
                        array (
                        ),
                    )
            */		
            $attributes = [];

            foreach ($a['options'] as $i => $op){
                $name  = $op['name'];
                $value = $v['option' . ($i +1)];
                $term  = strtolower($name);

                if ($term == 'size'){
                    $term = 'talla';
                } elseif ($term == 'style'){
                    $term = 'estilo';
                } elseif ($term == 'title'){
                    continue;
                }

                $term = 'pa_' . $term;

                $attributes[ $term ] = [ $value ];
            }

            $name = $a['name'];

            if ($v['title'] != 'Default Title'){
                $name = $name . ' - ' . $v['title'];
            }

            $vars[$k] = [
                'type'				=> 'simple',
                'name'       		=> $name,
                'description' 		=> $a['description'],
                'visibility'  		=> $a['visibility'],
                'status'      		=> $a['status'],
                'tags'        		=> $a['tags'],
                'categories'  		=> $a['categories'],
                'regular_price'		=> $v['price'],
                'sale_price'  		=> $v['compare_at_price'],
                'sku'         		=> $v['sku'],
                'weight'	  		=> $v['weight'],
                'stock_quantity' 	=> $v['inventory_quantity'],
                'manage_stock'      => $v['inventory_management'] !== NULL,
                //'tax_status'		=> $v['taxable'] ? 'taxable' : 'none',
                'attributes' 		=> $attributes
            ];
        

            foreach ($a['images'] as $img){
                foreach ($img['variant_ids'] as $vid){
                    if ($vid == $v['id']){
                        $vars[$k]['image'][0] = $img['src'];
                        $vars[$k]['image'][1] = $img['width'];
                        $vars[$k]['image'][2] = $img['height'];
                        break 2;
                    }
                }
            }

        }

        // si es un producto "simple"
        if (isset($a['image']) && $a['variants'][0]['title'] == 'Default Title'){            
            $vars[0]['image'] = [
                $img['src'],
                $img['width'],
                $img['height']
            ];
        }
        
        return $vars;
    }

    /*
		Status

		En WooCommerce puede ser publish, draft, pending
		En Shopify serían active, draft, archived
	*/
    function convertProductStatusFromShopifyToWooCommerce(string $status, bool $strict = false){
        $arr = [
            'active'   => 'publish',
            'archived' => 'draft',
            'draft'    => 'draft' 
        ];

        if (in_array($status, $arr)){
            return $arr[$status];
        }

        if ($strict){
            throw new \InvalidArgumentException("Status $status no válido para Shopify");
        }

        return $status;
    }

    function convertProductStatusFromWooCommerceToShopify(string $status, bool $strict = false) {
        $arr = [
            'publish' => 'active',
            'draft'   => 'draft', 
            'pending' => 'draft'
        ];

        if (in_array($status, $arr)){
            return $arr[$status];
        }

        if ($strict){
            throw new \InvalidArgumentException("Status $status no válido para Shopify");
        }

        return $status;
    }
}
