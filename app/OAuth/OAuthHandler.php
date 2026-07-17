<?php

namespace App\OAuth;

use Carbon\Carbon;
use Exception;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;

class OAuthHandler
{
    public $provider;

    public function __construct($strService)
    {
        $this->setProvider($strService);
    }

    public function runAuth($request)
    {
        // If code is set, user authorized or app
        if ($request->input('code') !== null) {
            // Validate state
            if (
                $request->input('state') === null ||
                ! $request->session()->has('state') ||
                $request->input('state') != $request->session()->pull('state')
            ) {
                Log::warning('DC401 oauth state validation failed', [
                    'code' => 'DC401',
                    'provider' => $this->provider->name ?? null,
                    'has_state' => $request->input('state') !== null,
                    'session_has_state' => $request->session()->has('state'),
                ]);

                throw new Exception('Invalid state parameter (#DC401)');
            }

            // Get access tokens with access code
            $oTokens = $this->getTokens($request->input('code'));
            if (isset($oTokens->error)) {
                $this->handleError($oTokens->error);
            } // Bungie error field
            if (isset($oTokens->name)) {
                $this->handleError($oTokens->name);
            } // Nightbot error field

            // Save tokens
            $OAuthSession = new OAuthSession;
            $OAuthSession->access_token = $oTokens->access_token;
            $OAuthSession->refresh_token = $oTokens->refresh_token;
            $OAuthSession->expires_in = Carbon::now()->addSeconds($oTokens->expires_in);
            $OAuthSession->refresh_expires_in = Carbon::now()->addSeconds(isset($oTokens->refresh_expires_in) ? $oTokens->refresh_expires_in : 5184000); // 60 Days default.
            $OAuthSession->provider_id = $this->provider->id;

            // Bungie includes membershipId in Token response, I guess we should save it directly here..
            if (isset($oTokens->membership_id)) {
                $OAuthSession->identifier = $oTokens->membership_id;
            }

            $OAuthSession->save();
            $request->session()->put($this->provider->name.'-auth', $OAuthSession->id);

            Redirect::to($this->provider->local_redirect)->send();

            return $OAuthSession->access_token;
        }

        // If user denied authorization an error will be returned
        elseif ($request->input('error') !== null) {
            $this->handleError($request->input('error'));
        }

        // Else it will be a new auth request
        else {
            // Go Auth!
            Redirect::to($this->getAuthUrl())->send();
        }

        return false;
    }

    /*
    * getAuthUrl
    * Build the auth url and save state to session
    * return (string) auth url
    */
    public function getAuthUrl()
    {
        // Create and save state
        $strState = $this->generateState();
        Session::put('state', $strState);

        // Build url
        return $this->provider->auth_url.'&state='.$strState.'&client_id='.$this->provider->client_id
        .(isset($this->provider->scope) ? '&scope='.$this->provider->scope : '')
        .(isset($this->provider->redirect_url) ? '&redirect_uri='.urlencode($this->provider->redirect_url) : '');
    }

    /*
    * generateState
    * Generate random string to validate user
    * return (string) random string
    */
    private function generateState()
    {
        return sha1(time().rand(1, 9999));
    }

