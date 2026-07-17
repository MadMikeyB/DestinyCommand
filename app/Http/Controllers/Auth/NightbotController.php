<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\NightbotService;
use Exception;
use Illuminate\Http\Request;

class NightbotController extends Controller
{
    public function redirectToNightbot(NightbotService $nightbotService)
    {
        return redirect($nightbotService->getAuthUrl());
    }

    public function handleNightbotCallback(Request $request, NightbotService $nightbotService)
    {
        if (
            $request->input('state') === null ||
            ! $request->session()->has('state') ||
            $request->input('state') !== $request->session()->pull('state')
        ) {
            throw new Exception('Invalid state parameter (#DC401)');
        }

        if ($request->input('error') !== null) {
            throw new Exception('Authorization was denied by client');
        }

        $oauthSession = $nightbotService->handleCallback((string) $request->input('code'));
        $request->session()->put('Nightbot-auth', $oauthSession->id);

        return redirect('/dashboard/settings/nightbot');
    }

    public function legacyNightbotAuth(Request $request, NightbotService $nightbotService)
    {
        if ($request->input('code') !== null || $request->input('error') !== null) {
            return $this->handleNightbotCallback($request, $nightbotService);
        }

        return $this->redirectToNightbot($nightbotService);
    }
}
