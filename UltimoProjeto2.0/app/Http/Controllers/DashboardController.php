<?php

namespace App\Http\Controllers;

use App\Models\Alerta;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $tutor = Auth::user()->tutor;

        $animais = $tutor->animais()
            ->with('coleira.ultimaLocalizacao')
            ->latest()
            ->get();

        // Alertas abertos gerados pela telemetria das coleiras (tabela alertas).
        $alertas = Alerta::whereIn('animal_id', $animais->pluck('id'))
            ->abertos()
            ->with('animal')
            ->orderByRaw("nivel = 'emergencia' desc")
            ->latest()
            ->get();

        $stats = [
            'total_animais'   => $animais->count(),
            'coleiras_ativas' => $animais->filter(fn ($a) => $a->coleira?->status === 'ativa')->count(),
            'alertas_ativos'  => $alertas->count(),
            'bateria_media'   => $animais->pluck('coleira.bateria_percentual')->filter()->avg(),
        ];

        return view('pages.dashboard', compact('tutor', 'animais', 'alertas', 'stats'));
    }

    /**
     * API JSON para refresh dinâmico dos cards.
     */
    public function stats()
    {
        $tutor = Auth::user()->tutor;
        $animais = $tutor->animais()->with('coleira')->get();

        return response()->json([
            'total_animais'   => $animais->count(),
            'coleiras_ativas' => $animais->filter(fn ($a) => $a->coleira?->status === 'ativa')->count(),
            'alertas_ativos'  => Alerta::whereIn('animal_id', $animais->pluck('id'))->abertos()->count(),
            'bateria_media'   => $animais->pluck('coleira.bateria_percentual')->filter()->avg(),
        ]);
    }

    /**
     * API JSON para o gráfico de frequência cardíaca: últimas 20 leituras
     * (tabela historico) do animal, da mais antiga para a mais recente.
     * Escopado ao tutor logado — animal de outra conta retorna 404.
     */
    public function batimentos(int $animalId)
    {
        $animal = Auth::user()->tutor->animais()->findOrFail($animalId);

        $leituras = $animal->batimentos()->take(20)->get()->reverse()->values();

        return response()->json([
            'animal'     => $animal->nome,
            'batimentos' => $leituras->map(fn ($h) => [
                'bpm'           => $h->batimento_cardiaco,
                'registrado_em' => $h->registrado_em?->toIso8601String(),
            ]),
        ]);
    }
}
