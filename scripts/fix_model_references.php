<?php

$directory = 'D:\\laragon\\www\\simplerest\\packages\\boctulus\\friendlypos-web\\src';

// Mapeo de nombres incorrectos a correctos
$replacements = [
    // Patrón Models\ con nombre sin Model suffix
    'use Boctulus\\FriendlyposWeb\\Models\\Ventas;' => 'use Boctulus\\FriendlyposWeb\\Models\\VentaModel as Ventas;',
    'use Boctulus\\FriendlyposWeb\\Models\\VentaDetalle;' => 'use Boctulus\\FriendlyposWeb\\Models\\VentaDetalleModel as VentaDetalle;',
    'use Boctulus\\FriendlyposWeb\\Models\\VentaDetalleItemExtra;' => 'use Boctulus\\FriendlyposWeb\\Models\\VentaDetalleItemExtraModel as VentaDetalleItemExtra;',
    'use Boctulus\\FriendlyposWeb\\Models\\EmpresaProducto;' => 'use Boctulus\\FriendlyposWeb\\Models\\EmpresaProductoModel as EmpresaProducto;',
    'use Boctulus\\FriendlyposWeb\\Models\\EmpresaProductoPack;' => 'use Boctulus\\FriendlyposWeb\\Models\\EmpresaProductoPackModel as EmpresaProductoPack;',
    'use Boctulus\\FriendlyposWeb\\Models\\CarritoCompras;' => 'use Boctulus\\FriendlyposWeb\\Models\\CarritoModel as CarritoCompras;',
    'use Boctulus\\FriendlyposWeb\\Models\\CarritoComprasDetalle;' => 'use Boctulus\\FriendlyposWeb\\Models\\CarritoDetalleModel as CarritoComprasDetalle;',
    'use Boctulus\\FriendlyposWeb\\Models\\CarritoComprasDetalleItemExtra;' => 'use Boctulus\\FriendlyposWeb\\Models\\CarritoDetalleItemExtraModel as CarritoComprasDetalleItemExtra;',
    'use Boctulus\\FriendlyposWeb\\Models\\CarritoComprasDetallePackSeleccionable;' => 'use Boctulus\\FriendlyposWeb\\Models\\CarritoDetallePackSeleccionableModel as CarritoComprasDetallePackSeleccionable;',
    'use Boctulus\\FriendlyposWeb\\Models\\CajaVenta;' => 'use Boctulus\\FriendlyposWeb\\Models\\CajaVentaModel as CajaVenta;',
    'use Boctulus\\FriendlyposWeb\\Models\\Cliente;' => 'use Boctulus\\FriendlyposWeb\\Models\\ClienteModel as Cliente;',
    'use Boctulus\\FriendlyposWeb\\Models\\ClienteEmpresa;' => 'use Boctulus\\FriendlyposWeb\\Models\\ClienteEmpresaModel as ClienteEmpresa;',
    'use Boctulus\\FriendlyposWeb\\Models\\Categoria;' => 'use Boctulus\\FriendlyposWeb\\Models\\CategoriaModel as Categoria;',
    'use Boctulus\\FriendlyposWeb\\Models\\Impuestos;' => 'use Boctulus\\FriendlyposWeb\\Models\\ImpuestoModel as Impuestos;',
    'use Boctulus\\FriendlyposWeb\\Models\\TipoDte;' => 'use Boctulus\\FriendlyposWeb\\Models\\DocumentodteModel as TipoDte;',
    'use Boctulus\\FriendlyposWeb\\Models\\User;' => 'use Boctulus\\FriendlyposWeb\\Models\\UsuarioModel as User;',
    'use Boctulus\\FriendlyposWeb\\Models\\Producto;' => 'use Boctulus\\FriendlyposWeb\\Models\\ProductoModel as Producto;',
    'use Boctulus\\FriendlyposWeb\\Models\\Marcas;' => 'use Boctulus\\FriendlyposWeb\\Models\\MarcasModel as Marcas;',
    'use Boctulus\\FriendlyposWeb\\Models\\TicketVenta;' => 'use Boctulus\\FriendlyposWeb\\Models\\TicketVentaModel as TicketVenta;',

    // Patrón sin Models\ subdirectory - agregar Models\
    'use Boctulus\\FriendlyposWeb\\VentaModel as Ventas;' => 'use Boctulus\\FriendlyposWeb\\Models\\VentaModel as Ventas;',
    'use Boctulus\\FriendlyposWeb\\VentaDetalleModel as VentaDetalle;' => 'use Boctulus\\FriendlyposWeb\\Models\\VentaDetalleModel as VentaDetalle;',
    'use Boctulus\\FriendlyposWeb\\EmpresaProductoModel as EmpresaProducto;' => 'use Boctulus\\FriendlyposWeb\\Models\\EmpresaProductoModel as EmpresaProducto;',
    'use Boctulus\\FriendlyposWeb\\EmpresaConfiguracionModel;' => 'use Boctulus\\FriendlyposWeb\\Models\\EmpresaConfiguracionModel;',
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

echo "Actualizando referencias de modelos...\n\n";
$updated = scanDirectory($directory, $replacements);
echo "\n✅ Archivos actualizados: $updated\n";
