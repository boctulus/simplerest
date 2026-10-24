# Seeder de datos AZ

`boctulus/simplerest-az-seeder` es una utilidad de desarrollo para un esquema que contiene `users` y `products`. Requiere SimpleRest `^2.0` solamente; su manifiesto no declara compatibilidad con core `3.0.0`.

## API observada

`AZSeederService` recibe un nombre de conexión, por defecto `az`, y ofrece `seedProducts($count)`, `seedProduct($overrides)`, `seedSoftDeletedProducts($count)`, `countProducts()`, `cleanup()` y `cleanupAll()`.

El seeder busca en la conexión `main` un usuario con el email de prueba definido por la clase y cambia la conexión de DB antes de escribir productos. `cleanup()` borra sólo los IDs que conserva esa instancia; `cleanupAll()` borra productos cuyo `belongs_to` coincide con ese usuario. `seedProduct()` acepta overrides para los campos de producto. El paquete no crea ni valida las tablas, claves foráneas o conexión.

## Límite de datos

Estas operaciones son escrituras y borrados reales si se apunta a una base conectada. Configura y verifica ambas conexiones en una base de pruebas desechable, con los datos de usuario y esquema esperados. No ejecutes `cleanupAll()` en producción ni compartas su usuario de prueba con datos que deban conservarse. En esta revisión sólo se inspeccionó el código; no se abrió conexión ni se insertó o borró dato alguno.
