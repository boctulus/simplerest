<?php declare(strict_types=1);

namespace Boctulus\Simplerest\Core\Libs;

use Boctulus\Simplerest\Core\Model;
use Boctulus\Simplerest\Core\Libs\DB;
use Boctulus\Simplerest\Core\Libs\Arrays;
use Boctulus\Simplerest\Core\Libs\Config;
use Boctulus\Simplerest\Core\Libs\StdOut;
use Boctulus\Simplerest\Core\Libs\Strings;
use Boctulus\Simplerest\Core\Exceptions\SqlException;

class DBRels
{
    // ---------------------------------------------------------------
    //  RELATIONS
    // ---------------------------------------------------------------

    /**
     * Implementa get_relations() de Helpers/db.php
     * @entryPoint
     */
    static function getRelations(?string $tenant_id = null, ?string $table = null)
    {
        static $rels;

        $def_conn_id = Config::get()['db_connection_default'];

        $key = ($tenant_id ?? $def_conn_id) . '.' . $table;

        if ($rels === null) {
            $rels = [];
        }

        if (isset($rels[$key])) {
            return $rels[$key];
        }

        if ($tenant_id == $def_conn_id) {
            $folder = $def_conn_id . '/';
        } else {
            if ($tenant_id === null) {
                $tenant_id = DB::getDefaultConnectionId();
                $folder = $tenant_id . '/';

                DB::getConnection($tenant_id);
            } else {
                $group = DB::getTenantGroupName($tenant_id);

                if ($group) {
                    $folder = $group . '/';
                } else {
                    $folder = '';
                }
            }
        }

        $path = SCHEMA_PATH . $folder . 'Relations.php';

        if (!file_exists($path)) {
            throw new \Exception("Please run \"php com make relation_scan --from:$tenant_id\" or re-build All schemas!");
        }

        $rels[$key] = include $path;

        return $rels[$key];
    }

