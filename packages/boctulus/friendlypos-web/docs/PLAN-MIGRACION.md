# Plan de Migración: FriendlyPOS Web Package

## Objetivo
Crear un paquete `friendlypos_web` en SimpleRest que contenga toda la lógica de negocio y generación de PDFs del sistema FriendlyPOS, adaptando el código desde la aplicación Laravel original.

---

## Fase 1: Preparación del Paquete

### 1.1 Crear Paquete Base
```bash
php com make package friendlypos_web --author:boctulus
```

**Resultado esperado:**
- Directorio: `packages/boctulus/friendlypos_web/`
- Estructura básica del paquete creada

### 1.2 Instalar Dependencias
```bash
composer require setasign/fpdf
```

**Nota:** FPDF es la librería base para generar PDFs.

---

## Fase 2: Migración de Librerías

### 2.1 Copiar LaravelFpdf
**Origen:** `D:\laragon\www\friendapp\app\LaravelFpdf`
**Destino:** `packages/boctulus/friendlypos_web/src/Libs/LaravelFpdf`

**Archivos a copiar:**
- `src/Fpdf/Fpdf.php` - Clase base de FPDF extendida
- `src/Fpdf/PDF_BARCODE.php` - Generación de códigos de barras

**Ajustes necesarios:**
- Cambiar namespace de `App\LaravelFpdf\src\Fpdf` a `Boctulus\FriendlyposWeb\Libs\LaravelFpdf`
- Verificar que extienda correctamente de FPDF del Composer

### 2.2 Copiar chillerlan/QRCode
**Origen:** `D:\laragon\www\friendapp\app\chillerlan`
**Destino:** `packages/boctulus/friendlypos_web/src/Libs/QRCode`

**Archivos a copiar:**
- `QRCode/` - Librería completa de QR
- `Settings/` - Configuraciones de QR

**Ajustes necesarios:**
- Cambiar namespace de `App\chillerlan\QRCode` a `Boctulus\FriendlyposWeb\Libs\QRCode`
- Revisar dependencias internas

---

## Fase 3: Migración de Domain (Lógica de Negocio)

### 3.1 Copiar Carpeta Domain Completa
**Origen:** `D:\laragon\www\friendapp\app\Domain`
**Destino:** `packages/boctulus/friendlypos_web/src/Domain`

**Estructura de Domain:**
```
Domain/
├── UseCases/
│   ├── Ventas/
│   │   ├── GestorComprobante.php  ← CRÍTICO
│   │   ├── Pagar.php
│   │   └── ...
│   ├── Carrito/
│   ├── Productos/
│   ├── Configuracion/
│   │   └── GestorConfig.php  ← Necesario para GestorComprobante
│   └── ...
├── Entities/
│   └── Dtes/  ← DTOs ya migrados en openfactura-sdk
├── Repositories/
├── Service/
└── Interfaces/
```

### 3.2 Archivos Prioritarios (Orden de migración)

#### Prioridad 1: Generación de PDFs
1. **GestorComprobante.php** (línea 35-1146)
   - Métodos principales:
     - `documentoPdf($id_venta, $sw_output)` - Genera PDF según tipo
     - `generarBoletaPdf($datos_compra)` - PDF de boleta
     - `generarFacturaPdf($datos_compra)` - PDF de factura
     - `getDatosCompra($id_venta)` - Lee datos de venta con JOINs

2. **GestorConfig.php**
   - Necesario para obtener configuración de tamaño de hoja
   - Método: `getUnaConfiguracion($id)`

#### Prioridad 2: Lógica de Ventas
1. **Pagar.php** (Orquestador de ventas)
   - Métodos:
     - `pagar()` - Proceso completo de pago
     - `crearRegistroVenta($venta)`
     - `crearRegistroDetalleVenta(...)`
     - `enviarVentaOpenFactura($idVenta)`
     - `descontarStock($idVenta)`

---

## Fase 4: Adaptaciones de Código

### 4.1 Namespaces
**Buscar y reemplazar en todos los archivos:**

```php
// Antes:
namespace Boctulus\FriendlyposWeb\Domain\UseCases\Ventas;
use Boctulus\FriendlyposWeb\LaravelFpdf\src\Fpdf\Fpdf;
use Boctulus\FriendlyposWeb\chillerlan\QRCode\...;

// Después:
namespace Boctulus\FriendlyposWeb\Domain\UseCases\Ventas;
use Boctulus\FriendlyposWeb\Libs\LaravelFpdf\Fpdf;
use Boctulus\FriendlyposWeb\Libs\QRCode\...;
```

### 4.2 Facade DB de Laravel → SimpleRest

**SimpleRest DB:**
```php
// Abrir conexión
DB::getConnection('laravel_pos');

// Queries (compatible con Laravel)
DB::table('venta')
  ->join("documentoDte", "documentoDte.idDocumentoDte", "venta.idDocumentoDte")
  ->where('venta.idVenta', $id_venta)
  ->select("*")
  ->get();

// Cerrar conexión
DB::closeConnection('laravel_pos');
```

