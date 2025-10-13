<?php

namespace Boctulus\Simplerest\Controllers\datagrids;

use Boctulus\Simplerest\Core\Controllers\Controller;
use
    DataTables\Database,
    DataTables\Editor,
    DataTables\Editor\Field;

/*
    PRUEBA de DATATABLES (el original)

    El EDITOR ¨free¨ no permite ni borrar ni crear nuevos registros

    https://datatables.net/forums/discussion/72902/is-this-the-paid-version-of-data-tables
*/

class DatatablesNetController extends Controller
{
    function __construct()
    {
        parent::__construct();        
    }

    function index()
    {   
        css_file('https://cdn.datatables.net/v/dt/jqc-1.12.4/dt-1.13.1/b-2.3.2/sl-1.5.0/datatables.min.css');
		css_file('third_party/datatables/css/editor.dataTables.min.css');
        css_file('https://cdn.datatables.net/buttons/1.1.2/css/buttons.dataTables.min.css');
        css_file('https://cdn.datatables.net/select/1.1.2/css/select.dataTables.min.css');
        css_file("https://cdn.datatables.net/responsive/2.0.2/css/responsive.dataTables.min.css");
        
        js_file('https://cdn.datatables.net/1.10.19/js/jquery.dataTables.js');
        js_file('https://cdn.datatables.net/buttons/1.1.2/js/dataTables.buttons.min.js');
        js_file('https://cdn.datatables.net/select/1.1.2/js/dataTables.select.min.js');
        js_file('https://cdn.datatables.net/responsive/2.0.2/js/dataTables.responsive.min.js');
        js_file('http://kingkode.com/datatables.editor.lite/js/altEditor/dataTables.altEditor.free.js');

        js_file(VIEWS_PATH . 'factory_parts/js/custom_dt.js');

        view('factory_parts/parts');
    }
}

