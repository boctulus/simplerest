<?php

namespace Boctulus\Simplerest\Controllers;

use Boctulus\Simplerest\Core\Controllers\Controller;
use Boctulus\Simplerest\Core\Libs\DB;
use Boctulus\Simplerest\Core\Libs\PostmanGenerator;
use Boctulus\Simplerest\Core\Libs\Strings;
use Boctulus\Simplerest\Core\Traits\TimeExecutionTrait;
use Boctulus\Simplerest\Libs\LaravelApiGenerator;

/*
    Pasos:

    1) Ir a databases.php y agregar la conexion a la base de datos

    2) Ejectuar el comando para generar el schema

        php com make schema all --from:laravelshopify --except=migrations,password_resets,cache,cache_locks,charges,failed_jobs,jobs,job_batches,migratons,password_reset_tokens,password_reset_tokens,sessions,plans

    3) Ajustar la clase y ejecutar

    4) Copiar las rutas entregadas por el comando a web.php, Ej:

    routes/api.php | routes

    Route::resource('addresses', App\Http\Controllers\AddressController::class);
    Route::resource('cart_items', App\Http\Controllers\CartItemController::class);
    Route::resource('carts', App\Http\Controllers\CartController::class);
    ...
*/
class CristianLaravelApiController extends Controller
{
    protected $conn_id = 'laravelshopify';
    protected $laravel_project_path = 'D:/laragon/www/laravel-shopify';

    function base() {
        LaravelApiGenerator::setConnId($this->conn_id);
    
        $base_path        = $this->laravel_project_path;
        $controllers_path = "$base_path/app/Http/Controllers";
        $resources_path   = "$base_path/app/Http/Resources";
        
        LaravelApiGenerator::setProjectPath($base_path);
        LaravelApiGenerator::setControllerDestPath($controllers_path);
        LaravelApiGenerator::setResourceDestPath($resources_path);
        
        // LaravelApiGenerator::writeFactories(false);
        // LaravelApiGenerator::writeSeeders(false);
        // LaravelApiGenerator::writeModels(true);
        LaravelApiGenerator::writeControllers(true);
        LaravelApiGenerator::writeResources(true);
        LaravelApiGenerator::writeRoutes(true);

        LaravelApiGenerator::setControllerBlacklist([
            'Products',
            'Address',
            'Cart',
            'Order',
            'Favorite',
            'PriceRule',
            'User'
            // ...
        ]);

        LaravelApiGenerator::setControllerWhitelist([
            'OrderStatus'       
        ]);

        LaravelApiGenerator::run();
    }
    

