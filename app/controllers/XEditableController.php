<?php

namespace simplerest\controllers;

use simplerest\controllers\MyController;

use simplerest\core\libs\HtmlBuilder\Bt5Form;
use simplerest\core\libs\HtmlBuilder\Tag;

class XEditableController extends MyController
{
    function __construct()
    {
        parent::__construct();

        css_file('vendors/x-editable/bt/css/bootstrap-editable.css');
        css_file('vendors/x-editable/bt/css/style.scss');

        js_file('vendors/x-editable/bt/js/bootstrap-editable.js');
    }

    function index()
    {
        //  Tag::registerBuilder(\simplerest\core\libs\HtmlBuilder\Bt5Form::class);

        // $html = tag('inputText')->value(microtime());
        
        // return $html->render();   
        
        view('datagrids/x-editable/x-editable.php', null, 'tpl.php');
    }
}

