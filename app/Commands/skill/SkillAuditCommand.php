<?php

require_once __DIR__ . '/BaseSkillCommand.php';

use Boctulus\Simplerest\Commands\Skill\Lib\CircularDependencyDetector;

class SkillAuditCommand extends BaseSkillCommand
{
    public function __construct()
    {
        parent::__construct();
        $this->command     = 'audit';
        $this->description = 'Audita los skills para detectar problemas (vacios, sin header, sin descripcion, name mismatches, referencias circulares)';
        $this->aliases     = [];
        $this->examples    = [
            'php com skill audit',
            'php com skill audit --agent=claude',
            'php com skill audit --min-size=100',
        ];
    }

    public static function config(): array
    {
        return [
            'required' => [],
            'optional' => ['agent', 'min-size'],
            'flags'    => [],
            'options'  => [
                'agent'    => ['describe' => 'Directorio del agente a auditar (agent, claude, qwen, o "all" para todos)', 'default' => 'all'],
                'min-size' => ['describe' => 'Tamano minimo en bytes para considerar un skill como sospechoso de estar vacio', 'default' => 200],
            ],
        ];
    }

    public function execute(array $parsed): void
    {
        $agentFilter = $this->opt($parsed, 'agent', 'all');
        $minSizeBytes = (int) $this->opt($parsed, 'min_size', 200);

        $agentsToAudit = $agentFilter === 'all'
            ? ['agent', 'claude', 'qwen']
            : [$agentFilter];

        $issues = [
            'tooSmall'           => [],
            'missingHeader'      => [],
            'missingDescription' => [],
            'nameMismatch'       => [],
            'circularDeps'       => [],
        ];

        $totalSkills = 0;

        foreach ($agentsToAudit as $agent) {
            $agentDir = $this->getAgentDir($agent);
            $skills   = $this->getSkillsDirectory($agentDir);
            $totalSkills += count($skills);

            foreach ($skills as $skill) {
                $skillFilePath = $skill['path'] . DIRECTORY_SEPARATOR . 'SKILL.md';
                $frontmatter   = $this->parseSkillFrontmatter($skillFilePath);

                $fileSize = is_file($skillFilePath) ? filesize($skillFilePath) : false;

                if ($fileSize !== false && $fileSize < $minSizeBytes) {
                    $issues['tooSmall'][] = [
                        'agent'     => $agent,
                        'skillName' => $skill['name'],
                        'size'      => $fileSize,
                    ];
                }

                if ($frontmatter === null) {
                    $issues['missingHeader'][] = [
                        'agent'     => $agent,
                        'skillName' => $skill['name'],
                    ];
                    continue;
                }

                $desc = $frontmatter['description'] ?? '';
                if ($desc === '' || trim($desc) === '') {
                    $issues['missingDescription'][] = [
                        'agent'     => $agent,
                        'skillName' => $skill['name'],
                    ];
                }

                if (isset($frontmatter['name']) && $frontmatter['name'] !== $skill['name']) {
                    $issues['nameMismatch'][] = [
                        'agent'           => $agent,
                        'skillName'       => $skill['name'],
                        'frontmatterName' => $frontmatter['name'],
                    ];
                }
            }

            $dependencyGraph = $this->buildDependencyGraph($skills);
            $cycleDetection  = CircularDependencyDetector::detectCircularDependencies($dependencyGraph);

            if ($cycleDetection['hasCycle']) {
                $formattedCycles = CircularDependencyDetector::formatCycles($cycleDetection['cycles']);
                foreach ($formattedCycles as $cycle) {
                    $issues['circularDeps'][] = [
                        'agent' => $agent,
                        'cycle' => $cycle,
                    ];
                }
            }
        }

        $this->log("Auditoria completada: {$totalSkills} skills revisados", 'success');
        echo PHP_EOL;

        $hasIssues = false;
        foreach ($issues as $arr) {
            if (count($arr) > 0) {
                $hasIssues = true;
                break;
            }
        }

        if (!$hasIssues) {
            $this->log('No se encontraron problemas!', 'success');
            return;
        }

        if (count($issues['tooSmall']) > 0) {
            $this->log("Skills sospechosos de estar vacios (< {$minSizeBytes} bytes): " . count($issues['tooSmall']), 'warning');
            foreach ($issues['tooSmall'] as $issue) {
                echo "   [{$issue['agent']}] {$issue['skillName']} ({$issue['size']} bytes)" . PHP_EOL;
            }
            echo PHP_EOL;
        }

        if (count($issues['missingHeader']) > 0) {
            $this->log("Skills sin header YAML: " . count($issues['missingHeader']), 'error');
            foreach ($issues['missingHeader'] as $issue) {
                echo "   [{$issue['agent']}] {$issue['skillName']}" . PHP_EOL;
            }
            echo PHP_EOL;
        }

        if (count($issues['missingDescription']) > 0) {
            $this->log("Skills sin descripcion en el header: " . count($issues['missingDescription']), 'warning');
            foreach ($issues['missingDescription'] as $issue) {
                echo "   [{$issue['agent']}] {$issue['skillName']}" . PHP_EOL;
            }
            echo PHP_EOL;
        }

        if (count($issues['nameMismatch']) > 0) {
            $this->log("Skills con name mismatch en el header: " . count($issues['nameMismatch']), 'error');
            foreach ($issues['nameMismatch'] as $issue) {
                echo "   [{$issue['agent']}] {$issue['skillName']} => frontmatter dice: \"{$issue['frontmatterName']}\"" . PHP_EOL;
            }
            echo PHP_EOL;
        }

        if (count($issues['circularDeps']) > 0) {
            $this->log("Skills con referencias circulares: " . count($issues['circularDeps']), 'error');
            foreach ($issues['circularDeps'] as $issue) {
                echo "   [{$issue['agent']}] {$issue['cycle']}" . PHP_EOL;
            }
            echo PHP_EOL;
        }

        $this->log('Resumen de issues:', 'info');
        echo "   Skills demasiado pequenos: " . count($issues['tooSmall']) . PHP_EOL;
        echo "   Skills sin header: " . count($issues['missingHeader']) . PHP_EOL;
        echo "   Skills sin descripcion: " . count($issues['missingDescription']) . PHP_EOL;
        echo "   Skills con name mismatch: " . count($issues['nameMismatch']) . PHP_EOL;
        echo "   Skills con referencias circulares: " . count($issues['circularDeps']) . PHP_EOL;
        echo PHP_EOL;
    }

    private function buildDependencyGraph(array $skills): array
    {
        $graph = [];

        foreach ($skills as $skill) {
            $skillFilePath = $skill['path'] . DIRECTORY_SEPARATOR . 'SKILL.md';
            $content       = $this->readSkillContent($skillFilePath);
            $dependencies  = $this->extractDependencies($content);

            $graph[$skill['name']] = $dependencies;
        }

        return $graph;
    }
}
