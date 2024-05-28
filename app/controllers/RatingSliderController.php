<?php

namespace simplerest\controllers;

use simplerest\core\libs\DB;
use simplerest\core\libs\Strings;
use simplerest\core\controllers\Controller;
use simplerest\shortcodes\star_rating\StarRatingShortcode;

class RatingSliderController extends Controller
{
    function index(){
        $this->rating_slider();
    }

    /*
        Test de shortcode
    */
    function rating_slider(){        
        $sc = new StarRatingShortcode();

        render($sc->rating_slider());
    }

    /*
        Test de shortcode
    */
    function rating_table()
    {
        $sc = new StarRatingShortcode();

        render($sc->rating_table());
    }
}

