<?php

namespace simplerest\core;

use simplerest\core\Controller;
use simplerest\core\Request;
use simplerest\libs\Factory;
use simplerest\libs\Debug;
use simplerest\libs\DB;
use simplerest\libs\Files;
use simplerest\libs\Strings;
use simplerest\libs\Schema;

/*
    Class generator

    Commands:
    
    make schema SuperAwesome [--force | -f]
    make schema super_awesome  [--force | -f]
    
    make model SuperAwesomeModel  [--force | -f]
    make model SuperAwesome [--force | -f]
    make model super_awesome  [--force | -f]
    
    make controller SuperAwesome  [--force | -f]
    
    make api SuperAwesome  [--force | -f]
    make api super_awesome  [--force | -f]
    
    make api all --from:dsi [--force | -f]
    
    <-- "from:" is required in this case.
    
    make any SuperAwesome  [-s | --schema ] 
                           [-m | --model] 
                           [-c | --controller ] 
                           [-a | --api ] 
                           [-p | --provider | --service ]
          
                           [--force | -f]
    
    make any all           [-s | --schema ] 
                           [-m | --model] 
                           [-c | --controller ] 
                           [-a | --api ] 
                           [-p | --provider | --service ]
          
                           [--force | -f]
    
    More examples:
    
    make any baz -s -m -a -f
    make any tbl_contacto -sam --from:dsi
    make any all -sam --from:dsi
    make any all -samf --from:dsi
    
    Note:
    
    To execute a command like
        
    make schema SuperAwesome [--force | -f]
    
    ... call 'php' interpreter + 'com' controller first. 
    
    php com make schema SuperAwesome [--force | -f]
    
*/
class MakeControllerBase extends Controller
{
    const SCHEMAS_PATH = MODELS_PATH . 'schemas' . DIRECTORY_SEPARATOR;
    const SERVICE_PROVIDERS_PATH = ROOT_PATH . 'providers' . DIRECTORY_SEPARATOR; //

    const TEMPLATES = CORE_PATH . 'templates' . DIRECTORY_SEPARATOR;

    const MODEL_TEMPLATE  = self::TEMPLATES . 'Model.php';
    const SCHEMA_TEMPLATE = self::TEMPLATES . 'Schema.php';
    const MIGRATION_TEMPLATE  = self::TEMPLATES . 'Migration.php';
    const CONTROLLER_TEMPLATE = self::TEMPLATES . 'Controller.php';
    const CONSOLE_TEMPLATE = self::TEMPLATES . 'ConsoleController.php';
    const API_TEMPLATE = self::TEMPLATES . 'ApiRestfulController.php';
    const SERVICE_PROVIDER_TEMPLATE = self::TEMPLATES . 'ServiceProvider.php'; 
    const HELPER_TEMPLATE = self::TEMPLATES . 'Helper.php'; 
    const LIBS_TEMPLATE = self::TEMPLATES . 'Lib.php';

    protected $class_name;
    protected $ctr_name;
    protected $api_name; 
    protected $excluded_files = [];

    function __construct()
    {
        if (php_sapi_name() != 'cli'){
            Factory::response()->send("Error: Make can only be excecuted in console", 403);
        }

        if (file_exists(APP_PATH. '.make_ignore')){
            $this->excluded_files = preg_split('/\R/', file_get_contents(APP_PATH. '.make_ignore'));
            
            foreach ($this->excluded_files as $ix => $f){
                $f = trim($f);
                if (empty($f) || $f == "\r" || $f == "\n" || $f == "\r\n"){
                    unset($this->excluded_files[$ix]);
                    continue;
                } 

                if (Strings::startsWith('#', $f) || Strings::startsWith(';', $f)){
                    unset($this->excluded_files[$ix]);
                    continue;
                }

                $this->excluded_files[$ix] = str_replace(array('/', '\\'), DIRECTORY_SEPARATOR, $f);

                if (Strings::contains(DIRECTORY_SEPARATOR, $this->excluded_files[$ix])){
                    $this->excluded_files[$ix] = APP_PATH . $this->excluded_files[$ix];    
                }                 
            }
        }

        parent::__construct();
    }
    
    function help(){
        echo <<<STR
        MAKE COMMAND HELP
          
        make helper my_cool_helper

        make schema super_awesome  [--force | -f]

        make model SuperAwesomeModel  [--force | -f]
        make model SuperAwesome [--force | -f]
        make model super_awesome  [--force | -f]
        
        make controller SuperAwesome  [--force | -f]
        make controller folder/SuperAwesome  [--force | -f]

        make console SuperAwesome  [--force | -f]
        make console folder/SuperAwesome  [--force | -f]

        make api SuperAwesome   [--force | -f]
        make api super_awesome  [--force | -f]

        make api all --from:dsi [--force | -f]

        <-- "from:" is required in this case.]
                 
