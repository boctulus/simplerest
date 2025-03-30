/*
    Transients.js
    
    Esta clase permite almacenar datos en localStorage o sessionStorage con un tiempo de vida (TTL) específico.

    @author Pablo Bozzolo
    
    // Uso:

    // Instanciar con localStorage
    const cache = new Cache(localStorage);
    cache.setItem('key', 'value', 3600); // Guarda en localStorage con TTL de 1 hora
    cache.dd(); // Muestra el contenido de localStorage

    // Instanciar con sessionStorage
    const sessionCache = new Cache(sessionStorage);
    sessionCache.setItem('sessionKey', 'sessionValue', 1800); // Guarda en sessionStorage
    sessionCache.clear(); // Limpia sessionStorage

    // Invalidación de cache
    // Invalida todas las entradas que excedan el tiempo máximo especificado (en segundos)
    cache.invalidate(3600); // Invalida entradas mayores a 1 hora
    
    // Invalida una key específica si excede el tiempo máximo
    cache.invalidate(3600, 'miKey'); // Verifica solo 'miKey' y la invalida si excede 1 hora

    Las keys que se invaliden seran auto-removidas periodicamente
*/

class Cache {
    constructor(storage) {
        this.storage = storage;
        this.invalidationTimes = {};
        this.cleanupInterval = null;
        this.startCleanupInterval();
    }

    startCleanupInterval() {
        if (this.cleanupInterval) {
            clearInterval(this.cleanupInterval);
        }
        
        this.cleanupInterval = setInterval(() => {
            this.cleanup();
        }, 1000);
    }

    stopCleanupInterval() {
        if (this.cleanupInterval) {
            clearInterval(this.cleanupInterval);
            this.cleanupInterval = null;
        }
    }

    cleanup() {
        const now = Date.now();
        const keys = [];
        for (let i = 0; i < this.storage.length; i++) {
            keys.push(this.storage.key(i));
        }

        keys.forEach(key => {
            const itemStr = this.storage.getItem(key);
            if (!itemStr) return;

            try {
                const item = JSON.parse(itemStr);
                if (!item.created || typeof item.created !== 'number') return;

                // Si hay un tiempo de invalidación específico para esta key
                const invalidationTime = this.invalidationTimes[key];
                if (invalidationTime && (now - item.created) > invalidationTime) {
                    this.storage.removeItem(key);
                    console.log(`Auto-removed invalidated key: "${key}"`);
                    delete this.invalidationTimes[key];
                    return;
                }

                // Chequear expiración normal
                if (now > item.expiration) {
                    this.storage.removeItem(key);
                    console.log(`Auto-removed expired key: "${key}"`);
                }
            } catch (error) {
                console.log(`Error processing key "${key}":`, error);
            }
        });
    }

    setItem(key, value, expirationInSeconds) {
        const now = Date.now();
        const ttl = expirationInSeconds * 1000;
        const item = {
            value: value,
            created: now,
            expiration: now + ttl,
            ttl: ttl
        };
        this.storage.setItem(key, JSON.stringify(item));
    }

    getItem(key) {
        const itemStr = this.storage.getItem(key);
        if (!itemStr) {
            return null;
        }

        const item = JSON.parse(itemStr);
        const now = Date.now();

        if (now > item.expiration) {
            this.storage.removeItem(key);
            return null;
        }

        return item.value;
    }

    dd() {
        const storageContents = {};
        for (let i = 0; i < this.storage.length; i++) {
            const key = this.storage.key(i);
            storageContents[key] = JSON.parse(this.storage.getItem(key));
        }

        console.log(storageContents);
    }

    clear(keys) {
        if (!keys) {
            this.storage.clear();
            this.invalidationTimes = {};  // Limpiar también los tiempos de invalidación
            console.log("Storage cleared.");
        } else if (typeof keys === "string") {
            this.storage.removeItem(keys);
            delete this.invalidationTimes[keys];  // Eliminar tiempo de invalidación de la key
            console.log(`Key "${keys}" removed from storage.`);
        } else if (Array.isArray(keys)) {
            keys.forEach((key) => {
                this.storage.removeItem(key);
                delete this.invalidationTimes[key];  // Eliminar tiempo de invalidación de cada key
                console.log(`Key "${key}" removed from storage.`);
            });
        } else {
            console.warn("Invalid input for clear(). Provide a key or an array of keys.");
        }
    }

    invalidate(maxAge, specificKey = null) {
        const maxAgeMs = maxAge * 1000;
        
        if (specificKey) {
            const itemStr = this.storage.getItem(specificKey);
            if (!itemStr) {
                console.log(`Key "${specificKey}" not found in storage.`);
                return;
            }
            
            this.invalidationTimes[specificKey] = maxAgeMs;
            console.log(`Set invalidation time for "${specificKey}": ${maxAge}s`);
            return;
        }
        
        const keys = [];
        for (let i = 0; i < this.storage.length; i++) {
            keys.push(this.storage.key(i));
        }
        
        keys.forEach(key => {
            const itemStr = this.storage.getItem(key);
            if (!itemStr) return;
            
            try {
                const item = JSON.parse(itemStr);
                if (!item.created || typeof item.created !== 'number') return;
                
                this.invalidationTimes[key] = maxAgeMs;
            } catch (error) {
                console.log(`Error processing key "${key}":`, error);
            }
        });
        
        console.log("Invalidation times set.");
    }

    destroy() {
        this.stopCleanupInterval();
        this.clear();
    }
}