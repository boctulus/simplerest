# Middlewares

Los middlewares constituyen una capa intermedia que permiten interceptar una respuesta y alterarla o modificar el flujo del programa proveyendo un punto donde se pueden hacer chequeos de seguridad por ejemplo.

SimpleRest implementa Middlewares para el FrontController de la siguiente manera:

Supongamos que se tiene un Controlador llamado TestController con un método que genera una respuesta y queremos alterarla *sin* tocar el código del método.

Ej:

	// Controlador de ejemplo

	class TestController {
		// ...

		function mid(){
			return "Hello World!";        
		}
	}

1) Se debe crear un Middleware que tendrá una estructura parecida a:

	<?php

	namespace Boctulus\Simplerest\middlewares;

	use Boctulus\Simplerest\Core\Middleware;
	use Boctulus\Simplerest\Core\Libs\Strings;

	class InyectarSaludo extends Middleware
	{   
		function handle(){
			$data = response()->get();

			if (is_string($data)){
				if (Strings::startsWith('Hello ', $data)){
					response()->set(preg_replace('/Hello (.*)/', "Hello happy $1", $data,1));
				}
			}
		}
	}

2) Se registra el Middleware en config/middlewares.php

	return [
		// ...
		'Boctulus\Simplerest\Controllers\TestController@mid' => InyectarSaludo::class,
		// ...
	];

Ahora la respuesta del método mid() de TestController será interceptada y modificada.

Para registrar todos los metodos de un controlador use @__all__

Ej:
	/*
    	Middleware registration
	*/

	return [
		'Boctulus\Simplerest\Controllers\FiltersController@__all__' => RestrictContentRoleBased::class,

Registrar varios Middleware(s) para el mismo controller

Es cuestion de pasar un array de Middlewares

Ej:

	return [
		'boctulus\relmotor_central\controllers\WooCommerceFiltersController@__all__' => [
			RestrictContentRoleBased::class,
			ProcessShortcodesInDescription::class
		]
	]