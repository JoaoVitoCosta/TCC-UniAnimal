<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\AnimalResource;
use App\Models\Coleira;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

/**
 * Cadastro de animal pelo app Flutter.
 *
 * Mesma regra de negócio do site (AnimalController@store): se o código da
 * coleira não existir, ela é criada; se existir, precisa estar disponível.
 */
class AnimalController extends Controller
{
    public function store(Request $request)
    {
        $tutor = $request->attributes->get('tutor');

        $data = $request->validate([
            'nome'           => ['required', 'string', 'max:100'],
            'especie'        => ['required', 'string', 'max:60'],
            'raca'           => ['nullable', 'string', 'max:100'],
            'porte'          => ['required', 'in:pequeno,medio,grande'],
            'idade'          => ['nullable', 'integer', 'min:0', 'max:50'],
            'peso'           => ['nullable', 'numeric', 'min:0.1', 'max:999'],
            'codigo_coleira' => ['required', 'string', 'max:50'],
        ]);

        $coleira = Coleira::obterOuCriar($data['codigo_coleira']);

        if (! $coleira->estaDisponivel()) {
            throw ValidationException::withMessages([
                'codigo_coleira' => 'Este código de coleira já está vinculado a outro animal.',
            ]);
        }

        $animal = $tutor->animais()->create([
            'nome'    => $data['nome'],
            'especie' => $data['especie'],
            'raca'    => $data['raca'] ?? null,
            'porte'   => $data['porte'],
            'idade'   => $data['idade'] ?? null,
            'peso'    => $data['peso'] ?? null,
            'status'  => 'saudavel',
        ]);

        $coleira->vincularAoAnimal($animal);

        return response()->json(
            new AnimalResource($animal->load(\App\Http\Controllers\Api\DashboardController::RELACOES)->loadCount('alertasAbertos')),
            201
        );
    }
}
