<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Animais monitorados e as coleiras UniCollar (dispositivos IoT).
     */
    public function up(): void
    {
        Schema::create('animais', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tutor_id')->constrained('tutores')->cascadeOnDelete();
            $table->string('nome', 100);
            $table->string('especie', 60)->comment('Cachorro, Gato, Coelho, Cavalo, Bovino ou Outro');
            $table->string('raca', 100)->nullable();
            $table->enum('porte', ['pequeno', 'medio', 'grande']);
            $table->unsignedTinyInteger('idade')->nullable()->comment('Anos');
            $table->decimal('peso', 6, 2)->nullable()->comment('Kg');
            // Atualizado automaticamente pelos alertas abertos do animal
            // (App\Services\AlertaService::atualizarStatusDoAnimal).
            $table->enum('status', ['saudavel', 'atencao', 'emergencia'])->default('saudavel');
            $table->timestamps();

            $table->index('status');
        });

        Schema::create('coleiras', function (Blueprint $table) {
            $table->id();
            $table->foreignId('animal_id')->nullable()->unique()
                  ->comment('NULL = coleira em estoque, ainda não vinculada')
                  ->constrained('animais')->nullOnDelete();

            // Código impresso na coleira: vincula ao animal e é o login do app.
            $table->string('codigo', 50)->unique();

            // Credencial do HARDWARE para a API IoT (POST /api/v1/iot/...).
            // Só o hash SHA-256 é guardado; a chave em texto aparece uma vez,
            // ao ser gerada (php artisan coleira:chave CODIGO).
            $table->string('chave_hash', 64)->nullable()->unique();
            $table->string('firmware_versao', 30)->nullable();
            $table->unsignedSmallInteger('intervalo_envio_seg')->default(60)
                  ->comment('De quantos em quantos segundos a coleira deve enviar telemetria');
            $table->timestamp('ultimo_contato_em')->nullable()->comment('Última telemetria recebida');

            // Última posição conhecida (o histórico fica em localizacoes)
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->decimal('precisao', 6, 2)->nullable()->comment('Precisão do GPS em metros');
            $table->timestamp('localizacao_atualizada_em')->nullable();

            // Bateria (normal > 35% · baixa 16–35% · critica ≤ 15%)
            $table->unsignedTinyInteger('bateria_percentual')->default(100);
            $table->enum('bateria_status', ['normal', 'baixa', 'critica'])->default('normal');
            $table->timestamp('bateria_atualizada_em')->nullable();

            // disponivel = em estoque · ativa = vinculada e transmitindo
            // inativa = desativada manualmente · sem_sinal = vinculada, sem telemetria recente
            $table->enum('status', ['disponivel', 'ativa', 'inativa', 'sem_sinal'])->default('disponivel');
            $table->timestamp('ativada_em')->nullable()->comment('Quando o código foi vinculado a um animal');
            $table->timestamps();

            $table->index('status');
            $table->index('ultimo_contato_em');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('coleiras');
        Schema::dropIfExists('animais');
    }
};
