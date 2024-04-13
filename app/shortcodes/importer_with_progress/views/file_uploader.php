<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header"><h1>CSV Importer</h1></div>
                <div class="card-body">
                    <form enctype="multipart/form-data">
                        <div class="mb-3">
                            <label for="csvFile" class="form-label">Select CSV File</label>
                            <input class="form-control" type="file" id="csvFile" name="csvFile">
                        </div>
                        <button type="button" class="btn btn-primary" onclick="uploadCSV()">Import CSV</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
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
            fileInput.value = ''; // Esto establece el valor del input en una cadena vacía
            // Aquí puedes agregar cualquier lógica adicional para manejar la respuesta del servidor
        })
        .catch(error => {
            console.error('Error:', error);
        });
    }
</script>

