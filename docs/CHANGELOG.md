# Changelog - SimpleRest Framework

Registro de cambios importantes del framework.

---

## [1.0.0] - 2026-05-15

### ⚡ Breaking Change — PHP 8.1 Mínimo Requerido

**Motivo**: El framework ahora utiliza constructor promotion con `readonly` properties (ej. `PermissionExplanation`), lo cual requiere PHP 8.1+.

#### Cambios

- **composer.json**: `"php":">=7.4,<8.5"` → `">=8.1,<8.5"`
- **composer.json**: `"version":"0.9.0"` → `"1.0.0"`

#### Impacto

- ❌ PHP 7.4 – 8.0 ya no son soportados
- ✅ PHP 8.1 – 8.4 soportados

---

## [0.9.0] - 2026-01-24

### 🏗️ Reorganización de Arquitectura - Framework Core Desacoplado

**Objetivo**: Separar el framework (Core) de la aplicación/playground, preparando el proyecto para releases limpios y futura publicación vía Composer.

#### Cambios Estructurales

**Nueva Estructura de Directorios**:

```
simplerest/
├─ src/
│  └─ Core/              # Framework puro (código del Core)
│     ├─ API/
│     ├─ Controllers/
│     ├─ Exceptions/
│     ├─ Handlers/
│     ├─ Helpers/
│     ├─ Interfaces/
│     ├─ Libs/
│     ├─ Psr7/
│     ├─ Templates/
│     └─ Traits/
│
├─ app/                  # Consumer code / Playground / Dogfooding
│  ├─ Background/
│  ├─ Commands/
│  ├─ Controllers/
│  ├─ DTO/
│  ├─ Helpers/
│  ├─ Interfaces/
│  ├─ Libs/
│  ├─ Locale/
│  ├─ Middlewares/
│  ├─ Models/
│  ├─ Modules/
│  ├─ Pages/
│  ├─ Schemas/
│  ├─ Traits/
│  ├─ Transformers/
│  └─ Views/
│
├─ modules/              # Funcionalidades opcionales (futuro)
├─ examples/             # Demos y ejemplos (futuro)
├─ packages/             # Packages locales
├─ config/
├─ public/
├─ database/
├─ scripts/
├─ tests/
└─ vendor/
```

**Migración Realizada**:
- `app/Core/` → `src/Core/` (Framework puro)
- `app/` ahora contiene únicamente código de aplicación

#### Cambios en composer.json

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
    },
    "exclude-from-classmap": [
      "src/Core/Templates/"
    ]
  },
  "autoload-dev": {
    "psr-4": {
      "Boctulus\\Simplerest\\Tests\\": "tests/",
      "Boctulus\\Simplerest\\": "app/"
    }
  }
}
```

**Cambios clave**:
- `type`: `"project"` → `"library"`
- Framework Core (`src/`) en autoload principal
- Application code (`app/`) en autoload-dev
- Templates excluidos del classmap
- Script `cs` actualizado: `phpstan analyse src --level=7`

#### Archivos de Configuración Actualizados

**config/autoload.php**:
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

#### Principios de la Nueva Arquitectura

| Carpeta   | Propósito                          | Autoload |
|-----------|------------------------------------|----------|
| `src/`    | Framework puro (Core)              | Principal |
| `app/`    | Playground / Dogfooding            | Dev       |
| `modules/`| Funcionalidades opcionales         | Futuro    |
| `examples/`| Demos y ejemplos                  | No        |
| `packages/`| Composer packages locales         | Variable  |

**Regla fundamental**: El framework (src/) NO debe depender de código de aplicación (app/).

#### Testing

✅ **Todas las pruebas unitarias pasan**:
```
OVERALL RESULT: SUCCESS
All tests passed!

Tests executed: 6
Tests passed: 6
Tests failed: 0
```

Tests ejecutados:
- `tests/DB_TransactionTest.php` ✅
- `tests/ModelTest.php` ✅
- `tests/ResponseImmutableMethodsTest.php` ✅
- `tests/ValidatorTest.php` ✅
- `tests/WebRouterTest.php` ✅
- `tests/WebRouterFunctionalTest.php` ✅

#### Compatibilidad

✅ **Backward Compatible**:
- El código existente sigue funcionando sin cambios
- Los namespaces se mantienen: `Boctulus\Simplerest\Core\...`
- Solo cambió el path físico de los archivos del Core

#### Próximos Pasos Planeados

1. Mover módulos opcionales de `app/Modules/` a `modules/`
2. Crear ejemplos de demostración en `examples/`
3. Convertir módulos reales en packages independientes
4. Crear skeleton `simplerest-app` para nuevos proyectos
5. Publicar `boctulus/simplerest-core` en Packagist

#### Referencias

- Plan de trabajo: [`docs/to-do/SimpleRest_Plan_de_Trabajo.md`](./to-do/SimpleRest_Plan_de_Trabajo.md)
- Arquitectura: [`docs/Framework-Architecture.md`](./Framework-Architecture.md)

---

## [0.8.12] - Anterior

Estado previo a la reorganización estructural.

---

**Autor**: Pablo Bozzolo (boctulus)
**Software Architect**
