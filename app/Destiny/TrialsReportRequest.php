<?php

namespace App\Destiny;

/**
 * Represents a queued Trials Report HTTP request.
 */
class TrialsReportRequest
{
    public string $url;

    public int $cache;

    public array $params;

    public string $baseUrl = 'https://api.trialsofthenine.com';

    public string $method;

    /**
     * Build a new Trials Report request definition.
     */
    public function __construct(string $strUrl, array $aParams = [], int $iCache = 0, string $strMethod = 'GET')
    {
        $this->url = $this->baseUrl.$strUrl;
        $this->cache = $iCache;
        $this->params = $aParams;
        $this->method = $strMethod;
        if (! empty($aParams)) {
            $this->url .= '?'.http_build_query($aParams);
        }
    }
}
