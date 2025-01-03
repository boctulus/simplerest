class BootstrapPaginator {
    /*
     * Renderiza el paginador de Bootstrap 5
     * @param {Object} data - Datos de la paginación
     * @param {number} shortAfter - Número de páginas antes de acortar
     * @param {boolean} showLast - Muestra la última página
     * @param {string} container_selector - Selector CSS de contenedor donde se renderizará el paginador
     * 
     * @author Pablo Bozzolo
     * 
        Ej:

        // Uso del paginador
        const data = {
            paginator: {
                current_page: 1,
                last_page: 60
            }
        };

        BootstrapPaginator.render(data, '#pagination-container', 5, true);
     */
    static render(data, container_selector, shortAfter = 5, showLast = false) {
        console.log('Datos recibidos por BootstrapPaginator.render:',
        {
            data:data, container_selector:container_selector, shortAfter:shortAfter, showLast:showLast
        } ); // debug

        const currentPage = data.paginator.current_page;
        const lastPage = data.paginator.last_page;
        const pageKey = 'page'; // Puedes ajustar esto según tus necesidades
        const currentUrl = window.location.href;

        const paginationContainer = document.querySelector(container_selector);
        paginationContainer.innerHTML = '';

        const nav = document.createElement('nav');
        nav.setAttribute('aria-label', 'Page navigation');
        const ul = document.createElement('ul');
        ul.className = 'pagination';

        if (lastPage <= shortAfter) {
            for (let i = 1; i <= lastPage; i++) {
                const li = this.createPageItem(i, currentPage, currentUrl, pageKey);
                ul.appendChild(li);
            }
        } else {
            if (currentPage > 1) {
                const prevLi = this.createPageItem(currentPage - 1, currentPage, currentUrl, pageKey, '<');
                ul.appendChild(prevLi);
            }

            if (showLast && currentPage > shortAfter) {
                const firstLi = this.createPageItem(1, currentPage, currentUrl, pageKey);
                ul.appendChild(firstLi);
                ul.appendChild(this.createEllipsisItem());
            }

            for (let i = Math.max(1, currentPage - 2); i <= Math.min(lastPage, currentPage + 2); i++) {
                const li = this.createPageItem(i, currentPage, currentUrl, pageKey);
                ul.appendChild(li);
            }

            if (showLast && currentPage < lastPage - shortAfter + 1) {
                ul.appendChild(this.createEllipsisItem());
                const lastLi = this.createPageItem(lastPage, currentPage, currentUrl, pageKey);
                ul.appendChild(lastLi);
            }

            if (currentPage < lastPage) {
                const nextLi = this.createPageItem(currentPage + 1, currentPage, currentUrl, pageKey, '>');
                ul.appendChild(nextLi);
            }
        }

        nav.appendChild(ul);
        paginationContainer.appendChild(nav);
    }

    static createPageItem(page, currentPage, currentUrl, pageKey, text = null) {
        const li = document.createElement('li');
        li.className = 'page-item' + (page === currentPage ? ' active' : '');
        const a = document.createElement('a');
        a.className = 'page-link';
        a.href = this.addQueryParam(currentUrl, pageKey, page);
        a.innerText = text || page;
    
        // Interceptamos el click para usar la History API
        a.addEventListener('click', (e) => {
            e.preventDefault(); // Evitar que el enlace recargue la página
            const newUrl = this.addQueryParam(currentUrl, pageKey, page);
            
            // Actualizamos la URL sin recargar la página usando History API
            history.pushState(null, '', newUrl);
    
            // Aquí puedes agregar el código para recargar o actualizar el contenido
            // basado en la nueva página seleccionada.
            console.log(`Página cambiada a: ${page}`);
        });
    
        li.appendChild(a);
        return li;
    }

    static createEllipsisItem() {
        const li = document.createElement('li');
        li.className = 'page-item disabled';
        const span = document.createElement('span');
        span.className = 'page-link';
        span.innerText = '...';
        li.appendChild(span);
        return li;
    }

    static addQueryParam(url, key, value) {
        // Extraer la parte antes del fragmento (si existe)
        const urlObj = new URL(url);
        const baseUrl = urlObj.origin + urlObj.pathname; // Parte base de la URL sin el fragmento
        const hash = `paginator:${key}=${value}`; // Usamos : en lugar de ?
        
        // Actualizamos la URL con el nuevo fragmento
        return `${baseUrl}#${hash}`;
    }
    
}

