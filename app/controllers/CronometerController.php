<?php

namespace Boctulus\Simplerest\Controllers;

use Boctulus\Simplerest\Core\Controllers\Controller;
use Boctulus\Simplerest\Core\Request;
use Boctulus\Simplerest\Core\Response;
use Boctulus\Simplerest\Core\Libs\Factory;
use Boctulus\Simplerest\Core\Libs\DB;

/*
    Podria armar componente para el cronometro de JS
*/
class CronometerController extends Controller
{
    function index()
    {
?>
        <div id="tiempo_cuest">
            <form name="crono">
                <p>
                    <span style="font-size: 24px">Tiempo utilizado = </span><input type="text" size="9" id="display" value="00:35:00" style="font-size: 24px; border-width:0;">
                </p>
                <div class="btn-group" style="margin-top: -10px;">
                    <button type="button" class="btn btn-laura btn btn-raised btn-primary" id="restart-test">Reiniciar</button>
                    <button type="button" class="btn btn-laura btn btn-raised btn-success" style="margin-left: 5px;" id="see-results">Ver resultados</button>
                </div>
            </form>
        </div>

        <script>
            /*
                Cronometro

                # Iniciar
                
                Se puede iniciar tomando el valor de el INPUT

                    startCrono()
                
                O bien pasarle el tiempo
                
                    startCrono('00:35:00')

                Se recomienda no ejectar el crono hasta que no se haya renderizado la pagina

                document.addEventListener("DOMContentLoaded", function() {
                    startCrono('00:35:00')
                });

                # Detener

                    stopCrono();

                # Re-iniciar

                    startCrono('00:35:00')

                O sea, para re-iniciar es necesario pasar el valor de inicio

            */

            let currentCronoInterval; 

            const stopCrono = () => {
                clearInterval(currentCronoInterval);
            }

            const startCrono = (tiempoInicial = null) => {
                const display = document.getElementById("display");
                tiempoInicial = tiempoInicial || display.value;
                const tiempo = tiempoInicial.split(":");

                if (typeof tiempoInicial != 'undefined' && tiempoInicial != null){
                    display.value = tiempoInicial
                }

                stopCrono();
                
                let horas = parseInt(tiempo[0]);
                let minutos = parseInt(tiempo[1]);
                let segundos = parseInt(tiempo[2]);

                currentCronoInterval = setInterval(() => {
                    if (segundos > 0) {
                        segundos--;
                    } else {
                        if (minutos > 0) {
                            minutos--;
                            segundos = 59;
                        } else {
                            if (horas > 0) {
                                horas--;
                                minutos = 59;
                                segundos = 59;
                            } else {
                                clearInterval(currentCronoInterval);
                            }
                        }
                    }

                    const nuevoTiempo = `${horas.toString().padStart(2, "0")}:${minutos.toString().padStart(2, "0")}:${segundos.toString().padStart(2, "0")}`;
                    display.value = nuevoTiempo;

                    if (horas === 0 && minutos === 0 && segundos === 0) {
                        clearInterval(currentCronoInterval);
                        // Aquí puedes agregar acciones adicionales cuando el contador llegue a cero.
                        alert("¡Tiempo agotado!");
                    }
                }, 1000);
            };

            // Agregar un manejador de eventos al botón "Reiniciar"
            const restartButton = document.getElementById("restart-test");
            
            restartButton.addEventListener("click", () => {
                // Llamar a startCrono con el tiempo inicial deseado (en este caso, "00:35:00")
                startCrono("00:35:00");
            });

            document.addEventListener("DOMContentLoaded", function() {
                startCrono()
            });


        </script>
    <?php

    }
}
