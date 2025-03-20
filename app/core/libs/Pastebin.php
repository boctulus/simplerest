<?php

namespace simplerest\core\libs;

/*
    GitHub Gist: GitHub Gist is a service provided by GitHub that allows you to share code snippets, text, and more. It has an API that you can use to programmatically create and retrieve Gists. Gists can be either public or private.

    Hastebin: Hastebin is a simple and fast pastebin service that supports various programming languages. It provides an API that allows you to create and retrieve pastes. All pastes on Hastebin are public and accessible on the internet.

    Pastebin: Pastebin is one of the most well-known pastebin services. While it does not provide an official API, there are third-party libraries and wrappers available that allow you to interact with Pastebin programmatically.

    Ghostbin: Ghostbin is another popular pastebin-like service that offers an API. It allows you to create, retrieve, and delete pastes. Ghostbin pastes can be public or password-protected.
*/

class Pastebin
{   

    /*
        Se podria generalizar haciendo reflection para saber que proveedores hay implementados en esta clase
        O permitir registrar otros
    */
    static function getLink(string $str)
    {    
        // pruebo 
        $link = static::sendToSprunge($str);

        // pruebo con otro servicio
        if (empty($link)){
            $link = static::sendToIx($str);
        }

        // Si todo falla
        if (empty($link)){
            throw new \Exception("No pastebin available");
        }

        return $link;
    }

    /*
        Lo ideal seria que si falla, se recordara que ha fallado para no usar este servidor
        por un tiempo.

        Usar ApiClient que tiene algo implementado 
    */

    /*
        Se envia un texto 
        Se recibe un enlace para visualizarlo
    */
    static function sendToSprunge(string $texto) : string {
        $url = 'http://sprunge.us';
    
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, array('sprunge' => $texto));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    
        $response = curl_exec($ch);
        curl_close($ch);
    
        return $response;
    }
    
    /*
        Se envia un texto 
        Se recibe un enlace para visualizarlo
    */
    static function sendToIx(string $texto) : string {
        $url = 'http://ix.io';
    
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, array('f:1' => $texto));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    
        $response = curl_exec($ch);
        curl_close($ch);
    
        return $response;
    }
    

}

