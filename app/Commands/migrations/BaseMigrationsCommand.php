<?php

use Boctulus\Simplerest\Core\Commands\BaseCommand;
use Boctulus\Simplerest\Core\Interfaces\IMigration;
use Boctulus\Simplerest\Core\Libs\Config;
use Boctulus\Simplerest\Core\Libs\DB;
use Boctulus\Simplerest\Core\Libs\Files;
use Boctulus\Simplerest\Core\Libs\PHPLexicalAnalyzer;
use Boctulus\Simplerest\Core\Libs\Schema;
use Boctulus\Simplerest\Core\Libs\StdOut;
use Boctulus\Simplerest\Core\Libs\Strings;

abstract class BaseMigrationsCommand extends BaseCommand
{
    public string $group = 'migrations';

    protected function toOpt(array $parsed): array
    {
        $opt = [];
        foreach ($parsed['_positional'] ?? [] as $v) {
            $opt[] = $v;
        }
        foreach ($parsed as $key => $value) {
            if (str_starts_with($key, '_')) continue;
            if ($value === true) {
                $opt[] = "--{$key}";
            } elseif ($value !== false && $value !== null) {
                $key_dashed = str_replace('_', '-', $key);
                $opt[] = "--{$key_dashed}={$value}";
            }
        }
        return $opt;
    }

    protected function doMake(...$opt)
    {
        require_once __DIR__ . '/../make/MigrationCommand.php';
        return (new MakeMigrationFileCommand())->migration(...$opt);
    }

