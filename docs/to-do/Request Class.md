# Clase Request

Las clases Request y Response implementan el patron Singleton.

Ej:

	$req = Request::getInstance();
	$res = Response::getInstance();

	$limit  = $req->get('limit', 10);
	$offset = $req->get('offset', 0);
	$order  = $req->get('order', []);

	$filters = [];

	// Procesar filtros
	if ($req->has('sku')) {
		$skus = explode(',', $req->get('sku'));
		$filters['sku'] = $skus;
	}

	// ...

