<?php

namespace simplerest\controllers\demos;

use simplerest\core\controllers\Controller;
use simplerest\core\Request;
use simplerest\core\Response;
use simplerest\core\libs\Factory;
use simplerest\core\libs\DB;

class IdanalizerTestController extends Controller
{
    /*
        - Requiere de API KEY
            
        Fatal error: Uncaught InvalidArgumentException: Please provide an API key in D:\www\woo1\wp-content\plugins\analyze\extras\src\CoreAPI.php on line <i>66</i></th></tr>

        <tr><th align='left' bgcolor='#f57900' colspan="5"><span style='background-color: #cc0000; color: #fce94f; font-size: x-large;'>( ! )</span> InvalidArgumentException: Please provide an API key in D:\www\woo1\wp-content\plugins\analyze\extras\src\CoreAPI.php on line <i>66</i></th></tr>

        - El plugin "analyze" no esta manejando bien la situacion de no recibir un FILE "doc" o "photo"

         Warning: Undefined array key "doc" in D:\www\woo1\wp-content\plugins\analyze\class\VerifyUser.php on line <i>36<
    */
    function index()
    {
        ?>           
            <form method="POST">            
                <div>
                    Foto DOC<br> 
                    <input type="file" name="doc">
                </div>
                <br>
                
                <div>
                    Foto selfie<br>
                    <input type="file" name="photo">
                </div>

                <p>
                <input type="submit" value="Enviar">
            </form>

            <script>
            
            /*
                Reemplazar el JS 
            */

            var form = document.querySelector('form');
            form.addEventListener('submit', function(event) {
                event.preventDefault();
                
                var xhr = new XMLHttpRequest();
                /*
                */
                xhr.open('POST', 'https://beta.escorts.red/wp-json/analyze/verify');
                xhr.onload = function() {
                    if (xhr.status === 200) {
                        console.log('Archivo subido con éxito');
                    } else {
                        console.log('Error al subir archivo');
                    }
                };
                

                var form = document.querySelector('form');
                var formData = new FormData(form);
            
                xhr.send(formData);

            });
            </script>

        <?php
    }
}

