<?php

namespace Boctulus\OpenfacturaSdk\Factory;

use Boctulus\OpenfacturaSdk\Libs\OpenFacturaSDK;
use Boctulus\OpenfacturaSdk\Mocks\OpenFacturaSDKMock;

class OpenFacturaSDKFactory
{
    public static function make(string $api_key, bool $sandbox = false, bool $mock = false) {
        return $mock ? (new OpenFacturaSDKMock($api_key, $sandbox)) : (new OpenFacturaSDK($api_key, $sandbox));
    }
}

