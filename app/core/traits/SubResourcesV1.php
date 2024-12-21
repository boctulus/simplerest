<?php declare(strict_types=1);

namespace simplerest\core\traits;;

use simplerest\core\libs\Factory;
use simplerest\core\libs\DB;
use simplerest\core\Model;
use simplerest\core\libs\Arrays;

trait SubResourcesV1
{
    static function getSubResources(string $table, Array $connect_to, ?Object &$instance = null, ?string $tenant_id = null)
    {
        static $ret;

        if ($tenant_id != null){
            DB::getConnection($tenant_id);
        }        

        // Genero una clave única usando el nombre de la tabla y un hash de los subrecursos
        $connect_to_key = implode(':', $connect_to);
        $cache_key      = "{$table}|{$connect_to_key}|{$tenant_id}";
    
        if ($ret !== null && isset($ret[$cache_key])){
            return $ret[$cache_key];
        }
        
        $tenantid = Factory::request()->getTenantId();
        if ($tenantid !== null){
            DB::setConnection($tenantid);
        }  

        $rows = $instance->get();

        $addons = [];
        if (!empty($connect_to)){
            $d2m = false;

            /*
                Detalle a maestro
            */

            static $rx = [];

            $schema  = $instance->getSchema();
            $id_name = $schema['id_name'];

            // Relaciones de la tabla padre
            $rs = $schema['relationships'];
            //dd($rs, 'RS');

            foreach ($rows as $k => $row){
                $_id = $rows[$k][$id_name];  
                
                foreach ($connect_to as $tb){
                    if (!isset($rx[$table])){
                        $schema = get_schema($tb); 
                        $rx = $rs[$table] ?? null;

                        //dd($rx, 'RX');
                    } 
                
                    if ($rx === null){
                        continue;
                    }

                    foreach($rx as $r){
                        list($tb, $field) = explode('.', $r[1]);

                        // Puede haber más de una relación entre dos tablas
                        $tb_alias = explode('|', $tb);

                        $alias = $tb;
                        if (count($tb_alias) == 2){
                            $tb0   = $tb_alias[0];
                            $alias = $tb_alias[1];
                        } 

                        if (isset($addons[$k][$alias])){
                            $addons[$k][$alias] = DB::table($tb)->where([$field => $_id])->get();
                        } else {
                            $addons[$k][$alias] = [];
                        }
                        
                    }
                }
           
                

                /*
                    Maestro a detalle 
                */

                foreach ($connect_to as $tb){                                
                    $rx = $rs[$tb] ?? null;

                    if ($rx === null){
                        continue;
                    }                         

                    foreach($rx as $r){
                        list($tb0, $field0) = explode('.', $r[0]);
                        list($tb1, $field1) = explode('.', $r[1]);

                        // Puede haber más de una relación entre dos tablas
                        $tb_alias = explode('|', $tb0);
                        
                        $alias = $tb0;
                        if (count($tb_alias) == 2){
                            $tb0   = $tb_alias[0];
                            $alias = $tb_alias[1];
                        } 

                        if (isset($rows[$k][$field1])){
                            $_id = $rows[$k][$field1];
                            $addons[$k][$alias] = DB::table($tb0)->where([$field0 => $_id])->first();
                        } else {
                            $addons[$k][$alias] = [];
                        }
                    }
                }
            }
            
        }
        
        $res = [];

        foreach ($rows as $k => $row){
            $res[$k] = $row;

            if (empty($addons)){
                continue;
            }

            //dd($addons[$k], '$addons');

            foreach ($addons[$k] as $name => $addon){
                $res[$k][$name] = $addon;
            } 
        }

        // if (!config()['include_enity_name']){
        //     $res = $res[$table];
        // }

        // Al final de todo el procesamiento, guardo en caché
        $ret[$cache_key] = $res;

        return $res;
    }
}