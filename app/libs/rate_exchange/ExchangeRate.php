<?php

namespace boctulus\SW\libs\currency;


/*
	@author boctulus
*/

class ExchangeRate extends Conversor
{
    /*
        Idealmente poder configurar expiration_time
    */
    
    static function convert($to = 'ars', $from = 'usd', $bid_ask = null){
        $from   = strtoupper($from);
        $to     = strtoupper($to);

        $api_key = env('EXCHANGERATE_API_KEY');
        $url     = "https://v6.exchangerate-api.com/v6/$api_key/latest/$from";

        $res     = consume_api($url, 'GET', null, null, null, true, true, static::$expiration_time);
        static::$data = $res;

        $rates   = $res['conversion_rates'];
        $val     = $rates[$to];

        static::$updated_at = $res['time_last_update_utc'];

        return $val;
    }
}