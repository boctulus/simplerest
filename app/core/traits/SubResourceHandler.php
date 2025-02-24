<?php

declare(strict_types=1);

namespace simplerest\core\traits;

use simplerest\core\libs\DB;
use simplerest\core\Model;
use simplerest\core\libs\Strings;

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
        
        // Detectar si estamos trabajando con la relación courses-users
        $specialCase = ($table == 'courses' && $tb == 'users');
        
        // Caso 1: Relación directa (profesor)
        if (isset($rels[$tb])) {
            $rs = $rels[$tb];
            $cnt = count($rs);
            
            // Manejamos primero las relaciones directas (1:1, 1:n, n:1)
            if ($cnt >= 1) {
                foreach ($rs as $rix => $relation) {
                    $sourceField = $relation[1][1]; // ej: professor_id
                    
                    // Determinamos un alias descriptivo para la relación
                    $descriptiveAlias = $tb;
                    
                    // Si es una FK con formato específico, extraemos el nombre
                    if (preg_match('/^(.+)_id$/', $sourceField, $matches)) {
                        $descriptiveAlias = $matches[1]; // ej: professor
                    }
                    
                    // Solo para relación courses-users, forzamos el alias "professor"
                    if ($specialCase && $sourceField == 'professor_id') {
                        $descriptiveAlias = 'professor';
                    }
                    
                    $m = DB::table($tb, "__$descriptiveAlias");

                    $arr = [];
                    foreach ($_fields as $f) {
                        $arr[] = "'$f', __$descriptiveAlias.{$f}";
                    }

                    $obj = implode(',' . PHP_EOL, $arr);

                    // Verificamos si es una relación múltiple
                    if (is_mul_rel_cached($table, $tb, null, $tenant_id)) {
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
                    
                    // Añadimos condición específica si es la relación con profesor
                    if ($specialCase && $descriptiveAlias == 'professor') {
                        $m->join($table, "__$descriptiveAlias.id", '=', "$table.$sourceField");
                    } else {
                        $m->join($table);
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
        }
        
        // Caso 2: Relaciones N:M (estudiantes)
        $pivot = get_pivot([$table, $tb]);
        
        if (!empty($pivot)) {
            $bridge = $pivot['bridge'];
            $fks = $pivot['fks'];
            
            // Para el caso específico de cursos-usuarios a través de course_student
            if ($specialCase && $bridge == 'course_student') {
                $descriptiveAlias = 'students';
                
                $m = DB::table($tb, "__$descriptiveAlias");
                
                $arr = [];
                foreach ($_fields as $f) {
                    $arr[] = "'$f', __$descriptiveAlias.{$f}";
                }
                
                $obj = implode(',' . PHP_EOL, $arr);
                
                // Siempre es una relación múltiple
                $sel = "
                    IF(
                        COUNT(__$descriptiveAlias.$pri) = 0, JSON_ARRAY(),
                        {$fn_json_arrayagg(
                    'JSON_OBJECT(' .$obj . ')'
                )}
                    )";
                
                $m->selectRaw($sel);
                
                // Creamos los joins necesarios para obtener estudiantes
                $m->join($bridge, "__$descriptiveAlias.id", '=', "$bridge.user_id")
                  ->join($table, "$table.id", '=', "$bridge.course_id")
                  ->where(["__$descriptiveAlias.role", 'student']);
                
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
            // Para otras relaciones N:M generales
            else if ($cnt == 0) {
                $descriptiveAlias = Strings::pluralize($tb);
                
                $m = DB::table($tb, "__$descriptiveAlias");
                
                $arr = [];
                foreach ($_fields as $f) {
                    $arr[] = "'$f', __$descriptiveAlias.{$f}";
                }
                
                $obj = implode(',' . PHP_EOL, $arr);
                
                // Siempre es una relación múltiple
                $sel = "
                    IF(
                        COUNT(__$descriptiveAlias.$pri) = 0, JSON_ARRAY(),
                        {$fn_json_arrayagg(
                    'JSON_OBJECT(' .$obj . ')'
                )}
                    )";
                
                $m->selectRaw($sel);
                
                // Joins genéricos para N:M
                $m->join($bridge, "__$descriptiveAlias.id", '=', "$bridge.{$fks[$tb]}")
                  ->join($table, "$table.id", '=', "$bridge.{$fks[$table]}");
                
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
