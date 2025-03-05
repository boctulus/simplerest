<?php

use simplerest\core\interfaces\ICommand;
use simplerest\core\libs\Config;
use simplerest\core\libs\DB;
use simplerest\core\libs\Factory;
use simplerest\core\libs\Files;
use simplerest\core\libs\i18n\Translate;
use simplerest\core\libs\PHPLexicalAnalyzer;
use simplerest\core\libs\Schema;
use simplerest\core\libs\StdOut;
use simplerest\core\libs\Strings;
use simplerest\core\traits\CommandTrait;

class MakeCommand implements ICommand 
{   
    const SERVICE_PROVIDERS_PATH = ROOT_PATH . 'packages' . DIRECTORY_SEPARATOR;

    const TEMPLATES = CORE_PATH . 'templates' . DIRECTORY_SEPARATOR;

    const MODEL_TEMPLATE  = self::TEMPLATES . 'Model.php';
    const MODEL_NO_SCHEMA_TEMPLATE  = self::TEMPLATES . 'ModelWithoutSchema.php';
    const SCHEMA_TEMPLATE = self::TEMPLATES . 'Schema.php';
    const MIGRATION_TEMPLATE = self::TEMPLATES . 'Migration.php'; 
    const MIGRATION_TEMPLATE_CREATE = self::TEMPLATES . 'Migration_New.php'; 
    const API_TEMPLATE = self::TEMPLATES . 'ApiRestfulController.php';
    const SERVICE_PROVIDER_TEMPLATE = self::TEMPLATES . 'ServiceProvider.php'; 
    const SYSTEM_CONST_TEMPLATE = self::TEMPLATES . 'SystemConstants.php';
    const INTERFACE_TEMPLATE = self::TEMPLATES . 'Interface.php';
    const HELPER_TEMPLATE = self::TEMPLATES . 'Helper.php'; 
    const LIBS_TEMPLATE = self::TEMPLATES . 'Lib.php';
    const TRAIT_TEMPLATE = self::TEMPLATES . 'Trait.php';
    const CRONJOBS_TEMPLATE = self::TEMPLATES . 'CronJob.php';
    const TASK_TEMPLATE = self::TEMPLATES . 'Task.php';
    const MIDDLEWARE_TEMPLATE = self::TEMPLATES . 'Middleware.php';
    const EXCEPTION_TEMPLATE = self::TEMPLATES . 'Exception.php';
    const COMMAND_TEMPLATE = self::TEMPLATES . 'Command.php';


    protected $namespace;
    protected $table_name;
    protected $class_name;
    protected $ctr_name;
    protected $api_name; 
    protected $camel_case;
    protected $snake_case;
    protected $excluded_files = [];
    protected $all_uppercase = false;

    use CommandTrait;

    function __construct()
    {
        if (php_sapi_name() != 'cli'){
            Factory::response()->send("Error: Make can only be excecuted in console", 403);
        }

        $this->namespace = Config::get()['namespace'];

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
    }

    protected function setup(string $name) {
        $this->table_name    = $name; // nuevo: para cubrirme de DBs que no siguen convenciones
        $this->all_uppercase = Strings::isAllCaps($name); 
        
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

    function help($name = null, ...$args){
        $str = <<<STR
        In general, 

        make {name} [options]

        Most common options are:

        --force 
        --unignore | --retry
        --remove
        --strict
          
        make helper my_helper [--force | -f] [ --unignore | -u ] [ --strict ] [ --remove ]
        make lib my_lib [--force | -f] [ --unignore | -u ] [ --strict ] [ --remove ]
        make interface [ -- from= ] [--force | -f] [ --unignore | -u ] [ --strict ] [ --remove ]
        make schema my_table [ --from:{conn_id} ] [--force | -f] [ --unignore | -u ] [ --strict ] [ --remove ]
        make schema all [ --from:{conn_id} ] [ --unignore | -u ] [ --strict ] [ --except={table1,table2,table3} ]
        make model my_table  [--force | -f] [ --unignore | -u ] [ --no-check | --no-verify ] [ --no-schema | -x ] [ --strict ] [ --remove ]
        make view my_view  [--force | -f] [ --unignore | -u ] [ --remove ]

        make controller my_controller  [--force | -f] [ --unignore | -u ] [ --strict ] [ --remove ]
        make controller folder/my_controller  [--force | -f] [ --unignore | -u ] [ --strict ] [ --remove ]

        make console my_console_ctrl  [--force | -f] [ --unignore | -u ] [ --strict ] [ --remove ]
        make console folder/my_console_ctrl  [--force | -f] [ --unignore | -u ] [ --strict ] [ --remove ]

        make api my_table [ --from:{conn_id} ]  [--force | -f] [ --unignore | -u ] [ --strict ] [ --remove ]
        make api my_table [ --from:{conn_id} ] [--force | -f] [ --unignore | -u ] [ --strict ] [ --remove ]

        make api all --from:some_conn_id [--force | -f] [ --unignore | -u ] [ --strict ] [ --remove ]

        <-- "from:" is required in this case.]
        
        make schema genders --from:mpo
        make schema gender --table=genders --from:mpo
        make schema all --from:mpo
        make schema all --from:mpp --except=migrations,password_resets,users

        make model medios_transporte --no-schema --from:az

        make widget [ --include-js | --js ]

        make command myCommand
             
        make migration {name} [ --dir= | --file= ] [ --table= ] [ --class_name= ] [ --to= ] [ --create | --edit ] [ --strict ] [ --remove ]

        make any Something  [--schema | -s] [--force | -f] [ --unignore | -u ]
                            [--model | -m] [--force | -f] [ --unignore | -u ]
                            [--controller | -c] [--force | -f] [ --unignore | -u ]
                            [--console ] [--force | -f] [ --unignore | -u ]
                            [--api | -a] [--force | -f] [ --unignore | -u ]
                            [--provider | --service | -p] [--force | -f] [ --unignore | -u ]                             

                            -sam  = -s -a -m
                            -samf = -s -a -m -f

        # Database scan

        make db_scan [ -- from= ]


        # Update

        make update {version}

        Ex.

        make update 0.8.0


        # Translation files
        
        make trans [--preset={name}]
        make trans --pot [--domain={text-domain}]
        make trans --po --mo [--preset={name}]
        make trans --po
        make trans [--from={dir}] [--to={dir}] [--domain={text-domain}] 

        Ex.

        make trans --from='/home/www/woo1/wp-content/plugins/import-quoter-cl/locale'
        make trans --domain=mutawp --to='D:\www\woo2\wp-content\plugins\mutawp\languages' --preset=wp


        # System constants

        make system_constants


        # Acl file

        make acl
        make acl [ --debug ]


        # Pages

        make page

        make page admin/graficos
        make page admin/control_usuarios


        # Migrations
                
        make migration rename_some_column --table=foo
        make migration --dir=test --name=books
        make migration books --table=books --class_name=BooksAddDescription --to:main        
        make migration --class_name=Filesss --table=files --to:main --dir='test\sub3'
        make migration --dir=test --to=az --table=boletas --class_name=BoletasDropNullable
        make migration brands --dir=giglio --to=giglio --create


        # Inline migrations
        
        make migration foo -e --dropColumn=algun_campo
        make migration foo -e --renameColumn=viejo_nombre,nuevo_nombre
        make migration foo -e --renameTable=viejo_nombre,nuevo_nombre
        make migration foo -e --nullable=campo
        make migration foo -e --dropNullable=campo
        make migration foo -e --primary=campo
        make migration foo -e --dropPrimary=campo
        make migration foo -e --unsigned=campo
        make migration foo -e --zeroFill=campo
        make migration foo -e --binaryAttr=campo
        make migration foo -e --dropAttributes=campo
        make migration foo -e --addUnique=campo
        make migration foo -e --dropUnique=campo
        make migration foo -e --addSpatial=campo
        make migration foo -e --dropSpatial=campo
        make migration foo -e --dropForeign=campo
        make migration foo -e --addIndex=campo
        make migration foo -e --dropIndex=campo
        make migration foo -e --trucateTable=campo
        make migration foo -e --comment=campo
        
        Ex.

        php com make migration --dir=test --table=my_table --dropPrimary --unique=some_field,another_field

        For Foreign key construction:
        
        --fromField=
        --toField=
        --toTable=
        --constraint=
        --onDelete={cascade|restrict|setNull|noAction}
        --onUpdate={cascade|restrict|setNull|noAction}

        Ex:

        make migration foo --fromField=user_id --toField=id --toTable=users --onDelete=cascade --onUpdate=setNull

        # CSS Scan

        make css_scan --dir={path} [--relative=yes|no|1|0]

        Ex:

        make css_scan --dir="D:\www\woo2\wp-content\plugins\mutawp\assets\css\storefront"


        # Interfaces

        make interface SomeClass

        Ex:

        make interface OpenFactura --from=D:\laragon\www\simplerest\app\libs\OpenFacturaSDK.php

        # Mixed examples
        
        make lib my_lib
        make lib my_folder\my_lib
        make helper my_helper
        make interface pluggable 
        make interface pluggable --remove
        make any baz -s -m -a -f
        make any tbl_contacto -sam --from:some_conn_id
        make any all -sam  --from:some_conn_id
        make any all -samf --from:some_conn_id
        make any all -s -f --from:main 
        make any all -s -f --from:main --unignore  

        STR;

        dd(strtoupper(Strings::before(__METHOD__, 'Command::')) . ' HELP');
        dd($str);
    }


