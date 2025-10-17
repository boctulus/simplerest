# Clase Response

De Response se puede obtener una instancia a traves de Factory

Ej:

	Factory::response()->send($data);

Tambien se podria querer enviar un mensaje de error.

Ej:

	error("No encontrado", 404, "El recurso no existe");

Puede simplificase a,...

	response()->error("No encontrado", 404);

y a 
	error("No encontrado", 404, "El recurso no existe");

Tambien se puede arrojar un error con response() pero no permite enviar el detalle del error.

Ej:

	response("No encontrado", 404);

Nota:

Nunca devolver un objeto Response ya que genera un loop porque la respuesta es recibida nuevamente por Response:

	// OK
	Response::getInstance()
	->send($data);

pero...

	// MAL
	Response::getInstance()
	->send($data);

Respuesta condicional

Ej:

	if (response()->isEmpty()){
		response([
			'message' => 'OK'
		]);  
	}

# Redireccion

En vez de hacer por ejemplo, 

	header('HTTP/1.1 307 Temporary Redirect');
	header('Location: http://yahoo.com');  // nueva URL
	exit();

Se puede hacer:

	response()
	->redirect('http://yahoo.es', 307);


y si estuvieramos usando ApiClient para ver la respuesta:

Ej:

	$cli = new ApiClient($url);

	$res = $cli->disableSSL()
		// ->followLocations()
		->get()
		->getResponse(false);

	dd($cli->getStatus(), 'STATUS');
	dd($cli->getHeaders(), 'HEADERS');   // <-- aqui veriamos en header "Location: Location: http://yahoo.com"
	dd($cli->getError(), 'ERRORS');
	dd($res, 'RES');  

y si quisieramos que se redireccione de forma automática:

	$cli = new ApiClient($url);

	$res = $cli->disableSSL() 
		->followLocations()  // <------- *
		->get()
		->getResponse(false);

	dd($cli->getStatus(), 'STATUS');
	dd($cli->getHeaders(), 'HEADERS');  
	dd($cli->getError(), 'ERRORS');
	dd($res, 'RES');  

Importante

No eslo mismo usar "directamente" la clase Response ya sea instanciándola directa o indirectamente (o sea usando alguna factoria, contenedor de dependencias inversas) que hacer un return $data;

	/*
		Cons:
		No setea HTTP STATUS CODE
		No entrega la respuesta de forma estructurada y estandar

		Pros:
		Es reutilizable desde otros metodos
	*/

	public function get($pid = null, $user_id = null, $attributes = null, $order = null, $limit = null, $offset = null)
    {  
        if (empty($user_id)){
            error('Parameter `user_id` is required', 400);
        }

		$data = [];

		// ...

        Response::getInstance()
        ->send($data);
    }

Versus...

	/*
		Pros:
		Setea HTTP STATUS CODE
		Entrega la respuesta de forma estructurada y estandar

		Cons:
		Ya no es reutilizable desde otros metodos
	*/

	public function get($pid = null, $user_id = null, $attributes = null, $order = null, $limit = null, $offset = null)
    {  
        if (empty($user_id)){
            error('Parameter `user_id` is required', 400);
        }

		$data = [];

		// ...

        return $data;
    }

Y existe una tercera posibilidad que no ofrece desventajas:

	public function get($pid = null, $user_id = null, $attributes = null, $order = null, $limit = null, $offset = null)
    {  
        if (empty($user_id)){
            error('Parameter `user_id` is required', 400);
        }

		$data = [];

		// ...

        return Response::format($data);
    }

En este caso solo formateamos la respuesta con  Response::format($data) y la enviamos. Es posible llamar a ese metodo desde otros en el controller (o desde otro controller) y asi tener una solucion modularmente eficiente.

Si el metodo va a recibir peticiones por GET es preferible recibir la data como parametros en la funcion:

	public function get($pid = null, $user_id = null, ...){ }

que...

	// No esta'a parametrizada
	public function get(){
		$pid     = $_GET['pid'] ?? null;
		$user_id = $_GET['user_id'] ?? null;
		// ...
	}

Formateo de errores

El metodo Response::formatError() permite formatear erores ganando concistencia.

Tipicamente,

	$errors[] = Response::formatError("Product with product_id=$pid not found.", 404);

Ej:

	function prices($product_ids = null, $user_id = null)
    {
        $req  = Request::getInstance();

        if ($req->method() == 'POST'){            
            $data = $req
            ->getBodyDecoded();

            $product_ids = $data['product_ids'] ?? null;
            $user_id     = $data['user_id'] ?? null;
        }

        if (!is_array($product_ids)){
            $product_ids = explode(',', $product_ids);
        }
        
        if (empty($product_ids)){
            error('Parameter `product_ids` is required', 400);
        }

        if (empty($user_id)){
            error('Parameter `user_id` is required', 400);
        }

        $data   = [];
        $errors = [];
        foreach ($product_ids as $pid){
            $p = (bool) rand(0,1);           

            if ($p === false){
                $errors[] = Response::formatError("Product with product_id=$pid not found.", 404);
                continue;
            }

            $price = rand(100, 500);
            $salep = $price * 0.8;

            $data[] = [
                'normal_price' => $price,
                'sale_price'   => $salep  
            ];
        }           
       
        return Response::format($data, 200, $errors);
    }

Esto permite generar una respuesta estructurada para los errores:

	{
    "data": [
        {
            "normal_price": 131814,
            "sale_price": ""
        },
        {
            "normal_price": 121904,
            "sale_price": ""
        }
    ],
    "status_code": 200,
    "error": [
        {
            "message": "Product with product_id=11111111 not found.",
            "code": 404
        }
    ]
}

