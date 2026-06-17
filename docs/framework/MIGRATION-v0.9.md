# Guía de Migración a v0.9.0

Guía para migrar proyectos existentes a SimpleRest Framework v0.9.0.

---

## Cambios Principales

### Reorganización de Estructura

**Antes (v0.8.x)**:
```
simplerest/
└─ app/
   ├─ Core/              # Framework
   ├─ Controllers/
   ├─ Models/
   └─ ...
```

**Después (v0.9.0)**:
```
simplerest/
├─ src/
│  └─ Core/              # Framework (movido desde app/Core)
└─ app/
   ├─ Controllers/
   ├─ Models/
   └─ ...
```

---

## ¿Necesito Migrar?

### ✅ NO requiere cambios si:

- Usas el framework como biblioteca vía Composer
- No modificaste archivos en `app/Core/`
- Solo tienes código de aplicación en `app/`

### ⚠️ Requiere atención si:

- Tienes referencias hardcoded a rutas físicas de `app/Core/`
- Modificaste archivos del Core directamente
- Tienes scripts que dependen de la estructura de directorios

---

## Pasos de Migración

### 1. Actualizar Composer

Si estás usando SimpleRest como dependencia:

```bash
composer update boctulus/simplerest
```

### 2. Verificar Referencias a Rutas

Buscar referencias hardcoded a la antigua ubicación:

```bash
# En Linux/Mac
grep -r "app/Core" --include="*.php" .

# En Windows
findstr /s /i "app\\Core" *.php
```

**Reemplazar**:
- `app/Core/` → `src/Core/`
- `app\\Core\\` → `src\\Core\\`

### 3. Actualizar config/autoload.php

Si tienes un fork o instalación local, actualizar las rutas:

```php
// Antes
'include' => [
    __DIR__ . '/../app/Core/Helpers',
    __DIR__ . '/../app/Helpers',
],

// Después
'include' => [
    __DIR__ . '/../src/Core/Helpers',
    __DIR__ . '/../app/Helpers',
],
```

### 4. Verificar Custom Autoloaders

Si tienes autoloaders personalizados, verificar que apunten a `src/Core/` en lugar de `app/Core/`.

### 5. Ejecutar Tests

```bash
php runalltests.php
```

Verificar que el resultado sea:
```
OVERALL RESULT: SUCCESS
All tests passed!
```

---

## Compatibilidad de Namespaces

### ✅ Sin Cambios

Los namespaces **NO cambiaron**:

```php
// Sigue funcionando exactamente igual
use Boctulus\Simplerest\Core\Libs\DB;
use Boctulus\Simplerest\Core\Response;
use Boctulus\Simplerest\Core\Request;
```

Solo cambió el **path físico** de los archivos.

---

## Cambios en Composer

### composer.json

**Antes**:
```json
{
  "type": "project",
  "autoload": {
    "psr-4": {
      "Boctulus\\Simplerest\\": "app/"
    }
  }
}
```

**Después**:
```json
{
  "type": "library",
  "autoload": {
    "psr-4": {
      "Boctulus\\Simplerest\\": "src/"
    }
  },
  "autoload-dev": {
    "psr-4": {
      "Boctulus\\Simplerest\\Tests\\": "tests/",
      "Boctulus\\Simplerest\\": "app/"
    }
  }
}
```

### Regenerar Autoload

Después de cualquier cambio en `composer.json`:

```bash
composer dumpautoload
```

---

## Migraciones Específicas

### Scripts Personalizados

Si tienes scripts que referencian archivos del Core:

```php
// Antes
require_once __DIR__ . '/app/Core/Libs/DB.php';

// Después
require_once __DIR__ . '/src/Core/Libs/DB.php';
```

O mejor aún, usar autoload de Composer:

```php
require_once __DIR__ . '/vendor/autoload.php';

use Boctulus\Simplerest\Core\Libs\DB;
```

### Configuración de Servidor

Si tienes configuración específica del servidor (Apache, Nginx) que referencia `app/Core/`, actualizar a `src/Core/`.

**Ejemplo Apache .htaccess**:
```apache
# Normalmente no es necesario cambiar nada
# El entry point sigue siendo public/index.php
```

### CI/CD Pipelines

Actualizar scripts de CI/CD si referencian la estructura antigua:

```yaml
# Antes
- name: Run tests
  run: php app/Core/test-runner.php

# Después
- name: Run tests
  run: php runalltests.php
```

---

## Verificación Post-Migración

### Checklist

- [ ] Todas las pruebas pasan (`php runalltests.php`)
- [ ] La aplicación carga correctamente
- [ ] No hay errores de autoload
- [ ] Las rutas funcionan correctamente
- [ ] Los comandos CLI funcionan (`php com help`)
- [ ] Migraciones de BD funcionan
- [ ] API endpoints responden correctamente

### Comandos de Verificación

```bash
# 1. Verificar autoload
composer dumpautoload

# 2. Ejecutar tests
php runalltests.php

# 3. Verificar comandos CLI
php com help

# 4. Verificar rutas web
php com web-router:list
```

---

## Problemas Comunes

### Error: "Class not found"

**Causa**: Autoload no regenerado o referencias incorrectas.

**Solución**:
```bash
composer dumpautoload
```

### Error: "File not found" en helpers

**Causa**: `config/autoload.php` apunta a la ruta antigua.

**Solución**: Actualizar `config/autoload.php` según la sección 3.

### Error: DirectoryIterator en app.php

**Causa**: El archivo `app.php` intenta cargar helpers desde `app/Core/Helpers`.

**Solución**: Ya debería estar actualizado en la nueva versión. Si no, actualizar manualmente.

---

## Rollback

Si necesitas volver a la versión anterior:

```bash
# 1. Revertir cambios en git
git checkout v0.8.12

# 2. O restaurar composer.json
composer require boctulus/simplerest:0.8.12

# 3. Regenerar autoload
composer dumpautoload
```

---

## Soporte

Si encuentras problemas durante la migración:

1. Revisar el [Changelog](./CHANGELOG.md)
2. Consultar la [Arquitectura](./Framework-Architecture.md)
3. Verificar [Issues conocidos](./issues/)
4. Abrir un issue en el repositorio

---

## Próximos Cambios

En futuras versiones (v0.10+):

- Migración de `app/Modules/` → `modules/`
- Creación de `examples/`
- Publicación en Packagist
- Skeleton `simplerest-app` para nuevos proyectos

---

**Fecha de actualización**: 2026-01-24
**Autor**: Pablo Bozzolo (boctulus)
**Software Architect**
