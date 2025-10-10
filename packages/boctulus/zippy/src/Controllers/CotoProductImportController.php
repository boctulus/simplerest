<?php

namespace Boctulus\Zippy\Controllers;

use Boctulus\Simplerest\Core\Libs\DB;
use Boctulus\Simplerest\Core\Libs\Files;
use Boctulus\Simplerest\Core\Libs\Strings;
use Boctulus\Simplerest\Core\Controllers\Controller;
use Boctulus\Simplerest\Core\Libs\System;
use Boctulus\Simplerest\Core\Traits\TimeExecutionTrait;

class CotoProductImportController extends Controller
{
    function __construct()
    {
        parent::__construct();
    }

    /**
     * Parsea una línea CSV del archivo coto.csv con formato:
     * "Almacén"|"Golosinas"|"Producto descripción"|"PLU: 598747 | EAN: 7896018211766"|"https://..."
     *
     * - Columna 1: Ignorada (siempre "Almacén")
     * - Columna 2: Categoría (guardar en catego_raw1)
     * - Columna 3: Descripción del producto
     * - Columna 4: Contiene PLU y EAN (extraer EAN)
     * - Columna 5: URL de la imagen
     *
     * @param string $line  Línea CSV
     * @return array Associative array con: category, description, ean, img
     */
    static function parseCsvLineSpecial(string $line): array
    {
        // Marcador temporal para proteger pipes dentro de comillas
        $MARK = '<<CSV_PIPE>>';

        // 1) Reemplazar pipes DENTRO de comillas por un marcador
        $modified = preg_replace_callback(
            '/"([^"]*)"/u',
            function ($m) use ($MARK) {
                // Reemplaza pipes internos por el marcador y devuelve el contenido sin comillas
                return str_replace('|', $MARK, $m[1]);
            },
            $line
        );

        // 2) Partir por pipes (ahora los pipes internos están protegidos)
        $parts = explode('|', $modified);

        // 3) Restaurar marcador a pipes y limpiar espacios
        $cleaned = array_map(function ($p) use ($MARK) {
            $s = str_replace($MARK, '|', $p); // restaurar pipes internos
            $s = trim($s);
            $s = trim($s, "\"'\x00..\x1F"); // quita comillas y control chars
            return $s;
        }, $parts);

        // Asegurarse de tener al menos 5 columnas
        $cleaned = array_pad($cleaned, 5, '');

        // Extraer campos según la estructura del CSV
        // Columna 0: Ignorar ("Almacén")
        // Columna 1: Categoría
        $category = $cleaned[1] ?? '';

        // Columna 2: Descripción
        $description = $cleaned[2] ?? '';

        // Columna 3: Puede contener "PLU: 598747 | EAN: 7896018211766" o estar vacía
        // Columna 4: Puede contener URL de imagen o "EAN: 7896018211766"
        // Intentar extraer EAN primero de columna 3, luego de columna 4
        $eanField3 = $cleaned[3] ?? '';
        $eanField4 = $cleaned[4] ?? '';

        $ean = '';
        $img = '';

        // Buscar EAN en columna 3
        if (preg_match('/EAN:\s*(\d+)/i', $eanField3, $matches)) {
            $ean = $matches[1];
            $img = $eanField4; // La columna 4 es la imagen
        }
        // Si no hay EAN en columna 3, buscarlo en columna 4
        elseif (preg_match('/EAN:\s*(\d+)/i', $eanField4, $matches)) {
            $ean = $matches[1];
            $img = ''; // No hay imagen en este caso
        }
        // Si ninguna columna tiene formato EAN, asumir que columna 4 es imagen
        else {
            $img = $eanField4;
        }

        return [
            'category' => $category,
            'description' => $description,
            'ean' => $ean,
            'img' => $img,
        ];
    }

    /**
     * Normaliza un string de price:
     * - Quita espacios y símbolos
     * - Si tiene punto y coma de miles y coma decimal: elimina puntos y convierte coma a punto
     * - Si solo tiene coma: la trata como decimal
     * - Devuelve float o null si no pudo parsear
     *
     * @param string $s
     * @return float|null
     */
    static function normalizePrice(string $s): ?float
    {
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
        if ($s === '' || $s === '-' || $s === '.') return null;
        // usar floatval es suficiente para la mayoría de casos
        $val = floatval($s);
        return $val;
    }

