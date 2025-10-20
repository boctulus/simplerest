<?php

/**
 * Helper: package.php
 * 
 * Autodiscovery de packages
 *
 * Provee utilidades para obtener la lista de packages/{author}/{package}/ de paquetes 
 * en cualquier ubicacion (vendor/, packages/, etc)
 *
 * Función exportada:
 *   - get_all_packages(): array of absolute paths (each ends with DIRECTORY_SEPARATOR)
 *
 * Ubicación recomendada:
 *   app/Core/Helpers/package.php
 *
 * Se asume que este archivo será cargado automáticamente desde app.php
 * 
 * TODO: podria cambiarse la forma en la que el autodiscovery trabaja o 
 * hacer cambiar la persistencia de la cache por otro mecanismo
 * 
 * https://chatgpt.com/c/68efc510-2248-8321-a90c-08e06332487f
 */

if (!function_exists('get_packages')) {
    /**
     * Busca el directorio "packages" y devuelve un array con todas las rutas
     * packages/{author}/{package}/ como rutas absolutas normalizadas.
     * 
     * Cachea los resultados en memoria
     *
     * @return string[] Array de rutas absolutas (cada ruta termina con DIRECTORY_SEPARATOR)
     * 
     * Ej:
     * 
     * [
     *   'D:\\laragon\\www\\simplerest\\packages\\boctulus\\web-test\\',
     *   'D:\\laragon\\www\\simplerest\\packages\\boctulus\\zippy\\',
     *   'D:\\laragon\\www\\simplerest\\packages\\devdojo\\calculator\\',
     * ]
     */
    function get_packages($base = null): array
    {
        static $package_paths = [];

        if ($base === null){
            $base = PACKAGES_PATH;
        }

        if (!empty($package_paths) && isset($package_paths[$base])){
            return $package_paths[$base];
        }

        $abs_base = realpath($base);
        $package_paths[$base] = package_scan_base($abs_base);

        return $package_paths[$base];
    }
}

if (!function_exists('get_packages_from_all_sources')) {
    function get_packages_from_all_sources(): array
    {
        static $packages = [];

        if (!empty($packages)){
            return $packages;
        }

        $packages = array_merge(
            package_scan_base(PACKAGES_PATH),
            package_scan_base(VENDOR_PATH)
        );
        
        // Opcional: ordenar
        sort($packages, SORT_NATURAL | SORT_FLAG_CASE);
        
        return $packages;
    }
}

