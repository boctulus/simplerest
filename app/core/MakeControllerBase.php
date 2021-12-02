<?php

namespace simplerest\core;

use simplerest\controllers\MakeController;
use simplerest\core\Controller;
use simplerest\core\Request;
use simplerest\libs\Factory;
use simplerest\libs\StdOut;
use simplerest\libs\DB;
use simplerest\libs\Files;
use simplerest\libs\Strings;
use simplerest\libs\Schema;

/*
    Class builder
*/
class MakeControllerBase extends Controller
{
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
    const MSG_CONST_TEMPLATE = self::TEMPLATES . 'Msg.php';
    
    protected $class_name;
    protected $ctr_name;
    protected $api_name; 
    protected $camel_case;
    protected $snake_case;
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

    function help(){
        echo <<<STR
        MAKE COMMAND HELP

        In general, 

        make {name} [options]
          
        make helper my_cool_helper [--force | -f] [ --unignore | -u ]

        make lib my_lib [--force | -f] [ --unignore | -u ]

        make schema super_awesome [--force | -f] [ --unignore | -u ]

        make model SuperAwesomeModel  [--force | -f] [ --unignore | -u ]
        make model SuperAwesome [--force | -f] [ --unignore | -u ]
        make model super_awesome  [--force | -f] [ --unignore | -u ]
        
        make controller SuperAwesome  [--force | -f] [ --unignore | -u ]
        make controller folder/SuperAwesome  [--force | -f] [ --unignore | -u ]

        make console SuperAwesome  [--force | -f] [ --unignore | -u ]
        make console folder/SuperAwesome  [--force | -f] [ --unignore | -u ]

        make api SuperAwesome   [--force | -f] [ --unignore | -u ]  
        make api super_awesome  [--force | -f] [ --unignore | -u ]

        make api all --from:dsi [--force | -f] [ --unignore | -u ]

        <-- "from:" is required in this case.]
             
        make migration {name} [ --dir= | --file= ] [ --table= ] [ --class_name= ] [ --to= ]

        make any Something  [--schema | -s] [--force | -f] [ --unignore | -u ]
                            [--model | -m] [--force | -f] [ --unignore | -u ]
                            [--controller | -c] [--force | -f] [ --unignore | -u ]
                            [--console ] [--force | -f] [ --unignore | -u ]
                            [--api | -a] [--force | -f] [ --unignore | -u ]
                            [--provider | --service | -p] [--force | -f] [ --unignore | -u ]                             

                            -sam  = -s -a -m
                            -samf = -s -a -m -f
        
        make migration rename_some_column
        make migration another_table_change --table=foo
        make migration books --table=books --class_name=BooksAddDescription --to:main
        make migration --class_name=Filesss --table=files --to:main --dir='test\sub3'

        make db_scan [ -- from= ]

        make constants
        
        Examples:
        
        make any baz -s -m -a -f
        make any tbl_contacto -sam --from:dsi
        make any all -sam  --from:dsi
        make any all -samf --from:dsi
        make any all -s -f --from:main 
        make any all -s -f --from:main --unignore                       
              
        STR;

