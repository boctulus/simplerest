<?php

namespace Boctulus\Simplerest\Libs;

use Boctulus\Simplerest\Libs\OpenFacturaSDK;
use Boctulus\Simplerest\Libs\OpenFacturaSDKMock;

class OpenFacturaSDKFactory
{
    static function make(string $api_key, bool $sandbox, bool $mock) { 
        return $mock ? (new OpenFacturaSDKMock($api_key, $sandbox)) : (new OpenFacturaSDK($api_key, $sandbox));
    }
}

