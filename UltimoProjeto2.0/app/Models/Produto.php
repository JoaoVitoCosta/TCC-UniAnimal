<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Produto extends Model
{
    protected $table = 'produtos';

    protected $fillable = ['nome', 'porte', 'descricao', 'preco', 'preco_promocional', 'imagem', 'ativo'];

    protected $casts = [
        'ativo'            => 'boolean',
        'preco'            => 'decimal:2',
        'preco_promocional'=> 'decimal:2',
    ];

    /** Máximo de parcelas sem juros exibido nos cards de preço. */
    public const PARCELAS = 12;

    public function scopeAtivos($query)
    {
        return $query->where('ativo', true);
    }

    /** Ex.: 799 -> "R$799" · 1099 -> "R$1.099" · 799.90 -> "R$799,90" */
    public static function formatarReais(float $valor): string
    {
        $casas = fmod($valor, 1) == 0.0 ? 0 : 2;

        return 'R$' . number_format($valor, $casas, ',', '.');
    }

    public function getPrecoFormatadoAttribute(): string
    {
        return self::formatarReais((float) $this->preco);
    }

    /** Texto da parcela calculado do preço — ex.: "ou 12x de R$66,58". */
    public function getParcelamentoAttribute(): string
    {
        $parcela = number_format((float) $this->preco / self::PARCELAS, 2, ',', '.');

        return 'ou ' . self::PARCELAS . 'x de R$' . $parcela;
    }
}
