<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SiteController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DepoimentoController;
use App\Http\Controllers\ContatoController;
use App\Http\Controllers\OrcamentoController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AnimalController;
use App\Http\Controllers\ColeiraController;
use App\Http\Controllers\PerfilController;

/*
|--------------------------------------------------------------------------
| Rotas Públicas
|--------------------------------------------------------------------------
*/

Route::get('/', [SiteController::class, 'home'])->name('home');
Route::get('/sobre', [SiteController::class, 'sobre'])->name('sobre');
Route::get('/diferenciais', [SiteController::class, 'diferenciais'])->name('diferenciais');
Route::get('/produtos', [SiteController::class, 'produtos'])->name('produtos');
Route::get('/oferta', [SiteController::class, 'oferta'])->name('oferta');

// Depoimentos
Route::get('/depoimentos', [DepoimentoController::class, 'index'])->name('depoimentos');
Route::get('/depoimentos/novo', [DepoimentoController::class, 'create'])->name('depoimentos.novo');
Route::post('/depoimentos', [DepoimentoController::class, 'store'])->name('depoimentos.store');

// Contactos
Route::get('/contactos', [ContatoController::class, 'index'])->name('contactos');
Route::post('/contactos', [ContatoController::class, 'store'])->name('contactos.store');

// Orçamento WhatsApp
Route::get('/orcamento', [OrcamentoController::class, 'create'])->name('orcamento');
Route::post('/orcamento', [OrcamentoController::class, 'store'])->name('orcamento.store');

/*
|--------------------------------------------------------------------------
| Autenticação
|--------------------------------------------------------------------------
*/

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
    Route::get('/registo', [AuthController::class, 'showRegister'])->name('registo');
    Route::post('/registo', [AuthController::class, 'register'])->name('registo.post');

    Route::get('/auth/google', [AuthController::class, 'redirectToGoogle'])->name('auth.google');
    Route::get('/auth/google/callback', [AuthController::class, 'handleGoogleCallback'])->name('auth.google.callback');
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

/*
|--------------------------------------------------------------------------
| Área Privada do Cliente (autenticado)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'ensure.tutor'])->group(function () {

    // Dashboard exclusiva do cliente
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/api/dashboard/stats', [DashboardController::class, 'stats'])->name('dashboard.stats');
    Route::get('/api/dashboard/batimentos/{animal}', [DashboardController::class, 'batimentos'])
        ->whereNumber('animal')->name('dashboard.batimentos');

    // Meus Animais (CRUD escopado ao tutor autenticado)
    // Parâmetro forçado para "animal": o inflector do Laravel é em inglês e
    // NÃO reconhece o plural em português "animais" -> "animal" (produziria
    // "animai" por padrão), quebrando o route model binding em show/edit/
    // update/destroy. Ver RELATORIO_FINAL.md para detalhes.
    Route::resource('animais', AnimalController::class)
        ->parameters(['animais' => 'animal']);

    // Localização da coleira (GPS) — arquitetura preparada, sem IoT real
    Route::get('/animais/{animal}/coleira', [ColeiraController::class, 'show'])->name('coleira.show');
    Route::get('/api/animais/{animal}/coleira/historico', [ColeiraController::class, 'historico'])->name('coleira.historico');
    Route::get('/api/animais/{animal}/coleira/simular', [ColeiraController::class, 'simular'])->name('coleira.simular');

    // Perfil / Configurações
    Route::get('/perfil', [PerfilController::class, 'show'])->name('perfil');
    Route::put('/perfil', [PerfilController::class, 'update'])->name('perfil.update');
});
