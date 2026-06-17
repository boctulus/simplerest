<?php

use Boctulus\Simplerest\Core\WebRouter;

// GET api/v1/files/{id} -> obtiene info del archivo (id = uuid). Ruta dedicada con placeholder
WebRouter::get('api/v1/files/{id}', function($id){
    $controller = new \Boctulus\Simplerest\Core\Api\Files();
    $ret = $controller->get($id);    
	\Boctulus\Simplerest\Core\Libs\Factory::response()->flush();
});

// GET api/v1/files -> listado de archivos (usa index() del ApiController)
WebRouter::get('api/v1/files', function(){
    $controller = new \Boctulus\Simplerest\Core\Api\Files();
    $ret = $controller->get();    
	\Boctulus\Simplerest\Core\Libs\Factory::response()->flush();
});

// POST api/v1/files -> upload (multipart/form-data, campo `file`, soporta varios)
WebRouter::post('api/v1/files', function(){
	$controller = new \Boctulus\Simplerest\Core\Api\Files();

	$controller->post();
	// En closures de WebRouter el framework no llama flush() automáticamente:
	// post() hace response()->send() (solo setea el body); flush() lo emite y exit
	// (sin esto el shutdown agrega un "null" y el body llega vacío).
	\Boctulus\Simplerest\Core\Libs\Factory::response()->flush();
});

// DELETE api/v1/files/{id} -> borra el archivo (id = uuid). Ruta dedicada con placeholder
// {id}: WebRouter lo inyecta como argumento del closure ($id).
WebRouter::delete('api/v1/files/{id}', function($id){
	$controller = new \Boctulus\Simplerest\Core\Api\Files();
	$controller->delete($id);
	\Boctulus\Simplerest\Core\Libs\Factory::response()->flush();
});

// GET download/{id} -> descarga/sirve el archivo por id (= uuid). Download::get() hace
// readfile()+exit (no usa response()->send), así que no requiere flush(). Es el destino
// del `link` que devuelve el upload (base_url()/download/<uuid>).
WebRouter::get('download/{id}', function($id){
	$controller = new \Boctulus\Simplerest\Core\Api\Download();
	$controller->get($id);
});
