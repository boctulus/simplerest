# Guía: Emisión de Notas de Crédito (DTE tipo 61)

**Autor:** Pablo Bozzolo (boctulus)
**Fecha:** 2026-01-20
**Package:** friendlypos-web

---

## Índice

1. [Introducción](#introducción)
2. [Problema Original](#problema-original)
3. [Solución Implementada](#solución-implementada)
4. [Uso del CreditNoteHelper](#uso-del-creditnotehelper)
5. [Ejemplos Prácticos](#ejemplos-prácticos)
6. [Testing](#testing)
7. [Troubleshooting](#troubleshooting)

---

## Introducción

Las **Notas de Crédito Electrónicas (DTE tipo 61)** en Chile se utilizan para:
- **Anular** documentos previamente emitidos
- **Corregir montos** de documentos
- **Corregir texto** de documentos

Este documento explica cómo emitir correctamente Notas de Crédito usando el SDK de OpenFactura.

---

## Problema Original

Al intentar emitir una Nota de Crédito con la estructura básica:

```json
{
    "dte": {
        "Encabezado": { ... },
        "Detalle": [ ... ],
        "Referencia": [ ... ]
    },
    "response": ["PDF", "FOLIO", "TIMBRE"]
}
```

Se recibía el error:

```json
{
    "error": {
        "message": "Problema al procesar los datos",
        "code": "OF-22",
        "details": {
            "message": "Ocurrió un error inesperado...",
            "code": 0
        }
    }
}
```

### Causa del Error

La estructura estaba **incompleta**. Según el análisis del plugin oficial de WooCommerce para OpenFactura, las Notas de Crédito requieren campos adicionales:

1. `selfService` - Configuración de autoservicio
2. `customer` - Datos del cliente (opcional)
3. `customizePage` - Personalización (opcional)
4. `custom` - Metadatos como origen

---

## Solución Implementada

Se crearon dos componentes:

### 1. CreditNoteHelper

Helper para construir payloads correctos de Notas de Crédito.

**Ubicación:** `packages/boctulus/friendlypos-web/src/Helpers/CreditNoteHelper.php`

**Funcionalidades:**
- `buildPayload()` - Construye payload completo
- `validate()` - Valida estructura del DTE
- `createFromParams()` - Crea DTE desde parámetros simplificados

### 2. DteDataTransformer (Actualizado)

Se agregó el método `adjustCreditNoteStructure()` para transformar automáticamente los datos de NC.

**Ubicación:** `packages/boctulus/friendlypos-web/src/Helpers/DteDataTransformer.php`

---

## Uso del CreditNoteHelper

### Método 1: Construcción Manual del DTE

```php
use Boctulus\FriendlyposWeb\Helpers\CreditNoteHelper;
use Boctulus\OpenfacturaSdk\Factory\OpenFacturaSDKFactory;

// 1. Crear estructura DTE manualmente
$dteData = [
    'Encabezado' => [
        'IdDoc' => [
            'TipoDTE' => 61,
            'FchEmis' => '2026-01-20',
            'IndNoRebaja' => 1 // Si es anulación (no rebaja stock)
        ],
        'Emisor' => [
            'RUTEmisor' => '76795561-8',
            'RznSoc' => 'MI EMPRESA SPA',
            'GiroEmis' => 'COMERCIO',
            'Acteco' => 479110,
            'DirOrigen' => 'Calle Ejemplo 123',
            'CmnaOrigen' => 'Santiago'
        ],
        'Receptor' => [
            'RUTRecep' => '12345678-9',
            'RznSocRecep' => 'CLIENTE SPA',
            'GiroRecep' => 'GIRO DEL CLIENTE',
            'DirRecep' => 'Dirección Cliente',
            'CmnaRecep' => 'Santiago'
        ],
        'Totales' => [
            'MntNeto' => 84,
            'TasaIVA' => 19,
            'IVA' => 16,
            'MntTotal' => 100
        ]
    ],
    'Detalle' => [
        [
            'NroLinDet' => 1,
            'NmbItem' => 'Producto a devolver',
            'QtyItem' => 1,
            'PrcItem' => 84,
            'MontoItem' => 84
        ]
    ],
    'Referencia' => [
        [
            'NroLinRef' => 1,
            'TpoDocRef' => 39,      // Tipo de doc que se anula (33=Factura, 39=Boleta)
            'FolioRef' => 631563,    // Folio del doc a anular
            'FchRef' => '2026-01-17',
            'CodRef' => 1,          // 1=Anula, 2=Corrige monto, 3=Corrige texto
            'RazonRef' => 'Anulación de documento',
            'IndGlobal' => 1        // Opcional
        ]
    ]
];

// 2. Validar
$validation = CreditNoteHelper::validate($dteData);
if (!$validation['valid']) {
    foreach ($validation['errors'] as $error) {
        echo "Error: $error\n";
    }
    exit(1);
}

// 3. Construir payload completo
$payload = CreditNoteHelper::buildPayload($dteData, [
    'responseOptions' => ['PDF', 'FOLIO', 'TIMBRE'],
    'customer' => [
        'fullName' => 'Nombre del Cliente',
        'email' => 'cliente@email.com'
    ],
    'origin' => 'MI_SISTEMA'
]);

// 4. Emitir
$sdk = OpenFacturaSDKFactory::make($apiKey, $sandbox);
$response = $sdk->emitirDTE(
    $payload['dte'],
    $payload['response'],
    $payload['custom'] ?? null,
    null,
    'idempotency_key_' . time()
);
```

### Método 2: Usando createFromParams() (Recomendado)

```php
use Boctulus\FriendlyposWeb\Helpers\CreditNoteHelper;

// 1. Crear DTE desde parámetros simplificados
$params = [
    'fechaEmision' => date('Y-m-d'),
    'emisor' => [
        'RUTEmisor' => '76795561-8',
        'RznSoc' => 'MI EMPRESA SPA',
        'GiroEmis' => 'COMERCIO',
        'Acteco' => 479110,
        'DirOrigen' => 'Calle Ejemplo 123',
        'CmnaOrigen' => 'Santiago'
    ],
    'receptor' => [
        'RUTRecep' => '12345678-9',
        'RznSocRecep' => 'CLIENTE SPA',
        'GiroRecep' => 'GIRO DEL CLIENTE',
        'DirRecep' => 'Dirección Cliente',
        'CmnaRecep' => 'Santiago'
    ],
    'totales' => [
        'MntNeto' => 84,
        'TasaIVA' => 19,
        'IVA' => 16,
        'MntTotal' => 100
    ],
    'items' => [
        [
            'NmbItem' => 'Producto a devolver',
            'QtyItem' => 1,
            'PrcItem' => 84,
            'MontoItem' => 84
        ]
    ],
    'referencia' => [
        'TpoDocRef' => 39,
        'FolioRef' => 631563,
        'FchRef' => '2026-01-17',
        'CodRef' => 1,
        'RazonRef' => 'Anulación de documento por solicitud del cliente',
        'IndGlobal' => 1
    ],
    'indNoRebaja' => true
];

$dteData = CreditNoteHelper::createFromParams($params);

// 2. Construir payload y emitir (igual que método 1)
// ...
```

---

## Ejemplos Prácticos

### Ejemplo 1: Anular una Boleta Electrónica

```php
$params = [
    'emisor' => [...],
    'receptor' => [...],
    'totales' => [...],
    'items' => [...],
    'referencia' => [
        'TpoDocRef' => 39,      // Boleta
        'FolioRef' => 123456,
        'FchRef' => '2026-01-15',
        'CodRef' => 1,          // Anula
        'RazonRef' => 'Cliente devuelve producto - Devolución de producto'
    ],
    'indNoRebaja' => true
];

$dteData = CreditNoteHelper::createFromParams($params);
```

### Ejemplo 2: Corregir Monto de Factura

```php
$params = [
    // ... mismos campos base
    'referencia' => [
        'TpoDocRef' => 33,      // Factura
        'FolioRef' => 789012,
        'FchRef' => '2026-01-10',
        'CodRef' => 2,          // Corrige monto
        'RazonRef' => 'Corrección de monto facturado'
    ],
    'indNoRebaja' => false      // Rebaja stock
];
```

### Ejemplo 3: Múltiples Referencias

```php
$params = [
    // ... campos base
    'referencia' => [
        [
            'TpoDocRef' => 33,
            'FolioRef' => 111,
            'FchRef' => '2026-01-01',
            'CodRef' => 1,
            'RazonRef' => 'Ref 1'
        ],
        [
            'TpoDocRef' => 33,
            'FolioRef' => 222,
            'FchRef' => '2026-01-02',
            'CodRef' => 1,
            'RazonRef' => 'Ref 2'
        ]
    ]
];
```

---

## Testing

### Configuración del Ambiente (Sandbox vs Producción)

Todos los scripts de testing leen la configuración del archivo `.env` para determinar si usar sandbox o producción:

```env
# .env
OPENFACTURA_SANDBOX=true                              # true = Sandbox, false = Producción
OPENFACTURA_API_KEY_DEV="928e15a2d14d4a..."          # API Key de Desarrollo (Sandbox)
OPENFACTURA_API_KEY_PROD="04f1d39392684b..."         # API Key de Producción
```

**Para cambiar entre modos:**
1. **Sandbox (Desarrollo):** `OPENFACTURA_SANDBOX=true` - Usa `OPENFACTURA_API_KEY_DEV` y `https://dev-api.haulmer.com`
2. **Producción:** `OPENFACTURA_SANDBOX=false` - Usa `OPENFACTURA_API_KEY_PROD` y `https://api.haulmer.com`

⚠️ **IMPORTANTE:** Asegúrate de tener configuradas ambas API keys en `.env` antes de ejecutar los tests.

### Opción 1: Script PHP

```bash
php tests/test_credit_note_emit.php
```

Este script:
- ✅ Lee configuración de `.env` (OPENFACTURA_SANDBOX)
- ✅ Usa la API key correspondiente al modo
- ✅ Crea DTE usando `createFromParams()`
- ✅ Valida la estructura
- ✅ Construye payload completo
- ✅ Emite a OpenFactura (sandbox o producción según .env)
- ✅ Guarda logs en `logs/`

### Opción 2: CURL (Linux/Mac)

```bash
bash tests/test_credit_note_curl.sh
```

Lee `.env` automáticamente y usa el modo configurado.

### Opción 3: PowerShell (Windows)

```powershell
powershell -File tests\test_credit_note_curl.ps1
```

Lee `.env` automáticamente y usa el modo configurado.

### Opción 4: CURL Manual

```bash
curl -X POST "http://simplerest.lan/api/v1/openfactura/dte/emit" \
  -H "Content-Type: application/json" \
  -H "X-Openfactura-Api-Key: TU_API_KEY" \
  -H "X-Openfactura-Sandbox: true" \
  -d '{
    "dteData": {
      "Encabezado": {
        "IdDoc": {
          "TipoDTE": 61,
          "FchEmis": "2026-01-20",
          "IndNoRebaja": 1,
          "RazonAnulacion": "Anulación"
        },
        "Emisor": {...},
        "Receptor": {...},
        "Totales": {...}
      },
      "Detalle": [...],
      "Referencia": [...]
    },
    "responseOptions": ["PDF", "FOLIO"]
  }'
```

---

## Troubleshooting

### Error: "Problema al procesar los datos" (OF-22)

**Causa:** Estructura incompleta

**Solución:** Usar `CreditNoteHelper::buildPayload()` en vez de construir manualmente.

### Error: "Nota de Crédito debe incluir al menos una Referencia"

**Causa:** Falta el campo `Referencia` o está vacío

**Solución:**
```php
'referencia' => [
    'TpoDocRef' => 39,
    'FolioRef' => 123456,
    'FchRef' => '2026-01-15',
    'CodRef' => 1,
    'RazonRef' => 'Razón de la NC'
]
```

### Error: "Este elemento no es esperado" (RazonAnulacion)

**Causa:** El campo `RazonAnulacion` NO debe ir en `IdDoc` según el esquema del SII

**Solución:** La razón de la anulación va en `Referencia->RazonRef`, NO en `IdDoc`:
```php
'referencia' => [
    'TpoDocRef' => 39,
    'FolioRef' => 123456,
    'FchRef' => '2026-01-15',
    'CodRef' => 1,
    'RazonRef' => 'Razón de la anulación'  // ← Aquí va la razón
]
```

### Validar antes de emitir

Siempre valida antes de emitir:

```php
$validation = CreditNoteHelper::validate($dteData);
if (!$validation['valid']) {
    print_r($validation['errors']);
    exit(1);
}
```

---

## Códigos de Referencia (CodRef)

| Código | Significado |
|--------|-------------|
| 1 | **Anula Documento de Referencia** |
| 2 | **Corrige Monto** |
| 3 | **Corrige Texto del Documento** |

---

## Tipos de DTE más comunes a referenciar

| Código | Tipo de Documento |
|--------|-------------------|
| 33 | Factura Electrónica |
| 39 | Boleta Electrónica |
| 52 | Guía de Despacho |
| 56 | Nota de Débito |

---

## Campos Opcionales vs Obligatorios

### Obligatorios para NC

- ✅ `TipoDTE: 61`
- ✅ `FchEmis`
- ✅ `Emisor` (completo)
- ✅ `Receptor` (completo)
- ✅ `Totales`
- ✅ `Detalle` (al menos 1 item)
- ✅ `Referencia` (al menos 1)

### Opcionales pero Recomendados

- `IndNoRebaja` (1 si es anulación, 0 si rebaja) - Va en `IdDoc`
- `RazonRef` (razón de la NC) - Va en `Referencia`, NO en `IdDoc`
- `customer` (para autoservicio)
- `customizePage` (para personalización)

**IMPORTANTE:** `RazonAnulacion` NO es un campo válido en `IdDoc` según el esquema del SII. La razón debe ir en `Referencia->RazonRef`.

---

## Enlaces Útiles

- [Documentación OpenFactura API](https://docsapi-openfactura.haulmer.com/)
- [Formato DTE SII](https://www.sii.cl/factura_electronica/formato_dte.pdf)
- [Plugin WooCommerce OpenFactura](https://github.com/haulmer/woocommerce-openfactura)

---

## Changelog

### v1.0.0 (2026-01-20)
- ✅ Creación de `CreditNoteHelper`
- ✅ Actualización de `DteDataTransformer` para NC
- ✅ Scripts de testing (PHP, Bash, PowerShell)
- ✅ Documentación completa

---

**¿Preguntas o problemas?**
Contacta al autor: Pablo Bozzolo (boctulus)
