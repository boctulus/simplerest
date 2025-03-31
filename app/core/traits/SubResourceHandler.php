<?php

declare(strict_types=1);

namespace Boctulus\Simplerest\Core\traits;

use Doctrine\Inflector\InflectorFactory;
use Boctulus\Simplerest\Core\Libs\DB;
use Boctulus\Simplerest\Core\Libs\Schema;
use Boctulus\Simplerest\Core\Libs\Strings;
use Boctulus\Simplerest\Core\Model;

/*
    SimpleRest

    @author Bozzolo Pablo
*/

trait SubResourceHandler
{
    public $exec = true;

    function getSubResources(string $table, array $connect_to, ?Object &$instance = null, ?string $tenant_id = null)
    {
        static $ret;

        if ($tenant_id != null && $this->exec) {
            DB::getConnection($tenant_id);
        }

        $connect_to_key = implode(':', $connect_to);
        $cache_key      = "{$table}|{$connect_to_key}|{$tenant_id}";

        if ($ret !== null && isset($ret[$cache_key])) {
            return $ret[$cache_key];
        }

        if ($instance == null) {
            $instance = DB::table($table, null, $this->exec);
        }

        $fields = $instance->getNotHidden();
        $sc   = get_schema($table);
        $rels = $sc['expanded_relationships'];

        $fn_where_x_join = function ($sql, $table) {
            $pos = strpos($sql, 'WHERE');
            if ($pos !== false) {
                $sql = substr($sql, 0, $pos) . 'AND' . substr($sql, $pos + 5);
            }
            $sql = str_replace("INNER JOIN $table ON ", "WHERE ", $sql);
            return $sql;
        };

        $fn_json_arrayagg = function ($q) {
            switch (DB::driver()) {
                case 'mysql':
                    if (DB::isMariaDB() || DB::driverVersion(true) < '5.7.22') {
                        $sql = "CONCAT( ' [' ,GROUP_CONCAT($q),']')";
                    } else {
                        $sql = "JSON_ARRAYAGG($q)";
                    }
                    break;
                case 'sqlite':
                    $sql = "CONCAT( ' [' ,GROUP_CONCAT($q),']')";
                    break;
                case 'pgsql':
                    if (DB::driverVersion(true) < '8.4') {
                        throw new \Exception("Unsupported Postgresql version");
                    } else {
                        $sql = "array_to_string(array_agg($q), ',')";
                    }
                    break;
                case 'oracle':
                    $sql = "JSON_ARRAYAGG($q)";
                    break;
                default:
                    $sql = "JSON_ARRAYAGG($q)";
            }
            return $sql;
        };

        $subqueries = [];
        $encoded    = [];

        foreach ($connect_to as $tb) {
            $_fields = DB::table($tb)->getNotHidden();
            $pri = get_primary_key($tb);

            $allRelationPaths = [];
            if (isset($rels[$tb])) {
                $rs = $rels[$tb];
                foreach ($rs as $relation) {
                    $sourceField = $relation[1][1];
                    $targetTable = $relation[0][0];
                    $descriptiveAlias = $tb;
                    if (preg_match('/^(.+)_id$/', $sourceField, $matches)) {
                        $descriptiveAlias = $matches[1];
                    }
                    $allRelationPaths[] = [
                        'type' => 'direct',
                        'sourceField' => $sourceField,
                        'targetTable' => $targetTable,
                        'alias' => $descriptiveAlias,
                        'relation' => $relation
                    ];
                }
            }

            $pivot = get_pivot([$table, $tb]);
            if (!empty($pivot)) {
                $bridge = $pivot['bridge'];
                $fks = $pivot['fks'];
                $inflector = InflectorFactory::create()->build();
                $relationshipName = $inflector->pluralize($tb);
                $allRelationPaths[] = [
                    'type' => 'pivot',
                    'bridge' => $bridge,
                    'fks' => $fks,
                    'alias' => $relationshipName
                ];
            }

            foreach ($allRelationPaths as $path) {
                $descriptiveAlias = $path['alias'];
                $instance->subquery_aliases[$tb] = "__$descriptiveAlias";

                $m = DB::table($tb, "__$descriptiveAlias", $this->exec);

                $arr = [];
                foreach ($_fields as $f) {
                    $arr[] = "'$f', __$descriptiveAlias.{$f}";
                }
                $obj = implode(',' . PHP_EOL, $arr);

                $isMultiple = ($path['type'] === 'pivot') || is_mul_rel_cached($table, $tb, null, $tenant_id);
                if ($isMultiple) {
                    $sel = "IF(COUNT(__$descriptiveAlias.$pri) = 0, JSON_ARRAY(), {$fn_json_arrayagg('JSON_OBJECT(' .$obj . ')')})";
                } else {
                    $sel = "IF(COUNT(__$descriptiveAlias.$pri) = 0, '', JSON_OBJECT($obj))";
                }

                $m->selectRaw($sel);

                if ($path['type'] === 'direct') {
                    $sourceField = $path['sourceField'];
                    $m->join($table, "__$descriptiveAlias.id", '=', "$table.$sourceField");
                } else if ($path['type'] === 'pivot') {
                    $bridge = $path['bridge'];
                    $fks = $path['fks'];
                    $m->join($bridge, "__$descriptiveAlias.id", '=', "$bridge.{$fks[$tb]}")
                        ->join($table, "$table.id", '=', "$bridge.{$fks[$table]}");
                }

                // Aplicar condiciones específicas de la subconsulta
                if (!empty($instance->subquery_conditions[$tb])) {
                    foreach ($instance->subquery_conditions[$tb] as $condition) {
                        $m->where(["__$descriptiveAlias.{$condition[0]}", $condition[1], $condition[2]]);
                    }
                }

                $sql = $m->dontBind()->dontExec()->dd();
                $sql = "($sql) as $descriptiveAlias";
                $sql = $fn_where_x_join($sql, $table);

                $subqueries[] = $sql;
                $encoded[] = $descriptiveAlias;
            }
        }

        $sub_qs = implode(',' . PHP_EOL . PHP_EOL, $subqueries);
        $sql = $instance->select($fields)->selectRaw($sub_qs)->dd();
        

        if ($this->exec){
            $rows = Model::query($sql);
        } else {
            return $sql;
        }

        foreach ($rows as $ix => $row) {
            foreach ($row as $field => $dato) {
                if (in_array($field, $encoded)) {
                    $rows[$ix][$field] = json_decode($dato, true);
                }
            }
        }

        $ret[$cache_key] = $rows;
        return $rows;
    }    

