<?php

namespace Boctulus\OpenfacturaSdk\Libs;

use Boctulus\Simplerest\Core\Libs\Files;
use Boctulus\Simplerest\Core\Libs\Logger;
use Boctulus\Simplerest\Core\Libs\VarDump;
use Boctulus\Simplerest\Core\Libs\ApiClient;
use Boctulus\OpenfacturaSdk\Interfaces\IOpenFactura;
use Boctulus\Simplerest\Core\Exceptions\NotImplementedException;

/*
    SDK para OpenFactura

    @version 1.1.0

    @author Pablo Bozzolo

    API KEY de desarrollo "928e15xxxxxxxxxxxxxxxxx"

    Metodos

    - emitirDTE: Emisión de DTEs con soporte para opciones de respuesta (response), campos personalizados (custom), envío de email (sendEmail) y clave de idempotencia (Idempotency-Key).
    - getDTEStatus: Consulta del estado de un DTE por token.
    - anularGuiaDespacho: Anulación de guías de despacho electrónicas (DTE tipo 52).
    - getCompanyInfo: Obtención de información de la empresa.
    - Métodos adicionales como getTaxpayer, getOrganization, getOrganizationDocuments,...

    Manejo de Idempotencia

    El método emitirDTE permite configurar una clave de idempotencia mediante "Idempotency-Key", lo cual es una práctica recomendada por la documentación para evitar duplicados en reintentos. 

    if ($idempotencyKey) {
        $this->apiClient->setHeader('Idempotency-Key', $idempotencyKey);

    La cabecera se elimina después de la solicitud, evitando interferencias en futuras peticiones.    

    Tipos de DTE

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

    https://docsapi-openfactura.haulmer.com/
    https://www.openfactura.cl/factura-electronica/api/
    https://www.sii.cl/factura_electronica/formato_dte.pdf

    De haber alguna duda, revisar:

    D:\laragon\www\friendapp\app\Services\OpenFacturaService.php [!]
}
*/
class OpenFacturaSDK implements IOpenFactura
{
    protected $apiClient;
    protected $apiKey;
    protected $base_url;

    /**
     * Constructor de la clase.
     *
     * @param string $apiKey Clave API para autenticación.
     * @param bool $sandbox Indica si se usa el entorno de desarrollo (true) o producción (false).
     */
    public function __construct($apiKey, $sandbox = false) {
        $this->apiKey = $apiKey;
        $this->base_url = $sandbox ? 'https://dev-api.haulmer.com' : 'https://api.haulmer.com';

        $this->apiClient = (new ApiClient())
            ->setHeaders([
                'apikey' => $this->apiKey,
                'Content-Type' => 'application/json'
            ]);
    }

    public function getClient(){
        return $this->apiClient;
    }

    /**
     * Configura el tiempo de expiración de la caché para las peticiones.
     *
     * @param int $expiration_time Tiempo en segundos.
     * @return self
     */
    public function setCache($expiration_time) {
        $this->apiClient->cache($expiration_time);
        return $this;
    }

    /**
     * Emite un Documento Tributario Electrónico (DTE).
     *
     * @param array $dteData Datos del DTE según formato SII.
     * @param array $responseOptions Tipos de respuesta deseados (e.g., ['PDF', 'FOLIO']).
     * @param array|null $custom Campos personalizados (informationNote, paymentNote).
     * @param array|null $sendEmail Opciones de envío de email (to, CC, BCC).
     * @param string|null $idempotencyKey Clave de idempotencia para evitar duplicados.
     * @return mixed Respuesta de la API.
     */
    public function emitirDTE($dteData, $responseOptions = [], $custom = null, $sendEmail = null, $idempotencyKey = null) {
        $body = array_filter([
            'dte' => $dteData,
            'response' => $responseOptions,
            'custom' => $custom,
            'sendEmail' => $sendEmail
        ], function ($value) {
            return $value !== null && $value !== [];
        });

        $url = $this->base_url . '/v2/dte/document';

        // REQUEST efectivamente enviado
        // Files::dump(json_encode($body, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES), LOGS_PATH . 'v2-dte-document.json', false);
        // Logger::dd('OpenFacturaSDK emitirDTE endpoint', $url);
        // Logger::dd($this->apiClient->getRequestHeaders(), 'Headers');
        
        Files::dump($this->apiClient->dump(), LOGS_PATH . 'OpenFacturaSDK-emitirDTE-ApiClient.txt', false);

        if ($idempotencyKey) {
            $this->apiClient->addHeader('Idempotency-Key', $idempotencyKey);
        }

        $response = $this->apiClient
            ->setBody($body, true)
            ->request($url, 'POST')
            ->data();

        if ($idempotencyKey) {
            $this->apiClient->removeHeader('Idempotency-Key');
        }

        return $response;
    }

