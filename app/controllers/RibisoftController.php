<?php

namespace simplerest\controllers;

use simplerest\core\libs\DB;
use simplerest\core\libs\XML;
use simplerest\libs\RibiSOAP;
use simplerest\controllers\MyController;

class RibisoftController extends MyController
{
    function test_decode_xml(){
        $xml = '<?xml version="1.0" encoding="utf-8"?><soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema"><soap:Body><soap:Fault><faultcode>soap:Server</faultcode><faultstring>Server was unable to process request. ---&gt; Error: La bodega no existe</faultstring><detail /></soap:Fault></soap:Body></soap:Envelope>';

        dd(XML::toArray($xml)[4]['FAULTSTRING'][0], 'XML -> ARR');
    }
    

    // OK
    function test_soap_erp_req_consultar_inv(){
        $idbodega = '001';  // son  '001' y  '002'
        $codigos  = [ '01003','01004','01005' ];

        $token    = 'b3d748f3-9238-465a-b748-9811d5b7a545';
        $url_base = 'http://ribifacturaelectronica.com:380/EASYPODSTEST/ribiservice.asmx?wsdl';


        $cli   = new RibiSOAP($token, $url_base);        
        
        $res   = $cli->consultarinventario($idbodega, $codigos);
        $error = $cli->getError();

        dd($cli->getStatus(), 'STATUS');
        dd($error, 'ERROR');
        dd($cli->getHeaders(), 'HEADERS');
        dd($res, 'DATA');

        // dd($cli->dump(), 'REQ');
    }

    // OK
    function test_soap_erp_req_consultar_productos(){
        $token    = 'b3d748f3-9238-465a-b748-9811d5b7a545';
        $url_base = 'http://ribifacturaelectronica.com:380/EASYPODSTEST/ribiservice.asmx?wsdl';


        $cli   = new RibiSOAP($token, $url_base);        
        
        $res   = $cli->consultarproductos();
        $error = $cli->getError();

        dd($cli->getStatus(), 'STATUS');
        dd($error, 'ERROR');
        dd($cli->getHeaders(), 'HEADERS');
        dd($res, 'DATA');

        // dd($cli->dump(), 'REQ');
    }

    //////////// NUEVOS /////////////////////////////////////////////////////////////////////////

    // OK
    function test_soap_erp_req_consultar_ciudades(){
        $token    = 'b3d748f3-9238-465a-b748-9811d5b7a545';
        $url_base = 'http://ribifacturaelectronica.com:380/EASYPODSTEST/ribiservice.asmx?wsdl';
    
        $cli   = new RibiSOAP($token, $url_base);        
    
        $res   = $cli->consultarciudades();
        $error = $cli->getError();
    
        dd($cli->getStatus(), 'STATUS');
        dd($error, 'ERROR');
        dd($cli->getHeaders(), 'HEADERS');
        dd($res, 'DATA');
    
        // dd($cli->dump(), 'REQ');
    }
    
    // OK
    function test_soap_erp_req_consultar_departamentos(){
        $token    = 'b3d748f3-9238-465a-b748-9811d5b7a545';
        $url_base = 'http://ribifacturaelectronica.com:380/EASYPODSTEST/ribiservice.asmx?wsdl';
    
        $cli   = new RibiSOAP($token, $url_base);        
    
        $res   = $cli->consultardepartamentos();
        $error = $cli->error();
    
        dd($cli->getStatus(), 'STATUS');
        dd($error, 'ERROR');
        dd($cli->getHeaders(), 'HEADERS');
        dd($res, 'DATA');
    
        // dd($cli->dump(), 'REQ');
    }
    
    // OK
    function test_soap_erp_req_consultar_tipos_documento(){
        $token    = 'b3d748f3-9238-465a-b748-9811d5b7a545';
        $url_base = 'http://ribifacturaelectronica.com:380/EASYPODSTEST/ribiservice.asmx?wsdl';
    
        $cli   = new RibiSOAP($token, $url_base);        
    
        $res   = $cli->consultartiposdocumento();
        $error = $cli->error();
    
        dd($cli->getStatus(), 'STATUS');
        dd($error, 'ERROR');
        dd($cli->getHeaders(), 'HEADERS');
        dd($res, 'DATA');
    
        // dd($cli->dump(), 'REQ');
    }
    
