<?php

use Boctulus\Simplerest\Core\Model;
use Boctulus\Simplerest\Core\Libs\DB;
use Boctulus\Simplerest\Core\Libs\Arrays;
use Boctulus\Simplerest\Core\Libs\Config;
use Boctulus\Simplerest\Core\Libs\StdOut;
use Boctulus\Simplerest\Core\Libs\Strings;
use Boctulus\Simplerest\Core\Exceptions\SqlException;

/*
    Ejecuta callback bajo una conexion a base de datos

    @param $new_connection_id
    @param $callback
*/
function withConnection($new_connection_id, $callback, ...$params) {
    $conn_id = DB::getCurrentConnectionId();
    $restore_conn = false;
    
    if ($conn_id === null || $conn_id != $new_connection_id) {  
        DB::setConnection($new_connection_id); 

        if ($conn_id != null){
            $restore_conn = true;
        }
    }
    
    try {
        $ret = $callback(...$params);
    } finally {
        if ($restore_conn) {
            DB::setConnection($conn_id);
        }

        return $ret ?? null;
    }
}

function withDefaultConnection($callback, ...$params) {
    $conn_id = DB::getCurrentConnectionId();
    $restore_conn = false;

    if ($conn_id != DB::getDefaultConnectionId()) {  
        DB::getDefaultConnection(); 

        if ($conn_id != null){
            $restore_conn = true;
        }
    }
    
    try {
        $ret = $callback(...$params);
    } finally {
        if ($restore_conn) {
            DB::setConnection($conn_id);
        }

        return $ret ?? null;
    }
}

function log_queries()
{
    $logFilePath = LOGS_PATH . 'mysql.txt';

    try {
        $conn = DB::getConnection();
        
        // Habilitar el registro general de consultas
        $conn->exec("SET GLOBAL general_log = 1");
        
        // Establecer la ubicación del archivo de registro general de consultas
        $conn->exec("SET GLOBAL general_log_file = '$logFilePath'");
        
        dd("General query log enabled successfully.");
    } catch (\PDOException $e) {
        dd("Error: " . $e->getMessage());
    }
}

/*
    @param Array ...$args por ejemplo "--dir=$folder", "--to=$tenant"
*/
function migrate(bool $show_response = true, ...$args){
    $mgr = new \Boctulus\Simplerest\Commands\MigrationsCommand();

    if (!$show_response){
        StdOut::hideResponse();
    }

    $mgr->migrate(...$args);
}

function get_default_connection(){
    return DB::getDefaultConnection();
}

function get_default_connection_id(){
    return DB::getDefaultConnectionId();
}

function get_default_database_name(){
    $def_con = get_default_connection_id();
    return Config::get()['db_connections'][$def_con]['db_name'];
}

function get_model_instance(string $model_name, $fetch_mode = 'ASSOC', bool $reuse = false){
    static $instance;

    if ($reuse && isset($instance[$model_name]) && !empty($instance[$model_name])){
        return $instance[$model_name];
    }

    if (!Strings::startsWith('\\Boctulus\\Simplerest\\', $model_name)){
        $model = get_model_namespace() . $model_name;
    } else {
        $model = $model_name;
    }
    
    $instance[$model_name] = (new $model(true))->setFetchMode($fetch_mode);
    DB::setModelInstance($instance[$model_name]);

    return $instance[$model_name];
}

function get_model_instance_by_table(string $table_name, $fetch_mode = 'ASSOC', bool $reuse = false){
    return get_model_instance(
        get_model_name($table_name)
    );
}

function get_api_name($resource_name, $api_ver = null){
    global $api_version;

    if ($api_ver !== null){
        $api_version = 'v'.$api_ver;
    }

    if (!Strings::startsWith('\\'. Config::get('namespace') . '\\',$resource_name)){
        $api = get_api_namespace($resource_name);
    } else {
        $api = $resource_name;
    }

    return $api;
}

