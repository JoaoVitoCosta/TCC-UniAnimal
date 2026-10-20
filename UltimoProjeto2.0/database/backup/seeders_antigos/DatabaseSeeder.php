<?php

namespace Database\Seeders;

use App\Models\Animal;
use App\Models\Coleira;
use App\Models\Tutor;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $clientesData = [
            [
                'user'  => ['name' => 'Ana Silva',    'email' => 'ana@email.com'],
                'tutor' => ['telefone' => '(19) 98888-1111', 'endereco' => 'Tambaú, SP'],
                'animais' => [
                    ['nome' => 'Thor', 'especie' => 'Cachorro', 'raca' => 'Pastor Alemão', 'porte' => 'grande',  'idade' => 6, 'peso' => 32.5, 'status' => 'saudavel'],
                    ['nome' => 'Mimi', 'especie' => 'Gato',     'raca' => 'Persa',          'porte' => 'pequeno', 'idade' => 2, 'peso' => 3.8,  'status' => 'atencao'],
                ],
            ],
            [
                'user'  => ['name' => 'Carlos Souza', 'email' => 'carlos@email.com'],
                'tutor' => ['telefone' => '(19) 97777-2222', 'endereco' => 'São Paulo, SP'],
                'animais' => [
                    ['nome' => 'Rex',     'especie' => 'Cachorro', 'raca' => 'Labrador', 'porte' => 'grande',  'idade' => 4, 'peso' => 28.0, 'status' => 'saudavel'],
                    ['nome' => 'Bolinha', 'especie' => 'Coelho',   'raca' => 'Anão',      'porte' => 'pequeno', 'idade' => 1, 'peso' => 1.2,  'status' => 'saudavel'],
                ],
            ],
            [
                'user'  => ['name' => 'Mariana Lima', 'email' => 'mariana@email.com'],
                'tutor' => ['telefone' => '(19) 96666-3333', 'endereco' => 'Campinas, SP'],
                'animais' => [
                    ['nome' => 'Luna', 'especie' => 'Cachorro', 'raca' => 'Golden Retriever', 'porte' => 'grande',  'idade' => 3, 'peso' => 25.0, 'status' => 'emergencia'],
                    ['nome' => 'Nemo', 'especie' => 'Gato',     'raca' => 'Siamês',           'porte' => 'pequeno', 'idade' => 4, 'peso' => 4.5,  'status' => 'saudavel'],
                ],
            ],
        ];

        foreach ($clientesData as $dados) {
            $user = User::firstOrCreate(
                ['email' => $dados['user']['email']],
                [
                    'name'     => $dados['user']['name'],
                    'password' => Hash::make('password'),
                ]
            );

            $tutor = Tutor::firstOrCreate(
                ['user_id' => $user->id],
                array_merge(
                    ['nome' => $dados['user']['name'], 'email' => $dados['user']['email']],
                    $dados['tutor']
                )
            );

            foreach ($dados['animais'] as $ad) {
                $animal = Animal::firstOrCreate(
                    ['nome' => $ad['nome'], 'tutor_id' => $tutor->id],
                    array_merge($ad, ['tutor_id' => $tutor->id])
                );

                $bateria = rand(20, 100);

                $coleira = Coleira::firstOrCreate(
                    ['animal_id' => $animal->id],
                    [
                        'codigo'                    => 'COL-' . strtoupper(substr(md5($animal->id . $animal->nome), 0, 8)),
                        'latitude'                  => -23.55 + (rand(-500, 500) / 10000),
                        'longitude'                 => -46.63 + (rand(-500, 500) / 10000),
                        'precisao'                  => rand(3, 12),
                        'localizacao_atualizada_em' => now(),
                        'bateria_percentual'        => $bateria,
                        'bateria_status'            => $bateria <= 15 ? 'critica' : ($bateria <= 35 ? 'baixa' : 'normal'),
                        'bateria_atualizada_em'     => now(),
                        'status'                    => 'ativa',
                        'ativada_em'                => now(),
                    ]
                );

                if ($coleira->localizacoes()->count() === 0) {
                    for ($i = 10; $i >= 0; $i--) {
                        $coleira->localizacoes()->create([
                            'latitude'      => $coleira->latitude + (rand(-100, 100) / 10000),
                            'longitude'     => $coleira->longitude + (rand(-100, 100) / 10000),
                            'precisao'      => rand(3, 12),
                            'registrado_em' => now()->subMinutes($i * 15),
                        ]);
                    }
                }
            }
        }

        // Estoque de códigos AINDA NÃO vinculados a nenhum animal — permite
        // testar: (a) cadastro de animal digitando um código válido em
        // estoque, e (b) o app tentando logar com um código que ainda não
        // foi vinculado (deve dar o erro correto, não autenticar).
        foreach (['COL-DEMO-0001', 'COL-DEMO-0002', 'COL-DEMO-0003'] as $codigoDisponivel) {
            Coleira::firstOrCreate(
                ['codigo' => $codigoDisponivel],
                ['status' => 'disponivel', 'bateria_percentual' => 100]
            );
        }

        $this->command?->info('Códigos de coleira já ativos (login funciona no app): '
            . Coleira::whereNotNull('animal_id')->pluck('codigo')->join(', '));
        $this->command?->info('Códigos em estoque, ainda sem vínculo: COL-DEMO-0001, COL-DEMO-0002, COL-DEMO-0003');
    }
}