    /**
     * Implementa get_rels() de Helpers/db.php
     * @entryPoint
     */
    static function getRels(string $t1, string $t2, string $type, ?string $tenant_id = null, string $relation_str = null)
    {
        $type = strtolower($type);

        if (!in_array($type, ['1:1', '1:n', 'n:1', 'n:m'])) {
            throw new \InvalidArgumentException("Type can be only '1:1', '1:n', 'n:1' or 'n:m'");
        }

        $current_id_conn = DB::getCurrentConnectionId();
        $conn = DB::getConnection($tenant_id);

        try {
            switch ($type) {
                case '1:1':
                    $pris = [];

                    $sc  = get_schema_name($t1, $tenant_id)::get();

                    if (!array_key_exists('autoincrement', $sc)) {
                        throw new \Exception("Schema file for $t1 is outdated. Please re-generate all.");
                    }

                    $rels  = $sc['expanded_relationships'] ?? null;
                    $uni1  = $sc['uniques'];
                    $pri1  = $sc['primary'];
                    $auto1 = $sc['autoincrement'];

                    if ($rels === null) {
                        throw new \Exception("Unexpected error. There are not relationships");
                    }
                    $rs = $rels[$t2] ?? null;
                    if (empty($rs)) {
                        return false;
                    }

                    $meet = [];

                    $sc2  = get_schema_name($t2, $tenant_id)::get();

                    $uni2  = $sc2['uniques'];
                    $pri2  = $sc2['primary'];
                    $auto2 = $sc2['autoincrement'];

                    $pris[$t1] = $pri1;
                    $pris[$t2] = $pri2;

                    $unis[$t1] = $uni1;
                    $unis[$t2] = $uni2;

                    foreach ($rs as $ix => $r) {
                        $tb1 = $r[0][0];
                        $k1  = $r[0][1];

                        $tb2 = $r[1][0];
                        $k2  = $r[1][1];

                        $meet[$ix] = false;
                        $rel_between_pri_keys = (in_array($k1, $pris[$tb1]) && in_array($k2, $pris[$tb2]));

                        if ($rel_between_pri_keys) {
                            $cnt_pris_tb1 = count($pris[$tb1]);
                            $cnt_pris_tb2 = count($pris[$tb2]);

                            if ($cnt_pris_tb1 == 1 && $cnt_pris_tb2 == 1) {
                                $meet[$ix] = true;
                                continue;
                            }
                        }

                        $k1_in_pris_tb1 = in_array($k1, $pris[$tb1]);
                        $k1_in_unis_tb1 = in_array($k1, $unis[$tb1]);
                        $k2_in_pris_tb2 = in_array($k2, $pris[$tb2]);
                        $k2_in_unis_tb2 = in_array($k2, $unis[$tb2]);

                        if ($k1_in_pris_tb1 && $k2_in_unis_tb2) {
                            $meet[$ix] = true;
                            continue;
                        }

                        if ($k1_in_unis_tb1 && $k2_in_pris_tb2) {
                            $meet[$ix] = true;
                            continue;
                        }
                    }

                    foreach (array_keys($rs) as $rsk) {
                        if (!$meet[$rsk]) {
                            unset($rs[$rsk]);
                        }
                    }

                    return $rs;

                case '1:n':
                case 'n:1':
                    $rel_1_1 = self::getRels($t1, $t2, '1:1', $tenant_id);

                    if ($rel_1_1 === false || (is_array($rel_1_1) && !empty($rel_1_1))) {
                        return false;
                    }

                    $sc  = get_schema_name($t2, $tenant_id)::get();

                    $rels = $sc['expanded_relationships'] ?? null;
                    if ($rels === null) {
                        throw new \Exception("Unexpected error. There are not relationships");
                    }

                    $rs = $rels[$t1] ?? null;
                    if (empty($rs)) {
                        return false;
                    }

                    $_rels = $rs;
                    break;

                case 'n:m':
                    $pivot = self::getPivot([
                        $t1, $t2
                    ], $tenant_id);

                    if ($pivot === null) {
                        $pivot = [];
                    }

                    return $pivot;
            }

            // Diferenciar 1:n vs n:1
            $sc   = get_schema($t1, $tenant_id);
            $fks  = $sc['fks'] ?? false;

            if ($fks === false) {
                throw new \Exception("Schema file for $t1 is outdated. Please re-generate all.");
            }

            if (empty($relation_str)) {
                $meet_n_1 = [];

                foreach ($_rels as $ix => $rel) {
                    $key = $rel[0][1];

                    if (in_array($key, $fks)) {
                        $meet_n_1[] = $ix;
                    }
                }

                $rel_is = null;

                if (count($meet_n_1) == count($_rels)) {
                    $rel_is = 'n:1';
                } else {
                    $rel_is = '1:n';
                }

                if ($type == $rel_is) {
                    return $_rels;
                } else {
                    return false;
                }
            } else {
                $_r = explode('=', $relation_str);
                $relation_str_inv = "{$_r[1]}={$_r[0]}";

                $meet = false;
                foreach ($_rels as $ix => $r) {
                    $tr1 = $r[0][0];
                    $tr2 = $r[1][0];

                    $k1 = $r[0][1];
                    $k2 = $r[1][1];

                    $current_rel = "$tr1.$k1=$tr2.$k2";

                    if ($current_rel == $relation_str || $current_rel == $relation_str_inv) {
                        if (in_array($k1, $fks)) {
                            $meet = $ix;
                            break;
                        }
                    }
                }

                if ($meet !== false) {
                    $rel_is = 'n:1';
                } else {
                    $rel_is = '1:n';
                }

                if ($type == $rel_is) {
                    return $_rels[$ix];
                } else {
                    return false;
                }
            }
        } finally {
            if (!empty($current_id_conn)) {
                DB::setConnection($current_id_conn);
            }
        }
    }

    /**
     * Implementa is_x_x() de Helpers/db.php
     */
    static function isRel(string $x_x, string $t1, string $t2, ?string $relation_str = null, ?string $tenant_id = null)
    {
        static $rel;

        if (!in_array($x_x, ['1:1', '1:n', 'n:1', 'n:m'])) {
            throw new \InvalidArgumentException("First parameter can only be ['1:1', '1:n', 'n:m']");
        }

        if (is_null($tenant_id)) {
            $tenant_id = DB::getCurrentConnectionId(true);
        }

        $key = "$tenant_id:$t1.{$t2}|" . (empty($relation_str) ? 'all' : $relation_str) . " -- for $x_x";

        if (isset($rel[$key])) {
            return $rel[$key];
        }

        $rls = self::getRels($t1, $t2, $x_x, $tenant_id, $relation_str);

        $rel[$key] = !empty($rls);

        return $rel[$key];
    }

