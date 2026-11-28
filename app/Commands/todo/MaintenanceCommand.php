<?php

require_once __DIR__ . '/BaseTodoCommand.php';

/**
 * Reporte de mantenimiento del Kanban. Solo lee: stale es una clasificación
 * calculada a partir de filemtime(), nunca un estado ni un campo escrito.
 */
class TodoMaintenanceCommand extends BaseTodoCommand
{
    public const DEFAULT_STALE_DAYS = 60;

    /** Estados operativos: los únicos que pueden aparecer como stale. */
    public const ACTIVE_STATES = ['pending', 'in-progress', 'on-hold', 'needs-review'];

    private const AGE_BANDS = [
        ['max' => 30, 'name' => 'normal'],
        ['max' => 60, 'name' => 'unattended'],
        ['max' => 90, 'name' => 'stale'],
    ];

    public function __construct()
    {
        $this->command     = 'maintenance';
        $this->description = 'Reporta el estado del Kanban y sus tarjetas stale sin modificarlas';
        $this->aliases     = [];
        $this->examples    = [
            'php com todo maintenance',
            'php com todo maintenance --stale',
            'php com todo maintenance --stale --days=90',
            'php com todo maintenance --json',
        ];
    }

    public static function config(): array
    {
        return [
            'required' => [],
            'optional' => ['days'],
            'flags'    => ['stale', 'json'],
            'options'  => [
                'days'  => ['describe' => 'Umbral de días para considerar stale (default: 60)'],
                'stale' => ['describe' => 'Listar solo las tarjetas sobre el umbral'],
                'json'  => ['describe' => 'Salida legible por máquina'],
            ],
        ];
    }

    public function validate(array $parsed): bool
    {
        if (!parent::validate($parsed)) {
            return false;
        }

        if ($this->resolveDays($parsed) === null) {
            echo "✗ Error: '--days' debe ser un entero positivo.\n";
            $this->showUsage();
            return false;
        }

        return true;
    }

    public function execute(array $parsed): void
    {
        $days = $this->resolveDays($parsed);

        if ($days === null) {
            $this->log("'--days' debe ser un entero positivo", 'error');
            return;
        }

        $report = $this->buildReport($days);

        if ((bool) ($parsed['json'] ?? false)) {
            echo json_encode(
                $report,
                JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE
            ) . PHP_EOL;
            return;
        }

        if ((bool) ($parsed['stale'] ?? false)) {
            $this->printStale($report, $days);
            return;
        }

        $this->printSummary($report, $days);
    }

    private function resolveDays(array $parsed): ?int
    {
        $raw = $this->opt($parsed, 'days');

        if ($raw === null) {
            return self::DEFAULT_STALE_DAYS;
        }

        if (is_int($raw)) {
            return $raw > 0 ? $raw : null;
        }

        if (!is_string($raw) || !preg_match('/^[1-9][0-9]*$/', $raw)) {
            return null;
        }

        return (int) $raw;
    }

    private function buildReport(int $days): array
    {
        $states     = [];
        $stale      = [];
        $staleByState = [];

        foreach ($this->detectAllFolders() as $state => $dir) {
            $files = glob($dir . '/*.md') ?: [];
            sort($files);

            $states[$state] = [
                'total' => count($files),
                'stale' => 0,
            ];

            if (!in_array($state, self::ACTIVE_STATES, true)) {
                continue;
            }

            $stateStale = [];

            foreach ($files as $file) {
                $age = $this->ageInDays($file);

                if ($age < $days) {
                    continue;
                }

                $meta  = $this->readFileMeta($file);
                $title = $meta['meta']['title'] ?? '';

                $stateStale[] = [
                    'state'    => $state,
                    'file'     => $this->relativeFile($state, $file),
                    'title'    => is_scalar($title) ? (string) $title : '',
                    'age_days' => $age,
                    'band'     => $this->ageBand($age),
                ];
            }

            usort($stateStale, static function (array $a, array $b): int {
                return ($b['age_days'] <=> $a['age_days']) ?: strcmp($a['file'], $b['file']);
            });

            $staleByState[$state]   = $stateStale;
            $states[$state]['stale'] = count($stateStale);
        }

        foreach ($staleByState as $entries) {
            foreach ($entries as $entry) {
                $stale[] = $entry;
            }
        }

        return [
            'generated_at'      => gmdate('Y-m-d\TH:i:s\Z'),
            'age_source'        => 'filemtime',
            'threshold_days'    => $days,
            'states'            => $states,
            'stale'             => $stale,
            'requires_attention' => count($stale),
        ];
    }

    private function ageInDays(string $path): int
    {
        clearstatcache(true, $path);
        $mtime = @filemtime($path);

        if ($mtime === false) {
            return 0;
        }

        return max(0, (int) floor((time() - $mtime) / 86400));
    }

    private function ageBand(int $age): string
    {
        foreach (self::AGE_BANDS as $band) {
            if ($age <= $band['max']) {
                return $band['name'];
            }
        }

        return 'requires-decision';
    }

    private function relativeFile(string $state, string $path): string
    {
        $folder = self::STATE_FOLDERS[$state] ?? '';
        $name   = basename($path);

        return $folder === '' ? $name : $folder . '/' . $name;
    }

    private function printSummary(array $report, int $days): void
    {
        echo "GoProp Kanban Maintenance Report\n";
        echo str_repeat('=', 32) . "\n\n";

        foreach ($report['states'] as $state => $info) {
            echo strtoupper(str_replace('-', ' ', $state)) . "\n";
            echo "  Total: {$info['total']}\n";

            if (in_array($state, self::ACTIVE_STATES, true)) {
                echo "  Stale > {$days} days: {$info['stale']}\n";
            }

            echo "\n";
        }

        echo str_repeat('-', 32) . "\n";
        echo "REQUIRES ATTENTION: {$report['requires_attention']} cards\n\n";
    }

    private function printStale(array $report, int $days): void
    {
        echo "STALE CARDS (>{$days} days)\n\n";

        if ($report['stale'] === []) {
            echo "No stale cards.\n\n";
            return;
        }

        $currentState = null;

        foreach ($report['stale'] as $entry) {
            if ($entry['state'] !== $currentState) {
                $currentState = $entry['state'];
                echo $currentState . "/\n";
            }

            echo "  {$entry['file']}  {$entry['age_days']} days\n";
        }

        echo "\n";
    }
}
