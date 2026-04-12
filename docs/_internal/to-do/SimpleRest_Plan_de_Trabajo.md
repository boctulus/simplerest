
# SimpleRest – Plan de trabajo (sin convertir módulos en packages)

## Objetivo
Separar **Framework (Core)** de **Aplicación / Playground**, limpiar Composer y dejar el proyecto listo para:
- releases limpias
- futura publicación vía Composer
- mantener módulos **sin** convertirlos aún en packages

---

## Fase 0 – Principios (no negociables)

- El framework **NO** vive en `app/`
- `app/` pasa a ser *consumer code*
- `src/` contiene **únicamente** el Core
- Los módulos siguen existiendo, pero **no forman parte del framework**
- Composer solo autoload-ea el Core

---

## Fase 1 – Reorganización mínima de directorios

### Estado actual (simplificado)

```
app/
  Core/
  Controllers/
  Modules/
  Views/
```

### Estado objetivo

```
simplerest/
├─ src/
│  └─ Core/
├─ app/
│  ├─ Controllers/
│  ├─ Models/
│  ├─ Views/
│  ├─ Pages/
│  └─ Playground/
├─ modules/
│  ├─ Xeni/
│  ├─ FriendlyPOS/
│  └─ AndroidEngine/
├─ examples/
│  ├─ Countdown/
│  ├─ ProgressBar/
│  └─ StarRating/
├─ config/
├─ public/
├─ database/
├─ scripts/
├─ tests/
├─ composer.json
└─ README.md
```

### Acción concreta

1. Mover:
   ```
   app/Core  →  src/Core
   ```

2. Crear carpetas vacías:
   ```
   modules/
   examples/
   ```

3. (Opcional) mover módulos “demo” a `examples/`

---

## Fase 2 – Namespaces (sin romper nada)

El namespace **NO cambia**:

```php
Boctulus\Simplerest\Core\...
```

Solo cambia el **path físico**.

No se tocan `use`, ni `new`, ni `extends`.

---

## Fase 3 – Composer (pieza clave)

### Nuevo `composer.json`

```json
{
  "name": "boctulus/simplerest",
  "type": "library",
  "description": "API Rest framework for PHP",
  "keywords": ["simplerest", "framework", "boctulus"],
  "license": "MIT",
  "authors": [
    {
      "name": "Pablo Gabriel Bozzolo",
      "email": "boctulus@gmail.com"
    }
  ],
  "require": {
    "php": ">=7.4,<8.4",
    "vlucas/phpdotenv": "^5.2",
    "nikic/php-parser": "^5.4",
    "doctrine/inflector": "^2.0",
    "setasign/fpdf": "^1.8",
    "chillerlan/php-qrcode": "^5.0",
    "psr/http-message": "^2.0"
  },
  "require-dev": {
    "phpunit/phpunit": "^10.0",
    "phpstan/phpstan": "^1.10"
  },
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
      "Boctulus\\Simplerest\\App\\": "app/"
    }
  },
  "scripts": {
    "test": "phpunit --colors=always",
    "cs": "phpstan analyse src --level=7"
  },
  "minimum-stability": "stable",
  "prefer-stable": true
}
```

### Qué cambió (importante)

- `type: project` → `type: library`
- `app/` **sale** del autoload principal
- `src/` es el único código del framework
- módulos NO están en Composer todavía

---

## Fase 4 – Configuración (`config.php`)

No se rompe.

Solo una convención clara:

```php
'providers' => [
    // Framework
    Boctulus\Simplerest\Core\Providers\CoreServiceProvider::class,

    // App / módulos
    App\Providers\AppServiceProvider::class,
    Modules\Xeni\ModuleProvider::class,
]
```

> El framework **no debe depender** de providers de módulos.

---

## Fase 5 – Regla mental definitiva

| Carpeta   | Significado |
|----------|------------|
| `src/`   | Framework puro |
| `app/`   | Playground / dogfooding |
| `modules/` | Funcionalidades opcionales |
| `examples/` | Demos |
| `packages/` | (futuro) Composer packages |

---

## Fase 6 – Release limpio (sin scripts)

```bash
git archive HEAD | tar -x -C release/
```

El release es limpio **por diseño**, no por borrado.

---

## Próximo paso (cuando quieras)

- Convertir módulos reales en packages
- Crear `simplerest-app` skeleton
- Publicar `boctulus/simplerest-core`

---

**Estado final:**  
Framework profesional, dogfooding sin culpa, Composer feliz.
