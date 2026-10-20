<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistroECadastroTest extends TestCase
{
    use RefreshDatabase;

    public function test_registro_cria_tutor_automaticamente(): void
    {
        $response = $this->post(route('registo.post'), [
            'name'                  => 'Maria Teste',
            'email'                 => 'maria@teste.com',
            'password'              => 'senha1234',
            'password_confirmation' => 'senha1234',
        ]);

        $user = User::where('email', 'maria@teste.com')->first();

        $response->assertRedirect(route('dashboard'));
        $this->assertNotNull($user);
        $this->assertNotNull($user->tutor);
        $this->assertSame('Maria Teste', $user->tutor->nome);
    }

    public function test_cadastro_de_animal_exige_codigo_de_coleira_unico(): void
    {
        $user = User::factory()->create();
        \App\Models\Tutor::factory()->for($user)->create();

        $response = $this->actingAs($user)->post(route('animais.store'), [
            'nome'    => 'Rex',
            'especie' => 'Cachorro',
            'porte'   => 'grande',
        ]);

        $response->assertSessionHasErrors('codigo_coleira');
    }

    public function test_cadastro_de_animal_vincula_coleira_do_estoque(): void
    {
        $user = User::factory()->create();
        \App\Models\Tutor::factory()->for($user)->create();

        // Regra atual: o código precisa já existir em estoque (provisionado).
        \App\Models\Coleira::factory()->create(['codigo' => 'COL-TESTE-001']);

        $response = $this->actingAs($user)->post(route('animais.store'), [
            'nome'           => 'Rex',
            'especie'        => 'Cachorro',
            'porte'          => 'grande',
            'codigo_coleira' => 'COL-TESTE-001',
        ]);

        $response->assertRedirect(route('animais.index'));

        $animal = \App\Models\Animal::where('nome', 'Rex')->firstOrFail();

        $this->assertNotNull($animal->coleira);
        $this->assertSame('COL-TESTE-001', $animal->coleira->codigo);
        $this->assertSame('ativa', $animal->coleira->status);
        $this->assertNotNull($animal->coleira->ativada_em);
    }
}