    protected function doMigrate(...$opt)
    {
        $filenames = [];
        $file_opt  = false;
        $dir_opt   = false;
        $to_db     = null;
        $steps     = PHP_INT_MAX;
        $skip      = 0;
        $retry     = false;
        $ignore    = null;
        $fresh     = false;
        $make      = false;
        $debug     = false;

        $path = MIGRATIONS_PATH . DIRECTORY_SEPARATOR;

        StdOut::showResponse();

        foreach ($opt as $o) {
            if (preg_match('/^--to[=|:](.*)$/', $o, $matches)) {
                $match = $matches[1];
                if (!preg_match('/^([a-z0-9_-]+)$/i', $match, $matches)) {
                    throw new \InvalidArgumentException("Invalid identifier '{$match}' for tenant id");
                }
                $to_db = $matches[1];
                if (!DB::connectionExists($to_db)) {
                    throw new \InvalidArgumentException("Connection '$to_db' is not registered in db_connections");
                }
            }

            if ('--retry' == $o || 'retry' == $o || '--force' == $o || 'force' == $o) {
                $retry = true;
            }

            if ('--ignore' == $o) {
                $ignore = true;
            }

            if ('--fresh' == $o) {
                $fresh = true;
            }

            if ('--debug' == $o) {
                $debug = true;
            }

            if (preg_match('/^--make[=|:](.*)$/', $o, $matches)) {
                $make = $matches[1];
            }

            if (Strings::startsWith('--file=', $o)) {
                $file_opt = true;
                $_f = substr($o, 7);

                if (Files::isAbsolutePath($_f)) {
                    $path = Files::getDir($_f);
                    $_f = basename($_f);
                } else {
                    if (Strings::contains(DIRECTORY_SEPARATOR, $_f) || Strings::contains('/', $_f)) {
                        $_f = str_replace('/', DIRECTORY_SEPARATOR, $_f);
                        $fr = explode(DIRECTORY_SEPARATOR, $_f);
                        $_f = $fr[count($fr) - 1];
                        unset($fr[count($fr) - 1]);
                        $path = implode(DIRECTORY_SEPARATOR, $fr) . DIRECTORY_SEPARATOR;

                        if (!Strings::startsWith(DIRECTORY_SEPARATOR, $path)) {
                            $path = MIGRATIONS_PATH . DIRECTORY_SEPARATOR . $path;
                        }
                    }
                }

                $path = str_replace('//', '/', $path);
                $filenames = [$_f];
            }

            if (Strings::startsWith('--step=', $o)) {
                $steps = Strings::slice($o, '/^--step=([0-9]+)$/');
            }

            if (Strings::startsWith('--skip=', $o)) {
                $skip = Strings::slice($o, '/^--skip=([0-9]+)$/');
            }

            if (Strings::startsWith('--folder=', $o)) {
                $o = str_replace('--folder=', '--dir=', $o);
            }

            if (Strings::startsWith('--dir=', $o)) {
                $dir_opt = true;
                $_dir    = substr($o, 6);

                if (Files::isAbsolutePath($_dir)) {
                    $path = $_dir;
                } else {
                    $path = MIGRATIONS_PATH . $_dir;
                }

                if (!is_dir($path)) {
                    throw new \Exception("Directory $path not found");
                }
            }
        }

        if (!$file_opt) {
            foreach (new \DirectoryIterator($path) as $fileInfo) {
                if ($fileInfo->isDot() || $fileInfo->isDir()) continue;
                $filename = $fileInfo->getFilename();
                if (Strings::startsWith('.', $filename) || $fileInfo->getExtension() != 'php') continue;
                $filenames[] = $filename;
            }
            sort($filenames);
        } else {
            if ($ignore) {
                $filename = $filenames[0];
                DB::getDefaultConnection();

                if (Schema::hasTable('migrations')) {
                    $normalized_migrations_path = rtrim(str_replace('\\', '/', realpath(MIGRATIONS_PATH)), '/');
                    $normalized_current_path    = rtrim(str_replace('\\', '/', realpath($path)), '/');

                    if (strpos($normalized_current_path, $normalized_migrations_path) === 0) {
                        $relative_path = substr($normalized_current_path, strlen($normalized_migrations_path));
                        $relative_path = ltrim($relative_path, '/');
                    } else {
                        $relative_path = $normalized_current_path;
                    }

                    $data = ['filename' => $filename, 'path' => !empty($relative_path) ? $relative_path : null];
                    if ($to_db != 'default') {
                        $data['db'] = $to_db;
                    }

                    $ok = table('migrations')->create($data);
                    if ($ok) {
                        StdOut::print("Migration file '$filename' was marked as ignored");
                        return;
                    }
                    StdOut::print("Error trying to ignore file '$filename'");
                    return;
                }
            }
        }

        $cnt = min($steps, count($filenames));

        get_default_connection();

        if ($fresh) {
            $this->doFresh(...$opt);
        }

        if (!Schema::hasTable('migrations')) {
            $filename_mg = '0000_00_00_00000000_migrations.php';
            $path_mg     = MIGRATIONS_PATH;

            if (!file_exists(MIGRATIONS_PATH . $filename_mg)) {
                StdOut::print("$filename_mg not found");
            }

            $full_path_mg  = str_replace('//', '/', $path_mg . '/' . $filename_mg);
            require_once $full_path_mg;
            $class_name_mg = PHPLexicalAnalyzer::getClassNameByFileName($full_path_mg);

            if (!class_exists($class_name_mg)) {
                throw new \Exception("Class '$class_name_mg' not found in $filename_mg");
            }

            StdOut::print("Migrating '$filename_mg'\r\n");
            try {
                DB::disableForeignKeyConstraints();
                (new $class_name_mg())->up();
            } finally {
                DB::enableForeignKeyConstraints();
            }

            StdOut::print("Migrated  '$filename_mg' --ok\r\n");
            get_default_connection();
            table('migrations')->create(['filename' => $filename_mg]);
        }

        $ix      = 0;
        $skipped = 0;

        foreach ($filenames as $filename) {
            if (!$retry) {
                $exists = table('migrations')
                    ->where(['filename' => $filename])
                    ->when(
                        $to_db != null,
                        function ($q) use ($to_db) { $q->where(['db', $to_db]); },
                        function ($q) { $q->whereNull('db'); }
                    )
                    ->exists();

                if ($exists) {
                    if ($to_db !== null) {
                        StdOut::print("Already migrated for DB=$to_db. Skipping '$filename'\r\n");
                    }
                    continue;
                }
            }

            if ($ix >= $cnt) break;

            if (!empty($skip) && $skipped < $skip) {
                $skipped++;
                continue;
            }

            $full_path = $path . '/' . trim($filename);
            $full_path = realpath($full_path);
            $migration = include $full_path;

            $simulation = false;
            if (in_array('--simulate', $opt) || in_array('--simulation', $opt) || in_array('simulate', $opt) || in_array('simulation', $opt)) {
                $simulation = true;
                $debug      = true;
            }

            if ($debug) {
                dd("Processing: $full_path [...]");
            }

            if ($migration instanceof IMigration) {
                $migrationInstance = $migration;
            } else {
                $class_name = PHPLexicalAnalyzer::getClassNameByFileName($full_path);
                if (!class_exists($class_name)) {
                    throw new \Exception("Class '$class_name' not found in $filename");
                }
                require_once $full_path;
                $migrationInstance = new $class_name();
            }

            StdOut::print("Migrating '$filename'\r\n");

            if (!method_exists($migrationInstance, 'up')) {
                StdOut::print("Method up() is missing. Impossible to migrate $filename\r\n");
                exit(1);
            }

            if (!$simulation) {
                if (!empty($to_db)) {
                    DB::setConnection($to_db);
                }

                DB::beginTransaction();
                try {
                    DB::disableForeignKeyConstraints();
                    $migrationInstance->up();
                    DB::commit();
                } catch (\Exception $e) {
                    DB::rollback();
                    throw $e;
                } finally {
                    DB::enableForeignKeyConstraints();
                }
            } else {
                StdOut::print("*** This is a Simulation ***" . PHP_EOL);
                $ix++;
                continue;
            }

            StdOut::print("Migrated  '$filename' --ok\r\n");

            get_default_connection();

            $normalized_migrations_path = rtrim(str_replace('\\', '/', realpath(MIGRATIONS_PATH)), '/');
            $normalized_current_path    = rtrim(str_replace('\\', '/', realpath($path)), '/');

            if (strpos($normalized_current_path, $normalized_migrations_path) === 0) {
                $relative_path = substr($normalized_current_path, strlen($normalized_migrations_path));
                $relative_path = ltrim($relative_path, '/');
            } else {
                $relative_path = $normalized_current_path;
            }

            $data = ['filename' => $filename, 'path' => !empty($relative_path) ? $relative_path : null];
            $main = get_default_connection_id();

            if ($to_db != null && $to_db != $main) {
                $data['db'] = $to_db;
            }

            table('migrations')->create($data);
            $ix++;
        }

        DB::enableForeignKeyConstraints();

        if (!empty($make)) {
            require_once __DIR__ . '/../make/MigrationCommand.php';
            $actions = explode(',', $make);
            $cmd = new MakeMigrationFileCommand();
            foreach ($actions as $action) {
                if ($action == 'schema') $cmd->schema('all');
                if ($action == 'model')  $cmd->model('all');
            }
        }
    }

