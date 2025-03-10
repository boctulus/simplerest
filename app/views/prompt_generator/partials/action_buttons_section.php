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