<?php

namespace simplerest\controllers;

use simplerest\controllers\MyController;
use simplerest\core\libs\Schema;
use simplerest\core\libs\Strings;
use simplerest\core\libs\Files;

/*
    Pablo Bozzolo <boctulus@gmail.com>
    Todos los derechos reservados (2022)
*/
class LaravelApiGeneratorController extends MyController
{
    static protected $laravel_project_path = 'D:/www/organizaciones';
    static protected $resource_output_path = 'D:/www/organizaciones' . '/app/Http/Resources/';   
    static protected $ctrl_output_path     = 'D:/www/organizaciones' . '/app/Http/Controllers/';
    static protected $faker_output_path    = 'D:/www/organizaciones' . '/database/factories/';
    static protected $seeder_output_path   = 'D:/www/organizaciones' . '/database/seeders/';
    static protected $conn_id = 'mpo'; 

    protected $table_models = [];

    // @return void
    function get_model_names(){
        $models_path = static::$laravel_project_path . '/'. 'app/Models/';
        $filenames = Files::glob($models_path, '*.php');

        foreach ($filenames as $filename){
            $model_name = Strings::before( Strings::last($filename, '/'), '.php' );
            $file = file_get_contents($filename);
       
            $table_name = Strings::match($file, '/protected \$table[ ]+=[ ]+\'([a-zñ0-9_-]+)\'/');
            $this->table_models[$table_name] = $model_name;
        }
    }

    function index(){
        $this->process_schemas();
    }

    /*
        Podria ser un comando
    */
    function process_schemas(){
        $write_controllers = true;
        $write_resources   = false;
        $write_fakers      = false;
        $write_seeders     = false;
        $write_routes      = false;

        $conn_id = static::$conn_id; // de SimpleRest apuntando al contenedor con Laravel

        $excluded = [
            'Users',
            'Migrations',
            'FailedJobs',
            'PasswordResets',
            'PersonalAccessTokens'
        ];

        $ctrl_template_path     = ETC_PATH . "templates/laravel_resource_controller.php";
        $resource_template_path = ETC_PATH . "templates/larevel_resource.php";
        $faker_template_path    = ETC_PATH . "templates/faker.php";
        $seeder_template_path   = ETC_PATH . "templates/seeder.php";


        $this->get_model_names();

        $ctrl_template     = file_get_contents($ctrl_template_path);        
        $resource_template = file_get_contents($resource_template_path);
        $faker_template    = file_get_contents($faker_template_path);
        $seeder_template   = file_get_contents($seeder_template_path);


        /*
            Schemas auto-generados de SimpleRest basados en la DB de Laravel
        */
        $paths = Schema::getSchemaFiles($conn_id);

        foreach ($paths as $path){
            $path         = str_replace('\\', '/', $path);
            $filename     = Strings::last($path, '/');
            $__class_name = Strings::beforeLast($filename, '.php'); 
            $__model_name = Strings::before($__class_name, 'Schema'); 

            if (in_array($__model_name, $excluded)){
                continue;
            }

            $class_name_full = "\\simplerest\\schemas\\$conn_id\\" . $__class_name;
            include $path;

            $schema = $class_name_full::get();

            $table_name = $schema['table_name']; // table name
            $class_name = $this->table_models[$table_name] ?? null;

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

            $fillables_str =  '[' . implode(', ', Strings::enclose($fillables, "'")) . ']';
            $fillables_str = 'protected $fillable = ' . $fillables_str . ';';

            $uniques   = $schema['uniques'];
            $nullables = $schema['nullable'];

            /*
                Reglas de validacion
            */

            $rules = $schema['rules'];

            $laravel_store_rules  = [];
            $laravel_update_rules = [];
            foreach ($rules as $field => $rule){
                $r = [];                 
                
                if (in_array($field, $nullables)){
                    $r[] = 'nullable';
                }

                if (isset($rule['required'])){
                    $r[] = 'required';
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

                $laravel_store_rules[$field] = implode('|', $r);
            }

            $get_laravel_rules_str = function ($laravel_rules){
                $laravel_rules_str = '';
                foreach ($laravel_rules as $f => $r){
                    $laravel_rules_str .= "\t\t'$f' => '$r',\r\n";
                }

                return $laravel_rules_str;
            };

            $laravel_store_rules_str  = $get_laravel_rules_str($laravel_store_rules); 
            $laravel_update_rules_str = $get_laravel_rules_str($laravel_update_rules); 

            $laravel_rules_str = 'static protected $store_rules = ['."\r\n" . $laravel_store_rules_str . "\t];\r\n\r\n\t" .
            'static protected $update_rules = ['."\r\n" . $laravel_update_rules_str . "\t];\r\n";

            /*
                Controller files
            */

            if ($write_controllers){
                $ctrl_file = str_replace('__CONTROLLER_NAME__', "{$model_name}Controller", $ctrl_template);
                $ctrl_file = str_replace('__MODEL_NAME__', $model_name, $ctrl_file);
                $ctrl_file = str_replace('__VALIDATION_RULES__', $laravel_rules_str, $ctrl_file);
                $ctrl_file = str_replace('__RESOURCE_NAME__', "{$model_name}Resource", $ctrl_file);

                $dest = static::$ctrl_output_path . "{$model_name}Controller.php";

                $ok  = file_put_contents($dest, $ctrl_file);
                dd($dest . " --" . ($ok ? 'ok' : 'failed!'));
            }

            continue;
     
            /*
                Resource files
            */

            if ($write_resources){
                $resr_name  = "{$model_name}Resource.php";
                $dest = static::$resource_output_path . $resr_name;

                $resource_file = str_replace('__RESOURCE_NAME__', "{$model_name}Resource", $resource_template);

                $ok  = file_put_contents($dest, $resource_file);
                dd($dest . " --" . ($ok ? 'ok' : 'failed!'));
            }

            /*
                Faker files
            */
        
            if ($write_fakers){
                $fillables_as_array_str = '';
                foreach ($fillables as $f){
                    $fillables_as_array_str .= "'$f' => '[valor]',\r\n";
                }

                $faker_file = str_replace('__MODEL_NAME__', $model_name, $faker_template);
                $faker_file = str_replace('__FIELDS__', $fillables_as_array_str, $faker_file);

                $dest = static::$faker_output_path . "{$model_name}Factory.php";

                $ok  = file_put_contents($dest, $faker_file);
                dd($dest . " --" . ($ok ? 'ok' : 'failed!'));
            }
           
            /*
                api.php
            */

            if ($write_routes){
                $routes[] = "Route::resource('$table_name', App\\Http\\Controllers\\{$model_name}Controller::class);";
            }
        }

        if ($write_routes){
            dd("app.php | routes");
            foreach ($routes as $route){
                print_r($route."\r\n");
            }
        }
    }
}

