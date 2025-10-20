<?php

// Script para copiar modelos al package friendlypos-web

$source_dir = 'D:\\laragon\\www\\simplerest\\app\\Models\\pos_laravel';
$dest_dir = 'D:\\laragon\\www\\simplerest\\packages\\boctulus\\friendlypos-web\\src\\Models';

// Crear directorio de destino si no existe
if (!is_dir($dest_dir)) {
    mkdir($dest_dir, 0755, true);
}

// Lista de modelos necesarios (basado en el análisis del código)
$models_needed = [
    'VentaModel',
    'VentaDetalleModel',
    'VentaDetalleItemExtraModel',
    'EmpresaProductoModel',
    'EmpresaProductoPackModel',
    'EmpresaConfiguracionModel',
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
    'EmpresaModel',
    'UsuarioModel',
    'TicketVentaModel',
    'TicketVentaDetalleModel'
];

$copied = 0;
$errors = [];

foreach ($models_needed as $model_name) {
    $source_file = "$source_dir\\{$model_name}.php";
    $dest_file = "$dest_dir\\{$model_name}.php";

    if (!file_exists($source_file)) {
        $errors[] = "No existe: $source_file";
        continue;
    }

    $content = file_get_contents($source_file);

    // Cambiar namespace
    $content = str_replace(
        'namespace Boctulus\\Simplerest\\Models\\pos_laravel;',
        'namespace Boctulus\\FriendlyposWeb\\Models;',
        $content
    );

    // Cambiar referencias a MyModel
    $content = str_replace(
        'use Boctulus\\Simplerest\\Models\\MyModel;',
        'use simplerest\\core\\Model as MyModel;',
        $content
    );

    // Cambiar referencias a Schemas
    $content = str_replace(
        'use Boctulus\\Simplerest\\Schemas\\pos_laravel\\',
        'use simplerest\\schemas\\pos_laravel\\',
        $content
    );

    file_put_contents($dest_file, $content);
    $copied++;
    echo "✓ Copiado: $model_name\n";
}

echo "\n=== RESUMEN ===\n";
echo "Modelos copiados: $copied\n";

if (!empty($errors)) {
    echo "\nErrores:\n";
    foreach ($errors as $error) {
        echo "  - $error\n";
    }
}

echo "\nModelos creados en: $dest_dir\n";
