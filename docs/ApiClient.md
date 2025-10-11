# La clase ApiClient

ApiClient es una abstraccion sobre las funciones curl e incluye manejo distintos tipos de autenticación.


Constructor de la clase ApiClient

Es posible especificar la url del endpoint de una vez al instanciar pasándola en el constructor y también cambiar la url mediante el método setUrl() o simplemente url()


Ej: (base)

    $url    = "https://www.amazon.com/Logitech-Widescreen-Calling-Recording-Desktop/dp/B006JH8T3S/ref=sr_1_3?s=pc&ie=UTF8&qid=1510936764&sr=1-3&keywords=logitech&tag=lnrscm-20";

    $client = new ApiClient($url);

    $res = $client
    ->get()
    ->getResponse(false);

    if ($res === null){
        return;
    }

    if ($res['http_code'] != 200){
        return;
    }

Para utilizar POST es con post() en vez de get() y soporta tambien delete(), patch() y put()

también se pueden incluir headers.

Ej:

	/*
		Instancio cliente
	*/

	$client = new ApiClient();

	/*
		Hago request
	*/

	$client
	->setBody($body)
	->setHeaders([
		"Content-type"  => "Application/json",
		"authToken" => "$token"
	])
	->request($ruta, 'POST');        

	/*
		Recojo respuesta
	*/

	d($client->getStatus(), 'STATUS');
	d($client->getErrors(), 'ERRORS');
	d($client->getResponse(), 'RES');    


Nota:

En vez de usar request($url, $verbo) se puede simplificar usando get(), post(), put(), path() y delete()

Ej:

	$client = new ApiClient();

	$client
	->setBody($body)
	->setHeaders([
		"Content-type"  => "Application/json",
		"authToken" => "$token"
	])
	->post($ruta);   


# El metodo data()

El metodo getResponse() retorna no solo el body de la respuesta sino tambien el (http) "status_code" y "error" por lo cual puede llevar a que se visualice un anidamiento de "data" con "data" (si "data" es definido en la respuesta enviada). 

Ej:

--| STATUS
200

--| ERROR


--| RES
Array
(
    [data] => Array
        (
            [data] => Array
                (
                    [norma_price] => 98400
                    [sale_price] =>
                )

            [status_code] => 200
            [error] => Array
                (
                )

        )

    [http_code] => 200
    [error] =>
)

Es por esto que es preferible usar data() en vez de getResponse()

Ej:

	$client = new ApiClient();

	$client
		->setHeaders([
			"Content-type"  => "Application/json"
		])
		->disableSSL()
		->get($url);

	dd($client->status(), 'STATUS');

	dd($client->error(), 'ERROR');

	dd(
		$client
		->decode()
		->data(),
	'DATA');    

En vez de hacer:

	dd(
		$client->getResponse(), 'RES'
	);    

En este caso obtendremos lo que esperamos:

--| STATUS
200

--| ERROR


