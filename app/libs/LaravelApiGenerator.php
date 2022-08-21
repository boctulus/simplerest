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
*/
class LaravelApiGenerator
{
    static protected $laravel_project_path;
    static protected $resource_output_path;   
    static protected $ctrl_output_path;
    static protected $faker_output_path;
    static protected $seeder_output_path;
    static protected $conn_id; 

    static protected $ctrl_template_path      = ETC_PATH . "templates/laravel_resource_controller.php";
    static protected $resource_template_path  = ETC_PATH . "templates/larevel_resource.php";
    static protected $faker_template_path     = ETC_PATH . "templates/faker.php";
    static protected $seeder_template_path    = ETC_PATH . "templates/seeder.php";
    static protected $seeder_nr_template_path = ETC_PATH . "templates/seeder_non_random.php";

    static protected $table_models = [];
    static protected $excluded = [];
    static protected $capitalized_table_names = false;

    static protected $validator = 'Laravel';
    static protected $callbacks = [];

    static protected $non_random_seeders = [];
    static protected $random_seeders     = [];
    static protected $excluded_seeders   = [];


    static function setSeederExclusion(Array $class_names){
        static::$excluded_seeders = $class_names;
    }

    static function addNonRandomSeeders(Array $class_names){
        static::$non_random_seeders = array_merge(static::$non_random_seeders, $class_names);
    }

    static function addRandomSeeders(Array $class_names){
        static::$random_seeders = array_merge(static::$random_seeders, $class_names);
    }
    
    static function setProjectPath($path){
        static::$laravel_project_path = $path;
    }

    static function setResourceDestPath($path){
        static::$resource_output_path = $path;
        Files::mkDirOrFail(static::$resource_output_path);
    }

    static function setControllerDestPath($path){
        static::$ctrl_output_path = $path;
    }

    static function setFactoryDestPath($path){
        static::$faker_output_path = $path;
    }

    static function setSeederDestPath($path){
        static::$seeder_output_path = $path;
    }

    static function setConnId($conn_id){
        static::$conn_id = $conn_id;
    }

    static function capitalizeTableNames(bool $status = true){
        static::$capitalized_table_names = $status;
    }

    static function setControllerTemplatePath(string $path){
        static::$ctrl_template_path = $path;
    }

    static function setResourceTemplatePath(string $path){
        static::$resource_template_path = $path;
    }

    static function setFactoryTemplatePath(string $path){
        static::$faker_template_path = $path;
    }

    static function setSeederTemplatePath(string $path){
        static::$seeder_template_path = $path;
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
    static function get_model_names(){
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

            $_table_models[$table_name] = $model_name;
        }

        return $_table_models;
    }

