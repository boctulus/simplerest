import { defineConfig } from 'vitepress'

export default defineConfig({
  base: '/simplerest/',
  title: 'SimpleRest | Documentación',
  description: 'Documentación pública curada de SimpleRest.',
  locales: {
    // Español vive en /; añadir locales cuando existan traducciones revisadas.
    root: {
      label: 'Español',
      lang: 'es-ES',
      themeConfig: {
        nav: [
          { text: 'Inicio', link: '/' },
          { text: 'Primeros pasos', link: '/getting-started' },
          {
            text: 'Guías',
            items: [
              { text: 'Arquitectura', link: '/architecture' },
              { text: 'Recorrido HTTP de una petición', link: '/core/request-lifecycle' },
              { text: 'Request y Response', link: '/core/request-response' },
              { text: 'Routing HTTP (WebRouter)', link: '/core/web-routing' },
              { text: 'Microcore y paquetes', link: '/core/microcore' },
              { text: 'ApiClient', link: '/core/api-client' },
              { text: 'Base de datos', link: '/database' },
              { text: 'Query Builder', link: '/database/query-builder' },
              { text: 'Paginación', link: '/database/pagination' },
              { text: 'Model y ModelRecord', link: '/database/model' },
              { text: 'Relaciones y subrecursos', link: '/database/relations' },
              { text: 'Consultas SQL raw', link: '/database/raw-queries' },
              { text: 'Transacciones', link: '/database/transactions' },
              { text: 'Compatibilidad de drivers', link: '/database/driver-compatibility' },
              { text: 'Constructor DDL de esquemas', link: '/database/schema-builder' },
              { text: 'Ciclo de migraciones', link: '/database/migrations' }
            ]
          },
          {
            text: 'Runtime del core',
            items: [
              { text: 'Middleware', link: '/core/middleware' },
              { text: 'Handlers de FrontController', link: '/core/handlers' },
              { text: 'Configuración y entorno', link: '/core/configuration-environment' },
              { text: 'Proveedores de servicios', link: '/core/service-providers' },
              { text: 'Container de dependencias', link: '/core/dependency-container' },
              { text: 'CORS', link: '/core/cors' },
              { text: 'Sesiones y cookies', link: '/core/sessions-cookies' },
              { text: 'Manejo de errores', link: '/core/error-handling' },
              { text: 'Logging', link: '/core/logging' }
            ]
          },
          {
            text: 'Archivos y contenido',
            items: [
              { text: 'Filesystem y rutas', link: '/core/filesystem' },
              { text: 'Uploads', link: '/core/file-uploads' },
              { text: 'ZIP y backups', link: '/core/archives-backups' },
              { text: 'Conversión HTML/XML e imágenes', link: '/core/content-conversion' }
            ]
          },
          {
            text: 'Servicios',
            items: [
              { text: 'Índice de servicios', link: '/services' },
              { text: 'Cache', link: '/services/cache' },
              { text: 'EventBus', link: '/services/event-bus' },
              { text: 'Vistas PHP', link: '/services/views' },
              { text: 'Traducciones', link: '/services/i18n' },
              { text: 'Validación', link: '/services/validation' },
              { text: 'HTML y formularios', link: '/services/forms' }
            ]
          },
          {
            text: 'HTTP y seguridad',
            items: [
              { text: 'HTTP/API', link: '/api' },
              { text: 'Endpoints automáticos', link: '/api/automatic-endpoints' },
              { text: 'Seguridad', link: '/security' },
              { text: 'Autenticación', link: '/security/authentication' },
              { text: 'ACL y autorización API', link: '/security/acl' },
              { text: 'Webhooks', link: '/webhooks' }
            ]
          },
          {
            text: 'Integraciones',
            items: [
              { text: 'Índice', link: '/integrations' },
              { text: 'API Clients', link: '/integrations/api-clients' },
              { text: 'Proveedores LLM', link: '/integrations/llm-providers' },
              { text: 'Shopify Connector', link: '/integrations/shopify' }
            ]
          },
          {
            text: 'Operación',
            items: [
              { text: 'Catálogo php com', link: '/cli/commands' },
              { text: 'Routing CLI (CliRouter)', link: '/cli/routing' },
              { text: 'Despliegue', link: '/deployment' },
              { text: 'Rendimiento', link: '/performance' }
            ]
          }
        ],
        sidebar: [
          {
            text: 'Empezar',
            items: [{ text: 'Primeros pasos', link: '/getting-started' }]
          },
          {
            text: 'Fundamentos',
            items: [
              { text: 'Arquitectura', link: '/architecture' },
              { text: 'Recorrido HTTP de una petición', link: '/core/request-lifecycle' },
              { text: 'Request y Response', link: '/core/request-response' },
              { text: 'Routing HTTP (WebRouter)', link: '/core/web-routing' },
              { text: 'Microcore y paquetes', link: '/core/microcore' },
              { text: 'ApiClient', link: '/core/api-client' }
            ]
          },
          {
            text: 'Runtime del core',
            items: [
              { text: 'Middleware', link: '/core/middleware' },
              { text: 'Handlers de FrontController', link: '/core/handlers' },
              { text: 'Configuración y entorno', link: '/core/configuration-environment' },
              { text: 'Proveedores de servicios', link: '/core/service-providers' },
              { text: 'Container de dependencias', link: '/core/dependency-container' },
              { text: 'CORS', link: '/core/cors' },
              { text: 'Sesiones y cookies', link: '/core/sessions-cookies' },
              { text: 'Manejo de errores', link: '/core/error-handling' },
              { text: 'Logging', link: '/core/logging' }
            ]
          },
          {
            text: 'Archivos y contenido',
            items: [
              { text: 'Filesystem y rutas', link: '/core/filesystem' },
              { text: 'Uploads', link: '/core/file-uploads' },
              { text: 'ZIP y backups', link: '/core/archives-backups' },
              { text: 'Conversión HTML/XML e imágenes', link: '/core/content-conversion' }
            ]
          },
          {
            text: 'Servicios',
            items: [
              { text: 'Servicios del framework', link: '/services' },
              { text: 'Cache', link: '/services/cache' },
              { text: 'EventBus', link: '/services/event-bus' },
              { text: 'Vistas PHP', link: '/services/views' },
              { text: 'Traducciones', link: '/services/i18n' },
              { text: 'Validación', link: '/services/validation' },
              { text: 'HTML y formularios', link: '/services/forms' }
            ]
          },
          {
            text: 'Integraciones',
            items: [
              { text: 'Índice de integraciones', link: '/integrations' },
              { text: 'API Clients', link: '/integrations/api-clients' },
              { text: 'Proveedores LLM', link: '/integrations/llm-providers' },
              { text: 'Shopify Connector', link: '/integrations/shopify' }
            ]
          },
          {
            text: 'Guías técnicas',
            items: [
              { text: 'Base de datos', link: '/database' },
              { text: 'Query Builder', link: '/database/query-builder' },
              { text: 'Paginación', link: '/database/pagination' },
              { text: 'Model y ModelRecord', link: '/database/model' },
              { text: 'Relaciones y subrecursos', link: '/database/relations' },
              { text: 'Consultas SQL raw', link: '/database/raw-queries' },
              { text: 'Transacciones', link: '/database/transactions' },
              { text: 'Compatibilidad de drivers', link: '/database/driver-compatibility' },
              { text: 'Constructor DDL de esquemas', link: '/database/schema-builder' },
              { text: 'Ciclo de migraciones', link: '/database/migrations' },
              { text: 'HTTP/API', link: '/api' },
              { text: 'Endpoints automáticos', link: '/api/automatic-endpoints' },
              { text: 'Seguridad', link: '/security' },
              { text: 'Autenticación', link: '/security/authentication' },
              { text: 'ACL y autorización API', link: '/security/acl' },
              { text: 'Webhooks', link: '/webhooks' }
            ]
          },
          {
            text: 'Operación',
            items: [
              { text: 'Catálogo php com', link: '/cli/commands' },
              { text: 'Routing CLI (CliRouter)', link: '/cli/routing' },
              { text: 'Despliegue', link: '/deployment' },
              { text: 'Rendimiento', link: '/performance' }
            ]
          }
        ],
        outline: { label: 'En esta página' },
        docFooter: { prev: 'Anterior', next: 'Siguiente' },
        lastUpdated: {
          text: 'Actualizado',
          formatOptions: { dateStyle: 'medium', forceLocale: true }
        },
        search: {
          provider: 'local',
          options: {
            locales: {
              root: {
                translations: {
                  button: {
                    buttonText: 'Buscar documentación',
                    buttonAriaLabel: 'Buscar documentación'
                  },
                  modal: {
                    displayDetails: 'Mostrar detalles',
                    resetButtonTitle: 'Limpiar búsqueda',
                    backButtonTitle: 'Cerrar búsqueda',
                    noResultsText: 'No hay resultados',
                    footer: {
                      selectText: 'Seleccionar',
                      selectKeyAriaLabel: 'Intro',
                      navigateText: 'Navegar',
                      navigateUpKeyAriaLabel: 'Flecha arriba',
                      navigateDownKeyAriaLabel: 'Flecha abajo',
                      closeText: 'Cerrar',
                      closeKeyAriaLabel: 'Esc'
                    }
                  }
                }
              }
            }
          }
        },
        darkModeSwitchLabel: 'Apariencia',
        lightModeSwitchTitle: 'Cambiar a tema claro',
        darkModeSwitchTitle: 'Cambiar a tema oscuro',
        sidebarMenuLabel: 'Menú',
        returnToTopLabel: 'Volver arriba',
        navMenuLabel: 'Navegación principal',
        mobileMenuLabel: 'Menú',
        extraMenuLabel: 'Más opciones',
        skipToContentLabel: 'Saltar al contenido'
      }
    }
  }
})
