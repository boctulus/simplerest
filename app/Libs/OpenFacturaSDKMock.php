<?php

namespace Boctulus\Simplerest\Libs;

use Boctulus\Simplerest\Core\exceptions\NotImplementedException;
use Boctulus\Simplerest\interfaces\IOpenFactura;

class OpenFacturaSDKMock implements IOpenFactura
{
    private $apiKey;
    private $base_url;

    /**
     * Constructor que inicializa la API key y la URL base según el modo sandbox.
     *
     * @param string $apiKey Clave de la API.
     * @param bool $sandbox Indica si se usa el entorno de desarrollo.
     */
    public function __construct($apiKey, $sandbox = false) {
        $this->apiKey = $apiKey;
        $this->base_url = $sandbox ? 'https://dev-api.haulmer.com' : 'https://api.haulmer.com';
    }

    /**
     * Simula la configuración de caché. En el mock, no hace nada más que devolver la instancia.
     *
     * @param int $expiration_time Tiempo de expiración del caché.
     * @return self
     */
    public function setCache($expiration_time) {
        return $this;
    }

    /**
     * Simula la emisión de un DTE, retornando un token y datos adicionales según las opciones.
     *
     * @param array $dteData Datos del DTE.
     * @param array $responseOptions Opciones de respuesta (e.g., 'PDF', 'FOLIO').
     * @param mixed $custom Datos personalizados (no usado en el mock).
     * @param mixed $sendEmail Indica si se envía email (no usado en el mock).
     * @param string $idempotencyKey Clave de idempotencia (no usado en el mock).
     * @return array Respuesta simulada.
     */
    public function emitirDTE($dteData, $responseOptions = [], $custom = null, $sendEmail = null, $idempotencyKey = null) {
        $response = ['TOKEN' => 'mock_token_12345'];
        foreach ($responseOptions as $option) {
            switch ($option) {
                case 'PDF':
                    $response['PDF'] = 'mock_pdf_base64';
                    break;
                case 'FOLIO':
                    $response['FOLIO'] = 12345;
                    break;
            }
        }
        return $response;
    }

    /**
     * Devuelve el estado simulado de un DTE según un token.
     *
     * @param string $token Token del DTE.
     * @return array Estado simulado.
     */
    public function getDTEStatus($token) {
        return [
            'estado' => 'ACEPTADO',
            'detalle' => 'Documento aceptado por el SII'
        ];
    }

    /**
     * Simula la anulación de un DTE tipo 52.
     *
     * @param int $folio Folio del documento.
     * @param string $fecha Fecha de anulación.
     * @return array Respuesta simulada.
     */
    public function anularDTE52($folio, $fecha) {
        return ['success' => "Se ha anulado el documento Folio: $folio"];
    }

    public function anularGuiaDespacho($folio, $fecha){
        throw new NotImplementedException();
    }

    /**
     * Devuelve información simulada de la empresa.
     *
     * @return array Datos de la empresa.
     */
    public function getCompanyInfo() {
        return [
            'rut' => '76795561-8',
            'razonSocial' => 'HAULMER SPA',
            'giro' => 'VENTA AL POR MENOR POR CORREO, POR INTERNET Y VIA TELEFONICA',
            'actividadEconomica' => '479100',
            'direccion' => 'ARTURO PRAT 527   CURICO',
            'comuna' => 'Curicó',
            'telefono' => '0 0',
            'codigoSucursal' => '81303347'
        ];
    }

    /**
     * Devuelve información simulada de un contribuyente según su RUT.
     *
     * @param string $rut RUT del contribuyente.
     * @return array Datos del contribuyente.
     */
    public function getTaxpayer($rut) {
        return [
            'rut' => $rut,
            'razonSocial' => 'Empresa Ejemplo SPA',
            'direccion' => 'Calle Falsa 123',
            'comuna' => 'Santiago',
            'actividadEconomica' => '620200'
        ];
    }