    protected function doRollback(...$opt)
    {
        $path                 = MIGRATIONS_PATH . DIRECTORY_SEPARATOR;
        $file_path_from_option = false;
        $file_explicit_path    = null;
        $steps                 = 1;
        $simulate              = false;

        StdOut::showResponse();

        foreach ($opt as $o) {
            if (isset($opt[0]) && $opt[0] !== null) {
                if (Strings::startsWith('--step=', $o)) {
                    $steps = Strings::slice($o, '/^--step=([0-9]+)$/');
                } elseif (Strings::startsWith('--steps=', $o)) {
                    $steps = Strings::slice($o, '/^--steps=([0-9]+)$/');
                } elseif (Strings::startsWith('--n=', $o)) {
                    $steps = Strings::slice($o, '/^--n=([0-9]+)$/');
                }

                if ($o == '--all') {
                    $steps = PHP_INT_MAX;
                }

                if (preg_match('/^--to[=|:]([a-z][a-z0-9A-Z_]+)$/', $o, $matches)) {
                    $to_db = $matches[1];
                    $main  = Config::get()['db_connection_default'];
                    if ($to_db == $main || $to_db == 'default') {
                        $to_db = '__NULL__';
                    }
                }

                if (Strings::startsWith('--folder=', $o)) {
                    $o = str_replace('--folder=', '--dir=', $o);
                }

                if (Strings::startsWith('--dir=', $o)) {
                    $dir_opt = true;
                    $_dir    = substr($o, 6);

                    if (Files::isAbsolutePath($_dir)) {
                        $path = $_dir;
                    } else {
                        $path = MIGRATIONS_PATH . $_dir;
                    }

                    if (!file_exists($path)) {
                        throw new \Exception("Directory $path not found");
                    }
                }

                if (Strings::startsWith('--file=', $o)) {
                    $file_opt = true;
                    $_f = substr($o, 7);
                    $_f = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $_f);

                    if (Files::isAbsolutePath($_f)) {
                        $file_explicit_path    = dirname($_f);
                        $_f                    = basename($_f);
                        $file_path_from_option = true;
                    } else {
                        if (Strings::contains(DIRECTORY_SEPARATOR, $_f)) {
                            $fr  = explode(DIRECTORY_SEPARATOR, $_f);
                            $_f  = $fr[count($fr) - 1];
                            unset($fr[count($fr) - 1]);
                            $path_from_file = implode(DIRECTORY_SEPARATOR, $fr);

                            if (!Files::isAbsolutePath($path_from_file)) {
                                $file_explicit_path = MIGRATIONS_PATH . DIRECTORY_SEPARATOR . $path_from_file;
                            } else {
                                $file_explicit_path = $path_from_file;
                            }
                            $file_path_from_option = true;
                        }
                    }

                    $filenames = [$_f];
                }

                if (in_array($o, ['--simulate', 'simulate', '--sim', 'simulation', '--simulation'])) {
                    StdOut::print("*** This is a Simulation ***" . PHP_EOL);
                    $simulate = true;
                }
            }
        }

