<?php

namespace App\Libraries;

use CodeIgniter\HTTP\CURLRequest;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * Simple CTA Train Tracker API client.
 */
class CtaTrainTracker
{
    private string $baseUrl = 'https://lapi.transitchicago.com/api/1.0/ttarrivals.aspx';
    private ?string $apiKey;
    private CURLRequest $http;

    public function __construct()
    {
    // Read API key directly from environment to avoid BaseService scalar return type issues
    $this->apiKey = function_exists('env') ? env('CTA_TRAIN_API_KEY') : getenv('CTA_TRAIN_API_KEY');
        /** @var CURLRequest $http */
        $http = service('curlrequest');
        $this->http = $http;
    }

    /**
     * Fetch arrivals for a station by map id.
     *
     * @param int|string $mapId Station map ID
     * @return array{ok:bool,error?:string,updated?:string,station?:array,arrivals?:list<array>}
     */
    public function getArrivals($mapId): array
    {
        if (empty($this->apiKey)) {
            return [
                'ok'    => false,
                'error' => 'Missing CTA API key. Set CTA_TRAIN_API_KEY in your .env.',
            ];
        }

        try {
            $resp = $this->http->get($this->baseUrl, [
                'query' => [
                    'key'         => $this->apiKey,
                    'mapid'       => (string) $mapId,
                    'outputType'  => 'JSON',
                ],
                'http_errors' => false,
                'timeout'     => 8,
                'connect_timeout' => 5,
            ]);
        } catch (\Throwable $e) {
            return [
                'ok'    => false,
                'error' => 'Request failed: ' . $e->getMessage(),
            ];
        }

        if ($resp->getStatusCode() !== ResponseInterface::HTTP_OK) {
            return [
                'ok'    => false,
                'error' => 'CTA API error HTTP ' . $resp->getStatusCode(),
            ];
        }

        $json = json_decode($resp->getBody(), true);
        if (! is_array($json) || ! isset($json['ctatt'])) {
            return [
                'ok'    => false,
                'error' => 'Unexpected response format from CTA API.',
            ];
        }

        $ctatt = $json['ctatt'];

        // Normalize
        $updated = $ctatt['tmst'] ?? null;
        $etas = $ctatt['eta'] ?? [];
        $station = null;
        $arrivals = [];

        foreach ($etas as $eta) {
            if ($station === null) {
                $station = [
                    'name' => $eta['staNm'] ?? '',
                    'id'   => $eta['staId'] ?? null,
                ];
            }

            $arrivals[] = [
                'route'    => $eta['rt'] ?? '',
                'dest'     => $eta['destNm'] ?? '',
                'arrives'  => $eta['arrT'] ?? '', // arrival time (ISO-like)
                'isDue'    => ($eta['isApp'] ?? '0') === '1' || ($eta['isDly'] ?? '0') === '1',
                'dueText'  => $eta['isApp'] === '1' ? 'Approaching' : (($eta['isDly'] ?? '0') === '1' ? 'Delayed' : ''),
                'platform' => $eta['stpDe'] ?? '',
                'run'      => $eta['rn'] ?? '',
                'flags'    => [
                    'delayed'     => ($eta['isDly'] ?? '0') === '1',
                    'scheduled'   => ($eta['isSch'] ?? '0') === '1',
                    'approaching' => ($eta['isApp'] ?? '0') === '1',
                ],
            ];
        }

        // Sort by arrival time if present
        usort($arrivals, static function ($a, $b) {
            return strcmp($a['arrives'], $b['arrives']);
        });

        return [
            'ok'       => true,
            'updated'  => $updated,
            'station'  => $station,
            'arrivals' => $arrivals,
        ];
    }
}
