<?php

namespace simplerest\middlewares;

use simplerest\core\Middleware;
use simplerest\core\libs\Factory;
use simplerest\core\libs\Schema;
use simplerest\core\Model;
use simplerest\core\libs\DB;

class InyectarInfoEmpresa extends Middleware
{
    function __construct()
    {
        parent::__construct();
    }
    function handle(?callable $next = null){
        $data  = $this->res->getData();
        
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
            $this->res->setData($data);
        }
        //return $next($this->req, $this->res);
    }
}