    function getCSVContent($limit = null)
    {
        $data  = Files::getContent('D:\\Desktop\\ZIPPY FILES\\exports\\coto.csv');
        $data  = Strings::fixEncoding($data);
        $rows  = Strings::lines($data, true, true);

        $ix = 0;
        $out = []; // filas parseadas que devolveremos

        foreach ($rows as $k => $line) {
            $row = static::parseCsvLineSpecial($line);
            $row = Strings::trim($row);

            if (Strings::isEmpty($row)) {
                // saltar filas vacías
                continue;
            }

            $out[] = $row; // añadir solo filas ya parseadas/limpias
            $ix++;

            // Si hay límite, detener cuando lleguemos al límite solicitado.
            // Uso >= para asegurar que con limit=3 devuelva 3 filas.
            if ($limit !== null && $ix >= $limit) break;
        }

        // reindexado numérico por seguridad
        return array_values($out);
    }

    function import_zippy_csv()
    {
        System::setMemoryLimit('8096M');

        $file = 'D:\\Desktop\\ZIPPY FILES\\products.csv';

        // Use the zippy connection while importing
        DB::setConnection('zippy');

        // counters
        global $count, $inserted, $skipped, $errors;
        $count = 0;
        $inserted = 0;
        $skipped = 0;
        $errors = 0;

        // model instance declared in outer scope so we can reuse it across rows
        $model_instance = null;

        // how often to free/recreate the model (set to 0 to never free)
        $reset_model_every = 5000;

        Files::processCSV($file, '|', true, function ($p) use (&$model_instance, $reset_model_every) {
            global $count, $inserted, $skipped, $errors;

            $count++;

            // --- Basic row filter ---
            if (empty($p['brand']) || $p['brand'] === 'SIN MARCA') {
                $skipped++;
                return;
            }

            // --- Normalize EAN ---
            $rawEan = $p['product-id'] ?? '';
            // remove everything that's not a digit
            $digits = preg_replace('/\D+/', '', $rawEan);

            if ($digits === '') {
                $skipped++;
                // optionally log reason: empty/invalid product-id
                // error_log("Skipping row $count: empty product-id");
                return;
            }

            // Enforce max length for EAN (13 digits expected for EAN-13)
            if (strlen($digits) > 13) {
                $errors++;
                // optional: log or save problematic EAN
                error_log("Row $count: product-id has >13 digits, skipping: '{$rawEan}' -> '{$digits}'");
                return;
            }

            // IMPORTANT: converting to integer will drop leading zeros.
            // Decide which behaviour you want:
            //  - if you NEED to preserve leading zeros (e.g. '0123456789012'), store as string in DB (column must accept numeric string or varchar).
            //  - if you truly want the numeric intval, continue and cast.
            //
            // The code below preserves numeric value but handles 32-bit PHP safely:
            if (PHP_INT_SIZE >= 8) {
                // 64-bit PHP: safe to cast up to 13 digits
                $ean_value = (int) $digits;
            } else {
                // 32-bit PHP: ints may overflow; keep numeric string and let MySQL cast when inserting into BIGINT
                $ean_value = $digits; // numeric string
            }

            $dato = [
                'ean' => $ean_value,
                'description' => $p['description'] ?? null,
                'net_content' => $p['net-content'] ?? null,
                'unit_of_measurement' => $p['unit-of-measurement'] ?? null,
                'brand' => $p['brand'],
                'created_at' => date('Y-m-d H:i:s'),
            ];

            try {
                // Reuse the model instance across rows to avoid reconnect/overhead
                // table() will create it on first use because we pass by reference.
                $id = table('products', $model_instance)->create($dato, true);

                if ($id === null) {
                    // create returned null -> duplicate (because you pass true to ignore duplicates)
                    $skipped++;
                } else {
                    $inserted++;
                }
            } catch (\Exception $e) {
                $errors++;
                error_log("Error inserting EAN '{$dato['ean']}' on row {$count}: " . $e->getMessage());
                // optionally: capture $dato or $p to a failed rows list for manual retry
            } finally {
                // free per-row heavy references (small arrays) to help GC
                $dato = null;

                // Optionally reset the model instance periodically to avoid long-running leaks or refresh schema
                if ($reset_model_every > 0 && $count % $reset_model_every === 0) {
                    // If model holds DB connections or large caches, unset and let GC run.
                    if (is_object($model_instance)) {
                        // if your MyModel has explicit cleanup, call it:
                        if (method_exists($model_instance, 'cleanup')) {
                            $model_instance->cleanup(); // user-defined cleanup (close statements, clear caches)
                        }
                        // remove reference
                        $model_instance = null;
                    }
                    // force collection
                    gc_collect_cycles();
                }
            }

            // periodic progress + minor GC
            if ($count % 500 === 0) {
                gc_collect_cycles();
                echo "Processed: $count | Inserted: $inserted | Skipped: $skipped | Errors: $errors\n";
            }
        }, null, 0, 99999999);

        // final cleanup
        if (is_object($model_instance) && method_exists($model_instance, 'cleanup')) {
            $model_instance->cleanup();
        }
        $model_instance = null;

        // close DB connection used for import
        DB::closeConnection();

        echo "Import finished. Processed: $count | Inserted: $inserted | Skipped: $skipped | Errors: $errors\n";
    }

