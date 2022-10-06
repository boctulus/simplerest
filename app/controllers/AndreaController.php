<?php

namespace simplerest\controllers;

use simplerest\core\View;
use simplerest\core\libs\DB;
use simplerest\core\Request;
use simplerest\core\Response;
use simplerest\core\libs\Factory;
use simplerest\controllers\MyController;

class AndreaController extends MyController
{
    function __construct()
    {
        $this->assets();
    }

    function index(){
        $this->builder();
    }

    protected function assets(){
        css_file(
            asset('andrea/css/master.css')
        );

        css_file(
            asset('andrea/css/header2.css')
        );

        css_file(
            asset('andrea/css/bookblock.css')
        );

        css('
            .main-slider_content { background-color:#FFCB0B; }
        ');

        js_file('andrea/js/modernizr.custom.js', null, true);	
        js_file('andrea/js/jquery-ui.min.js', null, true);

        js_file('andrea/js/modernizr.custom.js');
        js_file('andrea/js/jquery.placeholder.min.js');
        js_file('andrea/js/smoothscroll.min.js');

        //<!-- Loader -->
        js_file('andrea/js/plugins/loader/js/classie.js'); 
        js_file('andrea/js/plugins/loader/js/pathLoader.js');
        js_file('andrea/js/plugins/loader/js/main.js'); //
        js_file('andrea/js/classie.js');

        //<!-- bxSlider --> 
        js_file('andrea/js/plugins/bxslider/jquery.bxslider.min.js');

        //<!--Switcher--> 
        js_file('andrea/js/plugins/switcher/js/bootstrap-select.js');
        js_file('andrea/js/plugins/switcher/js/evol.colorpicker.min.js');
        js_file('andrea/js/plugins/switcher/js/dmss.js');

        //<!-- SCRIPTS --> 
        js_file('andrea/js/plugins/isotope/jquery.isotope.min.js');

        //<!--Owl Carousel--> 
        js_file('andrea/js/plugins/owl-carousel/owl.carousel.min.js');


        //<!--THEME--> 
        js_file('andrea/js/wow.min.js');
        js_file('andrea/js/cssua.min.js');
        js_file('andrea/js/theme.js');
        js_file('andrea/js/jquerypp.custom.js');
        js_file('andrea/js/jquery.bookblock.js');

        js_file('andrea/js/custom.js');
    }

    function builder()
    {   
        view('andrea/builder');
    }

    function result(){
        view('andrea/result');
    }

    function dedication(){
        view('andrea/dedication');
    }

    function buttons(){
        view('andrea/buttons');
    }

    function more_content()
    {
        view('andrea/more_content');
    }
}

