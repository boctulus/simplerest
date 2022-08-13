<?php declare(strict_types=1);

namespace simplerest\core\libs;

class Arrays 
{
    /*
        Trim every element of array
    */
    static function trimArray(array $arr){
        return array_map('trim', $arr);
    }

    static function rtrimArray(array $arr){
        return array_map('rtrim', $arr);
    }

    static function ltrimArray(array $arr){
        return array_map('rtrim', $arr);
    }
       
    /**
     * Gets the first key of an array
     *
     * @param array $array
     * @return mixed
     */
    static function arrayKeyFirst(array $arr) {
        foreach($arr as $key => $unused) {
            return $key;
        }
        return null;
    }

    static function arrayValueFirst(array $arr) {
        foreach($arr as $val) {
            return $val;
        }
        return null;
    }

    /**
     * shift
     *
     * @param  array  $arr
     * @param  string $key
     * @param  string $default_value
     *
     * @return mixed
     */
    static function shift(&$arr, $key, $default_value = NULL)
    {
        $out = $arr[$key] ?? $default_value;
        unset($arr[$key]);
        return $out;
    }

    static function shiftOrFail(&$arr, $key, string $error_msg)
    {
        if (!isset($arr[$key])){
            throw new \Exception(sprintf($error_msg, [$key]));
        }

        $out = $arr[$key];
        unset($arr[$key]);
        return $out;
    }

    /**
     * nonassoc
     * Associative to non associative array
     * 
     * @param  array $arr
     *
     * @return array
     */
    static function nonassoc(array $arr){
        $out = [];
        foreach ($arr as $key => $val) {
            $out[] = [$key, $val];
        }
        return $out;
    }
 
    static function is_assoc(array $arr)
    {
        foreach(array_keys($arr) as $key){
            if (!is_int($key)) return true;
	            return false; 
        }		
    }

    /**
     * A str_replace_array for PHP
     *
     * As described in http://php.net/str_replace this wouldnot make sense
     * However there are chances that we need it, so often !
     * See https://wiki.php.net/rfc/cyclic-replace
     *
     * @author Jitendra Adhikari | adhocore <jiten.adhikary@gmail.com>
     *
     * @param string $search  The search string
     * @param array  $replace The array to replace $search in cyclic order
     * @param string $subject The subject on which to search and replace
     *
     * @return string
     */
    static function str_replace_array($search, array $replace, $subject)
    {
        if (empty($subject)){
            return '';
        }

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

    static function shuffleAssoc($my_array)
	{
        $keys = array_keys($my_array);

        shuffle($keys);

        $new = [];
        foreach($keys as $key) {
            $new[$key] = $my_array[$key];
        }

        return $new;
    }

}

