<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class Stations extends BaseConfig
{
    /**
     * Source priority to resolve station lists. Valid values: 'json', 'html', 'db'.
     * Default prefers JSON, then legacy HTML, then DB.
     *
     * @var list<'json'|'html'|'db'>
     */
    public array $sourcePriority = ['json', 'html', 'db'];

    /**
     * Enable caching station lists via framework cache handler.
     */
    public bool $enableCache = true;

    /**
     * Cache TTL in seconds.
     */
    public int $cacheTTL = 3600; // 1 hour
}
