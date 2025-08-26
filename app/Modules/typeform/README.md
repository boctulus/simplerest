# Módulo Typeform

## Configuración

### Enlaces Configurables

Puedes configurar los enlaces del formulario editando el archivo:
```
D:\laragon\www\simplerest\app\modules\typeform\config\config.php
```

#### Términos y Condiciones

```php
return [
    "links" => [
        "tos" => "URL_DE_TUS_TERMINOS_Y_CONDICIONES"
    ]
];
```

**Ejemplos de configuración:**

```php
// URL absoluta (recomendado)
"tos" => "https://tudominio.com/terminos-y-condiciones"

// URL relativa al dominio actual
"tos" => "/terminos-y-condiciones"

// URL relativa sin barra inicial
"tos" => "terminos-y-condiciones"

// Deshabilitar enlace (solo para desarrollo)
"tos" => "#"
```

### Funcionalidad

- ✅ **URLs absolutas**: Se usan tal como están configuradas
- ✅ **URLs relativas**: Se convierten automáticamente usando el dominio actual
- ✅ **Validación automática**: El sistema verifica si es URL válida
- ✅ **Fallback seguro**: Si no hay configuración, usa "#" por defecto

### Uso en el formulario

El enlace se muestra en el paso 7 (Revisión Final) del formulario:
```
"Acepto los [términos y condiciones] del servicio"
```

- Se abre en nueva pestaña (`target="_blank"`)
- Funciona con URLs internas y externas
- Totalmente configurable sin modificar código

## Ejemplo de configuración completa

```php
<?php 
return [
    "links" => [
        "tos" => "https://miempresa.cl/terminos-y-condiciones/"
    ]
];
```

Con esta configuración, el enlace quedará como:
```html
<a href="https://miempresa.cl/terminos-y-condiciones/" target="_blank">términos y condiciones</a>
```