    /**
     * Implementa is_1_1() de Helpers/db.php
     */
    static function isOneToOne(string $t1, string $t2, ?string $relation_str = null, ?string $tenant_id = null)
    {
        return self::isRel('1:1', $t1, $t2, $relation_str, $tenant_id);
    }

    /**
     * Implementa is_1_n() de Helpers/db.php
     */
    static function isOneToMany(string $t1, string $t2, ?string $relation_str = null, ?string $tenant_id = null)
    {
        return self::isRel('1:n', $t1, $t2, $relation_str, $tenant_id);
    }

    /**
     * Implementa is_n_1() de Helpers/db.php
     */
    static function isNToOne(string $t1, string $t2, ?string $relation_str = null, ?string $tenant_id = null)
    {
        return self::isRel('n:1', $t1, $t2, $relation_str, $tenant_id);
    }

    /**
     * Implementa is_n_m() de Helpers/db.php
     */
    static function isManyToMany(string $t1, string $t2, ?string $relation_str = null, ?string $tenant_id = null)
    {
        return self::isRel('n:m', $t1, $t2, $relation_str, $tenant_id);
    }

    /**
     * Implementa get_rel_type() de Helpers/db.php
     * @entryPoint
     */
    static function getRelType(string $t1, string $t2, ?string $relation_str = null, ?string $tenant_id = null)
    {
        static $rel;

        if (is_null($tenant_id)) {
            $tenant_id = DB::getCurrentConnectionId(true);
        }

        $key = "$tenant_id:$t1.{$t2}|$relation_str";

        if (isset($rel[$key])) {
            return $rel[$key];
        }

        if (self::isManyToMany($t1, $t2, $relation_str, $tenant_id)) {
            $rel[$key] = 'n:m';
            return $rel[$key];
        }

        if (self::isOneToMany($t1, $t2, $relation_str, $tenant_id)) {
            $rel[$key] = '1:n';
            return $rel[$key];
        }

        if (self::isNToOne($t1, $t2, $relation_str, $tenant_id)) {
            $rel[$key] = 'n:1';
            return $rel[$key];
        }

        if (self::isOneToOne($t1, $t2, $relation_str, $tenant_id)) {
            $rel[$key] = '1:1';
            return $rel[$key];
        }

        $rel[$key] = false;
        return $rel[$key];
    }

    /**
     * Implementa is_mul_rel() de Helpers/db.php
     */
    static function isMulRel(string $t1, string $t2, ?string $relation_str = null, ?string $tenant_id = null)
    {
        if (empty($tenant_id)) {
            $tenant_id = DB::getDefaultConnectionId();
        }

        $rel_type = self::getRelType($t1, $t2, $relation_str, $tenant_id);

        switch ($rel_type) {
            case '1:1':
                return false;
            case 'n:m':
                return true;
            case '1:n':
                return true;
            case 'n:1':
                return false;

            default:
                StdOut::print("[ Warning ] Unknow or ambiguous relationship for $tenant_id:$t1~$t2 !!!");
        }
    }

    /**
     * Implementa is_mul_rel_cached() de Helpers/db.php
     */
    static function isMulRelCached(string $t1, string $t2, ?string $relation_str = null, ?string $tenant_id = null)
    {
        if (!is_null($relation_str)) {
            return self::isMulRel($t1, $t2, $relation_str, $tenant_id);
        }

        $r = self::getRelations($tenant_id);

        if (!isset($r['multiplicity']["$t1~$t2"])) {
            throw new \Exception("Mutiplicity information is missing for '$t1~$t2' (or they are not related). Please run \"php com make relation_scan --from:$tenant_id\" or re-build all schemas");
        }

        return $r['multiplicity']["$t1~$t2"];
    }

    // ---------------------------------------------------------------
    //  PIVOT
    // ---------------------------------------------------------------

