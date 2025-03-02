<?php 

$pivots        = array (
  'sellers,users' => 'customers',
  'orders,products' => 'order_items',
  'customers,sellers' => 'orders',
  'products,sellers' => 'seller_products',
);

$pivot_fks     = array (
  'customers' => 
  array (
    'users' => 'user_id',
    'sellers' => 'assigned_seller',
  ),
  'order_items' => 
  array (
    'orders' => 'order_id',
    'products' => 'product_id',
  ),
  'orders' => 
  array (
    'customers' => 'customer_id',
    'sellers' => 'seller_id',
  ),
  'seller_products' => 
  array (
    'sellers' => 'seller_id',
    'products' => 'product_id',
  ),
  'sellers' => 
  array (
    'users' => 'user_id',
    'sellers' => 'referred_by',
  ),
);

$relationships = array (
  'customers' => 
  array (
    'users' => 
    array (
      0 => 
      array (
        0 => 'users.id',
        1 => 'customers.user_id',
      ),
    ),
    'sellers' => 
    array (
      0 => 
      array (
        0 => 'sellers.id',
        1 => 'customers.assigned_seller',
      ),
    ),
  ),
  'order_items' => 
  array (
    'orders' => 
    array (
      0 => 
      array (
        0 => 'orders.id',
        1 => 'order_items.order_id',
      ),
    ),
    'products' => 
    array (
      0 => 
      array (
        0 => 'products.id',
        1 => 'order_items.product_id',
      ),
    ),
  ),
  'orders' => 
  array (
    'customers' => 
    array (
      0 => 
      array (
        0 => 'customers.id',
        1 => 'orders.customer_id',
      ),
    ),
    'sellers' => 
    array (
      0 => 
      array (
        0 => 'sellers.id',
        1 => 'orders.seller_id',
      ),
    ),
  ),
  'seller_products' => 
  array (
    'sellers' => 
    array (
      0 => 
      array (
        0 => 'sellers.id',
        1 => 'seller_products.seller_id',
      ),
    ),
    'products' => 
    array (
      0 => 
      array (
        0 => 'products.id',
        1 => 'seller_products.product_id',
      ),
    ),
  ),
  'sellers' => 
  array (
    'users' => 
    array (
      0 => 
      array (
        0 => 'users.id',
        1 => 'sellers.user_id',
      ),
    ),
    'sellers' => 
    array (
      0 => 
      array (
        0 => 'sellers.id',
        1 => 'sellers.referred_by',
      ),
    ),
  ),
);
