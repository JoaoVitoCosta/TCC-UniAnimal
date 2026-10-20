<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\AlertaResource;
use App\Http\Resources\AnimalResource;
use App\Http\Resources\ColeiraResource;
use App\Http\Resources\LocalizacaoResource;
use App\Models\Alerta;
use Illuminate\Http\Request;

/**
 * Dados reais para o app Flutter.
 *
 * O "tutor" já foi resolvido e validado pelo middleware api.token a partir
 * do banco de dados. Cada consulta é escopada por tutor_id, então um token
 * nunca enxerga dados de outra conta.
 */
class DashboardController extends Controller
{
    /** Relações carregadas em toda resposta de animal (mesma forma em todos os endpoints). */
    public const RELACOES = ['coleira', 'ultimoBatimento', 'ultimaTemperatura'];

    /**
     * Animais visíveis para o token atual. O login é feito pelo código de
     * UMA coleira, então o token só enxerga o animal dessa coleira. Tokens
     * sem coleira_id (legado) continuam vendo todos os animais do tutor.
     */
    private function animaisDoToken(Request $request)
    {
        $tutor = $request->attributes->get('tutor');
        $apiToken = $request->attributes->get('api_token');

        $query = $tutor->animais()->with(self::RELACOES)->withCount('alertasAbertos');

        if ($apiToken?->coleira_id) {
            $query->whereHas('coleira', fn ($q) => $q->whereKey($apiToken->coleira_id));
        }

        return $query;
    }

    private function animalOu404(Request $request, int $animalId)
    {
        // 404 (não 403): não confirmamos nem negamos a existência do
        // registro para outra conta, apenas que não pertence a esta.
        return $this->animaisDoToken($request)->find($animalId)
            ?? abort(response()->json(['message' => 'Animal não encontrado.'], 404));
    }

    public function me(Request $request)
    {
        $tutor = $request->attributes->get('tutor');

        return response()->json([
            'id'            => $tutor->id,
            'nome'          => $tutor->nome,
            'email'         => $tutor->email,
            'telefone'      => $tutor->telefone,
            'animais_count' => $this->animaisDoToken($request)->count(),
        ]);
    }

    /** Animal(is) acessíveis pelo token — na prática, o da coleira do login. */
    public function animais(Request $request)
    {
        return AnimalResource::collection($this->animaisDoToken($request)->get());
    }

    public function animal(Request $request, int $animalId)
    {
        return new AnimalResource($this->animalOu404($request, $animalId));
    }

    /**
     * Últimas 10 leituras de batimento (tabela leituras_saude), da mais
     * antiga para a mais recente — pronto para desenhar o gráfico.
     */
    public function batimentos(Request $request, int $animalId)
    {
        $animal = $this->animalOu404($request, $animalId);
        $leituras = $animal->batimentos()->take(10)->get()->reverse()->values();

        return response()->json([
            'batimentos' => $leituras->map(fn ($l) => [
                'batimento_cardiaco' => $l->batimento_cardiaco,
                'temperatura'        => $l->temperatura,
                'registrado_em'      => $l->registrado_em?->toIso8601String(),
            ]),
        ]);
    }

    public function localizacoes(Request $request, int $animalId)
    {
        $animal = $this->animalOu404($request, $animalId);

        if (! $animal->coleira) {
            return response()->json(['message' => 'Coleira não encontrada para este animal.'], 404);
        }

        return response()->json([
            'coleira'       => new ColeiraResource($animal->coleira),
            'localizacoes'  => LocalizacaoResource::collection($animal->coleira->localizacoes()->take(50)->get()),
            'zonas_seguras' => $animal->zonasSeguras()->where('ativa', true)
                ->get(['id', 'nome', 'latitude', 'longitude', 'raio_metros']),
        ]);
    }

    /**
     * GET /animais/{id}/alertas — alertas gerados pela telemetria, mais
     * recentes primeiro. ?abertos=1 traz só os que ainda não foram resolvidos.
     */
    public function alertas(Request $request, int $animalId)
    {
        $animal = $this->animalOu404($request, $animalId);

        $alertas = Alerta::where('animal_id', $animal->id)
            ->when($request->boolean('abertos'), fn ($q) => $q->abertos())
            ->latest()->take(50)->get();

        return response()->json([
            'abertos'   => Alerta::where('animal_id', $animal->id)->abertos()->count(),
            'nao_lidos' => Alerta::where('animal_id', $animal->id)->abertos()->naoLidos()->count(),
            'alertas'   => AlertaResource::collection($alertas),
        ]);
    }

    /** POST /alertas/{id}/lido — o tutor viu o alerta. */
    public function marcarAlertaLido(Request $request, int $alertaId)
    {
        $alerta = Alerta::find($alertaId);

        // Só alertas de animais que este token enxerga.
        if (! $alerta || ! $this->animaisDoToken($request)->whereKey($alerta->animal_id)->exists()) {
            return response()->json(['message' => 'Alerta não encontrado.'], 404);
        }

        $alerta->lido_em ??= now();
        $alerta->save();

        return new AlertaResource($alerta);
    }
}
