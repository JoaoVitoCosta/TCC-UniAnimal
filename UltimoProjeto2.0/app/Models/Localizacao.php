<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Localizacao extends Model
{
    use HasFactory;

    protected $table = 'localizacoes';

    protected $fillable = ['coleira_id', 'latitude', 'longitude', 'precisao', 'registrado_em'];

    protected $casts = [
        'latitude'      => 'decimal:7',
        'longitude'     => 'decimal:7',
        'precisao'      => 'decimal:2',
        'registrado_em' => 'datetime',
    ];

    public function coleira()
    {
        return $this->belongsTo(Coleira::class);
    }
}
