<div class="container-fluid mt-4">

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1 class="mb-3">Generador de Prompt</h1>

            <!-- Sección de introducción -->
            <div class="mb-3">
                <label for="promptIntro" class="form-label">PROMPT Introducción</label>
                <textarea class="form-control" id="promptIntro" rows="4"
                    placeholder="Escribe el texto de introducción..."></textarea>
            </div>

            <!-- Sección para las rutas de los archivos -->
            <div class="mb-3">
                <label class="form-label">RUTAS A ARCHIVOS A INCLUIR</label>
                <div id="filePathsContainer">
                    <!-- Los inputs de rutas se agregarán aquí dinámicamente -->
                </div>
                <button class="btn btn-success mt-2" id="addFilePath">Agregar ruta</button>
                <button class="btn btn-danger mt-2 float-end" id="deleteSelectedPaths">Borrar rutas
                    seleccionadas</button>
            </div>

            <!-- Sección de notas finales -->
            <div class="mb-3">
                <label for="promptFinal" class="form-label">PROMPT (Notas finales)</label>
                <textarea class="form-control" id="promptFinal" rows="4" placeholder="Escribe las notas finales..."></textarea>
            </div>

            <!-- Botón para generar el prompt -->
            <div class="d-flex">
                <button class="btn btn-primary me-2" id="generatePrompt">Generar Prompt</button>

                <!-- Botón dropdown para ejecutar con ChatGPT o Claude -->
                <div class="btn-group">
                    <button class="btn btn-warning dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        Ejecutar con
                    </button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="#" id="executeWithChatGPT">ChatGPT</a></li>
                        <li><a class="dropdown-item" href="#" id="executeWithClaude">Claude</a></li>
                    </ul>
                </div>
            </div>


            <!-- Sección donde se muestra el prompt generado con el botón de copiar -->
            <div class="position-relative mt-4">
                <label for="generatedPrompt" class="form-label">PROMPT GENERADO</label>
                <button class="btn btn-light border" id="copyPrompt" title="Copiar al portapapeles">
                    <img src="<?= asset('img/copy-icon.svg') ?>" alt="Copiar" width="30" height="30">
                </button>
                <textarea class="form-control" id="generatedPrompt" rows="6" readonly></textarea>
            </div>
        </div>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    $(document).ready(function(){
        // Inicializar eventos
        initializeEvents();

        // Función para inicializar los eventos
        function initializeEvents() {
            $('#addFilePath').click(addFilePathInput);
            $('#deleteSelectedPaths').click(deleteSelectedPaths);
            $('#generatePrompt').click(generatePrompt);
            $('#copyPrompt').click(copyToClipboard);
            // Asignar eventos a los botones del dropdown
            $('#executeWithChatGPT').click(executeWithChatGPT);
            $('#executeWithClaude').click(executeWithClaude);

            // Eventos para los botones de eliminar en inputs dinámicos
            $(document).on('click', '.delete-file-path', function() {
                let $filePathGroup = $(this).closest('.file-path-group');
                deleteFilePath($filePathGroup);
            });
        }

        // Función para agregar un input dinámico
        function addFilePathInput() {
            let inputHtml = `
                <div class="input-group mb-2 file-path-group">
                    <div class="input-group-text">
                        <input type="checkbox" class="form-check-input mt-0 file-path-checkbox">
                    </div>
                    <input type="text" class="form-control file-path-input" placeholder="Ingresa la ruta del archivo...">
                    <button class="btn btn-outline-secondary delete-file-path" type="button">&times;</button>
                </div>
            `;
            $('#filePathsContainer').append(inputHtml);
        }

        // Función para eliminar rutas seleccionadas
        function deleteSelectedPaths() {
            let $selectedPaths = $('.file-path-checkbox:checked').closest('.file-path-group');
            if ($selectedPaths.length === 0) {
                Swal.fire('Advertencia', 'No hay rutas seleccionadas para eliminar', 'warning');
                return;
            }
            Swal.fire({
                title: '¿Estás seguro?',
                text: `¿Quieres eliminar ${$selectedPaths.length} ruta(s) seleccionada(s)?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    $selectedPaths.remove();
                    Swal.fire(
                        'Eliminado',
                        'Las rutas seleccionadas han sido eliminadas.',
                        'success'
                    );
                }
            });
        }

        // Función para eliminar una ruta específica
        function deleteFilePath($filePathGroup) {
            Swal.fire({
                title: '¿Estás seguro?',
                text: "¿Quieres eliminar esta ruta de archivo?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    $filePathGroup.remove();
                    Swal.fire(
                        'Eliminado',
                        'La ruta ha sido eliminada.',
                        'success'
                    );
                }
            });
        }

        // Función para generar el prompt
        function generatePrompt() {
            // Obtener valores de los campos
            let intro = $('#promptIntro').val();
            let finalNotes = $('#promptFinal').val();

            // Obtener las rutas de los inputs dinámicos
            let filePaths = [];
            $('.file-path-input').each(function() {
                let path = $(this).val().trim();
                if (path) {
                    filePaths.push(path);
                }
            });

            // Generar el prompt concatenado
            let generatedPrompt = intro + '\n\n';

            // Agregar archivos con cabeceras
            filePaths.forEach(function(path) {
                generatedPrompt += `### Archivo: ${path} ###\n/* Contenido del archivo ${path} */\n\n`;
            });

            // Agregar notas finales
            generatedPrompt += finalNotes;

            // Mostrar en el textarea
            $('#generatedPrompt').val(generatedPrompt);
        }

        // Función para copiar el prompt generado al portapapeles
        function copyToClipboard() {
            let generatedPrompt = $('#generatedPrompt');
            generatedPrompt.select();
            document.execCommand('copy');
        }

        // Función para ejecutar con ChatGPT
        function executeWithChatGPT() {
            let generatedPrompt = $('#generatedPrompt').val();
            console.log("Ejecutar con ChatGPT:", generatedPrompt);
            // Aquí puedes añadir la lógica para enviar el prompt a ChatGPT
        }

        // Función para ejecutar con Claude
        function executeWithClaude() {
            let generatedPrompt = $('#generatedPrompt').val();
            console.log("Ejecutar con Claude:", generatedPrompt);
            // Aquí puedes añadir la lógica para enviar el prompt a Claude
        }



    });
</script>
