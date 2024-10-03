<?php

namespace simplerest\controllers;

use simplerest\core\controllers\Controller;
use simplerest\core\libs\Strings;
use simplerest\core\libs\DB;
use simplerest\core\traits\TimeExecutionTrait;

/*
    https://chatgpt.com/c/66f277ae-259c-8004-95a5-a3a9f37cb7fd

    TO-DO

    - Generar la API a fin de poder alimentar el form
*/
class PromptGeneratorController extends Controller
{
    function __construct() { parent::__construct(); }

    function index()
    {
        css_file(VIEWS_PATH . 'prompt_generator/css/index.css');

        # Sweet Alert 
        css_file('third_party/sweetalert2/sweetalert2.min.css');
        js_file('third_party/sweetalert2/sweetalert.js');

        # Toastr  ---> usar para avisar cuando un Prompt fue generado con exito
        js_file(VIEWS_PATH . '/third_party/third_party/toastr/toastr.min.js');

        view('prompt_generator/index.php', [
            'title' => 'Generador de Prompt'
        ], 'templates/tpl_bt5.php');                  
    }

}

