<?php

namespace App\Http\Middleware;

use App\Models\Tutor;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Garante que todo usuário autenticado tenha um Tutor 1:1 associado antes
 * de acessar a área privada. Protege contra contas antigas/importadas que
 * não passaram pelo fluxo normal de registro ou login com Google, evitando
 * um erro fatal (ex: "Call to a member function on null") na dashboard.
 */
class EnsureTutorExists
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        if ($user && ! $user->tutor) {
            Tutor::create([
                'user_id'  => $user->id,
                'nome'     => $user->name,
                'telefone' => '',
                'email'    => $user->email,
            ]);

            $user->unsetRelation('tutor');
        }

        return $next($request);
    }
}
