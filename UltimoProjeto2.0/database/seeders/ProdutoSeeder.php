<?php

namespace Database\Seeders;

use App\Models\Produto;
use Illuminate\Database\Seeder;

/**
 * Catálogo de coleiras — fonte única dos preços exibidos na home, em
 * /produtos (parcela calculada a partir do preço) e na página Oferta.
 */
class ProdutoSeeder extends Seeder
{
    public function run(): void
    {
        $produtos = [
            ['porte' => 'pequeno', 'nome' => 'UniCollar Mini', 'preco' => 799.00,  'preco_promocional' => null,
             'descricao' => 'Coleira inteligente para pets de pequeno porte, com GPS, ECG e sensor de temperatura.'],
            ['porte' => 'medio',   'nome' => 'UniCollar Pro',  'preco' => 879.00,  'preco_promocional' => 799.00,
             'descricao' => 'Nosso modelo mais vendido: GPS, ECG, monitor de sono e alertas com IA.'],
            ['porte' => 'grande',  'nome' => 'UniCollar Max',  'preco' => 1099.00, 'preco_promocional' => null,
             'descricao' => 'Para animais de grande porte e uso rural, com alcance de GPS de até 5km.'],
        ];

        foreach ($produtos as $p) {
            Produto::updateOrCreate(['porte' => $p['porte']], $p + ['ativo' => true]);
        }
    }
}
