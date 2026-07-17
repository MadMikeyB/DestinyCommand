<?php

namespace App\Http\Middleware;

use App\Models\OAuth\OAuthProvider;
use App\Services\BungieService;
use App\Services\NightbotService;
use Closure;
use Illuminate\Http\Request;

class CheckOAuth
{
    /**
     * Run the request filter.
     *
     * @param  Request  $request
     * @param  string  $strService
     * @return mixed
     */
    public function handle($request, Closure $next, $strService)
    {
        if ($strService === 'Bungie') {
            OAuthProvider::firstOrCreate([
                'name' => 'Bungie',
            ]);

            if ($request->session()->has('Bungie-auth') && (new BungieService)->isAuthValid($request->session()->get('Bungie-auth'))) {
                return $next($request);
            }

            return redirect('/auth/bungie/redirect');
        }

        if ($strService === 'Nightbot') {
            OAuthProvider::firstOrCreate([
                'name' => 'Nightbot',
            ]);

            if ($request->session()->has('Nightbot-auth') && (new NightbotService)->isAuthValid($request->session()->get('Nightbot-auth'))) {
                return $next($request);
            }

            return redirect('/auth/nightbot/redirect');
        }

        abort(404);
    }
}
