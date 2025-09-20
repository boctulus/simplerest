# Schema - Constructor de Esquemas de Base de Datos

También se ha agregado soporte para prefijos en Schema, la clase principal para manejo de migraciones.

## Schema Builder

Básicamente hay dos grandes funcionalidades que residen en la clase Schema: crear tablas y alterarlas.

- `Schema::create()` - Realiza un CREATE TABLE
- `Schema::change()` - Realiza un ALTER TABLE

Además `Schema::alter()` es un alias de `Schema::change()` por lo que pueden usarse indistintamente.

## Ejemplo de CREATE TABLE

Un "CREATE TABLE" dentro de una migración:

```php
function up()
{
    $sc = (new Schema('facturas'))

    ->setEngine('InnoDB')
    ->setCharset('utf8')
    ->setCollation('utf8_general_ci')

    ->integer('id')->auto()->unsigned()->pri()
    ->int('edad')->unsigned()
    ->varchar('firstname')
    ->varchar('lastname')->nullable()->charset('utf8')->collation('utf8_unicode_ci')
    ->varchar('username')->unique()
    ->varchar('password', 128)
    ->char('password_char')->nullable()
    ->varbinary('texto_vb', 300)

    // BLOB and TEXT columns cannot have DEFAULT values.
    ->text('texto')
    ->tinytext('texto_tiny')
    ->mediumtext('texto_md')
    ->longtext('texto_long')
    ->blob('codigo')
    ->tinyblob('blob_tiny')
    ->mediumblob('blob_md')
    ->longblob('blob_long')
    ->binary('bb', 255)
    ->json('json_str')

    ->int('karma')->default(100)
    ->int('code')->zeroFill()
    ->bigint('big_num')
    ->bigint('ubig')->unsigned()
    ->mediumint('medium')
    ->smallint('small')
    ->tinyint('tiny')
    ->decimal('saldo')
```

## Uso con prefijos

Ejemplo con prefijo:

```php
/**
* Run migration.
*
* @return void
*/
public function up()
{
    $sc = new Schema('test2');  // <-- el prefijo es agregado

    $sc
    ->integer('id')->auto()->pri()
    // ... resto de la definición
}
```

## Schemas en modelos

Usar un prefijo distinto del predeterminado ('') llevaría a tener que re-generar todos los schemas dado que hacen referencias a las tablas:

```php
class MigrationsSchema implements ISchema
{
    static function get(){
        return [
            'table_name' => 'migrations',  // <-- aquí
            //..
        ];
    }
}
```