<?php

namespace simplerest\middlewares;

use simplerest\core\Middleware;
use simplerest\core\libs\DB;
use simplerest\core\libs\Strings;

class InyectarUsername extends Middleware
{  
    function handle(?callable $next = null){
        $res = $this->res->get();

        $uid   = $res['data']['uid'];
        
        $uname = DB::table(get_users_table())
        ->find($uid)
        ->value('username');

        $res['data']['username'] = $uname;

        $this->res->set($res);
    }
}