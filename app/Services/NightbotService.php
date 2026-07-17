<?php

namespace App\Services;

use App\Models\OAuth\OAuthProvider;
use App\Models\OAuth\OAuthSession;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class NightbotService
{
    public function getProvider(): OAuthProvider
    {
        return OAuthProvider::where('name', 'Nightbot')->firstOrFail();
    }

    public function getAuthUrl(): string
    {
        $provider = $this->getProvider();
        $state = sha1(time().rand(1, 9999));

        Session::put('state', $state);

        return $provider->auth_url.'&state='.$state.'&client_id='.$provider->client_id
            .(isset($provider->scope) ? '&scope='.$provider->scope : '')
            .(isset($provider->redirect_url) ? '&redirect_uri='.urlencode($provider->redirect_url) : '');
    }

    public function handleCallback(string $code): OAuthSession
    {
        $provider = $this->getProvider();
        $tokens = $this->requestTokens($provider, $code);

        if (isset($tokens->error)) {
            $this->handleError($tokens->error);
        }

        if (isset($tokens->name)) {
            $this->handleError($tokens->name);
        }

        $oauthSession = new OAuthSession;
        $oauthSession->access_token = $tokens->access_token;
        $oauthSession->refresh_token = $tokens->refresh_token;
        $oauthSession->expires_in = Carbon::now()->addSeconds($tokens->expires_in);
        $oauthSession->refresh_expires_in = Carbon::now()->addSeconds($tokens->refresh_expires_in ?? 5184000);
        $oauthSession->provider_id = $provider->id;
        $oauthSession->save();

        return $oauthSession;
    }

    public function isAuthValid($authSessionId): bool
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
        if (! $provider) {
            return false;
        }

        $tokens = $this->requestTokens($provider, $oauthSession->refresh_token, true);

        if (isset($tokens->error)) {
            $this->handleError($tokens->error);
        }

        if (isset($tokens->name)) {
            $this->handleError($tokens->name);
        }

        $oauthSession->access_token = $tokens->access_token;
        $oauthSession->refresh_token = $tokens->refresh_token;
        $oauthSession->expires_in = Carbon::now()->addSeconds($tokens->expires_in);
        $oauthSession->refresh_expires_in = Carbon::now()->addSeconds($tokens->refresh_expires_in ?? 5184000);
        $oauthSession->save();

        return true;
    }

    private function requestTokens(OAuthProvider $provider, string $code, bool $refresh = false): object
    {
        $response = Http::withHeaders([
            'User-Agent' => config('destinycommand.user_agent'),
            'Accept' => 'application/json',
        ])
            ->connectTimeout(5)
            ->timeout(20)
            ->retry(3, 200)
            ->asForm()
            ->post($provider->token_url, [
                'grant_type' => $refresh ? 'refresh_token' : 'authorization_code',
                $refresh ? 'refresh_token' : 'code' => $code,
                'client_id' => $provider->client_id,
                'client_secret' => $provider->client_secret,
            ]);

        $responseBody = $response->body();
        $decodedResponse = json_decode($responseBody);

        if (json_last_error() !== JSON_ERROR_NONE) {
            Log::error('DC520 oauth token response was not valid JSON', [
                'code' => 'DC520',
                'provider' => $provider->name,
                'status' => $response->status(),
                'content_type' => $response->header('Content-Type'),
                'response_body' => mb_substr($responseBody, 0, 2000),
            ]);

            throw new Exception('Something went wrong, please try again later (#DC520)');
        }

        if (! $response->ok() || isset($decodedResponse->error) || isset($decodedResponse->name)) {
            Log::warning('OAuth token exchange returned an error response', [
                'code' => 'DC521',
                'provider' => $provider->name,
                'status' => $response->status(),
                'content_type' => $response->header('Content-Type'),
                'response_body' => mb_substr($responseBody, 0, 2000),
                'oauth_error' => $decodedResponse->error ?? $decodedResponse->name ?? null,
            ]);
        }

        return $decodedResponse;
    }

    private function handleError(string $error): void
    {
        $rawError = $error;
        $logLevel = 'warning';
        $code = 'DC410';

        switch ($error) {
            case 'access_denied':
                $error = 'Authorization was denied by client';
                break;

            case 'invalid_client':
                $error = 'Something went wrong, please try again later (#DC511)';
                $logLevel = 'error';
                $code = 'DC511';
                break;

            case 'invalid_grant':
                $error = 'Authorization code expired/invalid, please authorize again (#DC412)';
                $code = 'DC412';
                break;

            default:
                $error = 'Something went wrong, please try again later (#DC510)';
                $logLevel = 'error';
                $code = 'DC510';
        }

        Log::{$logLevel}('OAuth flow failed', [
            'code' => $code,
            'provider' => 'Nightbot',
            'oauth_error' => $error,
            'raw_error' => $rawError,
        ]);

        throw new Exception($error);
    }
}