    /**
     * Implementa get_pivot() de Helpers/db.php
     * @entryPoint
     *
     * NOTA: Se corrigió new MakeCommand() → new MakePivotScanCommand()
     * porque MakeCommand ya no existe tras la refactorización del sistema de comandos.
     */
    static function getPivot(array $tables, ?string $tenant_id = null)
    {
        static $ret = [];

        if (is_null($tenant_id)) {
            $tenant_id = DB::getCurrentConnectionId(true);
        }

        sort($tables);

        $needle = implode(',', $tables);

        if (isset($ret[$tenant_id][$needle])) {
            return $ret[$tenant_id][$needle];
        }

        $dir = get_schema_path(null, $tenant_id);

        if (!file_exists($dir . 'Pivots.php')) {
            StdOut::hideResponse();

            require_once __DIR__ . '/../../../app/Commands/make/PivotScanCommand.php';
            $cmd = new \MakePivotScanCommand();

            if (!empty($tenant_id)) {
                $cmd->pivot_scan("--from:$tenant_id");
            } else {
                $cmd->pivot_scan();
            }

            StdOut::showResponse();
        }

        include $dir . 'Pivots.php';

        if (!isset($pivots[$needle])) {
            return;
        }

        if (!isset($ret[$tenant_id])) {
            $ret[$tenant_id] = [];
        }

        if (!isset($ret[$tenant_id])) {
            $ret[$tenant_id][$needle] = [];
        }

        $bridge = $pivots[$needle];

        $ret[$tenant_id][$needle] = [
            'bridge'       => $bridge,
            'fks'          => $pivot_fks[$bridge],
            'relationships' => $relationships[$pivots[$needle]],
        ];

        return $ret[$tenant_id][$needle];
    }

    /**
     * Implementa get_fks() de Helpers/db.php
     */
    static function getFKs(string $t1, string $t2, ?string $tenant_id = null)
    {
        $sc  = get_schema($t1, $tenant_id);
        $fks = $sc['fks'];

        if (empty($fks)) {
            return [];
        }

        if (!isset($sc['expanded_relationships_from'][$t2])) {
            throw new \Exception("There is no relationship from '$t1' to '$t2'");
        }

        $rels = $sc['expanded_relationships_from'][$t2];
        $fks_t2 = array_column(array_column($rels, 1), 1);

        return $fks_t2;
    }

    // ---------------------------------------------------------------
    //  SCHEMA / MODEL METADATA  (moved from Helpers/db.php)
    // ---------------------------------------------------------------

    static function getModelNamespace(?string $tenant_id = null): string
    {
        if ($tenant_id === null) {
            $tenant_id = DB::getCurrentConnectionId(true);
        }

        if ($tenant_id == Config::get()['db_connection_default']) {
            $extra = Config::get()['db_connection_default'] . '\\';
        } else {
            $group = DB::getTenantGroupName($tenant_id);
            $extra = $group ? ($group . '\\') : '';
        }

        return '\\' . Config::get()['namespace'] . '\\Models\\' . $extra;
    }

    static function getModelName(string $table_name, ?string $tenant_id = null): string
    {
        if ($tenant_id === null) {
            $tenant_id = DB::getCurrentConnectionId(true);
        }

        if ($tenant_id == Config::get()['db_connection_default']) {
            $extra = Config::get()['db_connection_default'] . '\\';
        } else {
            $group = DB::getTenantGroupName($tenant_id);
            $extra = $group ? ($group . '\\') : '';
        }

        return '\\' . Config::get()['namespace'] . '\\Models\\' . $extra . Strings::snakeToCamel($table_name) . 'Model';
    }

    static function getApiNamespace(string $resource_name): string
    {
        return '\\' . Config::get()['namespace'] . '\\Controllers\\API\\' . Strings::snakeToCamel($resource_name);
    }

    static function getApiName(string $resource_name, $api_ver = null): string
    {
        global $api_version;

        if ($api_ver !== null) {
            $api_version = 'v' . $api_ver;
        }

        if (!Strings::startsWith('\\' . Config::get('namespace') . '\\', $resource_name)) {
            return self::getApiNamespace($resource_name);
        }
        return $resource_name;
    }

    static function getUsersTable(): string
    {
        $users_table = Config::get()['users_table'] ?? null;

        if (empty($users_table)) {
            response()->error("users_table in config file is required", 500);
        }

        return $users_table;
    }