/*
    Retorna definicion de modelos

    Ej:

    {
        "defs": {
            "id": {
                "type": "int"
            },
            "rating": {
                "formatter": "starts",
                "type": "int"
            },
            "how_popular": {
                "name": "Popularity",
                "formatter": "progress",
                "type": "int"
            },
            "created_at": {
                "name": "Creation Date",
                "type": "datetime"
            }
        }
    }        
*/
function get_model_defs(string $table_name, $tenant_id = null, bool $include_hidden = true, bool $include_related = true)
{
    if ($tenant_id != null){
        DB::getConnection($tenant_id);
    }

    $schema      = get_schema_name($table_name);
    $schema_defs = $schema::get();

    $defs          = [];
    $rel_tables    = [];

    /*
        Obtengo las "related tables"
    */
    $rels        = get_relations($tenant_id);
   
    if (!isset($rels['related_tables'])){
        throw new \Exception("Please run \"php com make relation_scan --from:$tenant_id\" or re-build All schemas!");
    }

    $_related    = $rels['related_tables'];
    $rel_tables  = $_related[$table_name] ?? [];

    Arrays::destroyIfExists($rel_tables, $table_name);

    // dd($schema_defs);
    // exit;

    $fields      = $schema_defs['fields'];
    $rules       = $schema_defs['rules'];

    $instance    = get_model_instance_by_table($table_name);

    $field_names = $instance->getFieldNames();  //  -- UNIFICAR field_names con formatters
    $formatters  = $instance->getformatters();  //

    $field_order = $instance->getFieldOrder();
    
    if (!empty($field_order)){
        $fields = Arrays::followOrder($fields, $field_order);
    }

    $hidden_ay   = $instance->getHidden();
    $fillable_ay = $instance->getFillables();
    $nullable_ay = $instance->getNullables(); 
    $uniques_ay  = $instance->getUniques();  

    $schema      = $instance->getSchema();

    $fk_ay       = $schema['fks'];
    $rels_from   = $schema['expanded_relationships_from'];

    // dd($fk_ay);

    foreach ($fields as $field){
        if (!$include_hidden && in_array($field, $hidden_ay)){
            continue;
        }

        $defs[$field]['hidden']   = in_array($field, $hidden_ay);
        $defs[$field]['fillable'] = in_array($field, $fillable_ay);
        $defs[$field]['nullable'] = in_array($field, $nullable_ay);
        $defs[$field]['unique']   = in_array($field, $uniques_ay);
        $defs[$field]['fk']       = in_array($field, $fk_ay);

        if ($defs[$field]['fk']){
            foreach ($rels_from as $rels_fk){
                foreach ($rels_fk as $rel){
                    
                    // dd($rel, 'REL');

                    if ($rel[1][1] == $field){
                        $t2 = $rel[0][0];

                        /*
                            Falta incluir la multiplicidad
                            (1>1, 1>n, n>m)
                        */

                        $rel = get_rel_type($table_name, $t2);

                        if ($rel == 'n:1'){
                            $rel = '1:n';
                        }
                        
                        $defs[$field]['fk'] = [
                            'table' => $t2,
                            'mul' => $rel,
                            'self_ref' => ($table_name == $t2)
                        ];
                    }
                }
            }
        }

        //
        //  -- UNIFICAR field_names con formatters
        //
        
        if (isset($field_names[$field])){
            $defs[$field]['name'] = $field_names[$field]; 
        } else {
            $defs[$field]['name'] = ucfirst(str_replace('_', ' ', $field));
        }
        
        if (isset($formatters[$field])){
            $defs[$field]['formatter'] = $formatters[$field];
        } 

        if (isset($rules[$field])){
            if (!isset($defs[$field])){
                $defs[$field] = $rules[$field];
            } else {
                $defs[$field] = array_merge($defs[$field], $rules[$field]);
            }
        }
    }
   
    // if ($include_related){
    //     $defs['__related_tables'] = array_values($rel_tables);
    // }
    
    return $defs;
}

// function get_related_table_defs(array $tables, $tenant_id, bool $include_hidden, $processed_tbs) {
//     $table_defs = [];

