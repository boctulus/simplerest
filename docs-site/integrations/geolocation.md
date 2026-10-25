---

title: "Geolocalización"
source:
  - docs/integrations/geolocation.md
source_revision: 379f2383edbe4978e934195d6a6ed4102651d154
status: partial
---

# Geolocalización

`boctulus/simplerest-geolocation` contiene dos resolutores distintos: una consulta remota a Google Maps y una consulta SQL local a datos GeoLite2. El manifiesto no declara SDK externo ni crea el esquema o los datos locales.

## Google Maps

`GoogleMaps` recibe una API key opcional; si no se pasa, lee `google_maps_api_key` de la configuración de SimpleRest. `getCoordinates($address)` arma una solicitud HTTPS al endpoint de geocoding y, ante una respuesta `OK`, devuelve `['lat' => ..., 'lon' => ...]`. Para otros estados de proveedor no garantiza una forma de error estable; el código puede terminar devolviendo `null`.

El camino que usa `ApiClient` llama a `disableSSL()` antes de `get()`, así que no establece verificación de certificado TLS. El camino con `allow_url_fopen` usa `file_get_contents()` y tampoco define en esta clase timeout o mapeo de errores HTTP. La clave viaja como parámetro de URL. No uses este helper como validación de dirección ni registres la URL completa con la key. Revisa transporte, timeout, credenciales y manejo de errores antes de producción.

## GeoLite2 local

`GeoLite2::getLocation($lat, $lon)` calcula la distancia Haversine y devuelve el registro más cercano desde las tablas `wp_city-locations` y `wp_city-blocks`. El README del paquete enumera migraciones y seeders CSV que pertenecen a la aplicación; el paquete no provisiona la base GeoLite2. La conexión activa debe apuntar a la base que contiene esas tablas.

No se envió una petición a Google ni se consultó una base de datos en esta revisión. Los nombres de tablas, cargas de datos, conexión y resultados no se verificaron en una aplicación consumidora.
