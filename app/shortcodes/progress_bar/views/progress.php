<div class="elementor-widget-container">
   <div class="text-center pt-4 mt-2 px-0">
      <h6 class="h4 text-dark pb-4">
         Proceso:
      </h6>
      <div class="container text-center pb-3  px-0">
         <div class="row px-0">
            <div class="cold-flex justify-content-center justify-content-sm-end px-0">

            <div>
               <!-- Coloca la imagen aquí -->
               <img src="<?= shortcode_asset(__DIR__ . '/img/loading-2.gif') ?>" id="loading-image" width="60px">
               <img src="<?= shortcode_asset(__DIR__ . '/img/time_over.png') ?>" id="timeover" height="60px" style="display:none">

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
        if (value == null){
            return;
        }

        if (value < 0 || value > 100){
            throw `Progress bar only accept values from 0 to 100. Current value ='${value}'`
        }

        console.log(`Setting value ='${value}'`);

        $('progress#progress-bar').val(value)
    }

    /*
        Función que realiza la llamada Ajax para completion

        Se utiliza la tecnica de "polling" o "bucle de llamadas" para realizar llamadas periódicas hasta que se cumpla la condición deseada
        para evitar recursividad
    */

    let completion = null;

    // aun no ha terminado?
    function isOver(startTime, max_polling_time) {
        let currentTime = new Date().getTime();
        return (currentTime - startTime > max_polling_time * 1000);
    }

    function get_completion_callback(max_polling_time = 3600)
    {
        let data = {
            'some_key':'some value'
        };

        /*
            Iniciar el proceso en background
        */

        jQuery.ajax({
            url: `/bzz_import/run`,
            type: "POST",
            dataType: "json",
            success: function(res) {
                console.log(res);               
            },
            error: function(xhr, status, error) {
                console.error("Error en la llamada Ajax: " + error);
            }
        });
        
        let startTime = new Date().getTime();

        /*
            Obtencion de datos en tiempo real
        */

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
                        setProgress(100); 
                        // ...
                    } else {
                        if (!isOver(startTime, max_polling_time)){
                            // Si no es 100, seguir haciendo la llamada periódicamente
                            setTimeout(pollCompletion, 1000);
                        } else {
                            console.log("Time is over!");

                            // Reemplazar el icono de carga por el de tiempo agotado
                            $('#loading-image').hide()
                            $('#timeover').show()                          
                        }
                       
                        completion = data.data.completion 
                     
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

    setTimeout(()=>{
        get_completion_callback();
    }, 100)
   
</script>