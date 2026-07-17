<?php

namespace App\Http\Middleware;

use App\OAuth\OAuthHandler;
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
        $OAuthHandler = new OAuthHandler($strService);
        if ($request->session()->has($strService.'-auth')) {
            if ($OAuthHandler->isAuthValid($request->session()->get($strService.'-auth'))) {
                return $next($request);
            }
        }

        return redirect($OAuthHandler->provider->local_redirect.'/login');
    }
}
