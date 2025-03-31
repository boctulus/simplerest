<?php

namespace Boctulus\Simplerest\Controllers;

use Boctulus\Simplerest\Core\Controllers\Controller;
use Boctulus\Simplerest\Core\Libs\Strings;
use Boctulus\Simplerest\Core\Libs\DB;
use Boctulus\Simplerest\Core\Traits\TimeExecutionTrait;

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

        # JQuery para Toastr (?)
        js_file('third_party/jquery/3.3.1/jquery.min.js');

        # Sweet Alert 
        css_file('third_party/sweetalert2/sweetalert2.min.css');
        js_file('third_party/sweetalert2/sweetalert.js');

        # Toastr  ---> usar para avisar cuando un Prompt fue generado con exito
        js_file('third_party/toastr/toastr.min.js');

        if (($_GET['fw'] ?? '') == 'alpine'){
            view('prompt_generator/index_alpine.php', [
                'title' => 'Generador de Prompt'
            ], 'templates/tpl_alpine-tailwind.php'); 
        } else {
            view('prompt_generator/index.php', [
                'title' => 'Generador de Prompt'
            ], 'templates/tpl_bt5.php');          
        }

                
    }

}

