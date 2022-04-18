<?php declare(strict_types=1);

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
    static function clear(string $_dst)
    {
        static $cleared = [];

        if (in_array($_dst, $cleared)){
            return;
        }

        $tmp  = sys_get_temp_dir();
        $_dst = "$tmp/to_obsfuscate";

        Files::mkDir  ($_dst);
        Files::delTree($_dst);
        Files::delTree("$tmp/obsfuscated");

        StdOut::pprint("CLEARED *************************");

        $cleared[] = $_dst;
    }

    /*
        TODO:

        - Ofuscar también los JS !!!

        https://www.npmjs.com/package/javascript-obfuscator
        
    */
    static function obfuscate(string $ori, string $dst, $files = null, $excluded =  null, $options = null, $profile = null, $copy_excluded = true)
    {   
        $profile_options = [
            'normal' =>  [
                "strip-indentation" => true,
                "shuffle-statements" => true,
                "obfuscate-string-literal" => true,
                "obfuscate-loop-statement" => true,
                "obfuscate-if-statement" => true, 
                "obfuscate-constant-name"  => false,
                "obfuscate-variable-name" => false, 
                "obfuscate-function-name" => false,
                "obfuscate-class_constant-name" => false, 
                "obfuscate-class-name"  => false, 
                "obfuscate-interface-name"  => false, 
                "obfuscate-trait-name"  => false, 
                "obfuscate-property-name" => false, 
                "obfuscate-method-name"  => false, 
                "obfuscate-namespace-name" => false,
                "obfuscate-label-name" => false
            ],

            'careful' => [
                "obfuscate-function-name" => false,
                "obfuscate-class-name"  => false, 
                "obfuscate-property-name" => false,
                "obfuscate-method-name"  => false, 
            ],

            'aggressive' =>  [
                "strip-indentation" => true,
                "shuffle-statements" => true,
                "obfuscate-string-literal" => true,
                "obfuscate-loop-statement" => true,
                "obfuscate-if-statement" => true, 
                "obfuscate-constant-name"  => true,
                "obfuscate-variable-name" => true, 
                "obfuscate-function-name" => true,
                "obfuscate-class_constant-name" => true, 
                "obfuscate-class-name"  => true, 
                "obfuscate-interface-name"  => true, 
                "obfuscate-trait-name"  => true, 
                "obfuscate-property-name" => true, 
                "obfuscate-method-name"  => true, 
                //"obfuscate-namespace-name" => true,
                "obfuscate-label-name" => true
            ],

            "none" => [
                "strip-indentation" => false,
                "shuffle-statements" => false,
                "obfuscate-string-literal" => false,
                "obfuscate-loop-statement" => false,
                "obfuscate-if-statement" => false, 
                "obfuscate-constant-name"  => false,
                "obfuscate-variable-name" => false, 
                "obfuscate-function-name" => false,
                "obfuscate-class_constant-name" => false, 
                "obfuscate-class-name"  => false, 
                "obfuscate-interface-name"  => false, 
                "obfuscate-trait-name"  => false, 
                "obfuscate-property-name" => false, 
                "obfuscate-method-name"  => false, 
                //"obfuscate-namespace-name" => false,
                "obfuscate-label-name" => false
            ]
        ];

        if (empty($profile)){
            $profile = 'normal';
        }

        $allowed_profiles = array_keys($profile_options);

        if (!in_array($profile, $allowed_profiles)){
            throw new \InvalidArgumentException("Invalid profile. It can only be ". implode(',', $allowed_profiles). ". Given '$profile'");
        }

        $tmp  = sys_get_temp_dir();
        $_dst = "$tmp/to_obsfuscate";

        static::clear($_dst);

        if ($files === null){
            $files = [];
        }

        if ($excluded === null){
            $excluded = [];
        }

        if (is_string($excluded)){
            if (Strings::contains(PHP_EOL, $excluded)){
                $excluded = explode(PHP_EOL, $excluded);
            }
        }

        // (1) ->  "$tmp/to_obsfuscate"
        Files::copy($ori, $_dst, $files, array_merge($excluded, [
            'obf.yaml',
            'glob:*.zip'
        ]));

        // limpia carpeta destino final
        Files::delTree($dst);

        // llamar al ofuscador
        $ori2 = "$_dst";
        $dst2 = "$tmp/obsfuscated";
   
        /*
            Default profile
        */
        $the_options = $profile_options[$profile];


        /*
            Las opciones "decoran" el profile elegido
        */
        if (!empty($options)){
            foreach ($options as $o){
                if (!preg_match('/--(no-)?([a-z\-_]+)/', $o, $matches)){
                    throw new \InvalidArgumentException("Option '$o' is invalid");
                }

                //dd($matches[1]);

                $bool = empty($matches[1]); 
                $the_options[ $matches[2] ] = $bool;
            }
        }
        
        $options_str = '';
        foreach ($the_options as $k => $v){
            $options_str .= '--' . ($v === false ? 'no-' : '') . "$k ";
        }

        // (2)
        $cmd  = "php yakpro-po/yakpro-po.php $ori2 -o $dst2 $options_str";

        #chdir(ROOT_PATH . 'yakpro-po');
        $ret  = shell_exec($cmd);
        
        var_dump("$cmd");
        echo "\r\n";
        dd($ret);

        /*
            Ahora copio los archivos no-ofuscados
        */

        // (3)
        if ($copy_excluded){
            Files::copy($ori, "$dst2/yakpro-po/obfuscated", $excluded);
        }

        /*
            Debería tenerse en consideración órigen y destino por si se corriera el ofuscador en otro contexto.
        */
        

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

        // (4)
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

