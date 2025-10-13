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
                                <path d="M6 10a2 2 0 11-4 0 2 2 0 014 0zM12 10a2 2 0 11-4 0 2 2 0 014 0zM16 12a2 2 0 100-4 2 2 0 000 4z"/>
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
                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
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