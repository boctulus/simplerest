 Plan de Refactoring: Migración de Endpoints Automáticos a WebRouter

 Contexto

 El usuario ha planteado dos tareas:

 Tarea 1: Arreglar pruebas unitarias no ejecutables

 Las pruebas en tests/ApiTest.php no se están ejecutando debido a una exclusión del grupo refactor en phpunit.xml.

 Tarea 2: Migrar endpoints automáticos del FrontController al WebRouter

 Evaluar y ejecutar la migración de rutas API automáticas (ej: GET /api/v1/products?fields=id,name&cost[between]=49,100) desde el FrontController al
 WebRouter, permitiendo deshabilitar el FrontController.

 Análisis de la Arquitectura Actual

 Sistema de Routing Actual

 El framework SimpleRest tiene 3 niveles de routing en cascada:

 1. WebRouter → Rutas explícitas definidas en config/routes.php
 2. CliRouter → Comandos CLI (solo terminal)
 3. FrontController → Fallback automático para rutas no explícitas

 Flujo de ejecución (index.php líneas 19-33):
 if ($cfg['web_router']){
     WebRouter::compile();
     WebRouter::resolve();  // Si resuelve, hace exit
 }

 if ($cfg['console_router']){
     CliRouter::compile();
     CliRouter::resolve();  // Si resuelve, hace exit
 }

 if ($cfg['front_controller']){
     FrontController::resolve();  // ← FALLBACK
 }

 Cómo funcionan los Endpoints Automáticos

 El FrontController utiliza un sistema de Handlers modulares:

 Request → RequestHandler::parse()
             ↓
          ApiHandler::resolve() (para /api/*)
             ↓
          Controlador API (Products.php)
             ↓
          ApiController::get() (lógica CRUD completa)
             ↓
          OutputHandler::format()
             ↓
          Response

 Características clave:
 - No requiere registro previo de rutas
 - Mapeo automático: GET /api/v1/products → ProductsController::get()
 - Soporte completo de query parameters complejos
 - Integración ACL automática
 - Versionado de API incorporado

 Problema Identificado

 NO ES VIABLE migrar completamente los endpoints automáticos al WebRouter por las siguientes razones:

 1. Diferencia Fundamental de Diseño
 ┌─────────────────────┬───────────────────────────────┬───────────────────────────┐
 │       Aspecto       │        FrontController        │         WebRouter         │
 ├─────────────────────┼───────────────────────────────┼───────────────────────────┤
 │ Filosofía           │ Convention over Configuration │ Explicit Configuration    │
 ├─────────────────────┼───────────────────────────────┼───────────────────────────┤
 │ Registro de rutas   │ Automático (descubrimiento)   │ Manual/Explícito          │
 ├─────────────────────┼───────────────────────────────┼───────────────────────────┤
 │ Wildcards           │ Implícito (catch-all)         │ No soportado              │
 ├─────────────────────┼───────────────────────────────┼───────────────────────────┤
 │ Resolución dinámica │ Sí (namespace + controlador)  │ No (rutas pre-compiladas) │
 └─────────────────────┴───────────────────────────────┴───────────────────────────┘
 2. WebRouter NO soporta wildcards

 - El WebRouter requiere rutas pre-definidas
 - Soporta parámetros dinámicos {id} pero NO wildcards * o **
 - No hay mecanismo de catch-all configurable

 3. Volumen de Rutas

 Para replicar la funcionalidad, habría que registrar manualmente:
 // Para CADA recurso API (ej: products, users, orders, etc.)
 WebRouter::get('api/v1/products', 'api\Products@get');
 WebRouter::get('api/v1/products/{id}', 'api\Products@get');
 WebRouter::post('api/v1/products', 'api\Products@post');
 WebRouter::put('api/v1/products/{id}', 'api\Products@put');
 WebRouter::patch('api/v1/products/{id}', 'api\Products@patch');
 WebRouter::delete('api/v1/products/{id}', 'api\Products@delete');

 Problema: Con 100+ recursos API, esto son 600+ líneas de código repetitivo.

 Solución Propuesta: Enfoque Híbrido

 Estrategia: Agregar Wildcard Support al WebRouter

 En lugar de migrar TODO al WebRouter, extender el WebRouter para soportar wildcards y permitir que maneje rutas API automáticas coexistiendo con el
 FrontController.

 Implementación en 3 Fases

 ---
 FASE 1: Arreglar Pruebas Unitarias (5-10 min)

 Archivos a modificar:

 - phpunit.xml

 Cambios:

 1. Remover la exclusión del grupo refactor en líneas 22-26
 2. Ejecutar pruebas para verificar estado actual
 3. Identificar pruebas fallidas (si existen)

 Resultado esperado:

 - Las pruebas se ejecutan correctamente
 - Conocemos el baseline de pruebas pasando/fallando

 ---
 FASE 2: Análisis de Viabilidad Detallada (30-45 min)

 Opción A: Agregar Wildcard Support al WebRouter

 Objetivo: Permitir rutas catch-all en WebRouter similar a otros frameworks.

 Archivos a modificar:
 - src/Core/WebRouter.php

 Cambios requeridos:

 1. Agregar soporte de wildcards en definición de rutas:
 WebRouter::get('api/{version}/{resource}/*', 'ApiWildcardHandler');
 2. Modificar compile() para manejar wildcards:
   - Detectar patrón * o **
   - Compilar a regex que capture todo: .*
   - Almacenar en nuevo array $wildcardRoutes
 3. Modificar resolve() para evaluar wildcards:
   - Primero buscar rutas exactas/paramétricas (comportamiento actual)
   - Si no encuentra, evaluar rutas wildcard
   - Pasar control a handler especializado
 4. Crear ApiWildcardHandler:
   - Reutiliza lógica del ApiHandler actual
   - Se invoca cuando WebRouter detecta ruta wildcard API
   - Mantiene toda la funcionalidad de endpoints automáticos

 Ventajas:
 - ✅ WebRouter gana capacidad de wildcards (útil para otros casos)
 - ✅ Endpoints automáticos pueden servirse desde WebRouter
 - ✅ Permite deshabilitar FrontController gradualmente
 - ✅ No rompe compatibilidad existente

 Desventajas:
 - ⚠️ Requiere modificación al core del WebRouter
 - ⚠️ Complejidad adicional en compile() y resolve()
 - ⚠️ Riesgo de bugs en lógica de matching

 Complejidad: Media-Alta
 Tiempo estimado: 2-3 horas

 Opción B: Router Registration Helper

 Objetivo: Crear un helper que auto-registre rutas API en WebRouter.

 Archivos a crear:
 - src/Core/Helpers/ApiRouterHelper.php

 Cambios requeridos:

 1. Crear helper de auto-registro:
 class ApiRouterHelper {
     public static function registerApiResources(array $resources, string $version = 'v1') {
         foreach ($resources as $resource) {
             $controller = "api\\{$resource}";
             WebRouter::get("api/{$version}/{$resource}", "{$controller}@get");
             WebRouter::get("api/{$version}/{$resource}/{id}", "{$controller}@get");
             WebRouter::post("api/{$version}/{$resource}", "{$controller}@post");
             WebRouter::put("api/{$version}/{$resource}/{id}", "{$controller}@put");
             WebRouter::patch("api/{$version}/{$resource}/{id}", "{$controller}@patch");
             WebRouter::delete("api/{$version}/{$resource}/{id}", "{$controller}@delete");
         }
     }
 }
 2. Modificar config/routes.php:
 ApiRouterHelper::registerApiResources([
     'products',
     'users',
     'orders',
     // ... lista de recursos
 ]);
 3. Opcionalmente auto-descubrir controladores:
 ApiRouterHelper::autoRegisterControllers('app/Controllers/api/');

 Ventajas:
 - ✅ No modifica el core del WebRouter
 - ✅ Fácil de implementar y testear
 - ✅ Explícito y predecible
 - ✅ Bajo riesgo de bugs

 Desventajas:
 - ⚠️ Requiere mantenimiento de lista de recursos
 - ⚠️ Auto-discovery puede ser lento en producción
 - ⚠️ Duplica lógica de resolución de controladores

 Complejidad: Baja
 Tiempo estimado: 1-2 horas

 Opción C: Mantener Status Quo

 Objetivo: Mantener FrontController habilitado junto con WebRouter.

 Cambios: Ninguno

 Configuración actual en config/config.php:
 'web_router'       => true,   // Rutas explícitas
 'console_router'   => true,   // Comandos CLI
 'front_controller' => true,   // Endpoints automáticos

 Ventajas:
 - ✅ Zero risk - no hay cambios
 - ✅ Sistema probado en producción
 - ✅ Separation of concerns:
   - WebRouter → Rutas personalizadas
   - FrontController → Rutas API automáticas
 - ✅ Flexible: Se puede deshabilitar por package

 Desventajas:
 - ⚠️ Tres sistemas de routing activos
 - ⚠️ Overhead mínimo (3 resolvers en cascada)
 - ⚠️ Complejidad conceptual para nuevos desarrolladores

 Complejidad: Ninguna
 Tiempo estimado: 0 horas

 ---
 RECOMENDACIÓN

 Mantener arquitectura actual (Opción C) por las siguientes razones:

 1. Diseño Superior
   - El sistema actual implementa Separation of Concerns correctamente
   - FrontController = Convention over Configuration (ideal para APIs REST)
   - WebRouter = Explicit Configuration (ideal para rutas especiales)
 2. Ya existe solución para deshabilitar FrontController
   - Se puede deshabilitar globalmente en config/config.php
   - Se puede deshabilitar por package en packages/vendor/package/config/config.php
 return [
     'front_controller' => false,  // ← Deshabilita para este package
     'web_router' => true,
 ];
 3. Performance
   - Overhead es mínimo: 3 if statements en cascada
   - WebRouter hace exit si resuelve (no continúa a FrontController)
   - FrontController solo se ejecuta si WebRouter no resolvió
 4. Duplicación de lógica
   - Cualquier otra opción duplica lo que ya existe
   - Agregar wildcards al WebRouter es reinventar el FrontController
 5. Mantenibilidad
   - El código actual está bien estructurado y documentado
   - Modificar el WebRouter agrega complejidad innecesaria
   - Helper de auto-registro requiere mantenimiento manual

 Si aún se requiere deshabilitar FrontController:

 Usar rutas explícitas en WebRouter para endpoints críticos:

 // config/routes.php
 WebRouter::fromArray([
     'GET:/api/v1/products' => 'api\Products@get',
     'GET:/api/v1/products/{id}' => 'api\Products@get',
     'POST:/api/v1/products' => 'api\Products@post',
     // ... solo para recursos críticos
 ]);

 // Deshabilitar FrontController
 // config/config.php
 'front_controller' => false

 Esto funciona hoy mismo sin modificaciones al framework.

 ---
 Plan de Ejecución Final

 FASE 1: Arreglar Pruebas (EJECUTAR)

 Archivo: phpunit.xml

 1. Remover líneas 22-26:
 <!-- ELIMINAR ESTO -->
 <groups>
     <exclude>
         <group>refactor</group>
     </exclude>
 </groups>
 2. Ejecutar pruebas:
 vendor\bin\phpunit tests\ApiTest.php
 3. Verificar que se ejecutan todas las pruebas

 Tiempo: 5 minutos

 FASE 2: Validar que Endpoints Funcionan (EJECUTAR)

 Tests manuales:

 1. Con FrontController habilitado:
 curl "http://simplerest.lan/api/v1/products?fields=id,name,cost&cost[between]=49,100&order[cost]=ASC"
 2. Deshabilitar FrontController en config/config.php:
 'front_controller' => false
 3. Probar mismo endpoint → debería fallar
 4. Agregar rutas explícitas en config/routes.php:
 WebRouter::get('api/v1/products', 'api\Products@get');
 WebRouter::get('api/v1/products/{id}', 'api\Products@get');
 5. Probar endpoint nuevamente → debería funcionar

 Tiempo: 15 minutos

 FASE 3: Documentación (EJECUTAR)

 Actualizar docs con hallazgos:

 1. Documentar que FrontController es necesario para endpoints automáticos
 2. Explicar trade-off entre FrontController (automático) vs WebRouter (explícito)
 3. Mostrar cómo deshabilitar selectivamente por package

 Tiempo: 10 minutos

 ---
 Archivos Críticos

 A Modificar:

 - phpunit.xml - Remover exclusión de grupo refactor

 A Revisar (no modificar):

 - src/Core/WebRouter.php - Entender limitaciones
 - src/Core/FrontController.php - Entender arquitectura
 - src/Core/Handlers/ApiHandler.php - Lógica de resolución API
 - config/config.php - Configuración de routers
 - config/routes.php - Rutas explícitas

 Documentación:

 - docs/Routing.md - Ya documenta todo correctamente

 ---
 Verificación End-to-End

 1. Pruebas Unitarias

 vendor\bin\phpunit tests\ApiTest.php
 Esperado: Todas las pruebas se ejecutan (algunas pueden fallar, eso es OK)

 2. Endpoint Automático (FrontController habilitado)

 curl "http://simplerest.lan/api/v1/products?fields=id,name,cost&cost[between]=49,100&order[cost]=ASC"
 Esperado: JSON con productos filtrados

 3. Endpoint Explícito (FrontController deshabilitado, WebRouter habilitado)

 // config/config.php
 'front_controller' => false,

 // config/routes.php
 WebRouter::get('api/v1/products', 'api\Products@get');
 curl "http://simplerest.lan/api/v1/products"
 Esperado: JSON con productos (filtros complejos pueden no funcionar)

 ---
 Conclusión

 NO se recomienda migrar los endpoints automáticos del FrontController al WebRouter porque:

 1. ✅ El diseño actual es superior (separation of concerns)
 2. ✅ Ya existe forma de deshabilitar FrontController si es necesario
 3. ✅ WebRouter y FrontController son complementarios, no competidores
 4. ✅ Cualquier alternativa duplica código existente o agrega complejidad

 Acción recomendada:
 - Arreglar las pruebas unitarias (Tarea 1) ← EJECUTAR
 - Documentar el trade-off entre FrontController y WebRouter ← EJECUTAR
 - Mantener arquitectura actual ← NO CAMBIAR

 Si el usuario realmente necesita deshabilitar FrontController, puede:
 - Usar rutas explícitas en WebRouter para recursos críticos
 - Aceptar pérdida de funcionalidad de query parameters complejos
 - O implementar Opción B (Helper de auto-registro) como compromiso