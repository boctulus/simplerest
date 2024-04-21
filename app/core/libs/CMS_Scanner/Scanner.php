<?php

namespace simplerest\core\libs\CMS_Scanner;

use simplerest\core\libs\Strings;
use simplerest\core\libs\ApiClient;
use simplerest\core\libs\Reflector;

class Scanner
{
    protected static function __getSite(string $url){
        $cli = ApiClient::instance($url)
        ->followLocations()
        ->withoutStrictSSL()
        ->cache(3600 * 24 * 31)
        ->userAgent(ApiClient::USER_AG_CHROME)
        ->get();   

        $status  = $cli->getStatus();
        $data    = $cli->data();

        if ($status == 0 || $status >= 400 || empty($data)){
            $error   = $cli->getError();
            $headers = $cli->getHeaders();

            dd($status,  'STATUS');
            dd($error,   'ERROR');
            dd($headers, 'HEADERS');

            dd($cli->dump(), 'DUMP');
            exit;
        }
    
        $res = $cli->getResponse();

        return $res['data'];
    }

    /*
        
        $url = 'http://woo4.lan';  // WordPress
        $url = 'www.leifshop.com'; // Shopify
        $url = 'https://www.lechocolat-alainducasse.com/'; // PrestaShop
        $url = 'https://www.fredperry.com'; // Magento
    */
    static function identify(string $url)
    {
        $site = static::__getSite($url);

        $cmsFiles = scandir(__DIR__ . '/CMSs');
        foreach ($cmsFiles as $file) {
            if ($file !== '.' && $file !== '..') {
                $className = __NAMESPACE__ . '\\CMSs\\' . pathinfo($file, PATHINFO_FILENAME);

                if (!class_exists($className)) {
                    throw new \Exception("Class '$className' not found");   
                }

                if ($className::isIt($site)) {
                    return substr($className, strrpos($className, '\\') + 1);
                }            
            }
        }

        return null;
    }
    

}

