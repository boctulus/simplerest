<?php 

$pivots        = array (
  'addresses,users' => 'orders',
);

$pivot_fks     = array (
  'orders' => 
  array (
    'addresses' => 'address_id',
    'users' => 'user_id',
  ),
);

$relationships = array (
  'orders' => 
  array (
    'addresses' => 
    array (
      0 => 
      array (
        0 => 'addresses.id',
        1 => 'orders.address_id',
      ),
    ),
    'users' => 
    array (
      0 => 
      array (
        0 => 'users.id',
        1 => 'orders.user_id',
      ),
    ),
  ),
);
