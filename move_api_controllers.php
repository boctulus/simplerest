<?php

$src_dir = 'D:\\laragon\\www\\simplerest\\app\\Controllers\\api';
$dst_dir = 'D:\\laragon\\www\\simplerest\\packages\\boctulus\\friendlypos-web\\src\\Controllers\\Api';

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
        'namespace boctulus\\simplerest\\controllers\\api;',
        'namespace Boctulus\\FriendlyposWeb\\Controllers\\Api;',
        $content
    );

    // Cambiar referencias a schemas
    $content = str_replace(
        'use simplerest\\schemas\\pos_laravel\\',
        'use Boctulus\\FriendlyposWeb\\Schemas\\',
        $content
    );

    // Cambiar referencias a models
    $content = str_replace(
        'use Boctulus\\Simplerest\\Models\\pos_laravel\\',
        'use Boctulus\\FriendlyposWeb\\Models\\',
        $content
    );

    file_put_contents($dst_file, $content);
    echo "✓ $filename\n";
    $moved++;
}

echo "\n✅ Controladores API movidos: $moved\n";
echo "Destino: $dst_dir\n";
