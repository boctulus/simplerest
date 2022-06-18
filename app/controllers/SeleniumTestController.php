<?php

namespace simplerest\controllers;

use simplerest\controllers\MyController;
use simplerest\core\Request;
use simplerest\core\Response;
use simplerest\core\libs\Factory;
use simplerest\core\libs\DB;

class SeleniumTestController extends MyController
{
    function __construct()
    {
        parent::__construct();        
    }

    function index()
    {
        return '
        <script>
            function enviar(event){
                let el_text_here = document.getElementById("text_here")
                alert(el_text_here.value)
            }
        </script>

        <div class="rendered-form">
            <div class="formbuilder-text form-group field-text-1655515442732">
                <label for="text_here" class="formbuilder-text-label">Text Field</label>
                <input type="text" class="form-control" name="text_here" id="text_here">
            </div>
            <div class="formbuilder-button form-group field-button-1655515462691">
                <button type="button" class="btn-success btn" name="my_btn" style="success" id="my_btn" onClick="enviar(event);">Enviar</button>
            </div>
        </div>
        ';
    }
}

