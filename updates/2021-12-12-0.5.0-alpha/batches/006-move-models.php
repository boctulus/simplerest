<?php

    use simplerest\libs\Files;
    use simplerest\libs\Strings;
    use simplerest\libs\DB;
    use simplerest\libs\Schema;

	/*
        Muevo modelos a su nueva ubicación
    */

    $db_representants = [
        'legion' => 'db_flor'
    ];

    /* 
        Creo estructura 
    */
    $groups  = DB::getTenantGroupNames();

    foreach ($groups as $g){
        Files::mkDir(MODELS_PATH . $g);
    }

    Files::mkDir(MODELS_PATH . get_default_connection_id());

    /*
        Muevo modelos
    */

    $grouped = DB::getDatabasesGroupedByTenantGroup(true);
    //dd($grouped);
    
    foreach ($grouped as $group_name => $db_name){
        // elijo la conexión a una DB cualquiera de cada grupo como representativa
        // o la que me especifiquen
        $db_conn = $db_name[0];

        if (isset($db_representants) && !empty($db_representants)){
            if (isset($db_representants[$group_name])){
                $db_conn = $db_representants[$group_name];
            }
        } 

        $tables = Schema::getTables($db_conn);
        
        foreach ($tables as $tb){
            $model_name = Strings::snakeToCamel($tb) . 'Model';
            $filename   = "$model_name.php";
            $ori_path   = MODELS_PATH . $filename;
            $dst_path   = MODELS_PATH . $group_name . DIRECTORY_SEPARATOR . $filename;

            //dd([$ori_path, $dst_path]);

            if (!file_exists($ori_path)){
                continue;
            }

            $ok = rename($ori_path, $dst_path);
            d($ok, "Move $ori_path > $dst_path done");
        }
    }