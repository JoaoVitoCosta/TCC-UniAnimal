<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ColeiraResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'codigo'                    => $this->codigo,
            'latitude'                  => $this->latitude !== null ? (float) $this->latitude : null,
            'longitude'                 => $this->longitude !== null ? (float) $this->longitude : null,
            'precisao'                  => $this->precisao !== null ? (float) $this->precisao : null,
            'localizacao_atualizada_em' => $this->localizacao_atualizada_em?->toIso8601String(),
            'bateria_percentual'        => $this->bateria_percentual,
            'bateria_status'            => $this->bateria_status,
            'bateria_status_label'      => $this->bateria_status_label,
            'status'                    => $this->status,
            'status_label'              => $this->status_label,
        ];
    }
}
