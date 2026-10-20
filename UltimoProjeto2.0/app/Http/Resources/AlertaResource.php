<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AlertaResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'           => $this->id,
            'tipo'         => $this->tipo,
            'nivel'        => $this->nivel,
            'titulo'       => $this->titulo,
            'mensagem'     => $this->mensagem,
            'valor'        => $this->valor,
            'aberto'       => $this->resolvido_em === null,
            'lido'         => $this->lido_em !== null,
            'criado_em'    => $this->created_at?->toIso8601String(),
            'resolvido_em' => $this->resolvido_em?->toIso8601String(),
        ];
    }
}
