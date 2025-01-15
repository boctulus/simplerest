<?php

namespace simplerest\core\libs;

use simplerest\core\libs\WooCommerceApiClient;

class WooCommerce {
    protected $client;
    protected $baseUrl;
    
    function __construct($config) 
    {        
        $this->baseUrl = $config['base_url'];
        $this->client = new WooCommerceApiClient(
            $config['consumer_key'],
            $config['consumer_secret']
        );
    }

    public function createProduct(array $productData) {
        $endpoint = '/wp-json/wc/v3/products';
        $url = "{$this->baseUrl}{$endpoint}";

        return $this->client
            ->url($url)
            ->post()
            ->setOAuth()
            ->setBody($productData)
            ->send()
            ->data();
    }

    public function createVariation($productId, array $variationData) {
        $endpoint = "/wp-json/wc/v3/products/{$productId}/variations";
        $url = "{$this->baseUrl}{$endpoint}";

        return $this->client
            ->url($url)
            ->post()
            ->setOAuth()
            ->setBody($variationData)
            ->send()
            ->data();
    }
    
    // Método para transformar el JSON de tu estructura al formato que espera WooCommerce
    public function transformProductData(array $jsonProduct) {
        // Transforma el producto principal
        $product = [
            'type' => $jsonProduct['type'],
            'name' => $jsonProduct['name'],
            'sku' => $jsonProduct['sku'],
            'description' => $jsonProduct['description'],
            'categories' => array_map(function($category) {
                return ['name' => $category];
            }, $jsonProduct['categories']),
            'attributes' => $this->transformAttributes($jsonProduct['attributes'])
        ];

        return $product;
    }

    protected function transformAttributes($attributes) {
        $transformed = [];
        foreach ($attributes as $name => $values) {
            $transformed[] = [
                'name' => $name,
                'visible' => true,
                'variation' => true,
                'options' => $values
            ];
        }
        return $transformed;
    }
}