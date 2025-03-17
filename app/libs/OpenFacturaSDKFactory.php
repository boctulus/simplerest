<?php

namespace simplerest\libs;

use simplerest\libs\OpenFacturaSDK;
use simplerest\libs\OpenFacturaSDKMock;

class OpenFacturaSDKFactory
{
    static function make(string $api_key, bool $sandbox, bool $mock) { 
        return $mock ? (new OpenFacturaSDKMock($api_key, $sandbox)) : (new OpenFacturaSDK($api_key, $sandbox));
    }
}

