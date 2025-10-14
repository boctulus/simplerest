<?php

namespace Boctulus\Simplerest\Libs\RateExchange;

use boctulus\SW\interfaces\ICurrencyConverter;

/*
	@author boctulus
*/

class VercelARS extends Conversor
{
    /*
        $to puede ser 'blue', 'oficial', etc
    */
    static function convert($to = 'blue', $from = 'usd', $bid_ask = null){
        $mapper = [
            'usd'   => 'dolares'
        ];

        $from = $mapper[$from];

        $url = "https://dolar-api-argentina.vercel.app/v1/$from/$to";

        $res = consume_api($url, 'GET', null, null, null, true, true, static::$expiration_time);
        static::$data = $res;

        $val = null;
        switch($bid_ask){
            case static::BID :
                $val = $res['compra'];
            break;

            default:

            case static::ASK :
                $val = $res['venta'];
            break;

            case static::AVG :
                $val = 0.5 * ($res['compra'] + $res['venta']);
            break;
        }

        static::$updated_at = $res['fechaActualizacion'];

        return $val;
    }
}