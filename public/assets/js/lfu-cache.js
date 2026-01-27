/*
    LFU Cache -- sin ensayar

    https://chatgpt.com/c/6727cef8-f52c-800d-83ee-5dc80990753a

    // Uso de Ejemplo:
    const cache = new LFUCache(localStorage, 50);

    // Guardar ítems en la caché
    cache.setItem('FORD', { model: 'Mustang' }, 3600);
    cache.setItem('REGULADOR', { type: 'Voltage Regulator' }, 3600);
    cache.setItem('ALTERNADOR', { type: 'Alternator' }, 3600);

    // Obtener ítems de la caché
    const ford = cache.getItem('FORD'); // Incrementa la frecuencia de 'FORD'
    console.log(ford);

    // Mostrar el contenido de la caché
    cache.dd();

    // Limpiar la caché
    // cache.clear();

*/

class LFUCache {
    constructor(storage, maxItems = 100) {
        this.storage = storage;
        this.maxItems = maxItems;
        this.cacheMetaKey = '__cache_meta__';
        this.loadMeta();
    }

    loadMeta() {
        const metaStr = this.storage.getItem(this.cacheMetaKey);
        if (metaStr) {
            this.meta = JSON.parse(metaStr);
        } else {
            this.meta = {}; // { key: { frequency: number, timestamp: number } }
        }
    }

    saveMeta() {
        this.storage.setItem(this.cacheMetaKey, JSON.stringify(this.meta));
    }

    setItem(key, value, expirationInSeconds) {
        const now = Date.now();
        const item = {
            value: value,
            expiration: now + expirationInSeconds * 1000
        };

        // Almacenar el ítem en storage
        this.storage.setItem(key, JSON.stringify(item));

        // Actualizar la metadata
        if (this.meta[key]) {
            this.meta[key].frequency += 1;
            this.meta[key].timestamp = now;
        } else {
            this.meta[key] = { frequency: 1, timestamp: now };
        }

        // Evict if necessary
        this.evictIfNeeded();

        this.saveMeta();
    }

    getItem(key) {
        const itemStr = this.storage.getItem(key);
        if (!itemStr) {
            return null;
        }

        const item = JSON.parse(itemStr);
        const now = Date.now();

        // Verificar expiración
        if (now > item.expiration) {
            this.removeItem(key);
            return null;
        }

        // Actualizar la metadata
        if (this.meta[key]) {
            this.meta[key].frequency += 1;
            this.meta[key].timestamp = now;
        } else {
            this.meta[key] = { frequency: 1, timestamp: now };
        }

        this.saveMeta();

        return item.value;
    }

    removeItem(key) {
        this.storage.removeItem(key);
        delete this.meta[key];
        this.saveMeta();
    }

    evictIfNeeded() {
        const keys = Object.keys(this.meta);
        if (keys.length <= this.maxItems) return;

        // Ordenar las claves por frecuencia y luego por timestamp
        keys.sort((a, b) => {
            if (this.meta[a].frequency === this.meta[b].frequency) {
                return this.meta[a].timestamp - this.meta[b].timestamp; // FIFO si misma frecuencia
            }
            return this.meta[a].frequency - this.meta[b].frequency; // Menor frecuencia primero
        });

        while (keys.length > this.maxItems) {
            const keyToEvict = keys.shift();
            this.removeItem(keyToEvict);
        }
    }

    dd() {
        const storageContents = {};
        for (let i = 0; i < this.storage.length; i++) {
            const key = this.storage.key(i);
            if (key === this.cacheMetaKey) continue; // Omitir metadata
            storageContents[key] = JSON.parse(this.storage.getItem(key));
        }
        console.log(storageContents);
        return storageContents;
    }

    clear() {
        const keys = Object.keys(this.meta);
        keys.forEach(key => {
            this.storage.removeItem(key);
        });
        this.meta = {};
        this.saveMeta();
        console.log("Storage cleared.");
    }
}