     /*
        Construir las dependencias para insercion

        Algoritmo seguro:

        1.= Crear lista de todas las FKs (tabla) origen y (tabla) destino. Ej:

            [
                'fk'  => 'product_id',
                'ori' => 'order_items',
                'dst' => 'products'
            ],
            // ..

        2.= Colocar tablas sin FKs primero y el resto despues

        3.- Recorrer una sola vez el array que "lista de todas las FKs origen y destino"

        e ir haciendo movimientos posicionales dentro del array a medida que se procesa la lista.


        Nota:

        Es preferible usar expanded_relationships en vez de relationships por eficiencia dentro de schema        
    */

    /**
     * Determines the correct order for inserting data into tables based on foreign key dependencies.
     *
     * @param array $tables Array of table names to order.
     * @param ?string $tenant_id Optional tenant identifier for database connection.
     * @return array Ordered array of table names.
     * @throws \Exception If a cyclic dependency is detected.
     */
    function getInsertionOrder(array $tables, ?string $tenant_id = null): array {
        // Handle empty input
        if (empty($tables)) {
            return [];
        }

        // Select database connection based on tenant_id
        if ($tenant_id !== null) {
            DB::getConnection($tenant_id);
        }

        // Fetch all foreign key relationships
        $all_rels = Schema::getRelations(null, false, $tenant_id);

        // Initialize dependencies: table => [tables it depends on]
        $dependencies = [];
        foreach ($tables as $table) {
            $dependencies[$table] = [];
        }

        // Build dependency graph
        foreach ($all_rels as $to_tb => $rels) {
            foreach ($rels as $rel) {
                $from_tb = explode('.', $rel['from'])[0]; // Table with the foreign key
                $to_tb = explode('.', $rel['to'])[0];     // Referenced table
                // Include only relationships between tables in $tables, ignore self-references
                if (in_array($from_tb, $tables) && in_array($to_tb, $tables) && $from_tb !== $to_tb) {
                    $dependencies[$from_tb][] = $to_tb;
                }
            }
        }

        // Remove duplicates from dependency lists
        foreach ($dependencies as $tb => $deps) {
            $dependencies[$tb] = array_unique($deps);
        }

        // Build dependents: table => [tables that depend on it]
        $dependents = [];
        foreach ($dependencies as $tb => $deps) {
            foreach ($deps as $dep) {
                if (!isset($dependents[$dep])) {
                    $dependents[$dep] = [];
                }
                $dependents[$dep][] = $tb;
            }
        }

        // Initialize incoming dependency counts
        $incoming = [];
        foreach ($tables as $table) {
            $incoming[$table] = count($dependencies[$table]);
        }

        // Queue tables with no dependencies
        $queue = [];
        foreach ($tables as $table) {
            if ($incoming[$table] === 0) {
                $queue[] = $table;
            }
        }

        $order = [];
        // Process the queue
        while (!empty($queue)) {
            $table = array_shift($queue);
            $order[] = $table;

            // Update dependents
            if (isset($dependents[$table])) {
                foreach ($dependents[$table] as $dependent) {
                    $incoming[$dependent]--;
                    if ($incoming[$dependent] === 0) {
                        $queue[] = $dependent;
                    }
                }
            }
        }

        // Check for cycles
        foreach ($incoming as $tb => $count) {
            if ($count > 0) {
                throw new \Exception("Cyclic dependency detected involving table '$tb'");
            }
        }

        return $order;
    }
}