if (!function_exists('package_scan_base')) {
    /**
     * Escanea el directorio base de packages y devuelve todas las rutas packages/{author}/{pkg}/
     *
     * @param string $packagesBase absolute path to packages or vendor directory
     * @param array $options [
     *     'resolve_symlinks' => true,  // si true usa realpath() (resuelve symlinks)
     *     'dedupe' => true,            // elimina duplicados por ruta real
     *     'only_under_base' => false,  // si true filtra solo rutas cuyo realpath está bajo $packagesBase
     * ]
     * @return string[]
     */
    function package_scan_base(string $packagesBase, array $options = []): array
    {
        $options = array_merge([
            'resolve_symlinks' => true,
            'dedupe' => true,
            'only_under_base' => true,
        ], $options);

        $packages = [];

        // Normalizar
        $packagesBase = rtrim($packagesBase, DIRECTORY_SEPARATOR);
        $packagesBaseLower = strtolower($packagesBase);

        // Primer nivel: autores
        $authorDirs = @glob($packagesBase . DIRECTORY_SEPARATOR . '*', GLOB_ONLYDIR) ?: [];

        foreach ($authorDirs as $authorDir) {
            // Segundo nivel: paquetes dentro del author
            $pkgDirs = @glob($authorDir . DIRECTORY_SEPARATOR . '*', GLOB_ONLYDIR) ?: [];
            foreach ($pkgDirs as $pkgDir) {
                // Si queremos preservar la ruta tal cual (sin resolver symlink) usamos $pkgDir.
                if ($options['resolve_symlinks']) {
                    $real = @realpath($pkgDir);
                } else {
                    $real = $pkgDir;
                }

                if (!$real || !is_dir($real)) {
                    continue;
                }

                // Aseguro barra final
                $path = rtrim($real, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;

                // Si pedimos solo rutas cuyo realpath esté bajo $packagesBase, filtramos
                if ($options['only_under_base']) {
                    $cmp = strtolower(rtrim($path, DIRECTORY_SEPARATOR));
                    if (strpos($cmp, $packagesBaseLower) !== 0) {
                        // no está bajo el base solicitado
                        continue;
                    }
                }

                $packages[] = $path;
            }
        }

        // Dedupe si hace falta (basado en ruta final)
        if ($options['dedupe']) {
            // normalizar mayúsculas/minúsculas para Windows
            $seen = [];
            $unique = [];
            foreach ($packages as $p) {
                $key = strtolower($p);
                if (!isset($seen[$key])) {
                    $seen[$key] = true;
                    $unique[] = $p;
                }
            }
            $packages = $unique;
        }

        // Orden determinista
        sort($packages, SORT_NATURAL | SORT_FLAG_CASE);

        return $packages;
    }
}

if (!function_exists('find_package_by_name')) {
    /**
     * Busca un package por su nombre (slug) en todos los packages disponibles
     *
     * @param string $package_name Nombre del package (slug), ej: "web-test", "zippy"
     * @param string $base_path Base path donde buscar (null = PACKAGES_PATH)
     * @return array|null Array con info del package o null si no se encuentra
     *
     * Retorna:
     * [
     *   'path' => 'D:\\laragon\\www\\simplerest\\packages\\boctulus\\web-test\\',
     *   'author' => 'boctulus',
     *   'name' => 'web-test',
     *   'namespace' => 'Boctulus\\WebTest',  // extraído de composer.json
     *   'full_name' => 'boctulus/web-test'
     * ]
     */
    function find_package_by_name(string $package_name, ?string $base_path = null): ?array
    {
        $packages = get_packages($base_path);

        foreach ($packages as $package_path) {
            // Extraer author y name del path
            // Ej: D:\laragon\www\simplerest\packages\boctulus\web-test\
            $parts = explode(DIRECTORY_SEPARATOR, rtrim($package_path, DIRECTORY_SEPARATOR));

            // Las últimas 2 partes son author/package
            if (count($parts) < 2) {
                continue;
            }

            $pkg_name = array_pop($parts);
            $author = array_pop($parts);

            // Comparar el nombre del package (case-insensitive)
            if (strcasecmp($pkg_name, $package_name) === 0) {
                // Intentar extraer namespace del composer.json
                $composer_path = $package_path . 'composer.json';
                $namespace = null;

                if (file_exists($composer_path)) {
                    $composer = json_decode(file_get_contents($composer_path), true);
                    if (isset($composer['autoload']['psr-4'])) {
                        // Tomar el primer namespace definido
                        $namespaces = array_keys($composer['autoload']['psr-4']);
                        if (!empty($namespaces)) {
                            $namespace = rtrim($namespaces[0], '\\');
                        }
                    }
                }

                // Si no se pudo extraer del composer.json, inferir del author/package
                if (empty($namespace)) {
                    $namespace = ucfirst(str_replace(['-', '_'], '', ucwords($author, '-_'))) . '\\' .
                                 ucfirst(str_replace(['-', '_'], '', ucwords($pkg_name, '-_')));
                }

                return [
                    'path' => $package_path,
                    'author' => $author,
                    'name' => $pkg_name,
                    'namespace' => $namespace,
                    'full_name' => "$author/$pkg_name"
                ];
            }
        }

        return null;
    }
}

if (!function_exists('find_package_by_full_name')) {
    /**
     * Busca un package por su nombre completo author/package
     *
     * @param string $full_name Nombre completo, ej: "boctulus/web-test"
     * @param string $base_path Base path donde buscar (null = PACKAGES_PATH)
     * @return array|null Array con info del package o null si no se encuentra
     */
    function find_package_by_full_name(string $full_name, ?string $base_path = null): ?array
    {
        $parts = explode('/', $full_name);
        if (count($parts) !== 2) {
            return null;
        }

        list($author, $package_name) = $parts;

        $packages = get_packages($base_path);

        foreach ($packages as $package_path) {
            $path_parts = explode(DIRECTORY_SEPARATOR, rtrim($package_path, DIRECTORY_SEPARATOR));

            if (count($path_parts) < 2) {
                continue;
            }

            $pkg_name = array_pop($path_parts);
            $pkg_author = array_pop($path_parts);

            // Comparar author y package (case-insensitive)
            if (strcasecmp($pkg_author, $author) === 0 && strcasecmp($pkg_name, $package_name) === 0) {
                // Extraer namespace del composer.json
                $composer_path = $package_path . 'composer.json';
                $namespace = null;

                if (file_exists($composer_path)) {
                    $composer = json_decode(file_get_contents($composer_path), true);
                    if (isset($composer['autoload']['psr-4'])) {
                        $namespaces = array_keys($composer['autoload']['psr-4']);
                        if (!empty($namespaces)) {
                            $namespace = rtrim($namespaces[0], '\\');
                        }
                    }
                }

                if (empty($namespace)) {
                    $namespace = ucfirst(str_replace(['-', '_'], '', ucwords($pkg_author, '-_'))) . '\\' .
                                 ucfirst(str_replace(['-', '_'], '', ucwords($pkg_name, '-_')));
                }

                return [
                    'path' => $package_path,
                    'author' => $pkg_author,
                    'name' => $pkg_name,
                    'namespace' => $namespace,
                    'full_name' => "$pkg_author/$pkg_name"
                ];
            }
        }

        return null;
    }
}

