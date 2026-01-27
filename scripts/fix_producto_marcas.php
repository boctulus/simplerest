<?php

$directory = 'D:\\laragon\\www\\simplerest\\packages\\boctulus\\friendlypos-web\\src';

$replacements = [
    'use Boctulus\\FriendlyposWeb\\Models\\ProductoModel as Producto;' => 'use Boctulus\\FriendlyposWeb\\Models\\ArticuloModel as Producto;',
    'use Boctulus\\FriendlyposWeb\\Models\\MarcasModel as Marcas;' => 'use Boctulus\\FriendlyposWeb\\Models\\EmpresaMarcaModel as Marcas;',
];

function updateFile($file, $replacements) {
    $content = file_get_contents($file);
    $original = $content;

    foreach ($replacements as $search => $replace) {
        $content = str_replace($search, $replace, $content);
    }

    if ($content !== $original) {
        file_put_contents($file, $content);
        return true;
    }

    return false;
}

function scanDirectory($dir, $replacements) {
    $updated = 0;
    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS),
        RecursiveIteratorIterator::SELF_FIRST
    );

    foreach ($iterator as $file) {
        if ($file->isFile() && $file->getExtension() === 'php') {
            if (updateFile($file->getPathname(), $replacements)) {
                echo "✓ " . substr($file->getPathname(), strlen($dir) + 1) . "\n";
                $updated++;
            }
        }
    }

    return $updated;
}

echo "Actualizando ProductoModel → ArticuloModel y MarcasModel → EmpresaMarcaModel...\n\n";
$updated = scanDirectory($directory, $replacements);
echo "\n✅ Archivos actualizados: $updated\n";
