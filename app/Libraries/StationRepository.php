<?php

namespace App\Libraries;

use CodeIgniter\Database\BaseConnection;
use CodeIgniter\Cache\CacheInterface;
use Config\Services;

class StationRepository
{
    /** @var BaseConnection|null */
    private $db = null; // Lazy to avoid requiring mysqli unless needed

    /** @var CacheInterface|null */
    private $cache = null;

    /** @var array<string, list<array{sid:int,station:string}>> */
    private array $memo = [];

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

        // Memoized per request
        if (isset($this->memo[$line])) {
            return $this->memo[$line];
        }

        // Config for data source and caching
        $cfg = new \Config\Stations();

        // Cache lookup
        if ($cfg->enableCache) {
            try {
                $this->cache = $this->cache ?? Services::cache();
                $cached = $this->cache?->get($this->cacheKey($line));
                if (is_array($cached)) {
                    $this->memo[$line] = $cached;
                    return $cached;
                }
            } catch (\Throwable $e) {
                // ignore cache errors
            }
        }

        // Source priority: json -> html -> db by default (configurable)
        $stations = [];
        foreach ($cfg->sourcePriority as $source) {
            if ($source === 'json') {
                $stations = $this->parseJson($line);
            } elseif ($source === 'html') {
                $stations = $this->parseLegacyFile($line);
            } elseif ($source === 'db') {
                $stations = $this->fetchFromDb($line);
            }
            if (! empty($stations)) {
                break;
            }
        }

        $stations = $this->applyConfiguredOrder($line, $stations);

        if ($cfg->enableCache && ! empty($stations)) {
            try {
                $this->cache = $this->cache ?? Services::cache();
                $this->cache?->save($this->cacheKey($line), $stations, $cfg->cacheTTL);
            } catch (\Throwable $e) {
                // ignore cache errors
            }
        }

        $this->memo[$line] = $stations;
        return $stations;
    }

    private function cacheKey(string $line): string
    {
        return 'stations:' . $line;
    }

    /**
     * DB fetch extracted to allow sourcePriority selection.
     *
     * @return list<array{sid:int,station:string}>
     */
    private function fetchFromDb(string $line): array
    {
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
            return $result;
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
                if ($ia === $ib) {
                    return 0;
                }
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

    /**
     * Parse stations from JSON.
     * Supports per-line JSON (stations/{line}.json) or combined stations/stations.json.
     *
     * @return list<array{sid:int,station:string}>
     */
    private function parseJson(string $line): array
    {
        $repoRoot = realpath(APPPATH . '..');
        if (! $repoRoot) {
            return [];
        }

        $dir = $repoRoot . DIRECTORY_SEPARATOR . 'stations' . DIRECTORY_SEPARATOR;
        $perLine = $dir . $line . '.json';
        $combined = $dir . 'stations.json';

        $data = null;

        if (is_readable($perLine)) {
            $json = @file_get_contents($perLine);
            if ($json !== false) {
                $data = json_decode($json, true);
            }
        }

        if ($data === null && is_readable($combined)) {
            $json = @file_get_contents($combined);
            if ($json !== false) {
                $decoded = json_decode($json, true);
                if (is_array($decoded)) {
                    if (array_key_exists($line, $decoded) && is_array($decoded[$line])) {
                        $data = $decoded[$line];
                    } else {
                        $subset = [];
                        foreach ($decoded as $entry) {
                            if (is_array($entry) && ($entry['line'] ?? null) === $line) {
                                $subset[] = ['sid' => $entry['sid'] ?? null, 'station' => $entry['station'] ?? null];
                            }
                        }
                        $data = $subset;
                    }
                }
            }
        }

        if (! is_array($data)) {
            return [];
        }

        $stations = [];
        foreach ($data as $row) {
            if (! is_array($row)) {
                continue;
            }
            $sid = isset($row['sid']) ? (int) $row['sid'] : 0;
            $name = isset($row['station']) ? (string) $row['station'] : '';
            if ($sid > 0 && $name !== '') {
                $stations[] = ['sid' => $sid, 'station' => $name];
            }
        }

        return $stations;
    }
}
