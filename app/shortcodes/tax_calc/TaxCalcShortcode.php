<?php

namespace simplerest\shortcodes\tax_calc;

class TaxCalcShortcode
{
    static function get()
    { 
        css_file('vendors/bootstrap/3.x/normalize.css');
        css_file(__DIR__ . '/assets/css/styles.css');
    
        return get_view(__DIR__ . '/views/tax_calc.php', null);              
    }
}