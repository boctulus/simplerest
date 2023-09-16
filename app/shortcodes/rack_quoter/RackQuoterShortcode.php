<?php

namespace simplerest\shortcodes\rack_quoter;

class RackQuoterShortcode
{
    static function get()
    { 
        css_file('vendors/bootstrap/3.x/normalize.css');
        css_file(SHORTCODES_PATH . 'assets/css/racks.css');
        css_file(SHORTCODES_PATH . 'assets/css/styles.css');
    
        return get_view(SHORTCODES_PATH . 'rack_quoter/views/racks.php', null);              
    }
}