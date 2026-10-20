<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabela "coleiras": representa o dispositivo físico vinculado ao animal.
     * Relação 1:1 (um animal possui exatamente uma coleira).
     * Arquitetura preparada para GPS/IoT real, sem implementar hardware.
     */
    public function up(): void
    {
        Schema::create('coleiras', function (Blueprint $table) {
            $table->id();

            // Nullable: a coleira existe no banco (provisionada pela fábrica/estoque)
            // ANTES de ser vinculada a um animal. É isso que permite validar o
            // código no cadastro do animal E usá-lo como login no app, sem
            // depender de o Flutter "inventar" um vínculo local.
            $table->foreignId('animal_id')
                  ->nullable()
                  ->unique()
                  ->constrained('animals')
                  ->onDelete('set null');

            // Identificação do dispositivo — credencial controlada pelo backend.
            // UNIQUE garante que o mesmo código físico nunca exista duas vezes.
            $table->string('codigo', 50)->unique();

            // Localização (GPS) - última posição conhecida
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->decimal('precisao', 6, 2)->nullable()->comment('Precisão do GPS em metros');
            $table->timestamp('localizacao_atualizada_em')->nullable();

            // Bateria
            $table->unsignedTinyInteger('bateria_percentual')->default(100);
            $table->enum('bateria_status', ['normal', 'baixa', 'critica'])->default('normal');
            $table->timestamp('bateria_atualizada_em')->nullable();

            // Status geral do dispositivo/credencial:
            // disponivel = provisionada em estoque, ainda não vinculada a nenhum animal (login/vínculo negado)
            // ativa      = vinculada a um animal e liberada para uso (login/vínculo permitido)
            // inativa    = desativada manualmente (ex.: suporte, troca de coleira, cliente cancelou)
            // sem_sinal  = vinculada e ativa, mas sem telemetria recente do dispositivo
            $table->enum('status', ['disponivel', 'ativa', 'inativa', 'sem_sinal'])->default('disponivel');

            $table->timestamp('ativada_em')->nullable()->comment('Quando o código foi vinculado a um animal');

            $table->timestamps();

            $table->index('codigo');
            $table->index('status');
            $table->index('bateria_status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('coleiras');
    }
};
