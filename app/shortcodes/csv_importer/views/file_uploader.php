<style>
    #upload_btn {
        width: 130px;
    }
</style>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h1>CSV Importer</h1>
                </div>
                <div class="card-body">
                    <form enctype="multipart/form-data">
                        <div class="mb-3">
                            <label for="csvFile" class="form-label">Select CSV File</label>
                            <input class="form-control" type="file" id="csvFile" name="csvFile">
                        </div>
                        <!-- Botón de importación con spinner -->
                        <button type="button" id="upload_btn" class="btn btn-primary" onclick="uploadCSV()">
                            <span id="spinner-container" class="spinner-container d-none">
                                <span id="spinner" class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                            </span>
                            Import CSV
                        </button>

                    </form>
                    <!-- Barra de progreso -->
                    <div id="progress-bar-container" class="mt-3">
                        <progress id="progress-bar" value="0" max="100" style="width:100%; height: 24px;">0%</progress>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function spinnerUp() {
        // Obtener el botón y el contenedor del spinner
        const button = document.querySelector('.btn-primary');
        const spinnerContainer = document.getElementById('spinner-container');

        // Mostrar el contenedor del spinner y desactivar el botón
        spinnerContainer.classList.remove('d-none');
        button.disabled = true;
    }

    function spinnerDown() {
        // Obtener el botón y el contenedor del spinner
        const button = document.querySelector('.btn-primary');
        const spinnerContainer = document.getElementById('spinner-container');

        // Ocultar el contenedor del spinner y activar el botón
        spinnerContainer.classList.add('d-none');
        button.disabled = false;
    }


    // Esta función se ejecuta cuando se hace clic en el botón "Import CSV"
    function uploadCSV() {
        // Obtener el input de tipo file
        const fileInput = document.getElementById('csvFile');

        // Verificar si se ha seleccionado un archivo
        if (fileInput.files.length === 0) {
            // Si no se ha seleccionado ningún archivo, mostrar un mensaje de error
            console.error('No file selected');
            return;
        }

        // Obtener el archivo seleccionado
        const file = fileInput.files[0];

        // Crear FormData y agregar el archivo seleccionado
        const formData = new FormData();
        formData.append('csvFile', file);

        // Realizar la solicitud Ajax
        fetch('/csv_importer/upload', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json()) // Convertir la respuesta a JSON
            .then(data => {
                // Manejar la respuesta del servidor
                console.log(data);

                // Limpiar el input de tipo file después de procesar el archivo
                //fileInput.value = ''; 
            })
            .catch(error => {
                console.error('Error:', error);
            });
    }

    /*
        Ej:

        setProgress(46)
    */
    function setProgress(value) {
        if (value == null) {
            return;
        }

        if (value < 0 || value > 100) {
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
    let startTime;

    // aun no ha terminado?
    function isOver(startTime, max_polling_time) {
        let currentTime = new Date().getTime();
        return (currentTime - startTime > max_polling_time * 1000);
    }

    function get_until_completion_callback(max_polling_time = 3600) {
        /*
            Obtencion de datos en tiempo real
        */

        function pollUntilCompletion() {
            let currentPage = 1;

            function pollPage() {
                // Obtener los parámetros de página
                const data = {
                    "page": currentPage.toString(),
                    "page_size": "10"
                };

                // Realizar la solicitud Ajax con los parámetros de página
                jQuery.ajax({
                    url: `/csv_importer/process_page`,
                    type: "POST",
                    dataType: "json",
                    contentType: "application/json",
                    data: JSON.stringify(data),
                    success: function(data) {
                        console.log(data)

                        // Actualizar la respuesta en la página
                        $("#response").text(JSON.stringify(data));

                        console.log('%', data.data.completion);

                        // Verificar si la completitud es igual a 100
                        if (data.data.completion == 100) {
                            setProgress(100);
                            // ...
                        } else {
                            completion = data.data.completion;
                            setProgress(completion);

                            // Verificar si hay una página siguiente
                            if (data.data.paginator.next !== null) {
                                // Incrementar el contador de página y continuar solicitando
                                currentPage++;
                                pollPage();
                            } else {
                                console.log("All pages processed!");
                                $('#loading-image').hide();
                            }
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error("Error en la llamada Ajax: ", error);
                    }
                });
            }

            // Comenzar a solicitar páginas
            pollPage();
        }

        pollUntilCompletion();
    }


    let data = {
        'some_key': 'some value'
    };

    setTimeout(() => {
        // Iniciar el bucle de llamadas
        startTime = new Date().getTime();
        get_until_completion_callback();
    }, 300)
</script>