//     foreach ($tables as $table) {
//         if (!in_array($table, $processed_tbs)){
//             $table_defs[$table] = get_model_defs($table, $tenant_id, $include_hidden, $processed_tbs);
//             $processed_tbs[] = $table;
//         }       
//     }

//     return $table_defs;
// }



function get_defs(string $table_name, $tenant_id = null, bool $include_hidden = true, bool $include_hidden_from_api = true)
{
    $tenant_id = $tenant_id ?? DB::getCurrentConnectionId();    
    
    $defs       = get_model_defs($table_name, $tenant_id, $include_hidden);

    $api        = get_api_name($table_name, 1);
    $api_hidden = $api::getHidden();

    if ($include_hidden_from_api){
        foreach ($api_hidden as $col){
            if (array_key_exists($col, $defs)){
                $defs[$col]['hidden'] = true;
            }
        }
    }

    return $defs;
}


/*
    Similar to DB::table() but schema is not loaded so no validations are performed
*/
function table(string $tb_name, &$model_instance = null) : Model {
    if ($model_instance === null){
       $model_instance = new Model(true);
    }

    return $model_instance->table($tb_name);
}

function get_users_table(){
    $users_table = Config::get()['users_table'] ?? null;

    if (empty($users_table)){
        response()->error("users_table in config file is required", 500);
    }

    return $users_table;
}

function get_model_namespace($tenant_id = null){
    if ($tenant_id == null){
        $tenant_id = DB::getCurrentConnectionId(true);
    }   

    if ($tenant_id == Config::get()['db_connection_default']){
        $extra = Config::get()['db_connection_default'] . '\\';
    } else {
        $group = DB::getTenantGroupName($tenant_id);

        if ($group){
            $extra = $group . '\\'; 
        } else {
            $extra = '';
        }
    }

    return '\\Boctulus\\Simplerest\\Models\\' . $extra;
}

function get_model_name($table_name, $tenant_id = null){
    if ($tenant_id == null){
        $tenant_id = DB::getCurrentConnectionId(true);
    }   

    if ($tenant_id == Config::get()['db_connection_default']){
        $extra = Config::get()['db_connection_default'] . '\\';
    } else {
        $group = DB::getTenantGroupName($tenant_id);

        if ($group){
            $extra = $group . '\\'; 
        } else {
            $extra = '';
        }
    }

    return '\\Boctulus\\Simplerest\\Models\\' . $extra . Strings::snakeToCamel($table_name). 'Model';
}

function get_api_namespace($resource_name){
    return '\\Boctulus\\Simplerest\\Controllers\\API\\' . Strings::snakeToCamel($resource_name);
}

function get_user_model_name(){
    static $model_name;
    
    $users_table = get_users_table();
    $conn_id     = DB::getCurrentConnectionId(true);
    $key         = $conn_id . '.' . $users_table;

    if (isset($model_name[$key])){
        return $model_name[$key];
    }

    $model_name[$key] = get_model_name($users_table, $conn_id);

    return $model_name[$key];
}


function get_schema_path($table_name = null, $tenant_id = null){
    static $schema_paths = [];

    if ($tenant_id == null){
        $tenant_id = DB::getCurrentConnectionId(true);
    }   

    $_table_name = ($table_name !== null ? $table_name : '__NULL__');
    
    if (isset($schema_paths[$tenant_id][$_table_name])){
        return $schema_paths[$tenant_id][$_table_name];
    }

    if ($tenant_id == Config::get()['db_connection_default']){
        $extra = Config::get()['db_connection_default'] . '/';
    } else {
        $group = DB::getTenantGroupName($tenant_id);

        if ($group){
            $extra = $group . '/'; 
        } else {
            $extra = '';
        }
    }

    $path = SCHEMA_PATH . $extra ;

    if ($table_name !== null){
        $path .= Strings::snakeToCamel($table_name). 'Schema.php';
    }

    $schema_paths[$tenant_id][$_table_name] = $path;
    return $path;
}

