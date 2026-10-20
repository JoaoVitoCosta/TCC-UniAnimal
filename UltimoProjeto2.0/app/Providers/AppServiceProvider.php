<?php

namespace App\Providers;

use App\Models\Animal;
use App\Policies\AnimalPolicy;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Database\QueryException;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Bind do Repositório de Animais
        $this->app->bind(
            \App\Repositories\AnimalRepositoryInterface::class,
            \App\Repositories\AnimalRepository::class
        );
    }

    public function boot(): void
    {
        Gate::policy(Animal::class, AnimalPolicy::class);

        // O grupo de middleware "api" (aplicado automaticamente a
        // routes/api.php) inclui throttle:api. O Laravel 11 só registra
        // esse limiter sozinho quando o projeto roda `php artisan
        // install:api`; como habilitamos a API manualmente em
        // bootstrap/app.php, precisamos declarar o limiter aqui — sem
        // isso, QUALQUER rota da API quebra em runtime com
        // "Rate limiter [api] is not defined".
        RateLimiter::for('api', function ($request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });

        // A API é consumida só pelo nosso próprio app Flutter (não é uma
        // API pública JSON:API) — desligamos o wrapper "data" padrão dos
        // Resources para simplificar o parsing no cliente: um único
        // animal vira {"id":..., "nome":...} direto, e uma lista vira
        // [{...}, {...}] direto, sem {"data": [...]}.
        JsonResource::withoutWrapping();

        // Ajuste de sql_mode do MySQL — só faz sentido durante requisições
        // HTTP reais, nunca durante comandos artisan (migrate, key:generate,
        // etc. rodam antes do banco existir ou não precisam dele). Também
        // envolvido em try/catch: se a conexão cair por qualquer motivo,
        // isso NUNCA deve derrubar a aplicação inteira.
        if (! $this->app->runningInConsole() && config('database.default') === 'mysql') {
            try {
                DB::statement("SET sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''))");
            } catch (\Throwable $e) {
                Log::warning('Não foi possível ajustar o sql_mode do MySQL: ' . $e->getMessage());
            }
        }
    }
}
