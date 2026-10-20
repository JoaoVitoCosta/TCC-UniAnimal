<?php

namespace Tests\Feature;

use App\Models\Alerta;
use App\Models\Animal;
use App\Models\ApiToken;
use App\Models\Coleira;
use App\Models\Telemetria;
use App\Models\Tutor;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * API IoT (coleira → servidor) e a parte de alertas da API do app.
 */
class IotTelemetriaTest extends TestCase
{
    use RefreshDatabase;

    private Animal $animal;
    private Coleira $coleira;
    private string $chave;

    protected function setUp(): void
    {
        parent::setUp();

        $tutor = Tutor::factory()->for(User::factory())->create();
        // Cão pequeno: batimento normal 80–140 (emergência < 60 ou > 170);
        // temperatura normal 37,5–39,2 °C (emergência < 36,5 ou > 40,0).
        $this->animal = Animal::factory()->for($tutor)->create([
            'nome' => 'Belinha', 'especie' => 'Cachorro', 'porte' => 'pequeno', 'status' => 'saudavel',
        ]);
        $this->coleira = Coleira::factory()->create([
            'codigo' => 'COL-TESTE', 'animal_id' => $this->animal->id, 'status' => 'ativa', 'bateria_percentual' => 90,
        ]);
        $this->chave = $this->coleira->gerarChave();
    }

    private function enviar(array $dados, ?string $chave = null)
    {
        return $this->withToken($chave ?? $this->chave)->postJson('/api/v1/iot/telemetria', $dados);
    }

    public function test_sem_chave_ou_chave_invalida_e_recusado_e_registrado(): void
    {
        $this->postJson('/api/v1/iot/telemetria', ['batimento_cardiaco' => 100])->assertStatus(401);
        $this->enviar(['batimento_cardiaco' => 100], 'chave-errada')->assertStatus(401);

        $this->assertSame(2, Telemetria::where('situacao', 'rejeitada')->count());
        $this->assertSame(0, $this->animal->leituras()->count());
    }

    public function test_coleira_em_estoque_nao_transmite(): void
    {
        $estoque = Coleira::factory()->create(['codigo' => 'COL-ESTOQUE']);

        $this->enviar(['batimento_cardiaco' => 100], $estoque->gerarChave())->assertStatus(403);
    }

    public function test_envio_normal_grava_leitura_posicao_e_atualiza_coleira(): void
    {
        $this->enviar([
            'batimento_cardiaco' => 110, 'temperatura' => 38.5,
            'latitude' => -21.705, 'longitude' => -47.274, 'precisao' => 6,
            'bateria_percentual' => 77, 'firmware_versao' => '1.2.0',
        ])->assertCreated()
          ->assertJsonPath('recebido', true)
          ->assertJsonPath('status_animal', 'saudavel')
          ->assertJsonCount(0, 'alertas_gerados');

        $this->assertDatabaseHas('leituras_saude', ['animal_id' => $this->animal->id, 'batimento_cardiaco' => 110, 'temperatura' => 38.5]);
        $this->assertDatabaseHas('localizacoes', ['coleira_id' => $this->coleira->id]);

        $c = $this->coleira->fresh();
        $this->assertEquals(-21.705, (float) $c->latitude);
        $this->assertSame(77, $c->bateria_percentual);
        $this->assertSame('1.2.0', $c->firmware_versao);
        $this->assertNotNull($c->ultimo_contato_em);
        $this->assertSame(1, Telemetria::where('situacao', 'processada')->count());
    }

    public function test_batimento_fora_da_faixa_abre_um_alerta_sem_duplicar_e_resolve_ao_normalizar(): void
    {
        $this->enviar(['batimento_cardiaco' => 185])
            ->assertCreated()
            ->assertJsonPath('status_animal', 'emergencia')
            ->assertJsonPath('alertas_gerados.0.tipo', 'batimento')
            ->assertJsonPath('alertas_gerados.0.nivel', 'emergencia');

        // Continua alto: atualiza o mesmo alerta, não cria outro.
        $this->enviar(['batimento_cardiaco' => 190])->assertJsonCount(0, 'alertas_gerados');
        $this->assertSame(1, Alerta::where('tipo', 'batimento')->count());
        $this->assertSame('190 bpm', Alerta::first()->valor);

        // Voltou ao normal: resolve e o animal volta a saudável.
        $this->enviar(['batimento_cardiaco' => 100])->assertJsonPath('status_animal', 'saudavel');
        $this->assertNotNull(Alerta::first()->resolvido_em);
    }

    public function test_faixa_de_alerta_deixa_o_animal_em_atencao(): void
    {
        $this->enviar(['batimento_cardiaco' => 150])->assertJsonPath('status_animal', 'atencao');
        $this->assertSame('alerta', Alerta::first()->nivel);
    }

