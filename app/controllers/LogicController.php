<?php

namespace simplerest\controllers;

use simplerest\controllers\MyController;
use simplerest\core\Request;
use simplerest\core\Response;
use simplerest\core\libs\Factory;
use simplerest\core\libs\DB;

class LogicController extends MyController
{
    static function cartLogic(&$cant_en_carrito_plus, &$cant_en_carrito_normal, &$cant_compras_mensuales_plus, $max_abs_plus){       
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

            if ($cant_en_carrito_plus < $margen_para_plus){  /// antes <=
                d("Debo convertir todos los items normales en plus (si el usuario tiene la membresia)");

                $cant_en_carrito_plus   += $cant_en_carrito_normal; //
                $cant_en_carrito_normal = 0; //

            } else {
                $_dif_plus_y_plus_max = $cant_en_carrito_plus - $margen_para_plus;

                d([
                    "Debo dejar la cantidad de $margen_para_plus items plus",
                    "Incrementar en la cantidad $_dif_plus_y_plus_max como normal"
                ],"Convertir cantidad plus -> normal");


                $cant_en_carrito_plus = $margen_para_plus;
                $cant_en_carrito_normal += $_dif_plus_y_plus_max;
            }
        }

        $cant_compras_mensuales_plus += $cant_en_carrito_plus;
    }

    function index()
    {   
        $max_abs_plus = 2;
        $cant_compras_mensuales_plus = 6;

        $cant_en_carrito_plus = 1;
        $cant_en_carrito_normal = 1;

        d($cant_compras_mensuales_plus, 'Compras mensuales');

        d([
            'Normal' => $cant_en_carrito_normal,
            'Plus' => $cant_en_carrito_plus
        ], 'Cant. <iniciales> en carrito');

        static::cartLogic($cant_en_carrito_plus, $cant_en_carrito_normal, $cant_compras_mensuales_plus, $max_abs_plus);

        d([
            'Normal' => $cant_en_carrito_normal,
            'Plus' => $cant_en_carrito_plus
        ], 'Cant. <finales> en carrito');

        d($cant_compras_mensuales_plus, 'Compras mensuales plus (para el cliente)');
    }
}
