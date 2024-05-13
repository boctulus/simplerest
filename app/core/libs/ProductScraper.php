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

    static function getHTML($slug)
    {
        // Podria normalizar la URL
        $url = rtrim(static::$urlBase, '/') . '/' . ltrim($slug, '/');

        $cli = (new ApiClient($url))
        ->withoutStrictSSL()
        ->setHeaders([
            'User-Agent' => 'PostmanRuntime/7.34.0',
        ])
        ->redirect()
        ->cache(static::$exp_time)
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
        throw new NotImplementedException('Method getProductBasicAttr() is not implemented.');
    }

    public static function getCategos(string $html)
    {
        throw new NotImplementedException('Method getCategos() is not implemented.');
    }

    public static function getBrands(string $html)
    {
        throw new NotImplementedException('Method getBrands() is not implemented.');
    }

    public static function getCategosList(string $html)
    {
        throw new NotImplementedException('Method getCategosList() is not implemented.');
    }

    public static function getBrandList(string $html)
    {
        throw new NotImplementedException('Method getBrandList() is not implemented.');
    }

    public static function getProductLinks(string $html)
    {
        throw new NotImplementedException('Method getProductLinks() is not implemented.');
    }

}

