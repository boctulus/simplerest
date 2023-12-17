<?php

use simplerest\core\Model;
use simplerest\core\libs\DB;
use simplerest\models\MyModel;
use simplerest\core\libs\Arrays;
use simplerest\core\libs\StdOut;
use simplerest\core\libs\Strings;
use simplerest\core\exceptions\SqlException;
use simplerest\controllers\MigrationsController;
use simplerest\core\controllers\MakeControllerBase;

/*
    @param Array ...$args por ejemplo "--dir=$folder", "--to=$tenant"
*/
function migrate(bool $show_response = true, ...$args){
    $mgr = new MigrationsController();

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
    return config()['db_connections'][$def_con]['db_name'];
}

function get_model_instance(string $model_name, $fetch_mode = 'ASSOC', bool $reuse = false){
    static $instance;

    if ($reuse && isset($instance[$model_name]) && !empty($instance[$model_name])){
        return $instance[$model_name];
    }

    if (!Strings::startsWith('\\simplerest\\', $model_name)){
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

    if (!Strings::startsWith('\\simplerest\\',$resource_name)){
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
function get_model_defs(string $table_name, $tenant_id = null, bool $include_hidden = true, bool $include_related = true){
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



function get_defs(string $table_name, $tenant_id = null, bool $include_hidden = true, bool $include_hidden_from_api = true){
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
function table(string $tb_name) : MyModel {
    return (new MyModel(true))->table($tb_name);
}

function get_users_table(){
    $users_table = config()['users_table'] ?? null;

    if (empty($users_table)){
        response()->error("users_table in config file is required", 500);
    }

    return $users_table;
}

function get_model_namespace($tenant_id = null){
    if ($tenant_id == null){
        $tenant_id = DB::getCurrentConnectionId(true);
    }   

    if ($tenant_id == config()['db_connection_default']){
        $extra = config()['db_connection_default'] . '\\';
    } else {
        $group = DB::getTenantGroupName($tenant_id);

        if ($group){
            $extra = $group . '\\'; 
        } else {
            $extra = '';
        }
    }

    return '\\simplerest\\models\\' . $extra;
}

function get_model_name($table_name, $tenant_id = null){
    if ($tenant_id == null){
        $tenant_id = DB::getCurrentConnectionId(true);
    }   

    if ($tenant_id == config()['db_connection_default']){
        $extra = config()['db_connection_default'] . '\\';
    } else {
        $group = DB::getTenantGroupName($tenant_id);

        if ($group){
            $extra = $group . '\\'; 
        } else {
            $extra = '';
        }
    }

    return '\\simplerest\\models\\' . $extra . Strings::snakeToCamel($table_name). 'Model';
}

function get_api_namespace($resource_name){
    return '\\simplerest\\controllers\\api\\' . Strings::snakeToCamel($resource_name);
}

function get_user_model_name(){
    static $model_name;
    
    $users_table = get_users_table(true);
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

    if ($tenant_id == config()['db_connection_default']){
        $extra = config()['db_connection_default'] . '/';
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

    if ($tenant_id == config()['db_connection_default']){
        $extra = config()['db_connection_default'] . '\\';
    } else {
        $group = DB::getTenantGroupName($tenant_id);

        if ($group){
            $extra = $group . '\\'; 
        } else {
            $extra = '';
        }
    }

    return '\\simplerest\\schemas\\' . $extra . Strings::snakeToCamel($table_name). 'Schema';
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
*/
function is_x_x(string $x_x, string $t1, string $t2, ?string $relation_str = null, ?string $tenant_id = null){
    static $rel;

    if (!in_array($x_x, ['1:1', '1:n', 'n:1', 'n:m'])){
        throw new \InvalidArgumentException("First parameter can only be ['1:1', '1:n', 'n:m']");
    }

    if (is_null($tenant_id)){
        $tenant_id = DB::getCurrentConnectionId(true);
    }  

    $key = "$tenant_id:$t1.{$t2}|" . (empty($relation_str) ? 'all' : $relation_str) . " -- for $x_x";

    if (isset($rel[$key])){
        return $rel[$key];
    }

    $rls = get_rels($t1, $t2, $x_x, $tenant_id, $relation_str);

    $rel[$key] = !empty($rls);

    return $rel[$key] ;
}

/*
    Check if *at least* there is one relation between tables which is 1:n
*/
function is_1_1(string $t1, string $t2, ?string $relation_str = null, ?string $tenant_id = null){
    return is_x_x('1:1', $t1, $t2, $relation_str, $tenant_id);
}

/*
    Check if *at least* there is one relation between tables which is 1:n
*/
function is_1_n(string $t1, string $t2, ?string $relation_str = null, ?string $tenant_id = null){
    return is_x_x('1:n', $t1, $t2, $relation_str, $tenant_id);
}

function is_n_1(string $t1, string $t2, ?string $relation_str = null, ?string $tenant_id = null){
    return is_x_x('n:1', $t1, $t2, $relation_str, $tenant_id);
}

/*
    Check if *at least* there is one relation between tables which is n:m
*/
function is_n_m(string $t1, string $t2, ?string $relation_str = null, ?string $tenant_id = null){
    return is_x_x('n:m', $t1, $t2, $relation_str, $tenant_id);
}

/*
    Get the type of relationship between two tables

*/
function get_rel_type(string $t1, string $t2, ?string $relation_str = null, ?string $tenant_id = null){
    static $rel;

    if (is_null($tenant_id)){
        $tenant_id = DB::getCurrentConnectionId(true);
    }    

    $key = "$tenant_id:$t1.{$t2}|$relation_str";

    if (isset($rel[$key])){
        return $rel[$key];
    }

    if (is_n_m($t1, $t2, $relation_str, $tenant_id)){
        $rel[$key] = 'n:m';
        return $rel[$key];
    }

    if (is_1_n($t1, $t2, $relation_str, $tenant_id)){
        $rel[$key] = '1:n';
        return $rel[$key];
    }

    if (is_n_1($t1, $t2, $relation_str, $tenant_id)){
        $rel[$key] = 'n:1';
        return $rel[$key];
    }

    if (is_1_1($t1, $t2, $relation_str, $tenant_id)){
        $rel[$key] = '1:1';
        return $rel[$key];
    }

    $rel[$key] = false;
    return $rel[$key];
}


/*
    Returns if relation can produce multiple rows
*/
function is_mul_rel(string $t1, string $t2, ?string $relation_str = null ,?string $tenant_id = null) {	
    if (empty($tenant_id)){
        $tenant_id = DB::getDefaultConnectionId(); 
    }
   
    $rel_type = get_rel_type($t1, $t2, $relation_str, $tenant_id);

    switch ($rel_type){
        case '1:1':
            return false;
        case 'n:m':
            return true;
        case '1:n':
            return true;
        case 'n:1': 
            return false;  

        default:
            StdOut::pprint("[ Warning ] Unknow or ambiguous relationship for $tenant_id:$t1~$t2 !!!");
    }
}

/*
    Devuelve el contenido del archivo Relations.php 
    para el tenant especificado o el actual
*/
function get_relations(?string $tenant_id = null, ?string $table = null){
    static $rels;

    $def_conn_id = config()['db_connection_default'];

    $key = ($tenant_id ?? $def_conn_id) . '.' . $table;

    if ($rels === null){
        $rels = [];
    }

    if (isset($rels[$key])){
        return $rels[$key];
    }

    if ($tenant_id == $def_conn_id){
        $folder = $def_conn_id . '/';
    } else {
        if ($tenant_id === null){
            $tenant_id = DB::getDefaultConnectionId();
            $folder = $tenant_id . '/';

            DB::getConnection($tenant_id);
        } else {
            $group = DB::getTenantGroupName($tenant_id);

            if ($group){
                $folder = $group . '/';
            } else {
                $folder = '';
            }
        }
    }

    $path = SCHEMA_PATH . $folder . 'Relations.php';

    if (!file_exists($path)){
        throw new \Exception("Please run \"php com make relation_scan --from:$tenant_id\" or re-build All schemas!");
    }

    $rels[$key] = include $path;

    return $rels[$key];
}

function is_mul_rel_cached(string $t1, string $t2, ?string $relation_str = null ,?string $tenant_id = null){
    if (!is_null($relation_str)){
        return is_mul_rel($t1, $t2, $relation_str, $tenant_id);
    }

    $r = get_relations($tenant_id);

    if (!isset($r['multiplicity']["$t1~$t2"])){
        throw new \Exception("Please run \"php com make relation_scan --from:$tenant_id\" or re-build all schemas");
    }

    return $r['multiplicity']["$t1~$t2"];
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

function get_pivot(Array $tables, ?string $tenant_id = null){
    static $ret = [];

    if (is_null($tenant_id)){
        $tenant_id = DB::getCurrentConnectionId(true);
    }  

    sort($tables);
    
    $needle = implode(',', $tables);

    if (isset($ret[$tenant_id][$needle])){
        return $ret[$tenant_id][$needle];
    }

    $dir = get_schema_path(null, $tenant_id);
    
    if (!file_exists($dir . 'Pivots.php')){
        StdOut::hideResponse();

        $mk = new MakeControllerBase();

        if (!empty($tenant_id)){
            $mk->pivot_scan("--from:$tenant_id");
        } else {
            $mk->pivot_scan();
        }

        StdOut::showResponse();
    }

    include $dir . 'Pivots.php';
    
    
    if (!isset($pivots[$needle])){
        return;
    }

    if (!isset($ret[$tenant_id])){
        $ret[$tenant_id] = [];
    }

    if (!isset($ret[$tenant_id])){
        $ret[$tenant_id][$needle] = [];
    }

    $bridge = $pivots[$needle];

    $ret[$tenant_id][$needle] = [
        'bridge' => $bridge, 
        'fks' => $pivot_fks[$bridge],
        'relationships' => $relationships[ $pivots[$needle] ]
    ];

    return $ret[$tenant_id][$needle];
}


/*
    Retorna la o las relaciones del tipo solicitado entre dos tablas (si existen)

    @param $type '1:1' or '1:n' or 'n:m'
    @return mixed

    El retorno es algo complejo porque puede devolver false o un array vacio en caso de "fallo"
    o un array con las relaciones en caso de "éxito". Específicamente devuelve false cuando 
    no hay una relación directa entre las tablas (caso n:m) y se pregunta si la relación es 1:1 o 1:n

    A su vez, el array devuelto tiene una estructura distinta según sea una relación n:m o 1:1/1:n

*/
function get_rels(string $t1, string $t2, string $type, ?string $tenant_id = null, string $relation_str = null){
    $type = strtolower($type);

    if (!in_array($type, ['1:1', '1:n', 'n:1', 'n:m'])){
        throw new \InvalidArgumentException("Type can be only '1:1', '1:n', 'n:1' or 'n:m'");
    }

    $current_id_conn = DB::getCurrentConnectionId();
    $conn = DB::getConnection($tenant_id);

    try {

        switch ($type){
            /*
                https://stackoverflow.com/questions/10292355/how-to-create-a-real-one-to-one-relationship-in-sql-server
                https://stackoverflow.com/a/25547364/980631

            */
            case '1:1':
                $pris = [];
    
                $sc  = get_schema_name($t1, $tenant_id)::get();

                if (!array_key_exists('autoincrement', $sc)){
                    throw new \Exception("Schema file for $t1 is outdated. Please re-generate all.");
                }
                
                $rels  = $sc['expanded_relationships'] ?? null;
                $uni1  = $sc['uniques'];
                $pri1  = $sc['primary']; 
                $auto1 = $sc['autoincrement'];
    
                if ($rels === null){
                    throw new \Exception("Unexpected error. There are not relationships");
                }
                $rs = $rels[$t2] ?? null;
                if (empty($rs)){
                    return false;
                }
    
                /*
                    SI la relación es entre dos PRIMARY KEYS (con sus campos AUTOINCREMENT o en caso de ser compuesta UNIQUE)
                    SI la relación es entre una PRIMARY KEY y una FK que es UNIQUE
    
                    =>
    
                    La relación es 1:1
                */
    
                // cumplen con la relación
                $meet = [];
    
                $sc2  = get_schema_name($t2, $tenant_id)::get();
                
                // $rels2 = $sc2['expanded_relationships'] ?? null;
                $uni2  = $sc2['uniques'];
                $pri2  = $sc2['primary'];
                $auto2 = $sc2['autoincrement']; 
    
                $pris[$t1] = $pri1;
                $pris[$t2] = $pri2;
    
                $unis[$t1] = $uni1;
                $unis[$t2] = $uni2;
    
    
                foreach ($rs as $ix => $r){             
    
                    $tb1 = $r[0][0];
                    $k1  = $r[0][1];
                    
                    $tb2 = $r[1][0];
                    $k2  = $r[1][1];
                
    
                    $meet[$ix] = false;
                    $rel_between_pri_keys = false;
                    $rel_between_pri_key_and_unique_key = false;

                    $rel_between_pri_keys = (in_array($k1, $pris[$tb1]) && in_array($k2, $pris[$tb2]));
                    
                    if ($rel_between_pri_keys){
                        // Ahora chequearé si hay "unicidad" de ambos lados,....
                        
                        // dd($pris[$tb1], '$pris[$tb1]');
                        // dd($pris[$tb2], '$pris[$tb2]');

                        $cnt_pris_tb1 = count($pris[$tb1]);
                        $cnt_pris_tb2 = count($pris[$tb2]);

                        // Caso simple: ambas PRI KEYs son simples
                        if ($cnt_pris_tb1 == 1 && $cnt_pris_tb2 == 1){
                            $meet[$ix] = true;
                            continue;
                        } else {
                            // Acá es menos claro el asunto,........
                        }                    
                    } 

                    // dd($tb1, 'tb1');
                    // dd($k1, 'k1');
                    // dd($tb2, 'tb1');
                    // dd($k2, 'k2');

                    $k1_in_pris_tb1 = in_array($k1, $pris[$tb1]);
                    $k1_in_unis_tb1 = in_array($k1, $unis[$tb1]);
                    $k2_in_pris_tb2 = in_array($k2, $pris[$tb2]);
                    $k2_in_unis_tb2 = in_array($k2, $unis[$tb2]);

                    // $rel_between_pri_key_and_unique_key1 = (in_array($k1, $pris[$tb1]) ||  in_array($k1, $unis[$tb1]));
                    // $rel_between_pri_key_and_unique_key2 = (in_array($k2, $pris[$tb2]) ||  in_array($k2, $unis[$tb2]));

                    if ($k1_in_pris_tb1 && $k2_in_unis_tb2){
                        $meet[$ix] = true;
                        continue;
                    }

                    if ($k1_in_unis_tb1 && $k2_in_pris_tb2){
                        $meet[$ix] = true;
                        continue;
                    }

                } // end foreach
    
                /*
                    Elimino relaciones que no cumplen
                */
                foreach (array_keys($rs) as $rsk){
                    if (!$meet[$rsk]){
                        unset($rs[$rsk]);
                    }
                }
               
                return $rs;
            case '1:n':
            case 'n:1':    
            
                /*
                    Si es de tipo 1:1 no puede ser de tipo 1:n
                */
                $rel_1_1 = get_rels($t1, $t2, '1:1', $tenant_id);
    
                if ($rel_1_1 === false || (is_array($rel_1_1) && !empty($rel_1_1))){
                    return false;
                } 
    
                $sc  = get_schema_name($t2, $tenant_id)::get();
        
                $rels = $sc['expanded_relationships'] ?? null;
                if ($rels === null){
                    throw new \Exception("Unexpected error. There are not relationships");
                }

                $rs = $rels[$t1] ?? null;
                if (empty($rs)){
                    return false;
                }

                // aún no se si son 1:n o n:1
        
                $_rels = $rs;
                break;

            case 'n:m':
                $pivot = get_pivot([
                    $t1, $t2
                ], $tenant_id);
    
                // por concistencia
                if ($pivot === null){
                    $pivot = [];
                }
    
                return $pivot;
        }
        
        // Ahora diferencio entre 1:n y n:1
    
        $sc   = get_schema($t1, $tenant_id);
        $fks  = $sc['fks'] ?? false;

        if ($fks === false){
            throw new \Exception("Schema file for $t1 is outdated. Please re-generate all.");
        }

        if (empty($relation_str)){
            $meet_n_1 = [];
            // relaciones entre dos tablas (puede haber varias)
            foreach ($_rels as $ix => $rel){
                #dd($rel, "REL $ix");

                $key = $rel[0][1];
                #dd($key, 'KEY');
    
                if (in_array($key, $fks)){
                    $meet_n_1[] = $ix;
                }
            }
    
            // Llegado a este punto, solo puede ser 1:n o n:1 en cada relación

            $rel_is = null;

            // Si hay una sola relación,....
            if (count($meet_n_1) == count($_rels)){
                // Como en todas las relaciones entre las dos tablas (concistentemente)
                // encuentro que la FK está del lado izquierdo de la realción, concluyo es N:1
                // en cualquier caso.

                $rel_is = 'n:1';
            } else {
                //  la única otra posibilidad
                $rel_is = '1:n';
            }   

            if ($type == $rel_is){
                return $_rels;
            } else {
                return false;
            }
        } else {
            // Se especifica la relación en particular entre dos tablas. 
            // Útil en el escenario donde hay varias relaciones entre las dos tablas (debo verificar no sean cíclica

            $_r = explode('=', $relation_str);
            $relation_str_inv = "{$_r[1]}={$_r[0]}";
    
            // reune las condiciones para n:1 ? sino será 1:n
            $meet = false;
            foreach ($_rels as $ix => $r){
                $tr1 = $r[0][0];
                $tr2 = $r[1][0];
    
                $k1 = $r[0][1];
                $k2 = $r[1][1];
    
                $current_rel = "$tr1.$k1=$tr2.$k2";
    
                if ($current_rel == $relation_str || $current_rel == $relation_str_inv){
                    if (in_array($k1, $fks)){
                        $meet = $ix;
                        break;
                    }
                }
            }
    
            if ($meet !== false){
                $rel_is = 'n:1';
            } else {
                $rel_is = '1:n';
            }   

            if ($type == $rel_is){
                return $_rels[$ix];
            } else {
                return false;
            }
        }

    } finally {
        // Restore previous connection
        if (!empty($current_id_conn)){
            DB::setConnection($current_id_conn);
        }
    }    
}

/*
    Returns FK(s) on $t1 pointing to $t2

    If there is more than one relationship between tables, then can be more than one FK.
*/
function get_fks(string $t1, string $t2, ?string $tenant_id = null){    
    $sc  = get_schema($t1, $tenant_id);
    $fks = $sc['fks'];

    if (empty($fks)){
        return [];
    }

    if (!isset($sc['expanded_relationships_from'][$t2])){
        throw new \Exception("There is no relationship from '$t1' to '$t2'");
    }

    $rels = $sc['expanded_relationships_from'][$t2];
    $fks_t2 = array_column(array_column($rels, 1),1); 

    // d($rels, 'RELS');
    // d($fks_t2, 'FKs');

    return $fks_t2;
}

function tb_prefix(){
    return config()['tb_prefix'] ?? null;
}

function sql_formatter(string $sql, ...$options){
    return MyModel::sqlFormatter($sql, ...$options);
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

        StdOut::pprint($sentence, 'SENTENCE');

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