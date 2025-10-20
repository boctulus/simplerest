<?php

$source_dir = 'D:\\laragon\\www\\simplerest\\app\\Models\\pos_laravel';
$dest_dir = 'D:\\laragon\\www\\simplerest\\packages\\boctulus\\friendlypos-web\\src\\Models';

if (!is_dir($dest_dir)) {
    mkdir($dest_dir, 0755, true);
}

$models = [
    'VentaModel',
    'VentaDetalleModel',
    'VentaDetalleItemExtraModel',
    'EmpresaProductoModel',
    'EmpresaProductoPackModel',
    'EmpresaConfiguracionModel',
    'EmpresaModel',
    'CarritoModel',
    'CarritoDetalleModel',
    'CarritoDetalleItemExtraModel',
    'CarritoDetallePackSeleccionableModel',
    'CajaVentaModel',
    'ClienteModel',
    'ClienteEmpresaModel',
    'CategoriaModel',
    'ImpuestoModel',
    'DocumentodteModel',
    'UsuarioModel',
    'TicketVentaModel',
    'TicketVentaDetalleModel',
    'TicketVentaDetalleItemExtraModel',
    'TicketVentaDetallePackSeleccionableModel'
];

$copied = 0;
foreach ($models as $model) {
    $src = "$source_dir\\$model.php";
    $dst = "$dest_dir\\$model.php";

    if (file_exists($src)) {
        $content = file_get_contents($src);

        // Cambiar namespace
        $content = str_replace(
            'namespace Boctulus\\Simplerest\\Models\\pos_laravel;',
            'namespace Boctulus\\FriendlyposWeb\\Models;',
            $content
        );

        // Cambiar MyModel
        $content = str_replace(
            'use Boctulus\\Simplerest\\Models\\MyModel;',
            'use simplerest\\core\\Model as MyModel;',
            $content
        );

        // Cambiar Schemas
        $content = str_replace(
            'use Boctulus\\Simplerest\\Schemas\\pos_laravel\\',
            'use simplerest\\schemas\\pos_laravel\\',
            $content
        );

        file_put_contents($dst, $content);
        echo "✓ $model\n";
        $copied++;
    } else {
        echo "✗ $model (no existe)\n";
    }
}

echo "\nCopiados: $copied modelos\n";
echo "Destino: $dest_dir\n";
