<?php

namespace simplerest\shortcodes\star_rating;

use simplerest\core\libs\Url;

class StarRatingShortcode
{
    function __construct(){
        js_file('third_party/jquery/3.3.1/jquery.min.js');  # external
    }

    function footer($rows, $count, $avg, $ratings)
    {
        css_file('https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.2.1/assets/owl.carousel.css');
        css_file('https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.2.1/assets/owl.theme.default.css');

        css_file('https://cdnjs.cloudflare.com/ajax/libs/uikit/3.0.0-beta.40/css/uikit.min.css');
        
        #css_file(__DIR__ . '/assets/css/owl-carousel.css');    
        #css_file(__DIR__ . '/assets/css/styles.css');    

        // js_file(__DIR__ . '/assets/js/jquery-migrate.min.js', null, true);

        js_file('third_party/owl_slider/owl.carousel.min.js');          # external

        js_file('third_party/fontawesome/fontawesome_kit.js');          # external
        #css_file('third_party/fontawesome/all.min.css');                # external

        js_file(__DIR__ . '/assets/js/card-slider.js')  ;

        js_file('https://cdnjs.cloudflare.com/ajax/libs/uikit/3.0.0-beta.40/js/uikit.min.js');
        js_file('https://cdnjs.cloudflare.com/ajax/libs/uikit/3.0.0-beta.40/js/uikit-icons.min.js');
        
        return get_view(__DIR__ . '/views/star_rating.php', [
            'reviews' => $rows,
            'count'   => $count,
            'avg'     => $avg,
            'ratings' => $ratings
        ]);              
    }

    /*
        La estructura de $data es 

        [
            "paginator" => [
                "total"       => {num},  // row_count 
				"count"       => {num},  // number of rows in the current page
				"last_page"   => {num},
				"total_pages" => {num},
				"page_size"   => {num}
            ],
            "rows"      => [

            ]

        ]
    */
    function table(Array $data)
    {
        css_file('third_party/bootstrap/5.x/bootstrap.min.css');
        js_file('third_party/bootstrap/5.x/bootstrap.bundle.min.js');

        return get_view(__DIR__ . '/views/star_rating_table.php', [
            'data' => $data
        ]); 
    }

}