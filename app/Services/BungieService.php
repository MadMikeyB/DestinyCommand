<?php

namespace App\Services;

use App\Models\OAuth\OAuthProvider;
use App\Models\OAuth\OAuthSession;
use App\Transports\BungieTransport;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class BungieService
{
    protected $baseUrl = 'https://www.bungie.net/Platform/';

    protected BungieTransport $httpClient;

    public function __construct()
    {
        $this->httpClient = new BungieTransport;
    }

    protected function makeRequest(string $url, array $params = [], ?string $accessToken = null)
    {
        $response = $this->httpClient
            ->pendingRequest($accessToken)
            ->get($url, $params);

        $responseData = $response->json();

        Log::info('Bungie API response', [
            'url' => $url,
            'params' => $params,
            'status' => $response->status(),
        ]);

        return [
            'status' => $response->status(),
            'body' => $response->body(),
            'json' => $responseData,
        ];
    }

    public function getTokenData($code)
    {
        $response = $this->httpClient
            ->pendingRequest()
            ->asForm()
            ->post($this->baseUrl.'App/OAuth/Token/', [
                'grant_type' => 'authorization_code',
                'code' => $code,
                'client_id' => config('services.bungie.client_id'),
                'client_secret' => config('services.bungie.client_secret'),
            ]);

        $tokenData = $response->json();

        Log::info('Bungie Token Exchange Response', [
            'status' => $response->status(),
            'tokenData' => $tokenData,
        ]);

        return [
            'status' => $response->status(),
            'body' => $response->body(),
            'json' => $tokenData,
        ];
    }

    public function refreshTokenData($refreshToken)
    {
        $response = $this->httpClient
            ->pendingRequest()
            ->asForm()
            ->post('https://www.bungie.net/Platform/App/OAuth/token/', [
                'grant_type' => 'refresh_token',
                'refresh_token' => $refreshToken,
                'client_id' => config('services.bungie.client_id'),
                'client_secret' => config('services.bungie.client_secret'),
            ]);

        $tokenData = $response->json();

        Log::info('Bungie Token Refresh Response', [
            'status' => $response->status(),
            'tokenData' => $tokenData,
        ]);

        return [
            'status' => $response->status(),
            'body' => $response->body(),
            'json' => $tokenData,
        ];
    }

    public function getAccessToken(array $tokenData)
    {
        return $tokenData['access_token'] ?? null;
    }

    public function getRefreshToken(array $tokenData)
    {
        return $tokenData['refresh_token'] ?? null;
    }

    public function getMembershipIdFromToken(array $tokenData)
    {
        return $tokenData['membership_id'] ?? null;
    }

    public function getCurrentBungieNetUser($accessToken)
    {
        $result = $this->makeRequest($this->baseUrl.'User/GetCurrentBungieNetUser/', [], $accessToken);

        return $result['json']['Response'] ?? null;
    }

    public function fetchMembershipsForCurrentUser($accessToken)
    {
        return $this->makeRequest($this->baseUrl.'User/GetMembershipsForCurrentUser/', [], $accessToken)['json'] ?? null;
    }

    public function isAuthValid($authSessionId)
    {
        if (! $oauthSession = OAuthSession::find($authSessionId)) {
            return false;
        }

        if ($oauthSession->expires_in > Carbon::now()) {
            return true;
        }

        if ($oauthSession->refresh_expires_in <= Carbon::now()) {
            return false;
        }

        $provider = OAuthProvider::find($oauthSession->provider_id);
        $response = $this->refreshTokenData($oauthSession->refresh_token);
        $tokenData = $response['json'] ?? [];

        if (($response['status'] ?? 500) >= 400 || ! isset($tokenData['access_token'])) {
            Log::error('Failed to refresh Bungie access token', [
                'code' => 'DC521',
                'provider' => $provider?->name ?? 'Bungie',
                'status' => $response['status'] ?? null,
                'body' => $tokenData,
                'raw_body' => $response['body'] ?? null,
            ]);

            return false;
        }

        $oauthSession->access_token = $tokenData['access_token'];
        $oauthSession->refresh_token = $tokenData['refresh_token'] ?? $oauthSession->refresh_token;
        $oauthSession->expires_in = Carbon::now()->addSeconds($tokenData['expires_in'] ?? 0);
        $oauthSession->refresh_expires_in = Carbon::now()->addSeconds($tokenData['refresh_expires_in'] ?? 5184000);
        $oauthSession->identifier = $tokenData['membership_id'] ?? $oauthSession->identifier;
        $oauthSession->save();

        return true;
    }
}
