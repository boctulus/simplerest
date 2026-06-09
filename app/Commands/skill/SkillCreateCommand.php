<?php

require_once __DIR__ . '/BaseSkillCommand.php';

class SkillCreateCommand extends BaseSkillCommand
{
    public function __construct()
    {
        parent::__construct();
        $this->command     = 'create';
        $this->description = 'Crea un archivo SKILL.md en el directorio especificado';
        $this->aliases     = ['new', 'add'];
        $this->examples    = [
            'php com skill create "My New Skill"',
            'php com skill create "Another Skill" --dir=claude',
            'php com skill create "Advanced Skill" --dir=.agent',
        ];
    }

    public static function config(): array
    {
        return [
            'required' => [],
            'optional' => ['dir', 'force'],
            'flags'    => ['force'],
            'options'  => [
                'dir'   => ['describe' => 'Directorio donde crear el skill (por defecto .agent)', 'default' => '.agent'],
                'force' => ['describe' => 'Sobrescribir el skill si ya existe'],
            ],
        ];
    }

    public function execute(array $parsed): void
    {
        $skillName = $parsed['_positional'][0] ?? null;

        if ($skillName === null || trim($skillName) === '') {
            $this->log('El nombre del skill es requerido', 'error');
            $this->showUsage();
            return;
        }

        $this->log('Iniciando creacion de skill...', 'info');

        $skillName = trim($skillName);
        $directory = $this->opt($parsed, 'dir', '.agent');
        $force     = $this->opt($parsed, 'force', false);

        if (!str_starts_with($directory, '.')) {
            $directory = '.' . $directory;
        }

        $kebabCaseName = $this->toKebabCase($skillName);

        $skillDir      = $directory . DIRECTORY_SEPARATOR . 'skills' . DIRECTORY_SEPARATOR . $kebabCaseName;
        $skillFilePath = $skillDir . DIRECTORY_SEPARATOR . 'SKILL.md';

        if (is_file($skillFilePath) && !$force) {
            $this->log("El skill ya existe: {$skillFilePath}", 'error');
            $this->log('Usa --force para sobrescribirlo', 'info');
            return;
        }

        $this->ensureDirectoryExists($skillDir);

        $skillContent = "---\n"
            . "name: {$kebabCaseName}\n"
            . "description:\n"
            . "---\n"
            . "\n"
            . "# SKILL_DEFINITION: {$skillName}\n";

        $this->writeSkillFile($skillFilePath, $skillContent, $force);

        $this->log("Skill creado exitosamente!", 'success');
        $this->log("Ruta: {$skillFilePath}", 'info');
        $this->log("Nombre: {$skillName}", 'info');
        $this->log("Nombre en kebab-case: {$kebabCaseName}", 'info');
        $this->log("Directorio: {$directory}", 'info');
    }
}
