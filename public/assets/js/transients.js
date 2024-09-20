const localStorageCache = {
    setItem: function (key, value, expirationInSeconds) {
        const now = new Date().getTime();
        const item = {
            value: value,
            expiration: now + expirationInSeconds * 1000
        };
        localStorage.setItem(key, JSON.stringify(item));
    },

    getItem: function (key) {
        const itemStr = localStorage.getItem(key);
        // Si el item no existe, devuelve null
        if (!itemStr) {
            return null;
        }

        const item = JSON.parse(itemStr);
        const now = new Date().getTime();

        // Comprobamos si ha expirado
        if (now > item.expiration) {
            localStorage.removeItem(key); // Limpiamos el cache
            return null;
        }

        return item.value;
    }
};

const sessionStorageCache = {
    setItem: function (key, value, expirationInSeconds) {
        const now = new Date().getTime();
        const item = {
            value: value,
            expiration: now + expirationInSeconds * 1000
        };
        sessionStorage.setItem(key, JSON.stringify(item));
    },

    getItem: function (key) {
        const itemStr = sessionStorage.getItem(key);
        // Si el item no existe, devuelve null
        if (!itemStr) {
            return null;
        }

        const item = JSON.parse(itemStr);
        const now = new Date().getTime();

        // Comprobamos si ha expirado
        if (now > item.expiration) {
            sessionStorage.removeItem(key); // Limpiamos el cache
            return null;
        }

        return item.value;
    }
};
