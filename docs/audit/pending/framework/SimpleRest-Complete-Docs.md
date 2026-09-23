> **Documentation status: historical and unverified.** This comparison document is not a current product specification. Verify every SimpleRest claim against source before reuse; external framework comparisons do not define SimpleRest architecture. See the [documentation audit](../../README.md).

# Documentación Completa de SimpleRest Framework: Características y Comparación con Laravel y Supabase

## Tabla de Contenidos
1. [Introducción](#introducción)
2. [Arquitectura del Framework](#arquitectura-del-framework)
3. [Características Principales](#características-principales)
4. [Comparación con Laravel](#comparación-con-laravel)
5. [Comparación con Supabase](#comparación-con-supabase)
6. [Casos de Uso Recomendados](#casos-de-uso-recomendados)
7. [Conclusión](#conclusión)

---

## Introducción

SimpleRest es un framework PHP modular y extensible con soporte PSR, diseñado para ofrecer una arquitectura desacoplada.

### Filosofía del Framework
- Arquitectura modular y extensible
- Compatibilidad con estándares PSR
- Flexibilidad en patrones de desarrollo (tradicional vs Laravel-like)

---

## Arquitectura del Framework

### Estructura de Directorios

```
simplerest/
├─ src/              # Framework Core (código del framework)
├─ app/              # Application Code (playground/dogfooding)
├─ modules/          # Módulos opcionales
├─ examples/         # Demos y ejemplos
├─ packages/         # Packages locales (composer)
├─ config/           # Configuración
├─ public/           # Assets públicos (index.php, CSS, JS)
├─ database/         # Migraciones y seeders
├─ scripts/          # Scripts de automatización
├─ tests/            # Pruebas unitarias
├─ vendor/           # Dependencias de Composer
├─ composer.json     # Configuración de Composer
└─ app.php           # Bootstrap de la aplicación
```

### Componentes Principales

#### src/ - Framework Core
- **Namespace**: `Boctulus\Simplerest\Core\`
- **Autoload**: Principal (type: `library`)
- Contiene el núcleo del framework con controladores base, handlers, helpers, interfaces, bibliotecas, etc.

#### app/ - Application Code
- **Namespace**: `Boctulus\Simplerest\*`
- **Autoload**: Dev (autoload-dev)
- Código de aplicación específica, modelos, controladores, vistas, etc.

#### packages/ - Packages Locales
- Packages desarrollados localmente que pueden publicarse independientemente
- Cada package tiene su propio `composer.json`
- Pueden publicarse independientemente
- Siguen PSR-4
- Reutilizables entre proyectos

---

## Características Principales

### 1. PSR Compliance

SimpleRest soporta estándares PSR para mejorar la interoperabilidad con el ecosistema PHP moderno:

- ✅ **PSR-7**: HTTP Message Interfaces (via adapters + métodos nativos)
- ✅ **Immutability**: Métodos inmutables `with*()` en Request y Response
- 📋 **PSR-17**: HTTP Factories (planeado)
- 📋 **PSR-15**: HTTP Server Request Handlers (planeado)

**Compliance**: 95% PSR-7 compatible

### 2. Query Builder Avanzado

El OQuery Builder de SimpleRest combina la potencia del Query Builder con la simplicidad de patrones Active Record:

#### Modo Tradicional vs Laravel-like
- **SimpleRest**: Trabaja exclusivamente con **instancias de modelos**
- **No usa métodos estáticos** (como `Model::where()`)
- Siempre se debe crear una instancia del modelo primero

```php
// ✓ CORRECTO: Crear instancia conectada a la BD
$userModel = new User(true);

// Consultas
$users = $userModel->where('active', 1)
                   ->orderBy('created_at', 'DESC')
                   ->limit(10)
                   ->get();
```

#### Operaciones CRUD
- Create, Read, Update, Delete con múltiples opciones
- Soporte para múltiples registros
- Soft deletes
- Validación integrada

#### Consultas Avanzadas
- WHERE, OR WHERE, WHERE IN, WHERE NOT IN, WHERE NULL, etc.
- Agrupaciones y agregaciones (COUNT, MAX, MIN, AVG, SUM)
- Joins automáticos y manuales

### 3. Sistema de Joins Automáticos (Característica Superior)

#### JOINs Automáticos (con Schemas)
Usa `connectTo()` para JOINs automáticos basados en relaciones del schema:

```php
// JOIN automático usando schema
$results = $userModel->connectTo(['profiles'])
                     ->where('users.active', 1)
                     ->get();

// SimpleRest automáticamente genera:
// LEFT JOIN profiles ON users.id = profiles.user_id

// Múltiples JOINs automáticos
$results = $userModel->connectTo(['profiles', 'roles'])
                     ->get();
```

**Ventajas:**
- ✅ No necesitas especificar las columnas de relación
- ✅ Automáticamente califica los campos para evitar ambigüedad
- ✅ Funciona con relaciones n:m (tablas pivot)
- ✅ Más mantenible - cambios en schema se reflejan automáticamente

### 4. Relaciones Automáticas con Schemas

SimpleRest tiene un sistema de relaciones automáticas cuando se utilizan **schemas**. El framework detecta automáticamente relaciones 1:1, 1:n, n:1 y n:m basándose en las claves foráneas definidas en los schemas.

#### Definición de Relaciones en Schemas
```php
'relationships' => [
    'posts' => [
        ['users.id', 'posts.user_id']  // users tiene muchos posts
    ],
    'profile' => [
        ['users.id', 'profiles.user_id']  // users tiene un profile
    ]
],

'expanded_relationships' => [
    'posts' => [
        [
            ['users', 'id'],
            ['posts', 'user_id']
        ]
    ],
    'profile' => [
        [
            ['users', 'id'],
            ['profiles', 'user_id']
        ]
    ]
],
```

#### Cargar Relaciones (Eager Loading)
```php
// Cargar usuarios con sus posts (JOIN automático)
$users = $userModel->connectTo(['posts'])
                   ->get();
```

### 5. Routing Flexible

#### WebRouter
- Soporta verbos HTTP: GET, POST, PUT, PATCH, DELETE, OPTIONS
- Soporte para rutas con parámetros
- Grupos de rutas
- Funciones anónimas
- **Ordenamiento automático** de rutas (más específica a más general)

#### CliRouter
- Soporta comandos de consola
- Comandos multi-palabra
- Soporte de métodos mágicos `__call()`
- Grupos de comandos

#### Routing en Packages y Modules
- Soporte para rutas definidas en packages
- Soporte para módulos autocontenidos
- Configuración específica por package

### 6. ApiClient

Abstracción sobre las funciones curl con manejo de diferentes tipos de autenticación:

```php
$client = new ApiClient($url);

$res = $client
    ->get()
    ->getResponse(false);
```

- Soporte para diferentes métodos HTTP
- Manejo de headers
- Autenticación básica y JWT
- Seguimiento de redirecciones
- Caché de respuestas
- Soporte para descarga de archivos
- Mocking para pruebas

### 7. Handlers Modulares

Arquitectura de handlers que separa responsabilidades:
- **RequestHandler**: Parsea requests HTTP/CLI
- **ApiHandler**: Maneja rutas `/api/*`
- **AuthHandler**: Procesa rutas `/auth`
- **OutputHandler**: Formatea respuestas
- **MiddlewareHandler**: Ejecuta middlewares
- **ErrorHandler**: Manejo centralizado de errores

---

## Comparación con Laravel

### Ventajas de SimpleRest sobre Laravel

#### 1. Sistema de Joins Automáticos Superior
**SimpleRest**:
- `connectTo()` para JOINs automáticos basados en schemas
- No requiere especificar manualmente las condiciones de JOIN
- Calificación automática de campos para evitar ambigüedad
- Soporte para relaciones complejas (n:m) sin configuración adicional

**Laravel**:
- Requiere especificar manualmente las condiciones de JOIN
- `join('profiles', 'users.id', 'profiles.user_id')`
- Más verboso para relaciones complejas

#### 2. Query Builder con Integración de Schema
**SimpleRest**:
- Sistema basado en schemas que define relaciones una vez
- Aplicación automática de relaciones en todas las consultas
- Menos código necesario para consultas complejas

**Laravel**:
- Relaciones definidas en modelos individuales
- Más código repetitivo para consultas complejas

#### 3. Flexibilidad en Patrones de Desarrollo
**SimpleRest**:
- Soporta tanto patrones tradicionales como Laravel-like
- Mayor flexibilidad en el enfoque de desarrollo
- Control explícito sobre conexiones y multi-tenant

**Laravel**:
- Más opiniado, sigue patrones establecidos
- Menos flexibilidad en ciertos aspectos

#### 4. Arquitectura Modular
**SimpleRest**:
- Separación clara entre core y aplicación
- Soporte nativo para packages y módulos
- Menos acoplamiento entre componentes

**Laravel**:
- Arquitectura más integrada
- Más difícil de desacoplar componentes

---

## Comparación con Supabase

### Diferencias Fundamentales

**SimpleRest**:
- Endpoints automaticos
- Control total sobre la infraestructura
- Lógica de negocio completamente personalizable
- Despliegue flexible

**Supabase**:
- Backend-as-a-Service (BaaS)
- Solución hospedada basada en PostgreSQL
- APIs auto-generadas
- Servicio en la nube

### Ventajas de SimpleRest sobre Supabase

#### 1. Control Total
- Control completo sobre la lógica de negocio
- Sin vendor lock-in
- Personalización ilimitada
- Control sobre la infraestructura
- Más expresivo

#### 2. Flexibilidad de Desarrollo
- Arquitectura personalizable
- Integración con cualquier servicio externo
- Sin limitaciones impuestas por la plataforma

#### 3. Costos Predecibles
- Costos basados en infraestructura elegida
- Sin tarifas variables por uso
- Mayor control sobre gastos

---

## Casos de Uso Recomendados

### SimpleRest es ideal para:

1. **Aplicaciones empresariales complejas** que requieren lógica de negocio sofisticada
2. **Proyectos que necesitan control total** sobre la arquitectura y la infraestructura
3. **Desarrollo de APIs** con requerimientos específicos de consulta
4. **Proyectos con equipos de desarrollo PHP** experimentados
5. **Situaciones donde se requiere integración con sistemas legados**
6. **Aplicaciones que necesitan JOINs complejos** con automatización

---

## Conclusión

SimpleRest representa un framework PHP profesional y sofisticado con características únicas que lo distinguen de otros frameworks. Su sistema de JOINs automáticos a través de `connectTo()` y la integración con schemas para relaciones automáticas es una innovación significativa que puede ofrecer ventajas sustanciales en ciertos escenarios de desarrollo.

### Puntos Fuertes de SimpleRest:

1. **Sistema de JOINs Automáticos**: La funcionalidad `connectTo()` es genuinamente innovadora y puede simplificar drásticamente consultas complejas
2. **Arquitectura Modular**: Separación clara entre core y aplicación con soporte para packages y módulos
3. **PSR Compliance**: Cumple con estándares PSR para interoperabilidad
4. **Flexibilidad**: Soporta múltiples enfoques de desarrollo
5. **Automatización Inteligente**: Generación automática de schemas y relaciones

### Consideraciones:

- **Curva de Aprendizaje**: Menos documentación y recursos de aprendizaje disponibles
- **Ecosistema**: Menor cantidad de paquetes de terceros
- **Comunidad**: Comunidad más pequeña comparada con Laravel
- **Madurez**: Framework más reciente, menor historial de uso en producción

SimpleRest es especialmente valioso para desarrolladores que necesitan un sistema de consultas sofisticado con automatización de relaciones, control total sobre la arquitectura, y la flexibilidad de trabajar con diferentes enfoques de desarrollo. Su sistema de JOINs automáticos y la integración con schemas lo posicionan como una opción superior en escenarios donde las consultas complejas son comunes.

**Autor**: Pablo Bozzolo (boctulus)  
**Software Architect**
