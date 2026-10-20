<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LocalizacaoResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'latitude'      => (float) $this->latitude,
            'longitude'     => (float) $this->longitude,
            'precisao'      => $this->precisao !== null ? (float) $this->precisao : null,
            'registrado_em' => $this->registrado_em?->toIso8601String(),
        ];
    }
}
