<?php

namespace simplerest\libs;

use simplerest\core\libs\ApiClient;
use simplerest\core\libs\Validator;
use simplerest\libs\NITColombiaValidator;
use simplerest\core\exceptions\InvalidValidationException;

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

        $client = $this->client();

        $url    = "$this->base_url/$name";

        $client->send($url, $data);
    }

    // OK pero faltarian algunos campos como categoria y foto
    function consultarproductos()
    {
        $method = 'consultarproductos';
        $token  = $this->token;

        $data = "<soapenv:Envelope xmlns:soapenv=\"http://schemas.xmlsoap.org/soap/envelope/\" xmlns:loc=\"http://localhost/\">
            <soapenv:Header/>
            <soapenv:Body>
            <loc:consultarproductos>
                <!--Optional:-->
                <loc:token>$token</loc:token>
            </loc:consultarproductos>
            </soapenv:Body>
        </soapenv:Envelope>";

        $this->op($method, $data);       
        
        $data  = $this->getResponse();
        $data  = $data['data'] ?? null;

        if ($data && !empty($data[4]['FAULTSTRING'][0])){
           $data = $data[4]['FAULTSTRING'][0];
        }

        return $data;
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
            <loc:consultarinventario>
                <!--Optional:-->
                <loc:token>$token</loc:token>
                <loc:idbodega>$idbodega</loc:idbodega>
                <loc:codigos>$str_cods</loc:codigos>
            </loc:consultarinventario>
            </soapenv:Body>
        </soapenv:Envelope>";

        $this->op($method, $data);       
        
        $data  = $this->getResponse();
        $data  = $data['data'] ?? null;

        if ($data && !empty($data[4]['FAULTSTRING'][0])){
           $data = $data[4]['FAULTSTRING'][0];
        }

        return $data;
    }

    /*
        Depende de:

        consultarciudades
        consultardepartamentos
        consultartiposdocumentos
        ...

        idvendedor es 01
    */
    function crearcliente(array $params)
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

        if (!NITColombiaValidator::isValid($params['nit'], true)) {
            throw new \InvalidArgumentException("NIT no válido");
        }

        // Construir el cuerpo de la solicitud SOAP
        $data = "<soapenv:Envelope xmlns:soapenv=\"http://schemas.xmlsoap.org/soap/envelope/\" xmlns:loc=\"http://localhost/\">
           <soapenv:Header/>
           <soapenv:Body>
              <loc:crearcliente>
                 <loc:token>$token</loc:token>";
        
        foreach ($params as $key => $value) {
            $data .= "<loc:$key>$value</loc:$key>";
        }

        $data .= "</loc:crearcliente>
           </soapenv:Body>
        </soapenv:Envelope>";

        $this->op($method, $data);

        $data  = $this->getResponse();
        $data  = $data['data'] ?? null;

        if ($data && !empty($data[4]['FAULTSTRING'][0])){
           $data = $data[4]['FAULTSTRING'][0];
        }

        return $data;
    }

    function consultarcliente($nit)
    {
        if (!NITColombiaValidator::isValid($nit, true)) {
            throw new \InvalidArgumentException("NIT no válido");
        }

        $method = 'consultarcliente';
        $token  = $this->token;
    
        // Construir el cuerpo de la solicitud SOAP
        $data = "<soapenv:Envelope xmlns:soapenv=\"http://schemas.xmlsoap.org/soap/envelope/\" xmlns:loc=\"http://localhost/\">
           <soapenv:Header/>
           <soapenv:Body>
              <loc:consultarcliente>
                 <loc:token>$token</loc:token>
                 <loc:nit>$nit</loc:nit>
              </loc:consultarcliente>
           </soapenv:Body>
        </soapenv:Envelope>";
    
        $this->op($method, $data);
    
        $data  = $this->getResponse();
        $data  = $data['data'] ?? null;
    
        if ($data && !empty($data[4]['FAULTSTRING'][0])){
           $data = $data[4]['FAULTSTRING'][0];
        }
    
        return $data;
    }    


    function crearpedido($params)
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
                'type' => 'array',
                'required' => true,
                'min'      => 1,
                'messages' => [
                    'type' => 'The detalle field must be an array',
                    'required' => 'The detalle field is required',
                ],
            ],
            // 'detalle.*.idreferencia' => ['type' => 'string', 'required' => true],
            // 'detalle.*.cantidad' => ['type' => 'string', 'required' => true],
            // 'detalle.*.precio' => ['type' => 'string', 'required' => true],
            // 'detalle.*.descuento' => ['type' => 'string', 'required' => true],
        ];        

        if (!$validator->validate($params, $rules)) {
            throw new InvalidValidationException(json_encode($validator->getErrors()));
        }

        if (!NITColombiaValidator::isValid($params['nit'], true)) {
            throw new \InvalidArgumentException("NIT no válido");
        }

        // Construir el cuerpo de la solicitud SOAP
        $data = "<soapenv:Envelope xmlns:soapenv=\"http://schemas.xmlsoap.org/soap/envelope/\" xmlns:ser=\"http://localhost/\">
           <soapenv:Header/>
           <soapenv:Body>
              <loc:crearpedido>
                 <loc:token>$token</loc:token>";
        
        foreach ($params as $key => $value) {
            $data .= "<loc:$key>$value</loc:$key>";
        }

        $data .= "</loc:crearpedido>
           </soapenv:Body>
        </soapenv:Envelope>";

        $this->op($method, $data);

        $data  = $this->getResponse();
        $data  = $data['data'] ?? null;

        if ($data && !empty($data[4]['FAULTSTRING'][0])){
           $data = $data[4]['FAULTSTRING'][0];
        }

        return $data;
    }

    ///////////////////////////////////////////////////////////////////
    ////////////////////////// NUEVOS /////////////////////////////////
    ///////////////////////////////////////////////////////////////////

    function consultarciudades()
    {
        $method = 'consultarciudades';
        $token  = $this->token;

        // Construir el cuerpo de la solicitud SOAP
        $data = "<soapenv:Envelope xmlns:soapenv=\"http://schemas.xmlsoap.org/soap/envelope/\" xmlns:loc=\"http://localhost/\">
        <soapenv:Header/>
        <soapenv:Body>
            <loc:consultarciudades>
                <loc:token>$token</loc:token>
            </loc:consultarciudades>
        </soapenv:Body>
        </soapenv:Envelope>";

        $this->op($method, $data);

        $data  = $this->getResponse();
        $data  = $data['data'] ?? null;

        if ($data && !empty($data[4]['FAULTSTRING'][0])){
        $data = $data[4]['FAULTSTRING'][0];
        }

        return $data;
    }

    function consultardepartamentos()
    {
        $method = 'consultardepartamentos';
        $token  = $this->token;

        // Construir el cuerpo de la solicitud SOAP
        $data = "<soapenv:Envelope xmlns:soapenv=\"http://schemas.xmlsoap.org/soap/envelope/\" xmlns:loc=\"http://localhost/\">
        <soapenv:Header/>
        <soapenv:Body>
            <loc:consultardepartamentos>
                <loc:token>$token</loc:token>
            </loc:consultardepartamentos>
        </soapenv:Body>
        </soapenv:Envelope>";

        $this->op($method, $data);

        $data  = $this->getResponse();
        $data  = $data['data'] ?? null;

        if ($data && !empty($data[4]['FAULTSTRING'][0])){
        $data = $data[4]['FAULTSTRING'][0];
        }

        return $data;
    }

    function consultartiposdocumento()
    {
        $method = 'consultartiposdocumento';
        $token  = $this->token;

        // Construir el cuerpo de la solicitud SOAP
        $data = "<soapenv:Envelope xmlns:soapenv=\"http://schemas.xmlsoap.org/soap/envelope/\" xmlns:loc=\"http://localhost/\">
        <soapenv:Header/>
        <soapenv:Body>
            <loc:consultartiposdocumento>
                <loc:token>$token</loc:token>
            </loc:consultartiposdocumento>
        </soapenv:Body>
        </soapenv:Envelope>";

        $this->op($method, $data);

        $data  = $this->getResponse();
        $data  = $data['data'] ?? null;

        if ($data && !empty($data[4]['FAULTSTRING'][0])){
        $data = $data[4]['FAULTSTRING'][0];
        }

        return $data;
    }

    function consultarregimenes()
    {
        $method = 'consultarregimenes';
        $token  = $this->token;

        // Construir el cuerpo de la solicitud SOAP
        $data = "<soapenv:Envelope xmlns:soapenv=\"http://schemas.xmlsoap.org/soap/envelope/\" xmlns:loc=\"http://localhost/\">
        <soapenv:Header/>
        <soapenv:Body>
            <loc:consultarregimenes>
                <loc:token>$token</loc:token>
            </loc:consultarregimenes>
        </soapenv:Body>
        </soapenv:Envelope>";

        $this->op($method, $data);

        $data  = $this->getResponse();
        $data  = $data['data'] ?? null;

        if ($data && !empty($data[4]['FAULTSTRING'][0])){
        $data = $data[4]['FAULTSTRING'][0];
        }

        return $data;
    }



}