    /**
     * Consulta el estado de un DTE mediante su token.
     *
     * @param string $token Token del DTE.
     * @return mixed Respuesta de la API.
     */
    public function getDTEStatus($token) {
        return $this->apiClient
            ->request($this->base_url . '/v2/dte/document/' . $token . '/status', 'GET')
            ->data();
    }   

    /**
     * Anulación de un DTE tipo 52.
     *
     * @param int $folio Folio del documento.
     * @param string $fecha Fecha de anulación.
     * @return array Respuesta
     */
    public function anularDTE52($folio, $fecha) {
        throw new NotImplementedException(); // Not Implemented !
    }

    /**
     * Anula una Guía de Despacho Electrónica (DTE tipo 52).
     *
     * @param int $folio Folio del documento.
     * @param string $fecha Fecha de emisión (formato AAAA-MM-DD).
     * @return mixed Respuesta de la API.
     */
    public function anularGuiaDespacho($folio, $fecha) {
        $body = [
            'Dte' => 52,
            'Folio' => $folio,
            'Fecha' => $fecha
        ];

        return $this->apiClient
            ->setBody($body, true)
            ->request($this->base_url . '/v2/dte/anularGuiaDespacho', 'POST')
            ->data();
    }

    /**
     * Obtiene información de la empresa asociada a la API Key.
     *
     * @deprecated Use getOrganization() instead
     * @return mixed Respuesta de la API.
     */
    public function getCompanyInfo() {
        // El endpoint /v2/dte/company no existe, usar /v2/dte/organization
        return $this->getOrganization();
    }

    /**
     * Get taxpayer details by RUT.
     *
     * @param string $rut The RUT of the taxpayer.
     * @return mixed The API response data.
     */
    public function getTaxpayer($rut) {
        return $this->apiClient->get($this->base_url . '/v2/dte/taxpayer/' . urlencode($rut))->data();
    }

    /**
     * Lista contribuyentes asociados a la organización (hipotético).
     *
     * @param array $queryParams Filtros como estado, tipo, etc.
     * @return mixed Lista de contribuyentes.
     */
    public function listTaxpayers($queryParams = []) {
        $url = $this->base_url . '/v2/dte/taxpayers';
        if (!empty($queryParams)) {
            $url .= '?' . http_build_query($queryParams);
        }
        return $this->apiClient->get($url)->data();
    }

    /**
     * Get organization details.
     *
     * @return mixed The API response data.
     */
    public function getOrganization() {
        return $this->apiClient->get($this->base_url . '/v2/dte/organization')->data();
    }

    /**
     * Get organization documents.
     *
     * @param array $queryParams Optional query parameters for filtering.
     * @return mixed The API response data.
     */
    public function getOrganizationDocuments($queryParams = []) {
        $url = $this->base_url . '/v2/dte/organization/document';
        if (!empty($queryParams)) {
            $url .= '?' . http_build_query($queryParams);
        }
        return $this->apiClient->get($url)->data();
    }

