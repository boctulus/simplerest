<?php

namespace Boctulus\Simplerest\Modules\CSVImporter;

use Boctulus\Simplerest\Core\Libs\Config;
use Boctulus\Simplerest\Core\Libs\DB;
use Boctulus\Simplerest\Core\Libs\Url;
use Boctulus\Simplerest\Core\Libs\Paginator;

class Importer
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