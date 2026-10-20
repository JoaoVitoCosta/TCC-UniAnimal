<?php

namespace Database\Factories;

use App\Models\Animal;
use App\Models\Tutor;
use Illuminate\Database\Eloquent\Factories\Factory;

class AnimalFactory extends Factory
{
    protected $model = Animal::class;

    public function definition(): array
    {
        $especies = ['Cachorro', 'Gato', 'Coelho', 'Hamster'];
        $racas    = ['SRD', 'Labrador', 'Persa', 'Pastor Alemão', 'Golden Retriever', 'Siamês'];

        return [
            'tutor_id' => Tutor::factory(),
            'nome'     => $this->faker->firstName(),
            'especie'  => $this->faker->randomElement($especies),
            'raca'     => $this->faker->randomElement($racas),
            'porte'    => $this->faker->randomElement(['pequeno', 'medio', 'grande']),
            'idade'    => $this->faker->numberBetween(0, 15),
            'peso'     => $this->faker->randomFloat(1, 1, 50),
            'status'   => $this->faker->randomElement(['saudavel', 'atencao', 'emergencia']),
        ];
    }
}
