<?php

namespace simplerest\libs;

use simplerest\core\libs\ApiClient;

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

    function op(string $name, $data = null){
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
    

    function crearcliente($params)
    {
        $method = 'crearcliente';
        $token  = $this->token;

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

