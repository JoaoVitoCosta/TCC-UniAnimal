<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Alerta;
use App\Models\Coleira;
use App\Models\Telemetria;
use App\Services\TelemetriaService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

/**
 * API da COLEIRA (hardware IoT). Autenticada por EnsureDeviceKey.
 * Documentação para o firmware: IOT_INTEGRACAO.md, na raiz do projeto.
 */
class IotController extends Controller
{
    /** Campos aceitos num envio. Todos opcionais; precisa vir ao menos uma medição. */
    public const REGRAS = [
        'batimento_cardiaco' => ['nullable', 'integer', 'between:10,400'],
        'temperatura'        => ['nullable', 'numeric', 'between:30,45'],
        'latitude'           => ['nullable', 'numeric', 'between:-90,90', 'required_with:longitude'],
        'longitude'          => ['nullable', 'numeric', 'between:-180,180', 'required_with:latitude'],
        'precisao'           => ['nullable', 'numeric', 'between:0,5000'],
        'bateria_percentual' => ['nullable', 'integer', 'between:0,100'],
        'firmware_versao'    => ['nullable', 'string', 'max:30'],
        'registrado_em'      => ['nullable', 'date'],
    ];

    private const MEDICOES = ['batimento_cardiaco', 'temperatura', 'latitude', 'bateria_percentual'];

    public function __construct(private TelemetriaService $telemetria) {}

    /** GET /api/v1/iot/config — a coleira busca sua configuração ao ligar. */
    public function config(Request $request)
    {
        /** @var Coleira $coleira */
        $coleira = $request->attributes->get('coleira');
        $animal = $coleira->animal;

        return response()->json([
            'coleira'             => $coleira->codigo,
            'animal'              => ['id' => $animal->id, 'nome' => $animal->nome, 'especie' => $animal->especie],
            'intervalo_envio_seg' => $coleira->intervalo_envio_seg,
            'servidor_hora'       => now()->toIso8601String(),
            'faixas'              => [
                'batimento'   => $animal->faixaReferencia(),
                'temperatura' => $animal->faixaTemperatura(),
            ],
            'zonas_seguras'       => $animal->zonasSeguras()->where('ativa', true)
                ->get(['nome', 'latitude', 'longitude', 'raio_metros']),
        ]);
    }

    /** POST /api/v1/iot/telemetria — um envio (o caso normal). */
    public function telemetria(Request $request)
    {
        $coleira = $request->attributes->get('coleira');
        $dados = $this->validar($request, $request->all(), $coleira);
        $resultado = $this->telemetria->processar($coleira, $dados);
        $this->registrar($request, $coleira, 'processada');

        return response()->json([
            'recebido'            => true,
            'leitura_id'          => $resultado['leitura_id'],
            'localizacao_id'      => $resultado['localizacao_id'],
            'alertas_gerados'     => collect($resultado['alertas'])->map(fn (Alerta $a) => $this->alertaResumo($a))->values(),
            'status_animal'       => $coleira->animal->fresh()->status,
            'proximo_envio_seg'   => $coleira->intervalo_envio_seg,
        ], 201);
    }

    /**
     * POST /api/v1/iot/telemetria/lote — várias medições de uma vez
     * ({ "leituras": [ {...}, {...} ] }), para quando a coleira ficou sem
     * internet e guardou os dados na memória. Processa da mais antiga
     * para a mais recente.
     */
    public function lote(Request $request)
    {
        $coleira = $request->attributes->get('coleira');

        $request->validate([
            'leituras'   => ['required', 'array', 'min:1', 'max:200'],
            'leituras.*' => ['array'],
        ], [
            'leituras.required' => 'Envie o campo "leituras" com a lista de medições.',
            'leituras.max'      => 'Máximo de 200 medições por lote.',
        ]);

        $leituras = collect($request->input('leituras'))
            ->map(fn ($l, $i) => $this->validar($request, $l, $coleira, "leituras.$i."))
            ->sortBy(fn ($l) => $l['registrado_em'] ?? now()->toIso8601String())
            ->values();

        $alertas = [];
        foreach ($leituras as $dados) {
            $alertas = array_merge($alertas, $this->telemetria->processar($coleira, $dados)['alertas']);
        }
        $this->registrar($request, $coleira, 'processada');

        return response()->json([
            'recebido'          => true,
            'processadas'       => $leituras->count(),
            'alertas_gerados'   => collect($alertas)->map(fn (Alerta $a) => $this->alertaResumo($a))->values(),
            'status_animal'     => $coleira->animal->fresh()->status,
            'proximo_envio_seg' => $coleira->intervalo_envio_seg,
        ], 201);
    }

    private function validar(Request $request, mixed $dados, Coleira $coleira, string $prefixo = ''): array
    {
        $dados = is_array($dados) ? $dados : [];
        $validator = Validator::make($dados, self::REGRAS, [
            'batimento_cardiaco.between' => 'batimento_cardiaco fora do intervalo aceito (10–400 bpm).',
            'temperatura.between'        => 'temperatura fora do intervalo aceito (30–45 °C).',
            'bateria_percentual.between' => 'bateria_percentual deve estar entre 0 e 100.',
        ]);

        $semMedicao = collect(self::MEDICOES)->every(fn ($c) => ! isset($dados[$c]));
        $validator->after(function ($v) use ($semMedicao) {
            if ($semMedicao) {
                $v->errors()->add('telemetria', 'Envie ao menos uma medição: batimento_cardiaco, temperatura, latitude/longitude ou bateria_percentual.');
            }
        });

        if ($validator->fails()) {
            $erros = collect($validator->errors()->messages())
                ->mapWithKeys(fn ($msgs, $campo) => [$prefixo . $campo => $msgs])->all();
            $this->registrar($request, $coleira, 'rejeitada', collect($erros)->flatten()->first());

            throw ValidationException::withMessages($erros);
        }

        return $validator->validated();
    }

    private function registrar(Request $request, Coleira $coleira, string $situacao, ?string $erro = null): void
    {
        Telemetria::create([
            'coleira_id'  => $coleira->id,
            'payload'     => $request->json()->all() ?: $request->all(),
            'ip'          => $request->ip(),
            'situacao'    => $situacao,
            'erro'        => $erro ? mb_substr($erro, 0, 255) : null,
            'recebido_em' => now(),
        ]);
    }

    private function alertaResumo(Alerta $a): array
    {
        return ['id' => $a->id, 'tipo' => $a->tipo, 'nivel' => $a->nivel, 'titulo' => $a->titulo];
    }
}
