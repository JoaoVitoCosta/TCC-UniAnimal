<?php

namespace Database\Factories;

use App\Models\Tutor;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class TutorFactory extends Factory
{
    protected $model = Tutor::class;

    public function definition(): array
    {
        return [
            'user_id'  => User::factory(),
            'nome'     => $this->faker->name(),
            'telefone' => $this->faker->phoneNumber(),
            'email'    => $this->faker->unique()->safeEmail(),
            'endereco' => $this->faker->address(),
        ];
    }
}