    /*
        Podria ser un comando
    */
    static function process_schemas(){        
        static::$excluded = [
            'Users',
            'Migrations',
            'FailedJobs',
            'PasswordResets',
            'PersonalAccessTokens'
        ];

        $write_controllers = (static::$ctrl_output_path != null);
        $write_resources   = (static::$resource_output_path != null);
        $write_fakers      = (static::$faker_output_path != null);
        $write_seeders     = (static::$seeder_output_path != null);
        $write_routes      = ($write_controllers);


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

        $ctrl_template      = file_get_contents(static::$ctrl_template_path); 
        $resource_template  = file_get_contents(static::$resource_template_path);
        $faker_template     = file_get_contents(static::$faker_template_path);
        $seeder_template    = file_get_contents(static::$seeder_template_path);
        $seeder_nr_template = file_get_contents(static::$seeder_nr_template_path);
        

        /*
            Schemas auto-generados de SimpleRest basados en la DB de Laravel
        */

        $paths = Schema::getSchemaFiles($conn_id);

        $model_datos = [];

        foreach ($paths as $path){
            $path          = str_replace('\\', '/', $path);
            $filename      = Strings::last($path, '/');
            $__class_name  = Strings::beforeLast($filename, '.php'); 
            $__model_name  = Strings::before($__class_name, 'Schema'); 

            if (in_array($__model_name, static::$excluded)){
                continue;
            }

            // if ($__model_name != 'ProyectosEjecutadosRecurPublicos'){
            //     continue; ///////////
            // }

            $ctrl_file      = $ctrl_template;
            $resource_file  = $resource_template;
            $faker_file     = $faker_template;
            $seeder_file    = $seeder_template;
            $seeder_nr_file = $seeder_nr_template;

            $class_name_full = "\\simplerest\\schemas\\$conn_id\\" . $__class_name;
            include $path;

            $schema = $class_name_full::get();

            $table_name = $schema['table_name']; // table name

            if (static::$capitalized_table_names){
                $table_name = strtoupper($table_name);
            }

            $class_name = static::$table_models[$table_name] ?? null;

            if ($class_name === null){
                //dd($this->table_models);
                throw new \Exception("Class name not found for '$table_name'");
            }

            /*
                Intentare usar $class_name en todos los casos
            */

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
        
                    $rules_str = 'static protected $store_rules = ['."\r\n" . $laravel_store_rules_str . "\t];\r\n\r\n\t" .
                    'static protected $update_rules = ['."\r\n" . $laravel_update_rules_str . "\t];\r\n";
                    break;

                case 'SimpleRest':
                    $validator_rules_str = var_export($rules, true);
                    $uniques_str         = var_export($uniques, true);

                    $rules_str = 'static protected $validation_rules = '."\r\n" . Strings::trimMultiline($validator_rules_str). ";\r\n" .
                    "\r\n". 'static protected $uniques = '."\r\n" . Strings::trimMultiline($uniques_str). ';';
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

            if ($write_controllers){
                //dd("Generando controladores ...");                
                
                $ctrl_file = str_replace('__CONTROLLER_NAME__', "{$model_name}Controller", $ctrl_file);
                $ctrl_file = str_replace('__MODEL_NAME__', $class_name, $ctrl_file);
                $ctrl_file = str_replace('__TABLE_NAME__', $table_name, $ctrl_file);
                $ctrl_file = str_replace('__PRI_KEY__', "'$id_name'", $ctrl_file);
                $ctrl_file = str_replace('__VALIDATION_RULES__', $rules_str, $ctrl_file);
                $ctrl_file = str_replace('__RESOURCE_NAME__', "{$class_name}Resource", $ctrl_file);

                $dest = static::$ctrl_output_path . "{$class_name}Controller.php";

                // dd($dest, "{$model_name}Controller");
                // continue;

                $ok  = file_put_contents($dest, $ctrl_file);
                dd($dest . " --" . ($ok ? 'ok' : 'failed!'));

                if (!$ok){
                    throw new \Exception("No se pudo crear controlador '{$model_name}Controller'");
                }
            }

            /*
                Resource files
            */

            if ($write_resources){
                $resr_name  = "{$model_name}Resource.php";
                $dest = static::$resource_output_path . $resr_name;

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

            $exclude_seeder = (in_array($model_name, static::$excluded_seeders));
        
            if (!$exclude_seeder && $write_fakers){
                /*
                    NON-RANDOM SEEDERS
                */

                if (in_array($class_name, static::$non_random_seeders)){                            
                    $fillables_as_array_str = '';

                    foreach ($fillables as $f){
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
                                $valor = "[valor]";

                                if (Strings::containsWordButNotStartsWith('num', $f, false) ||
                                    Strings::containsWordButNotStartsWith('nro', $f, false) ||
                                    Strings::containsAnyWord(['numero', 'number'], $f, false))
                                {
                                    $valor = '11111111';
                                }

                                if (Strings::contains('email', $f, false)){
                                    $valor = 'xxx@dominio.com';
                                }

                                if (Strings::containsAnyWord(['telefono', 'tel', 'phone'], $f, false)){
                                    $valor = '3001234567';
                                }

                                if (Strings::containsAnyWord(['cellphone', 'celular'], $f, false)){
                                    $valor = '(4) 3855555';
                                }

                                $valor = "'$valor'";
                            break;
                                
                            default:
                                $valor = "'[valor]'";
                        }

                        if (Strings::endsWith('_BORRADO', $f)){
                            $valor = 0;
                        }

                        $fillables_as_array_str .= "'$f' => $valor,\r\n";
                    }

                    Strings::replace('__MODEL_NAME__', $model_name, $seeder_nr_file);
                    Strings::replace('__FIELDS__', rtrim(Strings::tabulate($fillables_as_array_str, 4, 0)), $seeder_nr_file);

                    $dest = static::$seeder_output_path . "{$model_name}Seeder.php";

                    $ok  = file_put_contents($dest, $seeder_nr_file);
                    dd($dest . " --" . ($ok ? 'ok' : 'failed!'));
                }

                continue; /////


                /*
                    RANDOM SEEDERS + FAKERS
                */

                if (in_array($class_name, static::$random_seeders)){
                    // Seeders

                    // Factories
                    $faker_file = str_replace('__MODEL_NAME__', $model_name, $faker_file);
                    $faker_file = str_replace('__FIELDS__', $fillables_as_array_str, $faker_file);

                    $dest = static::$faker_output_path . "{$model_name}Factory.php";

                    $ok  = file_put_contents($dest, $faker_file);
                    dd($dest . " --" . ($ok ? 'ok' : 'failed!'));
                }
                
            }
           
            /*
                api.php
            */

            if ($write_routes){
                $routes[] = "Route::resource('$table_name', App\\Http\\Controllers\\{$model_name}Controller::class);";
            }

            $model_datos[$model_name] = $model_data; 
        } // end foreach


        /*
            Presento data a agregarse o actualizarse en modelos
        */
        foreach ($model_datos as $tb => $model_dato){
            $pri_key   = $model_dato['id_name'];
            $fillables = $model_dato['fillables'];

            $pri_key_str   =  'protected $primaryKey = "'.$pri_key.'";';

            $fillables_str =  '[' . implode(', ', Strings::enclose($fillables, "'")) . ']';
            $fillables_str = 'protected $fillable = ' . $fillables_str . ';';

            $str = "$pri_key_str\r\n\r\n$fillables_str";

            dd(
                $str
            , $tb);
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



