<div x-show="loading" 
     x-init="$watch('loading', value => document.body.style.overflow = value ? 'hidden' : '')" 
     style="display: none" 
     class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50" 
     id="ajaxLoader">
    <div class="bg-white p-5 rounded-lg flex flex-col items-center">
        <div class="loader-spinner mb-3">
            <div class="animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-blue-600"></div>
        </div>
        <p class="text-gray-700">Procesando su solicitud...</p>
    </div>
</div>