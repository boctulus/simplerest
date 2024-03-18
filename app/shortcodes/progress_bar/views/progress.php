<div class="elementor-widget-container">
   <div class="text-center pt-4 mt-2 px-0">
      <h6 class="h4 text-dark pb-4">
         Proceso:
      </h6>
      <div class="container text-center pb-3  px-0">
         <div class="row px-0">
            <div class="cold-flex justify-content-center justify-content-sm-end px-0">

            <div id="loading-text">
               <!-- Coloca la imagen aquí -->
               <img src="<?= shortcode_asset(__DIR__ . '/img/loading.gif') ?>" id="loading-image" width="40px">

               <div id="progress-bar-container">   
                  <progress id="progress-bar" value="0" max="100" style="width:300px; height: 24px;"> 0% </progress>
               </div>
            </div>
                           
            </div>
         </div>
      </div>
   </div>   	
</div>

<script>
    /*
        Ej:

        setProgress(46)
    */
    function setProgress(value){
        if (value < 0 || value > 100){
            throw "Progress bar only accept values from 0 to 100"
        }

        $('progress#progress-bar').val(value)
    }

    /*
        Función que realiza la llamada Ajax para completion

        Se utiliza la tecnica de "polling" o "bucle de llamadas" para realizar llamadas periódicas hasta que se cumpla la condición deseada
        para evitar recursividad
    */

    let completion = null;

    function get_completion_callback() {
        function pollCompletion() {
            jQuery.ajax({
                url: `/bzz_import/get_completion`,
                type: "GET",
                dataType: "json",
                success: function(data) {
                    // Actualizar la respuesta en la página
                    $("#response").text(JSON.stringify(data));

                    console.log('%', data.data.completion);

                    // Verificar si la completitud es igual a 100
                    if (data.data.completion == 100) {
                        // Ocultar
                        jQuery('#loading-text').hide();
                    } else {
                        // Si no es 100, seguir haciendo la llamada periódicamente
                        setTimeout(pollCompletion, 1000);

                        if (data.data.completion == 0){
                            completion =  completion + 10; // sumo 10% a pedido de Facundo (si continua en 0%)
                        } else {
                            completion = data.data.completion 
                        }

                        setProgress(completion); 
                    }
                },
                error: function(xhr, status, error) {
                    console.error("Error en la llamada Ajax: " + error);
                }
            });
        }

        // Iniciar el bucle de llamadas
        pollCompletion();
    }

   
</script>