        print_r(PHP_EOL);
    }


    protected function setup(string $name) {
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
            StdOut::pprint("Nothing to do. Please specify action using options.\r\nUse 'make help' for help.\r\n");
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
            if (in_array('-l', $opt) || in_array('--lib', $opt)){
                $opt = array_intersect($opt, ['-f', '--force']);
                $this->lib($name, ...$opt);
            }
        }            
    }

    /*
        File generation
    */
    function generic($name, $subfix, $namespace, $dest_path, $template_path, ...$opt) {        
        $name = str_replace('/', DIRECTORY_SEPARATOR, $name);

        $unignore = false;

        foreach ($opt as $o){ 
            if (preg_match('/^--even-ignored$/', $o, $matches) ||               
                preg_match('/^--unignore$/', $o, $matches) ||
                preg_match('/^-u$/', $o, $matches)){
                $unignore = true;
            }
        }
        
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

        $protected = $unignore ? false : $this->hasFileProtection($filename, $dest_path, $opt);
        
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
        $unignore = false;

        foreach ($opt as $o){            
            if (preg_match('/^--from[=|:]([a-z][a-z0-9A-Z_]+)$/', $o, $matches)){
                $from_db = $matches[1];
                DB::getConnection($from_db);
            }

            if (preg_match('/^--even-ignored$/', $o, $matches) ||               
                preg_match('/^--unignore$/', $o, $matches) ||
                preg_match('/^-u$/', $o, $matches)){
                $unignore = true;
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
    
        $filename  = $this->camel_case.'.php';
        $dest_path = API_PATH . $filename;

        $protected = $unignore ? false : $this->hasFileProtection($filename, $dest_path, $opt);

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

        // el resto (default)
        return 'STR'; 
    }

    /*
        Return if file is protected and not should be overwrited
    */
    protected function hasFileProtection(string $filename, string $dest_path, Array $opt) : bool {
        if (in_array($dest_path, $this->excluded_files)){
            StdOut::pprint("[ Skipping ] '$dest_path'. File was ignored\r\n"); 
            return true; 
        } elseif (file_exists($dest_path)){
            if (!in_array('-f', $opt) && !in_array('--force', $opt)){
                StdOut::pprint("[ Skipping ] '$dest_path'. File already exists. Use -f or --force if you want to override.\r\n");
                return true;
            } elseif (!is_writable($dest_path)){
                StdOut::pprint("[ Error ] '$dest_path'. File is not writtable. Please check permissions.\r\n");
                return true;
            }
        }
    
        if (in_array($filename, $this->excluded_files)){
            StdOut::pprint("[ Skipping ] '$dest_path'. File was ignored\r\n"); 
            return true; 
        } elseif (file_exists($dest_path)){
            if (!in_array('-f', $opt) && !in_array('--force', $opt)){
                StdOut::pprint("[ Skipping ] '$dest_path'. File already exists. Use -f or --force if you want to override.\r\n");
                return true;;
            } elseif (!is_writable($dest_path)){
                StdOut::pprint("[ Error ] '$dest_path'. File is not writtable. Please check permissions.\r\n");
                return true;
            }
        }

        return false;
    }

    protected function write(string $dest_path, string $file, bool $protected){
        if ($protected){
            return;
        }

        if (!is_writable($dest_path)){
            //throw new \Exception("$dest_path is not writable");
        }

        $ok = (bool) file_put_contents($dest_path, $file);
        
        if (!$ok) {
            throw new \Exception("Failed trying to write $dest_path");
        } else {
            StdOut::pprint("$dest_path was generated\r\n");
        } 

        return $ok;
    }

    function pivot_scan(...$opt)
    {     
        static $pivot_data = [];

        foreach ($opt as $o){            
            if (preg_match('/^--from[=|:]([a-z][a-z0-9A-Z_]+)$/', $o, $matches)){
                $from_db = $matches[1];
                DB::getConnection($from_db);
            }
        }

        $folder = '';

        if (!isset($from_db) && DB::getCurrentConnectionId() == null){
            $folder = DB::getDefaultConnectionId();
            $db_conn_id = $folder;
        } else {
            $db_conn_id = DB::getCurrentConnectionId();
            if ($db_conn_id == DB::getDefaultConnectionId()){
                $folder = $db_conn_id;
            } else {
                $group = DB::getTenantGroupName($db_conn_id);

                if ($group){
                    $folder = $group;
                }
            }
        }

        if (!empty($pivot_data[$db_conn_id])){
            return $pivot_data[$db_conn_id];
        }

        $pivot_file = 'Pivots.php';
        $dir = SCHEMA_PATH . $folder;

        $pivots = [];
        $relationships = [];
        $pivot_fks = [];

        foreach (new \DirectoryIterator($dir) as $fileInfo) {
            if ($fileInfo->isDot()  || $fileInfo->isDir()) continue;
            
            $filename = $fileInfo->getFilename();

            if (!Strings::endsWith('Schema.php', $filename)){
                continue;
            }

            $full_path = $dir . '/' . $filename;            
            $class_name = Strings::getClassNameByFileName($full_path);

            if (!class_exists($class_name)){
                throw new \Exception ("Class '$class_name' doesn't exist in $filename. Full path: $full_path");
            }

            $schema  = $class_name::get();
            
            if (!isset($schema['relationships_from'])){
                throw new \Exception("Undefined 'relationships_from' for $filename. Full path $full_path");
            }

            $rels = $schema['relationships_from'];

            // Debe haber 2 FK(s)
            if (count($rels) != 2){
                continue;
            }

            $relationships[$schema['table_name']] = $rels;

            /*
                Asumo que solo existe una tabla puente entre ciertas tablas
            */
            foreach ($rels as $tb => $r){
                $pivots[$schema['table_name']][] = $tb;
            }

            /*
                Construyo $pivot_fks  
            */
            foreach ($pivots as $pv => $tbs){
                $rels = $relationships[$pv];
                $tbs  = array_keys($rels);
                
                if (count($rels[$tbs[0]]) == 1){
                    $fk1  = substr($rels[$tbs[0]][0][1], strlen($pv)+1);
                } else {
                    $fk1 = [];
                    foreach ($rels[$tbs[0]] as $r){
                        $_f = explode('.', $r[1]);
                        $fk1[] = $_f[1]; 
                    }
                }

                if (count($rels[$tbs[1]]) == 1){
                    $fk2  = substr($rels[$tbs[1]][0][1], strlen($pv)+1);
                } else {
                    $fk2 = [];
                    foreach ($rels[$tbs[1]] as $r){
                        $_f = explode('.', $r[1]);
                        $fk2[] = $_f[1]; 
                    }
                }
            
                $pivot_fks[$pv] = [
                    $tbs[0] => $fk1, 
                    $tbs[1] => $fk2
                ];   
            }
        }   

        $_pivots = [];
        foreach ($pivots as $pv => $tbs){
            sort($tbs);

            $str_tbs = implode(',', $tbs);
            $_pivots[$str_tbs] = $pv; 
        }

        $path = str_replace('//', '/', $dir . '/' . $pivot_file);
        
        $pivot_data[$db_conn_id] = [
            'pivots' => $_pivots,
            'pivot_fks' => $pivot_fks,
            'relationships' => $relationships
        ];

        $this->write($path, '<?php '. PHP_EOL. PHP_EOL . 
          '$pivots = ' .var_export($_pivots, true) . ';' . PHP_EOL . PHP_EOL .
          '$pivot_fks = ' .var_export($pivot_fks, true) . ';' . PHP_EOL . PHP_EOL .
          '$relationships = ' . var_export($relationships, true) . ';' . PHP_EOL
        , false);

        #StdOut::pprint("Please run 'php com make rel_scan --from:$db_conn_id'");
    }

    function relation_scan(...$opt)
    {
        foreach ($opt as $o){            
            if (preg_match('/^--from[=|:]([a-z][a-z0-9A-Z_]+)$/', $o, $matches)){
                $from_db = $matches[1]; 
                DB::getConnection($from_db);               
            } 
        }

        $folder = '';

        if (!isset($from_db) && DB::getCurrentConnectionId() == null){
            $folder = $from_db = DB::getDefaultConnectionId();
        } else {
            $db_conn_id = DB::getCurrentConnectionId();
            $from_db    = $db_conn_id;

            if ($db_conn_id == DB::getDefaultConnectionId()){
                $folder  = $db_conn_id;
            } else {
                $group = DB::getTenantGroupName($db_conn_id);

                if ($group){
                    $folder = $group;
                }
            }

        }

        $rel_file = 'Relations.php';
        $dir  = SCHEMA_PATH . $folder;
        $path = str_replace('//', '/', $dir . '/' . $rel_file);
        
        $relation_type = [];
        $multiplicity  = [];

        $tables = Schema::getTables();
        foreach ($tables as $t){
            $rl = Schema::getAllRelations($t);
            $related_tbs = array_keys($rl);
            
            foreach ($related_tbs as $rtb){
                $relation_type["$t~$rtb"] = get_rel_type($t, $rtb, null, $from_db);
                $multiplicity["$t~$rtb"]  = is_mul_rel($t, $rtb, null, $from_db); 
            }
        }

        /*
            Repito para tablas puente con las que no hay relación directa
            => no aparencen antes
        */

        if (isset($pivot_data['pivots'])){
            $pivots = $pivot_data['pivots'];
        } else {
            $dir = get_schema_path(null, $from_db ?? null);
            include $dir . 'Pivots.php'; 
        }        

        // 

        $pivot_pairs = array_keys($pivots);    
        foreach ($pivot_pairs as $pvp){
            list($t, $rtb) = explode(',', $pvp);
            
            $relation_type["$t~$rtb"] = 'n:m';
            $relation_type["$rtb~$t"] = 'n:m';
            
            $multiplicity["$t~$rtb"]  = true;
            $multiplicity["$rtb~$t"]  = true;
        }    

        $relation_type_str = var_export($relation_type, true);
        $multiplicity_str  = var_export($multiplicity, true);

        $relation_type_str = Strings::tabulate($relation_type_str, 3, 0);
        $multiplicity_str  = Strings::tabulate($multiplicity_str, 3, 0);

        $this->write($path, '<?php '. PHP_EOL. PHP_EOL .  
        Strings::tabulate("return [
        'relation_type'=> $relation_type_str,
        'multiplicity' => $multiplicity_str
        ];", 0, 0, -8), false);
    }

    // alias
    function rel_scan(...$opt){
        $this->relation_scan(...$opt);
    }

    /*
        Solución parche
    */
    function db_scan(...$opt){
       $params = implode(' ',$opt);

        echo shell_exec("php com make pivot_scan $params");
        echo shell_exec("php com make relation_scan $params");

        // foreach ($opt as $o){
        //     if (preg_match('/^--from[=|:]([a-z][a-z0-9A-Z_-]+)$/', $o, $matches)){
        //         $from_db = $matches[1];
        //     }
        // }
    
        // $this->pivot_scan("--from:$from_db");
        // $this->relation_scan(...$opt);
    }

    function schema($name, ...$opt) 
    {
        $unignore = false;

        foreach ($opt as $o){            
            if (preg_match('/^--from[=|:]([a-z][a-z0-9A-Z_-]+)$/', $o, $matches)){
                $from_db = $matches[1];
                DB::getConnection($from_db);
            }

            if (preg_match('/^--even-ignored$/', $o, $matches) ||               
                preg_match('/^--unignore$/', $o, $matches) ||
                preg_match('/^-u$/', $o, $matches)){
                $unignore = true;
            }
        }

        if (!isset($from_db)){
            $from_db = get_default_connection_id();
        }

        if ($name == 'all'){
            $tables = Schema::getTables();

            foreach ($tables as $table){
                $this->schema($table, ...$opt);
            }

            $this->db_scan(...$opt);

            return;
        }

        $this->setup($name);    

        if (!Schema::hasTable($name)){
            StdOut::pprint("Table '$name' not found. It's case sensitive\r\n");
            return;
        }

        $filename = $this->camel_case.'Schema.php';

        $file = file_get_contents(self::SCHEMA_TEMPLATE);
        $file = str_replace('__NAME__', $this->camel_case.'Schema', $file);
        
        // destination

        DB::getConnection();
        $current = DB::getCurrentConnectionId(true);

        if ($current == config()['db_connection_default']){
            $file = str_replace('namespace simplerest\schemas', 'namespace simplerest\schemas' . "\\$current", $file);

            Files::mkdir_ignore(SCHEMA_PATH . $current);
            $dest_path = SCHEMA_PATH . "$current/". $filename;

        }  else {
            $group = DB::getTenantGroupName($current);

            if ($group){
                $current = $group;
                
                $file = str_replace('namespace simplerest\schemas', 'namespace simplerest\schemas' . "\\$current", $file);
                Files::mkdir_ignore(SCHEMA_PATH . $current);
                $dest_path = SCHEMA_PATH . "$current/". $filename;;
            } else {
                $dest_path = SCHEMA_PATH . $filename;
            }
        } 
        
        $protected = $unignore ? false : $this->hasFileProtection($filename, $dest_path, $opt);
        
        try {
            $fields = DB::select("SHOW COLUMNS FROM {$this->snake_case}", [], 'ASSOC', $from_db);
        } catch (\Exception $e) {
            StdOut::pprint('[ SQL Error ] '. DB::getLog(). "\r\n");
            StdOut::pprint($e->getMessage().  "\r\n");
            exit;
        }
        
        $id_name =  NULL;
        $uuid = false;
        $field_names  = [];
        $types = [];
        $types_raw = [];

        $nullables = [];
        $rules   = [];
        $_rules  = [];
        $_rules_ = [];
        $pri_components = [];
        $autoinc = null;
        $unsigned = [];
        $uniques  = [];
        $tinyint = [];
        $emails  = [];
        $double  = [];
        $decimal = [];

        foreach ($fields as $ix => $field){
            //dd($field, $ix);

            $field_name    = $field['Field'];
            $type          = $field['Type'];

            $field_names[] = $field_name;

            $comment = Schema::getColumnComment($name, $field_name)['COLUMN_COMMENT'];

            if ($comment == 'email' || $comment == 'e-mail'){
                $emails[] = $field_name;
            }

            if ($field['Null']  == 'YES' || $field['Default'] !== NULL) { 
                $nullables[] = $field_name;
            }

            #dd($field, "FIELD $field_name"); //
            
            if ($field['Key'] == 'PRI'){ 
                // if ($id_name != NULL){
                //     $msg = "A table should have simple Primary Key by convention for table \"$name\"";
                //     Files::logger($msg);      
                // }
                
                $id_name = $field['Field'];
                $pri_components[] = $field_name;
            } else if ($field['Key'] == 'UNI'){ 
                $uniques[] = $field_name;
            }                

            if ($field['Extra'] == 'auto_increment') { 
                //$not_fillable[] = $field['Field'];
                $nullables[] = $field_name;
                $autoinc     = $field_name;
            }
            
            if (Strings::containsWord('unsigned', $type)) { 
                $unsigned[] = $field_name;
            }

            if (Strings::startsWith('tinyint', $type)) { 
                $tinyint[] = $field_name; 
            }

            if ($type == 'double'){
                $double[] = $field_name;
            }

            if (Strings::startsWith('decimal(', $type)){
                $nums = substr($type, strlen('decimal('), -1);  
                $_rules_[$field_name]['type'] = "decimal($nums)";            
            }
            

            $types[$field['Field']] = $this->get_pdo_const($field['Type']);
            $types_raw[$field['Field']] = $field['Type'];
         
            if (!$autoinc && $field['Key'] == 'PRI'){ 
                $field_name_lo = strtolower($field['Field']);
                if ($field_name_lo == 'uuid' || $field_name_lo == 'guid'){
                    if ($types[$field['Field']] != 'STR'){
                        printf("Warning: {$field['Field']} has not a valid type for UUID ***\r\n");
                    }

                    $uuid = $field['Field']; /// *
                    $id_name = $uuid;   /// *
                }
            }    
        }

        if (count($pri_components) >1){
            // busco si hay un AUTOINC
            if (!empty($autoinc)){
                $id_name = $autoinc; 
            } else {
                //$msg = "A table should have simple Primary Key by convention for table \"$name\"";
                //Files::logger($msg);  
                //dd($msg, 'WARNING'); 
            }
        }

        $nullables = array_unique($nullables);

        $escf = function($x){ 
            return "'$x'"; 
        };

        $_attr_types = [];

        foreach ($types as $f => $type){
            $_attr_types[] = "\t\t\t\t'$f' => '$type'";

            $_rules[$f] = [];

            if (isset($_rules_[$f])){
                $_rules[$f] = $_rules_[$f];
            }

            $type = strtolower($type);

            if (!isset($_rules[$f]['type'])){
                $_rules[$f]['type'] = $type;
            }

            // emails
            if (in_array($f, $emails)){
                $_rules[$f]['type'] = 'email';
            } 

            // duble
            if (in_array($f, $double)){
                $_rules[$f]['type'] = 'double';
            }

            // varchars
            if (preg_match('/^(varchar)\(([0-9]+)\)$/', $types_raw[$f], $matches)){
                $len = $matches[2];
                $_rules[$f]['max'] = $len;
            } 

            /*
              https://www.php.net/manual/en/language.types.type-juggling.php
            */

            // varbinary
            if (preg_match('/^(|varbinary)\(([0-9]+)\)$/', $types_raw[$f], $matches)){
                $len = $matches[2];
                $_rules [$f] = ['max' => $len];
            }  

            // binary
            if (preg_match('/^(binary)\(([0-9]+)\)$/', $types_raw[$f], $matches)){
                $len = $matches[2];
                $_rules[$f]['max'] = $len;
            } 

            // unsigned
            if (in_array($f, $unsigned)){
                $_rules[$f]['min'] = 0;
            } 

            // bool
            if (in_array($f, $tinyint)){
                $_rules[$f]['type'] = 'bool';
            } 

            // timestamp
            if (strtolower($types_raw[$f]) == 'timestamp'){
                $_rules[$f]['type'] = 'timestamp';
            }

            // datetime
            if (strtolower($types_raw[$f]) == 'datetime'){
                $_rules[$f]['type'] = 'datetime';
            }

            // date
            if (strtolower($types_raw[$f]) == 'date'){
                $_rules[$f]['type'] =  'date';
            }

            // time
            if (strtolower($types_raw[$f]) == 'time'){
                $_rules[$f]['type'] =  'time';  
            }


            /*
                Para blobs

                https://www.virendrachandak.com/techtalk/how-to-get-size-of-blob-in-mysql/
            */

            if (!in_array($f, $nullables)){
                $_rules[$f]['required'] = 'true';
            }

            $tmp = [];  
            foreach ($_rules[$f] as $k => $v){
                $vv = ($k == 'max' || $k == 'min' || $k == 'required') ?  $v : "'$v'";
                $tmp[] = "'$k'" . ' => ' . $vv;
            }

            $_rules[$f] = "\t\t\t\t'$f' => " . '[' . implode(', ', $tmp) . ']';
        }

        $attr_types = "[\r\n". implode(",\r\n", $_attr_types). "\r\n\t\t\t]";
        $rules  = "[\r\n". implode(",\r\n", $_rules). "\r\n\t\t\t]";


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


        $relations_from = '';
        $rels = Schema::getAllRelations($name, true, false);

        $g = [];
        $c = 0;
        foreach ($rels as $tb => $rs){
            $grp = "\t\t\t\t\t" . implode(",\r\n\t\t\t\t\t", $rs);
            $grp = ($c != 0 ? "\t\t\t\t" : '') . "'$tb' => [\r\n$grp\r\n\t\t\t\t]";
            $g[] = $grp;
            $c++;
        }

        $relations_from = implode(",\r\n", $g);

        $fks = Schema::getFKs($name);
        $expanded_relations      = Strings::tabulate(var_export(Schema::getAllRelations($name, false), true), 4, 0);
        $expanded_relations_from = Strings::tabulate(var_export(Schema::getAllRelations($name, false, false), true), 4, 0);
        
        
        Strings::replace('__TABLE_NAME__', "'{$this->snake_case}'", $file);  
        Strings::replace('__ID__', !empty($id_name) ? "'$id_name'" : 'null', $file);   
        Strings::replace('__AUTOINCREMENT__', !empty($autoinc) ? "'$autoinc'" : 'null', $file);       
        Strings::replace('__ATTR_TYPES__', $attr_types, $file);
        Strings::replace('__PRIMARY__', '['. implode(', ',array_map($escf,  $pri_components)). ']',$file);
        Strings::replace('__NULLABLES__', '['. implode(', ',array_map($escf, $nullables)). ']',$file);        
        //Strings::replace('__NOT_FILLABLE__', '['.implode(', ',array_map($escf, $not_fillable)). ']',$file);
        Strings::replace('__UNIQUES__', '['. implode(', ',array_map($escf,  $uniques)). ']',$file);
        Strings::replace('__RULES__', $rules, $file);
        Strings::replace('__FKS__', '['. implode(', ',array_map($escf,  $fks)). ']',$file);
        Strings::replace('__RELATIONS__', $relations, $file);
        Strings::replace('__EXPANDED_RELATIONS__', $expanded_relations, $file);
        Strings::replace('__RELATIONS_FROM__', $relations_from, $file);
        Strings::replace('__EXPANDED_RELATIONS_FROM__', $expanded_relations_from, $file);
        
        $ok = $this->write($dest_path, $file, $protected);
    }

    protected function getUuid(){
        try {
            $fields = DB::select("SHOW COLUMNS FROM {$this->snake_case}");
        } catch (\Exception $e) {
            StdOut::pprint('[ SQL Error ] '. DB::getLog(). "\r\n");
            StdOut::pprint($e->getMessage().  "\r\n");
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
        $unignore = false;

        foreach ($opt as $o){            
            if (preg_match('/^--from[=|:]([a-z][a-z0-9A-Z_]+)$/', $o, $matches)){
                $from_db = $matches[1];
                DB::getConnection($from_db);
            }

            if (preg_match('/^--even-ignored$/', $o, $matches) ||               
                preg_match('/^--unignore$/', $o, $matches) ||
                preg_match('/^-u$/', $o, $matches)){
                $unignore = true;
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

        $file = file_get_contents(self::MODEL_TEMPLATE);
        $file = str_replace('__NAME__', $this->camel_case.'Model', $file);
       

        $imports = [];
        $traits  = [];
        $proterties = [];


        // destination

        DB::getConnection();
        $current = DB::getCurrentConnectionId(true);

        $folder = '';
        if ($current == config()['db_connection_default']){
            $file = str_replace('namespace simplerest\models', 'namespace simplerest\models' . "\\$current", $file);

            Files::mkdir_ignore(MODELS_PATH . $current);
            $dest_path = MODELS_PATH . "$current/". $filename;

        }  else {
            $group = DB::getTenantGroupName($current);

            if ($group){
                $current = $group;
                
                $file = str_replace('namespace simplerest\models', 'namespace simplerest\models' . "\\$current", $file);
                Files::mkdir_ignore(MODELS_PATH . $current);
                $dest_path = MODELS_PATH . "$current/". $filename;
            } else {
                $dest_path = MODELS_PATH . $filename;
            }
        } 
        
        $protected = $unignore ? false : $this->hasFileProtection($filename, $dest_path, $opt);

       
        if (!empty($current)){
            $folder = "$current\\";
        }

        $imports[] = "use simplerest\schemas\\$folder{$this->camel_case}Schema;";
       
        Strings::replace('__SCHEMA_CLASS__', "{$this->camel_case}Schema", $file); 


        $uuid = $this->getUuid();
        if ($uuid){

            $imports[] = 'use simplerest\traits\Uuids;';
            $traits[] = 'use Uuids;';      
        }

        Strings::replace('### IMPORTS', implode("\r\n", $imports), $file); 
        Strings::replace('### TRAITS',  implode("\r\n\t", $traits), $file); 
        Strings::replace('### PROPERTIES', implode("\r\n\t", $proterties), $file); 

        $this->write($dest_path, $file, $protected);
    }

    function migration(...$opt) 
    {
        if (count($opt)>0 && !Strings::startsWith('-', $opt[0])){
            $name = $opt[0];
            unset($opt[0]);
        }

        if (isset($name)){
            $this->setup($name);
        }        

        $file = file_get_contents(self::MIGRATION_TEMPLATE);

        $path    = MIGRATIONS_PATH;
        $to_db   = null;
        $tb_name = null;    
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

            if (Strings::startsWith('--dir=', $o)){
                // Convert windows directory separator into *NIX
                $o = str_replace('\\', '/', $o);

                if (preg_match('~^--dir[=|:]([a-z][a-z0-9A-Z_/]+)$~', $o, $matches)){
                    $dir= $matches[1];
                }
            }
            

            /*
                The only condition to work is the script should be enclosed with double mark quotes ("")
                and it should not contain any double mark inside
            */
            if (preg_match('/^--from_script[=|:]"([^"]+)"/', $o, $matches)){
                $script = $matches[1];
            }
        }

        if (!isset($name)){
            if (isset($class_name)){
                $this->setup($class_name);
            } else {
                if (!is_null($tb_name)){
                    $this->setup($tb_name);;
                }
            }
        }  

        if (is_null($this->camel_case)){
            throw new \InvalidArgumentException("No name for migration class");
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
            $up_rep .= "DB::setConnection('$to_db');\r\n\r\n";
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
        $date = date("Y_m_d");
        $secs = time() - 1603750000;
        $filename = $date . '_'. $secs . '_' . $this->snake_case . '.php'; 

        $dest_path = $path . $filename;

        $this->write($dest_path, $file, false);
    }    


    function provider($name, ...$opt) {
        $this->setup($name);    

        $unignore = false;

        foreach ($opt as $o){ 
            if (preg_match('/^--even-ignored$/', $o, $matches) ||               
                preg_match('/^--unignore$/', $o, $matches) ||
                preg_match('/^-u$/', $o, $matches)){
                $unignore = true;
            }
        }

        $filename = $this->camel_case . 'ServiceProvider'.'.php';
        $dest_path = self::SERVICE_PROVIDERS_PATH . $filename;

        $protected = $unignore ? false : $this->hasFileProtection($filename, $dest_path, $opt);

        $file = file_get_contents(self::SERVICE_PROVIDER_TEMPLATE);
        $file = str_replace('__NAME__', $this->camel_case . 'ServiceProvider', $file);
        
        $this->write($dest_path, $file, $protected);
    }

    function helper($name, ...$opt) 
    {
        $unignore = false;

        foreach ($opt as $o){ 
            if (preg_match('/^--even-ignored$/', $o, $matches) ||               
                preg_match('/^--unignore$/', $o, $matches) ||
                preg_match('/^-u$/', $o, $matches)){
                $unignore = true;
            }
        }

        $filename = $name.'.php';
        $dest_path = HELPERS_PATH . $filename;

        $file = file_get_contents(self::HELPER_TEMPLATE);

        $protected = $unignore ? false : $this->hasFileProtection($filename, $dest_path, $opt);
        $this->write($dest_path, $file, $protected);
    }

    function constants(...$opt){
        include_once CONFIG_PATH . '/messages.php';

        $lines = explode(PHP_EOL, $_messages);

        $consts = '';
        foreach ($lines as $line){
            $line = trim($line);

            if (empty($line)){
                continue;
            }

            if (!preg_match('/([A-Z_>]+)[ \t]+([A-Z_]+)[ \t]+["\'](.*)["\']/',$line, $matches)){
                echo "Unable to compile $line\r\n";
                continue;
            }

            $type = $matches[1];
            $code = $matches[2];
            $text = $matches[3];

            $name = $code;  

            $consts .= "\r\n\t" . "const $name = [
                'type' => '$type',
                'code' => '$code',
                'text' => \"$text\"
            ];" . "\r\n";

        }

        $filename  = 'Msg.php';
        $dest_path = LIBS_PATH . $filename;

        $protected = $this->hasFileProtection($filename, $dest_path, $opt);

        $data = file_get_contents(self::MSG_CONST_TEMPLATE);
        $data = str_replace('# __CONSTANTS', $consts, $data);

        $this->write($dest_path, $data, $protected);;
    }
    
    
}