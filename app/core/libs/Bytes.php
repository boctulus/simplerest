<?php

namespace simplerest\core\libs;

use simplerest\core\Model;
use simplerest\core\libs\DB;
use simplerest\core\libs\Factory;

class Bytes
{
    /*  
        Ej:

        $value1 = "1024M";
        $result1 = Bytes::toBytes($value1);
        echo $result1 . "\r\n";  // Output: 1073741824
    */
    static function toBytes(string $value)
    {
        $unit = strtoupper(substr($value, -1));
        $number = (int) substr($value, 0, -1);
    
        switch ($unit) {
            case 'K':
                return $number * 1024;
            case 'M':
                return $number * 1024 * 1024;
            case 'G':
                return $number * 1024 * 1024 * 1024;
            default:
                return false;
        }
    }

    static function toSizeString(int $bytes)
    {
        $units = array('', 'K', 'M', 'G');
        $unitIndex = 0;
    
        while ($bytes >= 1024 && $unitIndex < count($units) - 1) {
            $bytes /= 1024;
            $unitIndex++;
        }
    
        return round($bytes, 2) . $units[$unitIndex];
    }
    
}

