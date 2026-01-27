# Comando PackCommand

Se ha creado un comando `php com pack` cuyo objetivo es hacer una copia limpia de SimpleRest framework a otro directorio donde sera testeado, sufrira limpiezas adicionales en cuarentena antes de ser distribuido.

# Responsabilidades

Entre otras tareas el comando `php com pack` tiene como responsabilidades:

- Copiar los archivos relevantes asegurandose ignorar al copiar cualquier archivo definido en .cpignore

- Debe hacer un "composer install" en destino

- Al ejecutarse 'php com pack' se debe asegurar que en destino (`..\simplerest-pack\`) funcione:

	* El comando `php ..\simplerest-pack\com help` (eso implica copiar tambien "com")

	* El script `php ..\simplerest-pack\runalltests.php`  (no deberia mostrar errores y deberia leerse "All tests passed!" )

	* Al hacer un curl a "http://simplerest.test/" no deberia leerse "Error" o "Exception"

El propio comando deberia hacer las verificaciones pertinentes en destino.