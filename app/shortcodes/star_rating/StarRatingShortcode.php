<?php

namespace simplerest\shortcodes\star_rating;

class StarRatingShortcode
{
    static function get()
    { 
        // css_file('third_party/bootstrap/3.x/normalize.css');

        css_file('https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.2.1/assets/owl.carousel.css');
        css_file('https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.2.1/assets/owl.theme.default.css');
        css_file('https://cdnjs.cloudflare.com/ajax/libs/uikit/3.0.0-beta.40/css/uikit.min.css');

        #css_file(__DIR__ . '/assets/css/owl-carousel.css');    
        #css_file(__DIR__ . '/assets/css/styles.css');    

        // js_file(__DIR__ . '/assets/js/jquery-migrate.min.js', null, true);

        js_file('third_party/jquery/3.3.1/jquery.min.js');              # external
        js_file('third_party/owl_slider/owl.carousel.min.js');          # external
        js_file(__DIR__ . '/assets/js/card-slider.js')  ;

        js_file('https://cdnjs.cloudflare.com/ajax/libs/uikit/3.0.0-beta.40/js/uikit.min.js');
        js_file('https://cdnjs.cloudflare.com/ajax/libs/uikit/3.0.0-beta.40/js/uikit-icons.min.js');
        
        return get_view(__DIR__ . '/views/star_rating.php', null);              
    }
}