<?php

/**
 * Busca el BOM en un archivo y opcionalmente lo elimina.
 *
 * @param string $filePath
 * @param bool $fix
 */
function checkAndFixBOM(string $filePath, bool $fix = false): void
{
    $bom = "\xEF\xBB\xBF";

    $content = @file_get_contents($filePath);
    if ($content === false) {
        return;
    }

    if (substr($content, 0, 3) === $bom) {
        echo "BOM encontrado en: $filePath, Línea: 1\n";

        if ($fix) {
            $contentWithoutBOM = substr($content, 3);

            if (@file_put_contents($filePath, $contentWithoutBOM) !== false) {
                echo "BOM eliminado de: $filePath\n";
            } else {
                echo "Error: No se pudo escribir el archivo: $filePath\n";
            }
        }
    }
}

/**
 * Escanea un directorio recursivamente.
 *
 * @param string $directoryPath
 * @param bool $fix
 */
function scanDirectoryForBOM(string $directoryPath, bool $fix = false): void
{
    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($directoryPath, FilesystemIterator::SKIP_DOTS)
    );

    foreach ($iterator as $file) {
        if ($file->isFile()) {
            checkAndFixBOM($file->getPathname(), $fix);
        }
    }
}

/* =========================
   CLI argument parsing
   ========================= */

$fix = in_array('--fix', $argv, true);

$path = null;
foreach ($argv as $arg) {
    if ($arg !== $argv[0] && $arg !== '--fix') {
        $path = $arg;
        break;
    }
}

if (!$path || !file_exists($path)) {
    echo "Error: Debes especificar una ruta válida.\n";
    exit(1);
}

if (is_file($path)) {
    checkAndFixBOM($path, $fix);
} elseif (is_dir($path)) {
    scanDirectoryForBOM($path, $fix);
} else {
    echo "Error: La ruta especificada no es válida.\n";
    exit(1);
}