function get_schema_name($table_name, $tenant_id = null){
    if ($tenant_id == null){
        $tenant_id = DB::getCurrentConnectionId();
    }   

    $defcon = Config::get()['db_connection_default'];

    if ($tenant_id == $defcon){
        $extra = $defcon . '\\';
    } else {
        $group = DB::getTenantGroupName($tenant_id);

        if ($group){
            $extra = $group . '\\'; 
        } else {
            $extra = '';
        }
    }

    return '\\Boctulus\\Simplerest\\Schemas\\' . $extra . Strings::snakeToCamel($table_name). 'Schema';
}

function has_schema($table_name, $tenant_id = null){
    if ($tenant_id == null){
        $tenant_id = DB::getCurrentConnectionId();

        if ($tenant_id == null){
            DB::getDefaultConnection();
            $tenant_id = DB::getCurrentConnectionId();
        }
    }

    $class = get_schema_name($table_name, $tenant_id);

    return class_exists($class);
}

function get_schema($table_name, $tenant_id = null){
    static $sc;

    if ($tenant_id == null){
        $tenant_id = DB::getCurrentConnectionId();

        if ($tenant_id == null){
            DB::getDefaultConnection();
            $tenant_id = DB::getCurrentConnectionId();
        }
    }

    $key = "$tenant_id:$table_name";

    if (isset($sc[$key])){
        return $sc[$key];
    }

    $class = get_schema_name($table_name, $tenant_id);

    if (!class_exists($class)){
        throw new \Exception("Class $class does not exist");
    }

    $sc[$key] = $class::get();

    return $sc[$key];
}


/*
    Check if *at least* there is one relation between tables which is x_x where x_x can be 1:1, 1:n o n:m

    If relation ("table1.key1=table2.key2") is given then it is the only evaluated

    @deprecated Use Model::validateTableRelationship() instead
*/
function is_x_x(string $x_x, string $t1, string $t2, ?string $relation_str = null, ?string $tenant_id = null){
    return Model::validateTableRelationship($x_x, $t1, $t2, $relation_str, $tenant_id);
}

/*
    Check if *at least* there is one relation between tables which is 1:1

    @deprecated Use Model::is11() instead
*/
function is_1_1(string $t1, string $t2, ?string $relation_str = null, ?string $tenant_id = null){
    return Model::is11($t1, $t2, $relation_str, $tenant_id);
}

/*
    Check if *at least* there is one relation between tables which is 1:n

    @deprecated Use Model::is1N() instead
*/
function is_1_n(string $t1, string $t2, ?string $relation_str = null, ?string $tenant_id = null){
    return Model::is1N($t1, $t2, $relation_str, $tenant_id);
}

/*
    Check if *at least* there is one relation between tables which is n:1

    @deprecated Use Model::isN1() instead
*/
function is_n_1(string $t1, string $t2, ?string $relation_str = null, ?string $tenant_id = null){
    return Model::isN1($t1, $t2, $relation_str, $tenant_id);
}

/*
    Check if *at least* there is one relation between tables which is n:m

    @deprecated Use Model::isNM() instead
*/
function is_n_m(string $t1, string $t2, ?string $relation_str = null, ?string $tenant_id = null){
    return Model::isNM($t1, $t2, $relation_str, $tenant_id);
}

/*
    Get the type of relationship between two tables

    @deprecated Use Model::getRelType() instead
*/
function get_rel_type(string $t1, string $t2, ?string $relation_str = null, ?string $tenant_id = null){
    return Model::getRelType($t1, $t2, $relation_str, $tenant_id);
}


/*
    Returns if relation can produce multiple rows

    @deprecated Use Model::isMulRel() instead
*/
function is_mul_rel(string $t1, string $t2, ?string $relation_str = null ,?string $tenant_id = null){
    return Model::isMulRel($t1, $t2, $relation_str, $tenant_id);
}

/*
    Devuelve el contenido del archivo Relations.php
    para el tenant especificado o el actual

    @deprecated Use Model::getRelations() instead
*/
function get_relations(?string $tenant_id = null, ?string $table = null){
    return Model::getRelations($tenant_id, $table);
}

