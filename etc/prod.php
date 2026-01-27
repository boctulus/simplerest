<?php 

return array (
  'type' => 'variable',
  'name' => 'Producto Variable',
  'description' => 'Descripción del Producto asddddddddddddddf',
  'short_description' => 'desc corta prod variable sku_padre_001',
  'sku' => 'padre_001',
  'weight' => '150',
  'length' => '7',
  'width' => '3',
  'height' => '4.5',
  'categories' => 
  array (
    0 => 
    array (
      'name' => 'Hombre',
      'slug' => 'hombre',
      'description' => '',
    ),
  ),
  'attributes' => 
  array (
    'pa_intensidad' => 
    array (
      'term_names' => 
      array (
        0 => '1',
        1 => '3',
      ),
      'is_visible' => true,
      'for_variation' => true,
    ),
    'pa_atraccion' => 
    array (
      'term_names' => 
      array (
        0 => 'Mortales',
        1 => 'Inmortales',
      ),
      'is_visible' => false,
      'for_variation' => false,
    ),
  ),
  'variations' => 
  array (
    0 => 
    array (
      'attributes' => 
      array (
        'attribute_intensidad' => '1',
        'attribute_atraccion' => 'Mortales',
      ),
      'availability_html' => '',
      'backorders_allowed' => false,
      'dimensions' => 
      array (
        'length' => '6',
        'width' => '2',
        'height' => '4',
      ),
      'dimensions_html' => '7 &times; 3 &times; 4,5 cm',
      'display_price' => 200.0,
      'display_regular_price' => 490.0,
      'image' => 
      array (
        'title' => 'Renamed-Colonia-Pura-Unisex',
        'caption' => 'Some caption',
        'url' => 'https://www.zonaperfumes.cl/wp-content/uploads/RENAMED-Acqua-Di-Parma-Colonia-Pura-Unisex.jpg',
        'alt' => 'Some ALT',
        'src' => 'https://www.zonaperfumes.cl/wp-content/uploads/Acqua-Di-Parma-Colonia-Pura-Unisex.jpg',
      ),
      'is_downloadable' => false,
      'is_in_stock' => true,
      'is_purchasable' => true,
      'is_sold_individually' => 'no',
      'is_virtual' => false,
      'max_qty' => '',
      'min_qty' => 1,
      'price_html' => '<span class="price"><del aria-hidden="true"><span class="woocommerce-Price-amount amount"><bdi><span class="woocommerce-Price-currencySymbol">&#36;</span>490</bdi></span></del> <span class="screen-reader-text">Original price was: &#036;490.</span><ins aria-hidden="true"><span class="woocommerce-Price-amount amount"><bdi><span class="woocommerce-Price-currencySymbol">&#36;</span>200</bdi></span></ins><span class="screen-reader-text">Current price is: &#036;200.</span></span>',
      'sku' => 'sku_padre_001_hijo_1_Mortales',
      'variation_description' => 'variation_description 1',
      'variation_id' => 493,
      'variation_is_active' => true,
      'variation_is_visible' => true,
      'weight' => '12000',
      'weight_html' => '12000 kg',
    ),
    1 => 
    array (
      'attributes' => 
      array (
        'attribute_intensidad' => '3',
        'attribute_atraccion' => 'Inmortales',
      ),
      'availability_html' => '',
      'backorders_allowed' => false,
      'dimensions' => 
      array (
        'length' => '7.5',
        'width' => '3',
        'height' => '4.5',
      ),
      'dimensions_html' => '7 &times; 3 &times; 4,5 cm',
      'display_price' => 222.0,
      'display_regular_price' => 333.0,
      'image' => 
      array (
        'title' => 'Renamed-Zaafaran-Midnight-Oud-Hombre.jpg',
        'caption' => 'Otro caption',
        'url' => 'https://www.zonaperfumes.cl/wp-content/uploads/RENAMED-Ard-Al-Zaafaran-Midnight-Oud-Hombre.jpg',
        'alt' => 'Otro alt',
        'src' => 'https://www.zonaperfumes.cl/wp-content/uploads/Ard-Al-Zaafaran-Midnight-Oud-Hombre.jpg',
        'srcset' => false,
        'sizes' => false,
      ),
      'is_downloadable' => false,
      'is_in_stock' => false,
      'is_purchasable' => true,
      'is_sold_individually' => 'no',
      'is_virtual' => false,
      'stock_quantity' => 401,
      'max_qty' => '',
      'min_qty' => 1,
      'price_html' => '<span class="price"><del aria-hidden="true"><span class="woocommerce-Price-amount amount"><bdi><span class="woocommerce-Price-currencySymbol">&#36;</span>333</bdi></span></del> <span class="screen-reader-text">Original price was: &#036;333.</span><ins aria-hidden="true"><span class="woocommerce-Price-amount amount"><bdi><span class="woocommerce-Price-currencySymbol">&#36;</span>222</bdi></span></ins><span class="screen-reader-text">Current price is: &#036;222.</span></span>',
      'sku' => 'sku_padre_001_hijo_3_Inmortales',
      'variation_description' => 'Descripción var 2',
      'variation_id' => 494,
      'variation_is_active' => true,
      'variation_is_visible' => true,
      'weight' => '200',
      'weight_html' => '200 kg',
    ),
  ),
);