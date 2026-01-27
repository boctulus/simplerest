<?php

namespace Boctulus\Simplerest\Core\Libs;

use Boctulus\Simplerest\Core\Libs\Strings;

class WP
{
    /*
        Realiza un get_option() sobre un WordPress
    */
    static function get_option($option_name, $conn_id = null)
    {
        if ($conn_id != null){
            DB::getConnection($conn_id);
        }

        $option_value = table('options')
        ->where(['option_name' => $option_name])
        ->value('option_value');
    
        return unserialize($option_value);
    }

    static function set_option($option_name, $value, $conn_id = null)
    {
        if ($conn_id != null){
            DB::getConnection($conn_id);
        }

        return table('options')
        ->where(['option_name' => $option_name])
        ->update(['option_value', serialize($value)]);
    }

}

