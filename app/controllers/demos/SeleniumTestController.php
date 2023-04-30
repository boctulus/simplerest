<?php

namespace simplerest\controllers;

use simplerest\controllers\MyController;
use simplerest\core\Request;
use simplerest\core\Response;
use simplerest\core\libs\Factory;
use simplerest\core\libs\DB;

class SeleniumTestController extends MyController
{
    function index()
    {
        return '
        <script>
            function enviar(event){
                let el_text_here = document.getElementById("text_here")
                alert(el_text_here.value)
            }

            setTimeout(function(){
                let el = document.createElement("article");
                el.setAttribute("id", "myDynamicElement");
                el.innerHTML = "<p>Probando 1 2 3</p> <p class=\'entry-summary\'>bla bla<p/> <p>Mas contenido</p>";

                document.body.appendChild(el);
            }, 1500)
            
        </script>

        <div class="rendered-form">
            <div class="formbuilder-text form-group field-text-1655515442732">
                <label for="text_here" class="formbuilder-text-label">Text Field</label>
                <input type="text" class="form-control" name="text_here" id="text_here">
            </div>
            <div class="formbuilder-button form-group field-button-1655515462691">
                <button type="button" class="btn-success btn mt-3" style="margin-top:10px" name="my_btn" style="success" id="my_btn" onClick="enviar(event);">Enviar</button>
            </div>
        </div>
        ';
    }

    function action_chains(){
        view('selenium\action_chains.php');
    }
}

