<?php

namespace Boctulus\ExchangeRate;

/*
	@author boctulus
*/

abstract class Conversor
{
    static $updated_at;
    static $data;
    static $expiration_time = 3600;
    
    abstract static function convert($to = 'ars', $from = 'usd', $bid_ask = null);

    static function convertAmount($amount = 1, $to = 'ars', $from = 'usd', $bid_ask = null){
        return $amount * ((float) static::convert($to, $from, $bid_ask));
    }   

    static function updatedAt(){
        return static::$updated_at;
    }

    static function setExpirationTime(int $secs){
        static::$expiration_time = $secs;
    }

    // get (full) response
    static function data(){
        return static::$data;
    }
}