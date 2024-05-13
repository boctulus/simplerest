<?php

namespace simplerest\core\libs;

use simplerest\core\libs\ApiClient;
use simplerest\core\exceptions\NotImplementedException;

abstract class ProductScraper
{
    protected static $urlBase;
    protected static $exp_time = 3600 * 6;

    
    static function setup($urlBase, $exp_time){
        static::$urlBase  = $urlBase;
        static::$exp_time = $exp_time;
    }

    static function setExpTime($exp_time) {
        self::$exp_time = $exp_time;
    }

    /*  
        @param string $slug slug o url
        @param int|null $exp_time tiempo de duracion de la cache
    */
    static function getHTML($slug, $exp_time = null)
    {
        // Podria normalizar la URL
        $url = Strings::startsWith('https://', $slug) ?  $slug : rtrim(static::$urlBase, '/') . '/' . ltrim($slug, '/');

        $cli = (new ApiClient($url))
        ->withoutStrictSSL()
        ->setHeaders([
            'User-Agent' => 'PostmanRuntime/7.34.0',
        ])
        ->redirect()
        ->cache($exp_time ?? static::$exp_time)
        ;

        $cli->setMethod('GET');
        $cli->send();

        if ($cli->error()){
            throw new \Exception("HTTP ERROR: ". $cli->error());
        }

        $res = $cli->data(); // html

        return $res;
    }

    abstract public static function getProduct(string $url);

    public static function getProductBasicAttr(string $html)
    {
        throw new NotImplementedException('Method '. __METHOD__ .' is not implemented.');
    }
    
    public static function getCategosList(string $html)
    {
        throw new NotImplementedException('Method '. __METHOD__ .' is not implemented.');
    }

    /*
        Obtiene paginador de pagina de productos (u otras)
    */
    public static function getPaginator($html){
        throw new NotImplementedException('Method '. __METHOD__ .' is not implemented.');
    }

    /*
        Obtiene URL de pagina de categoria paginada
    */
    public static function getCategoryPageURL(int $page, $page_size = null)
    {
        throw new NotImplementedException('Method '. __METHOD__ .' is not implemented.');
    }

    /*
        Obtiene de pagina de categoria paginada
    */
    public static function getCategoryPage(int $page, $page_size = null)
    {
        return static::getHTML(static::getCategoryPageURL($page, $page_size));
    }

    /*
        Devuelve descripcion e imagenes de cada categoria
    */
    public static function getCatego(string $html)
    {
        throw new NotImplementedException('Method '. __METHOD__ .' is not implemented.');
    }

    public static function getBrandList(string $html)
    {
        throw new NotImplementedException('Method '. __METHOD__ .' is not implemented.');
    }

    public static function getProductLinks(string $html)
    {
        throw new NotImplementedException('Method '. __METHOD__ .' is not implemented.');
    }

}

