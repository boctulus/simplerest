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
        js("
            var f = 'bootstrap3';
        ");

        js_file('vendors/mockjax/jquery.mockjax.js', null, true);

        css_file('vendors/bootstrap-datetimepicker/css/datetimepicker.css');
        js_file('vendors/bootstrap-datetimepicker/js/bootstrap-datetimepicker.js', null, true);

        css_file('vendors/bootstrap-table/bootstrap-table.min.css');
        js_file('vendors/bootstrap-table/bootstrap-table.min.js');

        css_file('vendors/x-editable/dist/bootstrap5-editable/css/bootstrap-editable.css');
        js_file('vendors/x-editable/dist/bootstrap5-editable/js/bootstrap-editable.min.js');
        
        css_file('vendors/x-editable/dist/inputs-ext/typeaheadjs/lib/typeahead.js-bootstrap.css');
        js_file('vendors/x-editable/dist/inputs-ext/typeaheadjs/lib/typeahead.js');  
        js_file('vendors/x-editable/dist/inputs-ext/typeaheadjs/typeaheadjs.js');       
        
        css_file('vendors/x-editable/dist/inputs-ext/wysihtml5/bootstrap-wysihtml5-0.0.3/bootstrap-wysihtml5-0.0.3.css');
        js_file('vendors/x-editable/dist/inputs-ext/wysihtml5/bootstrap-wysihtml5-0.0.3/wysihtml5-0.3.0.min.js', null, true);
        js_file('vendors/x-editable/dist/inputs-ext/wysihtml5/bootstrap-wysihtml5-0.0.3/bootstrap-wysihtml5-0.0.3.min.js', null, true);
        js_file('vendors/x-editable/dist/inputs-ext/wysihtml5/bootstrap-wysihtml5-0.0.3/bootstrap-wysihtml5-0.0.3.min.js', null, true);

        css_file('vendors/x-editable/dist/bootstrap5-editable/css/demo-bs3.css');

        css_file('vendors/x-editable/dist/inputs-ext/address/address.css');
        js_file('vendors/x-editable/dist/inputs-ext/address/address.js');
    }

    function index()
    {
        //  Tag::registerBuilder(\simplerest\core\libs\HtmlBuilder\Bt5Form::class);

        // $html = tag('inputText')->value(microtime());
        
        // return $html->render();   
        
        view('datagrids/x-editable/x-editable.php', null, 'tpl.php');
    }
}