        if (!isset($to_db)) {
            $to_db = '__NULL__';
        } else {
            if ($to_db != '__NULL__') {
                $this->validateConnection($to_db);
            }
        }

        StdOut::print("Rolling back up to $steps migrations\r\n");

        $stored_paths = [];

        if (!isset($filenames)) {
            $m          = (object) table('migrations');
            $migrations = $m
                ->when(
                    $to_db == '__NULL__',
                    function ($q) { $q->whereNull('db'); },
                    function ($q) use ($to_db) { $q->where(['db' => $to_db]); }
                )
                ->orderBy(['id' => 'DESC'])
                ->get();

            $filenames = [];
            foreach ($migrations as $mig) {
                if (isset($dir_opt) && isset($_dir)) {
                    $mig_path       = $mig['path'] ?? '';
                    $normalized_dir = rtrim(str_replace('\\', '/', realpath($_dir) ?: $_dir), '/');
                    $normalized_mig = rtrim(str_replace('\\', '/', $mig_path), '/');

                    if ($normalized_mig !== $normalized_dir && strpos($normalized_mig, $normalized_dir . '/') !== 0) {
                        continue;
                    }
                }

                $filenames[]                   = $mig['filename'];
                $stored_paths[$mig['filename']] = $mig['path'] ?? null;
            }
        } elseif (isset($file_opt) && !$file_path_from_option) {
            $m         = (object) table('migrations');
            $migration = $m
                ->where(['filename' => $filenames[0]])
                ->when(
                    $to_db == '__NULL__',
                    function ($q) { $q->whereNull('db'); },
                    function ($q) use ($to_db) { $q->where(['db' => $to_db]); }
                )
                ->first();

            if ($migration) {
                $stored_paths[$filenames[0]] = $migration['path'] ?? null;
            }
        }

        if (empty($filenames)) return;

        DB::getDefaultConnection();
        DB::disableForeignKeyConstraints();