    /*
    * getTokens
    * Requests tokens to auth server
    * required (string) code / refresh token
    * optional (boolean) refresh, true for refresh token, false for code
    */
    private function getTokens($strCode, $bRefresh = false)
    {
        // Guzzle should be easy to use hmm
        $oClient = new Client([
            'http_errors' => false,
            'verify' => false,
            'headers' => [
                'User-Agent' => config('destinycommand.user_agent'),
            ],
        ]);

        $oResponse = $oClient->request('POST', $this->provider->token_url, [
            'form_params' => [
                'grant_type' => $bRefresh === false ? 'authorization_code' : 'refresh_token',
                $bRefresh === false ? 'code' : 'refresh_token' => $strCode,
                'client_id' => $this->provider->client_id,
                'client_secret' => $this->provider->client_secret,
            ],
        ]);

        $responseBody = $oResponse->getBody()->getContents();
        $decodedResponse = json_decode($responseBody);

        if (json_last_error() !== JSON_ERROR_NONE) {
            Log::error('DC520 oauth token response was not valid JSON', [
                'code' => 'DC520',
                'provider' => $this->provider->name ?? null,
                'status' => $oResponse->getStatusCode(),
                'content_type' => $oResponse->getHeaderLine('Content-Type'),
                'response_body' => mb_substr($responseBody, 0, 2000),
            ]);

            throw new Exception('Something went wrong, please try again later (#DC520)');
        }

        if ($oResponse->getStatusCode() !== 200 || isset($decodedResponse->error) || isset($decodedResponse->name)) {
            Log::warning('OAuth token exchange returned an error response', [
                'code' => 'DC521',
                'provider' => $this->provider->name ?? null,
                'status' => $oResponse->getStatusCode(),
                'content_type' => $oResponse->getHeaderLine('Content-Type'),
                'response_body' => mb_substr($responseBody, 0, 2000),
                'oauth_error' => $decodedResponse->error ?? $decodedResponse->name ?? null,
            ]);
        }

        return $decodedResponse;
    }

    /*
    * isAuthValid
    * Checks if Authsession is still valid
    * required (int) OAuthSessionId
    * return (boolean) true = valid / false = invalid
    */
    public function isAuthValid($iAuthSessionId)
    {
        if ($OAuthSession = OAuthSession::find($iAuthSessionId)) {
            // Access token still valid
            if ($OAuthSession->expires_in > Carbon::now()) {
                return true;
            }

            // Access token expired, check if we can refresh it
            elseif ($OAuthSession->refresh_expires_in > Carbon::now()) {
                // refresh the token
                $this->setProvider($OAuthSession->provider_id, true);
                $oTokens = $this->getTokens($OAuthSession->refresh_token, true);
                if (isset($oTokens->error)) {
                    $this->handleError($oTokens->error);
                } // Bungie error field
                if (isset($oTokens->name)) {
                    $this->handleError($oTokens->name);
                } // Nightbot error field

                // Save new tokens
                $OAuthSession->access_token = $oTokens->access_token;
                $OAuthSession->refresh_token = $oTokens->refresh_token;
                $OAuthSession->expires_in = Carbon::now()->addSeconds($oTokens->expires_in);
                $OAuthSession->refresh_expires_in = Carbon::now()->addSeconds(isset($oTokens->refresh_expires_in) ? $oTokens->refresh_expires_in : 5184000); // 60 Days default.
                $OAuthSession->save();

                return true;
            }
        }

        return false;
    }

    /*
    * setProvider
    * Set the provider
    * required (int/string) Id or Name of provider
    * optional (boolean) if first parameter is an Id set this value true
    */
    public function setProvider($strService, $id = false)
    {
        $this->provider = $id === false ? OAuthProvider::where('name', $strService)->firstOrFail() : OAuthProvider::findOrFail($strService);
    }

    private function handleError($strError)
    {
        $rawError = $strError;
        $logLevel = 'warning';
        $code = 'DC410';

        switch ($strError) {
            case 'access_denied':
                $strError = 'Authorization was denied by client';
                break;

            case 'invalid_client':
                $strError = 'Something went wrong, please try again later (#DC511)';
                $logLevel = 'error';
                $code = 'DC511';
                break;

            case 'invalid_grant':
                $strError = 'Authorization code expired/invalid, please authorize again (#DC412)';
                $code = 'DC412';
                break;

            default:
                $strError = 'Something went wrong, please try again later (#DC510)';
                $logLevel = 'error';
                $code = 'DC510';
        }

        Log::{$logLevel}('OAuth flow failed', [
            'code' => $code,
            'provider' => $this->provider->name ?? null,
            'oauth_error' => $strError,
            'raw_error' => $rawError,
        ]);

        throw new Exception($strError);
    }
}
