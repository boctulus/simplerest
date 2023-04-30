<?php

namespace simplerest\controllers\tests;

use simplerest\core\libs\DB;
use simplerest\core\Request;
use simplerest\core\Response;
use simplerest\core\libs\Schema;
use simplerest\core\libs\Factory;
use simplerest\controllers\MyController;

class SchemaController extends MyController
{
    /*

        https://www.w3resource.com/mysql/mysql-data-types.php
        https://manuales.guebs.com/mysql-5.0/spatial-extensions.html

    */
    function create_table()
    {
        //config()['db_connection_default'] = 'db2';
        $sc = (new Schema('facturas'))

            ->setEngine('InnoDB')
            ->setCharset('utf8')
            ->setCollation('utf8mb4_unicode_ci')

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
            ->float('flotante')
            ->double('doble_p')
            ->real('num_real')

            ->bit('some_bits', 3)->index()
            ->boolean('is_active')->default(1)
            ->boolean('paused')->default(true)

            ->set('flavors', ['strawberry', 'vanilla'])
            ->enum('role', ['admin', 'normal'])


            /*
            The major difference between DATETIME and TIMESTAMP is that TIMESTAMP values are converted from the current time zone to UTC while storing, and converted back from UTC to the current time zone when accessd. The datetime data type value is unchanged.
        */

            ->time('hora')
            ->year('birth_year')
            ->date('fecha')
            ->datetime('vencimiento')->nullable()->after('num_real') /* no está funcionando el AFTER */
            ->timestamp('ts')->currentTimestamp()->comment('some comment') // solo un first


            ->softDeletes() // agrega DATETIME deleted_at 
            ->datetimes()  // agrega DATETIME(s) no-nullables created_at y deleted_at

            ->varchar('correo')->unique()

            ->int('user_id')->index()
            ->foreign('user_id')->references('id')->on('users')->onDelete('cascade')
            //->foreign('user_id')->references('id')->on('users')->constraint('fk_uid')->onDelete('cascade')->onUpdate('restrict')

        ;

        //dd($sc->getSchema(), 'SCHEMA');
        /////exit;

        $res = $sc->create();
        dd($res, 'Succeded?');
        //var_dump($sc->dd());
    }

    function alter_table()
    {
        Schema::FKcheck(false);

        $sc = new Schema('facturas');
        //var_dump($sc->columnExists('correo'));

        $res = $sc


            //->timestamp('vencimiento')
            //->varchar('lastname', 50)->collate('utf8_esperanto_ci')
            //->varchar('username', 50)
            //->column('ts')->nullable()
            //->field('deleted_at')->nullable()
            //->column('correo')->unique()
            // ->field('correo')->default(false)->nullable(true)


            //->renameColumn('karma', 'carma')
            ->field('id')->index()
            //->renameIndex('id', 'idx')
            //->dropColumn('saldo')
            //->dropIndex('correo')
            //->dropPrimary('id')
            //->renameTable('boletas')
            //->dropTable()

            //->field('password_char')->default(false)->nullable(false)


            /*
         creo campos nuevos
        */

            //->varchar('nuevo_campito', 50)->nullable()->after('ts')
            //->text('aaa')->first()->nullable()

            //->dropFK('facturas_ibfk_1')
            //->foreign('user_id')->references('id')->on('users')->onDelete('cascade')->onUpdate('restrict')

            //->field('id')->auto(false)


            ->change();

        Schema::FKcheck(true);
        dd($sc->dd());
    }

    function debug_migration(){
        $sc = new Schema('rBLbtSeq_sinergia_queue');

        $sc
        ->int('id', 11)->primary()->auto()
        ->int('order_id', 11)->notNullable()->unique()
        ->varchar('status', 20)->nullable()
        ->datetime('datetime')->currentTimestamp()->notNullable()
        ->dontExec()
        ->create();

        /*
            Debugging
        */

        dd($sc->getSchema(), 'SCHEMA');
        dd($sc->dd(true), 'SQL');
    }

    function has_table()
    {
        dd(Schema::hasTable('users'));
        dd((new Schema('users'))->tableExists());
    }

    function get_schema()
    {
        DB::setConnection('parts');
        
        dd(
            Schema::getTables()
        , 'TABLES');
    }

    function test_get_rels()
    {
        $table = 'books';

        $relations = Schema::getRelations($table);
        dd($relations);
    }

    function rr()
    {
        // DB::getConnection('db_flor');
        // $rels = Schema::getRelations('tbl_estado_civil');

        DB::getConnection('az');
        $rels = Schema::getRelations('books');

        dd($rels);
    }

    function rels()
    {
        // DB::getConnection('db_flor');
        // $rels = Schema::getAllRelations('tbl_estado_civil', false);

        // DB::getConnection('az');
        // $rels = Schema::getAllRelations('books', false);   

        DB::getConnection('db_flor');
        $rels = Schema::getAllRelations('tbl_sub_cuenta_contable', false);

        dd($rels);
    }

}

