<div class="container mt-4">
        <h2 class="mb-3">Generador de Prompt</h2>
        
        <!-- Sección de introducción -->
        <div class="mb-3">
            <label for="promptIntro" class="form-label">PROMPT Introducción</label>
            <textarea class="form-control" id="promptIntro" rows="4" placeholder="Escribe el texto de introducción..."></textarea>
        </div>
        
        <!-- Sección para las rutas de los archivos -->
        <div class="mb-3">
            <label for="filePaths" class="form-label">RUTAS A ARCHIVOS A INCLUIR</label>
            <textarea class="form-control" id="filePaths" rows="3" placeholder="Ingresa las rutas de los archivos, una por línea..."></textarea>
        </div>
        
        <!-- Sección de notas finales -->
        <div class="mb-3">
            <label for="promptFinal" class="form-label">PROMPT (Notas finales)</label>
            <input type="text" class="form-control" id="promptFinal" placeholder="Escribe las notas finales...">
        </div>
        
        <!-- Botón para generar el prompt -->
        <button class="btn btn-primary" id="generatePrompt">Generar Prompt</button>

        <!-- Sección donde se muestra el prompt generado con el botón de copiar -->
        <div class="position-relative mt-4">
            <label for="generatedPrompt" class="form-label">PROMPT GENERADO</label>
            <!-- Botón de copiar con ícono en el margen superior derecho -->
            <button class="btn btn-light border" id="copyPrompt" title="Copiar al portapapeles">
                <!-- Icono SVG de copiar -->
                <!-- Referencia a la imagen SVG -->
                <img src="<?= asset('img/copy-icon.svg') ?>" alt="Copiar" width="30" height="30">
            </button>
            <textarea class="form-control" id="generatedPrompt" rows="6" readonly></textarea>
        </div>
    </div>

    <script>
        $(document).ready(function(){
            $('#generatePrompt').click(function(){
                // Obtener valores de los campos
                let intro = $('#promptIntro').val();
                let filePaths = $('#filePaths').val().split('\n');
                let finalNotes = $('#promptFinal').val();
                
                // Generar el prompt concatenado
                let generatedPrompt = intro + '\n\n';
                
                // Agregar archivos con cabeceras
                filePaths.forEach(function(path) {
                    if(path.trim() !== '') {
                        generatedPrompt += `### Archivo: ${path.trim()} ###\n` + `/* Contenido del archivo ${path.trim()} */\n\n`;
                    }
                });
                
                // Agregar notas finales
                generatedPrompt += finalNotes;
                
                // Mostrar en el textarea
                $('#generatedPrompt').val(generatedPrompt);
            });

            // Copiar al portapapeles
            $('#copyPrompt').click(function(){
                let generatedPrompt = $('#generatedPrompt');
                generatedPrompt.select();
                document.execCommand('copy');
            });
        });
    </script>