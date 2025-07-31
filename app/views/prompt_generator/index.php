<style>
    .navigation-buttons {
        margin-left: auto;
        /* Empuja los botones hacia la derecha */
    }

    .navigation-buttons button {
        min-width: 40px;
        padding: 0.375rem 0.75rem;
    }
</style>

<div class="container-fluid mt-4">

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="d-flex align-items-center position-relative mb-3">
                <h1 class="mb-3">Generador de Prompt</h1>
                <div class="ms-auto"> <!-- Botones de navegacion -->
                    <button class="btn btn-primary me-2" id="prevPrompt">&lt;</button>
                    <button class="btn btn-primary" id="nextPrompt">&gt;</button>
                </div>
            </div>

            <!-- Sección de introducción -->
            <div class="mb-5 position-relative">
                <label for="prompt-description" class="form-label">PROMPT Introducción</label>
                <textarea class="form-control" id="prompt-description" rows="4"
                    placeholder="Escribe el texto de introducción..."></textarea>
                <div class="d-flex justify-content-between mt-3">
                    <button class="btn btn-success" id="newFormButton">Nuevo</button>
                    <div>
                        <button class="btn btn-primary me-2" id="extractPathsButton">Agregar rutas en PROMPT</button>
                        <button class="btn btn-danger" id="clearFormButton">Borrar Form</button>
                    </div>
                </div>
            </div>

            <!-- Sección para las rutas de los archivos -->
            <div class="mb-3">
                <label class="form-label">RUTAS A ARCHIVOS A INCLUIR</label>
                <div id="filePathsContainer"></div>
                <button class="btn btn-success mt-2" id="addFilePath">Agregar ruta</button>

                <div class="btn-group mt-2 float-end">
                    <button class="btn btn-secondary dropdown-toggle" type="button" id="bulkActionDropdown"
                        data-bs-toggle="dropdown" aria-expanded="false">
                        Acción masiva
                    </button>
                    <ul class="dropdown-menu" aria-labelledby="bulkActionDropdown">
                        <li><a class="dropdown-item" href="#" id="deleteSelectedPaths">Borrar rutas</a></li>
                        <li><a class="dropdown-item" href="#" id="enableSelectedPaths">Habilitar</a></li>
                        <li><a class="dropdown-item" href="#" id="disableSelectedPaths">Deshabilitar</a></li>
                    </ul>
                </div>
            </div>

            <!-- Sección de notas finales -->
            <div class="mb-3">
                <label for="promptFinal" class="form-label">PROMPT (Notas finales)</label>
                <textarea class="form-control" id="promptFinal" rows="4"
                    placeholder="Escribe las notas finales..."></textarea>
            </div>

            <!-- Botones para generar el prompt y ejecutar con opciones -->
            <div class="d-flex justify-content-between">
                <button id="generate-prompt" class="btn btn-primary">Generar Prompt</button>

                <!-- Botón dropdown para ejecutar con ChatGPT o Claude -->
                <div class="btn-group">
                    <button class="btn btn-warning dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false" id="executeButton">
                        Ejecutar con
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="#" id="executeWithChatGPT">ChatGPT</a></li>
                        <li><a class="dropdown-item" href="#" id="executeWithClaude">Claude</a></li>
                    </ul>
                </div>
            </div>

            <!-- Sección donde se muestra el prompt generado con el botón de copiar y el menú de opciones -->
            <div class="position-relative mt-4 mb-5">
                <label for="generatedPrompt" class="form-label">PROMPT GENERADO</label>
                <div class="dropdown float-end">
                    <button class="btn btn-light border dropdown-toggle" type="button" id="promptOptionsDropdown"
                        data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-three-dots-vertical"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="promptOptionsDropdown">
                        <li><a class="dropdown-item" href="#" id="fullscreenPrompt">Pantalla completa</a></li>
                        <li><a class="dropdown-item" href="#" id="copyPrompt">Copiar a portapapeles</a></li>
                    </ul>
                </div>
                <!--button class="btn btn-light border" id="copyPrompt" title="Copiar al portapapeles">
                    <img src="< ?= asset('img/copy-icon.svg') ? >" alt="Copiar" width="30" height="30">
                </button-->
                <textarea class="form-control" id="generatedPrompt" rows="6"></textarea>
            </div>
        </div>
    </div>
</div>

