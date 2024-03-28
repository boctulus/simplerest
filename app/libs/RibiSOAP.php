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
        'consultarinventario',
        'consultarcliente',
        'crearcliente',
        'crearpedido'
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

    function consultarinventario($idbodega, $codigos = [])
    {
        $method = 'consultarinventario';
        $token  = $this->token;
       
        $include_codigos = (!empty($codigos) ? "<ser:codigos>".implode(',', $codigos)."</ser:codigos>" : '');

        $data     = "<soapenv:Envelope xmlns:soapenv=\"http://schemas.xmlsoap.org/soap/envelope/\" xmlns:ser=\"http://localhost/\">
        <soapenv:Header/>
            <soapenv:Body>
            <ser:consultarinventario>
                <ser:token>$token</ser:token>
                <ser:idbodega>$idbodega</ser:idbodega>
                $include_codigos
            </ser:consultarinventario>
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
        $data = "<soapenv:Envelope xmlns:soapenv=\"http://schemas.xmlsoap.org/soap/envelope/\" xmlns:ser=\"http://localhost/\">
           <soapenv:Header/>
           <soapenv:Body>
              <ser:consultarcliente>
                 <ser:token>$token</ser:token>
                 <ser:nit>$nit</ser:nit>
              </ser:consultarcliente>
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
        $data = "<soapenv:Envelope xmlns:soapenv=\"http://schemas.xmlsoap.org/soap/envelope/\" xmlns:ser=\"http://localhost/\">
           <soapenv:Header/>
           <soapenv:Body>
              <ser:crearcliente>
                 <ser:token>$token</ser:token>";
        
        foreach ($params as $key => $value) {
            $data .= "<ser:$key>$value</ser:$key>";
        }

        $data .= "</ser:crearcliente>
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
              <ser:crearpedido>
                 <ser:token>$token</ser:token>";
        
        foreach ($params as $key => $value) {
            $data .= "<ser:$key>$value</ser:$key>";
        }

        $data .= "</ser:crearpedido>
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

