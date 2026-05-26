<?php

use Boctulus\Simplerest\Core\Exceptions\SqlException;
use Boctulus\Simplerest\Core\Libs\Arrays;
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

function tb_prefix() {
    return DB::getTablePrefixForCurrent();
}

