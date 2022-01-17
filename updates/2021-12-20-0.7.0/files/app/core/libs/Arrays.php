<?php declare(strict_types=1);

namespace simplerest\core\libs;

class Arrays 
{
    
    static function array_key_first(array $arr) {
        foreach($arr as $key => $unused) {
            return $key;
        }
        return NULL;
    }

    
    static function shift(&$arr, $key, $default_value = NULL)
    {
        $out = $arr[$key] ?? $default_value;
        unset($arr[$key]);
        return $out;
    }

    
    static function nonassoc(array $arr){
        $out = [];
        foreach ($arr as $key => $val) {
            $out[] = [$key, $val];
        }
        return $out;
    }
 
    static function is_assoc(array $arr)
    {
        foreach(array_keys($arr) as $key)
		if (!is_int($key)) return TRUE;
	        return FALSE;
    }

    
    static function str_replace_array($search, array $replace, $subject)
    {
        if (0 === $tokenc = substr_count($subject, $search)) {
            return $subject;
        }
        $string  = '';
        if (count($replace) >= $tokenc) {
            $replace = array_slice($replace, 0, $tokenc);
            $tokenc += 1; 
        } else {
            $tokenc = count($replace) + 1;
        }
        foreach(explode($search, $subject, $tokenc) as $part) {
            $string .= $part.array_shift($replace);
        }
        return $string;
    }



}

