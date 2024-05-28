<?php

namespace simplerest\controllers;

use simplerest\core\libs\DB;
use simplerest\core\libs\XML;
use simplerest\libs\RibiSOAP;
use simplerest\core\controllers\Controller;

class RibisoftController extends Controller
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
        // dd($cli->getHeaders(), 'HEADERS');
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
        // dd($cli->getHeaders(), 'HEADERS');
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
        // dd($cli->getHeaders(), 'HEADERS');
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
        // dd($cli->getHeaders(), 'HEADERS');
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
        // dd($cli->getHeaders(), 'HEADERS');
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
        // dd($cli->getHeaders(), 'HEADERS');
        dd($res, 'DATA');
    
        // dd($cli->dump(), 'REQ');
    }
    

    /*
        Respuesta:

    */
    function test_soap_erp_req_consultar_grupos(){
        $token    = 'b3d748f3-9238-465a-b748-9811d5b7a545';
        $url_base = 'http://ribifacturaelectronica.com:380/EASYPODSTEST/ribiservice.asmx?wsdl';
    
        $cli   = new RibiSOAP($token, $url_base);        
    
        $res   = $cli->consultargrupos();
        $error = $cli->getError();
    
        dd($cli->getStatus(), 'STATUS');
        dd($error, 'ERROR');
        // dd($cli->getHeaders(), 'HEADERS');
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
        // dd($cli->getHeaders(), 'HEADERS');
        dd($res, 'DATA');
    
        // dd($cli->dump(), 'REQ');
    }

    function test_soap_erp_req_consultarcliente(){
        $token    = 'b3d748f3-9238-465a-b748-9811d5b7a545';
        $url_base = 'http://ribifacturaelectronica.com:380/EASYPODSTEST/ribiservice.asmx?wsdl';
    
        $cli   = new RibiSOAP($token, $url_base);        
    
        $nit   = "1088562365"; // <-- debo conocerlo

        $res   = $cli->consultarcliente($nit);
        $error = $cli->getError();
    
        dd($cli->getStatus(), 'STATUS');
        dd($error, 'ERROR');
        // dd($cli->getHeaders(), 'HEADERS');
        dd($res, 'DATA');
    
        // dd($cli->dump(), 'REQ');
    }

    function test_soap_erp_req_consultarclientes(){  // LISTAR
        $token    = 'b3d748f3-9238-465a-b748-9811d5b7a545';
        $url_base = 'http://ribifacturaelectronica.com:380/EASYPODSTEST/ribiservice.asmx?wsdl';
    
        $cli   = new RibiSOAP($token, $url_base);        
    

        $res   = $cli->consultarclientes();
        $error = $cli->getError();
    
        dd($cli->getStatus(), 'STATUS');
        dd($error, 'ERROR');
        // dd($cli->getHeaders(), 'HEADERS');
        dd($res, 'DATA');
    
        // dd($cli->dump(), 'REQ');
    }

    /*
        CREATE(s)
    */

    // ok
    public function test_soap_erp_req_crear_cliente()
    {
        $token    = 'b3d748f3-9238-465a-b748-9811d5b7a545';
        $url_base = 'http://ribifacturaelectronica.com:380/EASYPODSTEST/ribiservice.asmx?wsdl';
    
        $cli   = new RibiSOAP($token, $url_base); 

        // Simular los parámetros de entrada
        $params = array(
            'token' => $token,
            'nit' => '6042688649',
            'tipodocumento' => 'NIT', 
            'tiporegimen' => 'RS', // <----------------------------- usar MAESTRO
            'nombres' => 'Tal Cual Restaurante S A S',
            'iddepartamento' => '05',  // <----------------------------- usar MAESTRO
            'idciudad' => '001', // <----------------------------- usar MAESTRO
            'direccion' => 'Calle 123',
            'telefono' => '1234567',
            'celular' => '987654321',
            'correo' => 'juan@example.com',
            'contacto' => 'Maria Gomez',
            'idvendedor' => '01'
        );

        $res = $cli->crearcliente($params);
        $error = $cli->getError();
    
        dd($cli->getStatus(), 'STATUS');
        dd($error, 'ERROR');
        // dd($cli->getHeaders(), 'HEADERS');
        dd($res, 'DATA');
    }

    function test_soap_erp_req_crear_pedido(){
        $token    = 'b3d748f3-9238-465a-b748-9811d5b7a545';
        $url_base = 'http://ribifacturaelectronica.com:380/EASYPODSTEST/ribiservice.asmx?wsdl';
    
        $cli   = new RibiSOAP($token, $url_base);        
    
        // Ejemplo de parámetros para crear un pedido
        $params = [
            'token' => $token,
            'numero' => '99999997',  // 1111111119292, cualquier otro numero de pedido
            'fecha' => '2024-04-03',
            'fechaentrega' => '2024-04-10',
            'nit' => '6042688649',
            'observaciones' => 'Test order',
            'idbodega' => '001',
            'detalle' => [
                ['idreferencia' => '01007', 'cantidad' => '2',  'precio' => '8000', 'descuento' => '0'],
                ['idreferencia' => '01001', 'cantidad' => '3', 'precio' => '15000', 'descuento' => '0']
            ]
        ];

        $res = $cli->crearpedido($params, true);
        $error = $cli->getError();
    
        dd($cli->getStatus(), 'STATUS');
        dd($error, 'ERROR');
        // dd($cli->getHeaders(), 'HEADERS');
        dd($res, 'DATA');
    }

}