**Cambios necesarios en GestorComprobante:**
- Línea 658, 996: Añadir `DB::getConnection('laravel_pos')` al inicio
- Al final de métodos: Añadir `DB::closeConnection('laravel_pos')`

### 4.3 Autenticación: auth() Helper

**Laravel:**
```php
auth()->user()->idEmpresa
```

**SimpleRest:**
```php
// Opción 1: Obtener de sesión
$_SESSION['user_id']
$_SESSION['empresa_id']  // Si existe

// Opción 2: Crear helper en el paquete
namespace Boctulus\FriendlyposWeb\Helpers;

class Auth {
    public static function user() {
        return (object)[
            'idEmpresa' => $_SESSION['empresa_id'] ?? 1,
            'id' => $_SESSION['user_id'] ?? null
        ];
    }
}

// Uso:
Auth::user()->idEmpresa
```

### 4.4 Rutas y URLs

**Laravel:**
```php
url("/".$url_logo_empresa)
public_path('/pdf/dte/1/39/')
```

**SimpleRest:**
```php
// Investigar helpers disponibles en SimpleRest
// Probablemente:
ROOT_PATH . '/public/' . $url_logo_empresa
ROOT_PATH . '/public/pdf/dte/1/39/'
```

### 4.5 Sesiones y Helpers

**Buscar y adaptar:**
- `session('firmadte')` → `$_SESSION['firmadte']`
- `mb_convert_encoding()` - Ya disponible en PHP
- `file_exists()` - Ya disponible en PHP

---

## Fase 5: Integración con Models y Schemas

### 5.1 Models Ya Generados
**Ubicación:** `app/Models/laravel_pos/`

**Modelos disponibles:**
- `VentaModel.php`
- `VentaDetalleModel.php`
- `ArticuloModel.php`
- `EmpresaModel.php`
- `DocumentodteModel.php`
- etc.

**Uso en el paquete:**
```php
use Boctulus\FriendlyposWeb\VentaModel;
use Boctulus\FriendlyposWeb\VentaDetalleModel;

// Acceso directo
$venta = VentaModel::find($id_venta);
```

### 5.2 Schemas Ya Generados
**Ubicación:** `app/Schemas/laravel_pos/`

**Uso:**
```php
use simplerest\schemas\laravel_pos\VentaSchema;

// Validaciones, etc.
```

### 5.3 API Controllers Ya Generados
**Ubicación:** `app/Controllers/api/`

**Controladores disponibles:**
- `VentaController.php`
- `VentaDetalleController.php`
- `ArticuloController.php`
- etc.

**Nota:** NO mover estos al paquete (como indicaste), solo referenciarlos si es necesario.

---

## Fase 6: Estructura Final del Paquete

```
packages/boctulus/friendlypos_web/
├── composer.json
├── README.md
├── src/
│   ├── Libs/
│   │   ├── LaravelFpdf/
│   │   │   ├── Fpdf.php
│   │   │   └── PDF_BARCODE.php
│   │   └── QRCode/
│   │       ├── QRCode/
│   │       └── Settings/
│   ├── Domain/
│   │   ├── UseCases/
│   │   │   ├── Ventas/
│   │   │   │   ├── GestorComprobante.php
│   │   │   │   ├── Pagar.php
│   │   │   │   └── ...
│   │   │   ├── Carrito/
│   │   │   ├── Productos/
│   │   │   ├── Configuracion/
│   │   │   └── ...
│   │   ├── Entities/
│   │   ├── Repositories/
│   │   ├── Service/
│   │   └── Interfaces/
│   ├── Helpers/
│   │   ├── Auth.php
│   │   └── Path.php
│   └── Controllers/
│       └── ComprobantePdfController.php  ← Controlador de prueba
└── tests/
    └── PdfGenerationTest.php
```

---

## Fase 7: Archivo de Configuración del Paquete

**Crear:** `packages/boctulus/friendlypos_web/config/config.php`

```php
<?php

return [
    // Conexión a BD
    'database' => [
        'connection' => 'laravel_pos',
    ],

    // Rutas
    'paths' => [
        'logo' => 'images/logo_friendly_component.png',
        'pdf_output' => 'pdf/dte',
        'temp_qr' => 'temp/qr',
    ],

    // Configuración de PDFs
    'pdf' => [
        'default_page_size' => [45, 350], // ancho, alto en mm
        'font' => 'Courier',
        'font_size' => 8,
    ],

    // Empresa por defecto (para testing)
    'default_empresa_id' => 1,
];
```

---

## Fase 8: Testing

### 8.1 Crear Controlador de Prueba
**Ubicación:** `packages/boctulus/friendlypos_web/src/Controllers/ComprobantePdfController.php`

