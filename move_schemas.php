<?php

$src_dir = 'D:\\laragon\\www\\simplerest\\app\\Schemas\\laravel_pos';
$dst_dir = 'D:\\laragon\\www\\simplerest\\packages\\boctulus\\friendlypos-web\\src\\Schemas';

// Crear directorio destino
if (!is_dir($dst_dir)) {
    mkdir($dst_dir, 0755, true);
}

// Obtener todos los archivos .php
$files = glob("$src_dir/*.php");

$moved = 0;
foreach ($files as $file) {
    $filename = basename($file);
    $dst_file = "$dst_dir\\$filename";

    $content = file_get_contents($file);

    // Cambiar namespace
    $content = str_replace(
        'namespace simplerest\\schemas\\laravel_pos;',
        'namespace Boctulus\\FriendlyposWeb\\Schemas;',
        $content
    );

    file_put_contents($dst_file, $content);
    echo "✓ $filename\n";
    $moved++;
}

echo "\n✅ Schemas movidos: $moved\n";
echo "Destino: $dst_dir\n";
