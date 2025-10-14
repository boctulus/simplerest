<?php

namespace Boctulus\Simplerest\Modules\RackQuoter;

class RackQuoterShortcode
{
    static function get()
    { 
        css_file('third_party/bootstrap/3.x/normalize.css');
        css_file(__DIR__ . '/assets/css/racks.css'); 
        css_file(__DIR__ . '/assets/css/styles.css');
    
        return get_view(__DIR__ . '/views/racks.php', null);              
    }
}