<?php

namespace simplerest\controllers;

use simplerest\libs\OpenFacturaSDK;
use simplerest\core\controllers\Controller;

/*
    https://grok.com/chat/f4a3da3e-9861-42c8-b4b1-99e360c1bebe
*/
class OpenFacturaSDKTestController extends Controller
{
    private $sdk;

    public function __construct() {
        parent::__construct();

        $sandbox = false; // Cambiar a true para usar el entorno de desarrollo
        $this->sdk = new OpenFacturaSDK('928e15a2d14d4a6292345f04960f4bd3', $sandbox);

        if ($sandbox) {
            $this->sdk->setCache(48 * 3600);
            $this->sdk->getClient()->enablePostRequestCache();
        }
    }

    public function testEmitirDTE(){
        $dteData = [
            'Encabezado' => [
                'IdDoc' => [
                    'TipoDTE' => 33, // Factura electrónica
                    'Folio' => 1
                ],
                'Emisor' => [
                    'RUTEmisor' => '76795561-8'
                ],
                'Receptor' => [
                    'RUTRecep' => '12345678-9'
                ]
            ],
            'Detalle' => [
                [
                    'NmbItem' => 'Producto Ejemplo',
                    'QtyItem' => 1,
                    'PrcItem' => 10000
                ]
            ]
        ];
        $responseOptions = ['PDF', 'FOLIO']; // Solicita el PDF y el folio
        $idempotencyKey = 'clave_unica_' . time(); // Evita duplicados
        
        $response = $this->sdk->emitirDTE($dteData, $responseOptions, null, null, $idempotencyKey);
        dd($response, 'response'); // Devuelve un token, PDF, folio, etc.
    }

    public function testGetDTEStatus($token = 'mock_token_12345'){
        $status = $this->sdk->getDTEStatus($token);
        dd($status, 'status'); // Devuelve el estado del DTE
    }

    public function testAnularDTE52($folio = 12345, $fecha = '2023-01-01'){
        $response = $this->sdk->anularDTE52($folio, $fecha);
        dd($response, 'anularDTE52'); // Devuelve la respuesta de anulación
    }

    public function testGetCompanyInfo(){
        $companyInfo = $this->sdk->getCompanyInfo();
        dd($companyInfo, 'companyInfo'); // Devuelve la información de la empresa
    }

    public function testGetTaxpayer($rut = '12345678-9'){
        $taxpayer = $this->sdk->getTaxpayer($rut);
        dd($taxpayer, 'taxpayer'); // Devuelve la información del contribuyente
    }

    public function testListTaxpayers($queryParams = ['estado' => 'activo'])
    {
        $taxpayers = $this->sdk->listTaxpayers($queryParams);
        dd($taxpayers, 'taxpayers'); // Devuelve la lista de contribuyentes
    }

    public function testGetOrganization(){
        $organization = $this->sdk->getOrganization();
        dd($organization, 'organization'); // Devuelve la información de la organización
    }

    public function testGetOrganizationDocuments($queryParams = ['tipo' => 33]){        
        $documents = $this->sdk->getOrganizationDocuments($queryParams);
        dd($documents, 'documents'); // Devuelve la lista de documentos
    }

    public function testGetPurchaseRegistry($year = '2023', $month = '01'){        
        $purchases = $this->sdk->getPurchaseRegistry($year, $month);
        dd($purchases, 'purchases'); // Devuelve el registro de compras
    }

    public function testGetSalesRegistry($year = '2023', $month = '01') {
        $sales = $this->sdk->getSalesRegistry($year, $month);
        dd($sales, 'sales'); // Devuelve el registro de ventas
    }

    public function testGetDocumentByRutTypeFolio($rut = '12345678-9', $type = 33, $folio = 12345) {
        $document = $this->sdk->getDocumentByRutTypeFolio($rut, $type, $folio);
        dd($document, 'document'); // Devuelve el documento específico
    }

    public function testGetDocumentByTokenValue($token = 'mock_token_12345', $value = 'some_value') {
        $document = $this->sdk->getDocumentByTokenValue($token, $value);
        dd($document, 'document'); // Devuelve el documento específico
    }

    public function testDocumentIssued($data = ['token' => 'mock_token_12345']) {
        $response = $this->sdk->documentIssued($data);
        dd($response, 'documentIssued'); // Devuelve la respuesta del procesamiento
    }

    public function testDocumentReceived($data = ['token' => 'mock_token_12345']) {
        $response = $this->sdk->documentReceived($data);
        dd($response, 'documentReceived'); // Devuelve la respuesta del procesamiento
    }

    public function testDocumentReceivedAccuse($data = ['token' => 'mock_token_12345']) {
        $response = $this->sdk->documentReceivedAccuse($data);
        dd($response, 'documentReceivedAccuse'); // Devuelve la respuesta del acuse
    }

    public function testEmitirEnlaceAutoservicio($data = ['token' => 'mock_token_12345']) {
        $enlace = $this->sdk->emitirEnlaceAutoservicio($data);
        dd($enlace, 'enlace'); // Devuelve el enlace de autoservicio
    }

    public function testGetDocumentDetails($token = 'mock_token_12345') {
        $details = $this->sdk->getDocumentDetails($token);
        dd($details, 'details'); // Devuelve los detalles del documento
    }

    public function testSendDocumentEmail($token = 'mock_token_12345', $emailData = ['to' => 'cliente@ejemplo.com', 'subject' => 'Factura Electrónica']) {
        $response = $this->sdk->sendDocumentEmail($token, $emailData);
        dd($response, 'sendDocumentEmail'); // Devuelve la respuesta del envío
    }

    public function testCheckApiStatus() {
        $status = $this->sdk->checkApiStatus();
        dd($status, 'apiStatus'); // Devuelve el estado de la API
    }
}