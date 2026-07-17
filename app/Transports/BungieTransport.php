<?php

namespace App\Transports;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Http;

class BungieTransport
{
    public function headers(?string $accessToken = null, bool $includeOrigin = false): array
    {
        $headers = [
            'X-API-Key' => config('services.bungie.api_key'),
            'User-Agent' => config('destinycommand.user_agent'),
            'Accept' => 'application/json',
        ];

        if ($includeOrigin && filled(config('destinycommand.request_origin'))) {
            $headers['Origin'] = config('destinycommand.request_origin');
        }

        if ($accessToken) {
            $headers['Authorization'] = 'Bearer '.$accessToken;
        }

        return array_filter($headers);
    }

    public function pendingRequest(?string $accessToken = null, bool $includeOrigin = false)
    {
        return Http::withHeaders($this->headers($accessToken, $includeOrigin))
            ->connectTimeout(5)
            ->timeout(20)
            ->retry(3, 200);
    }

    public function guzzleClient(): Client
    {
        return new Client([
            'http_errors' => false,
            'verify' => false,
            'timeout' => 6,
            'connect_timeout' => 1.5,
            'force_ip_resolve' => 'v4',
        ]);
    }

    public function isBungieUrl(string $url): bool
    {
        return str_contains($url, 'bungie.net');
    }
}
