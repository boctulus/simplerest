<?php

namespace Boctulus\Simplerest\Core\Libs\CMS_Scanner;

use Boctulus\Simplerest\Core\exceptions\NotImplementedException;
use Boctulus\Simplerest\Core\Libs\Strings;
use Boctulus\Simplerest\Core\Libs\ApiClient;

/*
    mutawp.com                      // WordPress
    www.leifshop.com                // Shopify
    www.lechocolat-alainducasse.com // PrestaShop
    www.fredperry.com               // Magento
    moderne.st                      // NuxtJs 
    www.sonjavanduelmen.com         // Wix
    www.sundaystate.ca              // Squarespace
    omnisport.com                   // Laravel
    ladylee.net                     // Angular (SSR)
    ladylee.com                     // Angular (SSR)
    elektra.com.gt                  // ReactJs
*/
class Scanner
{
    protected static $url;
    protected static $content;
    protected static $headers;


    protected static function __getSite(string $url){
        $cli = ApiClient::instance($url)
        ->followLocations()
        ->withoutStrictSSL() 
        ->cache(3600 * 24 * 31)  // <-------------- deberian almacenarse headers tambien !!!
        ->userAgent(ApiClient::USER_AG_CHROME)
        ->get();   

        $status  = $cli->getStatus();
        $data    = $cli->data();
        $error   = $cli->getError();
        $headers = $cli->getHeaders();
        
        static::$content = $data;
        static::$headers = $headers;

        if ($status == 0 || $status >= 400 || empty($data)){
            dd($status,  'STATUS');
            dd($error,   'ERROR');
            dd($headers, 'HEADERS');

            dd($cli->dump(), 'DUMP');
            exit;
        }
    
        $res = $cli->getResponse();

        return $res;
    }

    static function runsLaravel(){
        return Strings::containsAny(['<meta name="csrf-token"', 'blade'], static::$content, false);
    }

    static function runsExpressJs(){
        return Strings::containsAny(['express'], static::$headers['server'] ?? ''); 
    }
    
    static function runsVueJs(){
        return Strings::containsAny(['/vue@', 'vue.global.js'], static::$content); 
    }

    static function runsNuxtJs(){
        return Strings::containsAny(['nuxt-link-active', 'nuxt-link-exact-active', 'nuxtjs'], static::$content);
    }
        
    static function runsVue_SSR(){        
        return Strings::containsAny(['data-vue-ssr-id'], static::$content);
    }

    static function runsReactJs(){
        return Strings::containsAny(['/react-dom@', '/react-intl@'], static::$content);
    }

    static function runsAngular(){
        return Strings::containsAny(['ng-star-inserted', 'ng-transition', '<app-root ', 'ng-version='], static::$content);
    }
        
    static function runsAngular_SSR(){        
        return Strings::containsAny(['ng-server-context="ssr"'], static::$content);
    }

    // 
    static function hasBootstrap(){
        return Strings::containsAnyWord(['btn-primary', 'container-fluid', 'pull-left', 'pull-right'], static::$content);
    }

    /*
        Sin implementar!

        Deberia parsear el parametro "v"

        <link href="https://sexyqfans.com/public/css/bootstrap-icons.css?v=5.1" rel="stylesheet">
    */
    static function getBootstrapVersion(){
        throw new NotImplementedException();
    }
   
    static function identify(string $url)
    {
        $site     = static::__getSite($url);

        $cmsFiles = scandir(__DIR__ . '/CMSs');
        foreach ($cmsFiles as $file) {
            if ($file !== '.' && $file !== '..') {
                $className = __NAMESPACE__ . '\\CMSs\\' . pathinfo($file, PATHINFO_FILENAME);

                if (!class_exists($className)) {
                    throw new \Exception("Class '$className' not found");   
                }

                if ($className::isIt(static::$content)) {
                    return substr($className, strrpos($className, '\\') + 1);
                }            
            }
        }

        $data = [
            'frontend' => [],
            'backend'  => []
        ];

        if (static::runsLaravel()){
            $data['backend'][] = 'Laravel';
        }

        if (static::runsExpressJs()){
            $data['backend'][] = 'Express.js (Node)'; 
        }

        if (static::runsReactJs()){
            $data['frontend'][] = 'ReactJs';
        }

        if (static::runsNuxtJs()){
            $data['frontend'][] = 'NuxtJs (Vue)';
        }

        if (static::runsVueJs()){
            if (static::runsVue_SSR()){
                $data['frontend'][] = 'VueJs (SSR)';
            } else {
                $data['frontend'][] = 'VueJs';
            }
        } 

        if (static::runsAngular()){
            if (static::runsAngular_SSR()){
                $data['frontend'][] = 'Angular (SSR)';
            } else {
                $data['frontend'][] = 'Angular';
            }
        }

        if (static::hasBootstrap()){
            $data['frontend'][] = 'Bootstrap';
        }

        return $data;
    }
    

}

