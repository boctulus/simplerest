<?php

namespace simplerest\middlewares;

use simplerest\core\libs\DB;
use simplerest\core\libs\Files;
use simplerest\core\Middleware;
use simplerest\core\libs\Strings;

class InyectarUsername extends Middleware
{  
    function handle(?callable $next = null){
        $res = $this->res->get();

        /*
            Se espera el resultado pueda ser un JSON en este caso
            ya que el Middleware se encuentra ya con la respuesta encodeada
        */

        if (is_string($res)){
            $res = json_decode($res, true);
        }

        $uid   = $res['data']['uid'];
        
        $uname = DB::table(get_users_table())
        ->find($uid)
        ->value('username');

        $res['data']['username'] = $uname;

        $this->res->set($res);
    }
}