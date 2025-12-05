<?php

namespace Boctulus\Simplerest\middlewares;

use Boctulus\Simplerest\Core\Middleware;
use Boctulus\Simplerest\Core\Libs\DB;

class InyectarInfoEmpresa extends Middleware
{
    function __construct()
    {
        parent::__construct();
    }

    function handle(){
        $data = response()->get();
        
        if (isset($data['data']["db_access"]) && !empty($data['data']["db_access"])){
       
            $info_empresas = [];
            foreach ($data['data']["db_access"] as $tenant){
                DB::setConnection($tenant);
                $info_empresas[] = [
                    "db_name" => $tenant,
                    "enterprise" => DB::table('tbl_empresa')->value("emp_varRazonSocial")
                ];
                // $info_empresas[$tenant] = DB::table('tbl_empresa')
                // ->value("emp_varRazonSocial");
            }
       
            $data['data']['info_empresas'] = $info_empresas;
            response()->set($data);
        }

    }

}