        $cnt = min($steps, count($filenames));
        for ($i = 0; $i < $cnt; $i++) {
            $filename = $filenames[$i];

            if ($file_explicit_path !== null) {
                $path = $file_explicit_path;
            } elseif (isset($_dir)) {
                $path = Files::isAbsolutePath($_dir) ? $_dir : MIGRATIONS_PATH . DIRECTORY_SEPARATOR . $_dir;
            } elseif (isset($file_opt) && !$file_path_from_option) {
                $path = MIGRATIONS_PATH;
            } elseif (!$file_path_from_option && isset($stored_paths[$filename]) && !empty($stored_paths[$filename])) {
                $stored_path = $stored_paths[$filename];
                $path = Files::isAbsolutePath($stored_path) ? $stored_path : MIGRATIONS_PATH . DIRECTORY_SEPARATOR . $stored_path;
            } elseif (!$file_path_from_option) {
                $path = MIGRATIONS_PATH;
            }

            if (!str_ends_with($path, DIRECTORY_SEPARATOR)) {
                $path .= DIRECTORY_SEPARATOR;
            }

            $full_path = $path . $filename;

            if (!file_exists($full_path)) {
                StdOut::print("File '$full_path' not found");
                exit;
            }

            $migration = include $full_path;

            if ($migration instanceof IMigration) {
                $migrationInstance = $migration;
            } else {
                $class_name = PHPLexicalAnalyzer::getClassNameByFileName($full_path);
                if (!class_exists($class_name)) {
                    throw new \Exception("Class '$class_name' not found in $filename");
                }
                require_once $full_path;
                $migrationInstance = new $class_name();
            }

            StdOut::print("Rolling back '$filename'\r\n");

            if (!method_exists($migrationInstance, 'down')) {
                StdOut::print("Method down() is missing. Impossible to rollback $filename\r\n");
                exit(1);
            }

            if (!$simulate) {
                if (!empty($to_db) && $to_db != '__NULL__') {
                    DB::setConnection($to_db);
                }

                try {
                    DB::disableForeignKeyConstraints();
                    $migrationInstance->down();
                } finally {
                    DB::enableForeignKeyConstraints();
                }

                DB::getDefaultConnection();

                $aff = table('migrations')
                    ->when(
                        $to_db == '__NULL__',
                        function ($q) { $q->whereNull('db'); },
                        function ($q) use ($to_db) { $q->where(['db' => $to_db]); }
                    )
                    ->where(['filename' => $filename])
                    ->delete();

                if (empty($aff)) {
                    StdOut::print("There was a problem rolling back $filename.\r\n");
                }
            }

            if (!empty($aff)) {
                StdOut::print("Rolled back  '$filename' --ok\r\n");
            }
        }

