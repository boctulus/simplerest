<?php

namespace simplerest\core\libs;

class DatabaseBackup
{      
    /*
        https://chatgpt.com/c/38ee451b-f84d-4095-be78-b1ef839999ac

        Ej:

        DatabaseBackup::export('localhost', 'dydcolombiacom', 'LFmkvV-,~#oS', 'dydpage', Constants::ETC_PATH . 'backup.sql');
    */    
    public static function export(string $host, string $username, string $password, string $databaseName, string $outputFile)
    {
        // Construir el comando mysqldump
        $command = sprintf(
            'mysqldump -h %s -u %s -p%s %s > %s',
            escapeshellarg($host),
            escapeshellarg($username),
            escapeshellarg($password),
            escapeshellarg($databaseName),
            escapeshellarg($outputFile)
        );

        // Ejecutar el comando
        return shell_exec($command);
    }

    /*
        Crear el archivo .my.cnf:

        [client]
        user = your_username
        password = your_password

        chmod 600 ~/.my.cnf

        y ...

        DatabaseBackup::export('your_database', 'backup.sql');

        @param string $cnf_file_path ruta al archivo my.cnf
    */
    public static function exportUsingCnf(string $cnf_file_path, string $databaseName, string $outputFile): void
    {
        // Construir el comando mysqldump utilizando el archivo de configuración .my.cnf
        $command = sprintf(
            "mysqldump --defaults-file=$cnf_file_path %s > %s",
            escapeshellarg($databaseName),
            escapeshellarg($outputFile)
        );

        // Ejecutar el comando
        shell_exec($command);
    }

    /*
        MySQL permite realizar exportaciones directamente desde el cliente de MySQL utilizando el comando SELECT INTO OUTFILE para exportar los datos de una tabla a un archivo. 
        
        Sin embargo, este comando tiene limitaciones y no es tan completo como mysqldump. SELECT INTO OUTFILE solo exporta los datos de una tabla y no incluye la estructura de la base de datos ni otras características como triggers, procedures, etc.
    */
    public static function exportTableAsCSV(string $tableName)
    {        
        $pdo = DB::getConnection();

        $stmt           = $pdo->query("SHOW VARIABLES LIKE 'secure_file_priv'");
        $result         = $stmt->fetch(\PDO::FETCH_ASSOC);
        $outputFilePath = $result['Value'];

        $file           = Files::normalize($outputFilePath, '/') . "$tableName.csv";

        if (file_exists($file)){
            Files::delete($file);
        }

        $ret = '\n';

        $sql = "SELECT *
        INTO OUTFILE '$file'
        FIELDS TERMINATED BY ',' OPTIONALLY ENCLOSED BY '\"'
        LINES TERMINATED BY '$ret'
        FROM $tableName";

        // dd($sql);

        // Ejecutar el comando
        $pdo->exec($sql);

        return $file;
    }
}


