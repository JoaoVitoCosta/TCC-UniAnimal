<?php

namespace Database\Factories;

use App\Models\Animal;
use App\Models\Coleira;
use Illuminate\Database\Eloquent\Factories\Factory;

class ColeiraFactory extends Factory
{
    protected $model = Coleira::class;

    public function definition(): array
    {
        $bateria = $this->faker->numberBetween(10, 100);

        return [
            // Por padrão, uma coleira "de fábrica": provisionada, sem
            // animal e sem telemetria — reflete o estoque real.
            'animal_id'                 => null,
            'codigo'                    => strtoupper('COL-' . $this->faker->unique()->bothify('####-????')),
            'latitude'                  => null,
            'longitude'                 => null,
            'precisao'                  => null,
            'localizacao_atualizada_em' => null,
            'bateria_percentual'        => $bateria,
            'bateria_status'            => match (true) {
                $bateria <= 15 => 'critica',
                $bateria <= 35 => 'baixa',
                default        => 'normal',
            },
            'bateria_atualizada_em' => null,
            'status'                => 'disponivel',
        ];
    }

    /** Estado: coleira já vinculada a um animal e ativa (com telemetria). */
    public function ativa(): static
    {
        return $this->state(function () {
            return [
                'animal_id'                 => Animal::factory(),
                'latitude'                  => $this->faker->latitude(-23.7, -23.4),
                'longitude'                 => $this->faker->longitude(-46.8, -46.5),
                'precisao'                  => $this->faker->randomFloat(2, 3, 15),
                'localizacao_atualizada_em' => now(),
                'bateria_atualizada_em'     => now(),
                'status'                    => 'ativa',
                'ativada_em'                => now(),
            ];
        });
    }
}