    function generate_collections(){
        PostmanGenerator::setCollectionName('LaravelShopify');

        PostmanGenerator::setDestPath($this->laravel_project_path . DIRECTORY_SEPARATOR . 'PostmanCollections');

        //PostmanGenerator::setBaseUrl('http://127.0.0.1:8889'); 
        PostmanGenerator::setBaseUrl('{{base_url}}'); 

        PostmanGenerator::setSegment('api');

        PostmanGenerator::setToken('{{token}}');

        // Agregar los headers globales
        PostmanGenerator::addHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json'
        ]);

        // PostmanGenerator::registerUser(['name', 'email', 'password']);
        PostmanGenerator::registerUser();
        PostmanGenerator::loginUser();

        PostmanGenerator::addEndpoints([  
            'users',                      
            'carts',
            'cart_items',
            'products',
            'addresses',
            'orders',
            'order_items',
            'favorites',
            'price_rules',
            'inventory',
            'order_status'        
        ], [
            PostmanGenerator::GET,
            PostmanGenerator::POST,
            PostmanGenerator::PATCH,
            PostmanGenerator::DELETE,
        ]);

        // PostmanGenerator::addEndpoints([
        //     'products',
        // ], [
        //     PostmanGenerator::GET,
        //     PostmanGenerator::POST,
        //     PostmanGenerator::PATCH,
        //     PostmanGenerator::DELETE,
        // ], true);

        $ok = PostmanGenerator::generate();

        dd($ok, 'Generated?');
    }

    // /*
    //     Se puede usar la "base" sin ajustes si la base de datos cumple con las convenciones de Laravel
    // */
    // function gen_laravel__base(){
    //     LaravelApiGenerator::setConnId($this->conn_id);

    //     LaravelApiGenerator::capitalizeTableNames();
    //     LaravelApiGenerator::setControllerTemplatePath(ETC_PATH . "templates/laravelshopify_resourcecontroller.php");

    //     LaravelApiGenerator::setProjectPath('D:\laragon\www\laravel-shopify');
    //     LaravelApiGenerator::setResourceDestPath('D:\laragon\www\laravel-shopify' . '/app/Http/Resources/');
    //     LaravelApiGenerator::setControllerDestPath('D:\laragon\www\laravel-shopify' . '/app/Http/Controllers/');
    //     LaravelApiGenerator::setFactoryDestPath('D:\laragon\www\laravel-shopify' . '/database/factories/');
    //     LaravelApiGenerator::setSeederDestPath('D:\laragon\www\laravel-shopify' . '/database/seeders/');

    //     LaravelApiGenerator::run();
    // }

    // function gen_laravel__custom_1(){
    //     LaravelApiGenerator::setConnId($this->conn_id); 
    //     LaravelApiGenerator::setProjectPath('D:\laragon\www\laravel-shopify');
    //     LaravelApiGenerator::setResourceDestPath('D:\laragon\www\laravel-shopify' . '/app/Http/Resources/');
    //     LaravelApiGenerator::setControllerDestPath('D:\laragon\www\laravel-shopify' . '/app/Http/Controllers/');
    //     LaravelApiGenerator::setFactoryDestPath('D:\laragon\www\laravel-shopify' . '/database/factories/');
    //     LaravelApiGenerator::setSeederDestPath('D:\laragon\www\laravel-shopify' . '/database/seeders/');

    //     LaravelApiGenerator::setControllerWhitelist([
    //         // 'TipoVinculoOER'
    //         // 'orgComunalEntidadRegController',
    //         // 'OrgComunal'
    //         // 'ProyectoEjecutadoRecursosPropios'
    //     ]);

    //     LaravelApiGenerator::setControllerBlacklist([
    //     //     'UsuarioToken',
    //     //     'EstPersJur',  
    //     //     'GrupoInteres' 
    //     //     // ...
    //     ]);

    //     // 

    //     LaravelApiGenerator::setSeederBlacklist([
    //         // ...
    //     ]);

    //     LaravelApiGenerator::addSeedersForHardcodedNonRandomData([
    //         // 'TipoVinculoOER',
    //         // 'Genero',
    //         // 'EstadoLaboral',
    //         // 'EstadoCivil',
    //         // 'Comuna',               // quitar luego
    //         // 'Municipio',            // quitar luego
    //         // 'Departamento',         // quitar luego
    //         // 'GrupoPoblacional',     // quitar luego
    //         // 'Barrio',               // quitar luego
    //         // 'EscalaTerritorial',
    //         // 'NivelEscolaridad',
    //         // 'Nivel',
    //         // 'SectorActividad',
    //         // 'Subregion',
    //         // 'TipoDoc',
    //         // 'TipoOrganismo',
    //         // 'InstrumentoPlaneacion',
    //         // 'CertificacionOrgComunal',
    //         // 'EstPersJur'
    //         // 'UsuarioToken',
    //         // 'GrupoInteres',
    //         // 'EstadoSeguimiento',
    //     ]);

    //     LaravelApiGenerator::addSeedersForRandomData([
    //         // 'ProyectoEjecutadoCooperacion',
    //         // 'ProyectoEjecutadoRecursosPropios',
    //         // 'ProyectoEjecutadoRecursosPublicos',
    //         // 'RepresentanteLegal',  
    //         // 'EntidadReg',
    //         // 'EntidadRegGrupoPoblacional', 
    //         // 'OrgComunal', 
    //         // 'OrgComunalEntidadReg', 
    //     ]);

    //     LaravelApiGenerator::setControllerTemplatePath(ETC_PATH . "templates/laravelshopify_resourcecontroller.php");

    //     LaravelApiGenerator::setValidator("SimpleRest");

    //     LaravelApiGenerator::registerCallback(function($fields){
    //         $softdelete_fieldname = null;
    //         foreach($fields as $field){
    //             if ($field == 'deleted_at'){
    //                 $softdelete_fieldname = $field;
    //             }
    //         }

    //         if ($softdelete_fieldname == null){
    //             // die("Campo _BORRADO es obligatorio en el template");
    //         }

    //         $habilitado_fieldname = null;
    //         foreach($fields as $field){
    //             if ($field == 'enabled_at'){
    //                 $habilitado_fieldname = $field;
    //             }
    //         }

    //         return [
    //             'eval' => [
    //                 "\$campo_borrado    = '$softdelete_fieldname';",
    //                 "\$campo_habilitado = '$habilitado_fieldname';",
    //                 "if (isset(\$campo_borrado)){
    //                     \$ctrl_file = str_replace('__FIELD_BORRADO__', \$campo_borrado, \$ctrl_file);
    //                 };",
    //                 "if (!isset(\$campo_habilitado) || empty(\$campo_habilitado)){
    //                     \$ctrl_file = \Boctulus\Simplerest\Core\Libs\Strings::removeSubstring('// INI:__FN_HABILITAR__', '// END:__FN_HABILITAR__', \$ctrl_file);
    //                 };"
    //             ]
    //         ];
    //     });


    //     #LaravelApiGenerator::writeModels(false);
    //     LaravelApiGenerator::writeControllers(true);
    //     LaravelApiGenerator::writeResources(false);
    //     LaravelApiGenerator::writeRoutes(false);
    //     LaravelApiGenerator::writeSeeders(false);
    //     LaravelApiGenerator::writeFactories(false);  // factories o seeders de random data

    //     LaravelApiGenerator::run();    
    // }

    // function gen_laravel__custom_2(){
    //     LaravelApiGenerator::setConnId($this->conn_id);

    //     LaravelApiGenerator::capitalizeTableNames();
    //     LaravelApiGenerator::setControllerTemplatePath(ETC_PATH . "templates/laravelshopify_resourcecontroller.php");

    //     LaravelApiGenerator::setProjectPath('D:\laragon\www\laravel-shopify');
    //     LaravelApiGenerator::setResourceDestPath('D:\laragon\www\laravel-shopify' . '/app/Http/Resources/');
    //     LaravelApiGenerator::setControllerDestPath('D:\laragon\www\laravel-shopify' . '/app/Http/Controllers/');
    //     // LaravelApiGenerator::setFactoryDestPath('D:\laragon\www\laravel-shopify' . '/database/factories/');
    //     // LaravelApiGenerator::setSeederDestPath('D:\laragon\www\laravel-shopify' . '/database/seeders/');

    //     // LaravelApiGenerator::setControllerWhitelist([
    //     //     'Users'
    //     //     // ...
    //     // ]);

    //     LaravelApiGenerator::setValidator("SimpleRest");

    //     LaravelApiGenerator::registerCallback(function($fields){
    //         $softdelete_fieldname = null;
    //         foreach($fields as $field){
    //             if ($field == 'deleted_at'){
    //                 $softdelete_fieldname = $field;
    //             }
    //         }

    //         $habilitado_fieldname = null;
    //         foreach($fields as $field){
    //             if ($field == 'enabled_at'){
    //                 $habilitado_fieldname = $field;
    //             }
    //         }

    //         if ($softdelete_fieldname == null){
    //             die("Campo _BORRADO es obligatorio en el template");
    //         }

    //         return [
    //             'eval' => [
    //                 "\$campo_borrado    = '$softdelete_fieldname';",
    //                 "\$campo_habilitado = '$habilitado_fieldname';",
    //                 "if (isset(\$campo_borrado)){
    //                     \$ctrl_file = str_replace('__FIELD_BORRADO__', \$campo_borrado, \$ctrl_file);
    //                 };",
    //                 "if (!isset(\$campo_habilitado) || empty(\$campo_habilitado)){
    //                     \$ctrl_file = \Boctulus\Simplerest\Core\Libs\Strings::removeSubstring('// INI:__FN_HABILITAR__', '// END:__FN_HABILITAR__', \$ctrl_file);
    //                 };"
    //             ]
    //         ];
    //     });

       
    //     LaravelApiGenerator::run();
    // }


}

