<?php

namespace simplerest\controllers;

use simplerest\core\controllers\Controller;
use simplerest\core\libs\DB;
use simplerest\core\libs\JobQueue;
use simplerest\core\libs\Strings;

class YtController extends Controller
{
    function download(string $url)
    {
        $queue = new JobQueue("yt");
	    $queue->dispatch(\simplerest\background\tasks\YtDownloadTask::class, $url);   
    }

    function start(int $workers = 1){
        $queue = new JobQueue("yt");
        $queue->addWorkers($workers);
    }
}

