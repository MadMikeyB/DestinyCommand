<?php

use App\Http\Controllers\Auth\BungieController;
use App\Http\Controllers\Auth\NightbotController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BungieNameConverterController;
use App\Http\Controllers\CommandController;
use Illuminate\Support\Facades\Route;

Route::get('/auth/bungie/redirect', [BungieController::class, 'redirectToBungie']);
Route::get('/auth/bungie/callback', [BungieController::class, 'handleBungieCallback']);
Route::get('/auth/nightbot/redirect', [NightbotController::class, 'redirectToNightbot']);
Route::get('/auth/nightbot/callback', [NightbotController::class, 'handleNightbotCallback']);
Route::get('/auth/Bungie', [BungieController::class, 'legacyBungieAuth']);
Route::get('/auth/Nightbot', [NightbotController::class, 'legacyNightbotAuth']);
Route::get('/auth/{service}', [AuthController::class, 'authHandler']);
Route::get('/live/api/command', [CommandController::class, 'parseRequest']);
Route::get('/api/command', [CommandController::class, 'parseRequest']);
Route::match(['get', 'post'], '/tools/bungienameconverter', [BungieNameConverterController::class, 'convert']);

Route::prefix('dashboard')->middleware('CheckOAuth:Bungie')->group(function (): void {
    Route::view('/', 'dashboard.home');

    Route::prefix('settings')->group(function (): void {
        Route::get('/', fn () => 'settings');
        Route::middleware('CheckOAuth:Nightbot')->get('/nightbot', fn () => 'nightbot settings');
    });
});

Route::get('/dashboard/login', [BungieController::class, 'redirectToBungie']);
Route::get('/dashboard/settings/nightbot/login', [NightbotController::class, 'redirectToNightbot']);

Route::get('/', function () {
    return view('welcome');
});