        make any SuperAwesome   [--schema | -s] 
                                [--model | -m] 
                                [--controller | -c]
                                [--console ] 
                                [--api | -a] 
                                [--provider | --service | -p]
                                [--force | -f]

                                -sam  = -s -a -m
                                -samf = -s -a -m -f
        
        make migration rename_some_column
        make migration another_table_change --table=foo
        
        Examples:
        
        make any baz -s -m -a -f
        make any tbl_contacto -sam --from:dsi
        make any all -sam  --from:dsi
        make any all -samf --from:dsi
        make any all -s -f --from:main                        
        
        STR;

        print_r(PHP_EOL);
    }

    // Rutear "make -h" y "make --help" a "make index -h" y "make index --help" respectivamente
    function index(...$opt){
        if (!isset($opt[0])){
            $this->help();
            return;
        }
        
        /*
        if ($opt[0] == '-h' || $opt[0] == '--help'){
            $this->help();
        }
        */
    }

    protected function setup(string $name) {
        static $prev_name;

        $name = str_replace('-', '_', $name);

        $name = ucfirst($name);    
        $name_lo = strtolower($name);

        if (Strings::endsWith('model', $name_lo)){
            $name = substr($name, 0, -5);
        } elseif (Strings::endsWith('controller', $name_lo)){
            $name = substr($name, 0, -10);
        }

        $name_uc = ucfirst($name);

        if (strpos($name, '_') !== false) {
            $camel_case  = Strings::snakeToCamel($name);
            $snake_case = $name_lo;
        } elseif ($name == $name_lo){
            $snake_case = $name;
            $camel_case  = ucfirst($name);
        } elseif ($name == $name_uc) {
            $camel_case  = $name; 
        }
        
        if (!isset($snake_case)){
            $snake_case = Strings::camelToSnake($camel_case);
        }

        $this->camel_case  = $camel_case; 
        $this->snake_case  = $snake_case;
        
        $prev_name = $name;
    }

    /*
        make any SuperAwesome   [-s | --schema ] 
                                [-m | --model] 
                                [-c | --controller ] 
                                [-a | --api ] 
                                [--force | -f]

                                -sam  = -s -a -m
                                -samf = -s -a -m -f

        make any  all           options    

        Ej:

        make any tbl_contacto -sam --from:dsi

        make any all          -sam --from:dsi


        Remember to call "php com" before "com"

        Ej:

        php com make any all -samf --from:dsi

    */
    function any($name, ...$opt){ 
        if (count($opt) == 0){
            self::pprint("Nothing to do. Please specify action using options.\r\nUse 'make help' for help.\r\n");
            exit;
        }

        foreach ($opt as $o){            
            if (preg_match('/^--from[=|:]([a-z][a-z0-9A-Z_]+)$/', $o, $matches)){
                $from_db = $matches[1];
                DB::getConnection($from_db);
            }
        }

        if ($name == 'all'){
            $tables = Schema::getTables();
            
            foreach ($tables as $table){
                $this->schema($table, ...$opt);
            }
        }

        $names = $name == 'all' ? $tables : [$name];
        
        switch($opt[0]){
            case '-sam':
                $opt = ['-s', '-a', '-m'];
                break;
            case '-samf':
                $opt = ['-s', '-a', '-m', '-f'];
                break;       
        }
        
        foreach ($names as $name){
            if (in_array('-s', $opt) || in_array('--schema', $opt)){
                $this->schema($name, ...$opt);
            }
            if (in_array('-m', $opt) || in_array('--model', $opt)){
                $this->model($name, ...$opt);
            }
            if (in_array('-a', $opt) || in_array('--api', $opt)){
                $this->api($name, ...$opt);
            }
            if (in_array('-c', $opt) || in_array('--controller', $opt)){
                $opt = array_intersect($opt, ['-f', '--force']);
                $this->controller($name, ...$opt);
            }
            if (in_array('--console', $opt)){
                $opt = array_intersect($opt, ['-f', '--force']);
                $this->console($name, ...$opt);
            }
            if (in_array('-p', $opt) || in_array('--service', $opt) || in_array('--provider', $opt)){
                $opt = array_intersect($opt, ['-f', '--force']);
                $this->provider($name, ...$opt);
            }
        }            
    }

    /*
        File generation
    */
    function generic($name, $subfix, $namespace, $dest_path, $template_path, ...$opt) {
        $name = str_replace('/', DIRECTORY_SEPARATOR, $name);
        
        $sub_path = '';
        if (strpos($name, DIRECTORY_SEPARATOR) !== false){
            $exp = explode(DIRECTORY_SEPARATOR, $name);
            $sub = implode(DIRECTORY_SEPARATOR, array_slice($exp, 0, count($exp)-1));
            $sub_path = $sub . DIRECTORY_SEPARATOR;
            $name = $exp[count($exp)-1];
            $namespace .= "\\$sub";
        }

        $this->setup($name);    

        if (!file_exists($dest_path . $sub_path)){
            Files::mkdir_ignore($dest_path . $sub_path);
        }
    
        $filename = $this->camel_case . $subfix.'.php';
        $dest_path = $dest_path . $sub_path . $filename;

        $protected = $this->hasFileProtection($filename, $dest_path, $opt);
        
        $data = file_get_contents($template_path);
        $data = str_replace('__NAME__', $this->camel_case . $subfix, $data);
        $data = str_replace('__NAMESPACE', $namespace, $data);

        $this->write($dest_path, $data, $protected);
    }

    function controller($name, ...$opt) {
        $namespace = 'simplerest\\controllers';
        $dest_path = CONTROLLERS_PATH;
        $template_path = self::CONTROLLER_TEMPLATE;
        $subfix = 'Controller';  // Ej: 'Controller'

        $this->generic($name, $subfix, $namespace, $dest_path, $template_path, ...$opt);
    }

    function console($name, ...$opt) {
        $namespace = 'simplerest\\controllers';
        $dest_path = CONTROLLERS_PATH;
        $template_path = self::CONSOLE_TEMPLATE;
        $subfix = 'Controller';  // Ej: 'Controller'

        $this->generic($name, $subfix, $namespace, $dest_path, $template_path, ...$opt);
    }

    function lib($name, ...$opt) {
        $namespace = 'simplerest\\libs';
        $dest_path = LIBS_PATH;
        $template_path = self::LIBS_TEMPLATE;
        $subfix = '';  // Ej: 'Controller'

        $this->generic($name, $subfix, $namespace, $dest_path, $template_path, ...$opt);
    }

    function api($name, ...$opt) { 
        foreach ($opt as $o){            
            if (preg_match('/^--from[=|:]([a-z][a-z0-9A-Z_]+)$/', $o, $matches)){
                $from_db = $matches[1];
                DB::getConnection($from_db);
            }
        }

        if ($name == 'all'){
            $tables = Schema::getTables();
            
            foreach ($tables as $table){
                $this->api($table, ...$opt);
            }

            return;
        }

        $this->setup($name);    
    
        $filename = $this->camel_case.'.php';

        $dest_path = API_PATH . $filename;

        $protected = $this->hasFileProtection($filename, $dest_path, $opt);

        $data = file_get_contents(self::API_TEMPLATE);
        $data = str_replace('__NAME__', $this->camel_case, $data);
        $data = str_replace('__SOFT_DELETE__', 'true', $data); // debe depender del schema

        $this->write($dest_path, $data, $protected);
    }

    protected function get_pdo_const(string $sql_type){
        if (Strings::startsWith('int', $sql_type) || 
        Strings::startsWith('tinyint', $sql_type) || 
        Strings::startsWith('smallint', $sql_type) ||
        Strings::startsWith('mediumint', $sql_type) ||
        Strings::startsWith('bigint', $sql_type) ||
        Strings::startsWith('serial', $sql_type)
        ){
            return 'INT';
        }   

        if (Strings::startsWith('bit', $sql_type) || 
        Strings::startsWith('bool', $sql_type)){
            return 'BOOL';
        } 

        // chequear
        if (Strings::startsWith('timestamp', $sql_type) || 
        Strings::startsWith('year', $sql_type)){
            return 'INT';
        } 

        // el resto (default)
        return 'STR'; 
    }

    /*
        Return if file is protected and not should be overwrited
    */
    protected function hasFileProtection(string $filename, string $dest_path, Array $opt) : bool {
        if (in_array($dest_path, $this->excluded_files)){
            self::pprint("[ Skipping ] '$dest_path'. File was ignored\r\n"); 
            return true; 
        } elseif (file_exists($dest_path)){
            if (!in_array('-f', $opt) && !in_array('--force', $opt)){
                self::pprint("[ Skipping ] '$dest_path'. File already exists. Use -f or --force if you want to override.\r\n");
                return true;
            } elseif (!is_writable($dest_path)){
                self::pprint("[ Error ] '$dest_path'. File is not writtable. Please check permissions.\r\n");
                return true;
            }
        }
    
        if (in_array($filename, $this->excluded_files)){
            self::pprint("[ Skipping ] '$dest_path'. File was ignored\r\n"); 
            return true; 
        } elseif (file_exists($dest_path)){
            if (!in_array('-f', $opt) && !in_array('--force', $opt)){
                self::pprint("[ Skipping ] '$dest_path'. File already exists. Use -f or --force if you want to override.\r\n");
                return true;;
            } elseif (!is_writable($dest_path)){
                self::pprint("[ Error ] '$dest_path'. File is not writtable. Please check permissions.\r\n");
                return true;
            }
        }

        return false;
    }

    protected function write(string $dest_path, string $file, bool $protected){
        if ($protected){
            return;
        }

        $ok = (bool) file_put_contents($dest_path, $file);
        
        if (!$ok) {
            throw new \Exception("Failed trying to write $dest_path");
        } else {
            self::pprint("$dest_path was generated\r\n");
        } 
    }

    function schema($name, ...$opt) 
    {
        foreach ($opt as $o){            
            if (preg_match('/^--from[=|:]([a-z][a-z0-9A-Z_]+)$/', $o, $matches)){
                $from_db = $matches[1];
                DB::getConnection($from_db);
            }
        }

        if ($name == 'all'){
            $tables = Schema::getTables();
            
            foreach ($tables as $table){
                $this->schema($table, ...$opt);
            }

            return;
        }

        $this->setup($name);    

        $filename = $this->camel_case.'Schema.php';


        $file = file_get_contents(self::SCHEMA_TEMPLATE);
        $file = str_replace('__NAME__', $this->camel_case.'Schema', $file);
        
        // destination

        DB::getConnection();
        $current = DB::getCurrentConnectionId();
     
        if ($current == config()['db_connection_default']){
            $file = str_replace('namespace simplerest\models\schemas', 'namespace simplerest\models\schemas' . "\\$current", $file);

            Files::mkdir_ignore(self::SCHEMAS_PATH . $current);
            $dest_path = self::SCHEMAS_PATH . "$current/". $filename;
        }  else {
            $dest_path = self::SCHEMAS_PATH . $filename;
        } 

        if (!Schema::hasTable($name)){
            self::pprint("Table '$name' not found. It's case sensitive\r\n");
            return;
        }
        
        $protected = $this->hasFileProtection($filename, $dest_path, $opt);

        try {
            $fields = DB::select("SHOW COLUMNS FROM {$this->snake_case}");
        } catch (\Exception $e) {
            self::pprint('[ SQL Error ] '. DB::getLog(). "\r\n");
            self::pprint($e->getMessage().  "\r\n");
        }
        
        $id_name =  NULL;
        $uuid = false;
        $field_names  = [];
        $types = [];
        $types_raw = [];

        $nullables = [];
        //$not_fillable = [];
        $rules = [];
        $pri_components = [];
        $autoinc = null;

        foreach ($fields as $field){
            $field_names[] = $field['Field'];

            if ($field['Null']  == 'YES' || $field['Default'] !== NULL) { 
                $nullables[] = $field['Field']; 
            }
            
            if ($field['Key'] == 'PRI'){ 
                if ($id_name != NULL){
                    //$msg = "A table should have simple Primary Key by convention for table \"$name\"";
                    //Files::logger($msg);      
                }
                
                $id_name = $field['Field'];
                $pri_components[] = $field['Field'];
            }
            if ($field['Extra'] == 'auto_increment') { 
                //$not_fillable[] = $field['Field'];
                $nullables[] = $field['Field']; 
                $autoinc     = $field['Field'];
            }
            $types[$field['Field']] = $this->get_pdo_const($field['Type']);
            $types_raw[$field['Field']] = $field['Type'];
         
            if ($field['Key'] == 'PRI'){ 
                $field_name_lo = strtolower($field['Field']);
                if ($field_name_lo == 'uuid' || $field_name_lo == 'guid'){
                    if ($types[$field['Field']] != 'STR'){
                        printf("Warning: {$field['Field']} has not a valid type for UUID ***\r\n");
                    }

                    $uuid = true;
                }
            }    
        }

        /*
        if ($id_name == NULL){
            throw new \Exception("No Primary Key found!");
        }
        */

        if (count($pri_components) >1){
            // busco si hay un AUTOINC
            if (!empty($autoinc)){
                $id_name = $autoinc; 
            } else {
                $msg = "A table should have simple Primary Key by convention for table \"$name\"";
                Files::logger($msg);  
                dd($msg, 'WARNING'); 
            }
        }

        $nullables = array_unique($nullables);

        $escf = function($x){ 
            return "'$x'"; 
        };

        $_attr_types = [];
        $_rules  = [];
        foreach ($types as $f => $type){
            $_attr_types[] = "\t\t\t\t'$f' => '$type'";

            if (preg_match('/^varchar\(([0-9]+)\)$/', $types_raw[$f], $matches)){
                $len = $matches[1];
                $_rules [] = "\t\t\t\t'$f' => ['max' => $len]";
            }
        }

        $attr_types = "[\r\n". implode(",\r\n", $_attr_types). "\r\n\t\t\t]";
        $rules  = "[\r\n". implode(",\r\n", $_rules). "\r\n\t\t\t]";

        if ($uuid){
            if (!empty($id_name)){
                $nullables[] = $id_name;
            }
                
            //Strings::replace('### IMPORTS', 'use simplerest\traits\Uuids;', $file); 
            //Strings::replace('### TRAITS', "use Uuids;", $file);        
        }


        /*
            Relationships
        */

        $relations = '';
        $rels = Schema::getAllRelations($name, true);
        
        $g = [];
        $c = 0;
        foreach ($rels as $tb => $rs){
            $grp = "\t\t\t\t\t" . implode(",\r\n\t\t\t\t\t", $rs);
            $grp = ($c != 0 ? "\t\t\t\t" : '') . "'$tb' => [\r\n$grp\r\n\t\t\t\t]";
            $g[] = $grp;
            $c++;
        }

        $relations = implode(",\r\n", $g);


        Strings::replace('__TABLE_NAME__', "'{$this->snake_case}'", $file);  
        Strings::replace('__ID__', !empty($id_name) ? "'$id_name'" : 'NULL', $file);          
        Strings::replace('__ATTR_TYPES__', $attr_types, $file);
        Strings::replace('__NULLABLES__', '['. implode(', ',array_map($escf, $nullables)). ']',$file);        
        //Strings::replace('__NOT_FILLABLE__', '['.implode(', ',array_map($escf, $not_fillable)). ']',$file);
        Strings::replace('__RULES__', $rules, $file);
        Strings::replace('__RELATIONS__', $relations, $file);
        
        $this->write($dest_path, $file, $protected);
    }

    protected function getUuid(){
        try {
            $fields = DB::select("SHOW COLUMNS FROM {$this->snake_case}");
        } catch (\Exception $e) {
            self::pprint('[ SQL Error ] '. DB::getLog(). "\r\n");
            self::pprint($e->getMessage().  "\r\n");
            throw $e;
        }
        
        $id_name =  NULL;
        $uuid = false;

        foreach ($fields as $field){
            if ($field['Key'] == 'PRI'){ 
                $field_name_lo = strtolower($field['Field']);
                if ($field_name_lo == 'uuid' || $field_name_lo == 'guid'){
                    if ($this->get_pdo_const($field['Type']) == 'STR'){
                        return $field['Field'];
                    }
                }
            }    
        }

        return false;
    }

    function model($name, ...$opt) { 
        foreach ($opt as $o){            
            if (preg_match('/^--from[=|:]([a-z][a-z0-9A-Z_]+)$/', $o, $matches)){
                $from_db = $matches[1];
                DB::getConnection($from_db);
            }
        }

        if ($name == 'all'){
            $tables = Schema::getTables();
            
            foreach ($tables as $table){
                $this->model($table, ...$opt);
            }

            return;
        }

        $this->setup($name);  

        $filename = $this->camel_case . 'Model'.'.php';

        // destination        
        $dest_path = MODELS_PATH . $filename;

        $protected = $this->hasFileProtection($filename, $dest_path, $opt);

        $file = file_get_contents(self::MODEL_TEMPLATE);
        $file = str_replace('__NAME__', $this->camel_case.'Model', $file);

        $imports = [];
        $traits  = [];
        $proterties = [];


        DB::getConnection();
        $current = DB::getCurrentConnectionId();
     
        $extra = '';
        if ($current == config()['db_connection_default']){
            $extra = "$current\\";
        }

        $imports[] = "use simplerest\\models\\schemas\\$extra{$this->camel_case}Schema;";
       
        Strings::replace('__SCHEMA_CLASS__', "{$this->camel_case}Schema", $file); 

        if ($uuid = $this->getUuid()){
            $imports[] = 'use simplerest\traits\Uuids;';
            $traits[] = 'use Uuids;';      
        }

        Strings::replace('### IMPORTS', implode("\r\n", $imports), $file); 
        Strings::replace('### TRAITS',  implode("\r\n\t", $traits), $file); 
        Strings::replace('### PROPERTIES', implode("\r\n\t", $proterties), $file); 

        $this->write($dest_path, $file, $protected);
    }

    function migration($name, ...$opt) 
    {
        $this->setup($name);

        // 2020_10_28_141833_yyy
        $date = date("Y_m_d");
        $secs = time() - 1603750000;
        $filename = $date . '_'. $secs . '_' . $this->snake_case . '.php'; 

        $file = file_get_contents(self::MIGRATION_TEMPLATE);

        $path    = MIGRATIONS_PATH;
        $to_db   = null;
        $tb_name = null;
        $from_db = null;
        $script  = null;
        $dir     = null;


        $up_rep = '';
        foreach ($opt as $o){
            if (is_array($o)){
                $o = $o[0];
            }

            if (preg_match('/^--to[=|:]([a-z][a-z0-9A-Z_]+)$/', $o, $matches)){
                $to_db = $matches[1];
            }

            if (preg_match('/^--from[=|:]([a-z][a-z0-9A-Z_]+)$/', $o, $matches)){
                $from_db = $matches[1];
            }

            /*
                Makes a reference to the specified table schema
            */
            if (preg_match('/^--table[=|:]([a-z][a-z0-9A-Z_]+)$/', $o, $matches)){
                $tb_name = $matches[1];
            }
            
            /*  
                This option forces php class name
            */
            if (preg_match('/^--class_name[=|:]([a-z][a-z0-9_]+)$/i', $o, $matches)){
                $class_name = Strings::snakeToCamel($matches[1]);
                $file = str_replace('__NAME__', $class_name, $file); 
            } 

            if (preg_match('/^--dir[=|:]([a-z][a-z0-9A-Z_]+)$/', $o, $matches)){
                $dir= $matches[1];
            }

            /*
                The only condition to work is the script should be enclosed with double mark quotes ("")
                and it should not contain any double mark inside
            */
            if (preg_match('/^--from_script[=|:]"([^"]+)"/', $o, $matches)){
                $script = $matches[1];
            }
        }

        $file = str_replace('__NAME__', $this->camel_case, $file); 

        if (!empty($dir)){
            $path .= "$dir/";
            Files::mkdir_ignore($path);
        }

        if (!empty($script)){
            if (!Strings::contains('"', $script)){
                $up_rep .= "Model::query(\"$script\");";
            } else {
                $up_rep .= "Model::query(\"
                <<<'SQL_QUERY'
                $script
                SQL_QUERY;
                \");";
            }            
        } 

        if (!empty($to_db)){
            $up_rep .= "config()['db_connection_default'] = '$to_db';\r\n\r\n";
        }
        
        if (!empty($tb_name)){
            if (!empty($up_rep)){
                $up_rep .= "\t\t";
            }

            $up_rep .= "\$sc = new Schema('$tb_name');\r\n";
        }
        
        $up_rep .= "";        
        Strings::replace('### UP', $up_rep, $file);

        // destination
        $dest_path = $path . $filename;

        $this->write($dest_path, $file, false);
    }    


    function provider($name, ...$opt) {
        $this->setup($name);    
    
        $filename = $this->camel_case . 'ServiceProvider'.'.php';
        $dest_path = self::SERVICE_PROVIDERS_PATH . $filename;

        $protected = $this->hasFileProtection($filename, $dest_path, $opt);

        $file = file_get_contents(self::SERVICE_PROVIDER_TEMPLATE);
        $file = str_replace('__NAME__', $this->camel_case . 'ServiceProvider', $file);
        
        $this->write($dest_path, $file, $protected);
    }

    function helper($name, ...$opt) 
    {
        $filename = $name.'.php';
        $dest_path = HELPERS_PATH . $filename;

        $file = file_get_contents(self::HELPER_TEMPLATE);

        $protected = $this->hasFileProtection($filename, $dest_path, $opt);
        $this->write($dest_path, $file, $protected);
    }

    

}