/*
    Returns if relation can produce multiple rows (cached version)

    @deprecated Use Model::isMulRelCached() instead
*/
function is_mul_rel_cached(string $t1, string $t2, ?string $relation_str = null ,?string $tenant_id = null){
    return Model::isMulRelCached($t1, $t2, $relation_str, $tenant_id);
}

function in_schema(array $props, string $table_name, ?string $tenant_id = null)
{  
    $sc = get_schema($table_name, $tenant_id);
    $attributes = array_keys($sc['attr_types']);

	if (empty($props))
		throw new \InvalidArgumentException("Attributes not found!");

	foreach ($props as $prop)
		if (!in_array($prop, $attributes)){
			return false; 
		}	
	
	return true;
}

// alias
function inSchema(array $props, string $table_name, ?string $tenant_id = null){
    return in_schema($props, $table_name, $tenant_id);
}

function get_primary_key(string $table_name, $tenant_id = null){
    static $keys = [];

    if (is_null($tenant_id)){
        $tenant_id = DB::getCurrentConnectionId(true);
    } 

    if (isset($keys[$tenant_id][$table_name])){
        return $keys[$tenant_id][$table_name];
    }

    $sc  = get_schema($table_name, $tenant_id);
    $keys[$tenant_id][$table_name] = $sc['id_name'];

    return $keys[$tenant_id][$table_name];
}

// alias
function get_id_name($table_name, $tenant_id = null){
    return get_primary_key($table_name, $tenant_id);
}

/*
    Get pivot table information for many-to-many relationships

    @deprecated Use Model::getPivot() instead
*/
function get_pivot(Array $tables, ?string $tenant_id = null){
    return Model::getPivot($tables, $tenant_id);
}


/*
    Retorna la o las relaciones del tipo solicitado entre dos tablas (si existen)

    @param $type '1:1' or '1:n' or 'n:m'
    @return mixed

    El retorno es algo complejo porque puede devolver false o un array vacio en caso de "fallo"
    o un array con las relaciones en caso de "éxito". Específicamente devuelve false cuando
    no hay una relación directa entre las tablas (caso n:m) y se pregunta si la relación es 1:1 o 1:n

    A su vez, el array devuelto tiene una estructura distinta según sea una relación n:m o 1:1/1:n

    @deprecated Use Model::getRels() instead
*/
function get_rels(string $t1, string $t2, string $type, ?string $tenant_id = null, string $relation_str = null){
    return Model::getRels($t1, $t2, $type, $tenant_id, $relation_str);
}

/*
    Returns FK(s) on $t1 pointing to $t2

    If there is more than one relationship between tables, then can be more than one FK.

    @deprecated Use Model::getFks() instead
*/
function get_fks(string $t1, string $t2, ?string $tenant_id = null){
    return Model::getFks($t1, $t2, $tenant_id);
}

// 16-feb-2024
function tb_prefix() {
    $conn_id = DB::getCurrentConnectionId();

    $cfg = Config::get();

    if ($conn_id == null){
        return $cfg['db_connections']['main']['tb_prefix'] ?? null;
    }

    return $cfg['db_connections'][$conn_id]['tb_prefix'] ?? null;
}

function sql_formatter(string $sql, ...$options){
    return Model::sqlFormatter($sql, ...$options);
}

/*
    Lee linea por línea y ejecuta sentencias SQL
*/
function process_sql_file(string $path, string $delimeter = ';', bool $stop_if_error = false){
    $file = file_get_contents($path);
    
    $sentences = explode($delimeter, $file);
        
    foreach ($sentences as $sentence){
        $sentence = trim($sentence);

        if ($sentence == ''){
            continue;
        }

        StdOut::print('SENTENCE : ' . $sentence);

        try {
            $ok = DB::statement($sentence);
        } catch (SqlException $e){
            dd($e->getMessage(), 'Sql Exception');

            if ($stop_if_error){
                exit(1);
            }
        }
    }    

    function enqueue_data($data, $category = null) {
        return DB::enqueue($data, $category);
    }
    
    function deque_data($category = null, bool $full_row = false) {
        return DB::deque($category, $full_row);
    }     
}