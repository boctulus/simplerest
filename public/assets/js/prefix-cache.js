/*
    Prefix Cache -- sin ensayar

    https://chatgpt.com/c/6727cef8-f52c-800d-83ee-5dc80990753a

    // Usando localStorage
    const searchCache = new PrefixCache(localStorage, 100); // Con un límite de 100 elementos en la caché

    // Usando sessionStorage
    // const searchCache = new PrefixCache(sessionStorage, 100);

    // Para almacenamiento en memoria (sin persistencia)
    // const searchCache = new PrefixCache(new Map(), 100);

    Para usar en un "inmediate search engine"

    Ej:

    // Función de búsqueda principal
    async function search(query) {
        const cachedResult = searchCache.getItem(query); // Busca en la caché por el prefijo

        if (cachedResult) {
            // Si el resultado está en la caché, usa los datos guardados
            displayResults(cachedResult);
            return;
        }

        // Si no está en la caché, realiza la búsqueda AJAX
        try {
            const response = await fetch(`https://miapi.com/search?q=${encodeURIComponent(query)}`);
            if (!response.ok) throw new Error("Error en la búsqueda");

            const result = await response.json();

            // Almacena el resultado en la caché para el prefijo actual
            searchCache.setItem(query, result);
            
            // Muestra el resultado obtenido de la API
            displayResults(result);
        } catch (error) {
            console.error("Error en la búsqueda:", error);
        }
    }

    // Función de mostrar resultados
    function displayResults(results) {
        // Lógica para mostrar los resultados en la interfaz de usuario
        console.log("Resultados:", results);
    }

    // Evento de input para capturar el texto mientras el usuario escribe
    document.getElementById("searchInput").addEventListener("input", (e) => {
        const query = e.target.value.trim();
        if (query.length > 2) { // Comienza a buscar solo si el término tiene al menos 3 caracteres
            search(query);
        }
    });

*/

class PrefixCache {
    constructor(storage, maxItems = 50) {
        this.storage = storage;
        this.maxItems = maxItems;
        this.usageFrequency = new Map(); // Para el conteo de accesos
    }

    setItem(prefix, result) {
        if (this.usageFrequency.size >= this.maxItems) {
            this.evictLFU(); // Elimina el elemento menos usado
        }

        // Almacena el resultado de búsqueda con el prefijo
        this.storage.setItem(prefix, JSON.stringify(result));
        this.usageFrequency.set(prefix, (this.usageFrequency.get(prefix) || 0) + 1);
    }

    getItem(prefix) {
        const itemStr = this.storage.getItem(prefix);
        if (!itemStr) {
            return null;
        }

        // Incrementa la frecuencia de uso
        this.usageFrequency.set(prefix, (this.usageFrequency.get(prefix) || 0) + 1);
        return JSON.parse(itemStr);
    }

    evictLFU() {
        let leastUsedKey = null;
        let leastUsedCount = Infinity;
        
        // Encuentra el prefijo menos usado
        for (const [key, count] of this.usageFrequency.entries()) {
            if (count < leastUsedCount) {
                leastUsedKey = key;
                leastUsedCount = count;
            }
        }

        if (leastUsedKey !== null) {
            this.storage.removeItem(leastUsedKey);
            this.usageFrequency.delete(leastUsedKey);
        }
    }
}
