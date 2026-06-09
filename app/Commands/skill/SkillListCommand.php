<?php

require_once __DIR__ . '/BaseSkillCommand.php';

class SkillListCommand extends BaseSkillCommand
{
    public function __construct()
    {
        parent::__construct();
        $this->command     = 'list';
        $this->description = 'Lista los skills disponibles en el directorio del agente';
        $this->aliases     = ['ls', 'show'];
        $this->examples    = [
            'php com skill list',
            'php com skill list --agent=claude',
            'php com skill list --agent=qwen',
            'php com skill list --detailed',
            'php com skill list --agent=claude --detailed',
        ];
    }

    public static function config(): array
    {
        return [
            'required' => [],
            'optional' => ['agent', 'detailed'],
            'flags'    => ['detailed'],
            'options'  => [
                'agent'    => ['describe' => 'Directorio del agente (agent, claude, qwen)', 'default' => 'agent'],
                'detailed' => ['describe' => 'Mostrar información detallada (descripción y tamaño)'],
            ],
        ];
    }

    public function execute(array $parsed): void
    {
        $agent    = $this->opt($parsed, 'agent', 'agent');
        $detailed = $this->opt($parsed, 'detailed', false);

        $agentDir = $this->getAgentDir($agent);
        $skills   = $this->getSkillsDirectory($agentDir);

        if (count($skills) === 0) {
            $this->log("No se encontraron skills en {$agentDir}/skills/", 'warning');
            return;
        }

        $this->log("Skills en `{$agentDir}/skills/` (" . count($skills) . " total):", 'success');
        echo PHP_EOL;

        if ($detailed) {
            foreach ($skills as $skill) {
                $skillFilePath = $skill['path'] . DIRECTORY_SEPARATOR . 'SKILL.md';
                $frontmatter   = $this->parseSkillFrontmatter($skillFilePath);
                $sizeKB        = $this->getFileSizeKB($skillFilePath);

                $skillName   = $frontmatter['name'] ?? $skill['name'];
                $description = $frontmatter['description'] ?? 'Sin descripción';
                $sizeStr     = $sizeKB !== null ? "{$sizeKB} KB" : 'N/A';

                echo "  {$skillName}" . PHP_EOL;
                echo "     Descripcion: {$description}" . PHP_EOL;
                echo "     Tamano: {$sizeStr}" . PHP_EOL;
                echo PHP_EOL;
            }
        } else {
            foreach ($skills as $skill) {
                echo "  {$skill['name']}" . PHP_EOL;
            }
            echo PHP_EOL;
        }
    }
}
