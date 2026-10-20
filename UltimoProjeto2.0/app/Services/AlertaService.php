<?php

namespace App\Services;

use App\Models\Alerta;
use App\Models\Animal;
use App\Models\Coleira;

/**
 * Abre e resolve alertas sem duplicar: existe no máximo UM alerta aberto por
 * animal e por tipo. Enquanto a situação continua, o mesmo alerta é
 * atualizado (valor/mensagem, e nível se piorar); quando volta ao normal,
 * ele é resolvido. O status do animal acompanha os alertas abertos.
 */
class AlertaService
{
    /**
     * Abre (ou atualiza) o alerta do tipo. Retorna [Alerta, bool $novo].
     */
    public function abrir(
        Animal $animal,
        ?Coleira $coleira,
        string $tipo,
        string $nivel,
        string $titulo,
        string $mensagem,
        ?string $valor = null,
    ): array {
        $aberto = Alerta::where('animal_id', $animal->id)->abertos()->where('tipo', $tipo)->first();

        if ($aberto) {
            $piorou = $nivel === 'emergencia' && $aberto->nivel !== 'emergencia';
            $aberto->fill(compact('nivel', 'titulo', 'mensagem', 'valor'));
            // Se piorou, o tutor precisa ver de novo.
            if ($piorou) {
                $aberto->lido_em = null;
            }
            $aberto->save();

            return [$aberto, false];
        }

        $alerta = Alerta::create([
            'animal_id'  => $animal->id,
            'coleira_id' => $coleira?->id,
            'tipo'       => $tipo,
            'nivel'      => $nivel,
            'titulo'     => $titulo,
            'mensagem'   => $mensagem,
            'valor'      => $valor,
        ]);

        return [$alerta, true];
    }

    /** Resolve o alerta aberto do tipo (se houver). */
    public function resolver(Animal $animal, string $tipo): void
    {
        Alerta::where('animal_id', $animal->id)->abertos()->where('tipo', $tipo)->update(['resolvido_em' => now()]);
    }

    /** saudavel / atencao / emergencia conforme os alertas abertos. */
    public function atualizarStatusDoAnimal(Animal $animal): void
    {
        $niveis = Alerta::where('animal_id', $animal->id)->abertos()->pluck('nivel');

        $status = match (true) {
            $niveis->contains('emergencia') => 'emergencia',
            $niveis->contains('alerta')     => 'atencao',
            default                         => 'saudavel',
        };

        if ($animal->status !== $status) {
            $animal->forceFill(['status' => $status])->save();
        }
    }

    /**
     * Marca como "sem sinal" as coleiras ativas que pararam de enviar
     * telemetria e abre o alerta correspondente. Usado pelo comando
     * coleiras:verificar-sinal (agendado a cada minuto).
     *
     * @return int quantas coleiras passaram para sem_sinal
     */
    public function verificarColeirasSemSinal(): int
    {
        $total = 0;

        Coleira::with('animal')
            ->where('status', 'ativa')
            ->whereNotNull('animal_id')
            ->whereNotNull('ultimo_contato_em')
            ->get()
            ->filter(fn (Coleira $c) => $c->ultimo_contato_em->lt($c->limiteSemSinal()))
            ->each(function (Coleira $coleira) use (&$total) {
                $coleira->forceFill(['status' => 'sem_sinal'])->save();

                $this->abrir(
                    $coleira->animal, $coleira, 'sem_sinal', 'alerta',
                    'Coleira sem sinal',
                    "Nenhum dado da coleira de {$coleira->animal->nome} desde "
                        . $coleira->ultimo_contato_em->format('d/m H:i') . '.',
                );
                $this->atualizarStatusDoAnimal($coleira->animal);
                $total++;
            });

        return $total;
    }
}
