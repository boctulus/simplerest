<div class="mb-6">
    <div class="flex justify-between items-center mb-2">
        <label for="generatedPrompt" class="font-medium">PROMPT GENERADO</label>
        <div class="relative">
            <button @click="togglePromptOptions" class="border bg-gray-100 p-1 rounded">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path d="M6 10a2 2 0 11-4 0 2 2 0 014 0zM12 10a2 2 0 11-4 0 2 2 0 014 0zM16 12a2 2 0 100-4 2 2 0 000 4z"/>
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