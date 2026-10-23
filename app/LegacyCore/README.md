# Código legacy fuera del microcore

Este directorio conserva 239 archivos que salieron de `src/framework/` durante la reducción del núcleo del 23 de octubre de 2026. Mantiene las rutas relativas originales para facilitar su revisión y una extracción posterior por responsabilidad.

Composer no autoload este directorio y `.gitattributes` lo excluye de los artefactos del paquete `boctulus/simplerest`. No es una extensión instalable ni un mecanismo de fallback. Antes de distribuir una capacidad, debe migrarse a un paquete con namespace, dependencias, documentación y pruebas propias; el código que no supere esa revisión puede eliminarse en una tarea posterior.