--| RES
Array
(
    [data] => Array
        (
            [norma_price] => 98400
            [sale_price] =>
        )

    [status_code] => 200
    [error] => Array
        (
        )

Y ya no hay anidamiento de "data".

En el siguiente ejemplo creamos un metodo getClient() para evitarnos tener que repetir las parametrizaciones que son comunes a distintos requests de la misma API.

Ej:

	protected function getClient()
    {
        $cli = new ApiClient();
    
        $cli
        ->withoutStrictSSL()
        ->followLocations()
        ->setHeaders([
            "Content-type"  => "application/json",
            "Amelia" => $this->api_key
        ])
        ->decode();

        return $cli;
    }

    function get_coupons()
    {
        $url  = "{$this->base_url}/coupons";
    
        $cli = $this->getClient();
        $cli->request($url, 'GET');        

        /*
            Recojo respuesta
        */

        dd($cli->getStatus(), 'STATUS');
        dd($cli->getError(), 'ERRORS');
        // dd($cli->getResponse(), 'RES');  

        $data = $cli->data();

        dd($data);
    }


# Chequeo de certificado SSL

Consideremos el siguiente ejemplo con Amazon:

    $url    = "https://www.amazon.com/Logitech-Widescreen-Calling-Recording-Desktop/dp/B006JH8T3S/ref=sr_1_3?s=pc&ie=UTF8&qid=1510936764&sr=1-3&keywords=logitech&tag=lnrscm-20";

    $client = new ApiClient($url);

    $res = $client
    ->get()
    ->getResponse(false);


    if ($res === null){
        print_r("Respuesta vacia");
        return;
    }

    if (!empty($res['error'])){
        print_r("Respuesta con error: {$res['error']} y codigo http {$res['http_code']}");
        return;
    }

    if ($res['http_code'] != 200){
        print_r("Respuesta con codigo http {$res['http_code']}");
        return;
    }

    var_dump($res);


La respuesta sera:

    Respuesta con error: SSL certificate problem: unable to get local issuer certificate y codigo http 0

La solución es incluir el archivo del certificado SSL 
        
    $cert = 'ruta al archivo de certificado';

    $res = $client
    ->setSSLCrt($cert) <---- *
    ->get()
    ->getResponse(false);

pero también se puede deshabilitar:

    $res = $client
    ->disableSSL()  <---- *
    ->get()
    ->getResponse(false);


# Autorización

La Básica es mediante el método setBasicAuth($user, $pass)

Ej:

	$url  = 'https://xxxxxxx.com/courses?order=desc&orderby=ID&paged=1';

	$user = 'key_f7a2062021f6a2218f96818631bf9a5c';
	$pass = 'secret_7b11f511f92355956e77aeaa2d9bba520b8e86025daae0ef6e94c33e885ccb7c';

	$client = new ApiClient();
	$client

	/*
		Seteo parámetos
	*/
	->disableSSL()
	->setBasicAuth($user, $pass)
	->request($url, 'GET');        

	dd($client->getStatus(), 'STATUS');
	dd($client->getError(), 'ERROR');
	dd($client->getHeaders(), 'HEADERS');
	dd($client->getResponse(true), 'RES'); 


Hay casos donde se necesita enviar el body sin encodear como JSON. Para esto se puede enviar false en el segundo parametro de setBody()

Ej:

	$client = new ApiClient();

	$postfields = array();
	$postfields['_username'] = 'admin';
	$postfields['_password'] = 'admin456';

	$client
	//->setRetries(3)
	->setHeaders([
		'Content-Type' => 'multipart/form-data'
	])
	->setBody($postfields, false)
	->disableSSL()
	->request('https://somesite.com/login_check', 'POST');        

	d($client->getStatus(), 'STATUS');
	d($client->getError(), 'ERROR');
	d($client->getResponse(true), 'RES'); 


Tambien puede usar el metodo authorization($raw_auth_string)


# Seguir redirecciones

Por defecto no se siguen redirecciones pero se pueden habilitar llamando al metodo followLocations()

Ej:

	$url    = 'https://www.awin1.com/cread.php?awinmid=20598&awinaffid=856219&platform=dl&ued=https%3A%2F%2Fwww.leroymerlin.es%2Ffp%2F81926166%2Fespejo-rectangular-pierre-roble-roble-152-x-52-cm';

	$client = new ApiClient($url);

	$client
	->disableSSL()
	->followLocations()
	->get();

	$data = $client->data();


# When

Al igual que en el QueryBuilder disponemos de when()

Ej:

$client->disableSSL()
	->followLocations()
	->when($overwrite_cache, function($e){
		$e->clearCache();
	})
	->cache()
	->get();

$data = $client->data();

Lo que hemos hecho fue borrar la cache antes de volver a generarla. Esto es distinto a no usar cache porque de todas formas se esta guardando en la cache y podra ser recuperado.


# Uso de cache

Para el uso de cache se utiliza el método setCache() o su alias cache()

Ej:

$content = (new ApiClient($url))
->cache(3600)
->disableSSL()
->get()
->getBody();

* Para el caso anterior se puede usar un helper:

$content = Url::getUrlContent($url, 3600);

Mas ejemplos,...

Ej:

	$client = new ApiClient();

	$user = 'intergrade';
	$pass = '9660ed881416fad88c5f48eddd7334c6';

	$client
	
	/*
		<--- en vez de guardar en disco..... usar Transientes con diversos drivers incluidos Memcached o REDIS
	*/

	->setCache(600)
	->setHeaders([
		'Authorization: Basic '. base64_encode("$user:$pass")
	])
	->request('http://200.6.78.34/stock/v1/catalog/YX0-947', 'GET');        

	d($client->getStatus(), 'STATUS');
	d($client->getError(), 'ERROR');
	d($client->getResponse(true), 'RES'); 


A setCache() se le pasa la cantidad de tiempo expresado en segundos que queremos utilice la copia en vez de repetir el request.

# Borrado de cache

Con el metodo clearCache()

Ej:

	$res = ApiClient::instance($url)
	->clearCache();


# Cache de solo lectura

Es posible desactivar la escritura de la cache para estar seguros de que no se va a sobre-escribir usando cache() en conjunción con readOnly()

Ej:

	$client = ApiClient::instance($url)
	->disableSSL()
	->readOnly()  // <------------- *
	->cache()
	->get();

	$data = $client->data();


# Ignorar ciertos status codes y guardar en cache

Ej:

	$cli = (new ApiClient($url))
	->ignoreStatusCodes([404])    # Se guarda en cache aunque devuelva 404
	->cache($exp_time);


# Descarga de archivos

Se utiliza el metodo download($filename)

Para el siguiente ejemplo se va a descargar un .zip que es de tipo binario y por tanto se usa conjuntamente con setBinary() 

Ej:

	$url = 'https://www.learningcontainer.com/wp-content/uploads/2020/05/sample.tar';
	$cli = new ApiClient($url); 

	$cli
	->setBinary()
	->withoutStrictSSL();

	$bytes = $cli->download(ETC_PATH . 'file.zip'); // <-- aca la ruta donde se guardara el archivo

	// empty => OK
	if (!empty($cli->error())){
		dd("HTTP Error. Detail: " . $cli->error());
	}
	
	// 200 OK
	if ($cli->status() != 200){
		dd("HTTP status code" . $cli->status());
		exit;
	}

	// true
	dd($cli->data(), 'DATA');

	dd($bytes, 'BYTES escritos');


# Haciendo las cosas fáciles

En caso de tener varios endpoints para la misma url base, es buena idea crear un artefacto como el siguiente.

Ej:

	/*
		Se incluye toda la lógica que se quiera
	*/
	static function getClient($endpoint){
        global $config;

        $ruta   = $config['url_base_endpoints'] . $endpoint;
        $token  = $config['token'];

        $client = ApiClient::instance($ruta);

        $client
        ->setHeaders(
            [
                "Content-type"  => "Application/json",
                "authToken" => "$token"
            ]
        )
        ->setRetries(3);

        if ($config['dev_mode']){
            $client->disableSSL();
        }        
        
        return $client;
    }

Esto habilita luego tener funciones muy simples como las siguientes.

Ej:

	static function registrar($data, $endpoint)
    {
        $response = static::getClient($endpoint)
        ->setBody($data)
        ->post()
        ->data();

        return $response;
    }

    static function get_pdf(string $serie, int $correlativo){
        $ruc_prop = static::getRuCPropietario();
        $endpoint = "/business/invoice-{$ruc_prop}-$serie-$correlativo/pdf";

        static::getClient($endpoint)
        ->get()->data();

        return $response;
    }

Otra posibilidad es extender la clase ApiClient

Ej:

	use boctulus\SW\core\libs\ApiClient;

	class WPAmelia extends ApiClient
	{
		protected $base_url;
		protected $api_key;

		function __construct($base_url = null, $api_key = null){
			$this->base_url = $base_url ?? env('AMELIA_API_URL');
			$this->api_key  = $api_key  ?? env('AMELIA_API_KEY');

			$this
			->withoutStrictSSL()
			->followLocations()
			->setHeaders([
				"Content-type"  => "application/json",
				"Amelia" => $this->api_key
			])
			->decode();
		}

		/*
			Endpoints
		*/
		function setCouponEndpoint(){
			$this->setUrl("{$this->base_url}/coupons");
			return $this;
		}
	}

y para hacer uso de nuestra libreria:

	$cli = new WPAmelia();
    $cli->setCouponEndpoint();
    
    $data = [
        'code'     => 'MYCOUPON',  
        'discount' => 100,                   
    ];

    $cli
    ->setBody($data)
    ->post();       
    
    /*
        Recojo respuesta
    */

    $cli->getResponse();

    dd($cli->getStatus(), 'STATUS');
    dd($cli->getError(), 'ERRORS');
    dd($cli->getResponse(), 'RES');  

    $data = $cli->data();

    dd($data);


Metodos para acceder a la respuesta:

	status()	el código http de la respuesta
	error()		mensaje de error si lo hubiera	
	data()		data obtenida
	headers()   headers de la respuesta

Ej:

	$client = ApiClient::instance()
	->disableSSL()
	->redirect()
	->get($url);

	if ($client->getStatus() != 200){
		throw new \Exception($client->error());
	}

	dd(
		$client->data()         
	);


# Proxy para ApiClient

Un muy buen recurso cuando se quiere hacer una VPN casera es utilizar el siguiente recurso:

	https://github.com/zounar/php-proxy/blob/master/Proxy.php

Se instala el scripy Proxy.php en el servidor donde queremos que se haga la peticion y el codigo para ApiClient quedaria como en el ej.

Ej:

	$url       = 'https://amzn.to/2M0SCXb';
	$proxy_url = 'http://2.56.221.125/php-proxy/Proxy.php';

	$client = ApiClient::instance($proxy_url)
	->setHeaders([
		'Proxy-Auth: Bj5pnZEX6DkcG6Nz6AjDUT1bvcGRVhRaXDuKDX9CjsEs2',
		'Proxy-Target-URL: '.$url
	]);

	$client
	->disableSSL()
	->redirect()
	->get();

	if ($client->getStatus() != 200){
		throw new \Exception($client->error());
	}

	dd(
		$client->data()         
	);  


# Debugging de requests con ApiClient

Puede que no estemos seguros de que se ha enviado en el request y/o que querramos poder reproducirlo en otro momento. Para esto se proveen los metodos dd() y exec()

Ej:

	$user_api_key = 'some-key';
	$url          = 'some-endpoint';

	$client  = ApiClient::instance()
	->setBody([
		// some data
	])
	->setHeaders([
		'X-API-KEY' => $user_api_key
	])
	->disableSSL()

	// some http verb
	->post($url);  

	/*	
		Exporto el request incluyendo url, verbo, headers,......
	*/

	$export = $client->dd();

	/*
		Almaceno el array 	
	*/

	Files::varExport($export, 'api_debug.php');


# Fallback de extension Curl (no completamente funcional)

Se dispone de una implementacion basica de ApiClient llamada ApiClientFallback
para cuando la extension de curl para PHP no esta disponible.

Ej:

	$client = new ApiClientFallback();

	$client
	->setHeaders([
		"Content-type" => "application/json"
	])
	->get("https://jsonplaceholder.typicode.com/posts/1");

	$res = $client->getResponse();

	dd($client->status(), 'STATUS');
	dd($client->error(), 'ERROR');
	dd($client
	->decode()
	->data(), 'DATA');

El fallback tiene una variante para correr sobre PowerShell que se activa con useInvokeWebRequest()

Ej:

	$client = new ApiClientFallback();
	$client->useInvokeWebRequest(); // Activar modo PowerShell

	// Configurar la URL
	$client->setUrl('https://api.haulmer.dev/v2.0/partners/signature/generate');
	
	// Headers
	$headers = [
		'apikey' => 'cebc90896c0445599e6d2269b9f89c8f',
		'Content-Type' => 'application/json'
	];

	// Body (como array para que se encodee automáticamente)
	$body = [
		"period" => 1,
		"email" => "email@correo.com"
	];

	// Configurar el cuerpo y habilitar encoding
	$client->setBody($body, true); // Segundo parámetro = encode_body = true

	// Hacer la petición POST (sin pasar $body nuevamente)
	$response = $client->post(null, $body, $headers); // <- ¡Body viene de setBody()!

	// Debug
	dd("STATUS: " . $response->status());
	dd("ERROR: " . ($response->error() ?? ''));
	
	dd($response->data(), 'DATA');
    

# Paginacion

Una forma sencilla de paginar es agregar los parametros de paginacion con queryParam() y queryParams($array)

Ej:

	$cli  = ApiClient::instance('http://simplerest.lan/api/v1/products?v=1');

	$cli
	->queryParam('size', 3)
	->queryParam('page', 2)
	
	->setMethod('GET') 

	->send();

	dd($cli->data());


Puede ser buena idea construir una funcion de paginado personalizada para separar la logica del paginado
de la logica de negocio.

Ej:

    static function paginate(callable $callback, ...$args){
        $page  = 1;
        $pages = PHP_INT_MAX;

        while ($page <= $pages){
            dd("Trayendo pagina $page de ". ($pages == PHP_INT_MAX ? '??' : $pages));

            $res  = MaxImport::getProducts([
                'page' => $page
            ], ...$args);

            /*
                Paginacion

                Ej:

                [page] => 1
                [length] => 7282
                [pageSize] => 20
                [pages] => 365
            */
            $rows     = $res['data']['rows'] ?? null;
            $length   = $res['data']['length'] ?? null;
            $pageSize = $res['data']['pageSize'] ?? null;
            $page     = $res['data']['page'] ?? null;
            $pages    = $res['data']['pages'] ?? null;

            // $rows     = $res['data']['rows'];

            // Llamo al callback
            $callback($res);

            $page++;
        }
    }

Forma de uso

	Sync::paginate(function($res){
		$rows     = $res['data']['rows'] ?? null;
		$page     = $res['data']['page'] ?? null;
		$pages    = $res['data']['pages'] ?? null;

		// Hago algo con la respuesta (paginada) del endpoint que estoy consumiendo

		$filtered = Arrays::getColumns($rows, [
			'id',
			'code',
			'stockByWarehouse',
		]);

		dd([
			$filtered
		], "DATA [page = $page/$pages]");
	}, true);


# Mocking 

$ php com make interface OpenFactura --from=D:\laragon\www\Boctulus\Simplerest\app\libs\OpenFacturaSDK.php

Eso nos genera una interfaz con todos los metodos publicos

Luego creamos una clase, por ejemplo OpenFacturaSDKMock y extendemos IOpenFactura (la interfaz) con lo cual sabemos que en principio no nos olvidaremos de mockear ningun metodo.


# Mocking con ApiClient

Es posible "mockear" solicitudes http hechas a ApiClient, con lo cual si esta tiene costo por ejemplo y no hay un sandbox,
podriamos probarla las veces que querramos.

Consideraciones:

	- Debe usarse *antes* de llamar a request(), get(), post(), etc

    - $mock puede ser la ruta a un archivo .json, .php o un array

Ej:

	$mock = ETC_PATH . 'res_ricerca_nazionale.php';
	//$mock = ETC_PATH . 'res_jsonplaceholder.json';

	$cli  = new ApiClient();

	$res  = $cli
	->when(!empty($mock), function($it) use ($mock){
		$it->mock($mock);
	})
	->request('http://jsonplaceholder.typicode.com/posts/3', 'GET')
	->getResponse();

	/*
		Array
			(
				[data] => {
				"userId": 1,
				"id": 3,
				"title": "ea molestias quasi exercitationem repellat qui ipsa sit aut",
				"body": "et iusto sed quo iure\nvoluptatem occaecati omnis eligendi aut ad\nvoluptatem doloribus vel accusantium quis pariatur\nmolestiae porro eius odio et labore et velit aut"
				}
					[http_code] => 200
					[error] =>
				)
			)
	*

	dd($res);

	/*
		{
			"userId": 1,
			"id": 3,
			"title": "ea molestias quasi exercitationem repellat qui ipsa sit aut",
			"body": "et iusto sed quo iure\nvoluptatem occaecati omnis eligendi aut ad\nvoluptatem doloribus vel accusantium quis pariatur\nmolestiae porro eius odio et labore et velit aut"
		}
	*/
	
	dd($cli->data());


Notas: 

- Con archivos .php

Ej:

	<?php

	return array (
		'data' => 
		array (
		'endpoint' => 'ricerca_nazionale_pg',
		'stato' => 'in_erogazione',
		'callback' => 
		array (
			'url' => 'https://ticiwe.com/callbacks?r=realstate&sub=ricerca_nazionale',
			'field' => 'data',
			'method' => 'POST',
			'data' => 
			array (
			),
		),
		'parametri' => 
		array (
			'cf_piva' => '12485671007',
			'tipo_catasto' => 'TF',
			'provincia' => 'NAZIONALE-IT',
		),
		'risultato' => NULL,
		'esito' => NULL,
		'timestamp' => 1683988870,
		'owner' => 'fabio56istrefi@gmail.com',
		'id' => '645fa18682673817d87710e8',
		),
		'success' => true,
		'message' => '',
		'error' => NULL,
	);


- Con mocking "http_code" y "error" quedaran "indefinidas".


# Sintaxis alternativa para ApiClient

Se pueden usar alternativamente los metodos setMethod(), send() y getBody()

	setMethod() + send()	reemplazo para get(), post(), ...
	getBody() 				reemplazo para data()

Ej:

	$client->setUrl('https://api.inmofactory.com/api/property');
    $client->setMethod(HTTP_METH_POST);

    $client->setHeaders(array(
      'Cache-Control' => 'no-cache',
      'Inmofactory-Api-Key' => '37d14b4e9a7a4f0fa4f959dbef3e23f69bb29abc6bdc4787809cfxxxxxxxxxxxxxx',
      'Content-Type' => 'application/json'
    ));

    $client->setBody('{  
       DATOS INMUEBLE EN FORMATO JSON
    }');

    try {
      $client->send();

      echo $client->getBody();
    } catch (\Exception $ex) {
      echo $ex;
    }

Ej:

	$base_url = "https://produzione.familyintale.com/create-personalized-tale_p/";

	$params = array ( 
		'name_b' => 'Andrea', 
		'name_p' => 'Pablo', 
		'genderkids' => 'm', 
		'genderparents' => 'm', 
		'characterkids' => 'bfb', 
		'characterparents' => 'gfb', 
		'tale_language' => 'es', 
		'tale_story' => 'gu', 
	);

	$url = Url::buildUrl($base_url, $params);

	$client = ApiClient::instance()
	->disableSSL()
	->redirect()
	->setUrl($url)
	//->setBody($body)
	->setMethod(ApiClient::HTTP_METH_GET);

	try {
		$client->send();

		if ($client->status() != 200){
			throw new \Exception($client->error());
		}

		dd(
			$client->getBody()
		);
	} catch (\Exception $ex) {
		dd($ex);
	}


# SOAP API con ApiClient

El siguiente es un ejemplo para una API SOAP concreta y debe ajustarse en cada caso.

Ej:

	$idbodega = 2;
	$codigos  = [];
	$token    = 'xxxxxxxx-xxxx-xxx-xxxx-xxxxxxxxxxx';
	$method   = 'consultarinventario';

	$url_base = 'http://xxxxxxxxxxxxxxxxxxxx.com:380/service.asmx?wsdl';

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

	$url      = "$url_base/$method";

	$client = new ApiClient();
	$client

	/*
		Seteo parámetos
	*/
	->setHeaders([
		'Content-Type' => 'text/xml;charset=UTF-8',
		'Accept' => 'text/xml',
	])
	->disableSSL()
	->followLocations()
	->setBody($data)
	
	->request($url, 'POST');       
	
	$error = $client->getError();
	$data  = $client->getResponse(true);

	$data  = $data['data'];

	dd($client->getStatus(), 'STATUS');
	dd($error, 'ERROR');
	dd($client->getHeaders(), 'HEADERS');
	dd($data, 'RES');