    static function getUserModelName(): string
    {
        static $model_name;

        $users_table = self::getUsersTable();
        $conn_id     = DB::getCurrentConnectionId(true);
        $key         = $conn_id . '.' . $users_table;

        if (isset($model_name[$key])) {
            return $model_name[$key];
        }

        $model_name[$key] = self::getModelName($users_table, $conn_id);
        return $model_name[$key];
    }

    static function getModelInstance(string $model_name, $fetch_mode = 'ASSOC', bool $reuse = false)
    {
        static $instance;

        if ($reuse && isset($instance[$model_name]) && !empty($instance[$model_name])) {
            return $instance[$model_name];
        }

        $normalized = ltrim($model_name, '\\');

        if (!Strings::startsWith(Config::get()['namespace'] . '\\', $normalized)) {
            $model = self::getModelNamespace() . $model_name;
        } else {
            $model = $model_name;
        }

        $instance[$model_name] = (new $model(true))->setFetchMode($fetch_mode);
        DB::setModelInstance($instance[$model_name]);

        return $instance[$model_name];
    }

    static function getModelInstanceByTable(string $table_name, $fetch_mode = 'ASSOC', bool $reuse = false)
    {
        return self::getModelInstance(self::getModelName($table_name), $fetch_mode, $reuse);
    }

    static function getSchemaPath(?string $table_name = null, ?string $tenant_id = null): string
    {
        static $schema_paths = [];

        if ($tenant_id === null) {
            $tenant_id = DB::getCurrentConnectionId(true);
        }

        $_table_name = $table_name ?? '__NULL__';

        if (isset($schema_paths[$tenant_id][$_table_name])) {
            return $schema_paths[$tenant_id][$_table_name];
        }

        if ($tenant_id == Config::get()['db_connection_default']) {
            $extra = Config::get()['db_connection_default'] . '/';
        } else {
            $group = DB::getTenantGroupName($tenant_id);
            $extra = $group ? ($group . '/') : '';
        }

        $path = SCHEMA_PATH . $extra;

        if ($table_name !== null) {
            $path .= Strings::snakeToCamel($table_name) . 'Schema.php';
        }

        $schema_paths[$tenant_id][$_table_name] = $path;
        return $path;
    }

    static function getSchemaName(string $table_name, ?string $tenant_id = null): string
    {
        if ($tenant_id === null) {
            $tenant_id = DB::getCurrentConnectionId();
        }

        $defcon = Config::get()['db_connection_default'];

        if ($tenant_id == $defcon) {
            $extra = $defcon . '\\';
        } else {
            $group = DB::getTenantGroupName($tenant_id);
            $extra = $group ? ($group . '\\') : '';
        }

        return '\\' . Config::get()['namespace'] . '\\Schemas\\' . $extra . Strings::snakeToCamel($table_name) . 'Schema';
    }

    static function hasSchema(string $table_name, ?string $tenant_id = null): bool
    {
        if ($tenant_id === null) {
            $tenant_id = DB::getCurrentConnectionId();

            if ($tenant_id === null) {
                DB::getDefaultConnection();
                $tenant_id = DB::getCurrentConnectionId();
            }
        }

        return class_exists(self::getSchemaName($table_name, $tenant_id));
    }

    static function getSchema(string $table_name, ?string $tenant_id = null)
    {
        static $sc;

        if ($tenant_id === null) {
            $tenant_id = DB::getCurrentConnectionId();

            if ($tenant_id === null) {
                DB::getDefaultConnection();
                $tenant_id = DB::getCurrentConnectionId();
            }
        }

        $key = "$tenant_id:$table_name";

        if (isset($sc[$key])) {
            return $sc[$key];
        }

        $class = self::getSchemaName($table_name, $tenant_id);

        if (!class_exists($class)) {
            throw new \Exception("Class $class does not exist");
        }

        $sc[$key] = $class::get();
        return $sc[$key];
    }

    static function inSchema(array $props, string $table_name, ?string $tenant_id = null): bool
    {
        $sc = self::getSchema($table_name, $tenant_id);
        $attributes = array_keys($sc['attr_types']);

        if (empty($props)) {
            throw new \InvalidArgumentException("Attributes not found!");
        }

        foreach ($props as $prop) {
            if (!in_array($prop, $attributes)) {
                return false;
            }
        }
        return true;
    }

