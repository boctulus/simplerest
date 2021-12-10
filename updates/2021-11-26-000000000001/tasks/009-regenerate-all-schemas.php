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
	    $mk->pivot_scan("--from:$tenant_id");
	};


	$conn_ids = DB::getConnectionIds();

	foreach ($conn_ids as $cid){
	    $re_gen_schemas($cid);
	}