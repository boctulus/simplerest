<?php

require_once __DIR__ . '/BaseTodoCommand.php';

class TodoArchiveCommand extends BaseTodoCommand
{
    public function __construct()
    {
        $this->command     = 'archive';
        $this->description = 'Archiva una tarea moviéndola a docs/to-do/archived/';
        $this->aliases     = ['arch'];
        $this->examples    = [
            'php com todo archive --file=dominio-com.md',
            'php com todo archive --file=done/mi-tarea.md',
        ];
    }

    public static function config(): array
    {
        return [
            'required' => ['file'],
            'optional' => [],
            'flags'    => [],
            'options'  => [
                'file' => ['describe' => 'Ruta relativa al archivo en docs/to-do/'],
            ],
        ];
    }

    public function execute(array $parsed): void
    {
        $fileRef = $this->opt($parsed, 'file');
        $absPath = $this->resolveFilePath($fileRef);

        if ($absPath === null) {
            $this->log("Archivo no encontrado: $fileRef", 'error');
            return;
        }

        $base    = $this->getTodoDir();
        $archDir = $base . '/archived';

        if (!is_dir($archDir)) {
            mkdir($archDir, 0777, true);
        }

        $filename = basename($absPath);
        $dest     = $archDir . '/' . $filename;

        if (file_exists($dest)) {
            $this->log("Ya existe un archivo con ese nombre en archived/", 'error');
            return;
        }

        rename($absPath, $dest);
        $this->log("Tarea archivada: docs/to-do/archived/$filename", 'success');
    }
}
