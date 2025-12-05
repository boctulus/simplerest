<?php

namespace Boctulus\Simplerest\pages\admin;

use Boctulus\Simplerest\Core\Libs\DB;

class Tabulator /* extends Page */
{
    public $tpl_params    = [
        'title'      => 'DataGrid',
        'page_name'  => ''
    ];

    function __construct()
    {   
        css_file('third_party/tabulator/dist/css/tabulator.min.css');
        //css_file('third_party/tabulator/dist/css/tabulator_bootstrap5.min.css');
        js_file('third_party/tabulator/dist/js/tabulator.min.js');
    }

    function index($entity = 'emergencias')
    {
        $this->tpl_params['page_name'] = ucfirst(
                str_replace('_', ' ', $entity)
        );

        $tenant_id = DB::getCurrentConnectionId(true);

        $incl_rels = false;

        /** Definiciones de la vista */
        $defs      = get_defs($entity, $tenant_id, false, false, $incl_rels);

        $tabulator = get_view("datagrids/tabulator/tabulator", [
            'entity'   => $entity,
            'tenantid' => DB::getCurrentConnectionId(true),
            'defs'     => $defs
        ]);  

        switch($entity){
            case 'emergencias':
                $right_cont = get_view(VIEWS_PATH . "gmaps/map.php");
                break;
            default:
                $right_cont = null;
        }

        return '
        <div class="row">
            <div class="col-9">
            '.$tabulator.'
            </div>
            <div class="col-3">
            '. $right_cont .'
            </div>
        </div>';
    }   
}

