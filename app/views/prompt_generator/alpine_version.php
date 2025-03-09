<div class="bg-gray-100 text-gray-800">
    <div x-data="promptGenerator()" class="container mx-auto p-4 max-w-5xl" x-init="init()">
        <!-- Encabezado con navegación -->
        <div class="flex items-center mb-4">
            <h1 class="text-3xl font-bold">Generador de Prompt</h1>
            <div class="ml-auto">
                <button @click="prevPrompt" class="bg-blue-600 text-white px-3 py-1 rounded mr-1">&lt;</button>
                <button @click="nextPrompt" class="bg-blue-600 text-white px-3 py-1 rounded">&gt;</button>
            </div>
        </div>

        <!-- Sección de introducción -->
        <div class="mb-6">
            <label for="prompt-description" class="block mb-2 font-medium">PROMPT Introducción</label>
            <textarea id="prompt-description" x-model="description"
                class="w-full p-2 border rounded mb-2 h-24"
                placeholder="Escribe el texto de introducción..."></textarea>
            <div class="flex justify-between">
                <button @click="newForm" class="bg-green-600 text-white px-4 py-2 rounded">Nuevo</button>
                <div>
                    <button @click="extractPathsFromPrompt" class="bg-blue-600 text-white px-4 py-2 rounded mr-2">Agregar rutas en PROMPT</button>
                    <button @click="clearForm" class="bg-red-600 text-white px-4 py-2 rounded">Borrar Form</button>
                </div>
            </div>
        </div>

        <!-- Sección de rutas de archivos -->
        <div class="mb-6">
            <label class="block mb-2 font-medium">RUTAS A ARCHIVOS A INCLUIR</label>
            <div id="filePathsContainer">
                <template x-for="(file, index) in filePaths" :key="file.id">
                    <div class="mb-4">
                        <!-- Grupo de ruta de archivo -->
                        <div class="flex items-center mb-1" :class="{'opacity-60': file.disabled}">
                            <div class="mr-2">
                                <input type="checkbox"
                                    class="w-5 h-5"
                                    :id="'chk-' + file.id"
                                    x-model="file.selected">
                            </div>
                            <input type="text"
                                x-model="file.path"
                                :disabled="file.disabled"
                                class="flex-1 p-2 border rounded"
                                placeholder="Ingresa la ruta del archivo...">
                            <div class="relative ml-2">
                                <button @click="toggleDropdown(file.id)" class="bg-gray-200 p-2 rounded">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                        <path d="M6 10a2 2 0 11-4 0 2 2 0 014 0zM12 10a2 2 0 11-4 0 2 2 0 014 0zM16 12a2 2 0 100-4 2 2 0 000 4z" />
                                    </svg>
                                </button>
                                <div x-show="file.showDropdown"
                                    @click.away="file.showDropdown = false"
                                    class="absolute right-0 mt-1 w-48 bg-white rounded shadow-lg z-10">
                                    <a href="#" @click.prevent="deleteFilePath(index)" class="block px-4 py-2 hover:bg-gray-100">Borrar</a>
                                    <a href="#" @click.prevent="toggleFileStatus(index)" class="block px-4 py-2 hover:bg-gray-100" x-text="file.disabled ? 'Habilitar' : 'Deshabilitar'"></a>
                                    <a href="#" @click.prevent="toggleFunctions(index)" class="block px-4 py-2 hover:bg-gray-100">Reducir código</a>
                                </div>
                            </div>
                        </div>

                        <!-- Panel de funciones a conservar -->
                        <div x-show="file.showFunctions" class="border rounded mb-4 bg-white">
                            <div class="flex justify-between items-center bg-gray-100 px-3 py-2 border-b">
                                <span class="text-sm font-semibold" x-text="'Funciones a conservar para: ' + file.path"></span>
                                <button @click="file.showFunctions = false" class="text-gray-500 hover:text-gray-700">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                                    </svg>
                                </button>
                            </div>
                            <div class="p-3">
                                <textarea
                                    x-model="file.allowedFunctions"
                                    class="w-full p-2 border rounded"
                                    rows="3"
                                    placeholder="Liste funciones a conservar (separadas por línea, coma o espacio)"></textarea>
                                <small class="text-gray-500">Ingrese los nombres de las funciones a conservar</small>
                            </div>
                        </div>
                    </div>
                </template>
            </div>

            <div class="flex justify-between">
                <button @click="addFilePath" class="bg-green-600 text-white px-4 py-2 rounded">Agregar ruta</button>

                <div class="relative">
                    <button @click="toggleBulkAction" class="bg-gray-600 text-white px-4 py-2 rounded">
                        Acción masiva
                    </button>
                    <div x-show="showBulkActions" @click.away="showBulkActions = false" class="absolute right-0 mt-1 w-48 bg-white rounded shadow-lg z-10">
                        <a href="#" @click.prevent="deleteSelectedPaths" class="block px-4 py-2 hover:bg-gray-100">Borrar rutas</a>
                        <a href="#" @click.prevent="toggleSelectedPathsStatus(true)" class="block px-4 py-2 hover:bg-gray-100">Habilitar</a>
                        <a href="#" @click.prevent="toggleSelectedPathsStatus(false)" class="block px-4 py-2 hover:bg-gray-100">Deshabilitar</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sección de notas finales -->
        <div class="mb-6">
            <label for="promptFinal" class="block mb-2 font-medium">PROMPT (Notas finales)</label>
            <textarea id="promptFinal" x-model="notes" class="w-full p-2 border rounded h-24" placeholder="Escribe las notas finales..."></textarea>
        </div>

        <!-- Botones para generar y ejecutar -->
        <div class="flex justify-between mb-6">
            <button @click="generatePrompt" class="bg-blue-600 text-white px-4 py-2 rounded">Generar Prompt</button>

            <div class="relative">
                <button @click="toggleExecuteOptions" class="bg-yellow-600 text-white px-4 py-2 rounded" x-text="executeWith ? 'Ejecutar con ' + executeWith : 'Ejecutar con'"></button>
                <div x-show="showExecuteOptions" @click.away="showExecuteOptions = false" class="absolute right-0 mt-1 w-48 bg-white rounded shadow-lg z-10">
                    <a href="#" @click.prevent="executeWithOption('ChatGPT')" class="block px-4 py-2 hover:bg-gray-100">ChatGPT</a>
                    <a href="#" @click.prevent="executeWithOption('Claude')" class="block px-4 py-2 hover:bg-gray-100">Claude</a>
                </div>
            </div>
        </div>

        <!-- Sección del prompt generado -->
        <div class="mb-6">
            <div class="flex justify-between items-center mb-2">
                <label for="generatedPrompt" class="font-medium">PROMPT GENERADO</label>
                <div class="relative">
                    <button @click="togglePromptOptions" class="border bg-gray-100 p-1 rounded">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path d="M6 10a2 2 0 11-4 0 2 2 0 014 0zM12 10a2 2 0 11-4 0 2 2 0 014 0zM16 12a2 2 0 100-4 2 2 0 000 4z" />
                        </svg>
                    </button>
                    <div x-show="showPromptOptions" @click.away="showPromptOptions = false" class="absolute right-0 mt-1 w-48 bg-white rounded shadow-lg z-10">
                        <a href="#" @click.prevent="toggleFullscreen" class="block px-4 py-2 hover:bg-gray-100">Pantalla completa</a>
                        <a href="#" @click.prevent="copyToClipboard" class="block px-4 py-2 hover:bg-gray-100">Copiar a portapapeles</a>
                    </div>
                </div>
            </div>
            <textarea id="generatedPrompt" x-model="generatedPrompt" class="w-full p-2 border rounded h-32" readonly></textarea>
        </div>
    </div>

    <script>
        function promptGenerator() {
            return {
                // Estado de la aplicación
                description: '',
                notes: '',
                generatedPrompt: '',
                filePaths: [],
                activeFileId: null,
                executeWith: '',

                // Estado de navegación
                currentPage: 1,
                totalPages: 1,
                currentPromptIndex: -1,
                prompts: [],

                // Estado de UI
                showBulkActions: false,
                showExecuteOptions: false,
                showPromptOptions: false,

                // Inicialización
                init() {
                    this.addFilePath();
                    this.loadFromHash();

                    // Escuchar cambios en el hash de la URL
                    window.addEventListener('hashchange', () => {
                        this.loadFromHash();
                    });

                    // Auto-guardar cambios
                    this.$watch('description', () => this.saveFormToLocalStorage());
                    this.$watch('notes', () => this.saveFormToLocalStorage());
                    this.$watch('filePaths', () => this.saveFormToLocalStorage(), {
                        deep: true
                    });
                },

                // Métodos para manipular rutas
                addFilePath() {
                    const id = 'file-' + Date.now();
                    this.filePaths.push({
                        id: id,
                        path: '',
                        disabled: false,
                        allowedFunctions: '',
                        showFunctions: false,
                        showDropdown: false,
                        selected: false
                    });
                },

                deleteFilePath(index) {
                    if (confirm('¿Estás seguro de que quieres eliminar esta ruta?')) {
                        this.filePaths.splice(index, 1);
                        // Asegurar que siempre haya al menos una ruta
                        if (this.filePaths.length === 0) {
                            this.addFilePath();
                        }
                    }
                },

                toggleFileStatus(index) {
                    this.filePaths[index].disabled = !this.filePaths[index].disabled;
                    this.filePaths[index].showDropdown = false;
                },

                toggleFunctions(index) {
                    this.filePaths[index].showFunctions = !this.filePaths[index].showFunctions;
                    this.filePaths[index].showDropdown = false;
                },

                toggleDropdown(id) {
                    // Cerrar todos los demás dropdowns
                    this.filePaths.forEach(file => {
                        if (file.id !== id) file.showDropdown = false;
                    });

                    // Toggle el dropdown actual
                    const file = this.filePaths.find(f => f.id === id);
                    if (file) file.showDropdown = !file.showDropdown;
                },

                // Acciones masivas
                toggleBulkAction() {
                    this.showBulkActions = !this.showBulkActions;
                },

                deleteSelectedPaths() {
                    if (!this.hasSelectedPaths()) {
                        alert('No hay rutas seleccionadas');
                        return;
                    }

                    if (confirm('¿Estás seguro de que quieres eliminar las rutas seleccionadas?')) {
                        this.filePaths = this.filePaths.filter(file => !file.selected);
                        this.showBulkActions = false;

                        // Asegurar que siempre haya al menos una ruta
                        if (this.filePaths.length === 0) {
                            this.addFilePath();
                        }
                    }
                },

                toggleSelectedPathsStatus(enable) {
                    if (!this.hasSelectedPaths()) {
                        alert('No hay rutas seleccionadas');
                        return;
                    }

                    this.filePaths.forEach(file => {
                        if (file.selected) {
                            file.disabled = !enable;
                        }
                    });
                    this.showBulkActions = false;
                },

                hasSelectedPaths() {
                    return this.filePaths.some(file => file.selected);
                },

                // Manejo del prompt
                extractPathsFromPrompt() {
                    const pathRegex = /(?:[a-zA-Z]:\\[^:\n]+)|(?:\/[^\n:]+)/g;
                    const matches = this.description.match(pathRegex) || [];

                    const validPaths = matches
                        .map(path => path.trim())
                        .filter(path => path && path.includes('.') && !path.endsWith('.'));

                    if (validPaths.length === 0) {
                        alert('No se encontraron rutas de archivo en el texto del PROMPT');
                        return;
                    }

                    // Agregar rutas que no existan ya
                    validPaths.forEach(path => {
                        const pathExists = this.filePaths.some(file => file.path === path);
                        if (!pathExists) {
                            this.filePaths.push({
                                id: 'file-' + Date.now() + Math.random().toString(36).substring(2),
                                path: path,
                                disabled: false,
                                allowedFunctions: '',
                                showFunctions: false,
                                showDropdown: false,
                                selected: false
                            });
                        }
                    });

                    alert(`Se agregaron ${validPaths.length} ruta(s) desde el PROMPT`);
                },

                // Replace the alert functions with SweetAlert versions
                deleteFilePath(index) {
                    Swal.fire({
                        title: '¿Estás seguro?',
                        text: '¿Quieres eliminar esta ruta?',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#3085d6',
                        confirmButtonText: 'Sí, eliminar',
                        cancelButtonText: 'Cancelar'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            this.filePaths.splice(index, 1);
                            // Asegurar que siempre haya al menos una ruta
                            if (this.filePaths.length === 0) {
                                this.addFilePath();
                            }
                            Swal.fire(
                                'Eliminado',
                                'La ruta ha sido eliminada.',
                                'success'
                            );
                        }
                    });
                },

                deleteSelectedPaths() {
                    if (!this.hasSelectedPaths()) {
                        Swal.fire({
                            title: 'Advertencia',
                            text: 'No hay rutas seleccionadas para eliminar',
                            icon: 'warning',
                            confirmButtonText: 'OK'
                        });
                        return;
                    }

                    Swal.fire({
                        title: '¿Estás seguro?',
                        text: '¿Quieres eliminar las rutas seleccionadas?',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#3085d6',
                        confirmButtonText: 'Sí, eliminar',
                        cancelButtonText: 'Cancelar'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            this.filePaths = this.filePaths.filter(file => !file.selected);
                            this.showBulkActions = false;

                            // Asegurar que siempre haya al menos una ruta
                            if (this.filePaths.length === 0) {
                                this.addFilePath();
                            }

                            Swal.fire(
                                'Eliminadas',
                                'Las rutas seleccionadas han sido eliminadas.',
                                'success'
                            );
                        }
                    });
                },

                extractPathsFromPrompt() {
                    const pathRegex = /(?:[a-zA-Z]:\\[^:\n]+)|(?:\/[^\n:]+)/g;
                    const matches = this.description.match(pathRegex) || [];

                    const validPaths = matches
                        .map(path => path.trim())
                        .filter(path => path && path.includes('.') && !path.endsWith('.'));

                    if (validPaths.length === 0) {
                        Swal.fire({
                            title: 'Información',
                            text: 'No se encontraron rutas de archivo en el texto del PROMPT',
                            icon: 'info',
                            confirmButtonText: 'OK'
                        });
                        return;
                    }

                    // Agregar rutas que no existan ya
                    let newPathsCount = 0;
                    validPaths.forEach(path => {
                        const pathExists = this.filePaths.some(file => file.path === path);
                        if (!pathExists) {
                            this.filePaths.push({
                                id: 'file-' + Date.now() + Math.random().toString(36).substring(2),
                                path: path,
                                disabled: false,
                                allowedFunctions: '',
                                showFunctions: false,
                                showDropdown: false,
                                selected: false
                            });
                            newPathsCount++;
                        }
                    });

                    Swal.fire({
                        title: 'Éxito',
                        text: `Se agregaron ${newPathsCount} ruta(s) desde el PROMPT`,
                        icon: 'success',
                        confirmButtonText: 'OK'
                    });
                },

                clearForm() {
                    Swal.fire({
                        title: '¿Estás seguro?',
                        text: '¿Quieres borrar todo el formulario?',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#3085d6',
                        confirmButtonText: 'Sí, borrar todo',
                        cancelButtonText: 'Cancelar'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            this.description = '';
                            this.notes = '';
                            this.generatedPrompt = '';
                            this.filePaths = [];
                            this.addFilePath();
                            localStorage.removeItem('currentForm');
                            history.pushState('', document.title, window.location.pathname);

                            Swal.fire(
                                'Borrado',
                                'El formulario ha sido borrado completamente.',
                                'success'
                            );
                        }
                    });
                },

                copyToClipboard() {
                    if (!this.generatedPrompt.trim()) {
                        Swal.fire({
                            title: 'Advertencia',
                            text: 'No hay contenido para copiar',
                            icon: 'warning',
                            confirmButtonText: 'OK'
                        });
                        return;
                    }

                    // Usar la API moderna del portapapeles si está disponible
                    if (navigator.clipboard) {
                        navigator.clipboard.writeText(this.generatedPrompt)
                            .then(() => {
                                Swal.fire({
                                    title: 'Éxito',
                                    text: 'El prompt ha sido copiado al portapapeles',
                                    icon: 'success',
                                    confirmButtonText: 'OK'
                                });
                                this.showPromptOptions = false;
                            })
                            .catch(err => {
                                console.error('Error al copiar: ', err);
                                this.fallbackCopyToClipboard();
                            });
                    } else {
                        this.fallbackCopyToClipboard();
                    }
                },

                fallbackCopyToClipboard() {
                    // Método de respaldo para navegadores que no soportan la API Clipboard
                    const textarea = document.createElement('textarea');
                    textarea.value = this.generatedPrompt;
                    textarea.style.position = 'fixed';
                    document.body.appendChild(textarea);
                    textarea.select();

                    try {
                        document.execCommand('copy');
                        Swal.fire({
                            title: 'Éxito',
                            text: 'El prompt ha sido copiado al portapapeles',
                            icon: 'success',
                            confirmButtonText: 'OK'
                        });
                    } catch (err) {
                        console.error('Error al copiar: ', err);
                        Swal.fire({
                            title: 'Error',
                            text: 'No se pudo copiar al portapapeles',
                            icon: 'error',
                            confirmButtonText: 'OK'
                        });
                    }

                    document.body.removeChild(textarea);
                    this.showPromptOptions = false;
                },

                // Add these new methods for navigation between saved prompts
                prevPrompt() {
                    // Get all saved prompts
                    const savedPrompts = this.getSavedPrompts();
                    if (savedPrompts.length === 0) {
                        Swal.fire({
                            title: 'Información',
                            text: 'No hay prompts guardados para navegar',
                            icon: 'info',
                            confirmButtonText: 'OK'
                        });
                        return;
                    }

                    // Find current index or default to last item
                    let currentIndex = -1;
                    const currentHash = window.location.hash;
                    if (currentHash.startsWith('#chat-')) {
                        const currentId = currentHash.split('-')[1];
                        currentIndex = savedPrompts.findIndex(p => p.id === currentId);
                    }

                    if (currentIndex <= 0) {
                        // Wrap around to the end if at beginning
                        currentIndex = savedPrompts.length - 1;
                    } else {
                        currentIndex--;
                    }

                    // Navigate to the previous prompt
                    const prevPrompt = savedPrompts[currentIndex];
                    window.location.hash = `#chat-${prevPrompt.id}`;
                    this.loadFromHash();

                    Swal.fire({
                        title: 'Navegación',
                        text: `Cargado prompt ${currentIndex + 1} de ${savedPrompts.length}`,
                        icon: 'success',
                        timer: 1500,
                        showConfirmButton: false
                    });
                },

                nextPrompt() {
                    // Get all saved prompts
                    const savedPrompts = this.getSavedPrompts();
                    if (savedPrompts.length === 0) {
                        Swal.fire({
                            title: 'Información',
                            text: 'No hay prompts guardados para navegar',
                            icon: 'info',
                            confirmButtonText: 'OK'
                        });
                        return;
                    }

                    // Find current index or default to -1 (before first item)
                    let currentIndex = -1;
                    const currentHash = window.location.hash;
                    if (currentHash.startsWith('#chat-')) {
                        const currentId = currentHash.split('-')[1];
                        currentIndex = savedPrompts.findIndex(p => p.id === currentId);
                    }

                    if (currentIndex >= savedPrompts.length - 1 || currentIndex === -1) {
                        // Wrap around to the beginning if at end
                        currentIndex = 0;
                    } else {
                        currentIndex++;
                    }

                    // Navigate to the next prompt
                    const nextPrompt = savedPrompts[currentIndex];
                    window.location.hash = `#chat-${nextPrompt.id}`;
                    this.loadFromHash();

                    Swal.fire({
                        title: 'Navegación',
                        text: `Cargado prompt ${currentIndex + 1} de ${savedPrompts.length}`,
                        icon: 'success',
                        timer: 1500,
                        showConfirmButton: false
                    });
                },

                // Helper method to get all saved prompts sorted by timestamp
                getSavedPrompts() {
                    const prompts = [];
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

                    // Sort by timestamp (newest first)
                    return prompts.sort((a, b) => {
                        return new Date(b.timestamp || 0) - new Date(a.timestamp || 0);
                    });
                },

                executeWithOption(option) {
                    if (!this.generatedPrompt.trim()) {
                        Swal.fire({
                            title: 'Advertencia',
                            text: 'Primero debes generar un prompt para ejecutarlo',
                            icon: 'warning',
                            confirmButtonText: 'OK'
                        });
                        return;
                    }

                    this.executeWith = option;
                    this.showExecuteOptions = false;

                    // En una implementación real, aquí iría la lógica para enviar a ChatGPT o Claude
                    console.log(`Ejecutando con ${option}:`, this.generatedPrompt);

                    // Fingir una ejecución exitosa
                    Swal.fire({
                        title: 'Ejecutando',
                        text: `Enviando prompt a ${option}...`,
                        icon: 'info',
                        timer: 2000,
                        timerProgressBar: true,
                        showConfirmButton: false
                    }).then(() => {
                        // Aquí se implementaría la lógica de comunicación con la API
                        // Para esta demo, mostrar un mensaje de éxito ficticio
                        Swal.fire({
                            title: 'Éxito',
                            text: `El prompt ha sido enviado a ${option}`,
                            icon: 'success',
                            confirmButtonText: 'OK'
                        });
                    });
                },

                // Add method to save the current form as a new prompt
                saveAsNewPrompt() {
                    const formId = Date.now().toString(36) + Math.random().toString(36).substring(2);

                    const formState = {
                        id: formId,
                        description: this.description,
                        notes: this.notes,
                        filePaths: this.filePaths.map(file => ({
                            path: file.path,
                            disabled: file.disabled,
                            allowedFunctions: file.allowedFunctions
                        })),
                        timestamp: new Date().toISOString()
                    };

                    localStorage.setItem(`chat-${formId}`, JSON.stringify(formState));
                    window.location.hash = `#chat-${formId}`;

                    Swal.fire({
                        title: 'Guardado',
                        text: 'El prompt ha sido guardado con un nuevo ID',
                        icon: 'success',
                        confirmButtonText: 'OK'
                    });
                },

                generatePrompt() {
                    // Verificar si hay rutas de archivos habilitadas
                    const enabledPaths = this.filePaths.filter(file => !file.disabled && file.path.trim());

                    if (enabledPaths.length === 0) {
                        Swal.fire({
                            title: 'Advertencia',
                            text: 'No hay rutas de archivos habilitadas para incluir en el prompt',
                            icon: 'warning',
                            confirmButtonText: 'OK'
                        });
                        return;
                    }

                    // Simulamos la generación del prompt (en una implementación real, esto haría una petición al servidor)
                    let promptText = '';

                    // Agregar introducción
                    if (this.description.trim()) {
                        promptText += `${this.description}\n\n`;
                    }

                    // Agregar rutas de archivos habilitadas
                    enabledPaths.forEach(file => {
                        const extension = file.path.split('.').pop().toLowerCase();
                        const language = {
                            'php': 'php',
                            'js': 'javascript',
                            'css': 'css',
                            'html': 'html',
                            'json': 'json'
                        } [extension] || '';

                        promptText += `/* Ruta: ${file.path} */\n\`\`\`${language}\n[Contenido del archivo ${file.path}]\n\`\`\`\n\n`;
                    });

                    // Agregar notas finales
                    if (this.notes.trim()) {
                        promptText += `// Notas finales\n${this.notes}\n`;
                    }

                    this.generatedPrompt = promptText;

                    // Mostrar notificación de éxito
                    Swal.fire({
                        title: 'Éxito',
                        text: 'El prompt ha sido generado correctamente',
                        icon: 'success',
                        timer: 1500,
                        showConfirmButton: false
                    });
                },

                // Operaciones con el prompt generado
                togglePromptOptions() {
                    this.showPromptOptions = !this.showPromptOptions;
                },

                copyToClipboard() {
                    if (!this.generatedPrompt.trim()) {
                        alert('No hay contenido para copiar');
                        return;
                    }

                    // Usar la API moderna del portapapeles si está disponible
                    if (navigator.clipboard) {
                        navigator.clipboard.writeText(this.generatedPrompt)
                            .then(() => {
                                alert('Copiado al portapapeles');
                                this.showPromptOptions = false;
                            })
                            .catch(err => {
                                console.error('Error al copiar: ', err);
                                this.fallbackCopyToClipboard();
                            });
                    } else {
                        this.fallbackCopyToClipboard();
                    }
                },

                fallbackCopyToClipboard() {
                    // Método de respaldo para navegadores que no soportan la API Clipboard
                    const textarea = document.createElement('textarea');
                    textarea.value = this.generatedPrompt;
                    textarea.style.position = 'fixed';
                    document.body.appendChild(textarea);
                    textarea.select();

                    try {
                        document.execCommand('copy');
                        alert('Copiado al portapapeles');
                    } catch (err) {
                        console.error('Error al copiar: ', err);
                        alert('No se pudo copiar al portapapeles');
                    }

                    document.body.removeChild(textarea);
                    this.showPromptOptions = false;
                },

                toggleFullscreen() {
                    const textarea = document.getElementById('generatedPrompt');

                    if (!document.fullscreenElement) {
                        if (textarea.requestFullscreen) {
                            textarea.requestFullscreen();
                        } else if (textarea.mozRequestFullScreen) {
                            textarea.mozRequestFullScreen();
                        } else if (textarea.webkitRequestFullscreen) {
                            textarea.webkitRequestFullscreen();
                        } else if (textarea.msRequestFullscreen) {
                            textarea.msRequestFullscreen();
                        }
                    } else {
                        if (document.exitFullscreen) {
                            document.exitFullscreen();
                        } else if (document.mozCancelFullScreen) {
                            document.mozCancelFullScreen();
                        } else if (document.webkitExitFullscreen) {
                            document.webkitExitFullscreen();
                        } else if (document.msExitFullscreen) {
                            document.msExitFullscreen();
                        }
                    }

                    this.showPromptOptions = false;
                },

                // Opciones de ejecución
                toggleExecuteOptions() {
                    this.showExecuteOptions = !this.showExecuteOptions;
                },

                executeWithOption(option) {
                    this.executeWith = option;
                    this.showExecuteOptions = false;

                    // En una implementación real, aquí iría la lógica para enviar a ChatGPT o Claude
                    console.log(`Ejecutando con ${option}:`, this.generatedPrompt);
                },

                // Gestión del formulario
                clearForm() {
                    if (confirm('¿Estás seguro de que quieres borrar todo el formulario?')) {
                        this.description = '';
                        this.notes = '';
                        this.generatedPrompt = '';
                        this.filePaths = [];
                        this.addFilePath();
                        localStorage.removeItem('currentForm');
                        history.pushState('', document.title, window.location.pathname);
                    }
                },

                newForm() {
                    this.clearForm();
                },

                // Persistencia
                saveFormToLocalStorage() {
                    const formState = {
                        description: this.description,
                        notes: this.notes,
                        filePaths: this.filePaths.map(file => ({
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
                },

                loadFromHash() {
                    const currentHash = window.location.hash;
                    let savedForm;

                    if (currentHash.startsWith('#chat-')) {
                        const formId = currentHash.split('-')[1];
                        savedForm = JSON.parse(localStorage.getItem(`chat-${formId}`));
                    } else {
                        savedForm = JSON.parse(localStorage.getItem('currentForm'));
                    }

                    if (savedForm) {
                        this.description = savedForm.description || '';
                        this.notes = savedForm.notes || '';

                        // Cargar rutas de archivo
                        if (Array.isArray(savedForm.filePaths)) {
                            this.filePaths = savedForm.filePaths.map(file => ({
                                id: 'file-' + Date.now() + Math.random().toString(36).substring(2),
                                path: file.path || '',
                                disabled: file.disabled || false,
                                allowedFunctions: file.allowedFunctions || '',
                                showFunctions: false,
                                showDropdown: false,
                                selected: false
                            }));
                        }

                        // Asegurar que siempre haya al menos una ruta
                        if (this.filePaths.length === 0) {
                            this.addFilePath();
                        }
                    }
                },

                // Navegación entre prompts guardados
                prevPrompt() {
                    // Esta función se implementaría con la API real
                    console.log('Cargar prompt anterior');
                },

                nextPrompt() {
                    // Esta función se implementaría con la API real
                    console.log('Cargar siguiente prompt');
                }
            };
        }
    </script>
</div>