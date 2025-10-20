<?php

namespace Boctulus\OpenfacturaSdk\Interfaces;

interface IOpenFactura {
    public function __construct($apiKey, $sandbox = false);
    public function setCache($expiration_time);
    public function emitirDTE($dteData, $responseOptions = [], $custom = null, $sendEmail = null, $idempotencyKey = null);
    public function getDTEStatus($token);
    public function anularGuiaDespacho($folio, $fecha);
    public function anularDTE52($folio, $fecha);
    public function getCompanyInfo();
    public function getTaxpayer($rut);
    public function listTaxpayers($queryParams = []);
    public function getOrganization();
    public function getOrganizationDocuments($queryParams = []);
    public function getPurchaseRegistry($year, $month, $queryParams = []);
    public function getSalesRegistry($year, $month, $queryParams = []);
    public function getDocumentByRutTypeFolio($rut, $type, $folio);
    public function getDocumentByTokenValue($token, $value);
    public function documentIssued($data);
    public function documentReceived($data);
    public function documentReceivedAccuse($data);
    public function emitirEnlaceAutoservicio($data);
    public function getDocumentDetails($token);
    public function sendDocumentEmail($token, $emailData);
    public function checkApiStatus();
}
