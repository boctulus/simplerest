<?php

use simplerest\libs\Strings;
use simplerest\libs\DB;
use simplerest\core\Model;

function get_default_connection(){
    return DB::getDefaultConnection();
}

function get_users_table(){
    $users_table = config()['users_table'] ?? null;

    if (empty($users_table)){
        response()->sendError("Lacks users_table reference in config file", 500);
    }

    return $users_table;
}

function get_user_model_name(){
    $users_table = get_users_table();

    return '\\simplerest\\models\\' . Strings::snakeToCamel($users_table). 'Model';
}

function get_model_name($table_name){
    return '\\simplerest\\models\\' . Strings::snakeToCamel($table_name).  'Model';
}

function get_schema_name($table_name, $tenant_id = null){
    if ($tenant_id !== null){
        DB::getConnection($tenant_id); 
    }    
      
    if (DB::getCurrentConnectionId() == null || DB::getCurrentConnectionId() == config()['db_connection_default']){
        $extra = config()['db_connection_default'] . '\\';
    } else {
        $extra = '';
    }

    return '\\simplerest\\models\\schemas\\' . $extra . Strings::snakeToCamel($table_name). 'Schema';
}

function inSchema(array $props, string $table_name){
    $class = get_schema_name($table_name);

    if (!class_exists($class)){
        throw new \Exception("Class $class does not exist");
    }
   
    $attributes = array_keys($class::get()['attr_types']);

	if (empty($props))
		throw new \InvalidArgumentException("Attributes not found!");

	foreach ($props as $prop)
		if (!in_array($prop, $attributes)){
			return false; 
		}	
	
	return true;
}

function get_name_id(string $table_name, $tenant_id = null){
    $class = get_schema_name($table_name, $tenant_id);

    if (!class_exists($class)){
        throw new \Exception("Class $class does not exist");
    }

    return $class::get()['id_name'];
}


/*
    Lee linea por línea y ejecuta sentencias SQL
*/
function readSql(string $path){
    $file = file_get_contents($path);
    
    $sentences = explode(';', $file);
        
    foreach ($sentences as $sentence){
        $sentence = trim($sentence);

        if ($sentence == ''){
            continue;
        }

        dd($sentence, 'SENTENCE');

        try {
            $ok = Model::query($sentence);
        } catch (\Exception $e){
            dd($e, 'Sql Exception');
        }
    }    
}