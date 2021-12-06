-- 
-- Set character set the client will use to send SQL statements to the server
--
SET NAMES 'utf8';

--
-- Create procedure `sp_insert_factura_contrato`
--
CREATE
DEFINER = 'root'@'%'
PROCEDURE sp_xml_factura_electronica (
 IN `p_idfactura` INT
)
MODIFIES SQL DATA
COMMENT 'Iterate an array and for each item invoke a callback procedure'
BEGIN

  /* 
   * Author: https://www.divergente.net.co Divergente Soluciones Informaticas S.A.S.
   * DBA: Jose Perez
   * Created: 13/09/2020
   * DBA Actualizador: Daniel Grajales
   * Update: 05/10/2020
   * Archivo: - Modulo de Facturación
   * Descripción: Se organiza sp para que reciba un array
  */ 

  DECLARE intError int;
  DECLARE varValorErrorMensaje varchar(20);
  DECLARE texErrorMensaje text;
  DECLARE varFullError varchar(4000);
  DECLARE lonXml LONGTEXT;
  DECLARE lonRutas LONGTEXT;
  DECLARE lonextUBLExtensions LONGTEXT;
  DECLARE varInvoiceAuthorization VARCHAR(1000);
  DECLARE varAuthorizationPeriod VARCHAR(1000);
  DECLARE varAuthorizedInvoices VARCHAR(1000);
  DECLARE varInvoiceSource VARCHAR(1000);
  DECLARE varSoftwareProvider VARCHAR(1000);
  DECLARE varSoftwareSecurityCode VARCHAR(1000);
  DECLARE varAuthorizationProvide VARCHAR(1000);
  DECLARE varQRCode VARCHAR(2000);
  DECLARE varUBLExtension VARCHAR(1000);  
  DECLARE varExtensionContent VARCHAR(1000);
  DECLARE lonSignature LONGTEXT;
  DECLARE varUBLVersionID VARCHAR(100) DEFAULT '<cbc:UBLVersionID>UBL 2.1</cbc:UBLVersionID>';
  DECLARE varCustomizationID VARCHAR(100) DEFAULT '<cbc:CustomizationID>05</cbc:CustomizationID>';
  DECLARE varProfileID VARCHAR(100) DEFAULT '<cbc:ProfileID>DIAN 2.1: Factura Electrónica de Venta</cbc:ProfileID>';
  DECLARE varProfileExecutionID VARCHAR(100) DEFAULT '<cbc:ProfileExecutionID>2</cbc:ProfileExecutionID>';
  DECLARE varID VARCHAR(100);
  DECLARE varUUID VARCHAR(1000);
  DECLARE varIssueDate VARCHAR(150);
  DECLARE varIssueTime VARCHAR(50);
  DECLARE varInvoiceTypeCode VARCHAR(50) DEFAULT '<cbc:InvoiceTypeCode>01</cbc:InvoiceTypeCode>';
  DECLARE lonNote LONGTEXT;
  DECLARE varDocumentCurrencyCode VARCHAR(500);  
  DECLARE varLineCountNumeric VARCHAR(150);
  DECLARE varInvoicePeriod VARCHAR(1000);
  DECLARE varBillingReference VARCHAR(2000);
  DECLARE loncAccountingSupplierParty LONGTEXT;
  DECLARE loncAccountingCustomerParty LONGTEXT;
  DECLARE varTaxRepresentativeParty VARCHAR(2000);
  DECLARE varPaymentMeans VARCHAR(1000);
  DECLARE lonTaxTotal LONGTEXT;
  DECLARE v_current_item longtext DEFAULT NULL;
  DECLARE lonTaxSubtotal LONGTEXT;
  DECLARE lonLegalMonetaryTotal LONGTEXT;
  DECLARE lonInvoiceLine LONGTEXT;
  DECLARE v_current_item_detalle longtext DEFAULT NULL;
  DECLARE lonData LONGTEXT;
  
  
  
  DECLARE EXIT HANDLER FOR SQLEXCEPTION
  BEGIN
    GET DIAGNOSTICS CONDITION 1 varValorErrorMensaje = RETURNED_SQLSTATE,
    intError = MYSQL_ERRNO, texErrorMensaje = MESSAGE_TEXT;
    SET varFullError = CONCAT("ERROR ", intError, " (", varValorErrorMensaje, "): ", texErrorMensaje);
    SELECT
      varFullError AS RESPUESTADB;
    ROLLBACK;
  END;

  START TRANSACTION;
  
  SET lonRutas = '<?xml version="1.0" encoding="UTF-8" standalone="no"?>
  <Invoice
  xmlns="urn:oasis:names:specification:ubl:schema:xsd:Invoice-2"
  xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" 
  xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2" 
  xmlns:ds="http://www.w3.org/2000/09/xmldsig#" 
  xmlns:ext="urn:oasis:names:specification:ubl:schema:xsd:CommonExtensionComponents-2"
  xmlns:sts="dian:gov:co:facturaelectronica:Structures-2-1"  
  xmlns:xades="http://uri.etsi.org/01903/v1.3.2#" 
  xmlns:xades141="http://uri.etsi.org/01903/v1.4.1#" 
  xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" 
  xsi:schemaLocation="urn:oasis:names:specification:ubl:schema:xsd:Invoice-2 
  http://docs.oasis-open.org/ubl/os-UBL-2.1/xsd/maindoc/UBL-Invoice-2.1.xsd">';
	
	SET lonextUBLExtensions = '\r\n<ext:UBLExtensions>\r\n<ext:UBLExtension>\r\n<ext:ExtensionContent>\r\n<sts:DianExtensions>\r\n<sts:InvoiceControl>';
	
	SELECT 
	R.res_varCodigoResolucion ,date_format(R.res_datFechaInicial, "%Y-%m-%d") ,date_format(R.res_datFechaFinal, "%Y-%m-%d") 
	,C.cse_varPrefijo,C.cse_intDesde,C.cse_intHasta,F.fac_varNroDocumento,F.fac_varNota,F.fac_dateFecha
	INTO 
	@InvoiceAuthorization,@StartDate,@EndDate
	,@cse_varPrefijo,@cse_intDesde,@cse_intHasta,@fac_varNroDocumento,@fac_varNota,@fac_dateFecha
	FROM tbl_factura AS F
	INNER JOIN tbl_consecutivo AS C ON F.cse_intIdConsecutivo = C.cse_intId 
	INNER JOIN tbl_resolucion AS R ON R.res_intId = C.res_intIdResolucion
	WHERE F.fac_intId = p_idfactura;
	
	SET varInvoiceAuthorization = CONCAT('<sts:InvoiceAuthorization>',@InvoiceAuthorization,'</sts:InvoiceAuthorization>');
	
	SET varAuthorizationPeriod = CONCAT('<sts:AuthorizationPeriod>',
	'<cbc:StartDate>',@StartDate,'</cbc:StartDate>',
   '<cbc:EndDate>',@EndDate,'</cbc:EndDate>'
	,'</sts:AuthorizationPeriod>');
	
	SET varAuthorizedInvoices = CONCAT('<sts:AuthorizedInvoices>',
	'<sts:Prefix>',@cse_varPrefijo,'</sts:Prefix>',
   '<sts:From>',@cse_intDesde,'</sts:From>'
   '<sts:To>',@cse_intHasta,'</sts:To>'
	,'</sts:AuthorizedInvoices>');
	
	SET varInvoiceSource = CONCAT('</sts:InvoiceControl><sts:InvoiceSource>',
	'<cbc:IdentificationCode listAgencyID="6" listAgencyName="United Nations Economic Commission for Europe" 
	listSchemeURI="urn:oasis:names:specification:ubl:codelist:gc:CountryIdentificationCode-2.1">CO</cbc:IdentificationCode>'
	,'</sts:InvoiceSource>');
	
   SET varSoftwareProvider = CONCAT('<sts:SoftwareProvider>'
	,'<sts:ProviderID'
	,' schemeAgencyID="195" schemeAgencyName="CO, DIAN (Dirección de Impuestos y Aduanas Nacionales)" schemeID="4" schemeName="31">800197268'
	,'</sts:ProviderID>'
   ,'<sts:SoftwareID'
	,' schemeAgencyID="195" schemeAgencyName="CO, DIAN (Dirección de Impuestos y Aduanas Nacionales)">56f2ae4e-9812-4fad-9255-08fcfcd5ccb0'
	,'</sts:SoftwareID>'
	,'</sts:SoftwareProvider>');
	
	SET varSoftwareSecurityCode = CONCAT('<sts:SoftwareSecurityCode'
	,' schemeAgencyID="195" schemeAgencyName="CO, DIAN (Dirección de Impuestos y Aduanas Nacionales)" >'
	,'a8d18e4e5aa00b44a0b1f9ef413ad8215116bd3ce91730d580eaed795c83b5a32fe6f0823abc71400b3d59eb542b7de8'
	,'</sts:SoftwareSecurityCode>');
	
	SET varAuthorizationProvide = CONCAT('<sts:AuthorizationProvider>'
	,' <sts:AuthorizationProviderID  '
	,' schemeAgencyID="195" schemeAgencyName="CO, DIAN (Dirección de Impuestos y Aduanas Nacionales)" schemeID="4" schemeName="31">'
	,'800197268'
	,'</sts:AuthorizationProviderID>'
	,'</sts:AuthorizationProvider>');
	
	SET varQRCode = CONCAT('<sts:QRCode>'
	,'NA'
	,'</sts:QRCode>');
	
	SET varUBLExtension = CONCAT('<ext:UBLExtension><ext:ExtensionContent>');
	
	SET lonSignature = CONCAT('<ds:Signature Id="xmldsig-d0322c4f-be87-495a-95d5-9244980495f4">'
	, ' <ds:SignedInfo> '
	, ' 	<ds:CanonicalizationMethod Algorithm="http://www.w3.org/TR/2001/REC-xml-c14n-20010315"/> '
	, ' 	<ds:SignatureMethod Algorithm="http://www.w3.org/2001/04/xmldsig-more#rsa-sha256"/> '
	, ' 	<ds:Reference Id="xmldsig-d0322c4f-be87-495a-95d5-9244980495f4-ref0" URI=""> '	
	, ' 		<ds:Transforms> '
	, ' 			<ds:Transform Algorithm="http://www.w3.org/2000/09/xmldsig#enveloped-signature"/> '
	, ' 		</ds:Transforms> '
	, ' 		<ds:DigestMethod Algorithm="http://www.w3.org/2001/04/xmlenc#sha256"/> '	
	, ' 		<ds:DigestValue>akcOQ5qEh4dkMwt0d5BoXRR8Bo4vdy9DBZtfF5O0SsA=</ds:DigestValue> '
	, '	</ds:Reference> '
	, '	<ds:Reference URI="#xmldsig-87d128b5-aa31-4f0b-8e45-3d9cfa0eec26-keyinfo"> '
	, '		<ds:DigestMethod Algorithm="http://www.w3.org/2001/04/xmlenc#sha256"/> '
	, '		<ds:DigestValue>troRYR2fcmJLV6gYibVM6XlArbddSCkjYkACZJP47/4=</ds:DigestValue> '
	, '	</ds:Reference> '
	, '	<ds:Reference Type="http://uri.etsi.org/01903#SignedProperties" URI="#xmldsig-d0322c4f-be87-495a-95d5-9244980495f4-signedprops"> '
	, '		<ds:DigestMethod Algorithm="http://www.w3.org/2001/04/xmlenc#sha256"/> '
	, '		<ds:DigestValue>hpIsyD/08hVUc1exnfEyhGyKX5s3pUPbpMKmPhkPPqU=</ds:DigestValue> '
	, '	</ds:Reference> '
	, ' </ds:SignedInfo> '
	, ' <ds:SignatureValue Id="xmldsig-d0322c4f-be87-495a-95d5-9244980495f4-sigvalue"> '
	, ' q4HWeb47oLdDM4D3YiYDOSXE4YfSHkQKxUfSYiEiPuP2XWvD7ELZTC4ENFv6krgDAXczmi0W7OMi '
	, ' LIVvuFz0ohPUc4KNlUEzqSBHVi6sC34sCqoxuRzOmMEoCB9Tr4VICxU1Ue9XhgP7o6X4f8KFAQWW NaeTtA6WaO/yUtq91MKP59aAnFMfYl8lXpaS0kpUwuui3wdCZsGycsl1prEWiwzpaukEUOXyTo7o '
	, ' RBOuNsDIUhP24Fv1alRFnX6/9zEOpRTs4rEQKN3IQnibF757LE/nnkutElZHTXaSV637gpHjXoUN 5JrUwTNOXvmFS98N6DczCQfeNuDIozYwtFVlMw== '
	, ' </ds:SignatureValue> '
	, ' <ds:KeyInfo Id="xmldsig-87d128b5-aa31-4f0b-8e45-3d9cfa0eec26-keyinfo"> '
	, ' 	<ds:X509Data>'
	, ' 		<ds:X509Certificate> 
				MIIIODCCBiCgAwIBAgIIbAsHYmJtoOIwDQYJKoZIhvcNAQELBQAwgbQxIzAhBgkqhkiG9w0BCQEW FGluZm9AYW5kZXNzY2QuY29tLmNvMSMwIQYDVQQDExpDQSBBTkRFUyBTQ0QgUy5BLiBDbGFzZSBJ '
	, ' 		STEwMC4GA1UECxMnRGl2aXNpb24gZGUgY2VydGlmaWNhY2lvbiBlbnRpZGFkIGZpbmFsMRMwEQYD VQQKEwpBbmRlcyBTQ0QuMRQwEgYDVQQHEwtCb2dvdGEgRC5DLjELMAkGA1UEBhMCQ08wHhcNMTcw 
				OTE2MTM0ODE5WhcNMjAwOTE1MTM0ODE5WjCCARQxHTAbBgNVBAkTFENhbGxlIEZhbHNhIE5vIDEy IDM0MTgwNgYJKoZIhvcNAQkBFilwZXJzb25hX2p1cmlkaWNhX3BydWViYXMxQGFuZGVzc2NkLmNv 
				bS5jbzEsMCoGA1UEAxMjVXN1YXJpbyBkZSBQcnVlYmFzIFBlcnNvbmEgSnVyaWRpY2ExETAPBgNV BAUTCDExMTExMTExMRkwFwYDVQQMExBQZXJzb25hIEp1cmlkaWNhMSgwJgYDVQQLEx9DZXJ0aWZp 
				Y2FkbyBkZSBQZXJzb25hIEp1cmlkaWNhMQ8wDQYDVQQHEwZCb2dvdGExFTATBgNVBAgTDEN1bmRp bmFtYXJjYTELMAkGA1UEBhMCQ08wggEiMA0GCSqGSIb3DQEBAQUAA4IBDwAwggEKAoIBAQC0Dn8t 
				oZ2CXun+63zwYecJ7vNmEmS+YouH985xDek7ImeE9lMBHXE1M5KDo7iT/tUrcFwKj717PeVL52Nt B6WU4+KBt+nrK+R+OSTpTno5EvpzfIoS9pLI74hHc017rY0wqjl0lw+8m7fyLfi/JO7AtX/dthS+ 
				MKHIcZ1STPlkcHqmbQO6nhhr/CGl+tKkCMrgfEFIm1kv3bdWqk3qHrnFJ6s2GoVNZVCTZW/mOzPC NnnUW12LDd/Kd+MjN6aWbP0D/IJbB42Npqv8+/oIwgCrbt0sS1bysUgdT4im9bBhb00MWVmNRBBe 
				3pH5knzkBid0T7TZsPCyiMBstiLT3yfpAgMBAAGjggLpMIIC5TAMBgNVHRMBAf8EAjAAMB8GA1Ud IwQYMBaAFKhLtPQLp7Zb1KAohRCdBBMzxKf3MDcGCCsGAQUFBwEBBCswKTAnBggrBgEFBQcwAYYb 
				aHR0cDovL29jc3AuYW5kZXNzY2QuY29tLmNvMIIB4wYDVR0gBIIB2jCCAdYwggHSBg0rBgEEAYH0 SAECCQIFMIIBvzBBBggrBgEFBQcCARY1aHR0cDovL3d3dy5hbmRlc3NjZC5jb20uY28vZG9jcy9E
				UENfQW5kZXNTQ0RfVjIuNS5wZGYwggF4BggrBgEFBQcCAjCCAWoeggFmAEwAYQAgAHUAdABpAGwA aQB6AGEAYwBpAPMAbgAgAGQAZQAgAGUAcwB0AGUAIABjAGUAcgB0AGkAZgBpAGMAYQBkAG8AIABl 
				AHMAdADhACAAcwB1AGoAZQB0AGEAIABhACAAbABhAHMAIABQAG8AbADtAHQAaQBjAGEAcwAgAGQA ZQAgAEMAZQByAHQAaQBmAGkAYwBhAGQAbwAgAGQAZQAgAFAAZQByAHMAbwBuAGEAIABKAHUAcgDt 
				AGQAaQBjAGEAIAAoAFAAQwApACAAeQAgAEQAZQBjAGwAYQByAGEAYwBpAPMAbgAgAGQAZQAgAFAA cgDhAGMAdABpAGMAYQBzACAAZABlACAAQwBlAHIAdABpAGYAaQBjAGEAYwBpAPMAbgAgACgARABQ 
				AEMAKQAgAGUAcwB0AGEAYgBsAGUAYwBpAGQAYQBzACAAcABvAHIAIABBAG4AZABlAHMAIABTAEMA RDAdBgNVHSUEFjAUBggrBgEFBQcDAgYIKwYBBQUHAwQwRgYDVR0fBD8wPTA7oDmgN4Y1aHR0cDov 
				L3d3dy5hbmRlc3NjZC5jb20uY28vaW5jbHVkZXMvZ2V0Q2VydC5waHA/Y3JsPTEwHQYDVR0OBBYE FL9BXJHmFVE5c5Ai8B1bVBWqXsj7MA4GA1UdDwEB/wQEAwIE8DANBgkqhkiG9w0BAQsFAAOCAgEA 
				b/pa7yerHOu1futRt8QTUVcxCAtK9Q00u7p4a5hp2fVzVrhVQIT7Ey0kcpMbZVPgU9X2mTHGfPdb R0hYJGEKAxiRKsmAwmtSQgWh5smEwFxG0TD1chmeq6y0GcY0lkNA1DpHRhSK368vZlO1p2a6S13Y 
				1j3tLFLqf5TLHzRgl15cfauVinEHGKU/cMkjLwxNyG1KG/FhCeCCmawATXWLgQn4PGgvKcNrz+y0 cwldDXLGKqriw9dce2Zerc7OCG4/XGjJ2PyZOJK9j1VYIG4pnmoirVmZbKwWaP4/TzLs6LKaJ4b6 
				6xLxH3hUtoXCzYQ5ehYyrLVwCwTmKcm4alrEht3FVWiWXA/2tj4HZiFoG+I1OHKmgkNv7SwHS7z9 tFEFRaD3W3aD7vwHEVsq2jTeYInE0+7r2/xYFZ9biLBrryl+q22zM5W/EJq6EJPQ6SM/eLqkpzqM 
				EF5OdcJ5kIOxLbrIdOh0+grU2IrmHXr7cWNP6MScSL7KSxhjPJ20F6eqkO1Z/LAxqNslBIKkYS24 VxPbXu0pBXQvu+zAwD4SvQntIG45y/67h884I/tzYOEJi7f6/NFAEuV+lokw/1MoVsEgFESASI9s 
				N0DfUniabyrZ3nX+LG3UFL1VDtDPWrLTNKtb4wkKwGVwqtAdGFcE+/r/1WG0eQ64xCq0NLutCxg= </ds:X509Certificate>'
	, ' 	</ds:X509Data> '
	, ' </ds:KeyInfo> '
	, ' <ds:Object> '
	, ' 	<xades:QualifyingProperties Target="#xmldsig-d0322c4f-be87-495a-95d5-9244980495f4"> '
	, ' 		<xades:SignedProperties Id="xmldsig-d0322c4f-be87-495a-95d5-9244980495f4-signedprops"> '
	, ' 			<xades:SignedSignatureProperties> '
	, ' 				<xades:SigningTime>2019-06-21T19:09:35.993-05:00</xades:SigningTime> '
	, ' 				<xades:SigningCertificate> '
	, ' 					<xades:Cert> '
	, ' 						<xades:CertDigest> '
	, ' 							<ds:DigestMethod Algorithm="http://www.w3.org/2001/04/xmlenc#sha256"/> '
	, ' 							<ds:DigestValue>nem6KXhqlV0A0FK5o+MwJZ3Y1aHgmL1hDs/RMJu7HYw=</ds:DigestValue> '
	, ' 						</xades:CertDigest> '
	, ' 						<xades:IssuerSerial> '
	, ' 							<ds:X509IssuerName>C=CO,L=Bogota D.C.,O=Andes SCD.,OU=Division de certificacion entidad final,CN=CA ANDES SCD S.A. Clase '
	, ' 							II,1.2.840.113549.1.9.1=#1614696e666f40616e6465737363642e636f6d2e636f</ds:X509IssuerName> '
	, ' 							<ds:X509SerialNumber>7785324499979575522</ds:X509SerialNumber> '
	, ' 						</xades:IssuerSerial> '
	, ' 					</xades:Cert> '
	, ' 					<xades:Cert> '
	, ' 						<xades:CertDigest> '
	, ' 							<ds:DigestMethod Algorithm="http://www.w3.org/2001/04/xmlenc#sha256"/> '
	, ' 							<ds:DigestValue>oEsyOEeUGTXr45Jr0jHJx3l/9CxcsxPMOTarEiXOclY=</ds:DigestValue> '
	, ' 						</xades:CertDigest> '
	, ' 						<xades:IssuerSerial> '
	, ' 							<ds:X509IssuerName>C=CO,L=Bogota D.C.,O=Andes SCD,OU=Division de certificacion,CN=ROOT CA ANDES SCD S.A.,1.2.840.113549.1.9.1=#1614696e666f40616e6465737363642e636f6d2e636f</ds:X509IssuerName> '
	, ' 							<ds:X509SerialNumber>8136867327090815624</ds:X509SerialNumber> '
	, ' 						</xades:IssuerSerial> '
	, ' 					</xades:Cert> '
	, ' 					<xades:Cert> '		
	, ' 						<xades:CertDigest> '	
	, ' 							<ds:DigestMethod Algorithm="http://www.w3.org/2001/04/xmlenc#sha256"/> '
	, ' 							<ds:DigestValue>Cs7emRwtXWVYHJrqS9eXEXfUcFyJJBqFhDFOetHu8ts=</ds:DigestValue> '		
	, ' 						</xades:CertDigest> '
	, ' 						<xades:IssuerSerial> '	
	, ' 							<ds:X509IssuerName>C=CO,L=Bogota D.C.,O=Andes SCD,OU=Division de certificacion,CN=ROOT CA ANDES SCD S.A.,1.2.840.113549.1.9.1=#1614696e666f40616e6465737363642e636f6d2e636f</ds:X509IssuerName> '
	, ' 							<ds:X509SerialNumber>3184328748892787122</ds:X509SerialNumber> '	
	, ' 						</xades:IssuerSerial> '		
	, ' 					</xades:Cert> '		
	, ' 			</xades:SigningCertificate> '
	, ' 	<xades:SignaturePolicyIdentifier> '
	, ' 		<xades:SignaturePolicyId> '
	, ' 			<xades:SigPolicyId> '
	, ' 				<xades:Identifier>https://facturaelectronica.dian.gov.co/politicadefirma/v1/politicadefirmav2.pdf</xades:Identifier> '
	, ' 			</xades:SigPolicyId> '
	, ' 			<xades:SigPolicyHash> '
	, ' 				<ds:DigestMethod Algorithm="http://www.w3.org/2001/04/xmlenc#sha256"/> '
	, ' 				<ds:DigestValue>dMoMvtcG5aIzgYo0tIsSQeVJBDnUnfSOfBpxXrmor0Y=</ds:DigestValue> '
	, ' 			</xades:SigPolicyHash> '
	, ' 		</xades:SignaturePolicyId> '
	, ' 	</xades:SignaturePolicyIdentifier> '
	, ' 	<xades:SignerRole> '
	, ' 		<xades:ClaimedRoles> '
	, ' 			<xades:ClaimedRole>supplier</xades:ClaimedRole> '
	, ' 		</xades:ClaimedRoles> '
	, ' 	</xades:SignerRole> '
	, '  </xades:SignedSignatureProperties> '
	, ' </xades:SignedProperties> '
	, '</xades:QualifyingProperties>'
	, '</ds:Object>'
	, '</ds:Signature>');
	
   SET varID = CONCAT('<cbc:ID>',@cse_varPrefijo,@fac_varNroDocumento,'</cbc:ID>'); 
   
   SET varUUID = '<cbc:UUID schemeID="2" schemeName="CUFE-SHA384"></cbc:UUID>';
   
   SET varIssueDate = CONCAT('<cbc:IssueDate>',date_format(NOW(), "%Y-%m-%d"),'</cbc:IssueDate>');
   
   SELECT CONCAT(TIME(CONVERT_TZ(NOW(),'Europe/London','America/Bogota')),'-', '05:00')
	INTO 
	@varIssueTime;
   
   SET varIssueTime = CONCAT('<cbc:IssueTime>',@varIssueTime,'</cbc:IssueTime>');
   
   SET lonNote = CONCAT('<cbc:Note>' , @fac_varNota ,'</cbc:Note>');
   
   SET varDocumentCurrencyCode = '<cbc:DocumentCurrencyCode listAgencyID="6" listAgencyName="United Nations Economic Commission for Europe" listID="ISO 4217 Alpha">COP</cbc:DocumentCurrencyCode>';
   
  	SELECT 
	  COUNT(fd.fde_intId)
	  INTO
	  @LineCountNumeric
	FROM tbl_factura_detalle  AS fd 
	WHERE fd.fac_intIdFactura = p_idfactura;
	
	SET varLineCountNumeric = CONCAT('<cbc:LineCountNumeric>',@LineCountNumeric,'</cbc:LineCountNumeric>');
	
	SELECT DATE_FORMAT(@fac_dateFecha - INTERVAL DAYOFMONTH(@fac_dateFecha) - 1 DAY, "%Y-%m-%d")
	INTO 
	@fechaInicioPeriodo;
	
	SELECT LAST_DAY(@fac_dateFecha) 
	INTO 
	@fechaFinPeriodo;
	
	SET varInvoicePeriod = CONCAT('<cac:InvoicePeriod>'
	,'<cbc:StartDate>',@fechaInicioPeriodo,'</cbc:StartDate>'
	,'<cbc:EndDate>',@fechaFinPeriodo,'</cbc:EndDate>'
	,'</cac:InvoicePeriod>');
	
	SET varBillingReference = CONCAT('<cac:BillingReference>'
	,'<cac:InvoiceDocumentReference>'
	,'		<cbc:ID>',' ','</cbc:ID>'
	,'		<cbc:UUID>',' ','</cbc:UUID>'
	,'		<cbc:IssueDate>',' ','</cbc:IssueDate>'
	,'		<cbc:DocumentDescription>',' ','</cbc:DocumentDescription>'
	,'</cac:InvoiceDocumentReference>'
	,'</cac:BillingReference>');
	
	
	SELECT 
	em.emp_varRazonSocial, CONCAT(d.dep_varCodigoDepartamento,c.ciu_varCodigo), c.ciu_varCiudad,d.dep_varDepartamento,d.dep_varCodigoDepartamento
	,em.emp_varDireccion,p.pai_varPais,em.emp_varNit
	INTO 
	@emp_varRazonSocial, @CodigoCiudad,@ciu_varCiudad,@dep_varDepartamento,@dep_varCodigoDepartamento
	,@emp_varDireccion,@pai_varPais,@emp_varNit
	FROM tbl_empresa AS em
	INNER JOIN tbl_ciudad AS c ON c.ciu_intId = em.ciu_intIdCiudad
	INNER JOIN tbl_pais AS p ON p.pai_intId = em.pai_intIdPais
	INNER JOIN tbl_departamento AS d ON d.dep_intId = em.dep_intIdDepartamento;
	
	SET @emp_varNit = '901277290';
	SET @emp_varNitDF = '7';
	
	-- informacion del vendedor.	
	
	SET loncAccountingSupplierParty = CONCAT(
	' <cac:AccountingSupplierParty>'
	,'	<cbc:AdditionalAccountID>1</cbc:AdditionalAccountID>'
	,'	<cac:Party> '
	,'		<cac:PartyName> '
	,'			<cbc:Name>',@emp_varRazonSocial,'</cbc:Name> '
	,'		</cac:PartyName> '
	,'		<cac:PartyName> '
	,'			<cbc:Name>',@emp_varRazonSocial,'</cbc:Name> '
	,'		</cac:PartyName> '
	,'		<cac:PartyName> '
	,'			<cbc:Name>DIAN</cbc:Name> '
	,'		</cac:PartyName> '
	,'		<cac:PhysicalLocation> '
	,'			<cac:Address> '
	,'				<cbc:ID>',@CodigoCiudad,'</cbc:ID> '
	,'				<cbc:CityName>',@ciu_varCiudad,'</cbc:CityName> '
	,'				<cbc:CountrySubentity>',@dep_varDepartamento,'</cbc:CountrySubentity> '
	,'				<cbc:CountrySubentityCode>',@dep_varCodigoDepartamento,'</cbc:CountrySubentityCode> '
	,'				<cac:AddressLine> '
	,'					<cbc:Line>',@emp_varDireccion,'</cbc:Line> '
	,'				</cac:AddressLine> '
	,'				<cac:Country> '
	,'					<cbc:IdentificationCode>CO</cbc:IdentificationCode> '
	,'					<cbc:Name languageID="es">',@pai_varPais,'</cbc:Name> '
	,'				</cac:Country> '
	,'			</cac:Address> '
	,'		</cac:PhysicalLocation> '
	,'		<cac:PartyTaxScheme> '
	,'			<cbc:RegistrationName>DIAN</cbc:RegistrationName> '
	,'			<cbc:CompanyID schemeAgencyID="195" schemeAgencyName="CO, DIAN (Dirección de Impuestos y Aduanas Nacionales)" schemeID="7" schemeName="31">',@emp_varNit,'</cbc:CompanyID> '
	,'			<cbc:TaxLevelCode listName="05">R-99-PN</cbc:TaxLevelCode> '
	,'			<cac:RegistrationAddress> '
	,'				<cbc:ID>',@CodigoCiudad,'</cbc:ID> '
	,'				<cbc:CityName>',@ciu_varCiudad,'</cbc:CityName> '
	,'				<cbc:CountrySubentity>',@dep_varDepartamento,'</cbc:CountrySubentity> '
	,'				<cbc:CountrySubentityCode>',@dep_varCodigoDepartamento,'</cbc:CountrySubentityCode> '
	,'				<cac:AddressLine> '
	,'					<cbc:Line>',@emp_varDireccion,'</cbc:Line> '
	,'				</cac:AddressLine> '
	,'				<cac:Country> '
	,'					<cbc:IdentificationCode>CO</cbc:IdentificationCode> '
	,'					<cbc:Name languageID="es">',@pai_varPais,'</cbc:Name> '
	,'				</cac:Country> '
	,'		   </cac:RegistrationAddress> '
	,'		   <cac:TaxScheme> '
	,'		   	<cbc:ID>01</cbc:ID> '
	,'		   	<cbc:Name>IVA</cbc:Name> '
	,'		   </cac:TaxScheme> '
	,'		</cac:PartyTaxScheme> '
	,'		<cac:PartyLegalEntity> '
	,'			<cbc:RegistrationName>DIAN</cbc:RegistrationName> '
	,'			<cbc:CompanyID schemeAgencyID="195" schemeAgencyName="CO, DIAN (Dirección de Impuestos y Aduanas Nacionales)" schemeID="9" schemeName="31">',@emp_varNit,'</cbc:CompanyID> '
	,'			<cac:CorporateRegistrationScheme> '
	,'				<cbc:ID>',@cse_varPrefijo,'</cbc:ID> '
	,'				<cbc:Name>',@fac_varNroDocumento,'</cbc:Name> '
	,'			</cac:CorporateRegistrationScheme> '
	,'		</cac:PartyLegalEntity> '
	,'		<cac:Contact> '
	,'			<cbc:Name></cbc:Name> '
	,'			<cbc:Telephone></cbc:Telephone> '
	,'			<cbc:ElectronicMail>contabilidad@divergente.net.co</cbc:ElectronicMail> '
	,'			<cbc:Note></cbc:Note> '
	,'		</cac:Contact> '
	,'</cac:Party> '
	,'</cac:AccountingSupplierParty> '
	
	);

	-- informacion del cliente -> alquirinete.
	
	SELECT 
	tip.tpr_varCodigoDian,per.per_varNombreCompleto,CONCAT(d.dep_varCodigoDepartamento,c.ciu_varCodigo),c.ciu_varCiudad,d.dep_varDepartamento,d.dep_varCodigoDepartamento
	,per.per_varDireccion,p.pai_varPais,tid.tip_varCodigoDian,per.per_varIdentificacion,per.per_varDV,per.per_varMatriculaMercantil
	,fdp.fdp_varCodigoDian,mp.tmp_varCodigoDian,date_format(fac.fac_dateFechaVencimiento, "%Y-%m-%d")
	INTO 
	@AdditionalAccountIDTipoPer,@per_varNombreCompleto,@CodigoMunicipioAlq,@ciu_varCiudadAlq,@dep_varDepartamentoAlq,@dep_varCodigoDepartamentoAlq
	,@per_varDireccionAlq,@pai_varPaisAlq,@schemeNameTipoDoc,@per_varIdentificacionAlq,@per_varDVAlq,@per_varMatriculaMercantilAlq
	,@fdp_varCodigoDianAlq,@tmp_varCodigoDianAlq,@fac_dateFechaVencimientoAlq
	FROM tbl_factura AS fac
	INNER JOIN tbl_persona AS per ON per.per_intId = fac.per_intIdPersona
	INNER JOIN tbl_tipo_persona AS tip ON tip.tpr_intId = per.tpr_intIdTipoPersona
	INNER JOIN tbl_pais AS p ON p.pai_intId = per.pai_intIdPaisNacimiento
	INNER JOIN tbl_departamento AS d ON d.dep_intId = per.dep_intIdDepartamentoNacimiento
	INNER JOIN tbl_ciudad AS c ON c.ciu_intId = per.ciu_intIdCiudadNacimiento
	INNER JOIN tbl_tipo_documento AS tid ON tid.tid_intId = per.tid_intIdTipoDocumento
	INNER JOIN tbl_forma_de_pago AS fdp ON fdp.fdp_intId = fac.tdp_intIdFormaPago 
	INNER JOIN tbl_medio_pago AS mp ON mp.tmp_intId = fac.tmp_intIdMedioPago
	WHERE fac.fac_intId = p_idfactura;
	
	
	SET loncAccountingCustomerParty = CONCAT('
	<cac:AccountingCustomerParty> '
	,'<cbc:AdditionalAccountID>',@AdditionalAccountIDTipoPer,'</cbc:AdditionalAccountID> '
	,'<cac:Party> '
	,'	<cac:PartyIdentification>'
	,'	 <cbc:ID schemeAgencyID="195" schemeAgencyName="CO, DIAN (Dirección de Impuestos y Aduanas Nacionales)" schemeID="',@per_varDVAlq,'" schemeName="',@schemeNameTipoDoc,'">'
	,	 @per_varIdentificacionAlq
	,'	 </cbc:ID> '
	,'	</cac:PartyIdentification>'
	,'	<cac:PartyName> '
	,'		<cbc:Name>',@per_varNombreCompleto,'</cbc:Name>'
	,'	</cac:PartyName> '
	,'	<cac:PhysicalLocation> '
	,'		<cac:Address> '
	,'			<cbc:ID>',@CodigoMunicipioAlq,'</cbc:ID>'
	,'			<cbc:CityName>',@ciu_varCiudadAlq,'</cbc:CityName>'
	,'			<cbc:CountrySubentity>',@dep_varDepartamentoAlq,'</cbc:CountrySubentity>'
	,'			<cbc:CountrySubentityCode>',@dep_varCodigoDepartamentoAlq,'</cbc:CountrySubentityCode>'
	,'			<cac:AddressLine>'
	,'				<cbc:Line>',@per_varDireccionAlq,'</cbc:Line>'
	,'			</cac:AddressLine>'
	,'			<cac:Country>'
	,'				<cbc:IdentificationCode>CO</cbc:IdentificationCode>'
	,'				<cbc:Name languageID="es">',@pai_varPaisAlq,'</cbc:Name>'
	,'			</cac:Country>'
	,'		</cac:Address> '
	,'	</cac:PhysicalLocation> '
	,'<cac:PartyTaxScheme>'
	,'	 <cbc:RegistrationName>',@per_varNombreCompleto,'</cbc:RegistrationName>'
	,'	 <cbc:CompanyID schemeAgencyID="195" schemeAgencyName="CO, DIAN (Dirección de Impuestos y Aduanas Nacionales)" schemeID="',@per_varDVAlq,'" schemeName="',@schemeNameTipoDoc,'">'
	,	 @per_varIdentificacionAlq
	,'	 </cbc:CompanyID> '
	,'	 <cbc:TaxLevelCode listName="04">R-99-PN</cbc:TaxLevelCode> '
	,'	 <cac:RegistrationAddress>'
	,'    <cbc:ID>',@CodigoMunicipioAlq,'</cbc:ID>'
	,'		<cbc:CityName>',@ciu_varCiudadAlq,'</cbc:CityName>'
	,'		<cbc:CountrySubentity>',@dep_varDepartamentoAlq,'</cbc:CountrySubentity>'
	,'		<cbc:CountrySubentityCode>',@dep_varCodigoDepartamentoAlq,'</cbc:CountrySubentityCode>'
	,'		<cac:AddressLine>'
	,'			<cbc:Line>',@per_varDireccionAlq,'</cbc:Line>'
	,'		</cac:AddressLine>'
	,'		<cac:Country>'
	,'			<cbc:IdentificationCode>CO</cbc:IdentificationCode>'
	,'			<cbc:Name languageID="es">',@pai_varPaisAlq,'</cbc:Name>'
	,'		</cac:Country>'
	,'	 </cac:RegistrationAddress>'
	,'	 <cac:TaxScheme>'
	,'	 	<cbc:ID>01</cbc:ID>'
	,'	 	<cbc:Name>IVA</cbc:Name>'
	,'	 </cac:TaxScheme>'
	,'</cac:PartyTaxScheme>'
	,'<cac:PartyLegalEntity>'
	,'	<cbc:RegistrationName>',@per_varNombreCompleto,'</cbc:RegistrationName>'
	,'		<cbc:CompanyID schemeAgencyID="195" schemeAgencyName="CO, DIAN (Dirección de Impuestos y Aduanas Nacionales)" schemeID="',@per_varDVAlq,'" schemeName="',@schemeNameTipoDoc,'">'
	,	 	@per_varIdentificacionAlq
	,'	 	</cbc:CompanyID> '
	,'	<cac:CorporateRegistrationScheme>'
	,'		<cbc:Name>',@per_varMatriculaMercantilAlq,'</cbc:Name>'
	,'	</cac:CorporateRegistrationScheme>'
	,'</cac:PartyLegalEntity>'
	,'	<cac:Contact> '
	,'		<cbc:Name></cbc:Name> '
	,'		<cbc:Telephone></cbc:Telephone> '
	,'		<cbc:ElectronicMail></cbc:ElectronicMail> '
	,'		<cbc:Note></cbc:Note> '
	,'	</cac:Contact>'
	,'</cac:Party> '
	,'</cac:AccountingCustomerParty>');

	SET varTaxRepresentativeParty = '
	<cac:TaxRepresentativeParty>
		<cac:PartyIdentification>
			<cbc:ID schemeAgencyID="195" schemeAgencyName="CO, DIAN (Dirección de Impuestos y Aduanas Nacionales)" schemeID="4" schemeName="31">989123123</cbc:ID>
		</cac:PartyIdentification>
	</cac:TaxRepresentativeParty>';
	
	SET varPaymentMeans = CONCAT(
	'<cac:PaymentMeans>'
	,'	<cbc:ID>',@fdp_varCodigoDianAlq,'</cbc:ID>'
	,'	<cbc:PaymentMeansCode>',@tmp_varCodigoDianAlq,'</cbc:PaymentMeansCode>'
	,'	<cbc:PaymentDueDate>',@fac_dateFechaVencimientoAlq,'</cbc:PaymentDueDate>'
	,'	<cbc:PaymentID>1234</cbc:PaymentID>'
	,'</cac:PaymentMeans>'
	);
		
	
	-- Iva Factuacion Electronica
	
	SELECT f.fac_decIva 
		INTO 
		@fac_decIva
	FROM tbl_factura f 
	WHERE f.fac_intId = p_idfactura;
	
   SET @array_impuestos = (SELECT
     CONCAT(
     '[',
     GROUP_CONCAT(JSON_OBJECT(
     'fde_decPorcentajeIva', fde_decPorcentajeIva
     , 'fde_decValorIva', fde_decValorIva
     , 'ValorBase', ValorBase
     )),
     ']'
     ) MES
   FROM (
	   SELECT 
			fd.fde_decPorcentajeIva,
			sum(fd.fde_decValorIva) AS fde_decValorIva,
			SUM(fd.fde_decValor * fd.fde_decCantidad) AS ValorBase
		FROM tbl_factura_detalle AS fd
		INNER JOIN tbl_producto AS p ON p.pro_intId = fd.pro_intIdProducto
		INNER JOIN tbl_iva ON tbl_iva.iva_intId = p.iva_intIdIva 
		WHERE fd.fac_intIdFactura = p_idfactura
		GROUP BY fd.fde_decPorcentajeIva
		ORDER BY fd.fde_decPorcentajeIva DESC
	) AS S);
	
	SET @i = 0;
	SET @v_count = JSON_LENGTH(@array_impuestos);
	
	SET lonTaxSubtotal = '';
	
   WHILE @i < @v_count DO
    
	   SET v_current_item := JSON_EXTRACT(@array_impuestos, CONCAT('$[', @i, ']'));
	
	   SET @fde_decPorcentajeIva = JSON_EXTRACT(v_current_item, '$.fde_decPorcentajeIva');
	   SET @fde_decValorIva = JSON_EXTRACT(v_current_item, '$.fde_decValorIva');
	   SET @ValorBase = JSON_EXTRACT(v_current_item, '$.ValorBase');
	   
	   SET lonTaxSubtotal = CONCAT(lonTaxSubtotal,
		'<cac:TaxSubtotal>'
		,'		<cbc:TaxableAmount currencyID="COP">',ROUND(@ValorBase ,2),'</cbc:TaxableAmount>'
		,'		<cbc:TaxAmount currencyID="COP">',@fde_decValorIva,'</cbc:TaxAmount>'
		,'	<cac:TaxCategory>'
		,'		<cbc:Percent>',@fde_decPorcentajeIva,'</cbc:Percent>'
		,'	<cac:TaxScheme>'
		,'		<cbc:ID>01</cbc:ID>'
		,'		<cbc:Name>IVA</cbc:Name>'
		,'	</cac:TaxScheme>'
		,'	</cac:TaxCategory>'
		,'</cac:TaxSubtotal>');
	   
	   SET @i := @i + 1;

    END WHILE;
    
    
   SET lonTaxTotal = CONCAT(
	'<cac:TaxTotal>'
	,'	<cbc:TaxAmount currencyID="COP">',@fac_decIva,'</cbc:TaxAmount>'
	,	lonTaxSubtotal);
    
    
   -- Retefuente  Factuacion Electronica
	 
   SELECT 
		f.fac_decRetencion,fac_decReteIva,fac_decIca
		INTO
		@fac_decRetencion,@fac_decReteIva,@fac_decIca
	FROM tbl_factura AS f 
	WHERE f.fac_intId = p_idfactura;
	
	SET @lonTaxTotalRetencion = '';
	SET @lonTaxTotalReteIva = '';
	SET @lonTaxTotalReteIca = '';
	
	IF(@fac_decRetencion > 0) THEN 
	
		SELECT 
			f.fac_decBruto,f.fac_decRetencion,re.ret_decPorcentaje
			INTO 
			@fac_decBrutoRetencion,@fac_decRetencion,@ret_decPorcentaje
		FROM tbl_factura f 
		INNER JOIN tbl_cliente cl ON cl.per_intIdPersona = f.per_intIdPersona
		INNER JOIN tbl_cliente_retencion_cuentacontable c ON c.cli_intIdCliente = cl.cli_intId
		INNER JOIN tbl_retencion re ON re.ret_intId = c.rcl_intIdRetencion
		WHERE f.fac_intId = p_idfactura;
		
		
		SET @lonTaxTotalRetencion = CONCAT(
		'<cac:TaxTotal>'
		,'		<cbc:TaxAmount currencyID="COP">',@fac_decBrutoRetencion,'</cbc:TaxAmount>'
		,'	<cac:TaxSubtotal>'
		,'		<cbc:TaxableAmount currencyID="COP">',ROUND(@fac_decBrutoRetencion ,2),'</cbc:TaxableAmount>'
		,'		<cbc:TaxAmount currencyID="COP">',ROUND(@fac_decRetencion ,2),'</cbc:TaxAmount>'
		,'	<cac:TaxCategory>'
		,'		<cbc:Percent>',@ret_decPorcentaje,'</cbc:Percent>'
		,'	<cac:TaxScheme>'
		,'		<cbc:ID>06</cbc:ID>'
		,'		<cbc:Name>ReteRenta</cbc:Name>'
		,'	</cac:TaxScheme>'
		,'	</cac:TaxCategory>'
		,'</cac:TaxSubtotal>'
		,'</cac:TaxTotal>');
	
	END IF;
	
	
	-- Reteiva  Factuacion Electronica
	
	IF(@fac_decReteIva > 0) THEN 
	
		SELECT 
			f.fac_decIva,f.fac_decReteIva,riva.riv_decPorcentaje
			INTO 
			@fac_decIva,@fac_decReteIva,@riv_decPorcentaje
		FROM tbl_factura f 
		INNER JOIN tbl_cliente cl ON cl.per_intIdPersona = f.per_intIdPersona
		INNER JOIN tbl_cliente_reteiva_cuentacontable cret ON cret.cli_intIdCliente = cl.cli_intId
		INNER JOIN tbl_reteiva riva ON riva.riv_intId = cret.ric_intIdReteiva
		WHERE f.fac_intId = p_idfactura;
		
		
		SET @lonTaxTotalReteIva = CONCAT(
		'<cac:TaxTotal>'
		,'		<cbc:TaxAmount currencyID="COP">',@fac_decIva,'</cbc:TaxAmount>'
		,'	<cac:TaxSubtotal>'
		,'		<cbc:TaxableAmount currencyID="COP">',ROUND(@fac_decIva ,2),'</cbc:TaxableAmount>'
		,'		<cbc:TaxAmount currencyID="COP">',ROUND(@fac_decReteIva ,2),'</cbc:TaxAmount>'
		,'	<cac:TaxCategory>'
		,'		<cbc:Percent>',@riv_decPorcentaje,'</cbc:Percent>'
		,'	<cac:TaxScheme>'
		,'		<cbc:ID>05</cbc:ID>'
		,'		<cbc:Name>ReteIVA</cbc:Name>'
		,'	</cac:TaxScheme>'
		,'	</cac:TaxCategory>'
		,'</cac:TaxSubtotal>'
		,'</cac:TaxTotal>');
	
	END IF;
	
	
   -- Reteica  Factuacion Electronica
	
	IF(@fac_decIca > 0) THEN 
	
		SELECT 
			f.fac_decBruto,f.fac_decIca,rica.ric_decPorcentaje
			INTO 
			@fac_decBruto,@fac_decIca,@ric_decPorcentaje
		FROM tbl_factura f 
		INNER JOIN tbl_persona per ON per.per_intId = f.per_intIdPersona
		INNER JOIN tbl_ciudad c ON c.ciu_intId = per.ciu_intIdCiudadNacimiento
		INNER JOIN tbl_rete_ica rica ON rica.ric_intId = c.ica_intIdICA
		WHERE f.fac_intId = p_idfactura;
		
		SET @lonTaxTotalReteIca = CONCAT(
		'<cac:TaxTotal>'
		,'		<cbc:TaxAmount currencyID="COP">',ROUND(@fac_decBruto,2),'</cbc:TaxAmount>'
		,'	<cac:TaxSubtotal>'
		,'		<cbc:TaxableAmount currencyID="COP">',ROUND(@fac_decBruto ,2),'</cbc:TaxableAmount>'
		,'		<cbc:TaxAmount currencyID="COP">',ROUND(@fac_decIca ,2),'</cbc:TaxAmount>'
		,'	<cac:TaxCategory>'
		,'		<cbc:Percent>',@ric_decPorcentaje,'</cbc:Percent>'
		,'	<cac:TaxScheme>'
		,'		<cbc:ID>07</cbc:ID>'
		,'		<cbc:Name>ReteICA</cbc:Name>'
		,'	</cac:TaxScheme>'
		,'	</cac:TaxCategory>'
		,'</cac:TaxSubtotal>'
		,'</cac:TaxTotal>');
	
	END IF;
	

	
	SELECT 
		f.fac_decBruto,f.fac_decNeto
		INTO 
		@fac_decBruto,@fac_decNeto	
	FROM tbl_factura f WHERE f.fac_intId = p_idfactura;
	
	
	SELECT 
		ROUND(SUM(f.fde_decCantidad * f.fde_decValor),2)
		INTO 
		@fac_valorBase
	FROM tbl_factura_detalle AS f WHERE f.fac_intIdFactura = p_idfactura;
	
	SET lonLegalMonetaryTotal = CONCAT(
	'<cac:LegalMonetaryTotal>'
	,'		<cbc:LineExtensionAmount currencyID="COP">',@fac_decBruto,'</cbc:LineExtensionAmount>'
	,'		<cbc:TaxExclusiveAmount currencyID="COP">',@fac_decBruto,'</cbc:TaxExclusiveAmount>'
	,'		<cbc:TaxInclusiveAmount currencyID="COP">',@fac_decNeto,'</cbc:TaxInclusiveAmount>'
	,'		<cbc:PrepaidAmount currencyID="COP">0</cbc:PrepaidAmount>'
	,'		<cbc:PayableAmount currencyID="COP">',@fac_decNeto,'</cbc:PayableAmount>'
	,'</cac:LegalMonetaryTotal>'
	);
	
	-- Detallle Factuacion Electronica
	
   SET @array_detalle = (SELECT
     CONCAT(
     '[',
     GROUP_CONCAT(JSON_OBJECT(
     'fde_decCantidad', fde_decCantidad
     , 'fde_decValor', fde_decValor
     , 'pro_intCodigoBarras', pro_intCodigoBarras
     , 'fde_decValorTotal', fde_decValorTotal
     , 'fde_decPorcentajeIva', fde_decPorcentajeIva
     , 'fde_decValorIva', fde_decValorIva
     , 'pro_varNombreProducto', pro_varNombreProducto
     , 'pro_varCodigoProducto', pro_varCodigoProducto
     )),
     ']'
     ) MES
   FROM (
	  SELECT 
		fd.fde_decCantidad
		,fd.fde_decValor
		,p.pro_intCodigoBarras
		,(fd.fde_decValor * fd.fde_decCantidad) AS fde_decValorTotal
		,fd.fde_decPorcentajeIva
		,fd.fde_decValorIva
		,p.pro_varNombreProducto
		,p.pro_varCodigoProducto
		FROM tbl_factura_detalle AS fd
		INNER JOIN tbl_producto AS p ON p.pro_intId = fd.pro_intIdProducto
		INNER JOIN tbl_iva ON tbl_iva.iva_intId = p.iva_intIdIva 
		WHERE fd.fac_intIdFactura = p_idfactura
	) AS S);
	
	SET @id = 0;
	SET @item = 1;
	SET @v_count_detalle = JSON_LENGTH(@array_detalle);
	
	SET lonInvoiceLine = '';
	
	WHILE @id < @v_count_detalle DO
    
	   SET v_current_item_detalle := JSON_EXTRACT(@array_detalle, CONCAT('$[', @id, ']'));
	
	   SET @fde_decCantidad = JSON_EXTRACT(v_current_item_detalle, '$.fde_decCantidad');
	   SET @fde_decValor = JSON_EXTRACT(v_current_item_detalle, '$.fde_decValor');
	   SET @pro_intCodigoBarras = JSON_EXTRACT(v_current_item_detalle, '$.pro_intCodigoBarras');
		SET @fde_decValorTotal = JSON_EXTRACT(v_current_item_detalle, '$.fde_decValorTotal');
		SET @fde_decPorcentajeIva = JSON_EXTRACT(v_current_item_detalle, '$.fde_decPorcentajeIva');
		SET @fde_decValorIva = JSON_EXTRACT(v_current_item_detalle, '$.fde_decValorIva');
		SET @pro_varNombreProducto = JSON_EXTRACT(v_current_item_detalle, '$.pro_varNombreProducto');
		SET @pro_varCodigoProducto = JSON_EXTRACT(v_current_item_detalle, '$.pro_varCodigoProducto');
		
		SELECT REPLACE(@pro_varNombreProducto, '"', '') INTO @pro_varNombreProducto;
		   
	   SET lonInvoiceLine = CONCAT(lonInvoiceLine,
		'<cac:InvoiceLine>'
		,'		<cbc:ID>',@item,'</cbc:ID>'
		,'		<cbc:InvoicedQuantity unitCode="EA">',@fde_decCantidad,'</cbc:InvoicedQuantity>'
		,'		<cbc:LineExtensionAmount currencyID="COP">',@fde_decValor,'</cbc:LineExtensionAmount>'
		,'		<cbc:FreeOfChargeIndicator>false</cbc:FreeOfChargeIndicator>'
		,'	<cac:Delivery>'
		,'		<cac:DeliveryLocation>'
		,'			<cbc:ID schemeID="999" schemeName="EAN">',@pro_intCodigoBarras,'</cbc:ID>'
		,'		</cac:DeliveryLocation>'
		,'	</cac:Delivery>'
		,'	<cac:TaxTotal>'
		,'		<cbc:TaxAmount currencyID="COP">',@fde_decValorIva,'</cbc:TaxAmount>'
		,'		<cac:TaxSubtotal>'
		,'			<cbc:TaxableAmount currencyID="COP">',@fde_decValorTotal,'</cbc:TaxableAmount>'
		,'			<cbc:TaxAmount currencyID="COP">',@fde_decValorIva,'</cbc:TaxAmount>'
		,'			<cac:TaxCategory>'
		,'			<cbc:Percent>',@fde_decPorcentajeIva,'</cbc:Percent>'
		,'			<cac:TaxScheme>'
		,'				<cbc:ID>01</cbc:ID>'
		,'				<cbc:Name>IVA</cbc:Name>'
		,'			</cac:TaxScheme>'
		,'			</cac:TaxCategory>'
		,'		</cac:TaxSubtotal>'
		,'	</cac:TaxTotal>'
		,'	<cac:Item>'
		,'		<cbc:Description>',@pro_varNombreProducto,'</cbc:Description>'
		,'		<cac:SellersItemIdentification>'
		,'			<cbc:ID>',@pro_varCodigoProducto,'</cbc:ID>'
		,'		</cac:SellersItemIdentification>'
		,'		<cac:AdditionalItemIdentification>'
		,'			<cbc:ID schemeID="999" schemeName="EAN13">',@pro_intCodigoBarras,'</cbc:ID>'
		,'		</cac:AdditionalItemIdentification>'
		,'	</cac:Item>'
		,'	<cac:Price>'
		,'		<cbc:PriceAmount currencyID="COP">',@fde_decValor,'</cbc:PriceAmount>'
		,'		<cbc:BaseQuantity unitCode="EA">',@fde_decCantidad,'</cbc:BaseQuantity>'
		,'	</cac:Price>'
		,'</cac:InvoiceLine>'
		);
	   
	   SET @item := @item + 1 ;
 	   SET @id := @id + 1;

   END WHILE;
   
   SET lonData = CONCAT(
	'<DATA> '
		,'<UBL21>true</UBL21> '
		,'<Partnership> '
			,'<ID>',@emp_varNit,'</ID> '
		--	,'<TechKey></TechKey> '
		-- ,'<SetTestID></SetTestID> '
		,'</Partnership> '
	,'</DATA> '
	);
	
	  
	SET lonextUBLExtensions = 
	CONCAT(lonextUBLExtensions
	,varInvoiceAuthorization
	,varAuthorizationPeriod
	,varAuthorizedInvoices
	,varInvoiceSource
	,varSoftwareProvider
	,varSoftwareSecurityCode
	,varAuthorizationProvide
	,varQRCode
	,'\r\n</sts:DianExtensions>\r\n</ext:ExtensionContent>\r\n</ext:UBLExtension>'
	,varUBLExtension
	,lonSignature
	,'</ext:ExtensionContent></ext:UBLExtension>\r\n</ext:UBLExtensions>'
	,varUBLVersionID
	,varCustomizationID
	,varProfileID
	,varProfileExecutionID
	,varID
	,varUUID
	,varIssueDate
	,varIssueTime
	,varInvoiceTypeCode
	,lonNote
	,varDocumentCurrencyCode
	,varLineCountNumeric
	,varInvoicePeriod
	,varBillingReference
	,loncAccountingSupplierParty
	,loncAccountingCustomerParty
	,varTaxRepresentativeParty
	,varPaymentMeans
	,lonTaxTotal,'</cac:TaxTotal>'
	,@lonTaxTotalRetencion
	,@lonTaxTotalReteIva
	,@lonTaxTotalReteIca
	,lonLegalMonetaryTotal
	,lonInvoiceLine
	,lonData);
	
