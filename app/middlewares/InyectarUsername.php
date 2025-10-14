<?php

namespace Boctulus\Simplerest\Middlewares;

use Boctulus\Simplerest\Core\Libs\DB;
use Boctulus\Simplerest\Core\Libs\Files;
use Boctulus\Simplerest\Core\Middleware;
use Boctulus\Simplerest\Core\Libs\Strings;

class InyectarUsername extends Middleware
{  
    function handle(){
        $res = response()->get();        

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

        response()->set($res);
    }
}