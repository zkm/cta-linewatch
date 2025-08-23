<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class StationOrder extends BaseConfig
{
    /**
     * Per-line route order by SID (map ID).
     * If a line has an array here, stations will be ordered to match it.
     * Any stations not listed will be appended in their original order.
     *
     * @var array<string, list<int>>
     */
    public array $orders = [
        // Brown Line: Kimball → Loop (Washington/Wells → Quincy → LaSalle/Van Buren)
        'brown' => [
            41290, // Kimball
            41180, // Kedzie
            40870, // Francisco
            41010, // Rockwell
            41480, // Western (Brown)
            41500, // Montrose
            41460, // Irving Park
            41440, // Addison
            41310, // Paulina
            40360, // Southport
            41320, // Belmont (transfer)
            41210, // Wellington
            40530, // Diversey
            41220, // Fullerton (transfer)
            40660, // Armitage
            40800, // Sedgwick
            40710, // Chicago
            40460, // Merchandise Mart
            40730, // Washington/Wells
            40040, // Quincy
            40160, // LaSalle/Van Buren
        ],

        // Other lines can be populated here similarly using SIDs in route order.
        'blue'   => [],
        'green'  => [],
        'orange' => [],
        'pink'   => [],
        'purple' => [],
        'red'    => [],
        'yellow' => [],
    ];
}