/*	SELECT 
	lonextUBLExtensions
	,varInvoiceAuthorization
	,varAuthorizationPeriod
	,varAuthorizedInvoices
	,varInvoiceSource
	,varSoftwareProvider
	,varSoftwareSecurityCode
	,varAuthorizationProvide
	,varQRCode
	,'\r\n</sts:DianExtensions>\r\n</ext:ExtensionContent>\r\n</ext:UBLExtension>'
	,varUBLExtension
	,lonSignature
	,'</ext:ExtensionContent></ext:UBLExtension>\r\n</ext:UBLExtensions>'
	,varUBLVersionID
	,varCustomizationID
	,varProfileID
	,varProfileExecutionID
	,varID
	,varUUID
	,varIssueDate
	,varIssueTime
	,varInvoiceTypeCode
	,lonNote
	,varDocumentCurrencyCode
	,varLineCountNumeric
	,varInvoicePeriod
	,varBillingReference
	,loncAccountingSupplierParty
	,loncAccountingCustomerParty
	,varTaxRepresentativeParty
	,varPaymentMeans
	,lonTaxTotal,'</cac:TaxTotal>'
	,@lonTaxTotalRetencion
	,@lonTaxTotalReteIva
	,@lonTaxTotalReteIca
	,lonLegalMonetaryTotal
	,lonInvoiceLine
	,lonData;*/
  
   SET lonXml = CONCAT(lonRutas,lonextUBLExtensions,'\r\n</Invoice>');
   
   COMMIT;
  
   SELECT lonXml AS XmlDian;

END;