```php
<?php

namespace Boctulus\FriendlyposWeb\Controllers;

use Boctulus\FriendlyposWeb\Domain\UseCases\Ventas\GestorComprobante;
use Boctulus\Simplerest\Core\Controller;

class ComprobantePdfController extends Controller
{
    public function generarPdf($id_venta = null)
    {
        if (!$id_venta) {
            return response()->json(['error' => 'ID de venta requerido'], 400);
        }

        $gestor = new GestorComprobante();
        $gestor->documentoPdf($id_venta);
    }

    public function testBoletaPdf()
    {
        // Genera boleta de prueba con ID 60 (ajustar según tu BD)
        $this->generarPdf(60);
    }
}
```

### 8.2 Ruta de Prueba
**Agregar en:** `config/routes.php` o similar

```php
Route::get('/pdf/venta/{id}', 'boctulus\\FriendlyposWeb\\Controllers\\ComprobantePdfController@generarPdf');
Route::get('/pdf/test', 'boctulus\\FriendlyposWeb\\Controllers\\ComprobantePdfController@testBoletaPdf');
```

---

## Fase 9: Checklist de Puntos Críticos

### 9.1 Verificar Adaptaciones
- [ ] Todos los namespaces actualizados
- [ ] DB::getConnection() añadido en métodos que usan DB
- [ ] auth()->user() reemplazado por Auth::user()
- [ ] url() y public_path() reemplazados
- [ ] session() reemplazado por $_SESSION
- [ ] Rutas de archivos ajustadas a SimpleRest

### 9.2 Verificar Dependencias
- [ ] fpdf instalado vía Composer
- [ ] LaravelFpdf copiado y funcional
- [ ] chillerlan/QRCode copiado y funcional
- [ ] GestorConfig disponible

### 9.3 Verificar Funcionalidad
- [ ] PDF de boleta se genera correctamente
- [ ] PDF de factura se genera correctamente
- [ ] Timbre del SII se muestra en PDF
- [ ] QR en tickets se genera correctamente
- [ ] Imágenes (logo) se cargan correctamente

---

## Fase 10: Optimizaciones Futuras

### 10.1 Manejo de Transacciones
Actualmente el código Laravel NO usa transacciones explícitas. Mejorar:

```php
DB::beginTransaction();
try {
    // Operaciones
    DB::commit();
} catch (Exception $e) {
    DB::rollback();
    throw $e;
}
```

### 10.2 Cache de PDFs
En lugar de regenerar cada vez:
```php
// Verificar si PDF ya existe
$pdf_path = "/pdf/dte/1/39/{$folio}.pdf";
if (!file_exists($pdf_path)) {
    // Generar PDF
    $gestor->documentoPdf($id_venta, 1); // sw_output = 1 guarda archivo
}
// Servir PDF existente
```

### 10.3 Cola de Jobs
Para ventas con alto volumen, procesar generación de PDFs en background.

---

## Notas Importantes del Análisis DB

### Flujo de Datos (Referencia)
1. **Carrito:** Usuario agrega productos → `carrito`, `carrito_detalle`
2. **Pago:** Se crea `venta` temporal → Se envía a OpenFactura API
3. **Éxito:** Actualiza `venta` con folio/timbre → Descuenta stock → Vacía carrito
4. **Fallo:** Borra `venta` temporal (rollback manual)

### Tablas Principales
- `venta` - Cabecera de venta con folio y timbre
- `venta_detalle` - Líneas de productos vendidos
- `venta_detalle_item_extra` - Items genéricos (idProducto = 42)
- `carrito` / `carrito_detalle` - Estado temporal pre-venta
- `empresa_producto` - Stock por empresa/producto

### Datos Críticos en PDF
- **Folio:** Número del SII (campo `venta.folio`)
- **Timbre:** Imagen base64 del SII (campo `venta.timbre`)
- **Totales:** `total_bruto`, `total_neto`, `impuesto`
- **Tipo DTE:** Boleta (39), Factura (33), etc.

---

## Resumen de Comandos

```bash
# 1. Crear paquete
php com make package friendlypos_web --author:boctulus

# 2. Instalar fpdf
composer require setasign/fpdf

# 3. Copiar archivos (manual o con script)
# ...

# 4. Probar generación de PDF
# Visitar: http://localhost/simplerest/pdf/test
```

---

## Referencias
- **Análisis DB:** `packages/boctulus/openfactura-sdk/Analisis-DB.md`
- **DTOs OpenFactura:** `packages/boctulus/openfactura-sdk/src/DTO/`
- **Schemas:** `app/Schemas/laravel_pos/`
- **Models:** `app/Models/laravel_pos/`
- **API Controllers:** `app/Controllers/api/`

---

**Fecha de creación:** 2025-10-20
**Autor:** Claude AI
**Estado:** Listo para ejecución
