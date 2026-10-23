# SimpleRest Geolocation

`boctulus/simplerest-geolocation` contains two location helpers:

- `GoogleMaps` geocodes an address through the Google Maps API.
- `GeoLite2` looks up coordinates in the host application's GeoLite2 tables.

The classes retain their existing `Boctulus\Simplerest\Core\Libs`
namespaces. `GoogleMaps` reads its API key from SimpleRest configuration unless
one is passed to its constructor. `GeoLite2` expects the database and tables to
be provisioned by the consuming application; this package does not ship the
GeoLite2 dataset or its schema.
