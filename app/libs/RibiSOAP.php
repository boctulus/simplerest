<?php

namespace simplerest\libs;

use simplerest\core\libs\XML;
use simplerest\core\libs\Logger;
use simplerest\core\libs\ApiClient;
use simplerest\core\libs\Validator;
use simplerest\libs\NITColombiaValidator;
use simplerest\core\exceptions\InvalidValidationException;

/*
    /c/5034bd75-3b16-4bcd-ad6a-fd1decb1cfed
*/
class RibiSOAP extends ApiClient
{
    protected $token;
    protected $base_url   = 'http://ribifacturaelectronica.com:380/EASYPODSTEST/ribiservice.asmx?wsdl';

    protected $allowed_op = [
        "consultarinventario",
        "consultarcliente",
        "crearpedido",
        "consultarproductos",
        "consultardepartamentos",
        "consultarciudades",
        "consultargrupos",
        "consultartiposdocumento",
        "consultarvendedores",
        "crearcliente",
        "consultartiposregimen", 
    ];

    function __construct($token, $base_url = null, $cache_exp_t = null) {
        $this->token    = $token;
        $this->base_url = $base_url;

        if ($cache_exp_t !== null){
            $this->setCache($cache_exp_t);
            $this->enablePostRequestCache();
        }
    }

    protected function client(){
        /*
            Seteo parámetos
        */

        $this
        ->setMethod('POST')
        ->setHeaders([
            'Content-Type' => 'text/xml;charset=UTF-8',
            'Accept'       => 'text/xml',
        ])
        ->disableSSL()
        ->followLocations();

        return $this;
    }

    
    protected function op(string $name, $data = null){
        if (!in_array($name, $this->allowed_op)){
            throw new \InvalidArgumentException("Operación no soportada");
        }

        $client    = $this->client();

        $url       = "$this->base_url/$name";

        $client->send($url, $data);

        $res       = $this->getResponse();
        $res_data  = $res['data'] ?? null;

        
        if ($res['http_code'] == 0){
            throw new \Exception("CONNECTION ERROR: ". var_export($res));
        }


        $res_data  = $data['soap:Envelope']['soap:Body'][$name . 'Response'][$name . 'Result'] ?? $res_data;

        if (isset($res_data['soap:Envelope']['soap:Body']['soap:Fault'])){
            Logger::logError($res_data['soap:Envelope']['soap:Body']['soap:Fault']);

            dd($data, 'DATA SENT'); //
            
            throw new \Exception(var_export($res_data['soap:Envelope']['soap:Body']['soap:Fault']), true);
        }

        if (!empty($res_data) && is_string($res_data) && XML::isXML($res_data)){
            $res_data = XML::toArray($res_data);            
        }

        if (isset($res_data['NewDataSet']['Table'])){
            $res_data = $res_data['NewDataSet']['Table'];
        }

        if (isset($res_data['soap:Envelope']['soap:Body']["{$name}Response"]["{$name}Result"])){
            $res_data = $res_data['soap:Envelope']['soap:Body']["{$name}Response"]["{$name}Result"];
        }

        return $res_data;
    }

    // OK 
    function consultarproductos()
    {
        $method = 'consultarproductos';
        $token  = $this->token;

        $data = "<soapenv:Envelope xmlns:soapenv=\"http://schemas.xmlsoap.org/soap/envelope/\" xmlns:loc=\"http://localhost/\">
            <soapenv:Header/>
            <soapenv:Body>
            <loc:$method>
                <!--Optional:-->
                <loc:token>$token</loc:token>
            </loc:$method>
            </soapenv:Body>
        </soapenv:Envelope>";

        return $this->op($method, $data);
    }

    /*
        OK

        idbodega es 001 y 002
    */
    function consultarinventario($idbodega, $codigos)
    {
        $method = 'consultarinventario';
        $token  = $this->token;

        $validator = new Validator();

        $params = [
            'idbodega' => $idbodega,
            'codigos'  => $codigos
        ];

        $rules = [
            'idbodega' => [ 'type' => 'string', 'required' => true ],
            'codigos' => [
                'type' => 'array',
                'required' => true,
                'min'      => 1,   // no esta validando la long min del array 
                'messages' => [
                    'type' => 'The detalle field must be an array',
                    'required' => 'codigos field is required',
                ],
            ],
        ];        

        if (!$validator->validate($params, $rules)) {
            throw new InvalidValidationException(json_encode($validator->getErrors()));
        }
       
        $str_cods = implode(',', $codigos);

        $data = "<soapenv:Envelope xmlns:soapenv=\"http://schemas.xmlsoap.org/soap/envelope/\" xmlns:loc=\"http://localhost/\">
            <soapenv:Header/>
            <soapenv:Body>
            <loc:$method>
                <!--Optional:-->
                <loc:token>$token</loc:token>
                <loc:idbodega>$idbodega</loc:idbodega>
                <loc:codigos>$str_cods</loc:codigos>
            </loc:$method>
            </soapenv:Body>
        </soapenv:Envelope>";

        return $this->op($method, $data);
    }  

