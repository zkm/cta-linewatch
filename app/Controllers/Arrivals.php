<?php

namespace App\Controllers;

use App\Libraries\CtaTrainTracker;

class Arrivals extends BaseController
{
    public function index(): string
    {
        $raw = $this->request->getGet('mapid');
        $mapId = '';
        if (is_string($raw)) {
            $mapId = $raw;
        } elseif (is_int($raw)) {
            $mapId = (string) $raw;
        }

        $data = [
            'title'    => 'CTA Train Arrivals',
            'mapid'    => $mapId,
            'result'   => null,
            'hasKey'   => (bool) (function_exists('env') ? env('CTA_TRAIN_API_KEY') : getenv('CTA_TRAIN_API_KEY')),
        ];

        if ($mapId !== '') {
            $tracker = new CtaTrainTracker();
            $data['result'] = $tracker->getArrivals($mapId);
        }

    return view('arrivals/index', $data);
    }
}
