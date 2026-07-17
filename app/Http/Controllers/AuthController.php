<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Auth\BungieController;
use App\Http\Controllers\Auth\NightbotController;
use App\Services\NightbotService;
use Exception;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function authHandler(Request $request, string $service)
    {
        try {
            return match (strtolower($service)) {
                'bungie' => app(BungieController::class)->legacyBungieAuth($request),
                'nightbot' => app(NightbotController::class)->legacyNightbotAuth($request, app(NightbotService::class)),
                default => abort(404),
            };
        } catch (Exception $exception) {
            report($exception);

            throw $exception;
        }
    }
}
