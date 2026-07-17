<?php

namespace App\Destiny;

/**
 * Represents a queued Bungie API HTTP request.
 */
class DestinyRequest
{
    public string $url;

    public int $cache;

    public array $params;

    public string $baseUrl = 'https://www.bungie.net';

    public string $method;

    public array $postFields = [];

    /**
     * Build a new Bungie API request definition.
     */
    public function __construct(string $strUrl, array $aParams = [], int $iCache = 0, string $strMethod = 'GET', array $aPostFields = [])
    {
        $this->url = $this->baseUrl.$strUrl;
        $this->cache = $iCache;
        $this->params = $aParams;
        $this->method = $strMethod;
        if (! empty($aParams)) {
            $this->url .= '?'.http_build_query($aParams);
        }
        if (! empty($aPostFields)) {
            $this->postFields = $aPostFields;
        }
    }
}
