<?php

$models = ['ArticuloModel', 'EmpresaMarcaModel', 'TipoProductoModel', 'ArticuloSeleccionableModel'];
$src_dir = 'D:\\laragon\\www\\simplerest\\app\\Models\\pos_laravel';
$dst_dir = 'D:\\laragon\\www\\simplerest\\packages\\boctulus\\friendlypos-web\\src\\Models';

foreach ($models as $model) {
    $src = "$src_dir\\$model.php";
    $dst = "$dst_dir\\$model.php";

    if (file_exists($src)) {
        $content = file_get_contents($src);
        $content = str_replace('namespace Boctulus\\Simplerest\\Models\\pos_laravel;', 'namespace Boctulus\\FriendlyposWeb\\Models;', $content);
        $content = str_replace('use Boctulus\\Simplerest\\Models\\MyModel;', 'use simplerest\\core\\Model as MyModel;', $content);
        $content = str_replace('use Boctulus\\Simplerest\\Schemas\\pos_laravel\\', 'use simplerest\\schemas\\pos_laravel\\', $content);
        file_put_contents($dst, $content);
        echo "✓ $model\n";
    } else {
        echo "✗ $model (no existe)\n";
    }
}

echo "\nListo!\n";
