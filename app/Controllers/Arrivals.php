<?php

namespace App\Controllers;

use App\Libraries\CtaTrainTracker;

class Arrivals extends BaseController
{
    public function index()
    {
        $mapId = $this->request->getGet('mapid') ?? '';

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
