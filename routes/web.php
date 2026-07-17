<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BungieNameConverterController;
use App\Http\Controllers\CommandController;
use App\OAuth\OAuthHandler;
use Illuminate\Support\Facades\Route;

Route::get('/auth/{service}', [AuthController::class, 'authHandler']);
Route::get('/api/command', [CommandController::class, 'parseRequest']);
Route::match(['get', 'post'], '/tools/bungienameconverter', [BungieNameConverterController::class, 'convert']);

Route::prefix('dashboard')->middleware('CheckOAuth:Bungie')->group(function (): void {
    Route::view('/', 'dashboard.home');

    Route::prefix('settings')->group(function (): void {
        Route::get('/', fn () => 'settings');
        Route::middleware('CheckOAuth:Nightbot')->get('/nightbot', fn () => 'nightbot settings');
    });
});

Route::get('/dashboard/login', function () {
    $oauthHandler = new OAuthHandler('Bungie');

    return view('dashboard.login', [
        'auth_url' => $oauthHandler->getAuthUrl(),
    ]);
});

Route::get('/dashboard/settings/nightbot/login', function () {
    $oauthHandler = new OAuthHandler('Nightbot');

    return view('dashboard.nightbot.login', [
        'auth_url' => $oauthHandler->getAuthUrl(),
    ]);
});

Route::get('/', function () {
    return view('welcome');
});
