# OpenFactura SDK

SDK de PHP para la API de **OpenFactura** (Haulmer - Chile), que permite emitir Documentos Tributarios Electrónicos (DTEs) de manera sencilla.

## Características

- ✅ Emisión de DTEs (Boletas, Facturas, Notas de Crédito, etc.)
- ✅ Consulta de estado de DTEs
- ✅ Consulta de contribuyentes por RUT
- ✅ Obtención de información de empresa/organización
- ✅ Registro de ventas y compras
- ✅ Anulación de Guías de Despacho
- ✅ Soporte para Sandbox y Producción
- ✅ Idempotencia automática
- ✅ Mock para testing

## Instalación

Este paquete ya está incluido en el proyecto SimpleRest. El autoload ya está configurado en `composer.json`:

```json
"autoload": {
    "psr-4": {
        "Boctulus\\OpenfacturaSdk\\": "packages/boctulus/openfactura-sdk/src"
    }
}
```

Si necesitas reinstalar las dependencias:

```bash
composer dumpautoload
```

## Configuración

### API Key

Para obtener tu API Key:

1. **Sandbox (desarrollo):** `928e15a2d14d4a6292345f04960f4bd3`
2. **Producción:** Contacta con Haulmer en [www.openfactura.cl](https://www.openfactura.cl)

### Inicialización

```php
use Boctulus\OpenfacturaSdk\OpenFacturaSDK;
use Boctulus\OpenfacturaSdk\OpenFacturaSDKFactory;

// Opción 1: Inicialización directa
$apiKey = '928e15a2d14d4a6292345f04960f4bd3';
$sandbox = true; // true = sandbox, false = producción

$sdk = new OpenFacturaSDK($apiKey, $sandbox);

// Opción 2: Usando Factory
$sdk = OpenFacturaSDKFactory::make($apiKey, $sandbox, $mock = false);

// Opción 3: Usar Mock para testing
$mockSdk = OpenFacturaSDKFactory::make($apiKey, $sandbox, $mock = true);
```

## Uso Básico

### 1. Emitir una Boleta Electrónica (DTE Tipo 39)

```php
$dteData = [
    'Encabezado' => [
        'IdDoc' => [
            'TipoDTE' => 39,  // Boleta Electrónica
            'Folio' => 12345, // Número de folio
            'FchEmis' => date('Y-m-d'),
            'IndServicio' => 3  // 3 = Servicio
        ],
        'Emisor' => [
            'RUTEmisor' => '76795561-8',
            'RznSocEmisor' => 'MI EMPRESA SPA',
            'GiroEmisor' => 'SERVICIOS DE TECNOLOGIA',
            'CdgSIISucur' => '81303347',
            'DirOrigen' => 'CALLE EJEMPLO 123',
            'CmnaOrigen' => 'Santiago'
        ],
        'Receptor' => [
            'RUTRecep' => '66666666-6'  // RUT genérico para boletas
        ],
        'Totales' => [
            'MntNeto' => 10000,
            'IVA' => 1900,
            'MntTotal' => 11900,
            'TotalPeriodo' => 11900,
            'VlrPagar' => 11900
        ]
    ],
    'Detalle' => [
        [
            'NroLinDet' => 1,
            'NmbItem' => 'Servicio de Desarrollo Web',
            'QtyItem' => 1,
            'PrcItem' => 11900,
            'MontoItem' => 11900
        ]
    ]
];

// Opciones de respuesta que deseas recibir
$responseOptions = ['PDF', 'FOLIO', 'TIMBRE'];

// Emitir DTE
$response = $sdk->emitirDTE($dteData, $responseOptions);

// La respuesta contiene:
echo "Token: " . $response['TOKEN'] . "\n";
echo "Folio: " . $response['FOLIO'] . "\n";
echo "PDF (base64): " . $response['PDF'] . "\n";
echo "Timbre (base64): " . $response['TIMBRE'] . "\n";
```

### 2. Consultar Estado de un DTE

```php
$token = '3d696756e0323d89fdce0801a3920a6f7635820337798d70f267c385c915fff9';

$status = $sdk->getDTEStatus($token);

print_r($status);
```

### 3. Consultar Contribuyente por RUT

```php
$rut = '76795561-8'; // RUT de Haulmer SPA

$contribuyente = $sdk->getTaxpayer($rut);

echo "Razón Social: " . $contribuyente['razonSocial'] . "\n";
echo "Dirección: " . $contribuyente['direccion'] . "\n";
echo "Comuna: " . $contribuyente['comuna'] . "\n";
```

### 4. Obtener Información de la Organización

```php
// Método recomendado
$info = $sdk->getOrganization();

// También disponible (deprecated, usa getOrganization internamente)
$info = $sdk->getCompanyInfo();

echo "RUT: " . $info['rut'] . "\n";
echo "Razón Social: " . $info['razonSocial'] . "\n";
echo "Email: " . $info['email'] . "\n";
```

### 5. Anular Guía de Despacho (DTE Tipo 52)

```php
$folio = 12345;
$fecha = '2025-01-15'; // Formato: YYYY-MM-DD

$response = $sdk->anularGuiaDespacho($folio, $fecha);

print_r($response);
```

### 6. Obtener Registro de Ventas

```php
$year = 2025;
$month = 10; // Octubre

$ventas = $sdk->getSalesRegistry($year, $month);

foreach ($ventas as $venta) {
    echo "Folio: " . $venta['folio'] . "\n";
    echo "Monto: $" . $venta['montoTotal'] . "\n";
}
```

### 7. Obtener Registro de Compras

```php
$year = 2025;
$month = 10;

$compras = $sdk->getPurchaseRegistry($year, $month);

foreach ($compras as $compra) {
    echo "Folio: " . $compra['folio'] . "\n";
    echo "Proveedor: " . $compra['proveedor'] . "\n";
}
```

## Tipos de DTE Soportados

| Tipo | Descripción |
|------|-------------|
| 33 | Factura Electrónica |
| 34 | Factura Electrónica Exenta |
| 39 | Boleta Electrónica |
| 41 | Boleta Electrónica Exenta |
| 46 | Factura de Compra Electrónica |
| 52 | Guía de Despacho Electrónica |
| 56 | Nota de Débito Electrónica |
| 61 | Nota de Crédito Electrónica |
| 110 | Factura de Exportación Electrónica |
| 111 | Nota de Débito de Exportación Electrónica |
| 112 | Nota de Crédito de Exportación Electrónica |

## Opciones de Respuesta

Al emitir un DTE, puedes solicitar diferentes elementos en la respuesta:

```php
$responseOptions = [
    'PDF',      // Documento en PDF (base64)
    'FOLIO',    // Número de folio asignado
    'TIMBRE',   // Timbre fiscal (base64)
    'XML',      // Documento XML
];

$response = $sdk->emitirDTE($dteData, $responseOptions);
```

## Idempotencia

El SDK implementa idempotencia automática para evitar duplicados en caso de reintentos:

```php
// La clave de idempotencia se genera automáticamente
$response = $sdk->emitirDTE($dteData, $responseOptions);

// O puedes proporcionar tu propia clave
$idempotencyKey = 'Key_' . time();
$response = $sdk->emitirDTE($dteData, $responseOptions, null, null, $idempotencyKey);
```

## Testing

El SDK incluye un Mock completo para testing:

```php
use Boctulus\OpenfacturaSdk\OpenFacturaSDKMock;
use Boctulus\OpenfacturaSdk\OpenFacturaSDKFactory;

// Opción 1: Instancia directa
$mockSdk = new OpenFacturaSDKMock($apiKey, $sandbox);

// Opción 2: Usando Factory
$mockSdk = OpenFacturaSDKFactory::make($apiKey, $sandbox, $mock = true);

// Usar exactamente igual que el SDK real
$response = $mockSdk->emitirDTE($dteData, ['PDF', 'FOLIO', 'TIMBRE']);

// Devuelve datos simulados
echo $response['FOLIO']; // 12345
echo $response['TOKEN']; // mock_token_12345
```

### Ejecutar Tests

```bash
./vendor/bin/phpunit tests/OpenFacturaSDKIntegrationTest.php
```

## Caché

Puedes habilitar caché para reducir llamadas a la API:

```php
$sdk->setCache(3600); // 1 hora en segundos

$info = $sdk->getOrganization(); // Primera llamada: consulta API
$info = $sdk->getOrganization(); // Segunda llamada: usa caché
```

## Métodos Disponibles

### DTEs
- `emitirDTE($dteData, $responseOptions, $custom, $sendEmail, $idempotencyKey)` - Emite un DTE
- `getDTEStatus($token)` - Consulta estado de un DTE
- `anularGuiaDespacho($folio, $fecha)` - Anula una guía de despacho

### Contribuyentes
- `getTaxpayer($rut)` - Consulta datos de un contribuyente
- `listTaxpayers($queryParams)` - Lista contribuyentes

### Organización
- `getOrganization()` - Obtiene información de la organización
- `getCompanyInfo()` - *Deprecated* - Usa `getOrganization()` en su lugar
- `getOrganizationDocuments($queryParams)` - Lista documentos de la organización

### Registros
- `getSalesRegistry($year, $month, $queryParams)` - Registro de ventas
- `getPurchaseRegistry($year, $month, $queryParams)` - Registro de compras

### Documentos
- `getDocumentByRutTypeFolio($rut, $type, $folio)` - Obtiene documento por RUT, tipo y folio
- `getDocumentByTokenValue($token, $value)` - Obtiene documento por token
- `getDocumentDetails($token)` - Detalles completos de un documento
- `sendDocumentEmail($token, $emailData)` - Envía documento por email

### Otros
- `checkApiStatus()` - Verifica estado de la API
- `documentIssued($data)` - Procesa documento emitido
- `documentReceived($data)` - Procesa documento recibido
- `documentReceivedAccuse($data)` - Acuse de recibo
- `emitirEnlaceAutoservicio($data)` - Genera enlace de autoservicio

## Errores Comunes

### Error: "Api no encontrada"

Si ves este error, verifica que estés usando el endpoint correcto. Algunos endpoints cambian entre versiones de la API.

**Solución:** Usa `getOrganization()` en lugar de `getCompanyInfo()` (ya está corregido en la versión actual).

### Error: "Razón Social no corresponde"

Aparece como WARNING cuando emites DTEs. Asegúrate de que la razón social en `RznSocEmisor` coincida con la registrada en el SII.

## Documentación de la API

- **API Docs:** [https://docsapi-openfactura.haulmer.com/](https://docsapi-openfactura.haulmer.com/)
- **Sitio Web:** [https://www.openfactura.cl/](https://www.openfactura.cl/)
- **SII - Formato DTE:** [https://www.sii.cl/factura_electronica/formato_dte.pdf](https://www.sii.cl/factura_electronica/formato_dte.pdf)

## Requisitos

- PHP >= 7.4
- SimpleRest Framework
- Extensión cURL habilitada
- Extensión JSON habilitada

## Changelog

### v1.1.0 (2025-10-20)
- ✅ Corrección del endpoint `getCompanyInfo()` para usar `/v2/dte/organization`
- ✅ Idempotencia automática implementada
- ✅ Tests completos incluidos
- ✅ Mock SDK para testing
- ✅ Factory pattern implementado

### v1.0.0 (2025-10-20)
- 🎉 Versión inicial del SDK
- ✅ Soporte para emisión de DTEs
- ✅ Consultas de contribuyentes
- ✅ Registros de ventas y compras

## Autor

**Pablo Gabriel Bozzolo** - [boctulus@gmail.com](mailto:boctulus@gmail.com)

## Licencia

MIT License

## Soporte

Para reportar problemas o solicitar nuevas características, contacta al autor o abre un issue en el repositorio del proyecto.

---

**Desarrollado con ❤️ para SimpleRest Framework**
