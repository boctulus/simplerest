<?php

use Boctulus\Simplerest\Core\Exceptions\SqlException;
use Boctulus\Simplerest\Core\Libs\Config;
use Boctulus\Simplerest\Core\Libs\DB;
use Boctulus\Simplerest\Core\Libs\DBRels;
use Boctulus\Simplerest\Core\Libs\StdOut;
use Boctulus\Simplerest\Core\Libs\Strings;
use Boctulus\Simplerest\Core\Model;

/*
    Helpers thin-proxies to DB::/DBRels:: class methods.
    Refactor in progress: prefer the class call at new callsites.
*/

function withDefaultConnection($callback, ...$params) {
    return DB::withDefaultConnection($callback, ...$params);
}

function migrate(bool $show_response = true, ...$args){
    DB::migrate($show_response, ...$args);
}

function get_model_defs(string $table_name, $tenant_id = null, bool $include_hidden = true, bool $include_related = true)
{
    return DBRels::getModelDefs($table_name, $tenant_id, $include_hidden, $include_related);
}

function get_defs(string $table_name, $tenant_id = null, bool $include_hidden = true, bool $include_hidden_from_api = true)
{
    return DBRels::getDefs($table_name, $tenant_id, $include_hidden, $include_hidden_from_api);
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
    return DBRels::getUsersTable();
}

function get_model_name($table_name, $tenant_id = null){
    return DBRels::getModelName($table_name, $tenant_id);
}

function get_user_model_name(){
    return DBRels::getUserModelName();
}

function get_schema_path($table_name = null, $tenant_id = null){
    return DBRels::getSchemaPath($table_name, $tenant_id);
}

function get_schema_name($table_name, $tenant_id = null){
    return DBRels::getSchemaName($table_name, $tenant_id);
}

function get_schema($table_name, $tenant_id = null){
    return DBRels::getSchema($table_name, $tenant_id);
}

function inSchema(array $props, string $table_name, ?string $tenant_id = null){
    return DBRels::inSchema($props, $table_name, $tenant_id);
}

function get_primary_key(string $table_name, $tenant_id = null){
    return DBRels::getPrimaryKey($table_name, $tenant_id);
}

// alias
function get_id_name($table_name, $tenant_id = null){
    return DBRels::getPrimaryKey($table_name, $tenant_id);
}

function get_rel_type(string $table_name, string $related_table_name, ?string $tenant_id = null){
    return DBRels::getRelType($table_name, $related_table_name, $tenant_id);
}

function is_mul_rel(string $table_name, string $related_table_name, ?string $tenant_id = null){
    return DBRels::isMulRel($table_name, $related_table_name, $tenant_id);
}

function get_pivot(array $tables, ?string $tenant_id = null){
    return DBRels::getPivot($tables, $tenant_id);
}

function get_default_connection(){
    return DB::getDefaultConnection();
}

function get_default_connection_id(){
    return DB::getDefaultConnectionId();
}

function tb_prefix() {
    return DB::getTablePrefixForCurrent();
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

    return '\\'.Config::get()['namespace'].'\\Models\\' . $extra;
}

function get_model_instance(string $model_name, $fetch_mode = 'ASSOC', bool $reuse = false){
    static $instance;

    if ($reuse && isset($instance[$model_name]) && !empty($instance[$model_name])){
        return $instance[$model_name];
    }

    if (!Strings::startsWith(Config::get()['namespace'] . '\\' , $model_name)){
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
    Miscelaneous helpers
*/

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