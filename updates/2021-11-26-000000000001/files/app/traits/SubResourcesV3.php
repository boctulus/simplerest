<?php

namespace simplerest\traits;

use simplerest\libs\Factory;
use simplerest\libs\DB;
use simplerest\core\Model;
use simplerest\libs\Arrays;

trait SubResourcesV3
{
    static function getSubResources(string $table, Array $connect_to, ?Object &$instance = null, ?string $tenant_id = null)
    {
        static $ret;

        $connect_str = implode(',', $connect_to);
        
        if (isset($ret[$table][$connect_str][$tenant_id])){
            return $ret[$table][$connect_str][$tenant_id];
        }

        if ($tenant_id != null){
            DB::getConnection($tenant_id);
        }

        if ($instance == null){
            $instance = DB::table($table);
        }     
        
        // Rows from instance (without sub-resources)
        $rows = $instance->get();
      
        $tb = $table;
        $fields = DB::table($tb)->getNotHidden();

        $sc   = get_schema($tb);
        $rels = $sc['expanded_relationships'];


        $subqueries = [];
        $encoded    = [];
        foreach ($connect_to as $ix => $tb){
            $_fields = DB::table($tb)->getNotHidden();

            $pri = get_primary_key($tb);
            $rs  = $rels[$tb] ?? [];
            $cnt = count($rs);
          
            $rel_type = get_rel_type($table, $tb, null, $tenant_id);
            $mul = is_mul_rel($table, $tb, null, $tenant_id);

            dd([
                'rel?' => $rel_type, 
                'mul?'=> $mul, "SPECs for $tb"
            ]);

            
            $m = DB::table($tb);

            dd($m->dd(), "SUB-query $tb");
        }

        exit;  //////////

   
        /*
            Main query
        */

        // $res = [];

        // foreach ($rows as $k => $row){
        //     $res[$k] = $row;

        //     if (empty($addons)){
        //         continue;
        //     }

        //     //dd($addons[$k], '$addons');

        //     foreach ($addons[$k] as $name => $addon){
        //         $res[$k][$name] = $addon;
        //     } 
        // }
    }
}