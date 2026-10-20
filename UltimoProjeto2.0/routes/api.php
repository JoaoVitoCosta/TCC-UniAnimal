<?php

use App\Http\Controllers\Api\AnimalController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\IotController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API v1
|--------------------------------------------------------------------------
| Dois clientes, duas autenticações:
|   • App Flutter  → login pelo código da coleira → token (api.token)
|   • Coleira IoT  → chave do dispositivo gravada no firmware (iot.coleira)
| Nada é validado só no cliente; toda regra vive no backend.
*/

Route::prefix('v1')->group(function () {

    /* ── App Flutter ─────────────────────────────────────────────── */

    // Login por código da coleira. Throttle dedicado para dificultar
    // força bruta sobre códigos de coleira.
    Route::post('/auth/coleira', [AuthController::class, 'loginPorColeira'])
        ->middleware('throttle:10,1');

    Route::middleware('api.token')->group(function () {
        Route::post('/auth/logout', [AuthController::class, 'logout']);

        Route::get('/me', [DashboardController::class, 'me']);
        Route::get('/animais', [DashboardController::class, 'animais']);
        Route::post('/animais', [AnimalController::class, 'store']);
        Route::get('/animais/{animal}', [DashboardController::class, 'animal'])->whereNumber('animal');
        Route::get('/animais/{animal}/batimentos', [DashboardController::class, 'batimentos'])->whereNumber('animal');
        Route::get('/animais/{animal}/localizacoes', [DashboardController::class, 'localizacoes'])->whereNumber('animal');
        Route::get('/animais/{animal}/alertas', [DashboardController::class, 'alertas'])->whereNumber('animal');
        Route::post('/alertas/{alerta}/lido', [DashboardController::class, 'marcarAlertaLido'])->whereNumber('alerta');
    });

    /* ── Coleira (hardware IoT) — ver IOT_INTEGRACAO.md ─────────── */

    Route::prefix('iot')->middleware(['iot.coleira', 'throttle:120,1'])->group(function () {
        Route::get('/config', [IotController::class, 'config']);
        Route::post('/telemetria', [IotController::class, 'telemetria']);
        Route::post('/telemetria/lote', [IotController::class, 'lote']);
    });
});
