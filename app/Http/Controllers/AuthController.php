<?php

namespace App\Http\Controllers;

use App\OAuth\OAuthHandler;
use Exception;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function authHandler(Request $request, string $service)
    {
        try {
            $oauthHandler = new OAuthHandler($service);

            return $oauthHandler->runAuth($request);
        } catch (Exception $exception) {
            report($exception);

            throw $exception;
        }
    }
}
