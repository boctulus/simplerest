/*
    LRU Cache -- sin ensayar

    https://chatgpt.com/c/6727cef8-f52c-800d-83ee-5dc80990753a

    // Example Usage:
    const cache = new LRUCache(localStorage, 3);
    cache.setItem('key1', 'value1', 3600);
    cache.setItem('key2', 'value2', 3600);
    cache.setItem('key3', 'value3', 3600);
    cache.getItem('key1'); // Access to make 'key1' recently used
    cache.setItem('key4', 'value4', 3600); // This should evict the least recently used item (key2)
    cache.dd(); // Show storage contents
    cache.clear(); // Clear storage
*/

class LRUCache {
    constructor(storage, maxItems = 5) {
        this.storage = storage;
        this.maxItems = maxItems;
        this.usageOrder = []; // To track order of usage by keys
        this.loadFromStorage(); // Initialize with existing storage data
    }

    setItem(key, value, expirationInSeconds) {
        const now = new Date().getTime();
        const item = {
            value: value,
            expiration: now + expirationInSeconds * 1000
        };

        // Store the item in storage
        this.storage.setItem(key, JSON.stringify(item));

        // Update usage order
        this.updateUsageOrder(key);

        // Ensure cache size limit
        this.evictIfNeeded();
    }

    getItem(key) {
        const itemStr = this.storage.getItem(key);
        if (!itemStr) {
            return null;
        }

        const item = JSON.parse(itemStr);
        const now = new Date().getTime();

        // Remove item if expired
        if (now > item.expiration) {
            this.storage.removeItem(key);
            this.removeFromUsageOrder(key);
            return null;
        }

        // Update usage order since it was accessed
        this.updateUsageOrder(key);
        return item.value;
    }

    updateUsageOrder(key) {
        const index = this.usageOrder.indexOf(key);
        if (index !== -1) {
            this.usageOrder.splice(index, 1);
        }
        this.usageOrder.push(key);
    }

    evictIfNeeded() {
        while (this.usageOrder.length > this.maxItems) {
            const oldestKey = this.usageOrder.shift();
            this.storage.removeItem(oldestKey);
        }
    }

    removeFromUsageOrder(key) {
        const index = this.usageOrder.indexOf(key);
        if (index !== -1) {
            this.usageOrder.splice(index, 1);
        }
    }

    loadFromStorage() {
        for (let i = 0; i < this.storage.length; i++) {
            const key = this.storage.key(i);
            const item = JSON.parse(this.storage.getItem(key));

            // Remove expired items during initialization
            if (new Date().getTime() > item.expiration) {
                this.storage.removeItem(key);
            } else {
                this.usageOrder.push(key); // Add non-expired items to usage order
            }
        }
    }

    dd() {
        const storageContents = {};
        for (let i = 0; i < this.storage.length; i++) {
            const key = this.storage.key(i);
            storageContents[key] = JSON.parse(this.storage.getItem(key));
        }
        console.log(storageContents);
        return storageContents;
    }

    clear() {
        this.storage.clear();
        this.usageOrder = [];
        console.log("Storage cleared.");
    }
}

