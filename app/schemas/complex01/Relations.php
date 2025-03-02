<?php 

return [
        'related_tables' => array (
  'customer_details' => 
  array (
    0 => 'customers',
  ),
  'customers' => 
  array (
    0 => 'users',
    1 => 'sellers',
    2 => 'customer_details',
    3 => 'orders',
  ),
  'order_items' => 
  array (
    0 => 'orders',
    1 => 'products',
  ),
  'orders' => 
  array (
    0 => 'customers',
    1 => 'sellers',
    2 => 'order_items',
  ),
  'products' => 
  array (
    0 => 'order_items',
    1 => 'seller_products',
  ),
  'seller_products' => 
  array (
    0 => 'sellers',
    1 => 'products',
  ),
  'sellers' => 
  array (
    0 => 'users',
    1 => 'sellers',
    2 => 'customers',
    3 => 'orders',
    4 => 'seller_products',
  ),
  'support_tickets' => 
  array (
    0 => 'users',
  ),
  'users' => 
  array (
    0 => 'customers',
    1 => 'sellers',
    2 => 'support_tickets',
  ),
),
        'relation_type'  => array (
  'customer_details~customers' => '1:1',
  'customers~users' => '1:1',
  'customers~sellers' => 'n:m',
  'customers~customer_details' => '1:1',
  'customers~orders' => '1:n',
  'order_items~orders' => 'n:1',
  'order_items~products' => 'n:1',
  'orders~customers' => 'n:1',
  'orders~sellers' => 'n:1',
  'orders~order_items' => '1:n',
  'products~order_items' => '1:n',
  'products~seller_products' => '1:n',
  'seller_products~sellers' => 'n:1',
  'seller_products~products' => 'n:1',
  'sellers~users' => 'n:m',
  'sellers~sellers' => '1:n',
  'sellers~customers' => 'n:m',
  'sellers~orders' => '1:n',
  'sellers~seller_products' => '1:n',
  'support_tickets~users' => 'n:1',
  'users~customers' => '1:1',
  'users~sellers' => 'n:m',
  'users~support_tickets' => '1:n',
  'orders~products' => 'n:m',
  'products~orders' => 'n:m',
  'products~sellers' => 'n:m',
  'sellers~products' => 'n:m',
),
        'multiplicity'   => array (
  'customer_details~customers' => false,
  'customers~users' => false,
  'customers~sellers' => true,
  'customers~customer_details' => false,
  'customers~orders' => true,
  'order_items~orders' => false,
  'order_items~products' => false,
  'orders~customers' => false,
  'orders~sellers' => false,
  'orders~order_items' => true,
  'products~order_items' => true,
  'products~seller_products' => true,
  'seller_products~sellers' => false,
  'seller_products~products' => false,
  'sellers~users' => true,
  'sellers~sellers' => true,
  'sellers~customers' => true,
  'sellers~orders' => true,
  'sellers~seller_products' => true,
  'support_tickets~users' => false,
  'users~customers' => false,
  'users~sellers' => true,
  'users~support_tickets' => true,
  'orders~products' => true,
  'products~orders' => true,
  'products~sellers' => true,
  'sellers~products' => true,
),
];