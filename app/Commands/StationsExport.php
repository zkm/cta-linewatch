<?php

namespace App\Commands;

use App\Libraries\StationRepository;
use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class StationsExport extends BaseCommand
{
    protected $group = 'Stations';
    protected $name = 'stations:export';
    protected $description = 'Export stations to JSON files from current data sources (JSON/HTML/DB).';
    protected $usage = 'stations:export [line] [--combined] [--force]';
    protected $arguments = [
        'line' => 'Optional line color to export (blue, brown, green, orange, pink, purple, red, yellow)',
    ];
    protected $options = [
        '--combined' => 'Also write stations/stations.json as a combined file',
        '--force'    => 'Overwrite existing JSON files if present',
    ];

    /**
     * @var list<string>
     */
    private array $allowed = ['blue','brown','green','orange','pink','purple','red','yellow'];

    public function run(array $params): void
    {
        $lineParam = $params[0] ?? null;
        // Prefer CodeIgniter's CLI option parsing
        $combined = (bool) (CLI::getOption('combined'));
        $force    = (bool) (CLI::getOption('force'));

        $lines = $this->resolveLines($lineParam);
        if (empty($lines)) {
            CLI::error('No valid line specified. Valid: ' . implode(', ', $this->allowed));
            return;
        }

        $repoRoot = realpath(APPPATH . '..') ?: getcwd();
        $stationsDir = $repoRoot . DIRECTORY_SEPARATOR . 'stations';
        if (! is_dir($stationsDir)) {
            if (! @mkdir($stationsDir, 0777, true) && ! is_dir($stationsDir)) {
                CLI::error('Failed to create stations directory at ' . $stationsDir);
                return;
            }
        }

        $repo = new StationRepository();
        $combinedPayload = [];
        $totalWritten = 0;

        foreach ($lines as $line) {
            $data = $repo->getStationsByLine($line);
            if (empty($data)) {
                // Some static analysers don't know CLI::warning(); use write with yellow
                CLI::write(sprintf('[%s] No stations found, skipping.', $line), 'yellow');
                continue;
            }

            $path = $stationsDir . DIRECTORY_SEPARATOR . $line . '.json';
            if (file_exists($path) && ! $force) {
                CLI::write(sprintf('[%s] JSON exists, skipping (use --force to overwrite): %s', $line, $path));
            } else {
                $ok = $this->writeJson($path, $data);
                if ($ok) {
                    $totalWritten++;
                    CLI::write(sprintf('[%s] Wrote %d stations to %s', $line, count($data), $path));
                } else {
                    CLI::error(sprintf('[%s] Failed to write %s', $line, $path));
                }
            }

            $combinedPayload[$line] = $data;
        }

        if ($combined && ! empty($combinedPayload)) {
            $combinedPath = $stationsDir . DIRECTORY_SEPARATOR . 'stations.json';
            $ok = $this->writeJson($combinedPath, $combinedPayload);
            if ($ok) {
                CLI::write(sprintf('[combined] Wrote combined JSON: %s', $combinedPath));
            } else {
                CLI::error('[combined] Failed to write combined JSON');
            }
        }

        CLI::write(sprintf('Done. %d file(s) written%s.', $totalWritten, $combined ? ' (+ combined)' : ''));
    }

    /**
     * @return list<string>
     */
    private function resolveLines(?string $lineParam): array
    {
        if ($lineParam === null || $lineParam === '') {
            return $this->allowed;
        }
        $line = strtolower(trim($lineParam));
        return in_array($line, $this->allowed, true) ? [$line] : [];
    }

    /**
     * @param string $path
     * @param mixed  $payload
     */
    private function writeJson(string $path, $payload): bool
    {
        $json = json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        if ($json === false) {
            return false;
        }
        return @file_put_contents($path, $json, LOCK_EX) !== false;
    }
}
