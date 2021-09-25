<?php

namespace simplerest\libs;

use simplerest\core\Model;
use simplerest\libs\DB;
use simplerest\libs\Factory;

class Classes
{
    /*
        https://stackoverflow.com/questions/7153000/get-class-name-from-file/44654073
    */
    static function getClassNameFromFile(string $full_path){
        $fp = fopen($full_path, 'r');
        $class_name = $buffer = '';
        $i = 0;
        while (!$class_name) {
            if (feof($fp)) break;
    
            $buffer .= fread($fp, 512);
            $tokens = token_get_all($buffer);
    
            if (strpos($buffer, '{') === false) continue;
    
            for (;$i<count($tokens);$i++) {
                if ($tokens[$i][0] === T_CLASS) {
                    for ($j=$i+1;$j<count($tokens);$j++) {
                        if ($tokens[$j] === '{') {
                            $class_name = $tokens[$i+2][1];
                        }
                    }
                }
            }
        }

        return $class_name;
    }

}

