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
            return $fromFile;
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
            $builder->select('sid, station');
            $builder->orderBy('station', 'ASC');
            $query = $builder->get();
            $rows = $query->getResultArray();
            return array_map(static function ($row) {
                return [
                    'sid'     => (int) $row['sid'],
                    'station' => (string) $row['station'],
                ];
            }, $rows);
        } catch (\Throwable $e) {
            return [];
        }
    }

    /**
     * Parse legacy stations HTML file like stations/blue.php with <option value="sid">Station</option>.
     *
     * @return list<array{sid:int,station:string}>
     */
    private function parseLegacyFile(string $line): array
    {
        // Expect repo layout with sibling /stations directory
        $root = realpath(APPPATH . '../..'); // move from cta-ci-app/app to repo root
        if (! $root) {
            return [];
        }
        $file = $root . DIRECTORY_SEPARATOR . 'stations' . DIRECTORY_SEPARATOR . $line . '.php';
        if (! is_readable($file)) {
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

        usort($stations, static function ($a, $b) {
            return strcasecmp($a['station'], $b['station']);
        });

        return $stations;
    }
}
