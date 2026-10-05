import { defineConfig } from 'vitepress'

export default defineConfig({
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
              { text: 'Ciclo de request', link: '/core/request-lifecycle' },
              { text: 'Base de datos', link: '/database' }
            ]
          },
          {
            text: 'HTTP y seguridad',
            items: [
              { text: 'HTTP/API', link: '/api' },
              { text: 'Seguridad', link: '/security' },
              { text: 'Webhooks', link: '/webhooks' }
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
              { text: 'Ciclo de request', link: '/core/request-lifecycle' }
            ]
          },
          {
            text: 'Guías técnicas',
            items: [
              { text: 'Base de datos', link: '/database' },
              { text: 'HTTP/API', link: '/api' },
              { text: 'Seguridad', link: '/security' },
              { text: 'Webhooks', link: '/webhooks' }
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
