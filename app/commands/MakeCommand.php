<?php

namespace Boctulus\Simplerest\Commands;

use Boctulus\Simplerest\Core\Libs\DB;
use Boctulus\Simplerest\Core\Libs\Files;
use Boctulus\Simplerest\Core\Libs\Config;
use Boctulus\Simplerest\Core\Libs\Schema;
use Boctulus\Simplerest\Core\Libs\StdOut;
use Boctulus\Simplerest\Core\Libs\Factory;
use Boctulus\Simplerest\Core\Libs\Strings;
use Boctulus\Simplerest\Core\Interfaces\ICommand;
use Boctulus\Simplerest\Core\Libs\i18n\Translate;
use Boctulus\Simplerest\Core\Traits\CommandTrait;
use Boctulus\Simplerest\Core\Libs\PHPLexicalAnalyzer;

class MakeCommand implements ICommand
{
    const SERVICE_PROVIDERS_PATH = ROOT_PATH . 'packages' . DIRECTORY_SEPARATOR;

    const TEMPLATES = CORE_PATH . 'Templates' . DIRECTORY_SEPARATOR;

    const MODEL_TEMPLATE  = self::TEMPLATES . 'Model.php';
    const DTO_TEMPLATE  = self::TEMPLATES . 'DTO.php';
    const MODEL_NO_SCHEMA_TEMPLATE  = self::TEMPLATES . 'ModelWithoutSchema.php';
    const SCHEMA_TEMPLATE = self::TEMPLATES . 'Schema.php';
    const MIGRATION_TEMPLATE = self::TEMPLATES . 'Migration.php';
    const MIGRATION_TEMPLATE_CREATE = self::TEMPLATES . 'Migration_New.php';
    const API_TEMPLATE = self::TEMPLATES . 'ApiRestfulController.php';
    const SERVICE_PROVIDER_TEMPLATE = self::TEMPLATES . 'ServiceProvider.php';
    const SYSTEM_CONST_TEMPLATE = self::TEMPLATES . 'SystemConstants.php';
    const INTERFACE_TEMPLATE = self::TEMPLATES . 'Interface.php';
    const PACKAGE_CONFIG_TEMPLATE = self::TEMPLATES . 'PackageConfig.php';
    const HELPER_TEMPLATE = self::TEMPLATES . 'Helper.php';
    const LIBS_TEMPLATE = self::TEMPLATES . 'Lib.php';
    const TRAIT_TEMPLATE = self::TEMPLATES . 'Trait.php';
    const CRONJOBS_TEMPLATE = self::TEMPLATES . 'CronJob.php';
    const TASK_TEMPLATE = self::TEMPLATES . 'Task.php';
    const MIDDLEWARE_TEMPLATE = self::TEMPLATES . 'Middleware.php';
    const EXCEPTION_TEMPLATE = self::TEMPLATES . 'Exception.php';
    const COMMAND_TEMPLATE = self::TEMPLATES . 'Command.php';
    const TEST_TEMPLATE = self::TEMPLATES . 'TestCase.php';

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
        if (php_sapi_name() != 'cli') {
            Factory::response()->send("Error: Make can only be excecuted in console", 403);
        }

        $this->namespace = Config::get()['namespace'];

