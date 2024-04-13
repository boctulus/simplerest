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
                        <button type="button" class="btn btn-primary">Import CSV</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Esta función se ejecuta cuando se selecciona un archivo
    function handleFileSelect(event) {
        const files = event.target.files;
        const formData = new FormData();

        // Agregar el archivo seleccionado al FormData
        formData.append('csvFile', files[0]);

        // Realizar la solicitud Ajax
        fetch('/csv_importer/upload', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json()) // Convertir la respuesta a JSON
        .then(data => {
            // Manejar la respuesta del servidor
            console.log(data);
            // Aquí puedes agregar cualquier lógica adicional para manejar la respuesta del servidor
        })
        .catch(error => {
            console.error('Error:', error);
        });
    }

    // Escuchar el evento 'change' en el input de tipo file
    document.getElementById('csvFile').addEventListener('change', handleFileSelect);
</script>

