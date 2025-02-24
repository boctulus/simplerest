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

            https://stackoverflow.com/questions/48843188/mysql-json-object-instead-of-group-concat/48844772
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

                    // untested
                case 'sqlite':
                    $sql = "CONCAT( ' [' ,GROUP_CONCAT($q),']')";
                    break;

                    // untested
                case 'pgsql':
                    // https://stackoverflow.com/questions/2560946/postgresql-group-concat-equivalent/8803563
                    // https://stackoverflow.com/questions/6162324/is-there-a-mysql-equivalent-to-postgresql-array-to-string

                    if (DB::driverVersion(true) < '8.4') {
                        throw new \Exception("Unsupported Postgresql version");
                    } else {
                        $sql = "array_to_string(array_agg($q), ',')";
                    }

                    break;

                    // untested    
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
            $rs  = $rels[$tb] ?? [];
            $cnt = count($rs);

            $alias   = null;
            switch ($cnt) {
                    // relación n:m
                case 0:
                    // En una relación con tabla puente no hay referencia directa desde la tabla principal
                    // hacia la tabla objetivo justamente porque existe una tabla intermedia.


                case 1:
                    $m = DB::table($tb, '__' . $tb);

                    $arr = [];
                    foreach ($_fields as $f) {
                        $arr[] = "'$f', __$tb.{$f}";
                    }

                    $obj = implode(',' . PHP_EOL, $arr);

                    if (is_mul_rel_cached($table, $tb, null, $tenant_id)) {
                        $sel = "
                            IF(
                                COUNT(__$tb.$pri) = 0, JSON_ARRAY(),
                                {$fn_json_arrayagg(
                            'JSON_OBJECT(' .$obj . ')'
                        )}
                            )";
                    } else {
                        $sel = "
                        IF(
                            COUNT(__$tb.$pri) = 0, '',
                            JSON_OBJECT($obj) )";
                    }

                    $m->selectRaw($sel);

                    $encoded[] = "$tb";

                    $m->join($table);

                    $sql = $m
                        ->dontBind()
                        ->dontExec()
                        ->dd();

                    $sql = "($sql) as $tb";


                    // INNER JOIN to WHERE conversion
                    $sql = $fn_where_x_join($sql, $table);

                    /////////////////////////////
                    //dd(Model::query($sql), $tb);
                    //dd(sql_formatter($sql), "SubQuery for $tb");
                    // dd('-------------------------------------'. PHP_EOL . PHP_EOL);
                    // dd($sql, "Pre-compiled SubQuery for $tb"); //
                    // print_r(PHP_EOL . PHP_EOL);


                    $subqueries[] = $sql;
                    break;

                    // $cnt > 1
                default:
                    /*
                        Caso donde hay más de una relación entre dos tablas. 
                        
                        Ej: cuando hay un usuario creador y otro actualizador (dos FKs hacia la misma tabla)
                    */

                    // De acá la idea es quedarme con los JOINS 
                    //
                    $m = DB::table($table)
                        ->join($tb);

                    $sql = $m
                        ->dontBind()
                        ->dontExec()
                        ->dd();

                    $ini = strpos($sql, 'INNER JOIN');
                    $end = strpos($sql, 'WHERE ', $ini + 7);

                    $inners = trim(Strings::middle($sql, $ini, $end));
                    $in_arr = explode('INNER JOIN ', $inners);

                    $aliases = [];
                    $ons = [];
                    foreach ($in_arr as $ix => $inner) {
                        if (empty($inner)) {
                            continue;
                        }

                        if (!preg_match('/[a-zA-Z0-9_]+ as ([a-zA-Z0-9_]+) ON (.*)/', $inner, $matches)) {
                            // dd($sql, 'SQL para JOINs');
                            // dd("Trying parse $inner");
                            throw new \Exception("SQL Error. Something was wrong");
                        }

                        $aliases[] = $matches[1];
                        $ons[] = $matches[2];
                    }

                    // dd($ons, 'ONs');
                    // dd($aliases, 'ALIASes');

                    // Acá debería iterar los JOINs....... 
                    foreach ($ons as $ix => $cond) {
                        // Obtenemos el alias descriptivo basado en la FK o tabla pivot
                        $raw_alias = $aliases[$ix];

                        // Extraemos el nombre descriptivo de la FK o tabla pivot
                        if (Strings::contains('__fk_', $raw_alias)) {
                            // Para FKs directas, extraemos el nombre del campo (ej: professor_id -> professor)
                            $descriptive_alias = Strings::before($raw_alias, '_id');
                        } elseif (Strings::contains('__pivot_', $raw_alias)) {
                            // Para relaciones N:M, usamos el nombre en singular (ej: course_students -> student)
                            $pivot_table = Strings::after($raw_alias, '__pivot_');
                            $descriptive_alias = Strings::singular(Strings::after($pivot_table, '_'));
                        } else {
                            $descriptive_alias = $raw_alias;
                        }

                        $encoded[] = $descriptive_alias;

                        $m = DB::table($tb, $descriptive_alias);

                        $arr = [];
                        foreach ($_fields as $f) {
                            $arr[] = "'$f', {$descriptive_alias}.{$f}";
                        }

                        $obj = implode(',' . PHP_EOL, $arr);

                        // La lógica de agregación se mantiene igual
                        if (is_mul_rel_cached($table, $tb, null, $tenant_id)) {
                            $sel = "
            IF(
                COUNT($descriptive_alias.$pri) = 0, JSON_ARRAY(),
                {$fn_json_arrayagg(
                                'JSON_OBJECT(' .$obj . ')'
                            )}
            )";
                        } else {
                            $sel = "
            IF(
                COUNT($descriptive_alias.$pri) = 0, '',
                JSON_OBJECT($obj) )";
                        }

                        $m->selectRaw($sel);
                        $m->whereRaw($cond);

                        $sql = $m
                            ->dontBind()
                            ->dontExec()
                            ->dd();

                        $sql = "($sql) as $descriptive_alias";

                        $subqueries[] = $sql;
                    }
                    break;
            }  // end switch

        }


        //exit;  //////////

        /*
            Query assembly
        */

        $sub_qs = implode(',' . PHP_EOL . PHP_EOL, $subqueries);
        //dd($sub_qs);

        //exit; /////
        ///////////


        /*
            Main query
        */

        $sql = $instance
            ->select($fields)
            ->selectRaw($sub_qs)
            ->dd();

        //dd($instance->dd(true), 'SQL'); exit; /// *

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

        //dd(sql_formatter($sql)); exit;//
        return $rows;
    }
}