    function check_dupes()
    {
        $file = 'D:\\Desktop\\ZIPPY FILES\\products.csv';

        DB::setConnection('zippy');

        global $eans;

        Files::processCSV($file, '|', true, function ($p) {
            global $eans;

            // $p contiene un array asociativo con los datos de cada fila
            // dd($p, 'P (por procesar)');

            $ean = intval($p['product-id']);

            if (isset($eans[$ean])) {
                echo "DUPLICATE EAN: $ean\n";
            } else {
                $eans[$ean] = true;
            }
        }, null, 0, 99999999);

        DB::closeConnection();
    }

    function index()
    {
        System::setMemoryLimit('4096M');

        $rows = $this->getCSVContent();

        // Import into products table

        DB::setConnection('zippy');

        foreach ($rows as $row) {
            $ean      = (int) $row['ean'];
            $img      = $row['img'] ?: null;
            $desc     = $row['description'] ?: null;
            $category = $row['category'] ?: null;

            if (empty($ean)) {
                // EAN inválido, saltar
                echo "Skipping row with empty/invalid EAN\n";
                continue;
            }

            if (table('products')->where('ean', $ean)->exists()) {
                // actualizar
                table('products')->where('ean', $ean)->update([
                    'description' => $desc,
                    'catego_raw1' => $category,
                    'img' => $img,
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);
                echo "Updated EAN: $ean\n";
            } else {
                // insertar
                table('products')->create([
                    'ean' => $ean,
                    'description' => $desc,
                    'catego_raw1' => $category,
                    'img' => $img,
                    'created_at' => date('Y-m-d H:i:s'),
                ], true); // true = ignore duplicates (shouldn't happen here)
                echo "Inserted EAN: $ean\n";


            }
        }

        DB::closeConnection('zippy');

        exit;

        // Generar CSV de salida
        $output = "categories,price,description,brand,ean,img,price\n";
        foreach ($rows as $row) {
            $line = [
                '"' . str_replace('"', '""', $row['categories']) . '"',
                $row['price'] !== null ? $row['price'] : '',
                '"' . str_replace('"', '""', $row['description']) . '"',
                '"' . str_replace('"', '""', $row['brand']) . '"',
                '"' . str_replace('"', '""', $row['ean']) . '"',
                '"' . str_replace('"', '""', $row['img']) . '"',
                $row['price'] !== null ? '"' . str_replace('"', '""', $row['price']) . '"' : ''
            ];
            $output .= implode(',', $line) . "\n";
        }

        // // Guardar en archivo
        // $outputFile = 'D:\\Desktop\\ZIPPY FILES\\vea_processed.csv';
        // $bytes = Files::writeOrFail($outputFile, $output);

        // echo "Processed " . count($rows) . " rows. Output saved to $outputFile (". (int) $bytes ." bytes).\n";

        

    }
}
