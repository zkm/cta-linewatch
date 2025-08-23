<?php

namespace App\Libraries;

use CodeIgniter\Database\BaseConnection;

class StationRepository
{
    /** @var BaseConnection|null */
    private $db = null; // Lazy to avoid requiring mysqli unless needed

    public function __construct()
    {
        // Intentionally empty: we avoid connecting to DB until absolutely necessary.
    }

    /**
     * Fetch stations for a given line using (in order):
     * 1) Legacy HTML lists in /stations/{line}.php (DB-less)
     * 2) Database fallback to legacy tables if configured
     *
     * @param string $line one of blue,brown,green,orange,pink,purple,red,yellow
     * @return list<array{sid:int,station:string}>
     */
    public function getStationsByLine(string $line): array
    {
        $allowed = ['blue','brown','green','orange','pink','purple','red','yellow'];
        if (! in_array($line, $allowed, true)) {
            return [];
        }

        // 1) Try to parse from legacy HTML select file
        $fromFile = $this->parseLegacyFile($line);
        if (! empty($fromFile)) {
            return $this->applyConfiguredOrder($line, $fromFile);
        }

        // 2) Fallback to DB if available/configured
        try {
            /** @var BaseConnection $db */
            $db = $this->db ?? (\Config\Database::connect());
            $this->db = $db;
        } catch (\Throwable $e) {
            return [];
        }

        try {
            $builder = $this->db->table($line);

            // Prefer route order via 'seq' column when available
            $hasSeq = false;
            try {
                if (method_exists($this->db, 'fieldExists')) {
                    $hasSeq = $this->db->fieldExists('seq', $line);
                }
            } catch (\Throwable $e) {
                $hasSeq = false;
            }

            if ($hasSeq) {
                $builder->select('sid, station, seq');
                $builder->orderBy('seq', 'ASC');
            } else {
                $builder->select('sid, station');
                // Fallback alphabetical only if no legacy file and no seq
                $builder->orderBy('station', 'ASC');
            }

            $query = $builder->get();
            $rows = $query->getResultArray();
            $result = array_map(static function ($row) {
                return [
                    'sid'     => (int) $row['sid'],
                    'station' => (string) $row['station'],
                ];
            }, $rows);
            return $this->applyConfiguredOrder($line, $result);
        } catch (\Throwable $e) {
            return [];
        }
    }

    /**
     * If a per-line station order is configured, reorder the list to match by SID.
     * Any stations not listed are appended in their original order.
     *
     * @param string $line
     * @param list<array{sid:int,station:string}> $stations
     * @return list<array{sid:int,station:string}>
     */
    private function applyConfiguredOrder(string $line, array $stations): array
    {
        try {
            $cfg = new \Config\StationOrder();
            $order = $cfg->orders[$line] ?? [];
            if (empty($order)) {
                return $stations;
            }
            $index = array_flip($order); // sid => desired index
            usort($stations, static function ($a, $b) use ($index) {
                $ia = $index[$a['sid']] ?? PHP_INT_MAX;
                $ib = $index[$b['sid']] ?? PHP_INT_MAX;
                if ($ia === $ib) return 0;
                return $ia <=> $ib;
            });
            return $stations;
        } catch (\Throwable $e) {
            return $stations; // do nothing on config errors
        }
    }

    /**
     * Parse legacy stations HTML file like stations/blue.php with <option value="sid">Station</option>.
     *
     * @return list<array{sid:int,station:string}>
     */
    private function parseLegacyFile(string $line): array
    {
        // Expect repo layout with sibling /stations directory at repo root
        // APPPATH points to <repo>/app/, so repo root is one level up.
        $repoRoot = realpath(APPPATH . '..');
        if (! $repoRoot) {
            return [];
        }

        // Candidate legacy locations to support archived layouts
        $candidates = [
            $repoRoot . DIRECTORY_SEPARATOR . 'stations' . DIRECTORY_SEPARATOR . $line . '.php',
            $repoRoot . DIRECTORY_SEPARATOR . 'legacy-site' . DIRECTORY_SEPARATOR . 'stations' . DIRECTORY_SEPARATOR . $line . '.php',
        ];

        $file = null;
        foreach ($candidates as $path) {
            if (is_readable($path)) {
                $file = $path;
                break;
            }
        }
        if ($file === null) {
            return [];
        }

        $contents = @file_get_contents($file);
        if ($contents === false) {
            return [];
        }

        $stations = [];
        // Match <option value="12345">Station Name</option>
        if (preg_match_all('/<option\\s+value=\"(\\d+)\">([^<]+)<\\/option>/i', $contents, $m, PREG_SET_ORDER)) {
            foreach ($m as $opt) {
                $sid = (int) $opt[1];
                $name = trim(html_entity_decode($opt[2]));
                if ($sid === 0 || $name === '' || stripos($name, '[') !== false) {
                    continue; // skip the header option like [ Blue Line Stops ]
                }
                $stations[] = ['sid' => $sid, 'station' => $name];
            }
        }

        return $stations;
    }
}
