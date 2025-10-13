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