<?php

namespace Boctulus\CliTest\Controllers;

class TestController
{
    public function info()
    {
        return 'TestController@info works!';
    }

    public function process($arg1, $arg2 = null)
    {
        if ($arg2 === null) {
            return "Processing: $arg1";
        }

        return "Processing: $arg1 and $arg2";
    }

    public function validate($num)
    {
        if (!is_numeric($num)) {
            return "Error: '$num' is not a number";
        }

        return "Valid number: $num";
    }
}