    /**
     * Devuelve información de la organización asociada a la API key.
     *
     * @return array Información de la organización en formato JSON decodificado
     */
    public function getOrganization() {
        return json_decode('{
            "rut": "76795561-8",
            "razonSocial": "HAULMER SPA",
            "giro": "VENTA AL POR MENOR POR CORREO, POR INTERNET Y VIA TELEFONICA",
            "actividadEconomica": "479100",
            "direccion": "ARTURO PRAT 527   CURICO",
            "comuna": "Curicó",
            "telefono": "0 0",
            "codigoSucursal": "81303347"
        }', true);
    }

    /**
     * Devuelve una lista de documentos emitidos por la organización.
     *
     * @param array $queryParams Parámetros de consulta (no usados en el mock)
     * @return array Lista de documentos en formato JSON decodificado
     */
    public function getOrganizationDocuments($queryParams = []) {
        return json_decode('[
            {
                "tipoDTE": 33,
                "folio": 123,
                "fechaEmision": "2023-01-01",
                "montoTotal": 10000,
                "estado": "ACEPTADO"
            },
            {
                "tipoDTE": 34,
                "folio": 124,
                "fechaEmision": "2023-01-02",
                "montoTotal": 20000,
                "estado": "ACEPTADO"
            }
        ]', true);
    }

    /**
     * Devuelve el registro de compras para un año y mes específicos.
     *
     * @param int $year Año del registro
     * @param int $month Mes del registro
     * @param array $queryParams Parámetros de consulta (no usados en el mock)
     * @return array Lista de compras en formato JSON decodificado
     */
    public function getPurchaseRegistry($year, $month, $queryParams = []) {
        return json_decode('[
            {
                "tipoDTE": 33,
                "folio": 100,
                "fechaEmision": "' . sprintf("%04d-%02d-01", $year, $month) . '",
                "montoTotal": 5000,
                "proveedor": "Empresa Ejemplo SPA"
            },
            {
                "tipoDTE": 33,
                "folio": 101,
                "fechaEmision": "' . sprintf("%04d-%02d-15", $year, $month) . '",
                "montoTotal": 7000,
                "proveedor": "Otra Empresa Ltda"
            }
        ]', true);
    }

    /**
     * Devuelve un registro simulado de ventas para un año y mes específicos.
     *
     * @param int $year Año.
     * @param int $month Mes.
     * @param array $queryParams Parámetros de consulta (no usados en el mock).
     * @return array Lista de ventas.
     */
    public function getSalesRegistry($year, $month, $queryParams = []) {
        return json_decode('[
            {
                "tipoDTE": 33,
                "folio": 200,
                "fechaEmision": "' . sprintf("%04d-%02d-05", $year, $month) . '",
                "montoTotal": 15000,
                "cliente": "Cliente Ejemplo"
            },
            {
                "tipoDTE": 34,
                "folio": 201,
                "fechaEmision": "' . sprintf("%04d-%02d-20", $year, $month) . '",
                "montoTotal": 25000,
                "cliente": "Otro Cliente"
            }
        ]', true);
    }

    /**
     * Devuelve un documento simulado según RUT, tipo y folio.
     *
     * @param string $rut RUT del emisor o receptor.
     * @param int $type Tipo de DTE.
     * @param int $folio Folio del documento.
     * @return array Datos del documento.
     */
    public function getDocumentByRutTypeFolio($rut, $type, $folio) {
        return json_decode('{
            "rut": "' . $rut . '",
            "tipoDTE": ' . $type . ',
            "folio": ' . $folio . ',
            "fechaEmision": "2023-01-01",
            "montoTotal": 10000,
            "estado": "ACEPTADO",
            "detalle": [
                {
                    "item": "Producto 1",
                    "cantidad": 2,
                    "precio": 5000
                }
            ]
        }', true);
    }

    /**
     * Devuelve un documento simulado según un token y valor.
     *
     * @param string $token Token del documento.
     * @param string $value Valor asociado al token.
     * @return array Datos del documento.
     */
    public function getDocumentByTokenValue($token, $value) {
        return json_decode('{
            "token": "' . $token . '",
            "value": "' . $value . '",
            "tipoDTE": 33,
            "folio": 123,
            "fechaEmision": "2023-01-01",
            "montoTotal": 10000,
            "estado": "ACEPTADO"
        }', true);
    }