    /**
     * Get purchase registry for a specific year and month.
     *
     * @param string $year The year (e.g., "2023").
     * @param string $month The month (e.g., "01").
     * @param array $queryParams Optional query parameters.
     * @return mixed The API response data.
     */
    public function getPurchaseRegistry($year, $month, $queryParams = []) {
        $url = $this->base_url . '/v2/dte/registry/purchase/' . urlencode($year) . '/' . urlencode($month);
        if (!empty($queryParams)) {
            $url .= '?' . http_build_query($queryParams);
        }
        return $this->apiClient->get($url)->data();
    }

    /**
     * Get sales registry for a specific year and month.
     *
     * @param string $year The year (e.g., "2023").
     * @param string $month The month (e.g., "01").
     * @param array $queryParams Optional query parameters.
     * @return mixed The API response data.
     */
    public function getSalesRegistry($year, $month, $queryParams = []) {
        $url = $this->base_url . '/v2/dte/registry/sales/' . urlencode($year) . '/' . urlencode($month);
        if (!empty($queryParams)) {
            $url .= '?' . http_build_query($queryParams);
        }
        return $this->apiClient->get($url)->data();
    }

    /**
     * Get a document by RUT, type, and folio.
     *
     * @param string $rut The RUT identifier.
     * @param string $type The document type.
     * @param string $folio The document folio number.
     * @return mixed The API response data.
     */
    public function getDocumentByRutTypeFolio($rut, $type, $folio) {
        $url = $this->base_url . '/v2/dte/document/' . urlencode($rut) . '/' . urlencode($type) . '/' . urlencode($folio);
        return $this->apiClient->get($url)->data();
    }

    /**
     * Get a document by token and value.
     *
     * @param string $token The token identifier.
     * @param string $value The value associated with the token.
     * @return mixed The API response data.
     */
    public function getDocumentByTokenValue($token, $value) {
        $url = $this->base_url . '/v2/dte/document/' . urlencode($token) . '/' . urlencode($value);
        return $this->apiClient->get($url)->data();
    }

    /**
     * Handle issued documents (exact purpose depends on API specifics).
     *
     * @param array $data Data for the issued document action.
     * @return mixed The API response data.
     */
    public function documentIssued($data) {
        return $this->apiClient->setBody($data)->post($this->base_url . '/v2/dte/document/issued')->data();
    }

    /**
     * Handle received documents.
     *
     * @param array $data Data for the received document action.
     * @return mixed The API response data.
     */
    public function documentReceived($data) {
        return $this->apiClient->setBody($data)->post($this->base_url . '/v2/dte/document/received')->data();
    }

    /**
     * Acknowledge received documents.
     *
     * @param array $data Data for the accusation of received documents.
     * @return mixed The API response data.
     */
    public function documentReceivedAccuse($data) {
        return $this->apiClient->setBody($data)->post($this->base_url . '/v2/dte/document/received/accuse')->data();
    }

    /**
     * Generate a self-service link for DTE (assumed endpoint).
     *
     * @param array $data Data required to generate the self-service link.
     * @return mixed The API response data.
     */
    public function emitirEnlaceAutoservicio($data) {
        return $this->apiClient
            ->setBody($data, true)
            ->post($this->base_url . '/v2/dte/selfservice')
            ->data();
    }

    /**
     * Obtiene detalles completos de un documento por su token.
     *
     * @param string $token Token del documento.
     * @return mixed Detalles del documento.
     */
    public function getDocumentDetails($token) {
        return $this->apiClient
            ->get($this->base_url . '/v2/dte/document/' . urlencode($token))
            ->data();
    }

    /**
     * Envía un documento por email.
     *
     * @param string $token Token del documento.
     * @param array $emailData Datos del email (to, subject, etc.).
     * @return mixed Respuesta de la API.
     */
    public function sendDocumentEmail($token, $emailData) {
        $url = $this->base_url . '/v2/dte/document/' . urlencode($token) . '/email';
        return $this->apiClient
            ->setBody($emailData, true)
            ->post($url)
            ->data();
    }

    /**
     * Verifica el estado de la API.
     *
     * @return mixed Estado de la API.
     */
    public function checkApiStatus() {
        return $this->apiClient
            ->get($this->base_url . '/v2/status')
            ->data();
    }    

    
}   

