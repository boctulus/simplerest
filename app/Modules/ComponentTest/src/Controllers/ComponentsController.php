<?php

namespace Boctulus\Simplerest\Controllers;

use Boctulus\Simplerest\Core\Controllers\WebController;

class ComponentsController extends WebController
{
    # /components/
    function index()
    {
        set_template('templates/tpl_bt5.php');

        $this->__view('components/index.php', [
            'title' => 'Sistema de Componentes - SimpleRest'
        ]);
    }

    # /components/examples
    function examples()
    {
        // Cargar CSS y JS necesarios para los componentes
        css_file('https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css');
        css_file('https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css');
        css_file('https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css');
        css_file('https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css');

        js_file('https://code.jquery.com/jquery-3.7.0.min.js', null, true);
        js_file('https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js');
        js_file('https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js');
        js_file('/js/componentLoader.js');
        js_file('/js/forms.js');

        set_template('templates/tpl_bt5.php');

        $this->__view('components/examples.php', [
            'title' => 'Ejemplos de Componentes - SimpleRest'
        ]);
    }
}

