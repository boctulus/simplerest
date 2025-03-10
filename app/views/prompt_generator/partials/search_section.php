<div class="relative mt-6 mb-8 w-full md:w-auto flex-grow">
    <input 
        type="text" 
        x-model="searchQuery" 
        @keydown.enter="searchPrompts"
        class="w-full p-2 pr-10 border rounded" 
        placeholder="Buscar prompts...">
    <button 
        @click="searchPrompts" 
        class="absolute right-2 top-1/2 transform -translate-y-1/2 text-gray-500 hover:text-gray-700">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
        </svg>
    </button>
    
    <div x-show="searchResults.length > 0" @click.away="searchResults = []" class="absolute z-10 bg-white w-full mt-1 rounded shadow-lg max-h-60 overflow-y-auto">
        <template x-for="result in searchResults" :key="result.id">
            <div @click="loadPrompt(result.id)" class="p-2 hover:bg-gray-100 cursor-pointer">
                <div class="font-medium truncate" x-text="result.description ? result.description.substring(0, 50) + '...' : 'Sin descripción'"></div>
                <div class="text-xs text-gray-500" x-text="formatDate(result.timestamp)"></div>
            </div>
        </template>
    </div>
</div>