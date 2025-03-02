<?php

namespace simplerest\shortcodes\star_rating;

use simplerest\core\libs\Config;
use simplerest\core\libs\DB;
use simplerest\core\libs\Url;
use simplerest\core\libs\Paginator;

class StarRatingShortcode
{
    function __construct(){
        js_file('third_party/jquery/3.3.1/jquery.min.js');  # external
    }

    function rating_slider(){
        $rows = table('star_rating')
        ->take(10)
        ->offset(0)
        ->orderBy([
            'id' => 'DESC'
        ])
        ->get();

        $count = table('star_rating')
        ->count();

        $avg  = table('star_rating')
        ->avg('score');

        $ratings = [];
        for ($stars=1; $stars<=5; $stars++){            
            $ratings[$stars] = table('star_rating')
            ->where(['score' => $stars])
            ->count();
        }

        /*
            Array
            (
                [1] => 8
                [2] => 3
                [3] => 4
                [4] => 3
                [5] => 4
            )
        */
        // dd($ratings);
    
        return $this->footer($rows, $count, $avg, $ratings);
    }

    function rating_table()
    {
        // En WordPress por ejemplo, no puedo usar ?page=
        $page_key   = Config::get()['paginator']['params']['page'] ?? 'page';
    
        $page_size = $_GET['size'] ?? 10;
        $page      = $_GET[$page_key] ?? 1;

        $offset = Paginator::calcOffset($page, $page_size);

        DB::getConnection();

        $rows = table('star_rating')
        ->take($page_size)
        ->offset($offset)
        ->get();

        $row_count = table('star_rating')->count();

        $paginator = Paginator::calc($page, $page_size, $row_count);
        $last_page = $paginator['totalPages'];

        $data = [
            "paginator" => [
                "current_page" => $page,
                "last_page"    => $last_page,
                "page_size"    => $page_size,
            ],
            "rows"      => $rows
        ];

        return $this->table($data);
    }

    protected function footer($rows, $count, $avg, $ratings)
    {
        css_file('https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.2.1/assets/owl.carousel.css');
        css_file('https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.2.1/assets/owl.theme.default.css');

        css_file('https://cdnjs.cloudflare.com/ajax/libs/uikit/3.0.0-beta.40/css/uikit.min.css');
        
        #css_file(__DIR__ . '/assets/css/owl-carousel.css');    
        #css_file(__DIR__ . '/assets/css/styles.css');    

        // js_file(__DIR__ . '/assets/js/jquery-migrate.min.js', null, true);

        js_file('third_party/owl_slider/owl.carousel.min.js');          # external

        js_file('third_party/fontawesome/5/fontawesome_kit.js');          # external
        #css_file('third_party/fontawesome/5/all.min.css');                # external

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
    protected function table(Array $data)
    {
        css_file('third_party/bootstrap/5.x/bootstrap.min.css');
        js_file('third_party/bootstrap/5.x/bootstrap.bundle.min.js');

        return get_view(__DIR__ . '/views/star_rating_table.php', [
            'data' => $data
        ]); 
    }

}