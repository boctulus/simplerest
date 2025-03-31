<?php

namespace Boctulus\Simplerest\Controllers\demos;

use Boctulus\Simplerest\Core\Libs\DB;
use Boctulus\Simplerest\Core\Request;
use Boctulus\Simplerest\Core\Response;
use Boctulus\Simplerest\Core\Libs\Paginator;
use Boctulus\Simplerest\Core\Libs\Factory;
use Boctulus\Simplerest\Core\Controllers\Controller;

class PaginatorController extends Controller
{
    function pagination  (){
        $page_size = $_GET['size'] ?? 10;
        $page      = $_GET['page'] ?? 1;

        DB::getConnection('az');

        $rows = DB::table('products')
        ->paginate($page, $page_size)
        ->get();

        return $rows;
    }

    function get_pagination_params(){
        dd(
            /*
                El metodo getPaginatorParams() debe ir en Response y no en Request !!!! 
            */
            request()->getPaginatorParams()
        );
    }

    /*
        0
        1
        2

        3 <--- page offset
        4
        5

        6
        7
        8
        
    */
    function calc_page_offset(){
        $page_size = 3;
        $page      = 2;

        dd(
            Paginator::calcOffset($page_size, $page)
        );
    }

    function calc_pagination(){
        $page_size = $_GET['size'] ?? 10;
        $page      = $_GET['page'] ?? 1;

        DB::getConnection('az');

        $row_count = DB::table('products')
        ->count();

        return Paginator::calc($page, $page_size, $row_count);
    }

}