    function any($name, ...$opt){ 
        if (count($opt) == 0){
            StdOut::print("Nothing to do. Please specify action using options.\r\nUse 'make help' for help.\r\n");
            exit;
        }

        foreach ($opt as $o){            
            if (preg_match('/^--from[=|:]([a-z0-9A-ZñÑ_]+)$/', $o, $matches)){
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
        File manipulation

        Pasar --lowercase si se quiere que el nombre de archivo concista en solo minusculas
    */
    function generic($name, $prefix, $subfix, $dest_path, $template_path, $namespace = null, ...$opt) {        
        $name = str_replace('/', DIRECTORY_SEPARATOR, $name);

        if (Strings::endsWith($subfix, $name, false)){
            $name   = Strings::before($name, $subfix);
        }

        $unignore  = false;
        $remove    = false;
        $force     = false;
        $strict    = false;
        $lowercase = false;

        foreach ($opt as $o){ 
            if (preg_match('/^(--even-ignored|--unignore|-u|--retry|-r)$/', $o)){
                $unignore = true;
            }

            if (preg_match('/^(--strict)$/', $o)){
                $strict = true;
            }

            if (preg_match('/^(--lowercase)$/', $o)){
                $lowercase = true;
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

        $fname     = (!$lowercase ? $this->camel_case : strtolower($this->snake_case));  
    
        $filename  = $prefix . $fname . $subfix . '.php';

        $dest_path = $dest_path . $sub_path . $filename;

        $protected = $unignore ? false : $this->hasFileProtection($filename, $dest_path, $opt);
        $remove    = $this->forDeletion($filename, $dest_path, $opt);

        if ($remove){
            $ok = $this->write($dest_path, '', $protected, true);
            return;
        }
        
        $data = file_get_contents($template_path);
        $data = str_replace('__NAME__', $prefix . $this->camel_case .  $subfix , $data);

        if (!is_null($namespace)){
            $data = str_replace('__NAMESPACE', $namespace, $data);
        }

        if ($strict){
            $data = str_replace('<?php', '<?php declare(strict_types=1);' , $data);
        }

        $this->write($dest_path, $data, $protected);
    }

    function acl(...$opt){
        $debug = false;
        $force = false;

        foreach ($opt as $o){            
            if ($o == '--debug' || $o == '--dd' || $o == '-d'){
                $debug = true;
            }

            if ($o == '--force' || $o == '-f'){
                $force = true;
            }
        }

        if (!isset(Config::get()['acl_file'])){
            throw new \Exception("ACL filename not defined");
        }

        if (file_exists(Config::get()['acl_file'])){
            unlink(Config::get()['acl_file']);
        }

        if ($force){
            dd("Deleting previous roles");

            DB::table('roles')
            ->whereRaw("1=1")
            ->delete();
        }   

        try {
            $acl = include CONFIG_PATH . 'acl.php';

            if ($debug){
                dd((array) $acl, 'ACL generated');
            }

            dd("ACL file was generated. Path: ". SECURITY_PATH);
        } catch (\Exception $e){
            throw new \Exception("Acl generation fails. Detail: " . $e->getMessage());
        }
    }

    function page($name, ...$opt) {
        $namespace = $this->namespace . '\\controllers';
        $dest_path = PAGES_PATH;
        $template_path = self::TEMPLATES . ucfirst(__FUNCTION__) . '.php';
        $prefix = '';
        $subfix = '';  // 'Page';  

        $this->generic($name, $prefix, $subfix, $dest_path, $template_path, $namespace, ...$opt);
    }

    function controller($name, ...$opt) {
        $namespace = $this->namespace . '\\controllers';
        $dest_path = CONTROLLERS_PATH;
        $template_path = self::TEMPLATES . ucfirst(__FUNCTION__) . '.php';
        $prefix = '';
        $subfix = 'Controller';  

        $this->generic($name, $prefix, $subfix, $dest_path, $template_path, $namespace, ...$opt);
    }

    function console($name, ...$opt) {
        $namespace = $this->namespace . '\\controllers';
        $dest_path = CONTROLLERS_PATH;
        $template_path = self::TEMPLATES . ucfirst(__FUNCTION__) . '.php';
        $prefix = '';
        $subfix = 'Controller';  

        $this->generic($name, $prefix, $subfix, $dest_path, $template_path, $namespace, ...$opt);
    }

    function middleware($name, ...$opt) {
        $namespace = $this->namespace . '\\middlewares';
        $dest_path = MIDDLEWARES_PATH;
        $template_path = self::TEMPLATES . ucfirst(__FUNCTION__) . '.php';
        $prefix = '';
        $subfix = '';  

        $this->generic($name, $prefix, $subfix, $dest_path, $template_path, $namespace, ...$opt);
    }

    function cronjob($name, ...$opt) {
        $namespace = null; //
        $dest_path = CRONOS_PATH;
        $template_path = self::CRONJOBS_TEMPLATE;
        $prefix = '';
        $subfix = 'CronJob';  

        $this->generic($name, $prefix, $subfix, $dest_path, $template_path, $namespace, ...$opt);
    }

    function task($name, ...$opt) {
        $namespace = $this->namespace . '\\jobs\\tasks';
        $dest_path = TASKS_PATH;
        $template_path = self::TEMPLATES . ucfirst(__FUNCTION__) . '.php';
        $prefix = '';
        $subfix = 'Task';  

        $this->generic($name, $prefix, $subfix, $dest_path, $template_path, $namespace, ...$opt);
    }

    function lib($name, ...$opt) {
        $core = false;

        foreach ($opt as $o){ 
            if (preg_match('/^(--core|-c)$/', $o)){
                $core = true;
            }
        }

        if ($core){
            $namespace = $this->namespace . '\\core\\libs';
            $dest_path = CORE_LIBS_PATH;
        } else {
            $namespace = $this->namespace . '\\libs';
            $dest_path = LIBS_PATH;
        }

        $template_path = self::LIBS_TEMPLATE;
        $prefix = '';
        $subfix = '';  // Ej: 'Controller'

        $this->generic($name, $prefix, $subfix, $dest_path, $template_path, $namespace, ...$opt);
    }

    function trait($name, ...$opt) {
        $core = false;

        foreach ($opt as $o){ 
            if (preg_match('/^(--core|-c)$/', $o)){
                $core = true;
            }
        }

        if ($core){
            $namespace = $this->namespace . '\\core\\traits';
            $dest_path = CORE_TRAIT_PATH;
        } else {
            $namespace = $this->namespace . '\\traits';
            $dest_path = TRAIT_PATH;
        }

        $template_path = self::TRAIT_TEMPLATE;
        $subfix = 'Trait';  // Ej: 'Controller'

        $this->generic($name, '', $subfix, $dest_path, $template_path, $namespace, ...$opt);
    }

    function interface($name, ...$opt) {
        $core = false;
    
        // Detectar opción --core
        foreach ($opt as $o) {
            if (preg_match('/^(--core|-c)$/', $o)) {
                $core = true;
            }
        }
    
        // Configurar namespace y destino según --core
        if ($core) {
            $namespace = $this->namespace . '\\core\\interfaces';
            $dest_path = CORE_INTERFACE_PATH;
        } else {
            $namespace = $this->namespace . '\\interfaces';
            $dest_path = INTERFACE_PATH;
        }
    
        $template_path = self::INTERFACE_TEMPLATE;
        $prefix = 'I';
        $subfix = '';
    
        // Detectar parámetro --from=
        $fromFile = null;
        foreach ($opt as $o) {
            if (strpos($o, '--from=') === 0) {
                $fromFile = substr($o, 7); // Extraer el path después de '--from='
                break;
            }
        }
    
        // Generar código de métodos si se proporciona --from=
        $methodsCode = '';
        if ($fromFile !== null) {
            if (!file_exists($fromFile)) {
                StdOut::print("Error: El archivo '$fromFile' no existe.\r\n");
                return;
            }
    
            // Obtener el nombre de la clase del archivo
            $className = PHPLexicalAnalyzer::getClassNameByFileName($fromFile);
            if ($className === null) {
                StdOut::print("Error: No se encontró ninguna clase en el archivo '$fromFile'.\r\n");
                return;
            }
    
            // Usar Reflector para obtener información de la clase
            require_once CORE_PATH . 'libs' . DIRECTORY_SEPARATOR . 'Reflector.php';
            $classInfo = \simplerest\core\libs\Reflector::getClassInfo($className);
            $methods = $classInfo['methods'];
    
            // Generar firmas de métodos públicos no estáticos
            foreach ($methods as $method) {
                if ($method['visibility'] !== 'public' || $method['is_static']) {
                    continue; // Solo métodos públicos no estáticos
                }
    
                $params = [];
                foreach ($method['parameters'] as $param) {
                    $paramStr = '';
                    if ($param['type']) {
                        $paramStr .= $param['type'] . ' ';
                    }
                    $paramStr .= '$' . $param['name'];
                    if ($param['is_optional'] && $param['has_default']) {
                        $paramStr .= ' = ' . var_export($param['default_value'], true);
                    }
                    $params[] = $paramStr;
                }
    
                $returnType = $method['return_type'] ? ': ' . $method['return_type'] : '';
                $methodsCode .= "    public function {$method['name']}(" . implode(', ', $params) . ")$returnType;\r\n";
            }
        }
    
        // Configurar el nombre y el subpath
        $name = str_replace('/', DIRECTORY_SEPARATOR, $name);
        if (Strings::endsWith($subfix, $name, false)) {
            $name = Strings::before($name, $subfix);
        }
    
        $unignore = false;
        $remove = false;
        $force = false;
        $strict = false;
        $lowercase = false;
    
        foreach ($opt as $o) {
            if (preg_match('/^(--even-ignored|--unignore|-u|--retry|-r)$/', $o)) {
                $unignore = true;
            }
            if (preg_match('/^(--strict)$/', $o)) {
                $strict = true;
            }
            if (preg_match('/^(--lowercase)$/', $o)) {
                $lowercase = true;
            }
        }
    
        $sub_path = '';
        if (strpos($name, DIRECTORY_SEPARATOR) !== false) {
            $exp = explode(DIRECTORY_SEPARATOR, $name);
            $sub = implode(DIRECTORY_SEPARATOR, array_slice($exp, 0, count($exp) - 1));
            $sub_path = $sub . DIRECTORY_SEPARATOR;
            $name = $exp[count($exp) - 1];
            $namespace .= "\\$sub";
        }
    
        $this->setup($name);
        $fname = (!$lowercase ? $this->camel_case : strtolower($this->snake_case));
        $filename = $prefix . $fname . $subfix . '.php';
        $dest_path = $dest_path . $sub_path . $filename;
    
        $protected = $unignore ? false : $this->hasFileProtection($filename, $dest_path, $opt);
        $remove = $this->forDeletion($filename, $dest_path, $opt);
    
        if ($remove) {
            $this->write($dest_path, '', $protected, true);
            return;
        }
    
        // Cargar y personalizar la plantilla
        $data = file_get_contents($template_path);
        $data = str_replace('__NAME__', $prefix . $this->camel_case . $subfix, $data);
        $data = str_replace('__NAMESPACE__', $namespace, $data);
        $data = str_replace('// __METHODS__', $methodsCode, $data);
    
        if ($strict) {
            $data = str_replace('<?php', '<?php declare(strict_types=1);', $data);
        }
    
        $this->write($dest_path, $data, $protected);
    }

    function exception($name, ...$opt) {
        $core = false;

        foreach ($opt as $o){ 
            if (preg_match('/^(--core|-c)$/', $o)){
                $core = true;
            }
        }

        if ($core){
            $namespace = $this->namespace . '\\core\\exceptions';
            $dest_path = CORE_EXCEPTIONS_PATH;
        } else {
            $namespace = $this->namespace . '\\exceptions';
            $dest_path = EXCEPTIONS_PATH;
        }

        $template_path = self::EXCEPTION_TEMPLATE;
        $prefix = '';
        $subfix = 'Exception';  // Ej: 'Controller'

        $this->generic($name, $prefix, $subfix, $dest_path, $template_path, $namespace, ...$opt);
    }

    function helper($name, ...$opt) {
        $core = false;

        foreach ($opt as $o){ 
            if (preg_match('/^(--core|-c)$/', $o)){
                $core = true;
            }
        }

        if ($core){
            $namespace = $this->namespace . '\\core\\helpers';
            $dest_path = CORE_HELPERS_PATH;
        } else {
            $namespace = $this->namespace . '\\helpers';
            $dest_path = HELPERS_PATH;
        }

        $template_path = self::HELPER_TEMPLATE;
        $prefix = '';
        $subfix = '';  // Ej: 'Controller'

        $opt[] = "--lowercase";

        $this->generic($name, $prefix, $subfix, $dest_path, $template_path, $namespace, ...$opt);
    }

    function api($name, ...$opt) { 
        $unignore = false;

        foreach ($opt as $o){            
            if (preg_match('/^--from[=|:]([a-z0-9A-ZñÑ_]+)$/', $o, $matches)){
                $from_db = $matches[1];
                DB::getConnection($from_db);
            }

            if (preg_match('/^(--even-ignored|--unignore|-u)$/', $o)){
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
        $remove    = $this->forDeletion($filename, $dest_path, $opt);

        if ($remove){
            $ok = $this->write($dest_path, '', $protected, true);
            return;
        }

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
        $warn_file_existance = true;
        $warn_ignored_file   = true;

        foreach ($opt as $o){ 
            if (preg_match('/^(--remove|--delete|--erase)$/', $o)){
                $warn_file_existance = false;
                $warn_ignored_file   = false;
                break;
            }
        }    

        $dest_path = Files::normalize($dest_path);

        if ($warn_ignored_file && in_array($dest_path, $this->excluded_files)){
            StdOut::print("[ Skipping ] '$dest_path'. File '$filename' was ignored\r\n"); 
            return true; 
        } 
        
        if (file_exists($dest_path)){
            if ($warn_file_existance && !in_array('-f', $opt) && !in_array('--force', $opt)){
                StdOut::print("[ Skipping ] '$dest_path'. File '$filename' already exists. Use -f or --force if you want to override.\r\n");
                return true;
            }
            
            if (!is_writable($dest_path)){
                StdOut::print("[ Error ] '$dest_path'. File '$filename' is not writtable. Please check permissions.\r\n");
                return true;
            }
        }
    
        return false;
    }

    protected function forDeletion(string $filename, string $dest_path, Array $opt) : ?bool { 
        $remove = false;
    
        foreach ($opt as $o){ 
            if (preg_match('/^(--remove|--delete|--erase)$/', $o)){
                $remove = true;
                break;
            }
        }  
    
        if ($remove && !file_exists($dest_path)){
            StdOut::print("[ Error ] '$dest_path'. File '$filename' doesn't exists.\r\n");
            exit; //
        }

        return $remove;
    }

    protected function write(string $dest_path, string $file, bool $protected, bool $remove = false){
        if ($protected){
            return;
        }

        Files::mkDir(
            Files::getDir($dest_path)
        );

        Files::writableOrFail($dest_path);

        $dest_path = Files::normalize($dest_path);

        if ($remove){
            $ok = Files::delete($dest_path);    

            if (!$ok) {
                throw new \Exception("Delete of $dest_path has failed");
            } else {
                StdOut::print("$dest_path was deleted\r\n");
            }
        } else {
            $ok = (bool) file_put_contents($dest_path, $file);

            if (!$ok) {
                throw new \Exception("Writing of $dest_path has failed");
            } else {
                StdOut::print("$dest_path was generated\r\n");
            }     
        }

        return $ok;
    }

    function pivot_scan(...$opt)
    {     
        static $pivot_data = [];

        foreach ($opt as $o){            
            if (preg_match('/^--from[=|:]([a-z0-9A-ZñÑ_-]+)$/', $o, $matches)){
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

        if (!is_dir($dir)){
            Files::mkDirOrFail($dir);
        }

        foreach (new \DirectoryIterator($dir) as $fileInfo) {
            if ($fileInfo->isDot()  || $fileInfo->isDir()) continue;
            
            $filename = $fileInfo->getFilename();

            if (!Strings::endsWith('Schema.php', $filename)){
                continue;
            }

            $full_path = $dir . '/' . $filename;            
            $class_name = PHPLexicalAnalyzer::getClassNameByFileName($full_path);

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
            /*
                Si bien una tabla podria pivotearse a si misma si se auto-referencia,
                voy a excluir esa posibilidad.
            */
            if (in_array($pv, $tbs)){
                continue;
            }

            sort($tbs);

            $str_tbs = implode(',', $tbs);
            $_pivots[$str_tbs] = $pv; 
        }

        $path = str_replace('//', '/', $dir . '/' . $pivot_file);
        
        $pivot_data[$db_conn_id] = [
            'pivots'        => $_pivots,
            'pivot_fks'     => $pivot_fks,
            'relationships' => $relationships
        ];

        $this->write($path, '<?php '. PHP_EOL. PHP_EOL . 
          '$pivots        = ' .var_export($_pivots, true) . ';' . PHP_EOL . PHP_EOL .
          '$pivot_fks     = ' .var_export($pivot_fks, true) . ';' . PHP_EOL . PHP_EOL .
          '$relationships = ' . var_export($relationships, true) . ';' . PHP_EOL
        , false);

        #StdOut::print("Please run 'php com make rel_scan --from:$db_conn_id'");
    }

    function relation_scan(...$opt)
    {
        foreach ($opt as $o){            
            if (preg_match('/^--from[=|:]([a-z0-9A-ZñÑ_-]+)$/', $o, $matches)){
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
        $dir      = SCHEMA_PATH . $folder;
        $path     = str_replace('//', '/', $dir . '/' . $rel_file);
        
        $relation_type = [];
        $multiplicity  = [];
        $related       = [];

        $tables = Schema::getTables();
        
        foreach ($tables as $t){
            $rl = Schema::getAllRelations($t);
            $related_tbs = array_keys($rl);
            
            foreach ($related_tbs as $rtb){
                $relation_type["$t~$rtb"] = get_rel_type($t, $rtb, null, $from_db);
                $multiplicity["$t~$rtb"]  = is_mul_rel($t, $rtb, null, $from_db); 

                // New *
                if (!in_array($rtb, $related)){
                    $related[$t][] = $rtb;
                }
            }
        }

        /*
            Repito para tablas puente con las que no hay relación directa
            => no aparencen antes
        */

        $dir = get_schema_path(null, $from_db ?? null);
        include $dir . 'Pivots.php'; 

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
        $related_str       = var_export($related, true);

        $relation_type_str = Strings::tabulate($relation_type_str, 3, 0);
        $multiplicity_str  = Strings::tabulate($multiplicity_str, 3, 0);
        $related_str       = Strings::tabulate($related_str, 3, 0);


        $this->write($path, '<?php '. PHP_EOL. PHP_EOL .  
        Strings::tabulate("return [
        'related_tables' => $related_str,
        'relation_type'  => $relation_type_str,
        'multiplicity'   => $multiplicity_str,
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

        StdOut::print(
            shell_exec("php com make pivot_scan $params && php com make relation_scan $params")
        );
    }

    function schema($name, ...$opt) 
    {
        $unignore   = false;
        $remove     = null;
        $table      = null;
        $excluded = [];

        foreach ($opt as $o){            
            $o = str_replace(',', '|', $o);

            if (preg_match('/^--from[=|:]([a-z0-9A-ZñÑ_-]+)$/', $o, $matches)){
                $from_db = $matches[1];
                DB::getConnection($from_db);
            }

            if (preg_match('/^--(except|excluded)[=|:]([a-z0-9A-ZñÑ_-|]+)$/', $o, $matches)){
                $_except  = $matches[2];

                if ($_except == 'laravel_tables'){
                    $excluded = [
                        'migrations',
                        'failed_jobs',
                        'users',
                        'password_resets',
                        'personal_access_tokens'
                    ];
                } else {
                    $excluded = explode('|', $_except);
                }
            }

            if (preg_match('/^(--even-ignored|--unignore|-u|--retry|-r)$/', $o)){
                $unignore = true;
            }

            if (preg_match('/^(--remove|--erase|--delete)$/', $o)){
                $remove = true;
            }

            if (preg_match('/^--table[=|:]([a-z0-9A-ZñÑ_-]+)$/', $o, $matches)){
                $table = $matches[1];
            }
        }

        if (!isset($from_db)){
            $from_db = get_default_connection_id();
        }

        if (empty($table) && $name == 'all'){
            $tables = Schema::getTables();

            $tables = array_diff($tables, $excluded);

            foreach ($tables as $table){
                $this->schema($table, ...$opt);
            }

            $this->db_scan(...$opt);

            return;
        }

        $this->setup($name);

        if (!empty($table)){
            $name = $table;
        }

        if (!Schema::hasTable($name)){
            StdOut::print("Table '$name' not found. It's case sensitive\r\n");
            return;
        }

        if (!$this->all_uppercase){
            $filename = $this->camel_case.'Schema.php';
        } else {
            $filename = $this->table_name.'Schema.php';
        }        

        $file = file_get_contents(self::SCHEMA_TEMPLATE);
        $file = str_replace('__NAME__', $this->camel_case.'Schema', $file);
        
        // destination

        DB::getConnection();
        $current = DB::getCurrentConnectionId(true);

        if ($current == Config::get()['db_connection_default']){
            $file = str_replace('namespace simplerest\schemas', 'namespace simplerest\schemas' . "\\$current", $file);

            Files::mkDir(SCHEMA_PATH . $current);
            $dest_path = SCHEMA_PATH . "$current/". $filename;

        }  else {
            $group = DB::getTenantGroupName($current);

            if ($group){
                $current = $group;
                
                $file = str_replace('namespace simplerest\schemas', 'namespace simplerest\schemas' . "\\$current", $file);
                Files::mkDir(SCHEMA_PATH . $current);
                $dest_path = SCHEMA_PATH . "$current/". $filename;;
            } else {
                $dest_path = SCHEMA_PATH . $filename;
            }
        } 
        
        $protected = false;
        $remove    = $this->forDeletion($filename, $dest_path, $opt);

        if ($remove){
            $ok = $this->write($dest_path, $file, $protected, true);
            return;
        }

        $db = DB::database();  

        $_table = !empty($table) ? $table : $this->table_name;
        
        try {
            $fields = DB::select("SHOW COLUMNS FROM $db.{$_table}", [], 'ASSOC', $from_db);
        } catch (\Exception $e) {
            $trace = __METHOD__ . '() - line: ' . __LINE__;
            StdOut::print("[ SQL Error ] ". DB::getLog(). "\r\n");
            StdOut::print($e->getMessage().  "\r\n");
            StdOut::print("Trace: $trace");
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
                $id_name = $field['Field'];
                $pri_components[] = $field_name;
            } else if ($field['Key'] == 'UNI'){ 
                $uniques[] = $field_name;
            }                

            if ($field['Extra'] == 'auto_increment') { 
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
            } 
        }

        $nullables = array_unique($nullables);

        $escf = function($x){ 
            return "'$x'"; 
        };

        $_attr_types       = [];
        $_attr_type_detail = [];

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

            if (strtolower($types_raw[$f]) == 'json'){
                $_attr_type_detail[] = "\t\t\t\t'$f' => 'JSON'";
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

        $attr_types        = "[\r\n". implode(",\r\n", $_attr_types). "\r\n\t\t\t]";
        $attr_type_detail  = "[\r\n". implode(",\r\n", $_attr_type_detail). "\r\n\t\t\t]";
        $rules             = "[\r\n". implode(",\r\n", $_rules). "\r\n\t\t\t]";

        // Non-nullables
        $required = array_diff($field_names, $nullables);

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
        
        
        Strings::replace('__TABLE_NAME__', "'$_table'", $file);  
        Strings::replace('__ID__', !empty($id_name) ? "'$id_name'" : 'null', $file);   
        Strings::replace('__AUTOINCREMENT__', !empty($autoinc) ? "'$autoinc'" : 'null', $file);
        Strings::replace('__FIELDS__', '[' . implode(', ', Strings::enclose($field_names, "'")) . ']' , $file);    
        Strings::replace('__ATTR_TYPES__', $attr_types, $file);
        Strings::replace('__ATTR_TYPE_DETAIL__', $attr_type_detail, $file);
        Strings::replace('__PRIMARY__', '['. implode(', ',array_map($escf,  $pri_components)). ']',$file);
        Strings::replace('__NULLABLES__', '['. implode(', ',array_map($escf, $nullables)). ']',$file);        
        Strings::replace('__REQUIRED__', '[' . implode(', ', Strings::enclose($required, "'")) . ']',$file);
        Strings::replace('__UNIQUES__', '['. implode(', ',array_map($escf,  $uniques)). ']',$file);
        Strings::replace('__RULES__', $rules, $file);
        Strings::replace('__FKS__', '['. implode(', ',array_map($escf,  $fks)). ']',$file);
        Strings::replace('__RELATIONS__', $relations, $file);
        Strings::replace('__EXPANDED_RELATIONS__', $expanded_relations, $file);
        Strings::replace('__RELATIONS_FROM__', $relations_from, $file);
        Strings::replace('__EXPANDED_RELATIONS_FROM__', $expanded_relations_from, $file);
        
        $ok = $this->write($dest_path, $file, $protected);

    } // end function

    protected function getUuid(){
        $db = DB::database();      

        try {
            $fields = DB::select("SHOW COLUMNS FROM $db.{$this->snake_case}");
        } catch (\Exception $e) {
            StdOut::print('[ SQL Error ] '. DB::getLog(). "\r\n");
            StdOut::print($e->getMessage().  "\r\n");
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
        $unignore   = false;
        $no_check   = false;
        $schemaless = false;

        foreach ($opt as $o){            
            if (preg_match('/^--from[=|:]([a-z0-9A-ZñÑ_-]+)$/', $o, $matches)){
                $from_db = $matches[1];
                DB::getConnection($from_db);
            }

            if (preg_match('/^(--even-ignored|--unignore|-u|--retry|-r)$/', $o)){
                $unignore = true;
            }

            if (preg_match('/^--no-(check|verify)$/', $o)){
                $no_check = true;
            }

            if (preg_match('/^(--no-schema|-x)$/', $o)){
                $schemaless = true;
            }
        }

        if ($no_check === false){
            if ($name == 'all'){
                $tables = Schema::getTables();
                
                foreach ($tables as $table){
                    $this->model($table, ...$opt);
                }
    
                return;
            }
        }

        $this->setup($name);  

        $filename = $this->camel_case . 'Model'.'.php';

        $template = $schemaless ? self::MODEL_NO_SCHEMA_TEMPLATE : self::MODEL_TEMPLATE;
        $file     = file_get_contents($template);


        $file = str_replace('__NAME__', $this->camel_case.'Model', $file);       

        $imports = [];
        $traits  = [];
        $proterties = [];

        //
        // destination
        //

        DB::getConnection();
        $current = DB::getCurrentConnectionId(true);
        
        $folder = '';
        if ($current == Config::get()['db_connection_default']){
            $file = str_replace('namespace simplerest\models', 'namespace simplerest\models' . "\\$current", $file);

            Files::mkDir(MODELS_PATH . $current);
            $dest_path = MODELS_PATH . "$current/". $filename;

        }  else {
            $group = DB::getTenantGroupName($current);

            if ($group){
                $current = $group;
                
                $file = str_replace('namespace simplerest\models', 'namespace simplerest\models' . "\\$current", $file);
                Files::mkDir(MODELS_PATH . $current);
                $dest_path = MODELS_PATH . "$current/". $filename;
            } else {
                $dest_path = MODELS_PATH . $filename;
            }
        } 
        
        $protected = $unignore ? false : $this->hasFileProtection($filename, $dest_path, $opt);
        $remove    = $this->forDeletion($filename, $dest_path, $opt);

        if ($remove){
            $ok = $this->write($dest_path, '', $protected, true);
            return;
        }
               
        if (!empty($current)){
            $folder = "$current\\";
        }

        if (!$no_check || $schemaless){
            if (!$schemaless){
                $imports[] = "use simplerest\schemas\\$folder{$this->camel_case}Schema;";
            }
        
            Strings::replace('__SCHEMA_CLASS__', "{$this->camel_case}Schema", $file); 

            $uuid = $this->getUuid();
            if ($uuid){
                $imports[] = 'use simplerest\core\traits\Uuids;';
                $traits[] = 'use Uuids;';      
            }

            if ($schemaless){
                Strings::replace('__TABLE_NAME__', $this->table_name, $file);
            }
        } else {
            Strings::replace('parent::__construct($connect, __SCHEMA_CLASS__::class);', 'parent::__construct();', $file);
        }

        Strings::replace('### IMPORTS', implode("\r\n", $imports), $file); 
        Strings::replace('### TRAITS',  implode("\r\n\t", $traits), $file); 
        Strings::replace('### PROPERTIES', implode("\r\n\t", $proterties), $file); 

        $file = Strings::trimEmptyLinesAfter("{", $file, 0, null, 1);
        $file = Strings::trimEmptyLinesBefore("class ", $file, 0, null, 2);

        $this->write($dest_path, $file, $protected);
    }

    /*
        Debería estar en otro archivo!!! de hecho solo se deberían incluir y no estar todos los comandos acá !!!
    */
    function migration(...$opt) 
    {
        if (count($opt)>0 && !Strings::startsWith('-', $opt[0])){
            $name = $opt[0];
            unset($opt[0]);
        }

        $mode = 'create';

        foreach ($opt as $o){
            if (preg_match('/^--name[=|:]([a-z0-9A-ZñÑ_-]+)$/', $o, $matches)){
                $name = $matches[1];
            }

            if (preg_match('/^--create$/', $o) || preg_match('/^-c$/', $o)){
                $mode = "create";
            }

            if (preg_match('/^--(edit|modify)$/', $o) || preg_match('/^-e$/', $o)){
                $mode = "edit";
            }
        }

        if (isset($name) && $name !== false){
            $this->setup($name);
        }        

        $file = file_get_contents($mode == 'edit' ? self::MIGRATION_TEMPLATE : self::MIGRATION_TEMPLATE_CREATE);

        $eol = Strings::carriageReturn($file);

        $path    = MIGRATIONS_PATH;
        $to_db   = null;
        $tb_name = null;    
        $script  = null;
        $dir     = null;
        $up_rep  = '';
        $constructor = '';

        $dropColumn_ay = [];
        $renameColumn_ay = [];
        $renameTable  = null; 
        $nullable_ay  = [];
        $dropNullable_ay  = [];
        $primary_ay = [];
        $dropPrimary  = null;
        $auto  =  null;
        $dropAuto = null;
        $unsigned_ay  = [];
        $zeroFill_ay  = [];
        $binaryAttr_ay  = [];
        $dropAttr_ay  = [];
        $addUnique_ay  = [];
        $dropUnique_ay  = [];
        $addSpatial_ay = [];
        $dropSpatial_ay  = [];
        $dropForeign_ay  = [];
        $addIndex_ay  = [];
        $dropIndex_ay  = [];
        $truncate  = null;    

        foreach ($opt as $o)
        {
            if (is_array($o)){
                $o = $o[0];
            }

            if (preg_match('/^--(cat|show|display|print)$/', $o)){
                $cat = true;
            }

            if (preg_match('/^--(no-save|nosave|dont)$/', $o)){
                $dont = true;
            }

            if (preg_match('/^--to[=|:]([a-z0-9A-ZñÑ_-]+)$/', $o, $matches)){
                $to_db = $matches[1];
            }

            /*
                Makes a reference to the specified table schema
            */
            if (preg_match('/^--(table|tb)[=|:]([a-z0-9A-ZñÑ_-]+)$/', $o, $matches)){
                $tb_name = $matches[2];
            }
            
            /*  
                This option forces php class name
            */
            if (preg_match('/^--(class_name|class)[=|:]([a-z0-9A-ZñÑ_-]+)$/', $o, $matches)){
                $class_name = Strings::snakeToCamel($matches[2]);
                $file = str_replace('__NAME__', $class_name, $file); 
            } 

            if (Strings::startsWith('--dir=', $o) || Strings::startsWith('--dir:', $o) || Strings::startsWith('--folder=', $o) || Strings::startsWith('--folder:', $o) ){
                // Convert windows directory separator into *NIX
                $o = str_replace('\\', '/', $o);

                if (preg_match('~^--(dir|directory|folder)[=|:]([a-z0-9A-ZñÑ_\-/]+)$~', $o, $matches)){
                    $dir= $matches[2];
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

        if (empty($tb_name) && isset($name)){
            $tb_name = $name;
        }

        if (empty($tb_name) && isset($class_name)){
            $tb_name = Strings::camelToSnake($class_name);
        }
       
        foreach ($opt as $o)
        {
            /*
                Schema changes
            */
            if ($mode == 'edit')
            {
                $primary      = Strings::matchParam($o, ['pri', 'primary', 'addPrimary', 'addPri', 'setPri', 'setPrimary'], '.*');

                if (!empty($primary)){
                    $primary_ay[] = $primary;
                }

                $_dropPrimary  = Strings::matchParam($o, ['dropPrimary', 'delPrimary', 'removePrimary'], null);

                if (!empty($_dropPrimary)){
                    $dropPrimary = $_dropPrimary;
                }

                $_auto         = Strings::matchParam($o, ['auto', 'autoincrement', 'addAuto', 'addAutoincrement', 'setAuto']);
                
                if (!empty($_auto)){
                    $auto = $_auto;
                }
                
                $_dropAuto     = Strings::matchParam($o, ['dropAuto', 'DropAutoincrement', 'delAuto', 'delAutoincrement', 'removeAuto', 'notAuto', 'noAuto'], null);
                
                if (!empty($_dropAuto)){
                    $dropAuto = $_dropAuto;
                }

                $unsigned     = Strings::matchParam($o, 'unsigned');

                if (!empty($unsigned)){
                    $unsigned_ay[] = $unsigned;
                }

                $zeroFill     = Strings::matchParam($o, 'zeroFill');

                if (!empty($zeroFill)){
                    $zeroFill_ay[] = $zeroFill;
                }

                $binaryAttr   = Strings::matchParam($o, ['binaryAttr', 'binary']);

                if (!empty($binaryAttr)){
                    $binaryAttr_ay[] = $binaryAttr;
                }

                $dropAttr     = Strings::matchParam($o, ['dropAttributes', 'dropAttr', 'dropAttr', 'delAttr', 'removeAttr']);

                if (!empty($dropAttr)){
                    $dropAttr_ay[] = $dropAttr;
                }

                $dropColumn = Strings::matchParam($o, [
                    'dropColumn',
                    'removeColumn',
                    'delColumn'
                ], '.*');

                if (!empty($dropColumn)){
                    $dropColumn_ay[] =  $dropColumn;
                }

                $renameColumn = Strings::matchParam($o, 'renameColumn', '[a-z0-9A-ZñÑ_-]+\,[a-z0-9A-ZñÑ_-]+'); // from,to

                if (!empty($renameColumn)){
                    $renameColumn_ay[] = $renameColumn;
                }

                $_renameTable  = Strings::matchParam($o, 'renameTable');

                if (!empty($_renameTable)){
                    $renameTable = $_renameTable;
                }

                $nullable     = Strings::matchParam($o, ['nullable'], '.*');

                if (!empty($nullable)){
                    $nullable_ay[] = $nullable;
                }

                $dropNullable = Strings::matchParam($o, ['dropNullable', 'delNullable', 'removeNullable', 'notNullable', 'noNullable'], '.*');

                if (!empty($dropNullable)){
                    $dropNullable_ay[] = $dropNullable;
                }
                

                // va a devolver una lista
                $addUnique    = Strings::matchParam($o, ['addUnique', 'setUnique', 'unique'], '.*');

                if (!empty($addUnique)){
                    $addUnique_ay[] = $addUnique;
                }

                $dropUnique   = Strings::matchParam($o, ['dropUnique', 'removeUnique', 'delUnique']);

                if (!empty($dropUnique)){
                    $dropUnique_ay[] = $dropUnique;
                }

                // $addSpatial   = Strings::matchParam($o, 'addSpatial');

                // if (!empty($addSpatial)){
                //     $addSpatial_ay[] = $addSpatial;
                // }

                $dropSpatial  = Strings::matchParam($o, ['dropSpatial', 'delSpatial', 'removeSpatial']);

                if (!empty($dropSpatial)){
                    $dropSpatial_ay[] = $dropSpatial;
                }

                $dropForeign  = Strings::matchParam($o, ['dropForeign', 'dropFK', 'delFK', 'removeFK', 'dropFk', 'delFk', 'removeFk']);

                if (!empty($dropForeign)){
                    $dropForeign_ay[] = $dropForeign;
                }

                $addIndex     = Strings::matchParam($o, ['index', 'addIndex'], '.*');

                if (!empty($addIndex)){
                    $addIndex_ay[] = $addIndex;
                }

                $dropIndex    = Strings::matchParam($o, ['dropIndex', 'delIndex', 'removeIndex']);

                if (!empty($dropIndex)){
                    $dropIndex_ay[] = $dropIndex;
                }

                $_truncate     = Strings::matchParam($o, ['truncateTable', 'truncate', 'clearTable'], null);

                if (!empty($_truncate)){
                    $truncate = $_truncate;
                }

                /*
                    FKs 
                */

                if (preg_match('/^--(foreign|fk|fromField)[=|:]([a-z0-9A-ZñÑ_-]+)$/', $o, $matches)){
                    $fromField = $matches[2];
                }

                if (preg_match('/^--(references|reference|toField)[=|:]([a-z0-9A-ZñÑ_-]+)$/', $o, $matches)){
                    $toField = $matches[2];
                }

                if (preg_match('/^--(constraint)[=|:]([a-z0-9A-ZñÑ_]+)$/', $o, $matches)){
                    $constraint = $matches[2];
                }

                if (preg_match('/^--(on|onTable|toTable)[=|:]([a-z0-9A-ZñÑ_]+)$/', $o, $matches)){
                    $toTable = $matches[2];
                }

                if (preg_match('/^--(onDelete)[=|:]([a-z0-9A-ZñÑ_]+)$/', $o, $matches)){
                    $onDelete = $matches[2];
                }

                if (preg_match('/^--(onUpdate)[=|:]([a-z0-9A-ZñÑ_]+)$/', $o, $matches)){
                    $onUpdate = $matches[2];
                }

                $check_action = function (string $onRestriction){
                    $onRestriction = strtoupper($onRestriction);

                    switch ($onRestriction){
                        case 'NO ACTION':
                            break;
                        case 'SET NULL':
                            break;    
                        case 'SET DEFAULT':
                            break;
                        case 'RESTRICT':
                            break;
                        case 'CASCADE':
                            break;
                        case 'NOACTION':
                            $onRestriction = 'NO ACTION';
                            break;
                        case 'SETNULL':
                            $onRestriction = 'SET NULL';
                            break;
                        case 'SETDEFAULT':
                            $onRestriction = 'SET DEFAULT';
                            break;                    
                        default:
                            StdOut::print("\r\nInvalid action '$onRestriction' for ON UPDATE / ON DELETE");
                            exit;
                    }

                    return $onRestriction;
                };


                if (isset($onDelete)){
                    $onDelete = $check_action($onDelete);
                }
                
                if (isset($onUpdate)){
                    $onUpdate = $check_action($onUpdate);
                }
            }
        }
              

        $file = str_replace('__NAME__', $this->camel_case, $file); 
        $file = str_replace('__TB_NAME__', $tb_name, $file);

        if (!empty($dir)){
            $path .= "$dir/";
            Files::mkDir($path);
        }

        if ($mode == 'edit'){
            $up_rep = '$sc = new Schema($this->table);' . "\r\n\r\n";
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
            $constructor = "DB::setConnection('$to_db');";
        }

        /////////////////////////////////////////////////////

        if (!empty($renameTable)){
            $up_rep .= "\$sc->renameTableTo('$renameTable');\r\n";
        }

        if (!empty($truncate)){
            $up_rep .= "\$sc->truncateTable('$tb_name');\r\n";
        }

        foreach ($dropColumn_ay as $dc){
            $_fs = explode(',', $dc);

            foreach ($_fs as $f){
                $up_rep .= "\$sc->dropColumn('$f');\r\n";
            }
        }

        foreach ($renameColumn_ay as $rc){
            list($from, $to) = explode(',', $rc);
            $up_rep .= "\$sc->renameColumn('$from', '$to');\r\n";
        }

        foreach ($nullable_ay as $nl){
            $_fs = explode(',', $nl);

            foreach ($_fs as $f){
                $up_rep .= "\$sc->field('$f')->nullable();\r\n";
            }            
        }

        foreach ($dropNullable_ay as $nl){
            $_fs = explode(',', $nl);

            foreach ($_fs as $f){
                $up_rep .= "\$sc->field('$f')->dropNullable();\r\n";
            }
        }

        foreach ($primary_ay as $pr){
            $_pr = explode(',', $pr);

            foreach ($_pr as $f){
                $up_rep .= "\$sc->field('$f')->primary();\r\n";
            }
        }

        if (!empty($dropPrimary)){
            $up_rep .= "\$sc->dropPrimary();\r\n";
        }

        if (!empty($auto)){
            $up_rep .= "\$sc->field('$auto')->addAuto();\r\n";
        }

        if (!empty($dropAuto)){
            $up_rep .= "\$sc->dropAuto();\r\n";
        }

        foreach ($unsigned_ay as $ns){
            $up_rep .= "\$sc->field('$ns')->unsigned();\r\n";
        }

        foreach ($zeroFill_ay as $zf){
            $up_rep .= "\$sc->field('$zf')->zeroFill();\r\n";
        }

        foreach ($binaryAttr_ay as $bt){
            $up_rep .= "\$sc->field('$bt')->binaryAttr();\r\n";
        }

        foreach ($dropAttr_ay as $da){
            $up_rep .= "\$sc->field('$da')->dropAttr();\r\n";
        }

        foreach ($addUnique_ay as $uq){
            $uq_ay = explode(',', $uq);
            $uq_ay = Strings::enclose($uq_ay, "'");
            $uq    = implode(',', $uq_ay);

            $up_rep .= "\$sc->unique($uq);\r\n";
        }

        foreach ($dropUnique_ay as $uq){
            $up_rep .= "\$sc->dropUnique('$uq');\r\n";
        }

        foreach ($dropSpatial_ay as $sp){
            $up_rep .= "\$sc->dropSpatial('$sp');\r\n";
        }

        foreach ($dropIndex_ay as $index){
            $up_rep .= "\$sc->dropIndex('$index');\r\n";
        }

        foreach ($addIndex_ay as $index){
            $index_ay = explode(',', $index);
            $index_ay = Strings::enclose($index_ay, "'");
            $index    = implode(',', $index_ay);

            $up_rep .= "\$sc->addIndex($index);\r\n";
        }

        foreach ($dropForeign_ay as $fk_constraint){
            $up_rep .= "\$sc->dropFK('$fk_constraint');\r\n";
        }

        if (isset($fromField) && isset($toField) && isset($toTable)){
            $up_rep .= "\$sc->foreign('$fromField')->references('$toField')->on('$toTable')";
            
            if (isset($constraint)){
                $up_rep .= "->constraint('$constraint')";
            }

            if (isset($onDelete)){
                $up_rep .= "->onDelete('$onDelete')";
            }

            if (isset($onUpdate)){
                $up_rep .= "->onUpdate('$onUpdate')";
            }

            $up_rep .= ";\r\n";
        }       

        /////////////////////////////////////////////////////

        $up_rep       = rtrim($up_rep, "\r\n\r\n");
        
        $up_before    = $up_rep;
        $file_before  = $file; 

        $constructor = Strings::tabulate($constructor, 2, 0);
        if (!empty($constructor)){
            Strings::replace('### CONSTRUCTOR', $constructor, $file);
        }

        if (!empty(trim($up_rep))){
            $up_rep = Strings::tabulate($up_rep, 2);
            Strings::replace("### UP", "### UP\r\n". $up_rep, $file);
        }
       
        // destination
        $date = date("Y_m_d");
        $secs = time() - 1603750000;
        $filename = $date . '_'. $secs . '_' . $this->snake_case . '.php'; 

        $dest_path = $path . $filename;

        if (isset($cat)){
            $up_rep = Strings::tabulate($up_before, 1, 0);
            $_file  = str_replace('### UP', $up_rep, $file_before);
            StdOut::print(PHP_EOL . $_file);
        }

        if (!isset($dont)){
            $this->write($dest_path, $file, false);
        }
    }    

    function provider($name, ...$opt) {
        $this->setup($name);    

        $unignore = false;

        foreach ($opt as $o){ 
            if (preg_match('/^(--even-ignored|--unignore|-u|--retry|-r)$/', $o)){
                $unignore = true;
            }
        }

        $filename = $this->camel_case . 'ServiceProvider'.'.php';
        $dest_path = self::SERVICE_PROVIDERS_PATH . $filename;

        $protected = $unignore ? false : $this->hasFileProtection($filename, $dest_path, $opt);
        $remove    = $this->forDeletion($filename, $dest_path, $opt);

        if ($remove){
            $ok = $this->write($dest_path, '', $protected, true);
            return;
        }

        $file = file_get_contents(self::SERVICE_PROVIDER_TEMPLATE);
        $file = str_replace('__NAME__', $this->camel_case . 'ServiceProvider', $file);
        
        $this->write($dest_path, $file, $protected, $remove);
    }

    function system_constants(...$opt){
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

        $filename  = 'SystemConstants.php';
        $dest_path = CORE_LIBS_PATH . $filename;

        $protected = $this->hasFileProtection($filename, $dest_path, $opt);

        $data = file_get_contents(self::SYSTEM_CONST_TEMPLATE);
        $data = str_replace('# __CONSTANTS', $consts, $data);

        $this->write($dest_path, $data, $protected);
    }

    // for translations
    function trans(...$opt){
        $pot         = false;
        $po          = null;
        $mo          = false;

        $text_domain = null;
        $preset      = null;

        // dirs
        $from        = null;
        $to          = null;
      
        foreach ($opt as $o){ 
            if (preg_match('/^(--pot)$/', $o)){
                $pot = true;
            }

            if (preg_match('/^(--po)$/', $o)){
                $po = true;
            }

            if (preg_match('/^(--mo)$/', $o)){
                $mo = true;
            }

            if (Strings::startsWith('--preset=', $o) || Strings::startsWith('--preset:', $o)){
                // Convert windows directory separator into *NIX
                $o = str_replace('\\', '/', $o);

                if (preg_match('~^--(preset)[=|:]([a-z0-9A-ZñÑ_-]+)$~', $o, $matches)){
                    $preset= $matches[2];
                }
            }

            if (Strings::startsWith('--domain=', $o) || Strings::startsWith('--domain:', $o)){
                // Convert windows directory separator into *NIX
                $o = str_replace('\\', '/', $o);

                if (preg_match('~^--(domain)[=|:]([a-z0-9A-ZñÑ_-]+)$~', $o, $matches)){
                    $text_domain= $matches[2];
                }
            }

            if (Strings::startsWith('--from=', $o) || Strings::startsWith('--from:', $o)){
                // Convert windows directory separator into *NIX
                $o = str_replace('\\', '/', $o);

                if (preg_match('~^--(from)[=|:]([a-z0-9A-ZñÑ_\-/:]+)$~', $o, $matches)){
                    $from= $matches[2] . '/';
                }
            }

            if (Strings::startsWith('--to=', $o) || Strings::startsWith('--to:', $o)){
                // Convert windows directory separator into *NIX
                $o = str_replace('\\', '/', $o);

                if (preg_match('~^--(to)[=|:]([a-z0-9A-ZñÑ_\-/:]+)$~', $o, $matches)){
                    $to= $matches[2] . '/';
                }
            }
        }

        if ($pot){
            Translate::convertPot($from, $text_domain);
            exit;
        }

        if ($po === true){
            $include_po = ($mo === true);
        } else {
            $include_po = true;
        }        

        Translate::exportLangDef($include_po, $from, $to, $text_domain, $preset);   
    }

    /*
        Podría haber usado generic()

        Debería admitir rutas absolutas y no solo relativas a VIEW_PATH
    */
    function view($name, ...$opt) {
        $this->setup($name);

        $filename  = $this->snake_case . '.php';
        $dest_path = VIEWS_PATH . $filename;

        $filename  = str_replace('\\', '/', $filename);

        if (Strings::contains('/', $filename)){
            $dir = Strings::beforeLast($filename, '/');            
            Files::mkDirOrFail(VIEWS_PATH . $dir);
        }

        $protected = $this->hasFileProtection($filename, $dest_path, $opt);
        $remove    = $this->forDeletion($filename, $dest_path, $opt);
        
        if (!$remove){
            $data = <<<HTML
            <h3>Un título</h3>
            
            <span>Un contenido cualquiera</span>
            HTML;
        } else {
            $data = '';
        }    
    
        if (!$protected){
            $this->write($dest_path, $data, $protected, $remove);
        }
    }

    /*
        Escanea un PATH en busca de archivos .css y devuelvee algo como

        css_file('css/storefront/admin.css');
        css_file('css/storefront/admin_notices.css');
        css_file('css/storefront/dropzone.css');
        css_file('css/storefront/fontawesome-all.min.css');
        css_file('css/storefront/jquery-ui.structure.min.css');
        css_file('css/storefront/jquery-ui.theme.min.css');
        css_file('css/storefront/provider_admin.css');
        css_file('css/storefront/spectrum.css');
        css_file('css/storefront/unitecreator_browser.css');
        css_file('css/storefront/unitecreator_styles.css');
    */
    function css_scan(...$opt) {
        $is_relative = true;
        $dir         = null;

        foreach ($opt as $o){ 
            if (Strings::startsWith('--relative=', $o)){
                if (preg_match('~^--(relative)[=|:]([a-z0-9A-Z]+)$~', $o, $matches)){
                    $val = strtolower($matches[2]);
                    $is_relative = ($val != "0" && $val != "false"); 
                }
            }

            if (Strings::startsWith('--dir=', $o) || Strings::startsWith('--dir:', $o)){
                // Convert windows directory separator into *NIX
                $o = str_replace('\\', '/', $o);

                if (preg_match('~^--(dir)[=:]([a-z:0-9A-ZñÑ_\-/]+)$~', $o, $matches)){
                    $dir = $matches[2] . '/';
                }
            }
        }

        if (empty($dir)){
            StdOut::print("Please specify path with --dir=");
            exit;
        }

        if ($is_relative){
            $dir = Files::convertSlashes($dir);
        }
        
        $files = Files::glob($dir, '*.css');
        
        $lines = [];
        foreach ($files as $file){
            if ($is_relative){
                $file = Strings::afterIfContains($file, "assets" . DIRECTORY_SEPARATOR . $dir);
            }
            
            $lines[] = "css_file('$file');";     
        }

        echo implode(PHP_EOL, $lines);
    }

    /*
        TO-DO

        - Crear funcion general makeScafolding()
        - Usar makeScafolding() en widget(), shortcode(), package() y otras
    */
    function widget(string $name, ...$opt) {
        $dir = WIDGETS_PATH . $name;

        $name = Strings::toSnakeCase($name);

        $js     = false;
        $remove = false;
        foreach ($opt as $o){ 
            if (preg_match('/^(--remove|--delete|--erase)$/', $o)){
                $remove = true;
                break;
            }

            if (preg_match('/^(--js|--javascript|--include-js)$/', $o)){
                $js = true;
            }
        }

        if ($remove){
            $ok = Files::rmDirOrFail($dir, true);    

            if (!$ok) {
                throw new \Exception("Delete of $dir has failed");
            } else {
                StdOut::print("Directory `$dir` was deleted\r\n");
            }

            return;
        }

        if (!is_dir($dir)){
            if (Files::mkDirOrFail($dir)){
                dd("$dir was created");
            }
        }

        $file_path = "$dir" . DIRECTORY_SEPARATOR ."styles.css";
        $exists    = file_exists($file_path);

        if (Files::touch($file_path)){
            dd("$file_path was " . (!$exists ? 'created' : 'touched'));
        }

        if ($js){
            $file_path = "$dir" . DIRECTORY_SEPARATOR ."$name.js";
            $exists    = file_exists($file_path);

            if (Files::touch($file_path)){
                dd("$file_path was " . (!$exists ? 'created' : 'touched'));
            }
        }                
    }

    function command($name, ...$opt) {
        $template_path = self::COMMAND_TEMPLATE;
        $prefix = '';
        $subfix = 'Command';  // Ej: 'Controller'

        $this->generic($name, $prefix, $subfix, COMMANDS_PATH, $template_path, '', ...$opt);
    }

    protected function package(string $packageName, string $author, ?string $destination = null): void
    {
        // Normalizar nombres a kebab-case para los directorios
        $authorSlug = strtolower(str_replace(' ', '-', preg_replace('/[^a-zA-Z0-9]+/', '-', $author)));
        $packageSlug = strtolower(str_replace(' ', '-', preg_replace('/[^a-zA-Z0-9]+/', '-', $packageName)));
    
        if (empty($authorSlug) || empty($packageSlug)) {
            StdOut::error("Author and package name are required.");
            return;
        }
    
        $basePath = $destination ?: PACKAGES_PATH;
        $packagePath = $basePath . "{$authorSlug}/{$packageSlug}/";
    
        if (file_exists($packagePath)) {
            StdOut::warning("Package already exists at: $packagePath");
            return;
        }
    
        $directories = ['src', 'config', 'tests', 'resources'];
        foreach ($directories as $dir) {
            $path = $packagePath . $dir;
            if (!mkdir($path, 0755, true) && !is_dir($path)) {
                StdOut::error("Failed to create directory: $path");
                return;
            }
        }
    
        $composerJsonPath = $packagePath . 'composer.json';
        if (!file_exists($composerJsonPath)) {
            $composerContent = json_encode([
                "name" => "{$authorSlug}/{$packageSlug}",
                "description" => "Package {$packageName} by {$author}",
                "type" => "library",
                "require" => new stdClass()
            ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    
            file_put_contents($composerJsonPath, $composerContent);
            StdOut::info("composer.json created at: $composerJsonPath");
        }
    
        // Generar namespace basado en autor y nombre del package (PascalCase)
        $namespace = ucfirst(self::toPascalCase($authorSlug)) . "\\" . ucfirst(self::toPascalCase($packageSlug));
    
        $templateFiles = [
            self::SERVICE_PROVIDER_TEMPLATE => 'src/ServiceProvider.php',
            self::INTERFACE_TEMPLATE => 'src/ExampleInterface.php',
        ];
    
        foreach ($templateFiles as $template => $destinationPath) {
            $destinationFile = $packagePath . $destinationPath;
            if (file_exists($template)) {
                $content = file_get_contents($template);
                if ($content !== false) {
                    // Reemplazar placeholders __NAME__ y __NAMESPACE__
                    $updatedContent = str_replace(['__NAME__', '__NAMESPACE__'], [ucfirst($packageName), $namespace], $content);
                    file_put_contents($destinationFile, $updatedContent);
                    StdOut::info("Copied and updated template to: $destinationFile");
                } else {
                    StdOut::warning("Failed to read template: $template");
                }
            }
        }
    
        StdOut::success("Package created successfully at: $packagePath");
    }
    
    /**
     * Helper to convert kebab-case or snake_case to PascalCase
     */
    protected static function toPascalCase(string $value): string
    {
        return str_replace(' ', '', ucwords(str_replace(['-', '_'], ' ', $value)));
    }
    

    
} 