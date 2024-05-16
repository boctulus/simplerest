<div class="container mt-4">
    <ul class="nav nav-tabs" id="myTab" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="ejecutar-orden-tab" data-bs-toggle="tab" data-bs-target="#ejecutar-orden" type="button" role="tab" aria-controls="ejecutar-orden" aria-selected="true">Ejecutar Orden</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="resultado-tab" data-bs-toggle="tab" data-bs-target="#resultado" type="button" role="tab" aria-controls="resultado" aria-selected="false">Resultado</button>
        </li>
    </ul>
    <div class="tab-content" id="myTabContent">
        <div class="tab-pane fade show active" id="ejecutar-orden" role="tabpanel" aria-labelledby="ejecutar-orden-tab">
            <div class="mt-3">
                <textarea class="form-control" id="jsonInput" rows="10" placeholder="Ingresa JSON aquí"></textarea>
                <button class="btn btn-primary mt-2 float-end" id="sendJsonBtn">Enviar</button>
            </div>
        </div>
        <div class="tab-pane fade" id="resultado" role="tabpanel" aria-labelledby="resultado-tab">
            <div class="mt-3">
                <div class="mb-3">
                    <img src="<?= shortcode_asset(__DIR__ . '/img/no-image.jpg') ?>" alt="Resultado" class="img-fluid w-100">
                </div>
                <div>
                    <table class="table">
                        <thead>
                            <tr>
                                <th scope="col" style="max-width: 70px;">Fecha y Hora de Ejecución</th>
                                <th scope="col" style="max-width: 70px;">Archivo de Orden</th>
                                <th scope="col" style="max-width: 70px;">Estado del Robot</th>
                                <th scope="col">Mensaje de Error</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- datos a rellenar -->
                            <td style="max-width: 70px;"></td>
                            <td style="max-width: 70px;"></td>
                            <td style="max-width: 70px;"></td>
                            <td></td>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// URL de la imagen por defecto
const defaultImg = '<?= shortcode_asset(__DIR__ . '/img/no-image.jpg') ?>';

// Función para realizar AJAX polling
function fetchDataAndUpdateTable() {
    // Endpoint al que se enviará la solicitud AJAX
    const endpoint = 'http://simplerest.lan/robot/status';

    // Intervalo de tiempo para realizar la solicitud (en milisegundos)
    const interval = 1000; // Se realiza cada 1 segundo

    // Función para realizar la solicitud AJAX
    function fetchData() {
        // Realizar la solicitud AJAX
        fetch(endpoint)
            .then(response => response.json())
            .then(data => {
                // Verificar si el estado final es "completed" o "failed"
                const finalStatus = data.data.robot_status;
                if (finalStatus === 'completed' || finalStatus === 'failed') {
                    // Detener el AJAX polling si el estado final es alcanzado
                    clearInterval(pollingInterval);
                }
                
                // Actualizar la tabla con los nuevos datos
                updateTable(data.data);
            })
            .catch(error => {
                console.error('Error al obtener los datos:', error);
            });
    }

    // Función para actualizar la tabla con los nuevos datos
    function updateTable(data) {
        // Obtener la referencia de la tabla y su cuerpo
        const table = document.querySelector('#resultado table');
        const tbody = table.querySelector('tbody');

        // Limpiar el contenido actual de la tabla
        tbody.innerHTML = '';

        let image_url = defaultImg;
        if (data.last_screenshot != null){
            image_url = 'http://simplerest.lan/robot/screenshots/' + data.last_screenshot + '.png';
        }      

        // Crear la fila de la tabla con los datos recibidos
        const row = document.createElement('tr');
        row.innerHTML = `
            <td style="max-width: 100px;">${data.execution_datetime}</td>
            <td style="max-width: 100px;">${data.order_file}</td>
            <td style="max-width: 70px;">${data.robot_status}</td>
            <td>${data.error_msg || 'N/A'}</td>
        `;

        // Agregar la fila a la tabla
        tbody.appendChild(row);

        // Actualizar la imagen con la URL de la captura de pantalla
        const img = document.querySelector('#resultado img');
        img.src = image_url;
    }

    // Realizar la primera solicitud de inmediato
    fetchData();

    // Configurar el intervalo para realizar el AJAX polling
    const pollingInterval = setInterval(fetchData, interval);
}

// Llamar a la función para iniciar el AJAX polling
fetchDataAndUpdateTable();

</script>