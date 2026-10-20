<?php

namespace Database\Seeders;

use App\Models\Animal;
use App\Models\Coleira;
use App\Models\Depoimento;
use App\Models\Orcamento;
use App\Models\Tutor;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * Demonstração completa para uma instalação do zero:
 *   php artisan migrate:fresh --seed
 *
 * Mesmos tutores, animais e códigos de coleira do banco entregue
 * (database/unianimal_v2_instalar.sql). Senha de todas as contas demo: password
 */
class DatabaseSeeder extends Seeder
{
    public const SENHA_DEMO = 'password';

    public function run(): void
    {
        $this->call(ProdutoSeeder::class);

        $tutores = [
            [
                'nome' => 'Rafael Nogueira', 'email' => 'rafael.nogueira@petmail.com', 'telefone' => '(11) 91234-5678',
                'animais' => [
                    ['Belinha', 'Cachorro', 'Shih-tzu',      'pequeno', 3, 9.5,  'saudavel', 'COL-DC9FEAAA', -23.5098, -46.6744],
                    ['Max',     'Cachorro', 'Beagle',        'pequeno', 5, 12.0, 'atencao',  'COL-69CE7CC6', -23.5612, -46.6559],
                ],
            ],
            [
                'nome' => 'Juliana Marques', 'email' => 'juliana.marques@petmail.com', 'telefone' => '(21) 99876-5432',
                'animais' => [
                    ['Simba',   'Gato',     'Maine Coon',    'grande',  4, 7.2,  'saudavel', 'COL-2A1FD091', -22.9068, -43.1729],
                ],
            ],
            [
                'nome' => 'Eduardo Ramos', 'email' => 'eduardo.ramos@petmail.com', 'telefone' => '(31) 98765-1234',
                'animais' => [
                    ['Amora',   'Cachorro', 'Pinscher',      'pequeno', 2, 6.0,   'saudavel', 'COL-E348E3A4', -19.9167, -43.9345],
                    ['Zeca',    'Cavalo',   'Mangalarga',    'grande',  8, 420.0, 'saudavel', 'COL-AB7F406E', -19.8890, -43.9790],
                ],
            ],
            [
                'nome' => 'Patrícia Gomes', 'email' => 'patricia.gomes@petmail.com', 'telefone' => '(41) 99222-3344',
                'animais' => [
                    ['Bento',   'Cachorro', 'Border Collie', 'medio',   5, 18.0,  'emergencia', 'COL-42811A8F', -25.4284, -49.2733],
                ],
            ],
        ];

        foreach ($tutores as $t) {
            $user = User::firstOrCreate(
                ['email' => $t['email']],
                ['name' => $t['nome'], 'password' => Hash::make(self::SENHA_DEMO)]
            );

            $tutor = Tutor::firstOrCreate(
                ['user_id' => $user->id],
                ['nome' => $t['nome'], 'email' => $t['email'], 'telefone' => $t['telefone']]
            );

            foreach ($t['animais'] as [$nome, $especie, $raca, $porte, $idade, $peso, $status, $codigo, $lat, $lng]) {
                $animal = Animal::firstOrCreate(
                    ['tutor_id' => $tutor->id, 'nome' => $nome],
                    compact('especie', 'raca', 'porte', 'idade', 'peso', 'status')
                );

                $bateria = random_int(40, 100);
                Coleira::firstOrCreate(['codigo' => $codigo], [
                    'animal_id'                 => $animal->id,
                    'latitude'                  => $lat,
                    'longitude'                 => $lng,
                    'precisao'                  => random_int(3, 12),
                    'localizacao_atualizada_em' => now(),
                    'bateria_percentual'        => $bateria,
                    'bateria_status'            => 'normal',
                    'bateria_atualizada_em'     => now(),
                    'status'                    => 'ativa',
                    'ativada_em'                => now()->subMonths(2),
                ]);
            }
        }

        $depoimentos = [
            ['Rafael Nogueira', 'rafael.nogueira@petmail.com', 5, 'A UniCollar me avisou que o Max estava com a temperatura alta antes mesmo de eu notar qualquer sintoma. Levei ao veterinário na hora.'],
            ['Juliana Marques', 'juliana.marques@petmail.com', 5, 'O Simba é um gato que ama fugir de casa. Com o GPS eu acho ele em minutos.'],
            ['Eduardo Ramos',   'eduardo.ramos@petmail.com',   4, 'Uso a coleira rural no Zeca há 6 meses. A bateria dura bastante e o alcance é ótimo.'],
            ['Patrícia Gomes',  'patricia.gomes@petmail.com',  5, 'Suporte muito atencioso quando tive dúvidas na configuração.'],
        ];
        foreach ($depoimentos as [$nome, $email, $estrelas, $texto]) {
            Depoimento::firstOrCreate(['email' => $email], compact('nome', 'estrelas') + ['depoimento' => $texto, 'aprovado' => true]);
        }

        $orcamentos = [
            ['Clínica Veterinária Vida Animal', 'contato@vidaanimal.vet.br', '(19) 3652-1100', 'medio',   25, 'pendente'],
            ['Pet Shop Amigo Fiel',             'compras@amigofiel.com.br',  '(19) 3652-2200', 'pequeno', 40, 'aprovado'],
            ['Fazenda Santa Rita',              'gestao@santarita.agr.br',   '(19) 99654-3300', 'grande', 60, 'pendente'],
        ];
        foreach ($orcamentos as [$nome, $email, $telefone, $porte, $quantidade, $status]) {
            Orcamento::firstOrCreate(['nome' => $nome], compact('email', 'telefone', 'porte', 'quantidade', 'status'));
        }

        // Batimentos, rotas GPS e coleiras em estoque (COL-DEMO-0001..0010)
        $this->call(MonitoramentoSeeder::class);

        $this->command?->info('Contas demo (senha ' . self::SENHA_DEMO . '): '
            . User::pluck('email')->join(', '));
    }
}
