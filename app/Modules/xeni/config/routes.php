<?php

use Boctulus\Simplerest\Core\Libs\Logger;
use Boctulus\Simplerest\Core\WebRouter;

$route = WebRouter::getInstance();

WebRouter::get('xeni/test', 'Boctulus\Simplerest\Modules\xeni\Controllers\TestController@index');



