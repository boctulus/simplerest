<?php

    use simplerest\libs\Files;
    use simplerest\libs\Strings;
    use simplerest\libs\DB;
    use simplerest\controllers\MakeController;

	/*
	    Re-generar todos los schemas

	*/

	$mk = new MakeController();

	$re_gen_schemas = function($tenant_id) use ($mk){
	    $mk->any("all", "-s", "-f", "--unignore", "--from:$tenant_id");
	    $mk->db_scan("--from:$tenant_id");
	};


	$db_representants = [
		'legion' => 'db_flor'
	];

	$tenants = DB::getAllTenantRepresentants($db_representants);

	foreach ($tenants as $db_conn_id){
		dd("Regenerating schemas for $db_conn_id");
		$re_gen_schemas($db_conn_id);
	}
	
		