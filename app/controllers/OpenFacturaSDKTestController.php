<?php

namespace simplerest\controllers;

use simplerest\core\libs\ApiClient;
use simplerest\libs\OpenFacturaSDKFactory;
use simplerest\core\controllers\Controller;

/*
    https://grok.com/chat/f4a3da3e-9861-42c8-b4b1-99e360c1bebe

    API KEY desarrollo: '928e15a2d14d4a6292345f04960f4bd3'
    API KEY produccion: '04f1d39392684b0a9e78ff2a3d0b167a'
*/
class OpenFacturaSDKTestController extends Controller
{
    private $sdk_instance;
    private $mock    = false;
    private $sandbox = false;
    private $api_key = '04f1d39392684b0a9e78ff2a3d0b167a';

    public function __construct() {
        parent::__construct();

        $sandbox = false; // Cambiar a true para usar el entorno de desarrollo
        $this->sdk_instance = OpenFacturaSDKFactory::make($this->api_key, $this->sandbox, $this->mock);

        if ($sandbox) {
            $this->sdk_instance->setCache(48 * 3600);
            $this->sdk_instance->getClient()->enablePostRequestCache();
        }
    }

    /////////////// TESTING

    /*
        Encabezado

            "Folio": 0  <-- asignar automaticamente folio

        Para el Emisor son OPCIONALES:

        - "CdgSIISucur" (codigo de sucursal, texto o numero)

        Para el Receptor, con "Boleta Electronica" ("TipoDTE": 39) son OPCIONALES:

        - "CmnaRecep"   (comuna, texto)
        - "CiudadRecep" (ciudad, texto)

        Para "cliente ocasional" se puede usar el RUT Universal:

        Ej:

        "Receptor": {
            "RUTRecep": "66666666-6",                   
            "DirRecep": "XXX XXXXXX"
        },


        DUDAS:

        - En que casos difieren "MntTotal", "TotalPeriodo" y "VlrPagar" ?
    */
    public function test_emitir_dte() {        
            $body = '{
                "dte": {
                "Encabezado": {
                    "IdDoc": {
                        "TipoDTE": 39,
                        "Folio": "67d889cf66ba8",
                        "FchEmis": "2025-03-17",
                        "IndServicio": 3
                    },
                    "Emisor": {
                        "RUTEmisor": "76535188-K",
                        "RznSocEmisor": "NUEVO MUNDO SPA",
                        "GiroEmisor": "IMPORTACIONES, TELECOMUNICACIONES, TECNOLOGÍA, PUBLICIDAD, MAQUINARIA, ASESORÍAS",
                        "CdgSIISucur": 79603042,
                        "DirOrigen": "Camino a cahuil 679 villa esperanza",
                        "CmnaOrigen": "PICHILEMU"
                    },
                    "Receptor": {
                        "RUTRecep": "66666666-6"
                    },
                    "Totales": {
                        "MntNeto": 4765,
                        "IVA": 905,
                        "MntTotal": 5670,
                        "TotalPeriodo": 5670,
                        "VlrPagar": 5670,
                        "MntExe": 0
                    }
                },
                "Detalle": [
                    {
                        "NroLinDet": 1,
                        "NmbItem": "Bebida CCU",
                        "QtyItem": 3,
                        "PrcItem": 1890,
                        "MontoItem": 5670,
                        "IndExe": null
                    }
                ],
                "Referencia": null
            }
        }';

        // DESARROLLO
        // $base_url        = 'https://dev-api.haulmer.com';
        // $api_key         = '928e15a2d14d4a6292345f04960f4bd3';
        // $idempotency_key = 'fccb60fb512d13df5083790d64c4d5dd';

        // PROD
        $base_url           = 'https://api.haulmer.com';
        $api_key            = '04f1d39392684b0a9e78ff2a3d0b167a';
        $idempotency_key    = null;

        $cli = new ApiClient();

        $cli->addHeader('apikey', $api_key);
        $cli->addHeader('Idempotency-Key', $idempotency_key ?? null); // si es null, no lo envia
        $cli->addHeader('Content-type', 'application/json');

        $response = $cli
            ->setBody(trim($body), true)
            ->request($base_url . '/v2/dte/document', 'POST')
            ->data();

        dd($cli->dump(), 'REQUEST');    
     
