<?php

namespace simplerest\core\libs;

/*
    Pablo Bozzolo
    boctulus@gmail.com
*/

use simplerest\core\libs\Files;
use simplerest\core\libs\StdOut;
use simplerest\core\libs\Update;
use simplerest\core\libs\Strings;

class Obfuscator
{
    /*
        TODO:

        - Ofuscar también los JS !!!

        https://www.npmjs.com/package/javascript-obfuscator
        
    */
    static function obfuscate(string $ori, string $dst, $excluded, $options = null)
    {   
        $tmp  = sys_get_temp_dir();
        $_dst = "$tmp/to_obsfuscate";

        Files::mkDir  ($_dst);
        Files::delTree($_dst);
        Files::delTree($dst);
        Files::delTree("$tmp/obsfuscated");

        if (is_string($excluded)){
            if (Strings::contains(PHP_EOL, $excluded)){
                $excluded = explode(PHP_EOL, $excluded);
            }
        }

        $excluded[] = 'obf.yaml';
        $excluded[] = 'glob:*.zip';

        Files::copy($ori, $_dst, null, $excluded);
        Files::delTree($dst);

        // llamar al ofuscador
        $ori2 = "$_dst";
        $dst2 = "$tmp/obsfuscated";


        /*
            Default
        */
        $the_options = [
            "obfuscate-variable-name" => false, 
            "obfuscate-function-name" => false ,
            "obfuscate-class_constant-name" => false, 
            "obfuscate-class-name"  => false, 
            "obfuscate-interface-name"  => false, 
            "obfuscate-trait-name"  => false, 
            "obfuscate-property-name" => false, 
            "obfuscate-method-name"  => false, 
            "obfuscate-namespace-name" => false,

            "strip-indentation" => true,
            "shuffle-statements" => true,
            "obfuscate-if-statement" => true,
            "obfuscate-string-literal" => true,
            "obfuscate-loop-statement" => true,
            "obfuscate-constant-name"  => false,

            "obfuscate-label-name" => false
        ];

        foreach ($options as $o){
            if (!preg_match('/--(no-)?([a-z\-_]+)/', $o, $matches)){
                throw new \InvalidArgumentException("Option '$o' is invalid");
            }

            //dd($matches[1]);

            $bool = empty($matches[1]); 
            $the_options[ $matches[2] ] = $bool;
        }

        $options_str = '';
        foreach ($the_options as $k => $v){
            $options_str .= '--' . ($v === false ? 'no-' : '') . "$k ";
        }

        $cmd  = "php yakpro-po/yakpro-po.php $ori2 -o $dst2 $options_str";

        #chdir(ROOT_PATH . 'yakpro-po');
        $ret  = shell_exec($cmd);
        d($ret, $cmd);

        /*
            Ahora copio los archivos no-ofuscados en el destino
        */

        Files::copy($ori, "$dst2/yakpro-po/obfuscated", $excluded);

        /*
            Copio al destino final
        */

        Files::setCallback(function(string $content, string $path){
            $content = str_replace(
                'YAK Pro', 
                'Sol.Bin', $content);

            $content = str_replace(
                'GitHub: https://github.com/pk-fr/yakpro-po', 
                'solucionbinaria.com                       ', $content);

            $content = str_replace(
                'Php Obfuscator  2.0.13', 
                'boctulus@gmail.com    ', $content);

            return $content;
        });

        $execept = [
            'obf.yaml',
            'glob:*.zip'
        ];

        Files::copy("$dst2/yakpro-po/obfuscated", $dst, null, $execept);

        d('Hecho!');
    }

    // https://stackoverflow.com/a/60283328/980631
    static function encryptDecrypt($action, $string) 
    {
        $output = false;
        $encrypt_method = "AES-256-CBC";
        $secret_key = 'dkmdkj89LLL__d.d.fd-(DD';
        $secret_iv = 'L0#%3fllflpLOKkjkl,32k1o1l,10i';
        
        // hash
        $key = hash('sha256', $secret_key);    
        // iv - encrypt method AES-256-CBC expects 16 bytes 
        
        $iv = substr(hash('sha256', $secret_iv), 0, 16);
        
        if ( $action == 'encrypt' ) {
            $output = openssl_encrypt($string, $encrypt_method, $key, 0, $iv);
            $output = base64_encode($output);
        } else if( $action == 'decrypt' ) {
            $output = openssl_decrypt(base64_decode($string), $encrypt_method, $key, 0, $iv);
        }

        return $output;
    }
}