    /**
     * Simula el procesamiento de un documento emitido.
     *
     * @param array $data Datos del documento.
     * @return array Respuesta simulada.
     */
    public function documentIssued($data) {
        return json_decode('{
            "success": true,
            "message": "Documento emitido procesado correctamente"
        }', true);
    }

    /**
     * Simula el procesamiento de un documento recibido.
     *
     * @param array $data Datos del documento.
     * @return array Respuesta simulada.
     */
    public function documentReceived($data) {
        return json_decode('{
            "success": true,
            "message": "Documento recibido procesado correctamente"
        }', true);
    }

    /**
     * Simula el procesamiento de un acuse de recibo.
     *
     * @param array $data Datos del acuse.
     * @return array Respuesta simulada.
     */
    public function documentReceivedAccuse($data) {
        return json_decode('{
            "success": true,
            "message": "Acuse de recibo procesado correctamente"
        }', true);
    }

    /**
     * Simula la generación de un enlace de autoservicio.
     *
     * @param array $data Datos para el enlace.
     * @return array Enlace simulado.
     */
    public function emitirEnlaceAutoservicio($data) {
        return json_decode('{
            "enlace": "https://mock-enlace-autoservicio.com/mock_token",
            "expiracion": "2023-12-31T23:59:59Z"
        }', true);
    }

    /**
     * Lista contribuyentes asociados a la organización (hipotético).
     *
     * @param array $queryParams Filtros como estado, tipo, etc.
     * @return array Lista de contribuyentes en formato asociativo
     */
    public function listTaxpayers($queryParams = []) {
        return [
            [
                "rut" => "76795561-8",
                "razonSocial" => "HAULMER SPA",
                "giro" => "VENTA AL POR MENOR POR CORREO, POR INTERNET Y VIA TELEFONICA",
                "actividadEconomica" => "479100",
                "direccion" => "ARTURO PRAT 527   CURICO",
                "comuna" => "Curicó"
            ],
            [
                "rut" => "76430498-5",
                "razonSocial" => "HOSTY SPA",
                "giro" => "EMPRESAS DE SERVICIOS INTEGRALES DE INFORMÁTICA",
                "actividadEconomica" => "620200",
                "direccion" => "ARTURO PRAT 527 3 pis OF 1",
                "comuna" => "Curicó"
            ]
        ];
    }

    /**
     * Obtiene detalles completos de un documento por su token.
     *
     * @param string $token Token del documento
     * @return array Detalles del documento en formato asociativo
     */
    public function getDocumentDetails($token) {
        return [
            "token" => $token,
            "tipoDTE" => 33,
            "folio" => 12345,
            "fechaEmision" => "2023-01-01",
            "rutEmisor" => "76795561-8",
            "razonSocialEmisor" => "HAULMER SPA",
            "rutReceptor" => "76430498-5",
            "razonSocialReceptor" => "HOSTY SPA",
            "montoTotal" => 10000,
            "iva" => 1900,
            "estado" => "ACEPTADO",
            "detalle" => [
                [
                    "item" => "Producto 1",
                    "cantidad" => 2,
                    "precio" => 5000,
                    "descuento" => 0
                ]
            ]
        ];
    }

    /**
     * Envía un documento por email.
     *
     * @param string $token Token del documento
     * @param array $emailData Datos del email (to, subject, etc.)
     * @return array Respuesta de éxito en formato asociativo
     */
    public function sendDocumentEmail($token, $emailData) {
        return [
            "success" => true,
            "message" => "Documento enviado por email correctamente",
            "emailDetails" => [
                "to" => $emailData['to'] ?? "destinatario@ejemplo.com",
                "subject" => $emailData['subject'] ?? "Documento Electrónico",
                "sentAt" => date("Y-m-d\TH:i:s\Z")
            ]
        ];
    }

    /**
     * Verifica el estado de la API.
     *
     * @return array Estado de la API en formato asociativo
     */
    public function checkApiStatus() {
        return [
            "status" => "operativo",
            "version" => "2.0.0",
            "uptime" => "99.9%",
            "lastMaintenance" => "2023-01-01T00:00:00Z"
        ];
    }
}