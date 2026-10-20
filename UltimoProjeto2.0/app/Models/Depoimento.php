<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Depoimento extends Model
{
    protected $table = 'depoimentos';

    protected $fillable = ['nome', 'email', 'telefone', 'depoimento', 'estrelas', 'aprovado'];

    protected $casts = [
        'aprovado' => 'boolean',
        'estrelas' => 'integer',
    ];

    public function scopeAprovados($query)
    {
        return $query->where('aprovado', true);
    }
}
