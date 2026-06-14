<?php

require_once __DIR__ . '/BaseTodoCommand.php';

class TodoUnarchiveCommand extends BaseTodoCommand
{
    public function __construct()
    {
        $this->command     = 'unarchive';
        $this->description = 'Desarchiva una tarea moviéndola a docs/to-do/in-progress/';
        $this->aliases     = [];
        $this->examples    = [
            'php com todo unarchive --file=dominio-com.md',
            'php com todo unarchive --file=archived/mi-tarea.md',
        ];
    }

    public static function config(): array
    {
        return [
            'required' => ['file'],
            'optional' => [],
            'flags'    => [],
            'options'  => [
                'file' => ['describe' => 'Ruta relativa al archivo en docs/to-do/archived/'],
            ],
        ];
    }

    public function execute(array $parsed): void
    {
        $fileRef = $this->opt($parsed, 'file');

        $absPath = $this->resolveFilePath($fileRef);
        if ($absPath === null) {
            $bareName = basename($fileRef);
            $absPath = $this->detectStateFolder($bareName, $currentFolder);
        }

        if ($absPath === null) {
            $this->log("Archivo no encontrado: $fileRef", 'error');
            return;
        }

        $base    = $this->getTodoDir();
        $ipDir   = $base . '/in-progress';

        if (!is_dir($ipDir)) {
            mkdir($ipDir, 0777, true);
        }

        $filename = basename($absPath);
        $dest     = $ipDir . '/' . $filename;

        if (file_exists($dest)) {
            $this->log("Ya existe un archivo con ese nombre en in-progress/", 'error');
            return;
        }

        rename($absPath, $dest);
        $this->log("Tarea desarchivada: docs/to-do/in-progress/$filename", 'success');
    }
}
