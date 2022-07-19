<?php

namespace simplerest\controllers;

use simplerest\controllers\MyController;
use simplerest\core\Request;
use simplerest\core\Response;
use simplerest\core\libs\Factory;
use simplerest\core\libs\DB;

class LogicController extends MyController
{
    function index()
    {   
        $max_abs_plus = 2;

        $cant_compras_mensuales_plus = 0;
        $cant_en_carrito_plus = 5;
        $cant_en_carrito_normal = 7;

        if ($cant_compras_mensuales_plus > $max_abs_plus){
            d([
                "Eliminar del carrito *todos* los items plus",
                "Agregar al carrito la misma cantidad como normal"
            ],"Convertir cantidad plus -> normal");
            
            $cant_en_carrito_normal += $cant_en_carrito_plus;
            $cant_en_carrito_plus = 0;
        } else {
            $margen_para_plus = $max_abs_plus - $cant_compras_mensuales_plus;

            d($margen_para_plus, 'Margen para plus');

            if ($cant_en_carrito_plus <= $margen_para_plus){
                d("Debo convertir todos los items normales en plus (si el usuario tiene la membresia)");

                $cant_en_carrito_normal -= $margen_para_plus;
                $cant_en_carrito_plus   += $margen_para_plus;

            } else {
                $cant_al_carrito_plus = $margen_para_plus;
                $dif_plus_y_plus_max  = $cant_en_carrito_plus - $margen_para_plus;

                d([
                    "Debo dejar la cantidad de $cant_al_carrito_plus items plus",
                    "Incrementar en la cantidad $dif_plus_y_plus_max como normal"
                ],"Convertir cantidad plus -> normal");


                $cant_en_carrito_plus = $margen_para_plus;
                $cant_en_carrito_normal += $dif_plus_y_plus_max;
            }
        }

        $cant_compras_mensuales_plus += $cant_en_carrito_plus;

        d([
            'Normal' => $cant_en_carrito_normal,
            'Plus' => $cant_en_carrito_plus
        ], 'Cant. <finales> en carrito');

        d($cant_compras_mensuales_plus, 'Compras mensuales plus (para el cliente)');
    }
}

