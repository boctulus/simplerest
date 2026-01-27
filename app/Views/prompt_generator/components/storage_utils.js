// Functions for handling local storage operations

function getSavedPromptsImpl() {
    let prompts = [];
    
    // Cargar prompts desde localStorage
    for (let i = 0; i < localStorage.length; i++) {
        const key = localStorage.key(i);
        if (key.startsWith('chat-')) {
            try {
                const promptData = JSON.parse(localStorage.getItem(key));
                if (promptData && promptData.id) {
                    prompts.push(promptData);
                }
            } catch (e) {
                console.error('Error parsing saved prompt:', e);
            }
        }
    }

    // Si hay pocos prompts en localStorage, cargar más desde API
    if (prompts.length < 10) {
        $.ajax({
            url: '/api/v1/prompts?page=1',
            type: 'GET',
            success: function(response) {
                if (response.data && response.data.prompts) {
                    response.data.prompts.forEach(prompt => {
                        // Solo agregar si no existe ya en localStorage
                        const existsInLocal = prompts.some(p => p.id == prompt.id);
                        if (!existsInLocal) {
                            const formState = {
                                id: prompt.id,
                                description: prompt.description || '',
                                notes: prompt.notes || '',
                                filePaths: JSON.parse(prompt.files || '[]').map(file => ({
                                    path: file.path || '',
                                    disabled: false,
                                    allowedFunctions: ''
                                })),
                                timestamp: prompt.created_at || new Date().toISOString()
                            };
                            
                            try {
                                localStorage.setItem(`chat-${prompt.id}`, JSON.stringify(formState));
                                prompts.push(formState);
                            } catch (e) {
                                console.warn('No se pudo guardar prompt en localStorage:', e);
                            }
                        }
                    });
                }
            },
            error: function(xhr) {
                console.error('Error loading prompts from backend:', xhr);
            },
            async: false
        });
    }

    return prompts.sort((a, b) => new Date(b.timestamp || 0) - new Date(a.timestamp || 0));
}

function saveFormToLocalStorageImpl(context) {
    const formState = {
        description: context.description,
        notes: context.notes,
        filePaths: context.filePaths.map(file => ({
            path: file.path,
            disabled: file.disabled,
            allowedFunctions: file.allowedFunctions
        }))
    };

    localStorage.setItem('currentForm', JSON.stringify(formState));

    // Si hay un ID en el hash, también guardar con ese ID
    const currentHash = window.location.hash;
    if (currentHash.startsWith('#chat-')) {
        const formId = currentHash.split('-')[1];
        localStorage.setItem(`chat-${formId}`, JSON.stringify({
            ...formState,
            id: formId,
            timestamp: new Date().toISOString()
        }));
    }
}

function loadFromHashImpl(context) {
    const currentHash = window.location.hash;
    console.log('Hash detectado en la URL:', currentHash);

    if (currentHash.startsWith('#chat-')) {
        const formId = currentHash.split('-')[1];
        console.log('ID extraído del hash:', formId);

        // BYPASS COMPLETO DE LOCALSTORAGE - IR DIRECTO A LA API
        console.log('Consultando API directamente (bypass localStorage)...');
        context.loading = true;

        $.ajax({
            url: `/api/v1/prompts/${formId}`,
            type: 'GET',
            cache: false, // Asegurar que no se use caché HTTP
            success: (response) => {
                console.log('Respuesta de la API:', response);

                const prompt = response.data;
                if (prompt && prompt.id == formId) {
                    console.log('Prompt encontrado en API:', prompt);
                    
                    // Cargar datos en el contexto
                    context.description = prompt.description || '';
                    context.notes = prompt.notes || '';
                    
                    let parsedFiles = [];
                    try {
                        parsedFiles = JSON.parse(prompt.files || '[]');
                    } catch (e) {
                        console.error('Error parsing files from API:', e);
                        parsedFiles = [];
                    }
                    
                    context.filePaths = parsedFiles.map((file, idx) => ({
                        id: `file-${Date.now()}-${idx}`,
                        path: file.path || file || '', // Manejar tanto objetos como strings
                        disabled: false,
                        allowedFunctions: file.allowed_functions ? file.allowed_functions.join('\n') : '',
                        showFunctions: false,
                        showDropdown: false,
                        selected: false
                    }));

                    // Asegurar que haya al menos una ruta
                    if (context.filePaths.length === 0) {
                        context.filePaths.push({
                            id: `file-${Date.now()}`,
                            path: '',
                            disabled: false,
                            allowedFunctions: '',
                            showFunctions: false,
                            showDropdown: false,
                            selected: false
                        });
                    }

                    console.log('Datos cargados exitosamente desde API');
                } else {
                    console.error('Prompt no encontrado en la API para ID:', formId);
                    Swal.fire('Error', 'El prompt solicitado no se encontró', 'error');
                    
                    // Limpiar formulario si no se encuentra
                    context.description = '';
                    context.notes = '';
                    context.filePaths = [{
                        id: `file-${Date.now()}`,
                        path: '',
                        disabled: false,
                        allowedFunctions: '',
                        showFunctions: false,
                        showDropdown: false,
                        selected: false
                    }];
                }
                context.loading = false;
            },
            error: (xhr, status, error) => {
                console.error('Error al consultar la API:', status, error);
                
                let errorMessage = 'No se pudo cargar el prompt';
                if (xhr.status === 404) {
                    errorMessage = 'El prompt no existe';
                } else if (xhr.status >= 500) {
                    errorMessage = 'Error del servidor';
                }
                
                Swal.fire('Error', `${errorMessage} (ID: ${formId})`, 'error');
                
                // Limpiar formulario en caso de error
                context.description = '';
                context.notes = '';
                context.filePaths = [{
                    id: `file-${Date.now()}`,
                    path: '',
                    disabled: false,
                    allowedFunctions: '',
                    showFunctions: false,
                    showDropdown: false,
                    selected: false
                }];
                
                context.loading = false;
            }
        });
    } else {
        // Si no hay hash, inicializar formulario vacío
        console.log('No hay hash, inicializando formulario vacío');
        context.description = '';
        context.notes = '';
        context.filePaths = [{
            id: `file-${Date.now()}`,
            path: '',
            disabled: false,
            allowedFunctions: '',
            showFunctions: false,
            showDropdown: false,
            selected: false
        }];
        context.loading = false;
    }
}

function saveAsNewPromptImpl(context) {
    // Mostrar spinner
    context.loading = true;

    setTimeout(() => {
        const formId = Date.now().toString(36) + Math.random().toString(36).substring(2);

        const formState = {
            id: formId,
            description: context.description,
            notes: context.notes,
            filePaths: context.filePaths.map(file => ({
                path: file.path,
                disabled: file.disabled,
                allowedFunctions: file.allowedFunctions
            })),
            timestamp: new Date().toISOString()
        };

        localStorage.setItem(`chat-${formId}`, JSON.stringify(formState));
        window.location.hash = `#chat-${formId}`;

        // Ocultar spinner
        context.loading = false;

        toastr.success('El prompt ha sido guardado con un nuevo ID');
    }, 700);
}

function clearFormWithoutConfirmationImpl(context) {
    context.description = '';
    context.notes = '';
    context.generatedPrompt = '';
    context.filePaths = [];
    context.addFilePath();
    localStorage.removeItem('currentForm');
    history.pushState('', document.title, window.location.pathname);
}