    // OK
    function test_soap_erp_req_consultar_regimenes(){
        $token    = 'b3d748f3-9238-465a-b748-9811d5b7a545';
        $url_base = 'http://ribifacturaelectronica.com:380/EASYPODSTEST/ribiservice.asmx?wsdl';
    
        $cli   = new RibiSOAP($token, $url_base);        
    
        $res   = $cli->consultarregimenes();
        $error = $cli->getError();
    
        dd($cli->getStatus(), 'STATUS');
        dd($error, 'ERROR');
        dd($cli->getHeaders(), 'HEADERS');
        dd($res, 'DATA');
    
        // dd($cli->dump(), 'REQ');
    }
    

    /*
        Respuesta:

        --| DATA
        <NewDataSet>
        <Table>
            <idgrupo>01</idgrupo>
            <grupo>BEBIDAS CALIENTES</grupo>
        </Table>
        <Table>
            <idgrupo>02</idgrupo>
            <grupo>EXPRESSOS</grupo>
        </Table>
        ...
        ...
        </NewDataSet>
    */
    function test_soap_erp_req_consultar_grupos(){
        $token    = 'b3d748f3-9238-465a-b748-9811d5b7a545';
        $url_base = 'http://ribifacturaelectronica.com:380/EASYPODSTEST/ribiservice.asmx?wsdl';
    
        $cli   = new RibiSOAP($token, $url_base);        
    
        $res   = $cli->consultargrupos();
        $error = $cli->getError();
    
        dd($cli->getStatus(), 'STATUS');
        dd($error, 'ERROR');
        dd($cli->getHeaders(), 'HEADERS');
        dd($res, 'DATA');
    
        // dd($cli->dump(), 'REQ');
    }

    function test_soap_erp_req_consultar_vendedores(){
        $token    = 'b3d748f3-9238-465a-b748-9811d5b7a545';
        $url_base = 'http://ribifacturaelectronica.com:380/EASYPODSTEST/ribiservice.asmx?wsdl';
    
        $cli   = new RibiSOAP($token, $url_base);        
    
        $res   = $cli->consultarvendedores();
        $error = $cli->getError();
    
        dd($cli->getStatus(), 'STATUS');
        dd($error, 'ERROR');
        dd($cli->getHeaders(), 'HEADERS');
        dd($res, 'DATA');
    
        // dd($cli->dump(), 'REQ');
    }

    // SIN ENSAYAR !!!!!!!!!!

    function test_soap_erp_req_crear_pedido(){
        $token    = 'b3d748f3-9238-465a-b748-9811d5b7a545';
        $url_base = 'http://ribifacturaelectronica.com:380/EASYPODSTEST/ribiservice.asmx?wsdl';
    
        $cli   = new RibiSOAP($token, $url_base);        
    
        // Ejemplo de parámetros para crear un pedido
        $params = [
            'token' => 'your_token_here',
            'numero' => '123456',
            'fecha' => '2024-04-03',
            'fechaentrega' => '2024-04-10',
            'nit' => '123456789',
            'observaciones' => 'Test order',
            'idbodega' => '001',
            'detalle' => [
                ['idreferencia' => '1', 'cantidad' => '2', 'precio' => '10', 'descuento' => '0'],
                ['idreferencia' => '2', 'cantidad' => '3', 'precio' => '15', 'descuento' => '0']
            ]
        ];
    
        $res = $cli->crearpedido($params);
        $error = $cli->getError();
    
        dd($cli->getStatus(), 'STATUS');
        dd($error, 'ERROR');
        dd($cli->getHeaders(), 'HEADERS');
        dd($res, 'DATA');
    }

}