    public function test_temperatura_alta_gera_alerta(): void
    {
        $this->enviar(['temperatura' => 40.6])
            ->assertJsonPath('alertas_gerados.0.tipo', 'temperatura')
            ->assertJsonPath('alertas_gerados.0.titulo', 'Temperatura alta');
    }

    public function test_bateria_critica_gera_emergencia(): void
    {
        $this->enviar(['bateria_percentual' => 10])
            ->assertJsonPath('alertas_gerados.0.tipo', 'bateria')
            ->assertJsonPath('alertas_gerados.0.nivel', 'emergencia');

        $this->assertSame('critica', $this->coleira->fresh()->bateria_status);
    }

    public function test_fora_da_zona_segura_gera_alerta_e_resolve_ao_voltar(): void
    {
        $this->animal->zonasSeguras()->create(['nome' => 'Casa', 'latitude' => -21.705, 'longitude' => -47.274, 'raio_metros' => 200]);

        $this->enviar(['latitude' => -21.715, 'longitude' => -47.274])   // ~1,1 km ao sul
            ->assertJsonPath('alertas_gerados.0.tipo', 'zona_segura');

        $this->enviar(['latitude' => -21.7051, 'longitude' => -47.2741]); // de volta em casa
        $this->assertNotNull(Alerta::where('tipo', 'zona_segura')->first()->resolvido_em);
    }

    public function test_lote_fora_de_ordem_mantem_a_posicao_mais_recente(): void
    {
        $this->withToken($this->chave)->postJson('/api/v1/iot/telemetria/lote', ['leituras' => [
            ['batimento_cardiaco' => 100, 'latitude' => -21.70, 'longitude' => -47.27, 'registrado_em' => now()->subMinutes(1)->toIso8601String()],
            ['batimento_cardiaco' => 101, 'latitude' => -21.60, 'longitude' => -47.20, 'registrado_em' => now()->subMinutes(10)->toIso8601String()],
        ]])->assertCreated()->assertJsonPath('processadas', 2);

        $this->assertSame(2, $this->animal->leituras()->count());
        $this->assertEquals(-21.70, (float) $this->coleira->fresh()->latitude);
    }

    public function test_envio_sem_nenhuma_medicao_e_rejeitado(): void
    {
        $this->enviar(['firmware_versao' => '1.0'])->assertStatus(422)->assertJsonValidationErrors('telemetria');
        $this->enviar(['batimento_cardiaco' => 999])->assertStatus(422)->assertJsonValidationErrors('batimento_cardiaco');
    }

    public function test_config_devolve_intervalo_faixas_e_zonas(): void
    {
        $this->withToken($this->chave)->getJson('/api/v1/iot/config')
            ->assertOk()
            ->assertJsonPath('coleira', 'COL-TESTE')
            ->assertJsonPath('intervalo_envio_seg', 60)
            ->assertJsonPath('faixas.batimento.min', 80);
    }

    public function test_coleira_que_para_de_transmitir_fica_sem_sinal_e_volta_ao_transmitir(): void
    {
        $this->coleira->forceFill(['ultimo_contato_em' => now()->subHour()])->save();

        $this->artisan('coleiras:verificar-sinal')->assertSuccessful();
        $this->assertSame('sem_sinal', $this->coleira->fresh()->status);
        $this->assertSame('atencao', $this->animal->fresh()->status);

        $this->enviar(['batimento_cardiaco' => 100])->assertJsonPath('status_animal', 'saudavel');
        $this->assertSame('ativa', $this->coleira->fresh()->status);
        $this->assertNotNull(Alerta::where('tipo', 'sem_sinal')->first()->resolvido_em);
    }

    public function test_app_lista_alertas_e_marca_como_lido_so_do_proprio_animal(): void
    {
        $this->enviar(['batimento_cardiaco' => 185]);
        [$token] = ApiToken::issueFor($this->animal->tutor, $this->coleira);

        $this->withToken($token)->getJson("/api/v1/animais/{$this->animal->id}/alertas?abertos=1")
            ->assertOk()
            ->assertJsonPath('abertos', 1)
            ->assertJsonPath('nao_lidos', 1)
            ->assertJsonPath('alertas.0.tipo', 'batimento');

        $id = Alerta::first()->id;
        $this->withToken($token)->postJson("/api/v1/alertas/{$id}/lido")->assertOk()->assertJsonPath('lido', true);

        // Outro tutor não enxerga o alerta.
        $outro = Animal::factory()->create();
        $outraColeira = Coleira::factory()->create(['animal_id' => $outro->id, 'status' => 'ativa']);
        [$tokenOutro] = ApiToken::issueFor($outro->tutor, $outraColeira);
        $this->withToken($tokenOutro)->postJson("/api/v1/alertas/{$id}/lido")->assertNotFound();
        $this->withToken($tokenOutro)->getJson("/api/v1/animais/{$this->animal->id}/alertas")->assertNotFound();
    }
}
