/**
 * Component Loader
 *
 * Sistema de carga dinámica de componentes web
 *
 * @author Pablo Bozzolo (boctulus)
 * @version 1.0.0
 */

(function(window) {
  'use strict';

  /**
   * Carga un componente de forma dinámica
   *
   * @param {string|HTMLElement} targetSelector - Selector CSS o elemento DOM donde se cargará el componente
   * @param {string} componentName - Nombre del componente a cargar
   * @param {object} options - Opciones para inicializar el componente
   * @returns {Promise<void>}
   */
  async function loadComponent(targetSelector, componentName, options = {}) {
    const basePath = `/components/${componentName}`;
    const htmlPath = `${basePath}/${componentName}.html`;
    const cssPath  = `${basePath}/${componentName}.css`;
    const jsPath   = `${basePath}/${componentName}.js`;

    // Obtener el elemento target
    const $target = typeof targetSelector === 'string'
      ? $(targetSelector)
      : $(targetSelector);

    if ($target.length === 0) {
      console.error(`ComponentLoader: No se encontró el elemento ${targetSelector}`);
      return;
    }

    // 1. Cargar HTML
    try {
      const htmlResponse = await fetch(htmlPath);
      if (!htmlResponse.ok) {
        throw new Error(`Error al cargar HTML: ${htmlResponse.status}`);
      }
      const html = await htmlResponse.text();
      $target.html(html);
    } catch (error) {
      console.error(`ComponentLoader: Error cargando HTML de ${componentName}:`, error);
      throw error;
    }

    // 2. Cargar e inyectar CSS si existe
    fetch(cssPath)
      .then(r => r.ok ? r.text() : null)
      .then(css => {
        if (css && !isStyleLoaded(componentName)) {
          const $style = $("<style>")
            .attr('data-component', componentName)
            .html(css);
          $("head").append($style);
        }
      })
      .catch(err => {
        console.warn(`ComponentLoader: No se pudo cargar CSS para ${componentName}`, err);
      });

    // 3. Cargar módulo JS e inicializar
    try {
      await loadScript(jsPath, componentName);

      // Esperar a que el script se cargue completamente
      await new Promise(resolve => setTimeout(resolve, 100));

      // Buscar la función init del componente
      const componentNamespace = componentName.replace(/-/g, '_');

      if (window[componentNamespace] && typeof window[componentNamespace].init === 'function') {
        window[componentNamespace].init($target, options);
      } else if (window.Components && window.Components[componentName] && typeof window.Components[componentName].init === 'function') {
        window.Components[componentName].init($target, options);
      } else {
        console.warn(`ComponentLoader: No se encontró la función init() para ${componentName}`);
      }
    } catch (e) {
      console.warn(`ComponentLoader: No se pudo cargar JS para ${componentName}`, e);
    }
  }

  /**
   * Verifica si el CSS de un componente ya fue cargado
   *
   * @param {string} componentName - Nombre del componente
   * @returns {boolean}
   */
  function isStyleLoaded(componentName) {
    return $(`style[data-component="${componentName}"]`).length > 0;
  }

  /**
   * Carga un script de forma dinámica
   *
   * @param {string} src - URL del script
   * @param {string} id - ID para el script
   * @returns {Promise<void>}
   */
  function loadScript(src, id) {
    return new Promise((resolve, reject) => {
      // Si ya existe el script, no lo cargamos de nuevo
      if (document.querySelector(`script[data-component="${id}"]`)) {
        resolve();
        return;
      }

      const script = document.createElement('script');
      script.src = src;
      script.setAttribute('data-component', id);
      script.onload = () => resolve();
      script.onerror = () => reject(new Error(`Error al cargar script: ${src}`));
      document.head.appendChild(script);
    });
  }

  /**
   * Descarga un componente (limpia recursos)
   *
   * @param {string} componentName - Nombre del componente
   */
  function unloadComponent(componentName) {
    // Remover CSS
    $(`style[data-component="${componentName}"]`).remove();

    // Remover JS
    $(`script[data-component="${componentName}"]`).remove();

    console.log(`ComponentLoader: Componente ${componentName} descargado`);
  }

  // Exportar como módulo global
  window.ComponentLoader = {
    loadComponent,
    unloadComponent
  };

  // También exportar como ES6 module si está disponible
  if (typeof module !== 'undefined' && module.exports) {
    module.exports = { loadComponent, unloadComponent };
  }

})(window);