    // OK
    function consultarcliente($nit)
    {
        // if (!NITColombiaValidator::isValid($nit, true)) {
        //     throw new \InvalidArgumentException("NIT no válido");
        // }

        $method = 'consultarcliente';
        $token  = $this->token;
    
        // Construir el cuerpo de la solicitud SOAP
        $data = "<soapenv:Envelope xmlns:soapenv=\"http://schemas.xmlsoap.org/soap/envelope/\" xmlns:loc=\"http://localhost/\">
           <soapenv:Header/>
           <soapenv:Body>
              <loc:$method>
                 <loc:token>$token</loc:token>
                 <loc:nit>$nit</loc:nit>
              </loc:$method>
           </soapenv:Body>
        </soapenv:Envelope>";
    
        return $this->op($method, $data);
    }    

    // OK
    function consultarciudades()
    {
        $method = 'consultarciudades';
        $token  = $this->token;

        // Construir el cuerpo de la solicitud SOAP
        $data = "<soapenv:Envelope xmlns:soapenv=\"http://schemas.xmlsoap.org/soap/envelope/\" xmlns:loc=\"http://localhost/\">
        <soapenv:Header/>
        <soapenv:Body>
            <loc:$method>
                <loc:token>$token</loc:token>
            </loc:$method>
        </soapenv:Body>
        </soapenv:Envelope>";

        return $this->op($method, $data);
    }

    // OK
    function consultardepartamentos()
    {
        $method = 'consultardepartamentos';
        $token  = $this->token;

        // Construir el cuerpo de la solicitud SOAP
        $data = "<soapenv:Envelope xmlns:soapenv=\"http://schemas.xmlsoap.org/soap/envelope/\" xmlns:loc=\"http://localhost/\">
        <soapenv:Header/>
        <soapenv:Body>
            <loc:$method>
                <loc:token>$token</loc:token>
            </loc:$method>
        </soapenv:Body>
        </soapenv:Envelope>";

        return $this->op($method, $data);
    }

    // OK
    function consultartiposdocumento()
    {
        $method = 'consultartiposdocumento';
        $token  = $this->token;

        // Construir el cuerpo de la solicitud SOAP
        $data = "<soapenv:Envelope xmlns:soapenv=\"http://schemas.xmlsoap.org/soap/envelope/\" xmlns:loc=\"http://localhost/\">
        <soapenv:Header/>
        <soapenv:Body>
            <loc:$method>
                <loc:token>$token</loc:token>
            </loc:$method>
        </soapenv:Body>
        </soapenv:Envelope>";

        return $this->op($method, $data);
    }

    // OK
    function consultarregimenes()
    {
        $method = 'consultartiposregimen';
        $token  = $this->token;

        // Construir el cuerpo de la solicitud SOAP
        $data = "<soapenv:Envelope xmlns:soapenv=\"http://schemas.xmlsoap.org/soap/envelope/\" xmlns:loc=\"http://localhost/\">
        <soapenv:Header/>
        <soapenv:Body>
            <loc:$method>
                <loc:token>$token</loc:token>
            </loc:$method>
        </soapenv:Body>
        </soapenv:Envelope>";

        return $this->op($method, $data);
    }

    /*
        OK 
    */
    public function consultargrupos() {
        $method = 'consultargrupos';
        $token  = $this->token;

        $data = "<soapenv:Envelope xmlns:soapenv=\"http://schemas.xmlsoap.org/soap/envelope/\" xmlns:loc=\"http://localhost/\">
            <soapenv:Header/>
            <soapenv:Body>
            <loc:$method>
                <loc:token>$token</loc:token>
            </loc:$method>
            </soapenv:Body>
        </soapenv:Envelope>";

        return $this->op($method, $data);
    }

    /*
        OK 

        Dado que hay un solo vendedor y este es "01" no se ocuparia este endpoint
    */
    public function consultarvendedores() {
        $method = 'consultarvendedores';
        $token  = $this->token;

        $data = "<soapenv:Envelope xmlns:soapenv=\"http://schemas.xmlsoap.org/soap/envelope/\" xmlns:loc=\"http://localhost/\">
            <soapenv:Header/>
            <soapenv:Body>
            <loc:$method>
                <loc:token>$token</loc:token>
            </loc:$method>
            </soapenv:Body>
        </soapenv:Envelope>";

        return $this->op($method, $data);
    }

