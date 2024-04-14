<?php

namespace simplerest\shortcodes\csv_importer;

use simplerest\core\libs\Config;
use simplerest\core\libs\DB;
use simplerest\core\libs\Url;
use simplerest\core\libs\Paginator;

class ImporterShortcode
{
    function __construct(){
        js_file('third_party/jquery/3.3.1/jquery.min.js'); 
    }

    function index()
    {
        css_file(__DIR__ . '/assets/css/styles.css');

        return get_view(__DIR__ . '/views/file_uploader.php');
    }
}