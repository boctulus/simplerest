<?php

namespace simplerest\libs;

use simplerest\core\libs\DB;
use simplerest\core\libs\Schema;
use simplerest\core\libs\Strings;
use simplerest\core\libs\Date;
use simplerest\core\libs\Files;

/*
    Pablo Bozzolo <boctulus@gmail.com>
    Todos los derechos reservados (2022)

    Mover a "packages"

    TO-DO

    Mediante algun metodo setAuthenticationPackage('Sanctum') podria modificar config\auth.php cambiando las guards a:

    'guards' => [
        'web' => [
            'driver' => 'session',
            'provider' => 'users',
        ],
        'api' => [
            'driver' => 'sanctum',
            'provider' => 'users',
        ],
    ],

*/
class LaravelApiGenerator
{
    static protected $laravel_project_path;
    static protected $resource_output_path;   
    static protected $ctrl_output_path;
    static protected $faker_output_path;
    static protected $seeder_output_path;
    static protected $conn_id; 

    static protected $ctrl_template_path          = ETC_PATH . "templates/laravel_resource_controller.php";
    static protected $resource_template_path      = ETC_PATH . "templates/larevel_resource.php";
    static protected $faker_template_path         = ETC_PATH . "templates/faker.php";
    static protected $seeder_template_path        = ETC_PATH . "templates/seeder.php";
    static protected $seeder_for_factory_tmp_path = ETC_PATH . "templates/seeder_for_factories.php";

    static protected $table_models = [];
    static protected $excluded = [];
    static protected $capitalized_table_names = false;

    static protected $validator = 'Laravel';
    static protected $callbacks = [];

    static protected $ctrl_whitelist     = [];
    static protected $ctrl_blacklist     = [];

    static protected $non_random_seeders = [];
    static protected $random_seeders     = [];
    static protected $seeder_blacklist   = [];

    static protected $write_models       = true;
    static protected $write_controllers  = true;
    static protected $write_resources    = true;
    static protected $write_fakers       = true;
    static protected $write_seeders      = true;
    static protected $write_routes       = true;


    static function setControllerWhitelist(Array $class_names){
        foreach ($class_names as $ix => $classname){
            if (Strings::endsWith('Controller', $classname)){
                $class_names[$ix] = Strings::removeEnding('Controller', $class_names[$ix]);   
            }
        }

        static::$ctrl_whitelist = $class_names;
    }

    static function setControllerBlacklist(Array $class_names){
        foreach ($class_names as $ix => $classname){
            if (Strings::endsWith('Controller', $classname)){
                $class_names[$ix] = Strings::removeEnding('Controller', $class_names[$ix]);   
            }
        }

        static::$ctrl_blacklist = $class_names;
    }

    static function setSeederBlacklist(Array $class_names){
        static::$seeder_blacklist = $class_names;
    }

    static function addSeedersForHardcodedNonRandomData(Array $class_names){
        static::$non_random_seeders = array_merge(static::$non_random_seeders, $class_names);
    }

    static function addSeedersForRandomData(Array $class_names){
        static::$random_seeders = array_merge(static::$random_seeders, $class_names);
    }
    
    static function setProjectPath($path){
        $path = Files::convertSlashes($path);
        static::$laravel_project_path = $path;
    }

    static function setResourceDestPath($path){
        $path = Files::convertSlashes($path);
        $path = Files::removeTrailingSlash($path); //
        static::$resource_output_path = $path;
        Files::mkDirOrFail(static::$resource_output_path);
    }

    static function setControllerDestPath($path){
        $path = Files::convertSlashes($path);
        static::$ctrl_output_path = $path;
    }

    static function setFactoryDestPath($path){
        $path = Files::convertSlashes($path);
        static::$faker_output_path = $path;
    }

    static function setSeederDestPath($path){
        $path = Files::convertSlashes($path);
        static::$seeder_output_path = $path;
    }

    static function setControllerTemplatePath(string $path){
        $path = Files::convertSlashes($path);
        static::$ctrl_template_path = $path;
    }

    static function setResourceTemplatePath(string $path){
        $path = Files::convertSlashes($path);
        static::$resource_template_path = $path;
    }

    static function setFactoryTemplatePath(string $path){
        $path = Files::convertSlashes($path);
        static::$faker_template_path = $path;
    }

    static function setSeederTemplatePath(string $path){
        $path = Files::convertSlashes($path);
        static::$seeder_template_path = $path;
    }

