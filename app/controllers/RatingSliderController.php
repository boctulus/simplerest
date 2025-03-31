<?php

namespace Boctulus\Simplerest\Controllers;

use Boctulus\Simplerest\Core\Libs\DB;
use Boctulus\Simplerest\Core\Libs\Strings;
use Boctulus\Simplerest\Core\Controllers\Controller;
use Boctulus\Simplerest\Modules\StarRating\StarRating;

class RatingSliderController extends Controller
{
    function index(){
        $this->rating_slider();
    }

    /*
        Test de shortcode
    */
    function rating_slider(){        
        $sc = new StarRating();

        render($sc->rating_slider());
    }

    /*
        Test de shortcode
    */
    function rating_table()
    {
        $sc = new StarRating();

        render($sc->rating_table());
    }
}

