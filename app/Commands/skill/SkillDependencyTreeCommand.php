<?php

require_once __DIR__ . '/BaseSkillCommand.php';

use Boctulus\Simplerest\Commands\Skill\Lib\CircularDependencyDetector;

class SkillDependencyTreeCommand extends BaseSkillCommand
{
    public function __construct()
    {
        parent::__construct();
        $this->command     = 'dependency-tree';
        $this->description = 'Muestra el arbol de dependencias entre skills';
        $this->aliases     = ['deps', 'dependencies'];
        $this->examples    = [
            'php com skill dependency-tree',
            'php com skill dependency-tree --agent=claude',
            'php com skill dependency-tree --skill=view-lifecycle-protocol',
            'php com skill dependency-tree --skill=view-lifecycle-protocol --full',
            'php com skill dependency-tree --agent=qwen --skill=code-quality-protocol',
        ];
    }

    public static function config(): array
    {
        return [
            'required' => [],
            'optional' => ['skill', 'agent', 'full'],
            'flags'    => ['full'],
            'options'  => [
                'skill' => ['describe' => 'Mostrar dependencias solo para un skill especifico'],
                'agent' => ['describe' => 'Directorio del agente (agent, claude, qwen)', 'default' => 'agent'],
                'full'  => ['describe' => 'Cuando se usa con --skill, muestra dependencias upstream y downstream'],
            ],
        ];
    }

