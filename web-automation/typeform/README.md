# Typeform Module - Automation Tests

Este directorio contiene las pruebas de automatización con Playwright para el módulo Typeform del framework SimpleRest.

## Estructura

```
webautomation/typeform/
├── tests/
│   ├── typeform-flow.spec.js        # Pruebas del flujo principal
│   ├── typeform-accessibility.spec.js # Pruebas de accesibilidad
│   └── typeform-performance.spec.js   # Pruebas de rendimiento
├── package.json                      # Dependencias del proyecto
├── playwright.config.js             # Configuración de Playwright
└── README.md                        # Este archivo
```

## Instalación

1. Navegar al directorio:
   ```bash
   cd webautomation/typeform
   ```

2. Instalar dependencias:
   ```bash
   npm install
   ```

3. Instalar navegadores:
   ```bash
   npm run install-browsers
   ```

## Ejecución de Pruebas

### Ejecutar todas las pruebas
```bash
npm test
```

### Ejecutar pruebas con interfaz gráfica
```bash
npm run test:headed
```

### Ejecutar pruebas en modo debug
```bash
npm run test:debug
```

### Generar código de pruebas automáticamente
```bash
npm run codegen
```

## Tipos de Pruebas

### 1. Pruebas del Flujo Principal (typeform-flow.spec.js)
- **Navegación entre pasos**: Verifica que el usuario puede navegar secuencialmente a través de todos los pasos del formulario
- **Validación de campos requeridos**: Confirma que los campos obligatorios son validados correctamente
- **Formateo automático**: Prueba el formateo automático del RUT y números de teléfono
- **Campos condicionales**: Verifica que el campo de subida de firma aparece/desaparece según la selección
- **Persistencia de datos**: Confirma que los datos del formulario se mantienen al recargar la página
- **Diseño responsivo**: Verifica que el formulario funciona correctamente en dispositivos móviles

### 2. Pruebas de Accesibilidad (typeform-accessibility.spec.js)
- **Jerarquía de encabezados**: Verifica que los encabezados H1, H2, etc. están correctamente estructurados
- **Etiquetas de formulario**: Confirma que todos los inputs tienen labels asociados
- **Navegación por teclado**: Prueba que el formulario es completamente navegable con teclado
- **Atributos ARIA**: Verifica que los elementos tienen los atributos ARIA apropiados
- **Contraste de colores**: Confirma que el texto es visible contra los fondos
- **Soporte de lectores de pantalla**: Prueba características amigables para lectores de pantalla
- **Modo de alto contraste**: Verifica que el formulario funciona en modo de alto contraste
- **Manejo del foco**: Confirma que el foco se maneja correctamente durante la navegación
- **Textos alternativos**: Verifica que los iconos tienen textos alternativos apropiados
- **Soporte de zoom**: Prueba que el formulario funciona correctamente con zoom al 200%

### 3. Pruebas de Rendimiento (typeform-performance.spec.js)
- **Tiempo de carga**: Verifica que la página carga en menos de 3 segundos
- **Core Web Vitals**: Mide métricas importantes como LCP (Largest Contentful Paint)
- **Transiciones suaves**: Confirma que las transiciones entre pasos son rápidas (<500ms)
- **Detección de memory leaks**: Verifica que no hay fugas de memoria durante la navegación
- **Eficiencia de recursos**: Confirma que los archivos CSS y JS son de tamaño razonable
- **Manejo de datos grandes**: Prueba el rendimiento con formularios con mucha información
- **Redes lentas**: Verifica que el formulario funciona en conexiones lentas (3G)
- **Optimización de imágenes**: Confirma que las imágenes están optimizadas
- **Interacciones concurrentes**: Prueba el manejo de múltiples interacciones simultáneas

## Configuración

El archivo `playwright.config.js` está configurado para:
- Ejecutar pruebas en múltiples navegadores (Chrome, Firefox, Safari)
- Probar en dispositivos móviles (Pixel 5, iPhone 12)
- Generar reportes HTML
- Capturar screenshots y videos en caso de fallos
- Generar trazas para debugging

## URL Base

Por defecto, las pruebas asumen que el servidor local está corriendo en `http://localhost:8080`. Si tu configuración es diferente, modifica la propiedad `baseURL` en `playwright.config.js`.

## Reportes

Después de ejecutar las pruebas, se genera un reporte HTML que incluye:
- Resultados de todas las pruebas
- Screenshots de fallos
- Videos de ejecución
- Trazas para debugging

Para ver el reporte:
```bash
npx playwright show-report
```

## Mejores Prácticas

1. **Ejecutar pruebas regularmente**: Integra estas pruebas en tu pipeline de CI/CD
2. **Mantener pruebas actualizadas**: Actualiza las pruebas cuando cambies la funcionalidad
3. **Usar selectores estables**: Prefiere usar `data-testid` o IDs estables en lugar de clases CSS
4. **Probar en múltiples navegadores**: Las pruebas están configuradas para ejecutarse en Chrome, Firefox y Safari
5. **Considerar la accesibilidad**: Las pruebas de accesibilidad ayudan a crear una mejor experiencia para todos los usuarios

## Solución de Problemas

### Error: "page.goto: net::ERR_CONNECTION_REFUSED"
- Verifica que el servidor web esté corriendo
- Confirma que la URL base en `playwright.config.js` es correcta

### Pruebas lentas
- Usa `--workers=1` para ejecutar pruebas secuencialmente
- Considera aumentar los timeouts si es necesario

### Fallos en pruebas de accesibilidad
- Revisa que todos los elementos interactivos tengan labels apropiados
- Verifica la estructura de encabezados HTML
- Confirma que los atributos ARIA están presentes

Para más información sobre Playwright, visita: https://playwright.dev/