<?php

namespace simplerest\controllers;

use simplerest\core\libs\DB;
use simplerest\core\Request;
use simplerest\core\Response;
use simplerest\core\libs\Factory;
use simplerest\core\libs\ApiClient;
use simplerest\controllers\MyController;

class Test1Controller extends MyController
{
    function __construct()
    {
        parent::__construct();        
    }

    function index()
    {
        $body = <<<BODY
        {
            "data": {
                "products": [
            {
                "id": 15,
                "type": "variable",
                "product_url": null,
                "name": "VP 1 (zapatos) ssssssdddddxxxRRRRRDDfff",
                "slug": "vp-1-zapatos",
                "status": "publish",
                "featured": false,
                "catalog_visibility": "visible",
                "description": "Zapatos para macho alfaaaadddsssttt",
                "short_description": "Zapatos de hombre",
                "sku": "ZAP1",
                "date_created": "2022-10-15 19:06:32",
                "date_modified": "2022-11-07 09:13:07",
                "price": "300",
                "regular_price": "",
                "sale_price": "",
                "manage_stock": false,
                "stock_quantity": null,
                "stock_status": "instock",
                "is_sold_individually": false,
                "weight": "2",
                "length": "10",
                "width": "20",
                "height": "10",
                "parent_id": 0,
                "tags": [],
                "categories": [
                    {
                        "name": "A2",
                        "slug": "a2",
                        "description": ""
                    }
                ],
                "image_id": "16",
                "image": [
                    "http://woo3.lan/wp-content/uploads/2022/10/17-compo-3045-1024x1024.jpg",
                    1024,
                    1024,
                    true
                ],
                "gallery_image_ids": [],
                "gallery_images": [],
                "attributes": {
                    "pa_color": {
                        "term_names": [
                            "marron",
                            "azul"
                        ],
                        "is_visible": true
                    }
                },
                "default_attributes": {
                    "pa_color": "marron"
                },
                "variations": [
                    {
                        "attributes": {
                            "attribute_pa_color": "marron"
                        },
                        "availability_html": "",
                        "backorders_allowed": false,
                        "dimensions": {
                            "length": "10",
                            "width": "20",
                            "height": "10"
                        },
                        "dimensions_html": "10 &times; 20 &times; 10 cm",
                        "display_price": 30,
                        "display_regular_price": 40,
                        "image": {
                            "title": "17-compo-3045",
                            "caption": "",
                            "url": "http://woo3.lan/wp-content/uploads/2022/10/17-compo-3045.jpg",
                            "alt": "",
                            "src": "http://woo3.lan/wp-content/uploads/2022/10/17-compo-3045-416x416.jpg",
                            "srcset": "http://woo3.lan/wp-content/uploads/2022/10/17-compo-3045-416x416.jpg 416w, http://woo3.lan/wp-content/uploads/2022/10/17-compo-3045-324x324.jpg 324w, http://woo3.lan/wp-content/uploads/2022/10/17-compo-3045-100x100.jpg 100w, http://woo3.lan/wp-content/uploads/2022/10/17-compo-3045-300x300.jpg 300w, http://woo3.lan/wp-content/uploads/2022/10/17-compo-3045-1024x1024.jpg 1024w, http://woo3.lan/wp-content/uploads/2022/10/17-compo-3045-150x150.jpg 150w, http://woo3.lan/wp-content/uploads/2022/10/17-compo-3045-768x768.jpg 768w, http://woo3.lan/wp-content/uploads/2022/10/17-compo-3045-1536x1536.jpg 1536w, http://woo3.lan/wp-content/uploads/2022/10/17-compo-3045.jpg 2000w",
                            "sizes": "(max-width: 416px) 100vw, 416px",
                            "full_src": "http://woo3.lan/wp-content/uploads/2022/10/17-compo-3045.jpg",
                            "full_src_w": 2000,
                            "full_src_h": 2000,
                            "gallery_thumbnail_src": "http://woo3.lan/wp-content/uploads/2022/10/17-compo-3045-100x100.jpg",
                            "gallery_thumbnail_src_w": 100,
                            "gallery_thumbnail_src_h": 100,
                            "thumb_src": "http://woo3.lan/wp-content/uploads/2022/10/17-compo-3045-324x324.jpg",
                            "thumb_src_w": 324,
                            "thumb_src_h": 324,
                            "src_w": 416,
                            "src_h": 416
                        },
                        "image_id": 16,
                        "is_downloadable": false,
                        "is_in_stock": true,
                        "is_purchasable": true,
                        "is_sold_individually": "no",
                        "is_virtual": false,
                        "max_qty": "",
                        "min_qty": 1,
                        "price_html": "<span class=\"price\"><del aria-hidden=\"true\"><span class=\"woocommerce-Price-amount amount\"><bdi><span class=\"woocommerce-Price-currencySymbol\">&pound;</span>40.00</bdi></span></del> <ins><span class=\"woocommerce-Price-amount amount\"><bdi><span class=\"woocommerce-Price-currencySymbol\">&pound;</span>30.00</bdi></span></ins></span>",
                        "sku": "ZAP1M",
                        "variation_description": "",
                        "variation_id": 18,
                        "variation_is_active": true,
                        "variation_is_visible": true,
                        "weight": "2",
                        "weight_html": "2 kg"
                    },
                    {
                        "attributes": {
                            "attribute_pa_color": "azul"
                        },
                        "availability_html": "",
                        "backorders_allowed": false,
                        "dimensions": {
                            "length": "10",
                            "width": "20",
                            "height": "10"
                        },
                        "dimensions_html": "10 &times; 20 &times; 10 cm",
                        "display_price": 54,
                        "display_regular_price": 600,
                        "image": {
                            "title": "17-compo-3045",
                            "caption": "",
                            "url": "http://woo3.lan/wp-content/uploads/2022/10/17-compo-3045.jpg",
                            "alt": "",
                            "src": "http://woo3.lan/wp-content/uploads/2022/10/17-compo-3045-416x416.jpg",
                            "srcset": "http://woo3.lan/wp-content/uploads/2022/10/17-compo-3045-416x416.jpg 416w, http://woo3.lan/wp-content/uploads/2022/10/17-compo-3045-324x324.jpg 324w, http://woo3.lan/wp-content/uploads/2022/10/17-compo-3045-100x100.jpg 100w, http://woo3.lan/wp-content/uploads/2022/10/17-compo-3045-300x300.jpg 300w, http://woo3.lan/wp-content/uploads/2022/10/17-compo-3045-1024x1024.jpg 1024w, http://woo3.lan/wp-content/uploads/2022/10/17-compo-3045-150x150.jpg 150w, http://woo3.lan/wp-content/uploads/2022/10/17-compo-3045-768x768.jpg 768w, http://woo3.lan/wp-content/uploads/2022/10/17-compo-3045-1536x1536.jpg 1536w, http://woo3.lan/wp-content/uploads/2022/10/17-compo-3045.jpg 2000w",
                            "sizes": "(max-width: 416px) 100vw, 416px",
                            "full_src": "http://woo3.lan/wp-content/uploads/2022/10/17-compo-3045.jpg",
                            "full_src_w": 2000,
                            "full_src_h": 2000,
                            "gallery_thumbnail_src": "http://woo3.lan/wp-content/uploads/2022/10/17-compo-3045-100x100.jpg",
                            "gallery_thumbnail_src_w": 100,
                            "gallery_thumbnail_src_h": 100,
                            "thumb_src": "http://woo3.lan/wp-content/uploads/2022/10/17-compo-3045-324x324.jpg",
                            "thumb_src_w": 324,
                            "thumb_src_h": 324,
                            "src_w": 416,
                            "src_h": 416
                        },
                        "image_id": 16,
                        "is_downloadable": false,
                        "is_in_stock": true,
                        "is_purchasable": true,
                        "is_sold_individually": "no",
                        "is_virtual": false,
                        "max_qty": "",
                        "min_qty": 1,
                        "price_html": "<span class=\"price\"><del aria-hidden=\"true\"><span class=\"woocommerce-Price-amount amount\"><bdi><span class=\"woocommerce-Price-currencySymbol\">&pound;</span>60.00</bdi></span></del> <ins><span class=\"woocommerce-Price-amount amount\"><bdi><span class=\"woocommerce-Price-currencySymbol\">&pound;</span>54.00</bdi></span></ins></span>",
                        "sku": "ZAP1A",
                        "variation_description": "",
                        "variation_id": 17,
                        "variation_is_active": true,
                        "variation_is_visible": true,
                        "weight": "2",
                        "weight_html": "2 kg"
                    }
                ]
            }
        ]
        
            }
        }
        BODY;
        
        $user_website = 'http://woo4.lan';
        
        $res = \simplerest\core\libs\ApiClient::instance()
        ->setBody($body)
        ->setHeaders([
            'X-API-KEY' => 'woo3-010011010101'
        ])
        ->post("$user_website/wp-json/connector/v1/products");
        
        dd($res->getResponse());
        dd($res->getStatus(), 'STATUS CODE');
        dd($res->getError(), 'ERROR');
                        
    }
}

