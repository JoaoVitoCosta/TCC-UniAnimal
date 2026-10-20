<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Orcamento extends Model
{
    protected $table = 'orcamentos';

    protected $fillable = ['nome', 'email', 'telefone', 'porte', 'quantidade', 'observacoes', 'status'];

    protected $casts = [
        'quantidade' => 'integer',
    ];
}
