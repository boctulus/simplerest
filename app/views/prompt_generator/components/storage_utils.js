// Functions for handling local storage operations

function getSavedPromptsImpl() {
    let prompts = [];
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

    // Si no hay prompts en localStorage, cargar desde el backend
    if (prompts.length === 0) {
        $.ajax({
            url: '/api/v1/prompts?page=1',
            type: 'GET',
            success: function(response) {
                if (response.data && response.data.prompts) {
                    response.data.prompts.forEach(prompt => {
                        const formState = {
                            id: prompt.id,
                            description: prompt.description || '',
                            notes: prompt.notes || '',
                            filePaths: prompt.files ? prompt.files.map(file => ({
                                path: file.path || '',
                                disabled: file.disabled || false,
                                allowedFunctions: file.allowedFunctions || ''
                            })) : [],
                            timestamp: prompt.timestamp || new Date().toISOString()
                        };
                        localStorage.setItem(`chat-${prompt.id}`, JSON.stringify(formState));
                        prompts.push(formState);
                    });
                }
            },
            error: function(xhr) {
                console.error('Error loading prompts from backend:', xhr);
            },
            async: false // Para mantener simplicidad, aunque no es ideal
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
    context.loading = true;
    setTimeout(() => {
        const currentHash = window.location.hash;
        let savedForm;

        console.log(currentHash, 'currentHash'); //

        if (currentHash.startsWith('#chat-')) {
            const formId = currentHash.split('-')[1];
            console.log(formId, 'formId'); //

            savedForm = JSON.parse(localStorage.getItem(`chat-${formId}`));
        } else {
            savedForm = JSON.parse(localStorage.getItem('currentForm'));
        }

        console.log(savedForm); //

        if (savedForm) {
            context.description = savedForm.description || '';
            context.notes = savedForm.notes || '';
            context.filePaths = savedForm.filePaths.map((file, index) => ({
                id: 'file-' + Date.now() + index, // Generar ID único
                path: file.path || '',
                disabled: file.disabled || false,
                allowedFunctions: file.allowedFunctions || '',
                showFunctions: false,
                showDropdown: false,
                selected: false
            }));
            if (context.filePaths.length === 0) {
                context.addFilePath();
            }
        }
        context.loading = false;
    }, 300);
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