    public function execute(array $parsed): void
    {
        $agent       = $this->opt($parsed, 'agent', 'agent');
        $skillFilter = $this->opt($parsed, 'skill', null);
        $showFull    = $this->opt($parsed, 'full', false);

        $agentDir = $this->getAgentDir($agent);
        $skills   = $this->getSkillsDirectory($agentDir);

        if (count($skills) === 0) {
            $this->log("No se encontraron skills en {$agentDir}/skills/", 'warning');
            return;
        }

        $dependencyGraph = $this->buildDependencyGraph($skills);

        if ($skillFilter !== null) {
            $normalizedFilter = $this->normalizeSkillName($skillFilter);
            $skillExists      = false;
            $targetSkill      = null;

            foreach ($skills as $s) {
                if ($this->normalizeSkillName($s['name']) === $normalizedFilter) {
                    $skillExists = true;
                    $targetSkill = $s;
                    break;
                }
            }

            if (!$skillExists) {
                $this->log("Skill '{$skillFilter}' no encontrado en {$agentDir}/skills/", 'error');
                $this->log('Skills disponibles:', 'info');
                foreach ($skills as $s) {
                    echo "  {$s['name']}" . PHP_EOL;
                }
                return;
            }
        }

        $cycleDetection = CircularDependencyDetector::detectCircularDependencies($dependencyGraph);

        $this->log("Skill Dependency Tree ({$agent})", 'success');
        $this->log(count($skills) . " skills scanned, " . $this->countEdges($dependencyGraph) . " dependencies mapped", 'info');
        echo PHP_EOL;

        if ($cycleDetection['hasCycle']) {
            $this->log("Circular dependencies detected! (" . count($cycleDetection['cycles']) . " cycle(s))", 'warning');
            $formattedCycles = CircularDependencyDetector::formatCycles($cycleDetection['cycles']);
            foreach ($formattedCycles as $index => $cycle) {
                echo "   Cycle " . ($index + 1) . ": {$cycle}" . PHP_EOL;
            }
            echo PHP_EOL;
        } else {
            $this->log("No circular dependencies detected", 'success');
            echo PHP_EOL;
        }

        if ($skillFilter !== null) {
            $normalizedFilter = $this->normalizeSkillName($skillFilter);
            $targetSkill      = null;

            foreach ($skills as $s) {
                if ($this->normalizeSkillName($s['name']) === $normalizedFilter) {
                    $targetSkill = $s;
                    break;
                }
            }

            echo "{$targetSkill['name']}" . PHP_EOL;

            $upstreamDeps = $dependencyGraph[$targetSkill['name']] ?? [];

            if (count($upstreamDeps) > 0) {
                echo "  Upstream dependencies (" . count($upstreamDeps) . "):" . PHP_EOL;
                $this->printTree($upstreamDeps, $dependencyGraph, '    ', [], $targetSkill['name']);
            } else {
                echo "  Upstream dependencies: none" . PHP_EOL;
            }

            if ($showFull) {
                $downstreamDeps = $this->findDownstreamDependencies($targetSkill['name'], $dependencyGraph);

                if (count($downstreamDeps) > 0) {
                    echo "  Downstream dependencies (" . count($downstreamDeps) . "):" . PHP_EOL;
                    $this->printTree($downstreamDeps, $dependencyGraph, '    ', [], $targetSkill['name']);
                } else {
                    echo "  Downstream dependencies: none" . PHP_EOL;
                }
            }
        } else {
            $skillsWithDeps    = [];
            $skillsWithoutDeps = [];

            foreach ($skills as $s) {
                $deps = $dependencyGraph[$s['name']] ?? [];
                if (count($deps) > 0) {
                    $skillsWithDeps[] = $s;
                } else {
                    $skillsWithoutDeps[] = $s;
                }
            }

            if (count($skillsWithDeps) > 0) {
                $this->log('Skills with dependencies:', 'info');
                echo PHP_EOL;

                foreach ($skillsWithDeps as $skill) {
                    $deps = $dependencyGraph[$skill['name']] ?? [];
                    echo "{$skill['name']}" . PHP_EOL;
                    $this->printTree($deps, $dependencyGraph, '  ', [$skill['name']], $skill['name']);
                    echo PHP_EOL;
                }
            }

            if (count($skillsWithoutDeps) > 0) {
                $this->log('Skills without dependencies (' . count($skillsWithoutDeps) . '):', 'info');
                echo PHP_EOL;
                foreach ($skillsWithoutDeps as $skill) {
                    echo "  {$skill['name']}" . PHP_EOL;
                }
                echo PHP_EOL;
            }
        }

        $this->log('Summary:', 'info');
        echo "   Total skills: " . count($skills) . PHP_EOL;
        echo "   Total dependencies: " . $this->countEdges($dependencyGraph) . PHP_EOL;
        echo "   Circular dependencies: " . count($cycleDetection['cycles']) . PHP_EOL;
        echo "   Visited nodes: " . $cycleDetection['visitedNodes'] . PHP_EOL;
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

    private function findDownstreamDependencies(string $skillName, array $graph): array
    {
        $downstream = [];

        foreach ($graph as $skill => $deps) {
            if (in_array($skillName, $deps, true)) {
                $downstream[] = $skill;
            }
        }

        sort($downstream);
        return $downstream;
    }

    private function printTree(array $deps, array $graph, string $prefix, array $visited, ?string $rootSkill = null): void
    {
        sort($deps);
        $lastIndex = count($deps) - 1;

        foreach ($deps as $index => $dep) {
            $isLast    = $index === $lastIndex;
            $connector = $isLast ? '└─' : '├─';

            $childDeps = $graph[$dep] ?? [];
            $icon      = count($childDeps) > 0 ? '📦' : '📄';

            echo "{$prefix}{$connector} {$icon} {$dep}" . PHP_EOL;

            if (!in_array($dep, $visited, true) && $dep !== $rootSkill) {
                if (count($childDeps) > 0) {
                    $visited[]    = $dep;
                    $childPrefix  = $isLast ? "{$prefix}  " : "{$prefix}│ ";
                    $this->printTree($childDeps, $graph, $childPrefix, $visited, $rootSkill);
                }
            } elseif ($dep === $rootSkill) {
                // no marcar rootSkill como circular
            } elseif (in_array($dep, $visited, true)) {
                echo "{$prefix}   (already shown ↑)" . PHP_EOL;
            }
        }
    }

    private function countEdges(array $graph): int
    {
        $count = 0;
        foreach ($graph as $deps) {
            $count += count($deps);
        }
        return $count;
    }
}