        if (file_exists(APP_PATH . '.make_ignore')) {
            $this->excluded_files = preg_split('/\R/', file_get_contents(APP_PATH . '.make_ignore'));

            foreach ($this->excluded_files as $ix => $f) {
                $f = trim($f);
                if (empty($f) || $f == "\r" || $f == "\n" || $f == "\r\n") {
                    unset($this->excluded_files[$ix]);
                    continue;
                }

                if (Strings::startsWith('#', $f) || Strings::startsWith(';', $f)) {
                    unset($this->excluded_files[$ix]);
                    continue;
                }

                $this->excluded_files[$ix] = str_replace(array('/', '\\'), DIRECTORY_SEPARATOR, $f);

                if (Strings::contains(DIRECTORY_SEPARATOR, $this->excluded_files[$ix])) {
                    $this->excluded_files[$ix] = APP_PATH . $this->excluded_files[$ix];
                }
            }
        }
    }

    /*
        Custom handle to support colons in method names (e.g., migrations:package)
    */
    public function handle($args) {
        if (empty($args)) {
            $this->help();
            return;
        }

        $method = array_shift($args);

        // Convert colons to underscores to support Laravel-style command naming
        $method = str_replace(':', '_', $method);

        if (!method_exists($this, $method)) {
            dd("Method not found for " . __CLASS__ . "::$method");
            exit;
        }

        // Call method dynamically with remaining arguments
        call_user_func_array([$this, $method], $args);
    }

    protected function setup(string $name)
    {
        $this->table_name    = $name; // nuevo: para cubrirme de DBs que no siguen convenciones
        $this->all_uppercase = Strings::isAllCaps($name);

        $name = str_replace('-', '_', $name);

        $name = ucfirst($name);
        $name_lo = strtolower($name);

        if (Strings::endsWith('model', $name_lo)) {
            $name = substr($name, 0, -5);
        } elseif (Strings::endsWith('controller', $name_lo)) {
            $name = substr($name, 0, -10);
        }

        $name_uc = ucfirst($name);

        if (strpos($name, '_') !== false) {
            $camel_case  = Strings::snakeToCamel($name);
            $snake_case = $name_lo;
        } elseif ($name == $name_lo) {
            $snake_case = $name;
            $camel_case  = ucfirst($name);
        } elseif ($name == $name_uc) {
            $camel_case  = $name;
        }

        if (!isset($snake_case)) {
            $snake_case = Strings::camelToSnake($camel_case);
        }

        $this->camel_case  = $camel_case;
        $this->snake_case  = $snake_case;
    }

    function help($name = null, ...$args)
    {
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
        make controller:package {package_name} {controller_name}  [--force | -f] [ --unignore | -u ] [ --strict ] [ --remove ]
        make controller:module {module_name} {controller_name}  [--force | -f] [ --unignore | -u ] [ --strict ] [ --remove ]

        make middleware:package {package_name} {middleware_name}  [--force | -f] [ --unignore | -u ] [ --strict ] [ --remove ]
        make middleware:module {module_name} {middleware_name}  [--force | -f] [ --unignore | -u ] [ --strict ] [ --remove ]

        make lib:package {package_name} {lib_name}  [--force | -f] [ --unignore | -u ] [ --strict ] [ --remove ]
        make lib:module {module_name} {lib_name}  [--force | -f] [ --unignore | -u ] [ --strict ] [ --remove ]

        make helper:package {package_name} {helper_name}  [--force | -f] [ --unignore | -u ] [ --strict ] [ --remove ]
        make helper:module {module_name} {helper_name}  [--force | -f] [ --unignore | -u ] [ --strict ] [ --remove ]

        make interface:package {package_name} {interface_name}  [--force | -f] [ --unignore | -u ] [ --strict ] [ --remove ]
        make interface:module {module_name} {interface_name}  [--force | -f] [ --unignore | -u ] [ --strict ] [ --remove ]

        make model:package {package_name} {model_name}  [--force | -f] [ --unignore | -u ] [ --strict ] [ --remove ]
        make model:module {module_name} {model_name}  [--force | -f] [ --unignore | -u ] [ --strict ] [ --remove ]

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

        make dto {name} [ --dir= ]

        make widget [ --include-js | --js ]

        make command myCommand
        make command:package zippy MyCommand

        make testcase MyFeature

        make module myModule [--force | -f] [ --remove ]
             
        make migration [ {name} ] [ --dir= | --file= ] [ --table= ] [ --class_name= ] [ --to= ] [ --create | --edit ] [ --strict ] [ --remove ]

        make migrations:package {package_name} {migration_name} [ --table= ] [ --class_name= ] [ --to= ] [ --create | --edit ] [ --strict ] [ --remove ]

        make migrations:module {module_name} {migration_name} [ --table= ] [ --class_name= ] [ --to= ] [ --create | --edit ] [ --strict ] [ --remove ]

        ⇨  See migrations section below

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


        # Acl file

        make acl [ -f ] [ --debug ]


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

        Anonymous migrations

        make migration --table=foobar

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

        # Package Migrations

        make migrations:package zippy categories --create
        make migrations:package zippy users --table=users --edit
        make migrations:package dummyapi products --table=products --class_name=ProductsAddDescription
        make migrations:package web-test orders --create --table=orders
        make migrations:package zippy categories --remove

        # Module Migrations

        make migrations:module FriendlyPOS products --create
        make migrations:module FriendlyPOS users --table=users --edit
        make migrations:module FriendlyPOS orders --table=orders --class_name=OrdersAddStatus
        make migrations:module FriendlyPOS products --remove
        
        ℹ  Use `php com migrations help` for specific help on migrations.

        For `fresh` and `refresh` refers also to "migrations help"


        # CSS Scan

        make css_scan --dir={path} [--relative=yes|no|1|0]

        Ex:

        make css_scan --dir="D:\www\woo2\wp-content\plugins\mutawp\assets\css\storefront"


        # Interfaces

        make interface SomeClass

        Ex:

        make interface OpenFactura --from=D:\laragon\www\Boctulus\Simplerest\app\libs\OpenFacturaSDK.php

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


    function any($name, ...$opt)
    {
        if (count($opt) == 0) {
            StdOut::print("Nothing to do. Please specify action using options.\r\nUse 'make help' for help.\r\n");
            exit;
        }

        foreach ($opt as $o) {
            if (preg_match('/^--from[=|:]([a-z0-9A-ZñÑ_]+)$/', $o, $matches)) {
                $from_db = $matches[1];
                DB::getConnection($from_db);
            }
        }

        if ($name == 'all') {
            $tables = Schema::getTables();

            foreach ($tables as $table) {
                $this->schema($table, ...$opt);
            }
        }

        $names = $name == 'all' ? $tables : [$name];

        switch ($opt[0]) {
            case '-sam':
                $opt = ['-s', '-a', '-m'];
                break;
            case '-samf':
                $opt = ['-s', '-a', '-m', '-f'];
                break;
        }

        foreach ($names as $name) {
            if (in_array('-s', $opt) || in_array('--schema', $opt)) {
                $this->schema($name, ...$opt);
            }
            if (in_array('-m', $opt) || in_array('--model', $opt)) {
                $this->model($name, ...$opt);
            }
            if (in_array('-a', $opt) || in_array('--api', $opt)) {
                $this->api($name, ...$opt);
            }
            if (in_array('-c', $opt) || in_array('--controller', $opt)) {
                $opt = array_intersect($opt, ['-f', '--force']);
                $this->controller($name, ...$opt);
            }
            if (in_array('--console', $opt)) {
                $opt = array_intersect($opt, ['-f', '--force']);
                $this->console($name, ...$opt);
            }
            if (in_array('-p', $opt) || in_array('--service', $opt) || in_array('--provider', $opt)) {
                $opt = array_intersect($opt, ['-f', '--force']);
                $this->provider($name, ...$opt);
            }
            if (in_array('-l', $opt) || in_array('--lib', $opt)) {
                $opt = array_intersect($opt, ['-f', '--force']);
                $this->lib($name, ...$opt);
            }
        }
    }

    /*
        File manipulation

        Considere el uso de makeScaffolding() combinado con copyAndParseTemplates() para casos complejos
        donde hay un scafolding y posiblemente archivos dentro.

        TO-DO

        - Pasar el case, ej: --lowercase si se quiere que el nombre de archivo concista en solo minusculas    

    */
    function renderTemplate($name, $prefix, $subfix, $dest_path, $template_path, $namespace = null, ...$opt)
    {
        $name = str_replace('/', DIRECTORY_SEPARATOR, $name);

        if (Strings::endsWith($subfix, $name, false)) {
            $name   = Strings::before($name, $subfix);
        }

        $unignore  = false;
        $remove    = false;
        $force     = false;
        $strict    = false;
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

        $fname     = (!$lowercase ? $this->camel_case : strtolower($this->snake_case));

        $filename  = $prefix . $fname . $subfix . '.php';

        $dest_path = $dest_path . $sub_path . $filename;

        $protected = $unignore ? false : $this->hasFileProtection($filename, $dest_path, $opt);
        $remove    = $this->forDeletion($filename, $dest_path, $opt);

        if ($remove) {
            $ok = $this->write($dest_path, '', $protected, true);
            return;
        }

        $data = file_get_contents($template_path);
        $data = str_replace('__NAME__', $prefix . $this->camel_case .  $subfix, $data);

        if (!is_null($namespace)) {
            $data = str_replace('__NAMESPACE__', $namespace, $data);
        }

        if ($strict) {
            $data = str_replace('<?php', '<?php declare(strict_types=1);', $data);
        }

        $this->write($dest_path, $data, $protected);
    }

    function acl(...$opt)
    {
        $debug = false;
        $force = false;

        foreach ($opt as $o) {
            if ($o == '--debug' || $o == '--dd' || $o == '-d') {
                $debug = true;
            }

            if ($o == '--force' || $o == '-f') {
                $force = true;
            }
        }

        if (!isset(Config::get()['acl_file'])) {
            throw new \Exception("ACL filename not defined");
        }

        if (file_exists(Config::get()['acl_file'])) {
            unlink(Config::get()['acl_file']);
        }

        if ($force) {
            dd("Deleting previous roles");

            DB::table('roles')
                ->whereRaw("1=1")
                ->delete();
        }

        try {
            $acl = include CONFIG_PATH . 'acl.php';

            if ($debug) {
                dd((array) $acl, 'ACL generated');
            }

            dd("ACL file was generated. Path: " . SECURITY_PATH);
        } catch (\Exception $e) {
            throw new \Exception("Acl generation fails. Detail: " . $e->getMessage());
        }
    }

    function dto($name, ...$opt)
    {
        $dir = null;
        foreach ($opt as $o) {
            if (Strings::startsWith('--dir=', $o) || Strings::startsWith('--dir:', $o) || Strings::startsWith('--folder=', $o) || Strings::startsWith('--folder:', $o)) {
                if (preg_match('/^--(dir|folder)[=|:]([a-z0-9A-ZñÑ_\.-\/\\\\]+)$/', $o, $matches)) {
                    $dir = $matches[2];
                }
            }
        }

        $namespace = $this->namespace . '\\DTO' . ($dir ? '\\' . str_replace('/', '\\', $dir) : '');
        $dest_path = DTO_PATH . Files::convertSlashes($dir, Files::WIN_DIR_SLASH) . DIRECTORY_SEPARATOR;
        $template_path = self::TEMPLATES . 'DTO.php';
        $prefix = '';
        $subfix = '';

        $this->renderTemplate($name, $prefix, $subfix, $dest_path, $template_path, $namespace, ...$opt);
    }

    function page($name, ...$opt)
    {
        $namespace = $this->namespace . '\\controllers';
        $dest_path = PAGES_PATH;
        $template_path = self::TEMPLATES . ucfirst(__FUNCTION__) . '.php';
        $prefix = '';
        $subfix = '';  // 'Page';  

        $this->renderTemplate($name, $prefix, $subfix, $dest_path, $template_path, $namespace, ...$opt);
    }

    function controller($name, ...$opt)
    {
        $namespace = $this->namespace . '\\controllers';
        $dest_path = CONTROLLERS_PATH;
        $template_path = self::TEMPLATES . ucfirst(__FUNCTION__) . '.php';
        $prefix = '';
        $subfix = 'Controller';

        $this->renderTemplate($name, $prefix, $subfix, $dest_path, $template_path, $namespace, ...$opt);
    }

    function console($name, ...$opt)
    {
        $namespace = $this->namespace . '\\controllers';
        $dest_path = CONTROLLERS_PATH;
        $template_path = self::TEMPLATES . ucfirst(__FUNCTION__) . '.php';
        $prefix = '';
        $subfix = 'Controller';

        $this->renderTemplate($name, $prefix, $subfix, $dest_path, $template_path, $namespace, ...$opt);
    }

    function middleware($name, ...$opt)
    {
        $namespace = $this->namespace . '\\Middlewares';
        $dest_path = MIDDLEWARES_PATH;
        $template_path = self::TEMPLATES . ucfirst(__FUNCTION__) . '.php';
        $prefix = '';
        $subfix = '';

        $this->renderTemplate($name, $prefix, $subfix, $dest_path, $template_path, $namespace, ...$opt);
    }

    function cronjob($name, ...$opt)
    {
        $namespace = null; //
        $dest_path = CRONOS_PATH;
        $template_path = self::CRONJOBS_TEMPLATE;
        $prefix = '';
        $subfix = 'CronJob';

        $this->renderTemplate($name, $prefix, $subfix, $dest_path, $template_path, $namespace, ...$opt);
    }

    function task($name, ...$opt)
    {
        $namespace = $this->namespace . '\\jobs\\tasks';
        $dest_path = TASKS_PATH;
        $template_path = self::TEMPLATES . ucfirst(__FUNCTION__) . '.php';
        $prefix = '';
        $subfix = 'Task';

        $this->renderTemplate($name, $prefix, $subfix, $dest_path, $template_path, $namespace, ...$opt);
    }

    function lib($name, ...$opt)
    {
        $core = false;

        foreach ($opt as $o) {
            if (preg_match('/^(--core|-c)$/', $o)) {
                $core = true;
            }
        }

        if ($core) {
            $namespace = $this->namespace . '\\Core\\Libs';
            $dest_path = CORE_LIBS_PATH;
        } else {
            $namespace = $this->namespace . '\\Libs';
            $dest_path = LIBS_PATH;
        }

        $template_path = self::LIBS_TEMPLATE;
        $prefix = '';
        $subfix = '';  // Ej: 'Controller'

        $this->renderTemplate($name, $prefix, $subfix, $dest_path, $template_path, $namespace, ...$opt);
    }

    function trait($name, ...$opt)
    {
        $core = false;

        foreach ($opt as $o) {
            if (preg_match('/^(--core|-c)$/', $o)) {
                $core = true;
            }
        }

        if ($core) {
            $namespace = $this->namespace . '\\Core\\Traits';
            $dest_path = CORE_TRAIT_PATH;
        } else {
            $namespace = $this->namespace . '\\Traits';
            $dest_path = TRAIT_PATH;
        }

        $template_path = self::TRAIT_TEMPLATE;
        $subfix = 'Trait';  // Ej: 'Controller'

        $this->renderTemplate($name, '', $subfix, $dest_path, $template_path, $namespace, ...$opt);
    }

    function interface($name, ...$opt)
    {
        $core  = false;

        // Detectar opción --core
        foreach ($opt as $o) {
            if (preg_match('/^(--core|-c)$/', $o)) {
                $core = true;
            }
        }

        // Configurar namespace y destino según --core
        if ($core) {
            $namespace = $this->namespace . '\\Core\\Interfaces';
            $dest_path = CORE_INTERFACE_PATH;
        } else {
            $namespace = $this->namespace . '\\Interfaces';
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
            $classInfo = \Boctulus\Simplerest\Core\Libs\Reflector::getClassInfo($className);
            $methods = $classInfo['methods'];

            // Generar firmas de métodos públicos
            foreach ($methods as $method) {
                if ($method['visibility'] !== 'public' /* || $method['is_static'] */) {
                    continue;
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
        $data = str_replace('// namespace __NAMESPACE__;', "namespace $namespace;", $data);
        $data = str_replace('// __METHODS__', $methodsCode, $data);

        if ($strict) {
            $data = str_replace('<?php', '<?php declare(strict_types=1);', $data);
        }

        $this->write($dest_path, $data, $protected);
    }

    function exception($name, ...$opt)
    {
        $core = false;

        foreach ($opt as $o) {
            if (preg_match('/^(--core|-c)$/', $o)) {
                $core = true;
            }
        }

        if ($core) {
            $namespace = $this->namespace . '\\Core\\exceptions';
            $dest_path = CORE_EXCEPTIONS_PATH;
        } else {
            $namespace = $this->namespace . '\\exceptions';
            $dest_path = EXCEPTIONS_PATH;
        }

        $template_path = self::EXCEPTION_TEMPLATE;
        $prefix = '';
        $subfix = 'Exception';  // Ej: 'Controller'

        $this->renderTemplate($name, $prefix, $subfix, $dest_path, $template_path, $namespace, ...$opt);
    }

    function helper($name, ...$opt)
    {
        $core = false;

        foreach ($opt as $o) {
            if (preg_match('/^(--core|-c)$/', $o)) {
                $core = true;
            }
        }

        if ($core) {
            $namespace = $this->namespace . '\\Core\\helpers';
            $dest_path = CORE_HELPERS_PATH;
        } else {
            $namespace = $this->namespace . '\\helpers';
            $dest_path = HELPERS_PATH;
        }

        $template_path = self::HELPER_TEMPLATE;
        $prefix = '';
        $subfix = '';  // Ej: 'Controller'

        $opt[] = "--lowercase";

        $this->renderTemplate($name, $prefix, $subfix, $dest_path, $template_path, $namespace, ...$opt);
    }

    function api($name, ...$opt)
    {
        $unignore = false;

        foreach ($opt as $o) {
            if (preg_match('/^--from[=|:]([a-z0-9A-ZñÑ_]+)$/', $o, $matches)) {
                $from_db = $matches[1];
                DB::getConnection($from_db);
            }

            if (preg_match('/^(--even-ignored|--unignore|-u)$/', $o)) {
                $unignore = true;
            }
        }

        if ($name == 'all') {
            $tables = Schema::getTables();

            foreach ($tables as $table) {
                $this->api($table, ...$opt);
            }

            return;
        }

        $this->setup($name);

        $filename  = $this->camel_case . '.php';
        $dest_path = API_PATH . $filename;

        $protected = $unignore ? false : $this->hasFileProtection($filename, $dest_path, $opt);
        $remove    = $this->forDeletion($filename, $dest_path, $opt);

        if ($remove) {
            $ok = $this->write($dest_path, '', $protected, true);
            return;
        }

        $data = file_get_contents(self::API_TEMPLATE);
        $data = str_replace('__NAME__', $this->camel_case, $data);
        $data = str_replace('__SOFT_DELETE__', 'true', $data); // debe depender del schema

        $this->write($dest_path, $data, $protected);
    }

    protected function get_pdo_const(string $sql_type)
    {
        if (
            Strings::startsWith('int', $sql_type) ||
            Strings::startsWith('tinyint', $sql_type) ||
            Strings::startsWith('smallint', $sql_type) ||
            Strings::startsWith('mediumint', $sql_type) ||
            Strings::startsWith('bigint', $sql_type) ||
            Strings::startsWith('serial', $sql_type)
        ) {
            return 'INT';
        }

        if (
            Strings::startsWith('bit', $sql_type) ||
            Strings::startsWith('bool', $sql_type)
        ) {
            return 'BOOL';
        }

        // el resto (default)
        return 'STR';
    }

    /*
        Return if file is protected and not should be overwrited
    */
    protected function hasFileProtection(string $filename, string $dest_path, array $opt): bool
    {
        $warn_file_existance = true;
        $warn_ignored_file   = true;

        foreach ($opt as $o) {
            if (preg_match('/^(--remove|--delete|--erase)$/', $o)) {
                $warn_file_existance = false;
                $warn_ignored_file   = false;
                break;
            }
        }

        $dest_path = Files::normalize($dest_path);

        if ($warn_ignored_file && in_array($dest_path, $this->excluded_files)) {
            StdOut::print("[ Skipping ] '$dest_path'. File '$filename' was ignored\r\n");
            return true;
        }

        if (file_exists($dest_path)) {
            if ($warn_file_existance && !in_array('-f', $opt) && !in_array('--force', $opt)) {
                StdOut::print("[ Skipping ] '$dest_path'. File '$filename' already exists. Use -f or --force if you want to override.\r\n");
                return true;
            }

            if (!is_writable($dest_path)) {
                StdOut::print("[ Error ] '$dest_path'. File '$filename' is not writtable. Please check permissions.\r\n");
                return true;
            }
        }

        return false;
    }

    protected function forDeletion(string $filename, string $dest_path, array $opt): ?bool
    {
        $remove = false;

        foreach ($opt as $o) {
            if (preg_match('/^(--remove|--delete|--erase)$/', $o)) {
                $remove = true;
                break;
            }
        }

        if ($remove && !file_exists($dest_path)) {
            StdOut::print("[ Error ] '$dest_path'. File '$filename' doesn't exists.\r\n");
            exit; //
        }

        return $remove;
    }

    protected function write(string $dest_path, string $file, bool $protected, bool $remove = false)
    {
        if ($protected) {
            return;
        }

        Files::mkDir(
            Files::getDir($dest_path)
        );

        Files::writableOrFail($dest_path);

        $dest_path = Files::normalize($dest_path);

        if ($remove) {
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

        foreach ($opt as $o) {
            if (preg_match('/^--from[=|:]([a-z0-9A-ZñÑ_-]+)$/', $o, $matches)) {
                $from_db = $matches[1];
                DB::getConnection($from_db);
            }
        }

        $folder = '';

        if (!isset($from_db) && DB::getCurrentConnectionId() == null) {
            $folder = DB::getDefaultConnectionId();
            $db_conn_id = $folder;
        } else {
            $db_conn_id = DB::getCurrentConnectionId();
            if ($db_conn_id == DB::getDefaultConnectionId()) {
                $folder = $db_conn_id;
            } else {
                $group = DB::getTenantGroupName($db_conn_id);

                if ($group) {
                    $folder = $group;
                }
            }
        }

        if (!empty($pivot_data[$db_conn_id])) {
            return $pivot_data[$db_conn_id];
        }

        $pivot_file = 'Pivots.php';
        $dir = SCHEMA_PATH . $folder;

        $pivots = [];
        $relationships = [];
        $pivot_fks = [];

        if (!is_dir($dir)) {
            Files::mkDirOrFail($dir);
        }

        foreach (new \DirectoryIterator($dir) as $fileInfo) {
            if ($fileInfo->isDot()  || $fileInfo->isDir()) continue;

            $filename = $fileInfo->getFilename();

            if (!Strings::endsWith('Schema.php', $filename)) {
                continue;
            }

            $full_path = $dir . '/' . $filename;
            $class_name = PHPLexicalAnalyzer::getClassNameByFileName($full_path);

            if (!class_exists($class_name)) {
                throw new \Exception("Class '$class_name' doesn't exist in $filename. Full path: $full_path");
            }

            $schema  = $class_name::get();

            if (!isset($schema['relationships_from'])) {
                throw new \Exception("Undefined 'relationships_from' for $filename. Full path $full_path");
            }

            $rels = $schema['relationships_from'];

            // Debe haber 2 FK(s)
            if (count($rels) != 2) {
                continue;
            }

            $relationships[$schema['table_name']] = $rels;

            /*
                Asumo que solo existe una tabla puente entre ciertas tablas
            */
            foreach ($rels as $tb => $r) {
                $pivots[$schema['table_name']][] = $tb;
            }

            /*
                Construyo $pivot_fks  
            */

            foreach ($pivots as $pv => $tbs) {
                $rels = $relationships[$pv];
                $tbs  = array_keys($rels);

                if (count($rels[$tbs[0]]) == 1) {
                    $fk1  = substr($rels[$tbs[0]][0][1], strlen($pv) + 1);
                } else {
                    $fk1 = [];
                    foreach ($rels[$tbs[0]] as $r) {
                        $_f = explode('.', $r[1]);
                        $fk1[] = $_f[1];
                    }
                }

                if (count($rels[$tbs[1]]) == 1) {
                    $fk2  = substr($rels[$tbs[1]][0][1], strlen($pv) + 1);
                } else {
                    $fk2 = [];
                    foreach ($rels[$tbs[1]] as $r) {
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
        foreach ($pivots as $pv => $tbs) {
            /*
                Si bien una tabla podria pivotearse a si misma si se auto-referencia,
                voy a excluir esa posibilidad.
            */
            if (in_array($pv, $tbs)) {
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

        $this->write(
            $path,
            '<?php ' . PHP_EOL . PHP_EOL .
                '$pivots        = ' . var_export($_pivots, true) . ';' . PHP_EOL . PHP_EOL .
                '$pivot_fks     = ' . var_export($pivot_fks, true) . ';' . PHP_EOL . PHP_EOL .
                '$relationships = ' . var_export($relationships, true) . ';' . PHP_EOL,
            false
        );

        #StdOut::print("Please run 'php com make rel_scan --from:$db_conn_id'");
    }

    function relation_scan(...$opt)
    {
        foreach ($opt as $o) {
            if (preg_match('/^--from[=|:]([a-z0-9A-ZñÑ_-]+)$/', $o, $matches)) {
                $from_db = $matches[1];
                DB::getConnection($from_db);
            }
        }

        $folder = '';

        if (!isset($from_db) && DB::getCurrentConnectionId() == null) {
            $folder = $from_db = DB::getDefaultConnectionId();
        } else {
            $db_conn_id = DB::getCurrentConnectionId();
            $from_db    = $db_conn_id;

            if ($db_conn_id == DB::getDefaultConnectionId()) {
                $folder  = $db_conn_id;
            } else {
                $group = DB::getTenantGroupName($db_conn_id);

                if ($group) {
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

        foreach ($tables as $t) {
            $rl = Schema::getAllRelations($t);
            $related_tbs = array_keys($rl);

            foreach ($related_tbs as $rtb) {
                $relation_type["$t~$rtb"] = get_rel_type($t, $rtb, null, $from_db);
                $multiplicity["$t~$rtb"]  = is_mul_rel($t, $rtb, null, $from_db);

                // New *
                if (!in_array($rtb, $related)) {
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
        foreach ($pivot_pairs as $pvp) {
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


        $this->write($path, '<?php ' . PHP_EOL . PHP_EOL .
            Strings::tabulate("return [
        'related_tables' => $related_str,
        'relation_type'  => $relation_type_str,
        'multiplicity'   => $multiplicity_str,
        ];", 0, 0, -8), false);
    }

    // alias
    function rel_scan(...$opt)
    {
        $this->relation_scan(...$opt);
    }

    /*
        Solución parche
    */
    function db_scan(...$opt)
    {
        $params = implode(' ', $opt);

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

        foreach ($opt as $o) {
            $o = str_replace(',', '|', $o);

            if (preg_match('/^--from[=|:]([a-z0-9A-ZñÑ_-]+)$/', $o, $matches)) {
                $from_db = $matches[1];
                DB::getConnection($from_db);
            }

            if (preg_match('/^--(except|excluded)[=|:]([a-z0-9A-ZñÑ_-|]+)$/', $o, $matches)) {
                $_except  = $matches[2];

                if ($_except == 'laravel_tables') {
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

            if (preg_match('/^(--even-ignored|--unignore|-u|--retry|-r)$/', $o)) {
                $unignore = true;
            }

            if (preg_match('/^(--remove|--erase|--delete)$/', $o)) {
                $remove = true;
            }

            if (preg_match('/^--table[=|:]([a-z0-9A-ZñÑ_-]+)$/', $o, $matches)) {
                $table = $matches[1];
            }
        }

        if (!isset($from_db)) {
            $from_db = get_default_connection_id();
        }

        if (empty($table) && $name == 'all') {
            $tables = Schema::getTables();

            $tables = array_diff($tables, $excluded);

            foreach ($tables as $table) {
                $this->schema($table, ...$opt);
            }

            $this->db_scan(...$opt);

            return;
        }

        $this->setup($name);

        if (!empty($table)) {
            $name = $table;
        }

        if (!Schema::hasTable($name)) {
            StdOut::print("Table '$name' not found. It's case sensitive\r\n");
            return;
        }

        if (!$this->all_uppercase) {
            $filename = $this->camel_case . 'Schema.php';
        } else {
            $filename = $this->table_name . 'Schema.php';
        }

        $file = file_get_contents(self::SCHEMA_TEMPLATE);
        $file = str_replace('__NAME__', $this->camel_case . 'Schema', $file);

        // destination

        DB::getConnection();
        $current = DB::getCurrentConnectionId(true);

        if ($current == Config::get()['db_connection_default']) {
            $file = str_replace('namespace Boctulus\Simplerest\Schemas', 'namespace Boctulus\Simplerest\Schemas' . "\\$current", $file);

            Files::mkDir(SCHEMA_PATH . $current);
            $dest_path = SCHEMA_PATH . "$current/" . $filename;
        } else {
            $group = DB::getTenantGroupName($current);

            if ($group) {
                $current = $group;

                $file = str_replace('namespace Boctulus\Simplerest\Schemas', 'namespace Boctulus\Simplerest\Schemas' . "\\$current", $file);
                Files::mkDir(SCHEMA_PATH . $current);
                $dest_path = SCHEMA_PATH . "$current/" . $filename;;
            } else {
                $dest_path = SCHEMA_PATH . $filename;
            }
        }

        $protected = false;
        $remove    = $this->forDeletion($filename, $dest_path, $opt);

        if ($remove) {
            $ok = $this->write($dest_path, $file, $protected, true);
            return;
        }

        $db = DB::database();

        $_table = !empty($table) ? $table : $this->table_name;

        try {
            $fields = DB::select("SHOW COLUMNS FROM $db.{$_table}", [], 'ASSOC', $from_db);
        } catch (\Exception $e) {
            $trace = __METHOD__ . '() - line: ' . __LINE__;
            StdOut::print("[ SQL Error ] " . DB::getLog() . "\r\n");
            StdOut::print($e->getMessage() .  "\r\n");
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

        foreach ($fields as $ix => $field) {
            //dd($field, $ix);

            $field_name    = $field['Field'];
            $type          = $field['Type'];

            $field_names[] = $field_name;

            $comment = Schema::getColumnComment($name, $field_name)['COLUMN_COMMENT'];

            if ($comment == 'email' || $comment == 'e-mail') {
                $emails[] = $field_name;
            }

            if ($field['Null']  == 'YES' || $field['Default'] !== NULL) {
                $nullables[] = $field_name;
            }

            #dd($field, "FIELD $field_name"); //

            if ($field['Key'] == 'PRI') {
                $id_name = $field['Field'];
                $pri_components[] = $field_name;
            } else if ($field['Key'] == 'UNI') {
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

            if ($type == 'double') {
                $double[] = $field_name;
            }

            if (Strings::startsWith('decimal(', $type)) {
                $nums = substr($type, strlen('decimal('), -1);
                $_rules_[$field_name]['type'] = "decimal($nums)";
            }


            $types[$field['Field']] = $this->get_pdo_const($field['Type']);
            $types_raw[$field['Field']] = $field['Type'];

            if (!$autoinc && $field['Key'] == 'PRI') {
                $field_name_lo = strtolower($field['Field']);
                if ($field_name_lo == 'uuid' || $field_name_lo == 'guid') {
                    if ($types[$field['Field']] != 'STR') {
                        printf("Warning: {$field['Field']} has not a valid type for UUID ***\r\n");
                    }

                    $uuid = $field['Field']; /// *
                    $id_name = $uuid;   /// *
                }
            }
        }

        if (count($pri_components) > 1) {
            // busco si hay un AUTOINC
            if (!empty($autoinc)) {
                $id_name = $autoinc;
            }
        }

        $nullables = array_unique($nullables);

        $escf = function ($x) {
            return "'$x'";
        };

        $_attr_types       = [];
        $_attr_type_detail = [];

        foreach ($types as $f => $type) {
            $_attr_types[] = "\t\t\t\t'$f' => '$type'";

            $_rules[$f] = [];

            if (isset($_rules_[$f])) {
                $_rules[$f] = $_rules_[$f];
            }

            $type = strtolower($type);

            if (!isset($_rules[$f]['type'])) {
                $_rules[$f]['type'] = $type;
            }

            // emails
            if (in_array($f, $emails)) {
                $_rules[$f]['type'] = 'email';
            }

            // duble
            if (in_array($f, $double)) {
                $_rules[$f]['type'] = 'double';
            }

            // varchars
            if (preg_match('/^(varchar)\(([0-9]+)\)$/', $types_raw[$f], $matches)) {
                $len = $matches[2];
                $_rules[$f]['max'] = $len;
            }

            /*
              https://www.php.net/manual/en/language.types.type-juggling.php
            */

            // varbinary
            if (preg_match('/^(|varbinary)\(([0-9]+)\)$/', $types_raw[$f], $matches)) {
                $len = $matches[2];
                $_rules[$f] = ['max' => $len];
            }

            // binary
            if (preg_match('/^(binary)\(([0-9]+)\)$/', $types_raw[$f], $matches)) {
                $len = $matches[2];
                $_rules[$f]['max'] = $len;
            }

            // unsigned
            if (in_array($f, $unsigned)) {
                $_rules[$f]['min'] = 0;
            }

            // bool
            if (in_array($f, $tinyint)) {
                $_rules[$f]['type'] = 'bool';
            }

            // timestamp
            if (strtolower($types_raw[$f]) == 'timestamp') {
                $_rules[$f]['type'] = 'timestamp';
            }

            // datetime
            if (strtolower($types_raw[$f]) == 'datetime') {
                $_rules[$f]['type'] = 'datetime';
            }

            // date
            if (strtolower($types_raw[$f]) == 'date') {
                $_rules[$f]['type'] =  'date';
            }

            // time
            if (strtolower($types_raw[$f]) == 'time') {
                $_rules[$f]['type'] =  'time';
            }

            if (strtolower($types_raw[$f]) == 'json') {
                $_attr_type_detail[] = "\t\t\t\t'$f' => 'JSON'";
            }

            /*
                Para blobs

                https://www.virendrachandak.com/techtalk/how-to-get-size-of-blob-in-mysql/
            */

            if (!in_array($f, $nullables)) {
                $_rules[$f]['required'] = 'true';
            }

            $tmp = [];
            foreach ($_rules[$f] as $k => $v) {
                $vv = ($k == 'max' || $k == 'min' || $k == 'required') ?  $v : "'$v'";
                $tmp[] = "'$k'" . ' => ' . $vv;
            }

            $_rules[$f] = "\t\t\t\t'$f' => " . '[' . implode(', ', $tmp) . ']';
        }

        $attr_types        = "[\r\n" . implode(",\r\n", $_attr_types) . "\r\n\t\t\t]";
        $attr_type_detail  = "[\r\n" . implode(",\r\n", $_attr_type_detail) . "\r\n\t\t\t]";
        $rules             = "[\r\n" . implode(",\r\n", $_rules) . "\r\n\t\t\t]";

        // Non-nullables
        $required = array_diff($field_names, $nullables);

        /*
            Relationships
        */

        $relations = '';
        $rels = Schema::getAllRelations($name, true);

        $g = [];
        $c = 0;
        foreach ($rels as $tb => $rs) {
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
        foreach ($rels as $tb => $rs) {
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
        Strings::replace('__FIELDS__', '[' . implode(', ', Strings::enclose($field_names, "'")) . ']', $file);
        Strings::replace('__ATTR_TYPES__', $attr_types, $file);
        Strings::replace('__ATTR_TYPE_DETAIL__', $attr_type_detail, $file);
        Strings::replace('__PRIMARY__', '[' . implode(', ', array_map($escf,  $pri_components)) . ']', $file);
        Strings::replace('__NULLABLES__', '[' . implode(', ', array_map($escf, $nullables)) . ']', $file);
        Strings::replace('__REQUIRED__', '[' . implode(', ', Strings::enclose($required, "'")) . ']', $file);
        Strings::replace('__UNIQUES__', '[' . implode(', ', array_map($escf,  $uniques)) . ']', $file);
        Strings::replace('__RULES__', $rules, $file);
        Strings::replace('__FKS__', '[' . implode(', ', array_map($escf,  $fks)) . ']', $file);
        Strings::replace('__RELATIONS__', $relations, $file);
        Strings::replace('__EXPANDED_RELATIONS__', $expanded_relations, $file);
        Strings::replace('__RELATIONS_FROM__', $relations_from, $file);
        Strings::replace('__EXPANDED_RELATIONS_FROM__', $expanded_relations_from, $file);

        $ok = $this->write($dest_path, $file, $protected);
    } // end function

    protected function getUuid()
    {
        $db = DB::database();

        try {
            $fields = DB::select("SHOW COLUMNS FROM $db.{$this->snake_case}");
        } catch (\Exception $e) {
            StdOut::print('[ SQL Error ] ' . DB::getLog() . "\r\n");
            StdOut::print($e->getMessage() .  "\r\n");
            throw $e;
        }

        $id_name =  NULL;
        $uuid = false;

        foreach ($fields as $field) {
            if ($field['Key'] == 'PRI') {
                $field_name_lo = strtolower($field['Field']);
                if ($field_name_lo == 'uuid' || $field_name_lo == 'guid') {
                    if ($this->get_pdo_const($field['Type']) == 'STR') {
                        return $field['Field'];
                    }
                }
            }
        }

        return false;
    }

    function model($name, ...$opt)
    {
        $unignore   = false;
        $no_check   = false;
        $schemaless = false;

        foreach ($opt as $o) {
            if (preg_match('/^--from[=|:]([a-z0-9A-ZñÑ_-]+)$/', $o, $matches)) {
                $from_db = $matches[1];
                DB::getConnection($from_db);
            }

            if (preg_match('/^(--even-ignored|--unignore|-u|--retry|-r)$/', $o)) {
                $unignore = true;
            }

            if (preg_match('/^--no-(check|verify)$/', $o)) {
                $no_check = true;
            }

            if (preg_match('/^(--no-schema|-x)$/', $o)) {
                $schemaless = true;
            }
        }

        if ($no_check === false) {
            if ($name == 'all') {
                $tables = Schema::getTables();

                foreach ($tables as $table) {
                    $this->model($table, ...$opt);
                }

                return;
            }
        }

        $this->setup($name);

        $filename = $this->camel_case . 'Model' . '.php';

        $template = $schemaless ? self::MODEL_NO_SCHEMA_TEMPLATE : self::MODEL_TEMPLATE;
        $file     = file_get_contents($template);


        $file = str_replace('__NAME__', $this->camel_case . 'Model', $file);

        $imports = [];
        $traits  = [];
        $proterties = [];

        //
        // destination
        //

        DB::getConnection();
        $current = DB::getCurrentConnectionId(true);

        $folder = '';
        if ($current == Config::get()['db_connection_default']) {
            $file = str_replace('namespace Boctulus\Simplerest\Models', 'namespace Boctulus\Simplerest\Models' . "\\$current", $file);

            Files::mkDir(MODELS_PATH . $current);
            $dest_path = MODELS_PATH . "$current/" . $filename;
        } else {
            $group = DB::getTenantGroupName($current);

            if ($group) {
                $current = $group;

                $file = str_replace('namespace Boctulus\Simplerest\Models', 'namespace Boctulus\Simplerest\Models' . "\\$current", $file);
                Files::mkDir(MODELS_PATH . $current);
                $dest_path = MODELS_PATH . "$current/" . $filename;
            } else {
                $dest_path = MODELS_PATH . $filename;
            }
        }

        $protected = $unignore ? false : $this->hasFileProtection($filename, $dest_path, $opt);
        $remove    = $this->forDeletion($filename, $dest_path, $opt);

        if ($remove) {
            $ok = $this->write($dest_path, '', $protected, true);
            return;
        }

        if (!empty($current)) {
            $folder = "$current\\";
        }

        if (!$no_check || $schemaless) {
            if (!$schemaless) {
                $imports[] = "use Boctulus\Simplerest\Schemas\\$folder{$this->camel_case}Schema;";
            }

            Strings::replace('__SCHEMA_CLASS__', "{$this->camel_case}Schema", $file);

            $uuid = $this->getUuid();
            if ($uuid) {
                $imports[] = 'use Boctulus\Simplerest\Core\Traits\Uuids;';
                $traits[] = 'use Uuids;';
            }

            if ($schemaless) {
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
        if (count($opt) > 0 && !Strings::startsWith('-', $opt[0])) {
            $name = $opt[0];
            unset($opt[0]);
        }

        $mode = 'create';

        foreach ($opt as $o) {
            if (preg_match('/^--name[=|:]([a-z0-9A-ZñÑ_-]+)$/', $o, $matches)) {
                $name = $matches[1];
            }

            if (preg_match('/^--create$/', $o) || preg_match('/^-c$/', $o)) {
                $mode = "create";
            }

            if (preg_match('/^--(edit|modify)$/', $o) || preg_match('/^-e$/', $o)) {
                $mode = "edit";
            }
        }

        if (isset($name) && $name !== false) {
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

        foreach ($opt as $o) {
            if (is_array($o)) {
                $o = $o[0];
            }

            if (preg_match('/^--(cat|show|display|print)$/', $o)) {
                $cat = true;
            }

            if (preg_match('/^--(no-save|nosave|dont)$/', $o)) {
                $dont = true;
            }

            if (preg_match('/^--to[=|:]([a-z0-9A-ZñÑ_-]+)$/', $o, $matches)) {
                $to_db = $matches[1];
            }

            /*
                Makes a reference to the specified table schema
            */
            if (preg_match('/^--(table|tb)[=|:]([a-z0-9A-ZñÑ_-]+)$/', $o, $matches)) {
                $tb_name = $matches[2];
            }

            /*  
                This option forces php class name
            */
            if (preg_match('/^--(class_name|class)[=|:]([a-z0-9A-ZñÑ_-]+)$/', $o, $matches)) {
                $class_name = Strings::snakeToCamel($matches[2]);
                $file = str_replace('__NAME__', $class_name, $file);
            }

            if (Strings::startsWith('--dir=', $o) || Strings::startsWith('--dir:', $o) || Strings::startsWith('--folder=', $o) || Strings::startsWith('--folder:', $o)) {
                if (preg_match('/^--(dir|folder)[=|:]([a-z0-9A-ZñÑ_\.-\/\\\\]+)$/', $o, $matches)) {
                    $dir = $matches[2];
                }
            }

            /*
                The only condition to work is the script should be enclosed with double mark quotes ("")
                and it should not contain any double mark inside
            */
            if (preg_match('/^--from_script[=|:]"([^"]+)"/', $o, $matches)) {
                $script = $matches[1];
            }
        }


        if (!isset($name)) {
            if (isset($class_name)) {
                $this->setup($class_name);
            } else {
                if (!is_null($tb_name)) {
                    $this->setup($tb_name);;
                }
            }
        }

        if (is_null($this->camel_case)) {
            throw new \InvalidArgumentException("No name for migration class");
        }

        if (empty($tb_name) && isset($name)) {
            $tb_name = $name;
        }

        if (empty($tb_name) && isset($class_name)) {
            $tb_name = Strings::camelToSnake($class_name);
        }

        foreach ($opt as $o) {
            /*
                Schema changes
            */
            if ($mode == 'edit') {
                $primary      = Strings::matchParam($o, ['pri', 'primary', 'addPrimary', 'addPri', 'setPri', 'setPrimary'], '.*');

                if (!empty($primary)) {
                    $primary_ay[] = $primary;
                }

                $_dropPrimary  = Strings::matchParam($o, ['dropPrimary', 'delPrimary', 'removePrimary'], null);

                if (!empty($_dropPrimary)) {
                    $dropPrimary = $_dropPrimary;
                }

                $_auto         = Strings::matchParam($o, ['auto', 'autoincrement', 'addAuto', 'addAutoincrement', 'setAuto']);

                if (!empty($_auto)) {
                    $auto = $_auto;
                }

                $_dropAuto     = Strings::matchParam($o, ['dropAuto', 'DropAutoincrement', 'delAuto', 'delAutoincrement', 'removeAuto', 'notAuto', 'noAuto'], null);

                if (!empty($_dropAuto)) {
                    $dropAuto = $_dropAuto;
                }

                $unsigned     = Strings::matchParam($o, 'unsigned');

                if (!empty($unsigned)) {
                    $unsigned_ay[] = $unsigned;
                }

                $zeroFill     = Strings::matchParam($o, 'zeroFill');

                if (!empty($zeroFill)) {
                    $zeroFill_ay[] = $zeroFill;
                }

                $binaryAttr   = Strings::matchParam($o, ['binaryAttr', 'binary']);

                if (!empty($binaryAttr)) {
                    $binaryAttr_ay[] = $binaryAttr;
                }

                $dropAttr     = Strings::matchParam($o, ['dropAttributes', 'dropAttr', 'dropAttr', 'delAttr', 'removeAttr']);

                if (!empty($dropAttr)) {
                    $dropAttr_ay[] = $dropAttr;
                }

                $dropColumn = Strings::matchParam($o, [
                    'dropColumn',
                    'removeColumn',
                    'delColumn'
                ], '.*');

                if (!empty($dropColumn)) {
                    $dropColumn_ay[] =  $dropColumn;
                }

                $renameColumn = Strings::matchParam($o, 'renameColumn', '[a-z0-9A-ZñÑ_-]+\,[a-z0-9A-ZñÑ_-]+'); // from,to

                if (!empty($renameColumn)) {
                    $renameColumn_ay[] = $renameColumn;
                }

                $_renameTable  = Strings::matchParam($o, 'renameTable');

                if (!empty($_renameTable)) {
                    $renameTable = $_renameTable;
                }

                $nullable     = Strings::matchParam($o, ['nullable'], '.*');

                if (!empty($nullable)) {
                    $nullable_ay[] = $nullable;
                }

                $dropNullable = Strings::matchParam($o, ['dropNullable', 'delNullable', 'removeNullable', 'notNullable', 'noNullable'], '.*');

                if (!empty($dropNullable)) {
                    $dropNullable_ay[] = $dropNullable;
                }


                // va a devolver una lista
                $addUnique    = Strings::matchParam($o, ['addUnique', 'setUnique', 'unique'], '.*');

                if (!empty($addUnique)) {
                    $addUnique_ay[] = $addUnique;
                }

                $dropUnique   = Strings::matchParam($o, ['dropUnique', 'removeUnique', 'delUnique']);

                if (!empty($dropUnique)) {
                    $dropUnique_ay[] = $dropUnique;
                }

                // $addSpatial   = Strings::matchParam($o, 'addSpatial');

                // if (!empty($addSpatial)){
                //     $addSpatial_ay[] = $addSpatial;
                // }

                $dropSpatial  = Strings::matchParam($o, ['dropSpatial', 'delSpatial', 'removeSpatial']);

                if (!empty($dropSpatial)) {
                    $dropSpatial_ay[] = $dropSpatial;
                }

                $dropForeign  = Strings::matchParam($o, ['dropForeign', 'dropFK', 'delFK', 'removeFK', 'dropFk', 'delFk', 'removeFk']);

                if (!empty($dropForeign)) {
                    $dropForeign_ay[] = $dropForeign;
                }

                $addIndex     = Strings::matchParam($o, ['index', 'addIndex'], '.*');

                if (!empty($addIndex)) {
                    $addIndex_ay[] = $addIndex;
                }

                $dropIndex    = Strings::matchParam($o, ['dropIndex', 'delIndex', 'removeIndex']);

                if (!empty($dropIndex)) {
                    $dropIndex_ay[] = $dropIndex;
                }

                $_truncate     = Strings::matchParam($o, ['truncateTable', 'truncate', 'clearTable'], null);

                if (!empty($_truncate)) {
                    $truncate = $_truncate;
                }

                /*
                    FKs 
                */

                if (preg_match('/^--(foreign|fk|fromField)[=|:]([a-z0-9A-ZñÑ_-]+)$/', $o, $matches)) {
                    $fromField = $matches[2];
                }

                if (preg_match('/^--(references|reference|toField)[=|:]([a-z0-9A-ZñÑ_-]+)$/', $o, $matches)) {
                    $toField = $matches[2];
                }

                if (preg_match('/^--(constraint)[=|:]([a-z0-9A-ZñÑ_]+)$/', $o, $matches)) {
                    $constraint = $matches[2];
                }

                if (preg_match('/^--(on|onTable|toTable)[=|:]([a-z0-9A-ZñÑ_]+)$/', $o, $matches)) {
                    $toTable = $matches[2];
                }

                if (preg_match('/^--(onDelete)[=|:]([a-z0-9A-ZñÑ_]+)$/', $o, $matches)) {
                    $onDelete = $matches[2];
                }

                if (preg_match('/^--(onUpdate)[=|:]([a-z0-9A-ZñÑ_]+)$/', $o, $matches)) {
                    $onUpdate = $matches[2];
                }

                $check_action = function (string $onRestriction) {
                    $onRestriction = strtoupper($onRestriction);

                    switch ($onRestriction) {
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


                if (isset($onDelete)) {
                    $onDelete = $check_action($onDelete);
                }

                if (isset($onUpdate)) {
                    $onUpdate = $check_action($onUpdate);
                }
            }
        }

        // Si no se especifica class_name, usar clase anónima
        if (empty($class_name) && empty($name)) {
            $file = str_replace('class __NAME__ implements IMigration', 'return new class implements IMigration', $file);
            $file = trim($file) . ';';
        }

        $file = str_replace('__NAME__', $this->camel_case, $file);
        $file = str_replace('__TB_NAME__', $tb_name, $file);

        if (!empty($dir)) {
            $path .= "$dir/";
            Files::mkDir($path);
        }

        if ($mode == 'edit') {
            $up_rep = '$sc = new Schema($this->table);' . "\r\n\r\n";
        }

        if (!empty($script)) {
            if (!Strings::contains('"', $script)) {
                $up_rep .= "Model::query(\"$script\");";
            } else {
                $up_rep .= "Model::query(\"
                <<<'SQL_QUERY'
                $script
                SQL_QUERY;
                \");";
            }
        }

        if (!empty($to_db)) {
            $constructor = "DB::setConnection('$to_db');";
        }

        /////////////////////////////////////////////////////

        if (!empty($renameTable)) {
            $up_rep .= "\$sc->renameTableTo('$renameTable');\r\n";
        }

        if (!empty($truncate)) {
            $up_rep .= "\$sc->truncateTable('$tb_name');\r\n";
        }

        foreach ($dropColumn_ay as $dc) {
            $_fs = explode(',', $dc);

            foreach ($_fs as $f) {
                $up_rep .= "\$sc->dropColumn('$f');\r\n";
            }
        }

        foreach ($renameColumn_ay as $rc) {
            list($from, $to) = explode(',', $rc);
            $up_rep .= "\$sc->renameColumn('$from', '$to');\r\n";
        }

        foreach ($nullable_ay as $nl) {
            $_fs = explode(',', $nl);

            foreach ($_fs as $f) {
                $up_rep .= "\$sc->field('$f')->nullable();\r\n";
            }
        }

        foreach ($dropNullable_ay as $nl) {
            $_fs = explode(',', $nl);

            foreach ($_fs as $f) {
                $up_rep .= "\$sc->field('$f')->dropNullable();\r\n";
            }
        }

        foreach ($primary_ay as $pr) {
            $_pr = explode(',', $pr);

            foreach ($_pr as $f) {
                $up_rep .= "\$sc->field('$f')->primary();\r\n";
            }
        }

        if (!empty($dropPrimary)) {
            $up_rep .= "\$sc->dropPrimary();\r\n";
        }

        if (!empty($auto)) {
            $up_rep .= "\$sc->field('$auto')->addAuto();\r\n";
        }

        if (!empty($dropAuto)) {
            $up_rep .= "\$sc->dropAuto();\r\n";
        }

        foreach ($unsigned_ay as $ns) {
            $up_rep .= "\$sc->field('$ns')->unsigned();\r\n";
        }

        foreach ($zeroFill_ay as $zf) {
            $up_rep .= "\$sc->field('$zf')->zeroFill();\r\n";
        }

        foreach ($binaryAttr_ay as $bt) {
            $up_rep .= "\$sc->field('$bt')->binaryAttr();\r\n";
        }

        foreach ($dropAttr_ay as $da) {
            $up_rep .= "\$sc->field('$da')->dropAttr();\r\n";
        }

        foreach ($addUnique_ay as $uq) {
            $uq_ay = explode(',', $uq);
            $uq_ay = Strings::enclose($uq_ay, "'");
            $uq    = implode(',', $uq_ay);

            $up_rep .= "\$sc->unique($uq);\r\n";
        }

        foreach ($dropUnique_ay as $uq) {
            $up_rep .= "\$sc->dropUnique('$uq');\r\n";
        }

        foreach ($dropSpatial_ay as $sp) {
            $up_rep .= "\$sc->dropSpatial('$sp');\r\n";
        }

        foreach ($dropIndex_ay as $index) {
            $up_rep .= "\$sc->dropIndex('$index');\r\n";
        }

        foreach ($addIndex_ay as $index) {
            $index_ay = explode(',', $index);
            $index_ay = Strings::enclose($index_ay, "'");
            $index    = implode(',', $index_ay);

            $up_rep .= "\$sc->addIndex($index);\r\n";
        }

        foreach ($dropForeign_ay as $fk_constraint) {
            $up_rep .= "\$sc->dropFK('$fk_constraint');\r\n";
        }

        if (isset($fromField) && isset($toField) && isset($toTable)) {
            $up_rep .= "\$sc->foreign('$fromField')->references('$toField')->on('$toTable')";

            if (isset($constraint)) {
                $up_rep .= "->constraint('$constraint')";
            }

            if (isset($onDelete)) {
                $up_rep .= "->onDelete('$onDelete')";
            }

            if (isset($onUpdate)) {
                $up_rep .= "->onUpdate('$onUpdate')";
            }

            $up_rep .= ";\r\n";
        }

        /////////////////////////////////////////////////////

        $up_rep       = rtrim($up_rep, "\r\n\r\n");

        $up_before    = $up_rep;
        $file_before  = $file;

        $constructor = Strings::tabulate($constructor, 2, 0);
        if (!empty($constructor)) {
            Strings::replace('### CONSTRUCTOR', $constructor, $file);
        }

        if (!empty(trim($up_rep))) {
            $up_rep = Strings::tabulate($up_rep, 2);
            Strings::replace("### UP", "### UP\r\n" . $up_rep, $file);
        }

        // destination
        $date = date("Y_m_d");
        $secs = time() - 1603750000;
        $filename = $date . '_' . $secs . '_' . $this->snake_case . '.php';

        $dest_path = $path . $filename;

        if (isset($cat)) {
            $up_rep = Strings::tabulate($up_before, 1, 0);
            $_file  = str_replace('### UP', $up_rep, $file_before);
            StdOut::print(PHP_EOL . $_file);
        }

        if (!isset($dont)) {
            $this->write($dest_path, $file, false);
        }
    }

    /*
        Helper method to create migrations in packages or modules

        @param string $context_type 'package' or 'module'
        @param string $context_name Name of the package or module
        @param string $base_path Base path where to find the context
        @param string $migrations_relative_path Relative path from MIGRATIONS_PATH to the migrations folder
        @param string $migration_name Name of the migration
        @param array $opt Command options
    */
    private function createMigrationInContext(
        string $context_type,
        string $context_name,
        string $base_path,
        string $migrations_relative_path,
        string $migration_name,
        array $opt
    ) {
        $migrations_path = $base_path . DIRECTORY_SEPARATOR . "database" . DIRECTORY_SEPARATOR . "migrations";

        // Check if context exists
        if (!file_exists($base_path)) {
            StdOut::print("\nError: " . ucfirst($context_type) . " '$context_name' does not exist at: $base_path\n\n");
            return false;
        }

        // Check for --remove flag
        $remove = false;
        foreach ($opt as $o) {
            if (preg_match('/^(--remove|--delete|--erase)$/', $o)) {
                $remove = true;
                break;
            }
        }

        // Handle removal
        if ($remove) {
            if (!file_exists($migrations_path)) {
                StdOut::print("\nNo migrations directory found at: $migrations_path\n\n");
                return false;
            }

            // Find migration files matching the name
            $migration_files = Files::glob($migrations_path, '*_' . $migration_name . '.php');

            if (empty($migration_files)) {
                StdOut::print("\nNo migration files found matching '$migration_name' in $context_type '$context_name'\n\n");
                return false;
            }

            // Delete each matching file
            foreach ($migration_files as $file) {
                $filename = basename($file);
                if (unlink($file)) {
                    StdOut::print("Deleted: $filename\n");
                } else {
                    StdOut::print("Failed to delete: $filename\n");
                }
            }

            StdOut::print("\nRemoval complete.\n\n");
            return true;
        }

        // Create migrations directory if it doesn't exist
        if (!file_exists($migrations_path)) {
            Files::mkDirOrFail($migrations_path);
            StdOut::print("Created migrations directory: $migrations_path\n");
        }

        // Check if --dir= or --file= is already specified in options
        $has_dir_param = false;
        foreach ($opt as $o) {
            if (Strings::startsWith('--dir=', $o) || Strings::startsWith('--dir:', $o) ||
                Strings::startsWith('--file=', $o) || Strings::startsWith('--file:', $o)) {
                $has_dir_param = true;
                break;
            }
        }

        // If no --dir= specified, add it pointing to the migrations folder
        if (!$has_dir_param) {
            $opt[] = '--dir=' . $migrations_relative_path;
        }

        // Prepend migration name to options and call the migration method
        array_unshift($opt, $migration_name);

        StdOut::print("\nCreating migration in $context_type '$context_name'...\n");

        // Call the existing migration method
        $this->migration(...$opt);

        return true;
    }

    /*
        Creates a migration within a module

        Usage: php com make migrations:module {module_name} {migration_name} [options]

        Example: php com make migrations:module FriendlyPOS products --create
                 php com make migrations:module FriendlyPOS users --table=users --edit
                 php com make migrations:module FriendlyPOS products --remove
    */
    function migrations_module(...$opt)
    {
        if (count($opt) < 2) {
            StdOut::print("\nError: Module name and migration name are required.\n");
            StdOut::print("Usage: php com make migrations:module {module_name} {migration_name} [options]\n");
            StdOut::print("Example: php com make migrations:module FriendlyPOS products --create\n\n");
            return;
        }

        // Extract module name and migration name
        $module_name = array_shift($opt);
        $migration_name = array_shift($opt);

        // Build paths
        $module_base = MODULES_PATH . $module_name;
        $relative_path = '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR .
                       'app' . DIRECTORY_SEPARATOR . 'Modules' . DIRECTORY_SEPARATOR .
                       $module_name . DIRECTORY_SEPARATOR . 'database' . DIRECTORY_SEPARATOR . 'migrations';

        // Use the helper method
        $this->createMigrationInContext(
            'module',
            $module_name,
            $module_base,
            $relative_path,
            $migration_name,
            $opt
        );
    }

    function provider($name, ...$opt)
    {
        $this->setup($name);

        $unignore = false;

        foreach ($opt as $o) {
            if (preg_match('/^(--even-ignored|--unignore|-u|--retry|-r)$/', $o)) {
                $unignore = true;
            }
        }

        $filename = $this->camel_case . 'ServiceProvider' . '.php';
        $dest_path = self::SERVICE_PROVIDERS_PATH . $filename;

        $protected = $unignore ? false : $this->hasFileProtection($filename, $dest_path, $opt);
        $remove    = $this->forDeletion($filename, $dest_path, $opt);

        if ($remove) {
            $ok = $this->write($dest_path, '', $protected, true);
            return;
        }

        $file = file_get_contents(self::SERVICE_PROVIDER_TEMPLATE);
        $file = str_replace('__NAME__', $this->camel_case . 'ServiceProvider', $file);

        $this->write($dest_path, $file, $protected, $remove);
    }

    // for translations
    function trans(...$opt)
    {
        $pot         = false;
        $po          = null;
        $mo          = false;

        $text_domain = null;
        $preset      = null;

        // dirs
        $from        = null;
        $to          = null;

        foreach ($opt as $o) {
            if (preg_match('/^(--pot)$/', $o)) {
                $pot = true;
            }

            if (preg_match('/^(--po)$/', $o)) {
                $po = true;
            }

            if (preg_match('/^(--mo)$/', $o)) {
                $mo = true;
            }

            if (Strings::startsWith('--preset=', $o) || Strings::startsWith('--preset:', $o)) {
                // Convert windows directory separator into *NIX
                $o = str_replace('\\', '/', $o);

                if (preg_match('~^--(preset)[=|:]([a-z0-9A-ZñÑ_-]+)$~', $o, $matches)) {
                    $preset = $matches[2];
                }
            }

            if (Strings::startsWith('--domain=', $o) || Strings::startsWith('--domain:', $o)) {
                // Convert windows directory separator into *NIX
                $o = str_replace('\\', '/', $o);

                if (preg_match('~^--(domain)[=|:]([a-z0-9A-ZñÑ_-]+)$~', $o, $matches)) {
                    $text_domain = $matches[2];
                }
            }

            if (Strings::startsWith('--from=', $o) || Strings::startsWith('--from:', $o)) {
                // Convert windows directory separator into *NIX
                $o = str_replace('\\', '/', $o);

                if (preg_match('~^--(from)[=|:]([a-z0-9A-ZñÑ_\-/:]+)$~', $o, $matches)) {
                    $from = $matches[2] . '/';
                }
            }

            if (Strings::startsWith('--to=', $o) || Strings::startsWith('--to:', $o)) {
                // Convert windows directory separator into *NIX
                $o = str_replace('\\', '/', $o);

                if (preg_match('~^--(to)[=|:]([a-z0-9A-ZñÑ_\-/:]+)$~', $o, $matches)) {
                    $to = $matches[2] . '/';
                }
            }
        }

        if ($pot) {
            Translate::convertPot($from, $text_domain);
            exit;
        }

        if ($po === true) {
            $include_po = ($mo === true);
        } else {
            $include_po = true;
        }

        Translate::exportLangDef($include_po, $from, $to, $text_domain, $preset);
    }

    /*
        Podría haber usado renderTemplate()

        Debería admitir rutas absolutas y no solo relativas a VIEW_PATH
    */
    function view($name, ...$opt)
    {
        $this->setup($name);

        $filename  = $this->snake_case . '.php';
        $dest_path = VIEWS_PATH . $filename;

        $filename  = str_replace('\\', '/', $filename);

        if (Strings::contains('/', $filename)) {
            $dir = Strings::beforeLast($filename, '/');
            Files::mkDirOrFail(VIEWS_PATH . $dir);
        }

        $protected = $this->hasFileProtection($filename, $dest_path, $opt);
        $remove    = $this->forDeletion($filename, $dest_path, $opt);

        if (!$remove) {
            $data = <<<HTML
            <h3>Un título</h3>
            
            <span>Un contenido cualquiera</span>
            HTML;
        } else {
            $data = '';
        }

        if (!$protected) {
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
    function css_scan(...$opt)
    {
        $is_relative = true;
        $dir         = null;

        foreach ($opt as $o) {
            if (Strings::startsWith('--relative=', $o)) {
                if (preg_match('~^--(relative)[=|:]([a-z0-9A-Z]+)$~', $o, $matches)) {
                    $val = strtolower($matches[2]);
                    $is_relative = ($val != "0" && $val != "false");
                }
            }

            if (Strings::startsWith('--dir=', $o) || Strings::startsWith('--dir:', $o)) {
                if (preg_match('/^--(dir|folder)[=|:]([a-z0-9A-ZñÑ_\.-\/\\\\]+)$/', $o, $matches)) {
                    $dir = $matches[2] . '/';
                }
            }
        }

        if (empty($dir)) {
            StdOut::print("Please specify path with --dir=");
            exit;
        }

        if ($is_relative) {
            $dir = Files::convertSlashes($dir);
        }

        $files = Files::glob($dir, '*.css');

        $lines = [];
        foreach ($files as $file) {
            if ($is_relative) {
                $file = Strings::afterIfContains($file, "assets" . DIRECTORY_SEPARATOR . $dir);
            }

            $lines[] = "css_file('$file');";
        }

        echo implode(PHP_EOL, $lines);
    }

     /**
     * Copia y parsea archivos desde una carpeta de plantillas al directorio del módulo.
     *
     * @param string $templateDir Directorio de las plantillas
     * @param string $basePath Directorio base del módulo
     * @param string $moduleName Nombre del módulo
     * @param array $opt Opciones como '-f' o '--force'
     * @return void
     */
    protected function copyAndParseTemplates(string $templateDir, string $basePath, string $moduleName, array $opt): void
    {
        $force = in_array('-f', $opt) || in_array('--force', $opt);

        // Obtener todos los archivos en la carpeta de plantillas recursivamente
        $files = Files::recursiveGlob($templateDir . DIRECTORY_SEPARATOR . '*');

        foreach ($files as $file) {
            if (is_dir($file)) {
                continue; // Saltar directorios, solo procesar archivos
            }

            // Calcular la ruta relativa respecto al directorio de plantillas
            $relativePath = str_replace($templateDir . DIRECTORY_SEPARATOR, '', $file);
            $destFile = $basePath . DIRECTORY_SEPARATOR . $relativePath;

            // Crear directorios destino si no existen
            $destDir = dirname($destFile);
            if (!is_dir($destDir)) {
                Files::mkDirOrFail($destDir);
            }

            // Verificar si el archivo ya existe y no se usa --force
            if (file_exists($destFile) && !$force) {
                StdOut::print("File '$destFile' already exists. Use -f or --force to overwrite.");
                continue;
            }

            // Leer el contenido del archivo plantilla
            $content = file_get_contents($file);

            // Definir valores para los placeholders
            $pascalName = Strings::toPascalCase($moduleName);
            $namespace = $this->namespace . '\\modules\\' . $pascalName;

            // Reemplazar placeholders en el contenido
            $content = str_replace('__NAME__', $pascalName, $content);
            $content = str_replace('__NAMESPACE__', $namespace, $content);

            // Escribir el archivo parseado en la ubicación destino
            file_put_contents($destFile, $content);
            StdOut::print("Created file: $destFile");
        }
    }

    /**
     * Crea una estructura de directorios basada en un array de nombres de directorios.
     *
     * @param array $directories Lista de directorios a crear (relativos al $basePath)
     * @param string $basePath Directorio base donde se crearán los directorios
     * @param array $options Opciones como '-f' o '--force' para forzar la creación
     * @return void
     */
    protected function makeScaffolding(array $directories, string $basePath, array $options = []): bool
    {
        $force  = in_array('-f', $options) || in_array('--force', $options);
        $remove = in_array('--remove', $options) || in_array('--delete', $options) || in_array('-r', $options) || in_array('-d', $options);

        // Solo verifico que no haya que crear nada
        if ($remove) {
            return false;
        }

        foreach ($directories as $dir) {
            $fullPath = $basePath . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $dir);
            if (is_dir($fullPath) && !$force) {
                StdOut::print("Directory '$fullPath' already exists. Use -f or --force to overwrite.");
                continue;
            }

            Files::mkDirOrFail($fullPath);
        }

        return true;
    }

    /**
     * Crea un nuevo módulo y copia plantillas si existen.
     *
     * @param string $name Nombre del módulo a crear
     * @param string ...$opt Opciones como '-f' o '--force'
     * @return void
     */
    public function module(string $name, ...$opt): void
    {
        $basePath = MODULES_PATH . $name;

        $remove = in_array('--remove', $opt) || in_array('--delete', $opt) || in_array('-r', $opt) || in_array('-d', $opt);

        if ($remove) {
            Files::rmDirOrFail($basePath, true);
            StdOut::print("Directory `$basePath` was deleted");
            return;
        }

        // Verificar si el directorio raíz existe y manejar la opción -f
        if (is_dir($basePath) && !in_array('-f', $opt) && !in_array('--force', $opt)) {
            StdOut::print("Module '$name' already exists. Use -f or --force to overwrite.");
            return;
        }

        $directories = [
            'assets/css',
            'assets/img',
            'assets/js',
            'assets/third_party',
            'config',
            'src/Libs',
            'src/Models',
            'src/Controllers',
            'src/Middlewares',
            'src/Interfaces',
            'src/Helpers',
            'src/Traits',
            'views',            
            'database/migrations',
            'database/seeders',
            'logs',
            'etc', // similar to Laravel's resources
            'tests'
        ];

        // Crear los directorios usando makeScaffolding
        $this->makeScaffolding($directories, $basePath, $opt);

        // Verificar si existe una carpeta de plantillas para este módulo
        $pascalName = Strings::toPascalCase(__FUNCTION__);
        $templateDir = CLASS_TEMPLATES_PATH . $pascalName;

        // dd($templateDir, '$templateDir'); exit; //

        if (is_dir($templateDir)) {
            // Copiar y parsear archivos desde la carpeta de plantillas
            $this->copyAndParseTemplates($templateDir, $basePath, $name, $opt);
        } else {
            // Si no existe la carpeta de plantillas, crear archivos por defecto
            $files = [
                "$name.php" => '<?php // Main module file',
                'config/config.php' => '<?php return []; // Configuration file',
            ];

            $force = in_array('-f', $opt) || in_array('--force', $opt);
            foreach ($files as $file => $content) {
                $filePath = $basePath . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $file);
                $dir = dirname($filePath);
                if (!is_dir($dir)) {
                    Files::mkDirOrFail($dir);
                }
                if (file_exists($filePath) && !$force) {
                    StdOut::print("File '$filePath' already exists. Use -f or --force to overwrite.");
                    continue;
                }
                file_put_contents($filePath, $content);
                StdOut::print("Created file: $filePath");
            }
        }

        StdOut::print("Module '$name' created successfully at '$basePath'.");
    }

    /**
     * Crea un nuevo "package" y copia plantillas si existen.
     *     
     */
    protected function package(string $packageName, $author = '', $destination = null, ...$opt): void
    {
        // Normalizar nombres a kebab-case para los directorios
        $authorSlug  = strtolower(str_replace(' ', '-', preg_replace('/[^a-zA-Z0-9]+/', '-', $author)));
        $packageSlug = strtolower(str_replace(' ', '-', preg_replace('/[^a-zA-Z0-9]+/', '-', $packageName)));

        if (empty($authorSlug) || empty($packageSlug)) {
            StdOut::print("Author and package name are required.");
            return;
        }

        $basePath = $destination ?: PACKAGES_PATH;
        $packagePath = $basePath . "{$authorSlug}/{$packageSlug}/";

        if (file_exists($packagePath)) {
            StdOut::print("Package already exists at: $packagePath");
            return;
        }

        $directories = [
            'assets/css',
            'assets/img',
            'assets/js',
            'assets/third_party',
            'config',
            'src/Libs',
            'src/Models',
            'src/Controllers',
            'src/Middlewares',
            'src/Interfaces',
            'src/Helpers',
            'src/Traits',
            'views',            
            'database/migrations',
            'database/seeders',
            'etc', // similar to Laravel's resources
            'tests'
        ];

        // Crear los directorios usando makeScaffolding
        $this->makeScaffolding($directories, $packagePath, $opt);

        // Generar el namespace basado en autor y nombre del paquete (en PascalCase)
        $namespace = ucfirst(Strings::toPascalCase($authorSlug)) . "\\" . ucfirst(Strings::toPascalCase($packageSlug));

        $composerJsonPath = $packagePath . 'composer.json';
        if (!file_exists($composerJsonPath)) {
            $composerContent = json_encode([
                "name"        => "{$authorSlug}/{$packageSlug}",
                "description" => "Package {$packageName} by {$author}",
                "type"        => "library",
                "autoload"    => [
                    "psr-4" => [
                        $namespace . "\\" => "src/"
                    ]
                ],
                "extra"       => [
                    "simplerest" => [
                        "providers" => [
                            $namespace . "\\ServiceProvider"
                        ]
                    ]
                ],
                "require"     => new \stdClass()
            ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);

            file_put_contents($composerJsonPath, $composerContent);
            StdOut::print("composer.json created at: $composerJsonPath");
        }

        // Crear archivo README.md básico si no existe
        $readmePath = $packagePath . 'README.md';
        if (!file_exists($readmePath)) {
            $readmeContent = "# {$packageName}\n\n";
            $readmeContent .= "Package {$packageName} by {$author}.\n\n";
            $readmeContent .= "## Installation\n\n";
            $readmeContent .= "1. Add the package namespace to `composer.json` autoload section:\n\n";
            $readmeContent .= "```json\n";
            $readmeContent .= "\"autoload\": {\n";
            $readmeContent .= "    \"psr-4\": {\n";
            $readmeContent .= "        \"{$namespace}\\\\\": \"packages/{$authorSlug}/{$packageSlug}/src\"\n";
            $readmeContent .= "    }\n";
            $readmeContent .= "}\n";
            $readmeContent .= "```\n\n";
            $readmeContent .= "2. Add the ServiceProvider to `config/config.php` providers array:\n\n";
            $readmeContent .= "```php\n";
            $readmeContent .= "'providers' => [\n";
            $readmeContent .= "    {$namespace}\\ServiceProvider::class,\n";
            $readmeContent .= "    // ...\n";
            $readmeContent .= "],\n";
            $readmeContent .= "```\n\n";
            $readmeContent .= "3. Regenerate the autoloader:\n\n";
            $readmeContent .= "```bash\n";
            $readmeContent .= "composer dumpautoload --no-ansi\n";
            $readmeContent .= "```\n\n";
            $readmeContent .= "## Usage\n\n";
            $readmeContent .= "### Routes\n\n";
            $readmeContent .= "Define your routes in `config/routes.php`:\n\n";
            $readmeContent .= "```php\n";
            $readmeContent .= "use Boctulus\\Simplerest\\Core\\WebRouter;\n\n";
            $readmeContent .= "WebRouter::get('{$packageSlug}/example', '{$namespace}\\Controllers\\ExampleController@index');\n";
            $readmeContent .= "```\n\n";
            $readmeContent .= "### Controllers\n\n";
            $readmeContent .= "Create controllers in `src/Controllers/` with namespace `{$namespace}\\Controllers`.\n\n";
            $readmeContent .= "## Structure\n\n";
            $readmeContent .= "```\n";
            $readmeContent .= "{$packageSlug}/\n";
            $readmeContent .= "├── assets/          # CSS, JS, images\n";
            $readmeContent .= "├── config/          # Configuration files (routes, etc.)\n";
            $readmeContent .= "├── database/        # Migrations and seeders\n";
            $readmeContent .= "├── etc/             # Additional resources\n";
            $readmeContent .= "├── src/             # Source code\n";
            $readmeContent .= "│   ├── Controllers/ # Controllers\n";
            $readmeContent .= "│   ├── Models/      # Models\n";
            $readmeContent .= "│   ├── Middlewares/ # Middlewares\n";
            $readmeContent .= "│   ├── Helpers/     # Helper functions\n";
            $readmeContent .= "│   ├── Libs/        # Libraries\n";
            $readmeContent .= "│   ├── Interfaces/  # Interfaces\n";
            $readmeContent .= "│   └── Traits/      # Traits\n";
            $readmeContent .= "├── tests/           # Unit tests\n";
            $readmeContent .= "├── views/           # View templates\n";
            $readmeContent .= "└── composer.json    # Package metadata\n";
            $readmeContent .= "```\n";

            file_put_contents($readmePath, $readmeContent);
            StdOut::print("README.md created at: $readmePath");
        }

        // Crear archivo LICENSE básico (MIT por defecto) si no existe
        $licensePath = $packagePath . 'LICENSE';
        if (!file_exists($licensePath)) {
            $licenseContent = "MIT License\n\nCopyright (c) " . date('Y') . " {$author}\n\n" .
                "Permission is hereby granted, free of charge, to any person obtaining a copy\n" .
                "of this software and associated documentation files (the \"Software\"), to deal\n" .
                "in the Software without restriction, including without limitation the rights\n" .
                "to use, copy, modify, merge, publish, distribute, sublicense, and/or sell\n" .
                "copies of the Software, and to permit persons to whom the Software is\n" .
                "furnished to do so, subject to the following conditions:\n\n" .
                "[...]\n";
            file_put_contents($licensePath, $licenseContent);
            StdOut::print("LICENSE created at: $licensePath");
        }

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
                    StdOut::print("Copied and updated template to: $destinationFile");
                } else {
                    StdOut::print("Failed to read template: $template");
                }
            }
        }

        // Crear archivo de rutas web de ejemplo
        $routesPath = $packagePath . 'config/routes.php';
        if (!file_exists($routesPath)) {
            $routesContent = "<?php\n\n";
            $routesContent .= "use Boctulus\Simplerest\Core\WebRouter;\n\n";
            $routesContent .= "/*\n";
            $routesContent .= "    {$packageName} Package Web Routes\n";
            $routesContent .= "*/\n\n";
            $routesContent .= "// Example route:\n";
            $routesContent .= "// WebRouter::get('{$packageSlug}/example', '{$namespace}\\Controllers\\ExampleController@index');\n";

            file_put_contents($routesPath, $routesContent);
            StdOut::print("Web routes file created at: $routesPath");
        }

        // Crear archivo de rutas CLI de ejemplo
        $cliRoutesPath = $packagePath . 'config/cli_routes.php';
        if (!file_exists($cliRoutesPath)) {
            $cliRoutesContent = "<?php\n\n";
            $cliRoutesContent .= "use Boctulus\Simplerest\Core\CliRouter;\n\n";
            $cliRoutesContent .= "/*\n";
            $cliRoutesContent .= "    {$packageName} Package - CLI Routes\n";
            $cliRoutesContent .= "    \n";
            $cliRoutesContent .= "    Define CLI commands for the {$packageName} package.\n";
            $cliRoutesContent .= "    Commands can be executed with: php com {$packageSlug} <command>\n";
            $cliRoutesContent .= "*/\n\n";

            $cliRoutesContent .= "// ========================================\n";
            $cliRoutesContent .= "// EXAMPLE 1: Simple command with closure\n";
            $cliRoutesContent .= "// ========================================\n\n";
            $cliRoutesContent .= "// CliRouter::command('{$packageSlug}:hello', function() {\n";
            $cliRoutesContent .= "//     return 'Hello from {$packageName}!';\n";
            $cliRoutesContent .= "// });\n";
            $cliRoutesContent .= "// Execute: php com {$packageSlug}:hello\n";
            $cliRoutesContent .= "//      or: php com {$packageSlug} hello\n\n";

            $cliRoutesContent .= "// ========================================\n";
            $cliRoutesContent .= "// EXAMPLE 2: Command with parameters\n";
            $cliRoutesContent .= "// ========================================\n\n";
            $cliRoutesContent .= "// CliRouter::command('{$packageSlug}:greet', function(\$name) {\n";
            $cliRoutesContent .= "//     return \"Hello, \$name! Welcome to {$packageName}.\";\n";
            $cliRoutesContent .= "// });\n";
            $cliRoutesContent .= "// Execute: php com {$packageSlug}:greet John\n\n";

            $cliRoutesContent .= "// ========================================\n";
            $cliRoutesContent .= "// EXAMPLE 3: Command with controller\n";
            $cliRoutesContent .= "// ========================================\n\n";
            $cliRoutesContent .= "// CliRouter::command('{$packageSlug}:example', '{$namespace}\\Controllers\\ExampleController@index');\n";
            $cliRoutesContent .= "// Execute: php com {$packageSlug}:example\n\n";

            $cliRoutesContent .= "// ========================================\n";
            $cliRoutesContent .= "// EXAMPLE 4: Grouped commands\n";
            $cliRoutesContent .= "// ========================================\n\n";
            $cliRoutesContent .= "// CliRouter::group('{$packageSlug}', function() {\n";
            $cliRoutesContent .= "//     \n";
            $cliRoutesContent .= "//     // Setup commands\n";
            $cliRoutesContent .= "//     CliRouter::command('install', '{$namespace}\\Controllers\\SetupController@install');\n";
            $cliRoutesContent .= "//     CliRouter::command('uninstall', '{$namespace}\\Controllers\\SetupController@uninstall');\n";
            $cliRoutesContent .= "//     \n";
            $cliRoutesContent .= "//     // Database commands\n";
            $cliRoutesContent .= "//     CliRouter::group('db', function() {\n";
            $cliRoutesContent .= "//         CliRouter::command('migrate', '{$namespace}\\Controllers\\DbController@migrate');\n";
            $cliRoutesContent .= "//         CliRouter::command('seed', '{$namespace}\\Controllers\\DbController@seed');\n";
            $cliRoutesContent .= "//         CliRouter::command('reset', '{$namespace}\\Controllers\\DbController@reset');\n";
            $cliRoutesContent .= "//     });\n";
            $cliRoutesContent .= "//     \n";
            $cliRoutesContent .= "//     // Cache commands\n";
            $cliRoutesContent .= "//     CliRouter::group('cache', function() {\n";
            $cliRoutesContent .= "//         CliRouter::command('clear', function() {\n";
            $cliRoutesContent .= "//             return 'Cache cleared successfully!';\n";
            $cliRoutesContent .= "//         });\n";
            $cliRoutesContent .= "//         CliRouter::command('info', '{$namespace}\\Controllers\\CacheController@info');\n";
            $cliRoutesContent .= "//     });\n";
            $cliRoutesContent .= "// });\n\n";
            $cliRoutesContent .= "// Execute:\n";
            $cliRoutesContent .= "//   php com {$packageSlug} install\n";
            $cliRoutesContent .= "//   php com {$packageSlug} db migrate\n";
            $cliRoutesContent .= "//   php com {$packageSlug} db seed\n";
            $cliRoutesContent .= "//   php com {$packageSlug} cache clear\n\n";

            $cliRoutesContent .= "// ========================================\n";
            $cliRoutesContent .= "// EXAMPLE 5: Command with validation\n";
            $cliRoutesContent .= "// ========================================\n\n";
            $cliRoutesContent .= "// CliRouter::command('{$packageSlug}:create-user', function(\$email, \$role = 'user') {\n";
            $cliRoutesContent .= "//     if (!filter_var(\$email, FILTER_VALIDATE_EMAIL)) {\n";
            $cliRoutesContent .= "//         return \"Error: Invalid email address\";\n";
            $cliRoutesContent .= "//     }\n";
            $cliRoutesContent .= "//     return \"User created: \$email with role: \$role\";\n";
            $cliRoutesContent .= "// });\n";
            $cliRoutesContent .= "// Execute: php com {$packageSlug}:create-user user@example.com admin\n\n";

            $cliRoutesContent .= "// ========================================\n";
            $cliRoutesContent .= "// YOUR COMMANDS HERE\n";
            $cliRoutesContent .= "// ========================================\n\n";
            $cliRoutesContent .= "// Add your custom CLI routes below:\n\n";

            file_put_contents($cliRoutesPath, $cliRoutesContent);
            StdOut::print("CLI routes file created at: $cliRoutesPath");
        }

        // Crear archivo de configuración del paquete
        $configPath = $packagePath . 'config/config.php';
        if (!file_exists($configPath)) {
            if (file_exists(self::PACKAGE_CONFIG_TEMPLATE)) {
                $configContent = file_get_contents(self::PACKAGE_CONFIG_TEMPLATE);
                file_put_contents($configPath, $configContent);
                StdOut::print("Package config file created at: $configPath");
            }
        }

        StdOut::print("Package created successfully at: $packagePath");
        StdOut::print("");
        StdOut::print("Next steps:");
        StdOut::print("1. Add the package namespace to composer.json autoload section:");
        StdOut::print("   \"{$namespace}\\\\\": \"packages/{$authorSlug}/{$packageSlug}/src\"");
        StdOut::print("");
        StdOut::print("2. Add the ServiceProvider to config/config.php providers array:");
        StdOut::print("   {$namespace}\\ServiceProvider::class,");
        StdOut::print("");
        StdOut::print("3. Run: composer dumpautoload --no-ansi");
        StdOut::print("");
    }

    function widget(string $name, ...$opt)
    {
        $dir    = WIDGETS_PATH . $name;
        $force  = in_array('-f', $opt) || in_array('--force', $opt);
        $js     = in_array('--js', $opt) || in_array('--include-js', $opt);
        $remove = in_array('--remove', $opt) || in_array('--delete', $opt) || in_array('-r', $opt) || in_array('-d', $opt);

        if ($remove) {
            Files::rmDirOrFail($dir, true);
            StdOut::print("Directory `$dir` was deleted");
            return;
        }

        $directories = ['']; // Directorio raíz

        if ($js) {
            $directories[] = 'js';
        }

        $this->makeScaffolding($directories, $dir, $opt);

        // Crear archivos
        $files = [
            'styles.css' => '/* Widget styles */'
        ];

        if ($js) {
            $files["js/$name.js"] = '// Widget JS';
        }

        foreach ($files as $file => $content) {
            $filePath = $dir . DIRECTORY_SEPARATOR . $file;
            if (!file_exists($filePath) || $force) {
                file_put_contents($filePath, $content);
                StdOut::print("$filePath was created");
            }
        }
    }

    function command($name, ...$opt)
    {
        $template_path = self::COMMAND_TEMPLATE;
        $prefix = '';
        $subfix = 'Command';  // Ej: 'Controller'

        $this->renderTemplate($name, $prefix, $subfix, COMMANDS_PATH, $template_path, '', ...$opt);
    }

    function testcase($name, ...$opt)
    {
        $template_path = self::TEST_TEMPLATE;
        $prefix = '';
        $subfix = 'Test';
        $dest_path = ROOT_PATH . 'tests' . DIRECTORY_SEPARATOR;

        $this->renderTemplate($name, $prefix, $subfix, $dest_path, $template_path, '', ...$opt);
    }

    // alias de testcase()
    function phpunit($name, ...$opt){
        $this->testcase($name, ...$opt);
    }

    /*
        Create a controller in a specific module

        Usage: php com make controller:module {module_name} {controller_name}

        Example: php com make controller:module FriendlyPOS MyController
    */
    function controller_module(...$opt)
    {
        if (count($opt) < 2) {
            StdOut::print("\nError: Module name and controller name are required.\n");
            StdOut::print("Usage: php com make controller:module {module_name} {controller_name}\n");
            StdOut::print("Example: php com make controller:module FriendlyPOS MyController\n\n");
            return;
        }

        // Extract module name and controller name
        $module_name = array_shift($opt);
        $controller_name = array_shift($opt);

        // Check if module exists
        $module_base = MODULES_PATH . $module_name;
        if (!file_exists($module_base)) {
            StdOut::print("\nError: Module '$module_name' does not exist at: $module_base\n\n");
            return;
        }

        // Build namespace and destination path
        $original_namespace = $this->namespace;
        $this->namespace = "Boctulus\\Simplerest\\Modules\\$module_name";

        $dest_path = $module_base . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'Controllers' . DIRECTORY_SEPARATOR;

        // Create directory if it doesn't exist
        if (!file_exists($dest_path)) {
            Files::mkDirOrFail($dest_path);
        }

        StdOut::print("\nCreating controller in module '$module_name'...\n\n");

        // Call renderTemplate directly
        $namespace = $this->namespace . '\\Controllers';
        $template_path = self::TEMPLATES . 'Controller.php';
        $prefix = '';
        $subfix = 'Controller';

        $this->renderTemplate($controller_name, $prefix, $subfix, $dest_path, $template_path, $namespace, ...$opt);

        // Restore original namespace
        $this->namespace = $original_namespace;
    }

    function middleware_module(...$opt)
    {
        if (count($opt) < 2) {
            StdOut::print("\nError: Module name and middleware name are required.\n");
            return;
        }

        $module_name = array_shift($opt);
        $middleware_name = array_shift($opt);

        $module_base = MODULES_PATH . $module_name;
        if (!file_exists($module_base)) {
            StdOut::print("\nError: Module '$module_name' does not exist.\n\n");
            return;
        }

        $namespace_base = "{$this->namespace}\\$module_name";
        $dest_path      = $module_base . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'Middlewares' . DIRECTORY_SEPARATOR;

        if (!file_exists($dest_path)) {
            Files::mkDirOrFail($dest_path);
        }

        StdOut::print("\nCreating middleware in module '$module_name'...\n\n");

        $namespace = $namespace_base . '\\Middlewares';
        $template_path = self::TEMPLATES . 'Middleware.php';
        $this->renderTemplate($middleware_name, '', '', $dest_path, $template_path, $namespace, ...$opt);
    }

    function lib_module(...$opt)
    {
        if (count($opt) < 2) {
            StdOut::print("\nError: Module name and lib name are required.\n");
            return;
        }

        $module_name = array_shift($opt);
        $lib_name = array_shift($opt);

        $module_base = MODULES_PATH . $module_name;
        if (!file_exists($module_base)) {
            StdOut::print("\nError: Module '$module_name' does not exist.\n\n");
            return;
        }

        $original_namespace = $this->namespace;
        $this->namespace = "Boctulus\\Simplerest\\modules\\$module_name";

        $dest_path = $module_base . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'Libs' . DIRECTORY_SEPARATOR;

        if (!file_exists($dest_path)) {
            Files::mkDirOrFail($dest_path);
        }

        StdOut::print("\nCreating lib in module '$module_name'...\n\n");

        $namespace = $this->namespace . '\\Libs';
        $template_path = self::LIBS_TEMPLATE;
        $this->renderTemplate($lib_name, '', '', $dest_path, $template_path, $namespace, ...$opt);

        $this->namespace = $original_namespace;
    }

    function helper_module(...$opt)
    {
        if (count($opt) < 2) {
            StdOut::print("\nError: Module name and helper name are required.\n");
            return;
        }

        $module_name = array_shift($opt);
        $helper_name = array_shift($opt);

        $module_base = MODULES_PATH . $module_name;
        if (!file_exists($module_base)) {
            StdOut::print("\nError: Module '$module_name' does not exist.\n\n");
            return;
        }

        $original_namespace = $this->namespace;
        $this->namespace = "Boctulus\\Simplerest\\modules\\$module_name";

        $dest_path = $module_base . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'Helpers' . DIRECTORY_SEPARATOR;

        if (!file_exists($dest_path)) {
            Files::mkDirOrFail($dest_path);
        }

        StdOut::print("\nCreating helper in module '$module_name'...\n\n");

        $namespace = $this->namespace . '\\helpers';
        $template_path = self::HELPER_TEMPLATE;
        $opt[] = "--lowercase";
        $this->renderTemplate($helper_name, '', '', $dest_path, $template_path, $namespace, ...$opt);

        $this->namespace = $original_namespace;
    }

    function interface_module(...$opt)
    {
        if (count($opt) < 2) {
            StdOut::print("\nError: Module name and interface name are required.\n");
            return;
        }

        $module_name = array_shift($opt);
        $interface_name = array_shift($opt);

        $module_base = MODULES_PATH . $module_name;
        if (!file_exists($module_base)) {
            StdOut::print("\nError: Module '$module_name' does not exist.\n\n");
            return;
        }

        $original_namespace = $this->namespace;
        $this->namespace = "Boctulus\\Simplerest\\modules\\$module_name";

        $dest_path = $module_base . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'Interfaces' . DIRECTORY_SEPARATOR;

        if (!file_exists($dest_path)) {
            Files::mkDirOrFail($dest_path);
        }

        StdOut::print("\nCreating interface in module '$module_name'...\n\n");

        $namespace = $this->namespace . '\\Interfaces';
        $template_path = self::INTERFACE_TEMPLATE;
        $this->renderTemplate($interface_name, 'I', '', $dest_path, $template_path, $namespace, ...$opt);

        $this->namespace = $original_namespace;
    }

    function model_module(...$opt)
    {
        if (count($opt) < 2) {
            StdOut::print("\nError: Module name and model name are required.\n");
            return;
        }

        $module_name = array_shift($opt);
        $model_name = array_shift($opt);

        $module_base = MODULES_PATH . $module_name;
        if (!file_exists($module_base)) {
            StdOut::print("\nError: Module '$module_name' does not exist.\n\n");
            return;
        }

        $original_namespace = $this->namespace;
        $this->namespace = "Boctulus\\Simplerest\\modules\\$module_name";

        $dest_path = $module_base . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'Models' . DIRECTORY_SEPARATOR;

        if (!file_exists($dest_path)) {
            Files::mkDirOrFail($dest_path);
        }

        StdOut::print("\nCreating model in module '$module_name'...\n\n");

        $namespace = $this->namespace . '\\Models';
        $template_path = self::MODEL_NO_SCHEMA_TEMPLATE; // Use schema-less template for simplicity
        $this->renderTemplate($model_name, '', 'Model', $dest_path, $template_path, $namespace, ...$opt);

        $this->namespace = $original_namespace;
    }

    /*
        Creates a migration within a package using autodiscovery

        Usage: php com make migrations:package {package_name} {migration_name} [options]

        Example: php com make migrations:package zippy categories --create
                 php com make migrations:package web-test users --table=users --edit
                 php com make migrations:package zippy products --table=products --class_name=ProductsAddDescription
                 php com make migrations:package web-test orders --create --table=orders
                 php com make migrations:package zippy categories --remove
    */
    function migrations_package(...$opt)
    {
        if (count($opt) < 2) {
            StdOut::print("\nError: Package name and migration name are required.\n");
            StdOut::print("Usage: php com make migrations:package {package_name} {migration_name} [options]\n");
            StdOut::print("Example: php com make migrations:package zippy categories --create\n\n");
            return;
        }

        // Extract package name and migration name
        $package_name = array_shift($opt);
        $migration_name = array_shift($opt);

        // Validate package name format (lowercase, alphanumeric, dashes, underscores, and optional author:package format)
        if (!preg_match('/^([a-z0-9_-]+:)?[a-z0-9_-]+$/', $package_name)) {
            StdOut::print("\nError: Invalid package name '$package_name'. Use format 'package' or 'author:package'.\n\n");
            return;
        }

        // Use autodiscovery to find package (supports both 'package' and 'author:package' formats)
        if (strpos($package_name, ':') !== false) {
            // Format: author:package
            $package_info = find_package_by_full_name(str_replace(':', '/', $package_name));
        } else {
            // Format: package
            $package_info = find_package_by_name($package_name);
        }

        if ($package_info === null) {
            StdOut::print("\nError: Package '$package_name' not found in any known location.\n");
            StdOut::print("Available packages:\n");
            $packages = get_packages();
            foreach ($packages as $pkg_path) {
                $parts = explode(DIRECTORY_SEPARATOR, rtrim($pkg_path, DIRECTORY_SEPARATOR));
                if (count($parts) >= 2) {
                    $name = array_pop($parts);
                    $author = array_pop($parts);
                    StdOut::print("  - $author/$name\n");
                }
            }
            StdOut::print("\n");
            return;
        }

        // Build paths using discovered package info
        $package_base = rtrim($package_info['path'], DIRECTORY_SEPARATOR);

        // Build relative path to migrations directory
        $relative_path = '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR .
                       'packages' . DIRECTORY_SEPARATOR . $package_info['author'] . DIRECTORY_SEPARATOR .
                       $package_info['name'] . DIRECTORY_SEPARATOR . 'database' . DIRECTORY_SEPARATOR . 'migrations';

        // Use the helper method
        $this->createMigrationInContext(
            'package',
            $package_info['full_name'],
            $package_base,
            $relative_path,
            $migration_name,
            $opt
        );
    }

    /*
        Creates a controller within a package using autodiscovery

        Usage: php com make controller:package {package_name} {controller_name} [options]

        Example: php com make controller:package zippy MyController
                 php com make controller:package web-test UserController --force
    */
    function controller_package(...$opt)
    {
        if (count($opt) < 2) {
            StdOut::print("\nError: Package name and controller name are required.\n");
            StdOut::print("Usage: php com make controller:package {package_name} {controller_name}\n");
            StdOut::print("Example: php com make controller:package zippy MyController\n\n");
            return;
        }

        // Extract package name and controller name
        $package_name = array_shift($opt);
        $controller_name = array_shift($opt);

        // Validate package name format (supports both 'package' and 'author:package' formats)
        if (!preg_match('/^([a-z0-9_-]+:)?[a-z0-9_-]+$/', $package_name)) {
            StdOut::print("\nError: Invalid package name '$package_name'. Use format 'package' or 'author:package'.\n\n");
            return;
        }

        // Use autodiscovery to find package
        if (strpos($package_name, ':') !== false) {
            $package_info = find_package_by_full_name(str_replace(':', '/', $package_name));
        } else {
            $package_info = find_package_by_name($package_name);
        }

        if ($package_info === null) {
            StdOut::print("\nError: Package '$package_name' not found.\n\n");
            return;
        }

        // Build namespace and destination path
        $original_namespace = $this->namespace;
        $this->namespace = $package_info['namespace'];

        $package_base = rtrim($package_info['path'], DIRECTORY_SEPARATOR);
        $dest_path = $package_base . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'Controllers' . DIRECTORY_SEPARATOR;

        // Create directory if it doesn't exist
        if (!file_exists($dest_path)) {
            Files::mkDirOrFail($dest_path);
        }

        StdOut::print("\nCreating controller in package '{$package_info['full_name']}'...\n\n");

        // Call the original controller method
        $namespace = $this->namespace . '\\Controllers';
        $template_path = self::TEMPLATES . 'Controller.php';
        $prefix = '';
        $subfix = 'Controller';

        $this->renderTemplate($controller_name, $prefix, $subfix, $dest_path, $template_path, $namespace, ...$opt);

        // Restore original namespace
        $this->namespace = $original_namespace;
    }

    /*
        Creates a middleware within a package using autodiscovery

        Usage: php com make middleware:package {package_name} {middleware_name} [options]

        Example: php com make middleware:package zippy MyMiddleware
    */
    function middleware_package(...$opt)
    {
        if (count($opt) < 2) {
            StdOut::print("\nError: Package name and middleware name are required.\n");
            StdOut::print("Usage: php com make middleware:package {package_name} {middleware_name}\n");
            StdOut::print("Example: php com make middleware:package zippy MyMiddleware\n\n");
            return;
        }

        $package_name = array_shift($opt);
        $middleware_name = array_shift($opt);

        if (!preg_match('/^([a-z0-9_-]+:)?[a-z0-9_-]+$/', $package_name)) {
            StdOut::print("\nError: Invalid package name '$package_name'. Use format 'package' or 'author:package'.\n\n");
            return;
        }

        // Use autodiscovery
        if (strpos($package_name, ':') !== false) {
            $package_info = find_package_by_full_name(str_replace(':', '/', $package_name));
        } else {
            $package_info = find_package_by_name($package_name);
        }

        if ($package_info === null) {
            StdOut::print("\nError: Package '$package_name' not found.\n\n");
            return;
        }

        $original_namespace = $this->namespace;
        $this->namespace = $package_info['namespace'];

        $package_base = rtrim($package_info['path'], DIRECTORY_SEPARATOR);
        $dest_path = $package_base . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'Middlewares' . DIRECTORY_SEPARATOR;

        if (!file_exists($dest_path)) {
            Files::mkDirOrFail($dest_path);
        }

        StdOut::print("\nCreating middleware in package '{$package_info['full_name']}'...\n\n");

        $namespace = $this->namespace . '\\Middlewares';
        $template_path = self::TEMPLATES . 'Middleware.php';
        $this->renderTemplate($middleware_name, '', '', $dest_path, $template_path, $namespace, ...$opt);

        $this->namespace = $original_namespace;
    }

    /*
        Creates a lib within a package using autodiscovery

        Usage: php com make lib:package {package_name} {lib_name} [options]

        Example: php com make lib:package zippy MyLib
    */
    function lib_package(...$opt)
    {
        if (count($opt) < 2) {
            StdOut::print("\nError: Package name and lib name are required.\n");
            StdOut::print("Usage: php com make lib:package {package_name} {lib_name}\n");
            StdOut::print("Example: php com make lib:package zippy MyLib\n\n");
            return;
        }

        $package_name = array_shift($opt);
        $lib_name = array_shift($opt);

        if (!preg_match('/^([a-z0-9_-]+:)?[a-z0-9_-]+$/', $package_name)) {
            StdOut::print("\nError: Invalid package name '$package_name'. Use format 'package' or 'author:package'.\n\n");
            return;
        }

        // Use autodiscovery
        if (strpos($package_name, ':') !== false) {
            $package_info = find_package_by_full_name(str_replace(':', '/', $package_name));
        } else {
            $package_info = find_package_by_name($package_name);
        }

        if ($package_info === null) {
            StdOut::print("\nError: Package '$package_name' not found.\n\n");
            return;
        }

        $original_namespace = $this->namespace;
        $this->namespace = $package_info['namespace'];

        $package_base = rtrim($package_info['path'], DIRECTORY_SEPARATOR);
        $dest_path = $package_base . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'Libs' . DIRECTORY_SEPARATOR;

        if (!file_exists($dest_path)) {
            Files::mkDirOrFail($dest_path);
        }

        StdOut::print("\nCreating lib in package '{$package_info['full_name']}'...\n\n");

        $namespace = $this->namespace . '\\Libs';
        $template_path = self::TEMPLATES . 'Lib.php';
        $this->renderTemplate($lib_name, '', '', $dest_path, $template_path, $namespace, ...$opt);

        $this->namespace = $original_namespace;
    }

    /*
        Creates a helper within a package using autodiscovery

        Usage: php com make helper:package {package_name} {helper_name} [options]

        Example: php com make helper:package zippy my_helper
    */
    function helper_package(...$opt)
    {
        if (count($opt) < 2) {
            StdOut::print("\nError: Package name and helper name are required.\n");
            StdOut::print("Usage: php com make helper:package {package_name} {helper_name}\n");
            StdOut::print("Example: php com make helper:package zippy my_helper\n\n");
            return;
        }

        $package_name = array_shift($opt);
        $helper_name = array_shift($opt);

        if (!preg_match('/^([a-z0-9_-]+:)?[a-z0-9_-]+$/', $package_name)) {
            StdOut::print("\nError: Invalid package name '$package_name'. Use format 'package' or 'author:package'.\n\n");
            return;
        }

        // Use autodiscovery
        if (strpos($package_name, ':') !== false) {
            $package_info = find_package_by_full_name(str_replace(':', '/', $package_name));
        } else {
            $package_info = find_package_by_name($package_name);
        }

        if ($package_info === null) {
            StdOut::print("\nError: Package '$package_name' not found.\n\n");
            return;
        }

        $package_base = rtrim($package_info['path'], DIRECTORY_SEPARATOR);
        $dest_path = $package_base . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'Helpers' . DIRECTORY_SEPARATOR;

        if (!file_exists($dest_path)) {
            Files::mkDirOrFail($dest_path);
        }

        StdOut::print("\nCreating helper in package '{$package_info['full_name']}'...\n\n");

        $template_path = self::TEMPLATES . 'helper.php';
        $file_name = $helper_name . '.php';
        $dest_file = $dest_path . $file_name;

        if (file_exists($template_path)) {
            $content = file_get_contents($template_path);
            if ($content !== false) {
                // Replace placeholders if template has any
                $updated_content = str_replace('__NAME__', $helper_name, $content);
                file_put_contents($dest_file, $updated_content);
                StdOut::print("Created: $dest_file\n");
            }
        } else {
            // Create basic helper file
            $content = "<?php\n\n";
            $content .= "/**\n";
            $content .= " * Helper: $helper_name\n";
            $content .= " * \n";
            $content .= " * Package: {$package_info['full_name']}\n";
            $content .= " */\n\n";
            $content .= "if (!function_exists('{$helper_name}')) {\n";
            $content .= "    function {$helper_name}() {\n";
            $content .= "        // TODO: Implement helper function\n";
            $content .= "    }\n";
            $content .= "}\n";

            file_put_contents($dest_file, $content);
            StdOut::print("Created: $dest_file\n");
        }
    }

    /*
        Creates an interface within a package using autodiscovery

        Usage: php com make interface:package {package_name} {interface_name} [options]

        Example: php com make interface:package zippy MyInterface
    */
    function interface_package(...$opt)
    {
        if (count($opt) < 2) {
            StdOut::print("\nError: Package name and interface name are required.\n");
            StdOut::print("Usage: php com make interface:package {package_name} {interface_name}\n");
            StdOut::print("Example: php com make interface:package zippy MyInterface\n\n");
            return;
        }

        $package_name = array_shift($opt);
        $interface_name = array_shift($opt);

        if (!preg_match('/^([a-z0-9_-]+:)?[a-z0-9_-]+$/', $package_name)) {
            StdOut::print("\nError: Invalid package name '$package_name'. Use format 'package' or 'author:package'.\n\n");
            return;
        }

        // Use autodiscovery
        if (strpos($package_name, ':') !== false) {
            $package_info = find_package_by_full_name(str_replace(':', '/', $package_name));
        } else {
            $package_info = find_package_by_name($package_name);
        }

        if ($package_info === null) {
            StdOut::print("\nError: Package '$package_name' not found.\n\n");
            return;
        }

        $original_namespace = $this->namespace;
        $this->namespace = $package_info['namespace'];

        $package_base = rtrim($package_info['path'], DIRECTORY_SEPARATOR);
        $dest_path = $package_base . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'Interfaces' . DIRECTORY_SEPARATOR;

        if (!file_exists($dest_path)) {
            Files::mkDirOrFail($dest_path);
        }

        StdOut::print("\nCreating interface in package '{$package_info['full_name']}'...\n\n");

        $namespace = $this->namespace . '\\Interfaces';
        $template_path = self::TEMPLATES . 'Interface.php';
        $this->renderTemplate($interface_name, '', '', $dest_path, $template_path, $namespace, ...$opt);

        $this->namespace = $original_namespace;
    }

    /*
        Creates a model within a package using autodiscovery

        Usage: php com make model:package {package_name} {model_name} [options]

        Example: php com make model:package zippy Product
    */
    function model_package(...$opt)
    {
        if (count($opt) < 2) {
            StdOut::print("\nError: Package name and model name are required.\n");
            StdOut::print("Usage: php com make model:package {package_name} {model_name}\n");
            StdOut::print("Example: php com make model:package zippy Product\n\n");
            return;
        }

        $package_name = array_shift($opt);
        $model_name = array_shift($opt);

        if (!preg_match('/^([a-z0-9_-]+:)?[a-z0-9_-]+$/', $package_name)) {
            StdOut::print("\nError: Invalid package name '$package_name'. Use format 'package' or 'author:package'.\n\n");
            return;
        }

        // Use autodiscovery
        if (strpos($package_name, ':') !== false) {
            $package_info = find_package_by_full_name(str_replace(':', '/', $package_name));
        } else {
            $package_info = find_package_by_name($package_name);
        }

        if ($package_info === null) {
            StdOut::print("\nError: Package '$package_name' not found.\n\n");
            return;
        }

        $original_namespace = $this->namespace;
        $this->namespace = $package_info['namespace'];

        $package_base = rtrim($package_info['path'], DIRECTORY_SEPARATOR);
        $dest_path = $package_base . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'Models' . DIRECTORY_SEPARATOR;

        if (!file_exists($dest_path)) {
            Files::mkDirOrFail($dest_path);
        }

        StdOut::print("\nCreating model in package '{$package_info['full_name']}'...\n\n");

        $namespace = $this->namespace . '\\Models';
        $template_path = self::MODEL_NO_SCHEMA_TEMPLATE; // Use schema-less template for simplicity
        $this->renderTemplate($model_name, '', 'Model', $dest_path, $template_path, $namespace, ...$opt);

        $this->namespace = $original_namespace;
    }
}
