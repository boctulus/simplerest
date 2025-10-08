### Controlador

Los controladores son clases cuyos métodos ejecutan acciones al ser invocados desde el FrontController o el Router.

Para demostrar como ejecutar un controlador desde la linea de comandos ejemplificaremos con un simple controlador ubicado en la carpeta controllers:

	<?php

	namespace Boctulus\Simplerest\Controllers;

	class DumbController extends Controller
	{

		function add($a, $b){
			$res = (int) $a + (int) $b;
			return  "$a + $b = " . $res;
		}

	}

La acción será ejecutada con: 

	php com dumb add 1 6

Hay soporte para sub-directorios o sub-sub-(sub)-directorios 


# Front Controller y Router 

Es posible configurar el uso del Front Controller y/o del Router. El primero es más sencillo pero se aconseja casi exclusivamente para utilizar los controllers desde la terminal. 

El Router tiene la enorme ventaja de permitir filtrar los requests por su verbo HTTP:

	# ruta: /calc/sum/4/5
	WebRouter::get('calc/sum', function($a, $b){
		return "La suma de $a y $b da ". ($a + $b);
	})->where(['a' => '[0-9]+', 'b' =>'[0-9]+']);

	# ruta: /saludador/sp/hi/Juan/Carlos/44
	WebRouter::get('saludador/sp/hi/', function($nombre, $apellido, $edad){
		return "Hola ". ($edad > 30 ? 'Sr. ' : '') . "$nombre $apellido";
	})->where(['edad' => '[0-9]+', 'nombre' =>'[a-zA-Z]+', 'apellido' =>'[a-zA-Z]+']);

	# ruta: /saludador/en/hi/Juan/Carlos/44
	WebRouter::get('saludador/en/hi/', function($nombre, $apellido, $edad){
		return "Hello ". ($edad > 30 ? 'Mr. ' : '') . "$apellido $nombre";
	})->where(['edad' => '[0-9]+', 'nombre' =>'[a-zA-Z]+', 'apellido' =>'[a-zA-Z]+']);

	# ruta: /tonterias
	WebRouter::get('tonterias',  'DumbController');

	# ruta: /chatbot/hi
	WebRouter::get('chatbot/hi', 'DumbController@hi')
	->where(['name' => '[a-zA-Z]+']);  // <-- where() no implementado con controladores !

	# ruta: /cosas/67
	WebRouter::delete('cosas', function($id){
		return "Deleting cosa con id = $id";
	});

El router se habilitan desde el archivo config/config.php y se configura desde /config/routes.php

Actualmente el orden en que se definen las rutas es importante ..... (es algo que debe corregirse).

Esta obligandose a ir de lo especifico a lo general.

Ej:

	WebRouter::get('admin/una-pagina', function(){
		$content = "Pagina (de acceso restringido)";
		render($content);
	});

	WebRouter::get('admin/pagina-dos', function(){
		$content = "Pagina dos (de acceso restringido)";
		render($content);
	});

	WebRouter::get('admin', function(){
		$content = "Panel de Admin";
		render($content);
	});


# Router

Introducción

El sistema de enrutamiento permite definir rutas de manera flexible utilizando una sintaxis simplificada. Este manual cubre la estructura y el uso de las rutas, incluyendo el método WebRouter::fromArray() para registrar múltiples rutas de manera eficiente.

Definición de Rutas

Las rutas pueden definirse utilizando los métodos estándar o con el nuevo método fromArray().

Métodos estándar

Se pueden registrar rutas utilizando los métodos get(), post(), put(), patch(), delete(), y options().

	WebRouter::get('/usuario/{id}', 'UserController@show');
	WebRouter::post('/producto', 'ProductController@store');

Uso de WebRouter::fromArray()

El método fromArray() permite definir múltiples rutas en un solo llamado.

Formato del Array

Cada entrada del array debe seguir el formato:

	'VERB:/ruta' => 'Controlador@metodo' (para una ruta con verbo específico)

	'/ruta' => 'Controlador@metodo' (para todos los verbos soportados)

Ejemplo

	WebRouter::fromArray([
		'GET:/speed_check' => 'boctulus\relmotor_central\controllers\SpeedCheck@index',
		'POST:/producto' => 'ProductController@store',
		'/ping' => 'SystemController@ping' // Se registrará en GET, POST, PUT, PATCH, DELETE, OPTIONS
	]);

Rutas con parametros

	WebRouter::get('user/{id}', 'DumbController@test_r2')->where(['id' => '[0-9]+']);

Grupos de rutas

	WebRouter::group('admin', function() {
		WebRouter::get('dashboard', 'DumbController@dashboard');
		WebRouter::get('settings', 'DumbController@settings');
	});

Funciones anonimas 

WebRouter::get('system/info/gettext', function(){
	if ( false === function_exists('gettext') ) {
		echo "You do not have the gettext library installed with PHP.";
		exit(1);
	}
});


# Route de consola

CliRouter es un componente que permite ejecutar comandos PHP desde la línea de comandos, facilitando la creación de scripts de automatización y la ejecución de tareas administrativas.

Uso Básico

Para ejecutar un comando, use la sintaxis:

	php com {comando} [argumentos]

Ej:

	php com hello

Salida esperada:

	Hello, world!
	
Manejo de Parámetros

Se pueden pasar argumentos a los comandos de la siguiente manera:

	php com math sum 10 20

Si el controlador MathController tiene el método sum, este recibirá los argumentos 10 y 20.

Ej:

	class MathController {
		function sum($a, $b) {
			return $a + $b;
		}
	}

	Salida esperada:

	30

Controladores en carpetas

Los controladores pueden organizarse en subcarpetas dentro de controllers. Para ejecutar un comando en una carpeta, use:

	php com folder\class_name action

Esto ejecutará el método action dentro de folder\{CassName}Controller.


Diferencia con FrontController

Mientras tanto CliRouter como FrontController pueden ejecutar asi:

	php com folder\calc inc 7

solo FrontController puede hacerlo asi:

	php com folder calc inc 7

O sea el FrontController tiene no necesita la "\"


Archivo config/cli_routes.php

CliRouter procesa el archivo cli_routes.php de forma similar a como WebRouter procesa routes.php

Ej:
	
	CliRouter::command('dbdriver', 'Boctulus\Simplerest\Controllers\DumbController@db_driver');
	CliRouter::command('adder', 'Boctulus\Simplerest\Controllers\Calculator@add');

Funciones anonimas

De forma similar al WebRouter, el CliRouter acepta funciones anonimas en el config/cli_routes.php

Ej:

	// Funcion anonima sin parametros 
	CliRouter::command('version', function() {
		return 'SimpleRest Framework v1.0.0';
	});

	// Funcion anonima con parametros
	CliRouter::command('pow', function($num, $exp) {
		return pow($num, $exp);
	});


Errores Comunes y Soluciones

- Comando no encontrado: Verifique que el nombre del controlador y el método sean correctos.

- Argumentos incorrectos: Revise la documentación del comando y asegúrese de pasar los valores correctos.

- Espacios en argumentos: Use comillas si el argumento contiene espacios.
