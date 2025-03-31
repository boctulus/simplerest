<?php

namespace Boctulus\Simplerest\Core\Libs;

/*
    Importar los archivos necesarios desde el plugin

    D:\www\woo7\wp-content\plugins\joinchat_m

    y crear package
*/
class GeoLite2
{
    /*
        Retorna el registro de la ubicacion mas cercana a esas coordenadas
        usando la fórmula de Haversine para calculo de distancia

        Requiere de las migraciones:

        2024_02_28_105363999_city_locations.php
        2024_02_28_105375597_city_blocks.php

        y de los "seeders":

        import_csv_city-blocks.php
        import_csv_city-locations.php

        Ej:
        
        $lat = 13.8529237;
        $lon = 120.9901801;

        // Muestra el resultado
        dd(
            GeoLite2::getLocation($lat, $lon)
        );
    */
    static function getLocation($lat, $lon) {
        DB::getConnection();

        // Calcula la distancia usando la fórmula de Haversine y ordena los resultados por distancia
        $closestLocation = DB::select("SELECT c.*, b.network,
        (6371 * acos(cos(radians(?)) * cos(radians(b.latitude)) * cos(radians(b.longitude) - radians(?)) + sin(radians(?)) * sin(radians(b.latitude)))) AS distance
        FROM `wp_city-locations` c
        INNER JOIN `wp_city-blocks` b ON c.geoname_id = b.geoname_id
        ORDER BY distance
        LIMIT 1", [
            $lat, $lon, $lat
        ], 'ASSOC', null, true);

        // Muestra el resultado
        return $closestLocation;
    }
}



