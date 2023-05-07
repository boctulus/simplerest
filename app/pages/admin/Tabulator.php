<?php

namespace simplerest\pages\admin;

use simplerest\core\libs\DB;

class Tabulator /* extends Page */
{
    public $tpl_params    = [
        'title'      => 'DataGrid',
        'page_name'  => ''
    ];

    function __construct()
    {   
        css_file('vendors/tabulator/dist/css/tabulator.min.css');
        //css_file('vendors/tabulator/dist/css/tabulator_bootstrap5.min.css');
        js_file('vendors/tabulator/dist/js/tabulator.min.js');
    }

    function index($entity = null)
    {
        $main       = '';
        $right_cont = '';

        if (!empty($entity)){
            $this->tpl_params['page_name'] = "DataGrid ".ucfirst($entity);

            $main = get_view("datagrids/tabulator/tabulator", [
                'entity'   => $entity,
                'tenantid' => DB::getCurrentConnectionId(true)
            ]);  
        } 
        
        // switch($entity){
        //     case 'emergencias':
        //         $right_cont = get_view(VIEWS_PATH . "gmaps/map.php");
        //         break;
        // }

        return '
        <div class="row">
            <div class="col-9">
            '.$main.'
            </div>
            <div class="col-3">
            '. $right_cont .'
            </div>
        </div>';
    }   
}