<script>
    let currentPromptIndex = -1;
    let cachedPrompts = {}; // Cache para almacenar prompts por página
    let currentPage = 1;
    let totalPages = 1;
    let nextUrl = null;

    // Función para parsear las funciones con múltiples separadores
    function parseFunctions(text) {
        // Primero dividir por saltos de línea
        let functions = [];

        // Dividir primero por saltos de línea
        let lines = text.split('\n');

        lines.forEach(line => {
            // Luego dividir por comas
            let commaParts = line.split(',');

            commaParts.forEach(part => {
                // Finalmente dividir por espacios
                let spaceParts = part.split(' ');

                spaceParts.forEach(func => {
                    func = func.trim();
                    if (func) {
                        functions.push(func);
                    }
                });
            });
        });

        // Eliminar duplicados y retornar
        return [...new Set(functions)];
    }

    function removeEmojis(text) {
        return text.replace(/[\u{1F000}-\u{1FAFF}\u2600-\u27BF]/gu, '');
    }

    // Función para obtener el contenido desde la API
    function getPromptContent() {
        const description = $('#prompt-description').val();
        const notes = $('#promptFinal').val();

        // Nuevo formato: array de objetos para archivos
        const files = [];

        // Recorrer todos los grupos de ruta
        $('.file-path-group').each(function() {
            let $group = $(this);
            let filePath = $group.find('.file-input').val().trim();

            // Solo incluir rutas no vacías y no deshabilitadas
            if (filePath && $group.attr('data-disabled') !== 'true') {
                const fileId = $group.data('file-id');
                let fileObj = {
                    path: filePath
                };

                // Buscar el textarea asociado
                const $functionsContainer = $(`.functions-container[data-file-id="${fileId}"]`);

                // Si el textarea está visible y tiene contenido, incluir allowed_functions
                if (!$functionsContainer.hasClass('d-none')) {
                    let functionsText = $functionsContainer.find('.allowed-functions').val();

                    if (functionsText.trim()) {
                        // Parsear las funciones considerando múltiples separadores
                        fileObj.allowed_functions = parseFunctions(functionsText);
                    }
                }

                files.push(fileObj);
            }
        });

        if (files.length == 0) {
            Swal.fire('Advertencia', 'Generando.... pero no hay rutas de archivos para procesar.', 'warning');
        }

        const data = {
            description: description,
            files: files,
            notes: notes
        };

        // Limpia el área de errores antes de la solicitud
        $('#errorContainer').empty();

        console.log('Enviando datos:', data); // Para depuración

        // Solicitud AJAX
        $.ajax({
            url: '/api/v1/prompts',
            type: 'POST',
            contentType: 'application/json',
            data: JSON.stringify(data),
            success: function(response) {
                clearValidationErrors();
                displayFileContents(description, response.data.prompts.content, files, notes);

                const id = response.data.id;
                const newUrl = `${window.location.pathname}#chat-${id}`;
                if (window.location.hash !== `#chat-${id}`) {
                    history.replaceState({
                        id: id
                    }, '', newUrl);
                }
                saveFormVersion(id, description, files, notes);
            },
            error: function(xhr, status, error) {
                let errorMessage = 'Error desconocido';

                if (xhr.responseJSON && xhr.responseJSON.error) {
                    errorMessage = xhr.responseJSON.error.message ||
                        xhr.responseJSON.error.detail ||
                        'Error en el servidor';
                }

                Swal.fire({
                    title: `Error ${xhr.status}`,
                    html: `<div style="text-align:left;">
                 <b>Mensaje:</b> ${errorMessage}<br>
                 <b>Tipo:</b> ${xhr.responseJSON?.error?.type || 'N/A'}<br>
                 <b>Código:</b> ${xhr.responseJSON?.error?.code || 'N/A'}
               </div>`,
                    icon: 'error',
                    showCancelButton: true,
                    confirmButtonText: 'Reintentar',
                    cancelButtonText: 'Cerrar', // ← Texto modificado
                    allowOutsideClick: false,
                    showCloseButton: false // Oculta la 'X' de cierre
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Solo reintentar si hace clic en el botón principal
                        getPromptContent();
                    }
                    // El cierre simple no requiere lógica adicional
                });

                clearValidationErrors();
                $('.file-input').removeClass('is-invalid');
            }
        });

    }

    // Función para mostrar errores de validación
    function showValidationErrors(errors) {
        clearValidationErrors(); // Limpiar los errores anteriores

        // Iterar sobre los errores y asignarlos a los campos
        if (errors.description) {
            $('#prompt-description').addClass('is-invalid');
            $('#prompt-description').after(`<div class="invalid-feedback">${errors.description[0].error_detail}</div>`);
        }

        if (errors.files) {
            // Aquí podrías iterar sobre las rutas de archivos si es necesario
            $('.file-input').each(function(index) {
                if (errors.files[index]) {
                    $(this).addClass('is-invalid');
                    $(this).after(`<div class="invalid-feedback">${errors.files[index].error_detail}</div>`);
                }
            });
        }

        if (errors.notes) {
            $('#promptFinal').addClass('is-invalid');
            $('#promptFinal').after(`<div class="invalid-feedback">${errors.notes[0].error_detail}</div>`);
        }
    }

    function handleServerResponse(response) {
        if (response.error) {
            // Manejar errores de ruta
            $('.file-input').each(function(index) {
                let $input = $(this);
                let value = $input.val();
                let hasError = response.error.invalidPaths.includes(value);

                // Reemplazar el input actual con uno nuevo basado en si tiene error o no
                let $newInput = $(addFilePathInput(value, hasError));
                $input.closest('.file-path-group').replaceWith($newInput);
            });
        } else {
            // Limpiar errores si la respuesta es exitosa
            clearValidationErrors();
        }
    }

    // Función para agregar un input dinámico
    function addFilePathInput(value = '', hasError = false, isDisabled = false, allowedFunctions = []) {
        if (typeof value === 'object' && value !== null) {
            value = '';
        }

        // Crear un ID único para asociar el input y su textarea
        const uniqueId = 'file-' + Math.random().toString(36).substr(2, 9);

        // Crear el wrapper para contener ambos elementos
        const $wrapper = $('<div class="file-wrapper"></div>');

        // Crear el grupo de ruta
        const $filePathGroup = $(`
            <div class="input-group mb-2 file-path-group" data-file-id="${uniqueId}" ${isDisabled ? 'data-disabled="true"' : ''}>
                <div class="input-group-text">
                    <input type="checkbox" class="form-check-input mt-0 file-path-checkbox" ${isDisabled ? 'checked' : ''}>
                </div>
                <input type="text" class="form-control file-input ${hasError ? 'is-invalid' : ''} ${isDisabled ? 'text-muted' : ''}" 
                    placeholder="Ingresa la ruta del archivo..." value="${value}" ${isDisabled ? 'disabled' : ''}>
                
                <!-- Dropdown -->
                <div class="dropdown">
                    <button class="btn btn-outline-secondary dropdown-toggle" type="button" 
                        data-bs-toggle="dropdown" aria-expanded="false">
                        <span class="visually-hidden">Opciones</span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item delete-file-path" href="#">Borrar</a></li>
                        <li><a class="dropdown-item toggle-file-status" href="#">${isDisabled ? 'Habilitar' : 'Deshabilitar'}</a></li>
                        <li><a class="dropdown-item toggle-code-reduction" href="#" data-file-id="${uniqueId}">Reducir código</a></li>
                    </ul>
                </div>
                
                ${hasError ? `<div class="invalid-feedback">La ruta '${value}' no existe</div>` : ''}
            </div>
        `);

        // Crear el contenedor de funciones
        const $functionsContainer = $(`
            <div class="functions-container mb-3 d-none" data-file-id="${uniqueId}">
                <div class="card">
                    <div class="card-header bg-light d-flex justify-content-between align-items-center">
                        <small class="fw-bold">Funciones a conservar para: ${value}</small>
                        <button type="button" class="btn-close close-functions" aria-label="Close" data-file-id="${uniqueId}"></button>
                    </div>
                    <div class="card-body p-2">
                        <textarea class="form-control allowed-functions" rows="3" 
                            placeholder="Liste funciones a conservar (separadas por línea, coma o espacio)">${Array.isArray(allowedFunctions) ? allowedFunctions.join('\n') : ''}</textarea>
                        <small class="form-text text-muted">Ingrese los nombres de las funciones a conservar</small>
                    </div>
                </div>
            </div>
        `);

        // Agregar ambos elementos al wrapper
        $wrapper.append($filePathGroup);
        $wrapper.append($functionsContainer);

        // Agregar el wrapper al contenedor principal
        $('#filePathsContainer').append($wrapper);

        return uniqueId;
    }

    function restoreDeleteButtons() {
        $('.file-path-group.has-error').each(function() {
            let $group = $(this);
            $group.removeClass('has-error');
            $group.find('.is-invalid').removeClass('is-invalid');
            $group.find('.invalid-feedback').remove();

            // Crear ID único para el nuevo dropdown
            const uniqueId = 'func-' + Math.random().toString(36).substr(2, 9);

            // Agregar el dropdown completo
            $group.append(`
                <div class="dropdown">
                    <button class="btn btn-outline-secondary dropdown-toggle" type="button" id="dropdown${uniqueId}" 
                        data-bs-toggle="dropdown" aria-expanded="false">
                        <span class="visually-hidden">Opciones</span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdown${uniqueId}">
                        <li><a class="dropdown-item delete-file-path" href="#">Borrar</a></li>
                        <li><a class="dropdown-item toggle-file-status" href="#">${$group.attr('data-disabled') === 'true' ? 'Habilitar' : 'Deshabilitar'}</a></li>
                        <li><a class="dropdown-item toggle-code-reduction" href="#">Reducir código</a></li>
                    </ul>
                </div>
            `);
        });
    }

    // Función para limpiar los errores de validación anteriores
    function clearValidationErrors() {
        $('.is-invalid').removeClass('is-invalid');
        $('.invalid-feedback').remove();

        $('.file-path-group').each(function() {
            let $group = $(this);
            let value = $group.find('.file-input').val();
            let isDisabled = $group.attr('data-disabled') === 'true';
            $group.replaceWith(addFilePathInput(value, false, isDisabled));
        });
    }

    function clearForm() {
        $('#prompt-description').val('');
        $('#promptFinal').val('');
        $('#generatedPrompt').val('');

        // Vaciar el contenedor de rutas pero mantener un solo campo vacío
        $('#filePathsContainer').html(`
            <div class="input-group mb-2 file-path-group">
                <div class="input-group-text">
                    <input type="checkbox" class="form-check-input mt-0 file-path-checkbox">
                </div>
                <input type="text" class="form-control file-input" placeholder="Ingresa la ruta del archivo...">
                <button class="btn btn-outline-secondary delete-file-path" type="button">&times;</button>
            </div>
        `);

        restoreDeleteButtons();

        localStorage.removeItem('currentForm');
    }

    // Función para gestionar el localStorage y mantener solo los N prompts más recientes
    function manageLocalStorage($max_saved = 100) {
    const prompts = [];
    for (let i = 0; i < localStorage.length; i++) {
        const key = localStorage.key(i);
        if (key.startsWith('chat-')) {
            try {
                const promptData = JSON.parse(localStorage.getItem(key));
                if (promptData && promptData.id && promptData.timestamp) {
                    prompts.push({
                        key,
                        timestamp: promptData.timestamp
                    });
                }
            } catch (e) {
                console.error('Error parsing prompt for cleanup:', e);
                // Eliminar entradas corruptas
                localStorage.removeItem(key);
            }
        }
    }

    // Ordenar por timestamp descendente (más recientes primero)
    prompts.sort((a, b) => new Date(b.timestamp) - new Date(a.timestamp));

    // Eliminar los prompts más antiguos si hay más de 20
    while (prompts.length > 100) {
        const oldestPrompt = prompts.pop();
        localStorage.removeItem(oldestPrompt.key);
        console.log(`Prompt antiguo eliminado: ${oldestPrompt.key}`);
    }
}

    // Función actualizada para guardar prompts
    function saveFormVersion(id, description, files, notes) {
        // Gestionar el localStorage antes de guardar
        manageLocalStorage();

        const formVersion = {
            id: id,
            description: description,
            files: files,
            notes: notes,
            timestamp: new Date().toISOString() // Incluir timestamp
        };

        try {
            localStorage.setItem(`chat-${id}`, JSON.stringify(formVersion));
        } catch (e) {
            if (e.name === 'QuotaExceededError') {
                console.error('QuotaExceededError al guardar. Eliminando el más antiguo...');
                const prompts = getSortedPrompts();
                if (prompts.length > 0) {
                    const oldestPrompt = prompts[prompts.length - 1];
                    localStorage.removeItem(oldestPrompt.key);
                    localStorage.setItem(`chat-${id}`, JSON.stringify(formVersion));
                }
            } else {
                throw e;
            }
        }
    }

    // Función auxiliar para obtener prompts ordenados
    function getSortedPrompts() {
        const prompts = [];
        for (let i = 0; i < localStorage.length; i++) {
            const key = localStorage.key(i);
            if (key.startsWith('chat-')) {
                try {
                    const promptData = JSON.parse(localStorage.getItem(key));
                    if (promptData && promptData.id && promptData.timestamp) {
                        prompts.push({
                            key,
                            timestamp: promptData.timestamp
                        });
                    }
                } catch (e) {
                    console.error('Error al parsear el prompt:', e);
                }
            }
        }
        return prompts.sort((a, b) => new Date(b.timestamp) - new Date(a.timestamp));
    }

    function saveFormToLocalStorage() {
        const formState = {
            description: $('#prompt-description').val(),
            files: $('.file-input').map((_, input) => {
                const $input = $(input);
                const $group = $input.closest('.file-path-group');
                return {
                    path: $input.val(),
                    disabled: $group.attr('data-disabled') === 'true'
                };
            }).get(),
            notes: $('#promptFinal').val()
        };
        localStorage.setItem('currentForm', JSON.stringify(formState));
    }

    function loadFormStateFromLocalStorage() {
        const currentHash = window.location.hash;
        let savedForm;

        if (currentHash.startsWith('#chat-')) {
            const formId = currentHash.split('-')[1];
            savedForm = JSON.parse(localStorage.getItem(`chat-${formId}`));
        } else {
            savedForm = JSON.parse(localStorage.getItem('currentForm'));
        }

        if (savedForm) {
            $('#prompt-description').val(savedForm.description || '');
            $('#promptFinal').val(savedForm.notes || '');
            $('#filePathsContainer').empty(); // Limpiar contenedor existente

            if (Array.isArray(savedForm.files)) {
                savedForm.files.forEach(file => {
                    let filePath, isDisabled, allowedFunctions;

                    if (typeof file === 'string') {
                        // Compatibilidad con formato antiguo
                        filePath = file;
                        isDisabled = false;
                        allowedFunctions = null;
                    } else {
                        filePath = file.path;
                        isDisabled = file.disabled || false;
                        allowedFunctions = file.allowed_functions || null;
                    }

                    if (filePath) {
                        // Añadir input y mostrar textarea si tiene funciones
                        const fileId = addFilePathInput(filePath, false, isDisabled, allowedFunctions);

                        // Si hay funciones, mostrar el textarea
                        if (allowedFunctions && allowedFunctions.length > 0) {
                            setTimeout(() => {
                                $(`.functions-container[data-file-id="${fileId}"]`).removeClass('d-none');
                            }, 100);
                        }
                    }
                });
            }

            // Asegurar al menos un input
            if ($('.file-path-group').length === 0) {
                addFilePathInput();
            }
        } else if ($('.file-path-group').length === 0) {
            addFilePathInput();
        }
    }

    /*
        Mostrar resultado en #generatedPrompt
    */
    function displayFileContents(description, contents, files, notes) {
        let generatedPrompt = '';

        // 1. Normalizar parámetros
        const safeDescription = typeof description === 'string' ? description : '';
        const safeNotes = typeof notes === 'string' ? notes : '';

        // 2. Agregar introducción validada
        if (safeDescription.trim() !== '') {
            generatedPrompt += `${safeDescription}\n\n`;
        }

        // 3. Validar estructura de contents
        if (!contents || typeof contents !== 'object') {
            console.error('Contenido inválido:', contents);
            Swal.fire('Error', 'Formato de archivos inválido', 'error');
            return;
        }

        // 4. Iterar sobre archivos
        Object.entries(contents).forEach(([filePath, fileContent]) => {
            if (typeof fileContent !== 'string') {
                console.warn(`Contenido no es texto en: ${filePath}`);
                return;
            }

            // Formatear bloque de código
            const extension = filePath.split('.').pop().toLowerCase();
            const language = {
                'php': 'php',
                'js': 'javascript',
                // ... otros mapeos
            } [extension] || '';

            generatedPrompt += `/* Ruta: ${filePath} */\n\`\`\`${language}\n${fileContent}\n\`\`\`\n\n`;
        });

        // 5. Agregar notas finales validadas
        if (safeNotes.trim() !== '') {
            generatedPrompt += `// Notas finales\n${safeNotes}\n`;
        }

        // 6. Actualizar textarea
        $('#generatedPrompt').val(generatedPrompt);
    }

    function formatFileContent(filePath, content) {
        const extension = filePath.split('.').pop().toLowerCase();
        const languageMap = {
            'php': 'php',
            'js': 'javascript',
            'css': 'css',
            'json': 'json',
        };

        const language = languageMap[extension] || '';
        return `\n/* Ruta: ${filePath} */\n\`\`\`${language}\n${content}\n\`\`\`\n`;
    }

    function handleHashChange() {
        const hash = window.location.hash;
        if (hash.startsWith('#chat-')) {
            const formId = hash.split('-')[1];
            const savedForm = JSON.parse(localStorage.getItem(`chat-${formId}`));
            if (savedForm) {
                loadFormStateFromLocalStorage();
            } else {
                // Si no existe en localStorage, limpiar el formulario
                clearForm();
            }
        } else {
            // Si no hay hash, cargar estado actual desde localStorage general
            loadFormStateFromLocalStorage();
        }
    }

    // Función para alternar pantalla completa
    function toggleFullscreen(element) {
        if (!document.fullscreenElement) {
            if (element.requestFullscreen) {
                element.requestFullscreen();
            } else if (element.mozRequestFullScreen) { // Firefox
                element.mozRequestFullScreen();
            } else if (element.webkitRequestFullscreen) { // Chrome, Safari and Opera
                element.webkitRequestFullscreen();
            } else if (element.msRequestFullscreen) { // IE/Edge
                element.msRequestFullscreen();
            }
        } else {
            if (document.exitFullscreen) {
                document.exitFullscreen();
            } else if (document.mozCancelFullScreen) { // Firefox
                document.mozCancelFullScreen();
            } else if (document.webkitExitFullscreen) { // Chrome, Safari and Opera
                document.webkitExitFullscreen();
            } else if (document.msExitFullscreen) { // IE/Edge
                document.msExitFullscreen();
            }
        }
    }

    function togglePathStatus(enable) {
        $('.file-path-checkbox:checked').each(function() {
            let $group = $(this).closest('.file-path-group');
            let $input = $group.find('.file-input');
            $group.attr('data-disabled', !enable);
            $input.prop('disabled', !enable);
            if (enable) {
                $group.removeClass('disabled');
                $input.removeClass('text-muted');
            } else {
                $group.addClass('disabled');
                $input.addClass('text-muted');
            }
        });
        updateBulkActionOptions();
    }

    function updateBulkActionOptions() {
        let $selectedPaths = $('.file-path-checkbox:checked');
        let allEnabled = $selectedPaths.closest('.file-path-group').not('.disabled').length === $selectedPaths.length;
        let allDisabled = $selectedPaths.closest('.file-path-group.disabled').length === $selectedPaths.length;

        $('#enableSelectedPaths').toggle(!allEnabled);
        $('#disableSelectedPaths').toggle(!allDisabled);
    }

    $('#enableSelectedPaths').click(function(e) {
        e.preventDefault();
        togglePathStatus(true);
    });

    $('#disableSelectedPaths').click(function(e) {
        e.preventDefault();
        togglePathStatus(false);
    });

    // Llamar a esta función cuando se seleccionen/deseleccionen rutas
    $(document).on('change', '.file-path-checkbox', updateBulkActionOptions);

    // Manejar el evento de carga de la página
    window.onload = function() {
        // Si la página carga con un hash en la URL
        if (window.location.hash) {
            handleHashChange();
        }
    };

    // Manejar el evento 'hashchange' para detectar cambios en el hash de la URL
    window.addEventListener('hashchange', handleHashChange);

    window.addEventListener('popstate', function(event) {
        if (event.state && event.state.id) {
            loadFormStateFromLocalStorage();
        }
    });

    // Manejar el estado del historial para cuando se navega hacia atrás o adelante
    window.onpopstate = function() {
        handleHashChange();
    };


    $(document).ready(function() {
        // Inicializar eventos
        initializeEvents();
        addFilePathInput(); // que haya al menos una ruta

        // Cargar estado inicial basado en hash si existe
        handleHashChange();

        // Inicializar API solo si no hay hash
        if (!window.location.hash) {
            initialize();
        }

        // Función para inicializar los eventos
        function initializeEvents() {
            $('#addFilePath').click(function(e) {
                e.preventDefault();
                addFilePathInput();
            });

            $('#generatePrompt').click(function(e) {
                e.preventDefault();
                generatePrompt();
            });

            $('#deleteSelectedPaths').click(function(e) {
                e.preventDefault();
                e.stopPropagation();
                deleteSelectedPaths();
            });

            $('#deleteSelectedPaths').click(function(e) {
                e.preventDefault();
                e.stopPropagation();
                deleteSelectedPaths();
            });

            $('#enableSelectedPaths').click(function(e) {
                e.preventDefault();
                e.stopPropagation();
                togglePathStatus(true);
            });

            // Función para copiar al portapapeles
            $('#copyPrompt').click(function(e) {
                e.preventDefault();
                copyToClipboard();
            });

            $('#clearFormButton').click(clearForm);

            // Función para mostrar en pantalla completa
            $('#fullscreenPrompt').click(function(e) {
                e.preventDefault();
                toggleFullscreen($('#generatedPrompt')[0]);
            });

            // Asignar eventos a los botones del dropdown
            $('#executeWithChatGPT').click(function(e) {
                e.preventDefault();
                executeWithChatGPT();
            });

            $('#executeWithClaude').click(function(e) {
                e.preventDefault();
                executeWithClaude();
            });

            // Registrar el evento para generar el prompt
            $('#generate-prompt').on('click', function(e) {
                e.preventDefault();
                getPromptContent();
            });

            // Eventos para los botones de eliminar en inputs dinámicos
            $(document).on('click', '.delete-file-path', function() {
                let $filePathGroup = $(this).closest('.file-path-group');
                deleteFilePath($filePathGroup);
            });

            // Agregar el evento para extraer rutas del PROMPT
            $('#extractPathsButton').click(function(e) {
                e.preventDefault();
                extractPathsFromPrompt({
                    absolute: true,
                    system: "WINDOWS-ONLY"
                });
            });

            // Agregar el evento para el botón nuevo
            $('#newFormButton').click(function(e) {
                e.preventDefault();
                newForm();
            });

            $('form').on('submit', function(e) {
                e.preventDefault();
            });
        }

        // Función para cargar prompts de una página específica
        function loadPromptsPage(page) {
            // Si ya tenemos esta página en caché, usar esos datos
            if (cachedPrompts[page]) {
                return Promise.resolve(cachedPrompts[page]);
            }

            // Si no está en caché, hacer el request
            return $.ajax({
                url: `/api/v1/prompts?page=${page}`,
                type: 'GET'
            }).then(function(response) {
                // Guardar en caché
                cachedPrompts[page] = response.data;
                totalPages = response.paginator.totalPages;
                nextUrl = response.paginator.nextUrl;
                return response.data;
            });
        }

        // Función para cargar los datos de un prompt en el formulario
        function loadPromptData(prompt) {
            if (!prompt) return;

            // Cargar datos en el formulario
            $('#prompt-description').val(prompt.description || '');
            $('#promptFinal').val(prompt.notes || '');

            // Limpiar y cargar rutas de archivos
            $('#filePathsContainer').empty();
            if (prompt.files) {
                try {
                    const files = JSON.parse(prompt.files);
                    files.forEach(file => addFilePathInput(file));
                } catch (e) {
                    console.error('Error parsing files:', e);
                }
            }

            // Actualizar URL
            const baseUrl = window.location.href.split('#')[0];
            history.pushState({
                    id: prompt.id
                },
                '',
                `${baseUrl}#chat-${prompt.id}`
            );
        }

        // Función para obtener el prompt actual
        function getCurrentPrompt() {
            if (!cachedPrompts[currentPage]) return null;
            return cachedPrompts[currentPage].prompts[currentPromptIndex];
        }

        // Manejadores de navegación
        $('#prevPrompt').click(function() {
            if (currentPromptIndex > 0) {
                currentPromptIndex--;
                loadPromptData(getCurrentPrompt());
            } else if (currentPage > 1) {
                currentPage--;
                loadPromptsPage(currentPage).then(data => {
                    currentPromptIndex = data.prompts.length - 1;
                    loadPromptData(getCurrentPrompt());
                });
            }
        });


        $('#nextPrompt').click(function() {
            const currentPageData = cachedPrompts[currentPage];
            if (!currentPageData) return;

            if (currentPromptIndex < currentPageData.prompts.length - 1) {
                currentPromptIndex++;
                loadPromptData(getCurrentPrompt());
            } else if (currentPage < totalPages) {
                currentPage++;
                loadPromptsPage(currentPage).then(data => {
                    currentPromptIndex = 0;
                    loadPromptData(getCurrentPrompt());
                });
            }
        });

        // Evento para borrar archivo
        $(document).on('click', '.delete-file-path', function(e) {
            e.preventDefault();
            let $filePathGroup = $(this).closest('.file-path-group');
            deleteFilePath($filePathGroup);
        });

        // Evento para habilitar/deshabilitar una ruta
        $(document).on('click', '.toggle-file-status', function(e) {
            e.preventDefault();
            let $group = $(this).closest('.file-path-group');
            let $input = $group.find('.file-input');
            let isDisabled = $group.attr('data-disabled') === 'true';

            // Cambiar estado
            $group.attr('data-disabled', !isDisabled);
            $input.prop('disabled', !isDisabled);

            if (isDisabled) {
                // Habilitar
                $group.removeClass('disabled');
                $input.removeClass('text-muted');
                $(this).text('Deshabilitar');
            } else {
                // Deshabilitar
                $group.addClass('disabled');
                $input.addClass('text-muted');
                $(this).text('Habilitar');
            }
        });

        // Evento para cerrar el textarea de funciones
        $(document).on('click', '.close-functions', function(e) {
            e.preventDefault();
            const fileId = $(this).data('file-id');
            $(`.functions-container[data-file-id="${fileId}"]`).addClass('d-none');
        });

        // Evento para mostrar/ocultar el textarea de funciones
        $(document).on('click', '.toggle-code-reduction', function(e) {
            e.preventDefault();
            const fileId = $(this).data('file-id');
            const $container = $(`.functions-container[data-file-id="${fileId}"]`);
            $container.toggleClass('d-none');

            // Actualizar el nombre del archivo en la cabecera
            if (!$container.hasClass('d-none')) {
                const filePath = $(this).closest('.file-path-group').find('.file-input').val();
                $container.find('.card-header small').text('Funciones a conservar para: ' + filePath);
            }
        });

        // Inicialización al cargar la página
        function initialize() {
            // Solo cargar desde API si no hay hash en la URL
            if (!window.location.hash) {
                loadPromptsPage(1).then(data => {
                    if (data.prompts.length > 0) {
                        currentPromptIndex = 0;
                        loadPromptData(getCurrentPrompt());
                    }
                });
            }
        }

        // Llamar a la inicialización cuando el documento esté listo
        initialize();

        // Función para eliminar rutas seleccionadas
        function deleteSelectedPaths() {
            let $selectedPaths = $('.file-path-checkbox:checked').closest('.file-path-group').not('.disabled');

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
            let intro = $('#prompt-description').val();
            let finalNotes = $('#promptFinal').val();

            // Obtener las rutas de los inputs dinámicos
            let filePaths = $('.file-input').map(function() {
                let $input = $(this);
                let $group = $input.closest('.file-path-group');
                return {
                    path: $input.val().trim(),
                    disabled: $group.attr('data-disabled') === 'true'
                };
            }).get().filter(file => file.path !== '');

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

        // Función para copiar el prompt generado al portapapeles sin seleccionar el texto
        function copyToClipboard() {
            let generatedPrompt = $('#generatedPrompt').val();

            // Verificar si la API del portapapeles está disponible
            if (navigator.clipboard && navigator.clipboard.writeText) {
                // Usamos el API del portapapeles si está disponible
                navigator.clipboard.writeText(generatedPrompt).then(function() {
                    // Si se copia correctamente, mostramos un mensaje de éxito
                    Swal.fire('Éxito', 'El prompt ha sido copiado al portapapeles', 'success');
                }).catch(function(error) {
                    // Si ocurre algún error, mostramos un mensaje de error
                    Swal.fire('Error', 'Hubo un problema al copiar el prompt', 'error');
                    console.error('Error al copiar al portapapeles:', error);
                });
            } else {
                // Fallback manual si la API del portapapeles no está disponible
                let tempTextArea = document.createElement("textarea");
                tempTextArea.value = generatedPrompt;
                document.body.appendChild(tempTextArea);

                // Seleccionamos el contenido del textarea temporal y lo copiamos
                tempTextArea.select();
                try {
                    document.execCommand("copy"); // Copiamos el texto seleccionado
                    Swal.fire('Éxito', 'El prompt ha sido copiado al portapapeles', 'success');
                } catch (error) {
                    Swal.fire('Error', 'Hubo un problema al copiar el prompt', 'error');
                    console.error('Error al copiar manualmente:', error);
                }

                // Limpiar el textarea temporal
                document.body.removeChild(tempTextArea);
            }
        }

        // Agregar función para el nuevo botón
        function newForm() {
            // Limpiar el formulario
            clearForm();

            // Remover el hash de la URL sin recargar la página
            history.pushState('', document.title, window.location.pathname);
        }

        function extractPathsFromPrompt(options = {}) {
            const promptText = $('#prompt-description').val();

            const absolute = options.absolute ?? null;
            const system = options.system ?? "ALL";

            // Expresión regular para detectar rutas absolutas en Windows y Unix (en bruto)
            const pathRegex = /(?:[a-zA-Z]:\\[^:\n]+)|(?:\/[^\n:]+)/g;
            const rawPaths = promptText.match(pathRegex) || [];

            const cleanedPaths = rawPaths.map(raw => {
                let ret = raw
                    .replace(/^\s*(\/\*|\*\/)/, '') // elimina /* o */ al inicio
                    .replace(/(\/\*|\*\/)\s*$/, '') // elimina /* o */ al final
                    .replace(/\*\//g, '') // elimina cualquier "*/" intermedio
                    .replace(/\/\*/g, '') // elimina cualquier "/*" intermedio
                    .trim();

                return ret;
            });

            const validPaths = cleanedPaths.filter(path => {
                // Debe contener punto para suponer que es un archivo (no carpeta)
                if (!path || !path.includes('.') || path.endsWith('.')) return false;

                // Si se pide ruta absoluta, validar según el sistema
                if (absolute === true) {
                    if (system === "WINDOWS-ONLY") {
                        // Debe comenzar con letra y ":\" (como C:\ o D:\)
                        return /^[c-zC-Z]:\\/.test(path);
                    } else if (system === "UNIX-ONLY") {
                        // Debe comenzar con /home/ o /var/www/
                        return path.startsWith('/home/') || path.startsWith('/var/www/');
                    } else {
                        // ALL: Acepta ambos formatos válidos
                        return /^[c-zC-Z]:\\/.test(path) || path.startsWith('/');
                    }
                }

                // Si no se requiere absoluta, acepta cualquier ruta con extensión
                return true;
            });

            if (validPaths.length === 0) {
                Swal.fire('Información', 'No se encontraron rutas válidas en el texto del PROMPT', 'info');
                return;
            }

            let addedCount = 0;

            validPaths.forEach(path => {
                const exists = $('.file-input').filter(function() {
                    return $(this).val() === path;
                }).length > 0;

                if (!exists) {
                    addFilePathInput(path);
                    addedCount++;
                }
            });

            Swal.fire({
                title: 'Éxito',
                text: `Se agregaron ${addedCount} ruta(s) desde el PROMPT`,
                icon: 'success',
                timer: 2000,
                showConfirmButton: false
            });
        }



        // Función para actualizar el texto del botón con la opción seleccionada
        function updateExecuteButtonText(optionText) {
            $('#executeButton').text('Ejecutar con ' + optionText);
        }

        // Función para ejecutar con ChatGPT
        function executeWithChatGPT() {
            let generatedPrompt = $('#generatedPrompt').val();
            console.log("Ejecutar con ChatGPT:", generatedPrompt);
            updateExecuteButtonText("ChatGPT");
            // Aquí puedes añadir la lógica para enviar el prompt a ChatGPT
        }

        // Función para ejecutar con Claude
        function executeWithClaude() {
            let generatedPrompt = $('#generatedPrompt').val();
            console.log("Ejecutar con Claude:", generatedPrompt);
            updateExecuteButtonText("Claude");
            // Aquí puedes añadir la lógica para enviar el prompt a Claude
        }


        // Función para cargar el formulario basado en el ID
        function loadFormFromLocalStorage(id) {
            // Recuperar el contenido del formulario del localStorage
            const formContent = localStorage.getItem(id);

            if (formContent) {
                // Mostrar el formulario en el contenedor
                document.getElementById('form-container').innerHTML = formContent;
            } else {
                // Si no hay formulario en localStorage para ese ID
                document.getElementById('form-container').innerHTML = `<p>No form found for ID: ${id}</p>`;
            }
        }

    });
</script>