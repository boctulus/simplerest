<style>
    #upload_btn, #pause_btn, #resume_btn,#cancel_btn {
        width: 130px;
    }

    .disabled {
        opacity: 0.6;  /* Reduce la opacidad para indicar estado deshabilitado */
        pointer-events: none;  /* Evita interacciones con el elemento */
        filter: grayscale(100%);  /* Aplica escala de grises para efecto visual */
        cursor: not-allowed;  /* Cambia el cursor a 'no permitido' */
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
                        <!-- Botones de acción -->
                        <button type="button" id="upload_btn" class="btn btn-primary" onclick="uploadCSV()">
                            <span id="spinner-container" class="spinner-container d-none">
                                <span id="spinner" class="spinner-border spinner-border-sm" role="status"
                                    aria-hidden="true"></span>
                            </span>
                            Import CSV
                        </button>
                        <button type="button" id="pause_btn" class="btn btn-primary d-none" onclick="pauseCSV()">
                            Pause
                        </button>
                        <button type="button" id="resume_btn" class="btn btn-primary d-none" onclick="resumeCSV()">
                            Resume
                        </button>
                        <button type="button" id="cancel_btn" class="btn btn-secondary d-none" onclick="cancelCSV()">
                            Cancel
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
    let file;
    let completion;
    let currentPage;
    let startTime;
    let debug = true;

    /*
        Manejar status: [ "ready", "active" (in progress), "paused", "completed" ]

        Comienza en "ready" 

        Si bien al terminar queda en "completed" si la pagina es recargada en "completed" debe pasar a "ready"
    */

    function fileSelected(){
        const fileInput = document.getElementById('csvFile');

        // Verificar si se ha seleccionado un archivo
        return (fileInput.files.length > 0);
    }

    function clearInputFile(){
        const fileInput = document.getElementById('csvFile');
        fileInput.value = '';
    }

    function toggleInputFile(enabled){
        const fileInput = document.getElementById('csvFile');
        fileInput.disabled = !enabled;
    }

    function setImportStatus(status) {
        const allowedStatus = ["ready", "active", "paused", "completed"];

        if (!allowedStatus.includes(status)) {
            throw new Error(`Error: El estado '${status}' no es válido.`);
        }

        localStorage.setItem("bzz-importer-status", status);
    }

    function getImportStatus() {
        return localStorage.getItem('bzz-importer-status');
    }

    function setStatusAsReady() {
        setImportStatus("ready");
    }

    function setStatusAsActive() {
        setImportStatus("active");
    }

    function setStatusAsPaused() {
        setImportStatus("paused");
    }

    function setStatusAsCompleted() {
        setImportStatus("completed");
    }

    function setStatusAsCancelled() {
        setImportStatus("ready");    // <--- asi
    }

    /**
     * Alternates visibility of the upload button based on a boolean flag.
     * @param {boolean} enabled - Indicates whether the button should be enabled (true) or disabled (false).
     */
    function toggleUploadButtonVisibility(enabled) {
        const uploadButton = document.getElementById('upload_btn');
        uploadButton.classList.toggle('d-none', !enabled);

        if (enabled){
            toggleUploadButton(true);
        }        
    }

    /**
     * Alternates visibility of the pause button based on a boolean flag.
     * @param {boolean} enabled - Indicates whether the button should be enabled (true) or disabled (false).
     */
    function togglePauseButtonVisibility(enabled) {
        const pauseButton = document.getElementById('pause_btn');
        pauseButton.classList.toggle('d-none', !enabled);

        if (enabled){
            togglePauseButton(true);
        }  
    }

    /**
     * Alternates visibility of the resume button based on a boolean flag.
     * @param {boolean} enabled - Indicates whether the button should be enabled (true) or disabled (false).
     */
    function toggleResumeButtonVisibility(enabled) {
        const resumeButton = document.getElementById('resume_btn');
        resumeButton.classList.toggle('d-none', !enabled);

        if (enabled){
            toggleResumeButton(true);
        }  
    }

    function toggleButtons($status){
        togglePauseButtonVisibility($status  == 'active');
        toggleResumeButtonVisibility($status == 'paused');
        toggleCancelButtonVisibility($status == 'active' || $status == 'paused');
        toggleUploadButton(!fileSelected() && ($status == 'ready' || $status == 'completed'));
    }


    /**
     * Alternates visibility of the cancel button based on a boolean flag.
     * @param {boolean} enabled - Indicates whether the button should be enabled (true) or disabled (false).
     */
    function toggleCancelButtonVisibility(enabled) {
        const cancelButton = document.getElementById('cancel_btn');
        cancelButton.classList.toggle('d-none', !enabled);

        if (enabled){
            toggleCancelButton(true);
        }  
    }

    /**
     * Alternates enabled/disabled state of the upload button based on a boolean flag.
     * @param {boolean} enabled - Indicates whether the button should be enabled (true) or disabled (false).
     */
    function toggleUploadButton(enabled) {
        const button = document.getElementById('upload_btn');
        button.disabled = !enabled;

        if (enabled) {
            button.classList.remove('disabled');
        } else {
            button.classList.add('disabled');
        }

        toggleInputFile(enabled);
    }

    /**
     * Alternates enabled/disabled state of the pause button based on a boolean flag.
     * @param {boolean} enabled - Indicates whether the button should be enabled (true) or disabled (false).
     */
    function togglePauseButton(enabled) {
        const button = document.getElementById('pause_btn');
        button.disabled = !enabled;
        if (enabled) {
            button.classList.remove('disabled');
        } else {
            button.classList.add('disabled');
        }
    }

    /**
     * Alternates enabled/disabled state of the resume button based on a boolean flag.
     * @param {boolean} enabled - Indicates whether the button should be enabled (true) or disabled (false).
     */
    function toggleResumeButton(enabled) {
        const button = document.getElementById('resume_btn');
        button.disabled = !enabled;
        if (enabled) {
            button.classList.remove('disabled');
        } else {
            button.classList.add('disabled');
        }
    }

    /**
     * Alternates enabled/disabled state of the cancel button based on a boolean flag.
     * @param {boolean} enabled - Indicates whether the button should be enabled (true) or disabled (false).
     */
    function toggleCancelButton(enabled) {
        const button = document.getElementById('cancel_btn');
        button.disabled = !enabled;
        if (enabled) {
            button.classList.remove('disabled');
        } else {
            button.classList.add('disabled');
        }
    }

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
            // console.error('No file selected');
            return;
        }

        toggleUploadButton(false);
        showProgress();

        // Obtener el archivo seleccionado
        file = fileInput.files[0];

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
            if (debug){
                console.log(data);
            }               

            showProgress();
            
            startTime = new Date().getTime();
            get_until_completion_callback();

            togglePauseButtonVisibility(true);
            toggleCancelButtonVisibility(true);
        })
        .catch(error => {
            console.error('Error:', error);
        });
    }

    // revisar
    function pauseCSV() {        
        toggleResumeButtonVisibility(true);
        togglePauseButtonVisibility(false);
        setStatusAsPaused();
    }

    // revisar
    function resumeCSV() {        
        toggleResumeButtonVisibility(false);
        togglePauseButtonVisibility(true);        
        checkCompletionStatus()
        setStatusAsActive();
    }

    // ok
    function cancelCSV() {
        fetch('/csv_importer/cancel', {
            method: 'POST'
        })
            .then(response => response.json())
            .then(data => {
                setImportStatus('ready');
                hideProgress();
                setProgress(0);
                toggleCancelButtonVisibility(false);
                togglePauseButtonVisibility(false);
                toggleResumeButtonVisibility(false);

                localStorage.removeItem('importInProgress');
                localStorage.removeItem('importPaused');
                localStorage.removeItem('progress');
                localStorage.removeItem('currentPage');
                localStorage.removeItem('importCompleted');
                console.log(data.message);

                setStatusAsCancelled();
                toggleUploadButton(true);
            })
            .catch(error => {
                console.error('Error al cancelar:', error);
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

        if (debug){
            console.log(`Setting value ='${value}'`);
        }        

        toggleUploadButton(false);
        $('progress#progress-bar').val(value)
    }

    // Función para mostrar la barra de progreso
    function showProgress() {
        $('#progress-bar').val(completion);
        $('#progress-bar-container').show();
    }

    // Función para ocultar la barra de progreso
    function hideProgress() {
        $('#progress-bar-container').hide();
    }

    /*
        Función que realiza la llamada Ajax para completion

        Se utiliza la tecnica de "polling" o "bucle de llamadas" para realizar llamadas periódicas hasta que se cumpla la condición deseada
        para evitar recursividad
    */

    // aun no ha terminado?
    function isOver(startTime, max_polling_time) {
        let currentTime = new Date().getTime();
        return (currentTime - startTime > max_polling_time * 1000);
    }

    function get_until_completion_callback(page = 1, max_polling_time = 3600) {
        let status = getImportStatus();

        if (status != null && status.includes('paused', 'completed')) {  
            return;
        }

        function pollPage(page) {
            status = getImportStatus();

            if (status != null && status.includes('paused', 'completed')) {  
                return;
            }

            // Obtener los parámetros de página
            const data = {
                "page": page.toString()
            };

            // Realizar la solicitud Ajax con los parámetros de página
            jQuery.ajax({
                url: `/csv_importer/process_page`,
                type: "POST",
                dataType: "json",
                contentType: "application/json",
                data: JSON.stringify(data),
                success: function (data) {
                    if (debug){
                        console.log(data);
                    }     

                    // Actualizar la respuesta en la página
                    $("#response").text(JSON.stringify(data));

                    if (debug){
                        console.log('%', data.data.completion);
                    }        

                    // Verificar si la completitud es igual a 100
                    if (data.data.completion == 100) {
                        setProgress(100);
                        setImportStatus('completed');                        
                        clearInputFile();
                    } else {
                        completion = data.data.completion;
                        setProgress(completion);

                        // console.log('Next page', data.data.paginator.next);

                        // Verificar si hay una página siguiente
                        if (data.data.paginator.next !== null) {
                            // Incrementar el contador de página y continuar solicitando
                            page++;
                            pollPage(page);
                        } else {
                            console.log("All pages processed!");
                            $('#loading-image').hide();
                        }
                    }
                },
                error: function (xhr, status, error) {
                    console.error("Error en la llamada Ajax: ", error);
                }
            });
        }

        // Comenzar a solicitar páginas
        pollPage(page);

    }

    // Función para obtener el estado de completitud y la página actual
    function checkCompletionStatus() {
    return new Promise((resolve, reject) => {
        fetch('/csv_importer/get_completion')
        .then(response => response.json())
        .then(data => {
            completion = data.data.completion;
            currentPage = parseInt(data.data.current_page);

            if (completion !== null) {
                if (completion < 100) {
                    showProgress();

                    status = getImportStatus();

                    if (status != null && (status.includes('active') || status.includes('ready'))) {
                        // Iniciar el bucle de llamadas para actualizar el progreso desde la página actual
                        startTime = new Date().getTime();
                        get_until_completion_callback(currentPage + 1);
                    }  
                    
                    toggleUploadButton(false);
                } else if (completion == 100) {
                    toggleUploadButton(true);
                }                
            } 
            resolve(); // Resuelve la promesa cuando se completa la lógica
        })
        .catch(error => {
            console.error('Error al obtener el estado de completitud:', error);
            reject(error); // Rechaza la promesa si hay un error
        });
    });
}

// Llama a checkCompletionStatus y luego ejecuta la lógica adicional
checkCompletionStatus().then(() => {
    let status = getImportStatus();

    if (currentPage > 0) {
        if (status == 'paused') {
            toggleResumeButtonVisibility(true);                        
        } else {
            togglePauseButtonVisibility(true);
        }                    
        
        toggleCancelButtonVisibility(true);
    } else {
        toggleButtons(status);
    }           

    if (status == 'active' || status == 'paused') {
        showProgress();
    }
});


    // Verificar el estado de completitud al cargar la página
    $(document).ready(function () {      
        console.log(getImportStatus());
        
        switch (getImportStatus()){
            case 'completed':
                setImportStatus('ready');
                break;
            case undefined:
                setImportStatus('ready');
                break;
            case 'active':
        }
        
        checkCompletionStatus().then(()=>{
            $status = getImportStatus();

            toggleButtons($status);

            if ($status == 'active' || $status == 'paused'){
                showProgress();
            }
        })

       
    });



</script>