    /*
        Las operaciones parar "CREAR"
    */

     /*
        Depende de:

        consultarciudades
        consultardepartamentos
        consultartiposdocumentos
        ...

        idvendedor es 01
    */
    function crearcliente(array $params, bool $validate_nit = false)
    {
        $method = 'crearcliente';
        $token  = $this->token;

        $validator = new Validator();

        $rules = [
            'nit' => ['type' => 'string', 'required' => true],
            'tipodocumento' => ['type' => 'string', 'required' => true],
            'tiporegimen' => ['type' => 'string', 'required' => true],
            'nombres' => ['type' => 'string', 'required' => true],
            'iddepartamento' => ['type' => 'string', 'required' => true],
            'idciudad' => ['type' => 'string', 'required' => true],
            'direccion' => ['type' => 'string', 'required' => true],
            'telefono' => ['type' => 'string', 'required' => true],
            'celular' => ['type' => 'string', 'required' => true],
            'correo' => ['type' => 'email', 'required' => true],
            'contacto' => ['type' => 'string', 'required' => true],
            'idvendedor' => ['type' => 'string', 'required' => true],
        ];

        if (!$validator->validate($params, $rules)) {
            throw new InvalidValidationException(json_encode($validator->getErrors()));
        }

        if ($validate_nit && $params['tipodocumento'] == 'NIT'){
            if (!NITColombiaValidator::isValid($params['nit'], true)) {
                throw new \InvalidArgumentException("NIT no válido");
            }
        }
      
        $params['token'] = $this->token;

        // Construir el cuerpo de la solicitud SOAP
        $data = "<soapenv:Envelope xmlns:soapenv=\"http://schemas.xmlsoap.org/soap/envelope/\" xmlns:loc=\"http://localhost/\">
           <soapenv:Header/>
           <soapenv:Body>
              <loc:$method>\r\n";
        
        foreach ($params as $key => $value) {
            $data .= "<loc:$key>$value</loc:$key>\r\n";
        }

        $data .= "</loc:$method>
           </soapenv:Body>
        </soapenv:Envelope>";

        return $this->op($method, $data);
    }

    // ok
    function crearpedido($params, bool $validate_nit = false)
    {
        $method = 'crearpedido';
        $token  = $this->token;

        $validator = new Validator();

        $rules = [
            'token' => ['type' => 'string', 'required' => true],
            'numero' => ['type' => 'string', 'required' => true],
            'fecha' => ['type' => 'date', 'required' => true],
            'fechaentrega' => ['type' => 'date', 'required' => true],
            'nit' => ['type' => 'string', 'required' => true],
            'observaciones' => ['type' => 'string', 'required' => true],
            'idbodega' => ['type' => 'string', 'required' => true],
            'detalle' => [
                // 'type' => 'array',
                'required' => true,
                // 'min'      => 1,
                // 'messages' => [
                //     'type' => 'The detalle field must be an array',
                //     'required' => 'The detalle field is required',
                // ],
            ],
            // 'detalle.*.idreferencia' => ['type' => 'string', 'required' => true],
            // 'detalle.*.cantidad' => ['type' => 'string', 'required' => true],
            // 'detalle.*.precio' => ['type' => 'string', 'required' => true],
            // 'detalle.*.descuento' => ['type' => 'string', 'required' => true],
        ];        

        if (!$validator->validate($params, $rules)) {
            throw new InvalidValidationException(json_encode($validator->getErrors()));
        }

        // if ($validate_nit){
        //     if (!NITColombiaValidator::isValid($params['nit'], true)) {
        //         throw new \InvalidArgumentException("NIT no válido");
        //     }
        // }

        // Construir el cuerpo de la solicitud SOAP
        $data = "<soap:Envelope xmlns:soap=\"http://www.w3.org/2003/05/soap-envelope\" xmlns:loc=\"http://localhost/\">
        <soap:Header/>
        <soap:Body>
              <loc:$method>";        
                foreach ($params as $key => $value) {
                    if ($key === 'detalle') {
                        // Construir la estructura XML para el detalle del pedido
                        $data .= "<loc:$key>";
                        foreach ($value as $detalleItem) {
                            $data .= "<loc:listadetallepedidos>";
                            foreach ($detalleItem as $detalleKey => $detalleValue) {
                                $data .= "<loc:$detalleKey>$detalleValue</loc:$detalleKey>";
                            }
                            $data .= "
                                </loc:listadetallepedidos>";
                        }
                        $data .= "</loc:$key>";
                    } else {
                        // Agregar otros parámetros directamente
                        $data .= "<loc:$key>$value</loc:$key>";
                    }
                }

        $data .= "</loc:$method>    
           </soap:Body>
        </soap:Envelope>";

        return $this->op($method, $data);
    }



}

