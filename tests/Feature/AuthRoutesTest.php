<?php

namespace Tests\Feature;

use App\Models\OAuth\OAuthProvider;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class AuthRoutesTest extends TestCase
{
    use RefreshDatabase;

    public function test_explicit_bungie_redirect_route_redirects_to_bungie_authorize_url(): void
    {
        config()->set('services.bungie.client_id', 'bungie-client-id');
        config()->set('services.bungie.redirect', 'https://destinycommand.test/auth/bungie/callback');

        $response = $this->get('/auth/bungie/redirect');

        $response->assertRedirect();
        $this->assertStringStartsWith('https://www.bungie.net/en/OAuth/Authorize?', $response->headers->get('Location'));
        $this->assertStringContainsString('client_id=bungie-client-id', $response->headers->get('Location'));
        $this->assertStringContainsString('redirect_uri=https%3A%2F%2Fdestinycommand.test%2Fauth%2Fbungie%2Fcallback', $response->headers->get('Location'));
    }

    public function test_legacy_bungie_auth_route_still_redirects_to_bungie_authorize_url(): void
    {
        config()->set('services.bungie.client_id', 'bungie-client-id');
        config()->set('services.bungie.redirect', 'https://destinycommand.test/auth/bungie/callback');

        $response = $this->get('/auth/Bungie');

        $response->assertRedirect();
        $this->assertStringStartsWith('https://www.bungie.net/en/OAuth/Authorize?', $response->headers->get('Location'));
    }

    public function test_bungie_callback_creates_session_and_redirects_to_dashboard(): void
    {
        config()->set('services.bungie.api_key', 'bungie-api-key');
        config()->set('services.bungie.client_id', 'bungie-client-id');
        config()->set('services.bungie.client_secret', 'bungie-secret');
        config()->set('services.bungie.redirect', 'https://destinycommand.test/auth/bungie/callback');

        Http::fake([
            'https://www.bungie.net/Platform/App/OAuth/Token/' => Http::response([
                'access_token' => 'bungie-access-token',
                'refresh_token' => 'bungie-refresh-token',
                'expires_in' => 3600,
                'refresh_expires_in' => 7200,
                'membership_id' => '20650504',
            ], 200),
            'https://www.bungie.net/Platform/User/GetCurrentBungieNetUser/' => Http::response([
                'Response' => [
                    'membershipId' => '20650504',
                ],
            ], 200),
            'https://www.bungie.net/Platform/User/GetMembershipsForCurrentUser/' => Http::response([
                'Response' => [
                    'destinyMemberships' => [
                        ['membershipId' => '4611686018476297288'],
                    ],
                ],
            ], 200),
        ]);

        $response = $this->get('/auth/bungie/callback?code=test-bungie-code');

        $response->assertRedirect('/dashboard');
        $response->assertSessionHas('Bungie-auth');
        $this->assertDatabaseHas('oauth_providers', [
            'name' => 'Bungie',
        ]);
        $this->assertDatabaseHas('oauth_sessions', [
            'access_token' => 'bungie-access-token',
            'refresh_token' => 'bungie-refresh-token',
            'identifier' => '20650504',
        ]);
    }

    public function test_legacy_bungie_callback_route_still_creates_session(): void
    {
        config()->set('services.bungie.api_key', 'bungie-api-key');
        config()->set('services.bungie.client_id', 'bungie-client-id');
        config()->set('services.bungie.client_secret', 'bungie-secret');
        config()->set('services.bungie.redirect', 'https://destinycommand.test/auth/bungie/callback');

        Http::fake([
            'https://www.bungie.net/Platform/App/OAuth/Token/' => Http::response([
                'access_token' => 'legacy-bungie-access-token',
                'refresh_token' => 'legacy-bungie-refresh-token',
                'expires_in' => 3600,
                'refresh_expires_in' => 7200,
                'membership_id' => '20650504',
            ], 200),
            'https://www.bungie.net/Platform/User/GetCurrentBungieNetUser/' => Http::response([
                'Response' => [
                    'membershipId' => '20650504',
                ],
            ], 200),
            'https://www.bungie.net/Platform/User/GetMembershipsForCurrentUser/' => Http::response([
                'Response' => [
                    'destinyMemberships' => [
                        ['membershipId' => '4611686018476297288'],
                    ],
                ],
            ], 200),
        ]);

        $response = $this->get('/auth/Bungie?code=legacy-bungie-code');

        $response->assertRedirect('/dashboard');
        $response->assertSessionHas('Bungie-auth');
        $this->assertDatabaseHas('oauth_sessions', [
            'access_token' => 'legacy-bungie-access-token',
            'refresh_token' => 'legacy-bungie-refresh-token',
        ]);
    }

    public function test_explicit_nightbot_redirect_route_redirects_to_provider_auth_url(): void
    {
        OAuthProvider::create([
            'name' => 'Nightbot',
            'auth_url' => 'https://api.nightbot.tv/oauth2/authorize?response_type=code',
            'client_id' => 'nightbot-client-id',
            'scope' => 'channel',
            'redirect_url' => 'https://destinycommand.test/auth/nightbot/callback',
            'local_redirect' => '/dashboard/settings/nightbot',
        ]);

        $response = $this->get('/auth/nightbot/redirect');

        $response->assertRedirect();
        $this->assertStringStartsWith('https://api.nightbot.tv/oauth2/authorize?response_type=code', $response->headers->get('Location'));
        $this->assertStringContainsString('client_id=nightbot-client-id', $response->headers->get('Location'));
        $this->assertStringContainsString('redirect_uri=https%3A%2F%2Fdestinycommand.test%2Fauth%2Fnightbot%2Fcallback', $response->headers->get('Location'));
    }

    public function test_legacy_nightbot_auth_route_still_redirects_to_provider_auth_url(): void
    {
        OAuthProvider::create([
            'name' => 'Nightbot',
            'auth_url' => 'https://api.nightbot.tv/oauth2/authorize?response_type=code',
            'client_id' => 'nightbot-client-id',
            'scope' => 'channel',
            'redirect_url' => 'https://destinycommand.test/auth/nightbot/callback',
            'local_redirect' => '/dashboard/settings/nightbot',
        ]);

        $response = $this->get('/auth/Nightbot');

        $response->assertRedirect();
        $this->assertStringStartsWith('https://api.nightbot.tv/oauth2/authorize?response_type=code', $response->headers->get('Location'));
    }

    public function test_nightbot_callback_creates_session_and_redirects_to_settings(): void
    {
        OAuthProvider::create([
            'name' => 'Nightbot',
            'auth_url' => 'https://api.nightbot.tv/oauth2/authorize?response_type=code',
            'token_url' => 'https://api.nightbot.tv/oauth2/token',
            'client_id' => 'nightbot-client-id',
            'client_secret' => 'nightbot-secret',
            'scope' => 'channel',
            'redirect_url' => 'https://destinycommand.test/auth/nightbot/callback',
            'local_redirect' => '/dashboard/settings/nightbot',
        ]);

        Http::fake([
            'https://api.nightbot.tv/oauth2/token' => Http::response([
                'access_token' => 'nightbot-access-token',
                'refresh_token' => 'nightbot-refresh-token',
                'expires_in' => 3600,
                'refresh_expires_in' => 7200,
            ], 200),
        ]);

        $response = $this->withSession(['state' => 'nightbot-state'])
            ->get('/auth/nightbot/callback?code=nightbot-code&state=nightbot-state');

        $response->assertRedirect('/dashboard/settings/nightbot');
        $response->assertSessionHas('Nightbot-auth');
        $this->assertDatabaseHas('oauth_sessions', [
            'access_token' => 'nightbot-access-token',
            'refresh_token' => 'nightbot-refresh-token',
        ]);
    }

    public function test_legacy_nightbot_callback_route_still_creates_session(): void
    {
        OAuthProvider::create([
            'name' => 'Nightbot',
            'auth_url' => 'https://api.nightbot.tv/oauth2/authorize?response_type=code',
            'token_url' => 'https://api.nightbot.tv/oauth2/token',
            'client_id' => 'nightbot-client-id',
            'client_secret' => 'nightbot-secret',
            'scope' => 'channel',
            'redirect_url' => 'https://destinycommand.test/auth/nightbot/callback',
            'local_redirect' => '/dashboard/settings/nightbot',
        ]);

        Http::fake([
            'https://api.nightbot.tv/oauth2/token' => Http::response([
                'access_token' => 'legacy-nightbot-access-token',
                'refresh_token' => 'legacy-nightbot-refresh-token',
                'expires_in' => 3600,
                'refresh_expires_in' => 7200,
            ], 200),
        ]);

        $response = $this->withSession(['state' => 'legacy-nightbot-state'])
            ->get('/auth/Nightbot?code=nightbot-code&state=legacy-nightbot-state');

        $response->assertRedirect('/dashboard/settings/nightbot');
        $response->assertSessionHas('Nightbot-auth');
        $this->assertDatabaseHas('oauth_sessions', [
            'access_token' => 'legacy-nightbot-access-token',
            'refresh_token' => 'legacy-nightbot-refresh-token',
        ]);
    }
}
