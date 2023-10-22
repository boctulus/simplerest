<?php

namespace simplerest\shortcodes\tax_calc;

class TaxCalcShortcode
{
    static function get()
    { 
        //css_file('vendors/bootstrap/3.x/normalize.css');
        css_file(__DIR__ . '/assets/css/styles.css');

        //js_file(__DIR__ . '/assets/js/jquery.min.js', null, true);
        js_file(__DIR__ . '/assets/js/jquery-migrate.min.js', null, true);
        js_file(__DIR__ . '/assets/js/numeral.min.js');
        js_file(__DIR__ . '/assets/js/jquery-calx-2.2.6.js');
        js_file(__DIR__ . '/assets/js/js_extra.js');
        js_file(__DIR__ . '/assets/js/tf_tc_script.js');
        js_file(__DIR__ . '/assets/js/tf_tc_charts_config.js');
        js_file(__DIR__ . '/assets/js/jquery.formatCurrency.js');
        js_file(__DIR__ . '/assets/js/jquery.ui.touch-punch.min.js');    
    
        return get_view(__DIR__ . '/views/tax_calc.php', null);              
    }
}