    static function getPrimaryKey(string $table_name, ?string $tenant_id = null): string
    {
        static $keys = [];

        if ($tenant_id === null) {
            $tenant_id = DB::getCurrentConnectionId(true);
        }

        if (isset($keys[$tenant_id][$table_name])) {
            return $keys[$tenant_id][$table_name];
        }

        $sc = self::getSchema($table_name, $tenant_id);
        $keys[$tenant_id][$table_name] = $sc['id_name'];

        return $keys[$tenant_id][$table_name];
    }

    static function getModelDefs(string $table_name, ?string $tenant_id = null, bool $include_hidden = true, bool $include_related = true): array
    {
        if ($tenant_id !== null) {
            DB::getConnection($tenant_id);
        }

        $schema      = self::getSchemaName($table_name);
        $schema_defs = $schema::get();

        $defs       = [];
        $rel_tables = [];

        $rels = self::getRelations($tenant_id);

        if (!isset($rels['related_tables'])) {
            throw new \Exception("Please run \"php com make relation_scan --from:$tenant_id\" or re-build All schemas!");
        }

        $_related   = $rels['related_tables'];
        $rel_tables = $_related[$table_name] ?? [];

        Arrays::destroyIfExists($rel_tables, $table_name);

        $fields = $schema_defs['fields'];
        $rules  = $schema_defs['rules'];

        $instance = self::getModelInstanceByTable($table_name);

        $field_names = $instance->getFieldNames();
        $formatters  = $instance->getformatters();

        $field_order = $instance->getFieldOrder();

        if (!empty($field_order)) {
            $fields = Arrays::followOrder($fields, $field_order);
        }

        $hidden_ay   = $instance->getHidden();
        $fillable_ay = $instance->getFillables();
        $nullable_ay = $instance->getNullables();
        $uniques_ay  = $instance->getUniques();

        $schema    = $instance->getSchema();
        $fk_ay     = $schema['fks'];
        $rels_from = $schema['expanded_relationships_from'];

        foreach ($fields as $field) {
            if (!$include_hidden && in_array($field, $hidden_ay)) {
                continue;
            }

            $defs[$field]['hidden']   = in_array($field, $hidden_ay);
            $defs[$field]['fillable'] = in_array($field, $fillable_ay);
            $defs[$field]['nullable'] = in_array($field, $nullable_ay);
            $defs[$field]['unique']   = in_array($field, $uniques_ay);
            $defs[$field]['fk']       = in_array($field, $fk_ay);

            if ($defs[$field]['fk']) {
                foreach ($rels_from as $rels_fk) {
                    foreach ($rels_fk as $rel) {
                        if ($rel[1][1] == $field) {
                            $t2 = $rel[0][0];

                            $rel = self::getRelType($table_name, $t2);
                            if ($rel == 'n:1') {
                                $rel = '1:n';
                            }

                            $defs[$field]['fk'] = [
                                'table'    => $t2,
                                'mul'      => $rel,
                                'self_ref' => ($table_name == $t2)
                            ];
                        }
                    }
                }
            }

            if (isset($field_names[$field])) {
                $defs[$field]['name'] = $field_names[$field];
            } else {
                $defs[$field]['name'] = ucfirst(str_replace('_', ' ', $field));
            }

            if (isset($formatters[$field])) {
                $defs[$field]['formatter'] = $formatters[$field];
            }

            if (isset($rules[$field])) {
                if (!isset($defs[$field])) {
                    $defs[$field] = $rules[$field];
                } else {
                    $defs[$field] = array_merge($defs[$field], $rules[$field]);
                }
            }
        }

        return $defs;
    }

    static function getDefs(string $table_name, ?string $tenant_id = null, bool $include_hidden = true, bool $include_hidden_from_api = true): array
    {
        $tenant_id = $tenant_id ?? DB::getCurrentConnectionId();

        $defs = self::getModelDefs($table_name, $tenant_id, $include_hidden);

        $api        = self::getApiName($table_name, 1);
        $api_hidden = $api::getHidden();

        if ($include_hidden_from_api) {
            foreach ($api_hidden as $col) {
                if (array_key_exists($col, $defs)) {
                    $defs[$col]['hidden'] = true;
                }
            }
        }

        return $defs;
    }
}