        return $response;
    }

    /*
        Para "anular un DTE" se hace creando una "Nota de Credito" ("TipoDTE": 61)
    */
    public function test_anular_dte(){
        $body = '{
            "response": ["XML", "PDF", "TIMBRE", "LOGO", "FOLIO", "RESOLUCION"],
            "dte": {
              "Encabezado": {
                "IdDoc": {
                  "TipoDTE": 61,
                  "Folio": 0,
                  "FchEmis": "{{DateToday}}",
                  "TpoTranVenta": "1",
                  "FmaPago": "2"
                },
                "Emisor": {
                  "RUTEmisor": "76795561-8",
                  "RznSoc": "HAULMER SPA",
                  "GiroEmis": "VENTA AL POR MENOR EN EMPRESAS DE VENTA A DISTANCIA VÍA INTERNET; COMERCIO ELEC",
                  "Acteco": 479100,
                  "DirOrigen": "ARTURO PRAT 527 CURICÓ",
                  "CmnaOrigen": "Curicó",
                  "Telefono": "0",
                  "CdgSIISucur": "81303347"
                },
                "Receptor": {
                  "RUTRecep": "76430498-5",
                  "RznSocRecep": "HOSTY SPA",
                  "GiroRecep": "EMPRESAS DE SERVICIOS INTEGRALES DE INFO",
                  "Contacto": "+56969195057",
                  "DirRecep": "Arturo Prat 5273 piso oficina 1",
                  "CmnaRecep": "CURICÓ"
                },
                "Totales": {
                  "MntNeto": 1500,
                  "TasaIVA": "19",
                  "IVA": 285,
                  "MntTotal": 1785,
                  "MontoPeriodo": 1785,
                  "VlrPagar": 1785
                }
              },
              "Detalle": [
                {
                  "NroLinDet": 1,
                  "NmbItem": "item1",
                  "QtyItem": 1,
                  "PrcItem": 1500,
                  "MontoItem": 1500
                }
              ],
              "Referencia": [
                {
                  "NroLinRef": 1,
                  "TpoDocRef": "33",
                  "FolioRef": "106",
                  "FchRef": "2018-08-16",
                  "CodRef": "3"
                }
              ]
            }
          }';

        // PROD
        $base_url           = 'https://api.haulmer.com';
        $api_key            = '04f1d39392684b0a9e78ff2a3d0b167a';
        $idempotency_key    = 'clave_unica_' . time(); // Evita duplicados

        $cli = new ApiClient();

        $cli->addHeader('apikey', $api_key);
        $cli->addHeader('Idempotency-Key', $idempotency_key ?? null); // si es null, no lo envia
        $cli->addHeader('Content-type', 'application/json');

        $response = $cli
            ->setBody(trim($body), true)
            ->request($base_url . '/v2/dte/document', 'POST')
            ->data();

        dd($cli->dump(), 'REQUEST');    
        
        return $response;
    }

    /*
        33: Factura Electrónica
        39: Boleta Electrónica
        34: Factura Electrónica Exenta
        41: Boleta Electrónica Exenta
        56: Nota de Débito Electrónica
        61: Nota de Crédito Electrónica
        52: Guía de Despacho Electrónica
        110: Factura de Exportación Electrónica
        111: Nota de Débito de Exportación Electrónica
        112: Nota de Crédito de Exportación Electrónica
        46: Factura de Compra Electrónica
    */
    public function testEmitirDTE(){
        $dteData = [
            'Encabezado' => [
                'IdDoc' => [
                    'TipoDTE' => 39, // Boleta
                    'Folio' => 1,
                    'FchEmis' => '2025-03-17'
                ],
                'Emisor' => [
                    'RUTEmisor' => '76535188-k'
                ],
                'Receptor' => [
                    'RUTRecep' => '9853049-5'
                ]                
            ],
            'Detalle' => [
                [
                    'NmbItem' => 'Producto Ejemplo',
                    'QtyItem' => 1,
                    'PrcItem' => 100
                ]
            ]
        ];
        $responseOptions = ['PDF', 'FOLIO']; // Solicita el PDF y el folio
        $idempotency_key = 'clave_unica_' . time(); // Evita duplicados
        
        $response = $this->sdk_instance->emitirDTE($dteData, $responseOptions, null, null, $idempotency_key);
        dd($response, 'response'); // Devuelve un token, PDF, folio, etc.

        dd($this->sdk_instance->getClient()->dump(), 'REQUEST');
    }

    public function testGetDTEStatus($token){
        $status = $this->sdk_instance->getDTEStatus($token);
        dd($status, 'status'); // Devuelve el estado del DTE
    }

    public function testAnularDTE52($folio, $fechaEmis){
        $response = $this->sdk_instance->anularGuiaDespacho($folio, $fechaEmis);
        dd($response, 'anularGuiaDespacho'); // Devuelve la respuesta de anulación
    }

    public function testGetCompanyInfo(){
        $companyInfo = $this->sdk_instance->getCompanyInfo();
        dd($companyInfo, 'companyInfo'); // Devuelve la información de la empresa
    }

    public function testGetTaxpayer($rut){
        $taxpayer = $this->sdk_instance->getTaxpayer($rut);
        dd($taxpayer, 'taxpayer'); // Devuelve la información del contribuyente
    }

    public function testListTaxpayers($queryParams = ['estado' => 'activo'])
    {
        $taxpayers = $this->sdk_instance->listTaxpayers($queryParams);
        dd($taxpayers, 'taxpayers'); // Devuelve la lista de contribuyentes
    }

    public function testGetOrganization(){
        $organization = $this->sdk_instance->getOrganization();
        dd($organization, 'organization'); // Devuelve la información de la organización
    }

    public function testGetOrganizationDocuments($queryParams = ['tipo' => 33]){        
        $documents = $this->sdk_instance->getOrganizationDocuments($queryParams);
        dd($documents, 'documents'); // Devuelve la lista de documentos
    }

    public function testGetPurchaseRegistry($year = '2023', $month = '01'){        
        $purchases = $this->sdk_instance->getPurchaseRegistry($year, $month);
        dd($purchases, 'purchases'); // Devuelve el registro de compras
    }

    public function testGetSalesRegistry($year = '2023', $month = '01') {
        $sales = $this->sdk_instance->getSalesRegistry($year, $month);
        dd($sales, 'sales'); // Devuelve el registro de ventas
    }

    public function testGetDocumentByRutTypeFolio($rut = '12345678-9', $type = 33, $folio = 12345) {
        $document = $this->sdk_instance->getDocumentByRutTypeFolio($rut, $type, $folio);
        dd($document, 'document'); // Devuelve el documento específico
    }

    public function testGetDocumentByTokenValue($token = 'mock_token_12345', $value = 'some_value') {
        $document = $this->sdk_instance->getDocumentByTokenValue($token, $value);
        dd($document, 'document'); // Devuelve el documento específico
    }

    public function testDocumentIssued($data = ['token' => 'mock_token_12345']) {
        $response = $this->sdk_instance->documentIssued($data);
        dd($response, 'documentIssued'); // Devuelve la respuesta del procesamiento
    }

    public function testDocumentReceived($data = ['token' => 'mock_token_12345']) {
        $response = $this->sdk_instance->documentReceived($data);
        dd($response, 'documentReceived'); // Devuelve la respuesta del procesamiento
    }

    public function testDocumentReceivedAccuse($data = ['token' => 'mock_token_12345']) {
        $response = $this->sdk_instance->documentReceivedAccuse($data);
        dd($response, 'documentReceivedAccuse'); // Devuelve la respuesta del acuse
    }

    public function testEmitirEnlaceAutoservicio($data = ['token' => 'mock_token_12345']) {
        $enlace = $this->sdk_instance->emitirEnlaceAutoservicio($data);
        dd($enlace, 'enlace'); // Devuelve el enlace de autoservicio
    }

    public function testGetDocumentDetails($token = 'mock_token_12345') {
        $details = $this->sdk_instance->getDocumentDetails($token);
        dd($details, 'details'); // Devuelve los detalles del documento
    }

    public function testSendDocumentEmail($token = 'mock_token_12345', $emailData = ['to' => 'cliente@ejemplo.com', 'subject' => 'Factura Electrónica']) {
        $response = $this->sdk_instance->sendDocumentEmail($token, $emailData);
        dd($response, 'sendDocumentEmail'); // Devuelve la respuesta del envío
    }

    public function testCheckApiStatus() {
        $status = $this->sdk_instance->checkApiStatus();
        dd($status, 'apiStatus'); // Devuelve el estado de la API
    }
}