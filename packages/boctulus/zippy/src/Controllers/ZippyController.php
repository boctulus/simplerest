<?php

namespace Boctulus\Zippy\Controllers;

use Boctulus\Simplerest\Core\Libs\DB;
use Boctulus\Simplerest\Core\Libs\Files;
use Boctulus\Simplerest\Core\Libs\Strings;
use Boctulus\Simplerest\Core\Controllers\Controller;
use Boctulus\Simplerest\Core\Traits\TimeExecutionTrait;

class ZippyController extends Controller
{
    function read_csv_comercio()
    {
        $archivo = 'D:\Desktop\PALITO PRJ\DATABASE\comercio.csv';
        dd($archivo, 'ARCHIVO');

        Files::processCSV($archivo, 'AUTO', true, function ($p) {
            dd($p, 'P');
        }, null, 0, 2);
    }

    function read_csv_products()
    {
        $archivo = 'D:\Desktop\PALITO PRJ\DATABASE\productos.csv';

        Files::processCSV($archivo, 'AUTO', true, function ($p) {
            dd($p, 'P');
        }, null, 0, 2);

        // Campos promo para los que queremos un ejemplo no vacío
        $fields = [
            'productos-precio-unitario-promo1',
            'productos-leyenda-promo1',
            'productos-precio-unitario-promo2',
            'productos-leyenda-promo2',
        ];

        // Inicializar ejemplos a null
        $examples = array_fill_keys($fields, null);

        try {
            Files::processCSV(
                $archivo,
                'AUTO',
                true,
                function ($row) use (&$examples, $fields) {
                    // Para cada campo, si aún no tenemos ejemplo y el valor no está vacío, lo guardamos
                    foreach ($fields as $field) {
                        if ($examples[$field] === null && !empty($row[$field])) {
                            $examples[$field] = $row[$field];
                        }
                    }

                    // Si ya tenemos ejemplo para todos, lanzamos excepción para detener el bucle
                    if (!in_array(null, $examples, true)) {
                        throw new \Exception('StopIteration');
                    }
                },
                null,   // Sin redefinición de cabecera
                0,      // start_line
                false   // sin límite, pues rompemos con excepción
            );
        } catch (\Exception $e) {
            // Capturamos únicamente nuestra excepción de control de flujo
            if ($e->getMessage() !== 'StopIteration') {
                throw $e;
            }
        }

        // Mostrar los ejemplos encontrados
        dd($examples, 'Promo Field Examples');
    }

    function read_csv_sucursales()
    {
        $archivo = 'D:\Desktop\PALITO PRJ\DATABASE\sucursales.csv';
        dd($archivo, 'ARCHIVO');

        Files::processCSV($archivo, 'AUTO', true, function ($p) {
            dd($p, 'P');
        }, null, 0, 2);
    }
}

