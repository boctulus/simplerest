<div class="flex justify-between mb-6">
    <button @click="generatePrompt" :disabled="loading" class="bg-blue-600 text-white px-4 py-2 rounded flex items-center">
        <span x-show="!loading">Generar Prompt</span>
        <svg x-show="loading" class="animate-spin h-5 w-5 ml-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
    </button>
    <div x-show="showExecuteOptions" @click.away="showExecuteOptions = false" class="absolute right-0 mt-1 w-48 bg-white rounded shadow-lg z-10">
        <a href="#" @click.prevent="executeWithOption('ChatGPT')" class="block px-4 py-2 hover:bg-gray-100">ChatGPT</a>
        <a href="#" @click.prevent="executeWithOption('Claude')" class="block px-4 py-2 hover:bg-gray-100">Claude</a>
    </div>
</div>