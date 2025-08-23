<?php

namespace App\Controllers;

use App\Libraries\StationRepository;

class Lines extends BaseController
{
    private StationRepository $stations;

    public function __construct()
    {
        $this->stations = new StationRepository();
    }

    /**
     * Show all CTA lines (legacy app entry).
     */
    public function index()
    {
        $data = [
            'title' => 'CTA Lines',
            'lines' => [
                ['slug' => 'blue',   'name' => "Blue",   'desc' => "O'Hare – Forest Park"],
                ['slug' => 'brown',  'name' => 'Brown',   'desc' => 'Kimball – Loop'],
                ['slug' => 'green',  'name' => 'Green',   'desc' => 'Harlem/Lake – Ashland/63rd – Cottage Grove'],
                ['slug' => 'orange', 'name' => 'Orange',  'desc' => 'Midway – Loop'],
                ['slug' => 'pink',   'name' => 'Pink',    'desc' => '54th/Cermak – Loop'],
                ['slug' => 'purple', 'name' => 'Purple',  'desc' => 'Linden – Howard – Loop'],
                ['slug' => 'red',    'name' => 'Red',     'desc' => 'Howard – 95th/Dan Ryan'],
                ['slug' => 'yellow', 'name' => 'Yellow',  'desc' => 'Skokie – Howard'],
            ],
        ];

        return view('lines/index', $data);
    }

    /**
     * Show stations for a given line color (reads from legacy per-line tables).
     *
     * @param string $line Slug/color (e.g., blue, red)
     */
    public function stations(string $line)
    {
        $color = strtolower($line);
        $valid = ['blue','brown','green','orange','pink','purple','red','yellow'];
        if (! in_array($color, $valid, true)) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound("Unknown line: $line");
        }

        $stations = $this->stations->getStationsByLine($color);

        $data = [
            'title'    => ucfirst($color) . ' Line Stations',
            'line'     => $color,
            'stations' => $stations,
        ];

        return view('lines/stations', $data);
    }
}
