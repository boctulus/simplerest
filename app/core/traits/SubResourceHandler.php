<?php

declare(strict_types=1);

namespace simplerest\core\traits;

use Doctrine\Inflector\InflectorFactory;
use simplerest\core\libs\DB;
use simplerest\core\libs\Strings;
use simplerest\core\Model;

/*
    SimpleRest

    @author Bozzolo Pablo
*/

trait SubResourceHandler
{
    static function getSubResources(string $table, array $connect_to, ?Object &$instance = null, ?string $tenant_id = null)
{
    static $ret;

    if ($tenant_id != null) {
        DB::getConnection($tenant_id);
    }

    // Genero una clave única usando el nombre de la tabla y un hash de los subrecursos
    $connect_to_key = implode(':', $connect_to);
    $cache_key      = "{$table}|{$connect_to_key}|{$tenant_id}";

    if ($ret !== null && isset($ret[$cache_key])) {
        return $ret[$cache_key];
    }

    if ($instance == null) {
        $instance = DB::table($table);
    }

    $fields = [];

    $tb = $table;
    $fields = DB::table($tb)->getNotHidden();

    $sc   = get_schema($tb);
    $rels = $sc['expanded_relationships'];

    /*
        Reemplace the INNER JOIN to the main table for WHERE
    */
    $fn_where_x_join = function ($sql, $table) {
        $pos = strpos($sql, 'WHERE');

        if ($pos !== false) {
            $sql = substr($sql, 0, $pos) . 'AND' . substr($sql, $pos + 5);
        }

        $sql = str_replace("INNER JOIN $table ON ", "WHERE ", $sql);

        return $sql;
    };

    /*
        Apply JSON_ARRAYAGG() or equivalent
    */
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
    
    foreach ($connect_to as $ix => $tb) {
        $_fields = DB::table($tb)->getNotHidden();
        $pri = get_primary_key($tb);
        
        // Obtener todas las posibles relaciones entre las tablas
        $allRelationPaths = [];
        
        // 1. Buscar relaciones directas
        if (isset($rels[$tb])) {
            $rs = $rels[$tb];
            
            foreach ($rs as $rix => $relation) {
                $sourceField = $relation[1][1]; // ej: professor_id
                $targetTable = $relation[0][0]; // tabla relacionada
                
                // Derivar un alias descriptivo de la clave foránea
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
        
        // 2. Buscar relaciones N:M a través de tabla pivot
        $pivot = get_pivot([$table, $tb]);
        
        if (!empty($pivot)) {
            $bridge = $pivot['bridge'];
            $fks = $pivot['fks'];
            
            // Determinar si la tabla pivot incluye un rol o tipo específico
            $relationshipName = null;
            
            // Intentar derivar un nombre de relación significativo de la tabla pivot
            if (strpos($bridge, '_') !== false) {
                $pivotParts = explode('_', $bridge);
                
                // Si hay más de 2 partes (ej: 'course_student'), la parte después del primer '_' 
                // podría ser descriptiva del rol
                if (count($pivotParts) > 2) {
                    $possibleRole = $pivotParts[1]; // ej: 'student' de 'course_student'
                    if ($possibleRole !== $table && $possibleRole !== $tb) {
                        $inflector = InflectorFactory::create()->build();
                        $relationshipName = $inflector->pluralize($possibleRole);
                    }
                }
            }
            
            // Si no pudimos derivar un nombre significativo, usar el plural de la tabla
            if ($relationshipName === null) {
                $inflector = InflectorFactory::create()->build();
                $relationshipName = $inflector->pluralize($tb);
            }
            
            $allRelationPaths[] = [
                'type' => 'pivot',
                'bridge' => $bridge,
                'fks' => $fks,
                'alias' => $relationshipName
            ];
        }
        
        // Procesar cada camino de relación
        foreach ($allRelationPaths as $path) {
            $descriptiveAlias = $path['alias'];
            
            $m = DB::table($tb, "__$descriptiveAlias");
            
            $arr = [];
            foreach ($_fields as $f) {
                $arr[] = "'$f', __$descriptiveAlias.{$f}";
            }
            
            $obj = implode(',' . PHP_EOL, $arr);
            
            // Determinar si es una relación múltiple
            $isMultiple = ($path['type'] === 'pivot') || 
                          is_mul_rel_cached($table, $tb, null, $tenant_id);
            
            if ($isMultiple) {
                $sel = "
                    IF(
                        COUNT(__$descriptiveAlias.$pri) = 0, JSON_ARRAY(),
                        {$fn_json_arrayagg(
                    'JSON_OBJECT(' .$obj . ')'
                )}
                    )";
            } else {
                $sel = "
                IF(
                    COUNT(__$descriptiveAlias.$pri) = 0, '',
                    JSON_OBJECT($obj) )";
            }
            
            $m->selectRaw($sel);
            
            // Generar los JOINs según el tipo de relación
            if ($path['type'] === 'direct') {
                $sourceField = $path['sourceField'];
                $m->join($table, "__$descriptiveAlias.id", '=', "$table.$sourceField");
            } else if ($path['type'] === 'pivot') {
                $bridge = $path['bridge'];
                $fks = $path['fks'];
                
                $m->join($bridge, "__$descriptiveAlias.id", '=', "$bridge.{$fks[$tb]}")
                  ->join($table, "$table.id", '=', "$bridge.{$fks[$table]}");
            }
            
            $sql = $m
                ->dontBind()
                ->dontExec()
                ->dd();
            
            $sql = "($sql) as $descriptiveAlias";
            
            // INNER JOIN to WHERE conversion
            $sql = $fn_where_x_join($sql, $table);
            
            $subqueries[] = $sql;
            $encoded[] = $descriptiveAlias;
        }
    }

    /*
        Query assembly
    */
    $sub_qs = implode(',' . PHP_EOL . PHP_EOL, $subqueries);

    /*
        Main query
    */
    $sql = $instance
        ->select($fields)
        ->selectRaw($sub_qs)
        ->dd();

    $rows = Model::query($sql);

    /*
        JSON decoding
    */
    foreach ($rows as $ix => $row) {
        foreach ($row as $field => $dato) {
            if (in_array($field, $encoded)) {
                $rows[$ix][$field] = json_decode($dato, true);
            }
        }
    }

    // Al final de todo el procesamiento, guardo en caché
    $ret[$cache_key] = $rows;

    return $rows;
}
}
