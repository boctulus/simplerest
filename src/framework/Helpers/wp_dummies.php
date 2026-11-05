<?php

declare(strict_types=1);
/*
    Dummy functions for WP equivalents
*/

// Dummy WP apply_filters()
function apply_filters($hook_name, $value, ...$args){
    return $value;
}

