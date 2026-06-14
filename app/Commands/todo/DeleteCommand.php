<?php

require_once __DIR__ . '/BaseTodoCommand.php';

class TodoDeleteCommand extends BaseTodoCommand
{
    public function __construct()
    {
        $this->command     = 'delete';
        $this->description = 'Mueve una tarea a la papelera (docs/to-do/abandoned/)';
        $this->aliases     = ['rm', 'remove'];
        $this->examples    = [
            'php com todo delete --file=dominio-com.md',
            'php com todo delete --file=in-progress/mi-tarea.md',
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
            $bareName = basename($fileRef);
            $absPath = $this->detectStateFolder($bareName, $currentFolder);
        }

        if ($absPath === null) {
            $this->log("Archivo no encontrado: $fileRef", 'error');
            return;
        }

        $base       = $this->getTodoDir();
        $abandonDir = $base . '/abandoned';

        if (!is_dir($abandonDir)) {
            mkdir($abandonDir, 0777, true);
        }

        $filename = basename($absPath);
        $dest     = $abandonDir . '/' . $filename;

        if (file_exists($dest)) {
            $this->log("Ya existe un archivo con ese nombre en abandoned/", 'error');
            return;
        }

        rename($absPath, $dest);
        $this->log("Tarea movida a papelera: docs/to-do/abandoned/$filename", 'success');
    }
}