    static function setSeederForFactoryTemplatePath(string $path){
        $path = Files::convertSlashes($path);
        static::$seeder_for_factory_tmp_path = $path;
    }
    
    static function setConnId($conn_id){
        static::$conn_id = $conn_id;
    }

    static function capitalizeTableNames(bool $status = true){
        static::$capitalized_table_names = $status;
    }

    static function registerCallback(callable $callback){
        static::$callbacks[] = $callback;
    }

    static function setValidator(string $validator){
        if (!in_array($validator, ['SimpleRest', 'Laravel'])){
            throw new \InvalidArgumentException("Unkown validador");
        }

        static::$validator = $validator;
    }

    // @return void
    static function get_model_names(bool $include_namespace = false){
        $models_path = static::$laravel_project_path . '/'. 'app/Models/';
        $filenames = Files::glob($models_path, '*.php');

        $_table_models = [];

        foreach ($filenames as $filename){
            $model_name = Strings::before( Strings::last($filename, '/'), '.php' );
            $file = file_get_contents($filename);
       
            $table_name = Strings::match($file, '/protected \$table[ ]{0,}=[ ]{0,}[\'"]([a-zñ0-9_-]+)[\'"]/i');

            // d($table_name, 'TABLE NAME');
            // d($model_name, 'MODEL NAME');
            // dd('-----');

            if (empty($table_name)){
                continue;
            }

            if (!$include_namespace){
                $model_name = Strings::afterLast($model_name, '\\');
            }

            $_table_models[$table_name] = $model_name;
        }

        return $_table_models;
    }

    static function writeModels(bool $status = true){
        static::$write_models = $status;
    }

    static function writeControllers(bool $status = true){
        static::$write_controllers = $status;
    }

    static function writeResources(bool $status = true){
        static::$write_resources = $status;
    }

    static function writeFactories(bool $status = true){
        static::$write_fakers = $status;
    }

    static function writeSeeders(bool $status = true){
        static::$write_seeders = $status;
    }

    static function writeRoutes(bool $status = true){
        static::$write_routes = $status;
    }