        DB::enableForeignKeyConstraints();
    }

    protected function doClear(...$opt)
    {
        foreach ($opt as $o) {
            if (preg_match('/^--to[=|:]([a-z][a-z0-9A-Z_]+)$/', $o, $matches)) {
                $to_db = $matches[1];
            }
        }

        if (isset($to_db)) {
            $this->validateConnection($to_db);
        }

        DB::getDefaultConnection();

        $affected = table('migrations')
            ->when(
                isset($to_db) && $to_db != DB::getDefaultConnectionId(),
                function ($q) use ($to_db) { $q->where(['db' => $to_db]); },
                function ($q) { $q->whereRaw('1'); }
            )
            ->delete();

        StdOut::print("$affected entries were cleared from migrations table for database `" . ($to_db ?? 'default') . "`\r\n");
    }

    protected function doReset(...$opt)
    {
        $opt[] = '--all';
        $this->doRollback(...$opt);
    }

    protected function doRedo(...$opt)
    {
        $this->doRollback(...$opt);
        $this->doMigrate(...$opt);
    }

    protected function doFresh(...$opt)
    {
        $force   = false;
        $migrate = false;
        $_f      = null;
        $_dir    = null;

        StdOut::showResponse();

        foreach ($opt as $o) {
            if ($o == '--force') { $force = true; continue; }
            if ($o == '--migrate') { $migrate = true; continue; }

            if (preg_match('/^--to[=|:]([a-z][a-z0-9A-Z_]+)$/', $o, $matches)) {
                $to_db = $matches[1];
            }

            if (Strings::startsWith('--file=', $o)) {
                $_f = substr($o, 7);
            }

            if (Strings::startsWith('--folder=', $o)) {
                $o = str_replace('--folder=', '--dir=', $o);
            }

            if (Strings::startsWith('--dir=', $o)) {
                $_dir = substr($o, 6);
            }
        }

        $this->validateConnection($to_db ?? null, true);

        if (!$force) {
            StdOut::print("fresh: this method is destructive. " .
                (!isset($_f) ? "Every table for '$to_db' will be dropped." : '') .
                " Please use option --force if you want to proceed.\r\n");
            exit;
        }

        if (!is_null($_f) || !is_null($_dir)) {
            $arr[] = "--to=$to_db";
            if ($_f !== null)   $arr[] = "--file=$_f";
            if ($_dir !== null) $arr[] = "--dir=$_dir";
            return $this->doRedo(...$arr);
        }

        $tables  = Schema::getTables($to_db);
        $dropped = [];

        if (empty($tables)) {
            if ($migrate) $this->doMigrate(...$opt);
            return;
        }

        $delete_migrations_tb = false;
        if ($ix = array_search('migrations', $tables)) {
            unset($tables[$ix]);
            $delete_migrations_tb = true;
        }

        try {
            Schema::FKcheck(false);
            foreach ($tables as $table) {
                StdOut::print("Dropping table '$table'\r\n");
                $res = DB::statement("DROP TABLE IF EXISTS `$table`;");
                if ($res) {
                    StdOut::print("Dropped table  '$table' --ok\r\n");
                    $dropped[] = $table;
                } else {
                    StdOut::print("Dropped table failure for '$table'\r\n");
                }
            }

            $this->doClear("--to=$to_db");

            if ($delete_migrations_tb) {
                StdOut::hideResponse();
                StdOut::print("Dropping table 'migrations'\r\n");
                $res = DB::statement("DROP TABLE IF EXISTS `migrations`;");
                if ($res) {
                    StdOut::print("Dropped table  'migrations' --ok\r\n");
                    $dropped[] = 'migrations';
                } else {
                    StdOut::print("Dropped table failure for 'migrations'\r\n");
                }
            }

            if ($migrate) {
                StdOut::showResponse();
                $this->doMigrate(...$opt);
            }
        } catch (\PDOException $e) {
            log_error($e->getMessage());
            throw $e;
        } finally {
            Schema::FKcheck(true);
            StdOut::showResponse();
        }
    }

    protected function doRefresh(...$opt)
    {
        $this->doReset(...$opt);
        $this->doMigrate(...$opt);
    }

    protected function doList(...$opt)
    {
        $dir      = null;
        $table    = null;
        $contains = null;

        foreach ($opt as $o) {
            if (Strings::startsWith('--dir=', $o))      $dir      = substr($o, 6);
            elseif (Strings::startsWith('--table=', $o))    $table    = substr($o, 8);
            elseif (Strings::startsWith('--contains=', $o)) $contains = substr($o, 11);
        }

        $path = $dir ? MIGRATIONS_PATH . DIRECTORY_SEPARATOR . $dir : MIGRATIONS_PATH;

        if (!is_dir($path)) {
            throw new \Exception("Directory $path not found");
        }

        $files = [];
        foreach (new \DirectoryIterator($path) as $fileInfo) {
            if ($fileInfo->isDot() || $fileInfo->isDir()) continue;
            $filename = $fileInfo->getFilename();
            if (Strings::startsWith('.', $filename) || $fileInfo->getExtension() != 'php') continue;
            $files[] = $filename;
        }

        $migrations = [];
        foreach ($files as $filename) {
            $full_path    = $path . DIRECTORY_SEPARATOR . $filename;
            $class_name   = PHPLexicalAnalyzer::getClassNameByFileName($full_path);
            $file_content = file_get_contents($full_path);

            $table_value = null;
            if (preg_match('/\$table\s*=\s*[\'"]([^\'"]+)[\'"]/', $file_content, $matches)) {
                $table_value = $matches[1];
            }

            $migrations[] = ['filename' => $filename, 'class_name' => $class_name, 'table' => $table_value];
        }

        $filtered = $migrations;

        if ($table !== null) {
            $filtered = array_filter($filtered, function ($mig) use ($table) {
                return $mig['table'] !== null && (str_contains($mig['table'], $table) || $mig['table'] === $table);
            });
        }

        if ($contains !== null) {
            $filtered = array_filter($filtered, function ($mig) use ($contains) {
                return str_contains($mig['filename'], $contains) ||
                    str_contains($mig['class_name'], $contains) ||
                    ($mig['table'] !== null && str_contains($mig['table'], $contains));
            });
        }

        if (empty($filtered)) {
            echo "No migrations found matching the criteria.\n";
        } else {
            foreach ($filtered as $mig) {
                $table_str = $mig['table'] ? " (Table: {$mig['table']})" : '';
                echo "{$mig['filename']} - {$mig['class_name']}{$table_str}\n";
            }
        }
    }

    protected function doMigrateModule(...$opt)
    {
        if (count($opt) < 1) {
            StdOut::print("\nError: Module name is required.\n");
            StdOut::print("Usage: php com migrations migrate-module <module_name> [options]\n\n");
            return;
        }

        $module_name = array_shift($opt);
        $module_path = MODULES_PATH . $module_name . DIRECTORY_SEPARATOR . 'database' . DIRECTORY_SEPARATOR . 'migrations';
        $module_base = MODULES_PATH . $module_name;

        if (!file_exists($module_base)) {
            StdOut::print("\nError: Module '$module_name' does not exist at: $module_base\n\n");
            return;
        }

        if (!file_exists($module_path)) {
            StdOut::print("\nError: No migrations directory found for module '$module_name' at: $module_path\n\n");
            return;
        }

        $opt[] = '--dir=' . $module_path;
        StdOut::print("\nRunning migrations for module '$module_name'...\n\n");
        $this->doMigrate(...$opt);
    }

    protected function doMigratePackage(...$opt)
    {
        if (count($opt) < 1) {
            StdOut::print("\nError: Package name is required.\n");
            StdOut::print("Usage: php com migrations migrate-package <package_name> [options]\n\n");
            return;
        }

        $package_name = array_shift($opt);

        if (!preg_match('/^[a-z0-9_-]+$/', $package_name)) {
            StdOut::print("\nError: Invalid package name '$package_name'.\n\n");
            return;
        }

        $package_path = PACKAGES_PATH . "boctulus" . DIRECTORY_SEPARATOR . $package_name . DIRECTORY_SEPARATOR . 'database' . DIRECTORY_SEPARATOR . 'migrations';
        $package_base = PACKAGES_PATH . "boctulus" . DIRECTORY_SEPARATOR . $package_name;

        if (!file_exists($package_base)) {
            StdOut::print("\nError: Package 'boctulus/$package_name' does not exist at: $package_base\n\n");
            return;
        }

        if (!file_exists($package_path)) {
            StdOut::print("\nError: No migrations directory found for package 'boctulus/$package_name' at: $package_path\n\n");
            return;
        }

        $opt[] = '--dir=' . $package_path;
        StdOut::print("\nRunning migrations for package 'boctulus/$package_name'...\n\n");
        $this->doMigrate(...$opt);
    }

    protected function doRollbackModule(...$opt)
    {
        if (count($opt) < 1) {
            StdOut::print("\nError: Module name is required.\n");
            StdOut::print("Usage: php com migrations rollback-module <module_name> [options]\n\n");
            return;
        }

        $module_name = array_shift($opt);
        $module_path = MODULES_PATH . $module_name . DIRECTORY_SEPARATOR . 'database' . DIRECTORY_SEPARATOR . 'migrations';
        $module_base = MODULES_PATH . $module_name;

        if (!file_exists($module_base)) {
            StdOut::print("\nError: Module '$module_name' does not exist at: $module_base\n\n");
            return;
        }

        if (!file_exists($module_path)) {
            StdOut::print("\nError: No migrations directory found for module '$module_name' at: $module_path\n\n");
            return;
        }

        $opt[] = '--dir=' . $module_path;
        StdOut::print("\nRolling back migrations for module '$module_name'...\n\n");
        $this->doRollback(...$opt);
    }

    protected function doRollbackPackage(...$opt)
    {
        if (count($opt) < 1) {
            StdOut::print("\nError: Package name is required.\n");
            StdOut::print("Usage: php com migrations rollback-package <package_name> [options]\n\n");
            return;
        }

        $package_name = array_shift($opt);

        if (!preg_match('/^[a-z0-9_-]+$/', $package_name)) {
            StdOut::print("\nError: Invalid package name '$package_name'.\n\n");
            return;
        }

        $package_path = PACKAGES_PATH . "boctulus" . DIRECTORY_SEPARATOR . $package_name . DIRECTORY_SEPARATOR . 'database' . DIRECTORY_SEPARATOR . 'migrations';
        $package_base = PACKAGES_PATH . "boctulus" . DIRECTORY_SEPARATOR . $package_name;

        if (!file_exists($package_base)) {
            StdOut::print("\nError: Package 'boctulus/$package_name' does not exist at: $package_base\n\n");
            return;
        }

        if (!file_exists($package_path)) {
            StdOut::print("\nError: No migrations directory found for package 'boctulus/$package_name' at: $package_path\n\n");
            return;
        }

        $opt[] = '--dir=' . $package_path;
        StdOut::print("\nRolling back migrations for package 'boctulus/$package_name'...\n\n");
        $this->doRollback(...$opt);
    }

    protected function doRefreshModule(...$opt)
    {
        if (count($opt) < 1) {
            StdOut::print("\nError: Module name is required.\n");
            StdOut::print("Usage: php com migrations refresh-module <module_name> [options]\n\n");
            return;
        }

        $module_name = array_shift($opt);
        $module_path = MODULES_PATH . $module_name . DIRECTORY_SEPARATOR . 'database' . DIRECTORY_SEPARATOR . 'migrations';
        $module_base = MODULES_PATH . $module_name;

        if (!file_exists($module_base)) {
            StdOut::print("\nError: Module '$module_name' does not exist at: $module_base\n\n");
            return;
        }

        if (!file_exists($module_path)) {
            StdOut::print("\nError: No migrations directory found for module '$module_name' at: $module_path\n\n");
            return;
        }

        $opt[] = '--dir=' . $module_path;
        StdOut::print("\nRefreshing migrations for module '$module_name' (rollback all + migrate)...\n\n");

        $rollback_opt   = array_merge($opt, ['--all']);
        $this->doRollback(...$rollback_opt);
        StdOut::print("\n");
        $this->doMigrate(...$opt);
    }

    protected function doRefreshPackage(...$opt)
    {
        if (count($opt) < 1) {
            StdOut::print("\nError: Package name is required.\n");
            StdOut::print("Usage: php com migrations refresh-package <package_name> [options]\n\n");
            return;
        }

        $package_name = array_shift($opt);

        if (!preg_match('/^[a-z0-9_-]+$/', $package_name)) {
            StdOut::print("\nError: Invalid package name '$package_name'.\n\n");
            return;
        }

        $package_path = PACKAGES_PATH . "boctulus" . DIRECTORY_SEPARATOR . $package_name . DIRECTORY_SEPARATOR . 'database' . DIRECTORY_SEPARATOR . 'migrations';
        $package_base = PACKAGES_PATH . "boctulus" . DIRECTORY_SEPARATOR . $package_name;

        if (!file_exists($package_base)) {
            StdOut::print("\nError: Package 'boctulus/$package_name' does not exist at: $package_base\n\n");
            return;
        }

        if (!file_exists($package_path)) {
            StdOut::print("\nError: No migrations directory found for package 'boctulus/$package_name' at: $package_path\n\n");
            return;
        }

        $opt[] = '--dir=' . $package_path;
        StdOut::print("\nRefreshing migrations for package 'boctulus/$package_name' (rollback all + migrate)...\n\n");

        $rollback_opt = array_merge($opt, ['--all']);
        $this->doRollback(...$rollback_opt);
        StdOut::print("\n");
        $this->doMigrate(...$opt);
    }

    private function validateConnection($to_db, $required = false)
    {
        if (!isset($to_db)) {
            if ($required) {
                throw new \InvalidArgumentException("--to= is not optional");
            }
            return;
        }

        if (!DB::connectionExists($to_db)) {
            throw new \InvalidArgumentException("Connection '$to_db' is not registered in db_connections");
        }
    }
}
