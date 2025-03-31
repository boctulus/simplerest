<?php

namespace Boctulus\Simplerest\Controllers;

use Boctulus\Simplerest\Core\Controllers\Controller;
use Boctulus\Simplerest\Core\Libs\DB;
use Boctulus\Simplerest\Core\Libs\JobQueue;
use Boctulus\Simplerest\Core\Libs\Strings;

class YtController extends Controller
{
    function download(string $url)
    {
        $queue = new JobQueue("yt");
	    $queue->dispatch(\Boctulus\Simplerest\Background\Tasks\YtDownloadTask::class, $url);   
    }

    function start(int $workers = 1){
        $queue = new JobQueue("yt");
        $queue->addWorkers($workers);
    }
}

