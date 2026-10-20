<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Cada conta possui exatamente um Tutor: o próprio usuário autenticado.
 * Não existe mais CRUD administrativo de tutores — o registro é criado
 * automaticamente no cadastro (tradicional ou via Google).
 */
class Tutor extends Model
{
    use HasFactory;

    protected $table = 'tutores';

    protected $fillable = ['user_id', 'nome', 'telefone', 'email', 'endereco'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function animais()
    {
        return $this->hasMany(Animal::class);
    }

    public function apiTokens()
    {
        return $this->hasMany(ApiToken::class);
    }

    public function getAnimaisCountAttribute(): int
    {
        return $this->relationLoaded('animais')
            ? $this->animais->count()
            : $this->animais()->count();
    }
}
