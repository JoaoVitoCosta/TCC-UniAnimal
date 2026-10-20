<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AnimalResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'             => $this->id,
            'nome'           => $this->nome,
            'especie'        => $this->especie,
            'especie_emoji'  => $this->especie_emoji,
            'raca'           => $this->raca,
            'porte'          => $this->porte,
            'porte_label'    => $this->porte_label,
            'idade'          => $this->idade,
            'peso'           => $this->peso !== null ? (float) $this->peso : null,
            'status'         => $this->status,
            'status_label'   => $this->status_label,
            'status_color'   => $this->status_color,
            'coleira'        => $this->whenLoaded('coleira', fn () => new ColeiraResource($this->coleira)),
            // Últimas leituras da coleira (tabela leituras_saude; null se ainda não houver).
            'batimento_cardiaco'          => $this->whenLoaded('ultimoBatimento', fn () => $this->ultimoBatimento?->batimento_cardiaco),
            'batimento_registrado_em'     => $this->whenLoaded('ultimoBatimento', fn () => $this->ultimoBatimento?->registrado_em?->toIso8601String()),
            'temperatura'                 => $this->whenLoaded('ultimaTemperatura', fn () => $this->ultimaTemperatura?->temperatura),
            'temperatura_registrada_em'   => $this->whenLoaded('ultimaTemperatura', fn () => $this->ultimaTemperatura?->registrado_em?->toIso8601String()),
            'alertas_abertos'             => $this->whenCounted('alertasAbertos'),
        ];
    }
}