    /*
        Analiza todos los schemas y modelos de existir y crea demas archivos

        TODO:

        - Si no existe un Model con $table entonces crear el archivo
        sino, modificar el archivo del modelo agregando los campos que falten (priKey, fillables, etc)
    */
    static function run(){        
        static::$excluded = [
            // 'Users',
            'Migrations',
            'FailedJobs',
            'PasswordResets',
            'PersonalAccessTokens',
            'Cache',
            'CacheLocks',
            'Sessions',
            'Jobs',
            'JobBatches'
        ];

        $write_models      = static::$write_models; 
        $write_controllers = static::$write_controllers && (static::$ctrl_output_path != null);
        $write_resources   = static::$write_resources   && (static::$resource_output_path != null);
        $write_fakers      = static::$write_fakers      && (static::$faker_output_path != null);
        $write_seeders     = static::$write_seeders     && (static::$seeder_output_path != null);
        $write_routes      = static::$write_routes      && $write_controllers;


        /*
            Conexion de SimpleRest apuntando a Laravel
        */
        
        $conn_id = static::$conn_id; 

        if (static::$conn_id == null){
            static::$conn_id = DB::getCurrentConnectionId();
        }

        static::$table_models = static::get_model_names();

        // dd(static::$table_models, 'TABLE MODEL NAMES');
        // exit;

        $ctrl_template           = file_get_contents(static::$ctrl_template_path); 
        $resource_template       = file_get_contents(static::$resource_template_path);
        $faker_template          = file_get_contents(static::$faker_template_path);
        $seeder_template         = file_get_contents(static::$seeder_template_path);
        $seeder_for_factory_temp = file_get_contents(static::$seeder_for_factory_tmp_path);
        

        /*
            Schemas auto-generados de SimpleRest basados en la DB de Laravel
        */

        $paths = Schema::getSchemaFiles($conn_id);

        $model_datos  = [];
        $model_str_ay = [];

        foreach ($paths as $path){
            $path          = str_replace('\\', '/', $path);
            $filename      = Strings::last($path, '/');
            $__class_name  = Strings::beforeLast($filename, '.php'); 
            $__model_name  = Strings::before($__class_name, 'Schema'); 

            if (in_array($__model_name, static::$excluded)){
                continue;
            }

            // dd($__model_name, 'MODEL NAME');

            // if ($__model_name != 'Products'){
            //     continue; ///////////
            // }

            $ctrl_file      = $ctrl_template;
            $resource_file  = $resource_template;
            $faker_file     = $faker_template;
            $seeder_file    = $seeder_template;
            $seeder_4f_file = $seeder_for_factory_temp;

            $class_name_full = "\\simplerest\\schemas\\$conn_id\\" . $__class_name;
            include $path;

            $schema = $class_name_full::get();

            // dd($schema, 'SCHEMA'); exit;

            $table_name = $schema['table_name']; // table name
            $relations  = $schema['expanded_relationships'];

            if (static::$capitalized_table_names){
                $table_name = strtoupper($table_name);
            }

            $class_name = static::$table_models[$table_name] ?? null;

            if ($class_name === null){
                //dd($this->table_models);
                dd("[ Warning ] Class name not found for '$table_name'");
                continue; //
            }

            $generated_controllers = [];
            $generated_seeders     = [];

            /*
                Intentare usar $class_name en todos los casos
            */

            // dd($class_name); exit;

            $model_name = $class_name;
            $ctrl_name  = $class_name;
            
            //dd($table_name, $class_name);

            $fields    = $schema['fields'];
            $fillables = array_diff($fields, ['created_at', 'updated_at', 'deleted_at'], [ $schema['id_name'] ]);

            $uniques    = $schema['uniques'];
            $nullables  = $schema['nullable'];

            $model_data = [];

            /*
                Reglas de validacion
            */

            //dd("Analizando Reglas de Validacion");

            $rules   = $schema['rules'];

            $id_name = $schema['id_name']; // PRI_KEY si es simple o podria ser el campo AUTOINC


            // Voy completando el modelo
            $model_data['id_name'] = $id_name;

            switch (static::$validator){
                case 'Laravel':
                    $laravel_store_rules  = [];
                    $laravel_update_rules = [];

                    $get_rules_str = function ($_rules){
                        $rules_str = '';
                        foreach ($_rules as $f => $r){
                            $rules_str .= "\t\t'$f' => '$r',\r\n";
                        }
        
                        return $rules_str;
                    };
        
                    foreach ($rules as $field => $rule){
                        // Skip validation rules for autoincrement primary key
                        if ($field === $schema['autoincrement'] || $field === $schema['id_name']) {
                            continue;
                        }

                        // También podríamos excluir created_at y updated_at
                        if (in_array($field, ['created_at', 'updated_at'])) {
                            continue;
                        }
                        
                        $r = [];                 
                        
                        if (in_array($field, $nullables)){
                            $r[] = 'nullable';
                        }

                        /*
                            minimos para los distintos tipos de enteros

                            https://dev.mysql.com/doc/refman/8.0/en/integer-types.html
                        */

                        $is_int = false;
                        switch($rule['type']){
                            case 'bool':
                                $r[] = 'boolean';
                                break;
                        
                            case 'int':
                            case 'tinyint':
                            case 'smallint':
                            case 'mediumint':
                                $is_int = true;
                                $r[] = 'integer';
                                break;    

                            case 'date':
                                $r[] = 'date';
                                break;    

                            case 'str':
                                $r[] = 'string';
                                break; 
                        }

                        if (isset($rule['min'])){
                            // fix para bug en schemas 
                            if ($is_int && $rule['min'] != 0){
                                $r[] = "min:{$rule['min']}";
                            }
                        }

                        if (isset($rule['max'])){
                            $r[] = "max:{$rule['max']}";
                        }

                        // No puede contener el "unique"
                        $laravel_update_rules[$field] = implode('|', $r);

                        if (in_array($field, $uniques)){
                            $r[] = "unique:$table_name,$field";
                        }

                        if (isset($rule['required'])){
                            $r[] = 'required';
                        }

                        $laravel_store_rules[$field] = implode('|', $r);
                    }

                    $laravel_store_rules_str  = $get_rules_str($laravel_store_rules); 
                    $laravel_update_rules_str = $get_rules_str($laravel_update_rules); 
        
                    $rules_str = 'protected $store_rules = ['."\r\n" . $laravel_store_rules_str . "\t];\r\n\r\n\t" .
                    'protected $update_rules = ['."\r\n" . $laravel_update_rules_str . "\t];\r\n";
                    break;

                case 'SimpleRest':
                    $validator_rules_str = var_export($rules, true);
                    $uniques_str         = var_export($uniques, true);

                    $rules_str = 'protected $validation_rules = '."\r\n" . Strings::trimMultiline($validator_rules_str). ";\r\n" .
                    "\r\n". 'protected $uniques = '."\r\n" . Strings::trimMultiline($uniques_str). ';';
                    break;
            }

            //dd($rules_str, static::$validator);

            //////////////// [ EXTENSIONES CON CUSTOM CODE ] ///////////////////

            // CALLBACKS
            
            foreach (static::$callbacks as $cb){
                $res = $cb($fields);

                if (isset($res['eval'])){
                    if (is_array($res['eval'])){
                        foreach ($res['eval'] as $eval_code){
                            eval($eval_code);
                        }
                    } else {
                        eval($res['eval']);
                    }
                }
            }
            
            ///////////////////////////////////////////////////////////////////////


            /*
                Controller files
            */

            if ($write_controllers && (empty(static::$ctrl_whitelist) || in_array($class_name, static::$ctrl_whitelist)) && (!in_array($class_name, static::$ctrl_blacklist)))
            {
                //dd("Generando controladores ...");    
            
                $ctrl_file = str_replace('__CONTROLLER_NAME__', "{$model_name}Controller", $ctrl_file);
                $ctrl_file = str_replace('__MODEL_NAME__', $class_name, $ctrl_file);
                $ctrl_file = str_replace('__TABLE_NAME__', $table_name, $ctrl_file);
                $ctrl_file = str_replace('__PRI_KEY__', "'$id_name'", $ctrl_file);
                $ctrl_file = str_replace('// __VALIDATION_RULES__', $rules_str, $ctrl_file);
                $ctrl_file = str_replace('__RESOURCE_NAME__', "{$class_name}Resource", $ctrl_file);

                $ctrl =  "{$class_name}Controller.php";
                $dest =  static::$ctrl_output_path . DIRECTORY_SEPARATOR . $ctrl; 

                // dd([
                //     'class_name' => $class_name,
                //     'dest' => $dest
                // ]);
                
                // continue;

                $ok  = file_put_contents($dest, $ctrl_file);
                dd($dest . " --" . ($ok ? 'ok' : 'failed!'));

                if (!$ok){
                    throw new \Exception("No se pudo crear controlador '{$model_name}Controller'");
                }

                $generated_controllers[] = $class_name;
            }

            /*
                Resource files
            */

            if ($write_resources){
                $resr_name  = "{$model_name}Resource.php";
                $dest = static::$resource_output_path . DIRECTORY_SEPARATOR . $resr_name;

                $resource_file = str_replace('__RESOURCE_NAME__', "{$model_name}Resource", $resource_file);

                $ok  = file_put_contents($dest, $resource_file);
                dd($dest . " --" . ($ok ? 'ok' : 'failed!'));
            }

            /*
                Fillables como van en los modelos
            */

            $model_data['fillables'] = $fillables;
         
            /*
                Faker files
            */

            $exclude_seeder = (in_array($model_name, static::$seeder_blacklist));
        
            if (!$exclude_seeder)
            {
                /*
                    NON-RANDOM SEEDERS
                */

                if (in_array($class_name, static::$non_random_seeders)){                            
                    $fillables_as_array_vals_str     = '';
               
                    foreach ($fillables as $i => $f){
                        $max = $rules[$f]['max'] ?? 255;
                        $min = $rules[$f]['max'] ?? 0;
                            
                        switch ($rules[$f]['type']){
                            case 'int':
                                $valor = 0;

                                // deberia salir a buscar la FK cuidando que hay excepciones y a veces comienza con ID_
                                if (Strings::endsWith('_ID', $f)){
                                    $valor = 1;
                                }
                            break;

                            case 'bool':
                                $valor = 0;
                            break;

                            case 'double':
                            case 'float':
                                $valor = '0.00';
                            break;

                            case 'date':
                                $valor =  Strings::enclose(Date::date());
                            break;    

                            case 'time':
                                $valor = Strings::enclose(Date::time());
                            break; 

                            case 'timestamp':
                            case 'datetime':
                                $valor = Strings::enclose(Date::time());
                            break; 

                            case 'str':
                                $valor = null;

                                if (Strings::containsAnyWord(['numero', 'number'], $f, false))
                                {
                                    $valor = '1234567890';
                                }

                                if (is_null($valor) && Strings::containsWordButNotStartsWith('num', $f, false) ||
                                        Strings::containsWordButNotStartsWith('nro', $f, false) ||
                                        Strings::containsAnyWord(['valor', 'duracion'], $f, false))
                                {
                                    $valor = '10';
                                }

                                if (is_null($valor) && Strings::contains('email', $f, false)){
                                    $valor = 'xxx@dominio.com';
                                }

                                if (is_null($valor) && Strings::containsAnyWord(['telefono', 'tel', 'phone'], $f, false)){
                                    $valor = '3001234567';
                                }

                                if (is_null($valor) && Strings::containsAnyWord(['cellphone', 'celular'], $f, false)){
                                    $valor = '(4) 3855555';
                                }

                                if ($valor === null){
                                    $len   = rand($min, $max);
                                    $valor = trim(Strings::randomString($len));
                                }

                                $valor = "'$valor'";
                            break;
                                
                            default:
                                $valor = "'[valor]'";
                        }

                        if (Strings::endsWith('_BORRADO', $f)){
                            $valor = 0;
                        }
                        
                        if (Strings::containsWord('anno', $f, false)){
                            $valor = '2022';
                        }

                        $fillables_as_array_vals_str .= "'$f' => $valor,\r\n";                        
                    }


                    $id_fillables_as_array_vals_str  = "'$id_name' => 1,\r\n" . $fillables_as_array_vals_str;

                    $__fields__ = rtrim(Strings::tabulate($id_fillables_as_array_vals_str, 4, 0));

                    $data = "[\r\n\t\t\t\t$__fields__
                    ],";

                    Strings::replace('__MODEL_NAME__', $model_name, $seeder_file);
                    Strings::replace('__DATA__', $data, $seeder_file);


                    $dest = static::$seeder_output_path . "{$model_name}Seeder.php";            

                    $ok  = file_put_contents($dest, $seeder_file);
                    dd($dest . " --" . ($ok ? 'ok' : 'failed!'));

                    $generated_seeders[] = $class_name;
                }


                /*
                    En vez de usar "factories" + seeders, ... de momento solo usare otro tipo de seeder:
                    un seeder que tiene generados datos al azar
                */

                if ($write_seeders && in_array($class_name, static::$random_seeders)){
                    
                    /*
                        SEEDERs de DATA RANDOM
                    */

                    $data_count = 5;

                    $fillables_as_array_vals_str_arr = [];
                    
                    for ($j=0; $j<$data_count; $j++)
                    {
                        $fillables_as_array_vals_str = '';

                        foreach ($fillables as $i => $f){
                            $is_fk       = in_array($f, $schema['fks']);
                            $is_nullable = in_array($f, $schema['nullable']);

                            $max = $rules[$f]['max'] ?? 255;
                            $min = $rules[$f]['max'] ?? 0;

                            switch ($rules[$f]['type']){
                                case 'int':
                                    $valor = rand($min, $max);

                                    if ($is_fk)
                                    {
                                        /*
                                            Estoy asumiendo hay solo una relacion entre las dos tablas
                                        */

                                        $tb = null;
                                        $pk = null;

                                        foreach ($relations as $r){
                                            if ($r[0][1][1] == $f){
                                                $r_sel = $r[0];

                                                $tb = $r_sel[0][0];
                                                $pk = $r_sel[0][1];

                                                break;
                                                //dd($pk, $tb);
                                            }
                                        }
                                        
                                        if ($tb == null || $pk == null){
                                            throw new \Exception("Imposible determinar relacion para $table_name.$f");
                                        }

                                        $_val = DB::selectOne("SELECT $pk FROM $tb ORDER BY RAND() LIMIT 1;", null, 'ASSOC', $conn_id);
                                        
                                        $valor = $_val[$pk] ?? 'NULL'; 

                                        if (!$is_nullable && $valor === 'NULL'){
                                            throw new \Exception("No hay registros en la tabla '$tb' y la FK '$f' no puede ser nulla");
                                        }                    

                                        # dd($valor, "VALOR PK | $table_name.$f"); ////////////////////////
                                    }
                                break;

                                case 'bool':
                                    $valor = rand(0,1);
                                break;

                                case 'double':
                                case 'float':
                                    // en realidad tocaria ver el min y max
                                    $valor = rand($min, $max) * 0.99;
                                break;

                                case 'date':
                                    $fecha = Date::subDays(Date::date(), rand(0, 365));                            
                                    $valor = Strings::enclose($fecha);
                                break;    

                                case 'time':
                                    $time  = Date::randomTime();
                                    $valor = Strings::enclose($time);
                                break; 

                                case 'timestamp':
                                case 'datetime':
                                    $fecha = Date::subDays(Date::date(), rand(0, 365)) . ' '. Date::randomTime(true);
                                break; 

                                case 'str':
                                    $valor = null;

                                    if (Strings::containsAnyWord(['numero', 'number'], $f, false))
                                    {
                                        $valor = '1234567890';
                                    }

                                    if (is_null($valor) && Strings::containsWordButNotStartsWith('num', $f, false) ||
                                            Strings::containsWordButNotStartsWith('nro', $f, false) ||
                                            Strings::containsAnyWord(['valor', 'duracion'], $f, false))
                                    {
                                        $valor = '10';
                                    }

                                    if (is_null($valor) && Strings::contains('email', $f, false)){
                                        $valor = 'xxx@dominio.com';
                                    }

                                    if (is_null($valor) && Strings::containsAnyWord(['telefono', 'tel', 'phone'], $f, false)){
                                        $valor = '3001234567';
                                    }

                                    if (is_null($valor) && Strings::containsAnyWord(['cellphone', 'celular'], $f, false)){
                                        $valor = '(4) 3855555';
                                    }

                                    if ($valor === null){
                                        $len   = rand($min, $max);
                                        $valor = trim(Strings::randomString($len));
                                    }

                                    $valor = "'$valor'";
                                break;
                                    
                                default:
                                    $valor = "'[valor]'";
                            }

                            if (Strings::endsWith('_BORRADO', $f)){
                                $valor = 0;
                            }

                            if (Strings::containsWord('anno', $f, false)){
                                $valor = rand(2022, 2032);
                            }

                            $fillables_as_array_vals_str .= "'$f' => $valor,\r\n";                        
                        }

                        $fillables_as_array_vals_str_arr[] = "[\r\n" . $fillables_as_array_vals_str . "\r\n]";
                    }

                    $data_str = implode(",\r\n", $fillables_as_array_vals_str_arr);

                    Strings::replace('__MODEL_NAME__', $model_name, $seeder_file);
                    Strings::replace('__DATA__', rtrim(Strings::tabulate($data_str, 4, 0)), $seeder_file);


                    //$dest = static::$faker_output_path . "{$model_name}Factory.php";
                    $dest = static::$seeder_output_path . "{$model_name}Seeder.php";                    

                    if ($write_fakers){
                        $ok  = file_put_contents($dest, $seeder_file);
                        dd($dest . " --" . ($ok ? 'ok' : 'failed!'));
                    }

                    $generated_seeders[] = $class_name;
                }
                
            }
           
            /*
                api.php
            */

            if ($write_routes){
                $routes[] = "Route::resource('$table_name', App\\Http\\Controllers\\{$model_name}Controller::class);";
            }

            /*
                Modelo
            */

            $model_datos[$model_name] = $model_data; 

            $pri_key   = $model_data['id_name'];
            $fillables = $model_data['fillables'];

            $table_str     =  'protected $table = "'.$table_name.'";';
            $pri_key_str   =  'protected $primaryKey = "'.$pri_key.'";';

            $fillables_str =  '[' . implode(', ', Strings::enclose($fillables, "'")) . ']';
            $fillables_str = 'protected $fillable = ' . $fillables_str . ';';

            $model_str = "$table_str\r\n\r\n$pri_key_str\r\n\r\n$fillables_str";            
    
            $model_str_ay[$model_name] = $model_str; 

        } // end foreach


        /*
            Presento data a agregarse o actualizarse en modelos
        */

        if ($write_models){
            foreach ($model_str_ay as $tb => $model_str){
                dd(
                    $model_str . "\r\n"
                , $tb);
            }
        }

       
        /*
            Rutas 
            
            (podria tener que ajustarse)
        */
        if (!empty($routes)){
            dd("routes/api.php | routes");
            foreach ($routes as $route){
                print_r($route."\r\n");
            }
        }

        
        if (!empty(static::$non_random_seeders)){
            $seeder_clases     = Strings::enclose(static::$non_random_seeders, '', 'Seeder::class');
            $seeder_clases_str = implode(",\r\n", $seeder_clases);

            dd($seeder_clases_str, "database/seeders/DatabaseSeeder.php | Non-random");
        }

    } // end method

} // end class



