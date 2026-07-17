<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\OAuth\OAuthProvider;
use App\Models\OAuth\OAuthSession;
use App\Services\BungieService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class BungieController extends Controller
{
    public function redirectToBungie()
    {
        $bungieAuthUrl = 'https://www.bungie.net/en/OAuth/Authorize?'.http_build_query([
            'client_id' => config('services.bungie.client_id'),
            'response_type' => 'code',
            'redirect_uri' => config('services.bungie.redirect'),
        ]);

        return redirect($bungieAuthUrl);
    }

    public function legacyBungieAuth(Request $request)
    {
        if ($request->input('code') !== null || $request->input('error') !== null) {
            return $this->handleBungieCallback($request);
        }

        return $this->redirectToBungie();
    }

    public function handleBungieCallback(Request $request)
    {
        $code = $request->get('code');

        if (! $code) {
            Log::error('No authorization code provided in Bungie callback.');

            abort(400, 'Authorization code is missing.');
        }

        $bungieService = new BungieService;
        $tokenResponse = $bungieService->getTokenData($code);
        $tokenData = $tokenResponse['json'] ?? [];

        if (($tokenResponse['status'] ?? 500) >= 400 || ! isset($tokenData['access_token'])) {
            Log::error('Failed to exchange authorization code for access token.', [
                'status' => $tokenResponse['status'] ?? null,
                'body' => $tokenData,
                'raw_body' => $tokenResponse['body'] ?? null,
            ]);

            abort(500, 'Failed to authenticate with Bungie.');
        }

        $accessToken = $bungieService->getAccessToken($tokenData);
        $bungieUser = $bungieService->getCurrentBungieNetUser($accessToken);
        $destinyData = $bungieService->fetchMembershipsForCurrentUser($accessToken);

        Log::info('User logged in via Bungie OAuth', [
            'bungie_membership_id' => $bungieUser['membershipId'] ?? null,
            'destiny_memberships' => $destinyData['Response']['destinyMemberships'] ?? [],
        ]);

        $provider = OAuthProvider::firstOrCreate([
            'name' => 'Bungie',
        ]);

        if (! $provider->local_redirect) {
            $provider->local_redirect = '/dashboard';
            $provider->save();
        }

        $oauthSession = new OAuthSession;
        $oauthSession->access_token = $accessToken;
        $oauthSession->refresh_token = $bungieService->getRefreshToken($tokenData);
        $oauthSession->expires_in = Carbon::now()->addSeconds($tokenData['expires_in'] ?? 0);
        $oauthSession->refresh_expires_in = Carbon::now()->addSeconds($tokenData['refresh_expires_in'] ?? 5184000);
        $oauthSession->provider_id = $provider->id;
        $oauthSession->identifier = $bungieService->getMembershipIdFromToken($tokenData)
            ?? ($bungieUser['membershipId'] ?? null);
        $oauthSession->save();

        $request->session()->put('Bungie-auth', $oauthSession->id);

        return redirect('/dashboard');
    }
}
