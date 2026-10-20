<?php

namespace Tests\Feature;

use App\Models\Animal;
use App\Models\Tutor;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AnimalScopingTest extends TestCase
{
    use RefreshDatabase;

    public function test_tutor_can_only_see_seus_proprios_animais(): void
    {
        $userA = User::factory()->create();
        $tutorA = Tutor::factory()->for($userA)->create();
        $animalA = Animal::factory()->for($tutorA)->create();

        $userB = User::factory()->create();
        $tutorB = Tutor::factory()->for($userB)->create();
        $animalB = Animal::factory()->for($tutorB)->create();

        $response = $this->actingAs($userA)->get(route('animais.index'));

        $response->assertOk();
        $response->assertSee($animalA->nome);
        $response->assertDontSee($animalB->nome);
    }

    public function test_tutor_nao_pode_acessar_animal_de_outro_tutor(): void
    {
        $userA = User::factory()->create();
        Tutor::factory()->for($userA)->create();

        $userB = User::factory()->create();
        $tutorB = Tutor::factory()->for($userB)->create();
        $animalB = Animal::factory()->for($tutorB)->create();

        $response = $this->actingAs($userA)->get(route('animais.show', $animalB));

        $response->assertForbidden();
    }

    public function test_convidado_e_redirecionado_para_login(): void
    {
        $response = $this->get(route('animais.index'));

        $response->assertRedirect(route('login'));
    }
}
