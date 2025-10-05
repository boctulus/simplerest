<?php

namespace Boctulus\Simplerest\controllers;

use Boctulus\Simplerest\Core\Libs\DB;
use Boctulus\Simplerest\Core\Libs\Files;
use Boctulus\Simplerest\Core\Libs\Strings;
use Boctulus\Simplerest\Core\Controllers\Controller;
use Boctulus\Simplerest\Core\Traits\TimeExecutionTrait;

class VeaController extends Controller
{
    function __construct() { parent::__construct(); }

    /**
     * Parsea una línea CSV con el comportamiento específico:
     * - Algunos valores pueden tener comas dentro de comillas.
     * - Los números decimales vienen entre comillas y usan coma decimal y punto de miles.
     * - Devuelve array asociativo con claves:
     *   categorias, precio, descripcion, marca, ean, imagen
     *
     * @param string $line  Línea CSV
     * @param int $expectedTail Número de campos fijos al final (precio, descripcion, marca, ean, imagen). Default 5.
     * @return array Associative array parsed
     */
    static function parseCsvLineSpecial(string $line, int $expectedTail = 5): array {
        // marcador temporal improbable
        $MARK = '<<CSV_COMMA>>';

        // 1) Reemplazar comas DENTRO de comillas por un marcador
        //    Usamos regex simple para grupos entre comillas dobles.
        $modified = preg_replace_callback(
            '/"([^"]*)"/u',
            function ($m) use ($MARK) {
                // Reemplaza solo comas internas por el marcador y devuelve el contenido sin las comillas
                return str_replace(',', $MARK, $m[1]);
            },
            $line
        );

        // 2) Partir por comas (ahora las comas internas están protegidas)
        $parts = explode(',', $modified);

        // 3) Restaurar marcador a comas, limpiar espacios y comillas sobrantes
        $cleaned = array_map(function($p) use ($MARK) {
            // restaurar comas internas, quitar posibles comillas sobrantes y trim
            $s = str_replace($MARK, ',', $p);
            // quitar comillas residuales y trim
            $s = trim($s);
            $s = trim($s, "\"'\x00..\x1F"); // quita comillas y control chars al inicio/fin
            return $s;
        }, $parts);

        // 4) Si hay más campos de los esperados, unir los encabezados en 'categorias'
        $n = count($cleaned);
        if ($n < $expectedTail + 1) {
            // no es suficiente, devolver algo razonable
            // completar con valores vacíos
            $pad = array_pad($cleaned, $expectedTail + 1, '');
            $cleaned = $pad;
            $n = count($cleaned);
        }

        // categorias = unión de todos los campos que queden antes de los últimos $expectedTail campos
        $numCategories = $n - $expectedTail;
        $categoriesArr = array_slice($cleaned, 0, $numCategories);
        $tailArr = array_slice($cleaned, $numCategories, $expectedTail);

        $categorias = implode(',', $categoriesArr);
        // Mapear tail a campos en orden: precio, descripcion, marca, ean, imagen
        // Si el CSV esperara otro orden, ajustar aquí.
        $mapping = [
            'precio' => $tailArr[0] ?? '',
            'descripcion' => $tailArr[1] ?? '',
            'marca' => $tailArr[2] ?? '',
            'ean' => $tailArr[3] ?? '',
            'imagen' => $tailArr[4] ?? '',
        ];

        // 5) Normalizar precio: convertir "6.216,44" -> "6216.44" (float)
        $mapping['precio_raw'] = $mapping['precio'];
        $mapping['precio'] = static::normalizePrice($mapping['precio']);

        return array_merge(['categorias' => trim(trim($categorias, ','))], $mapping);
    }

    /**
     * Normaliza un string de precio:
     * - Quita espacios y símbolos
     * - Si tiene punto y coma de miles y coma decimal: elimina puntos y convierte coma a punto
     * - Si solo tiene coma: la trata como decimal
     * - Devuelve float o null si no pudo parsear
     *
     * @param string $s
     * @return float|null
     */
    static function normalizePrice(string $s): ?float {
        $s = trim($s);
        if ($s === '') return null;

        // eliminar símbolos comunes (moneda, espacios, etc.)
        $s = str_replace(['$', '€', '£', "\u{00A0}"], '', $s);
        $s = trim($s);

        // mantener solo dígitos, puntos, comas, signo menos
        $s = preg_replace('/[^\d\.,-]/', '', $s);

        // si contiene BOTH '.' y ',' asumimos: '.' = separador de miles, ',' = decimal
        if (strpos($s, '.') !== false && strpos($s, ',') !== false) {
            $s = str_replace('.', '', $s);      // quitar miles
            $s = str_replace(',', '.', $s);     // coma decimal -> punto
        } elseif (strpos($s, ',') !== false) {
            // sólo coma -> tratar como decimal
            $s = str_replace(',', '.', $s);
        }
        // ahora $s debe usar punto como decimal (si acaso)
        // evitar múltiples puntos
        // si quedan varios puntos (caso raro), tomar la última como decimal:
        if (substr_count($s, '.') > 1) {
            // eliminar todos los puntos excepto el último
            $parts = explode('.', $s);
            $last = array_pop($parts);
            $s = implode('', $parts) . '.' . $last;
        }

        // convertir a float
        if ($s === '' || $s === '-' || $s === '.' ) return null;
        // usar floatval es suficiente para la mayoría de casos
        $val = floatval($s);
        return $val;
    }

    // OK
    function try_parse_csv(){
        $data  = Files::getContent('D:\\Desktop\\ZIPPY LAST FILES\\ListaVea.csv');
        $data  = Strings::fixEncoding($data);
        $lines = Strings::lines($data, true, true);

        // dd($lines[0], 'LINES');

        foreach($lines as $k => $line){
            $row = static::parseCsvLineSpecial($line); 
            $row = Strings::trim($row);
            
            if (Strings::isEmpty($row)){
                unset($lines[$k]);
                continue;
            }
            
            $lines[$k] = $row;
        }

        dd($lines);
    }

    function index()
    {
        $this->try_parse_csv();      
    }
}

