<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Coleiras UniCollar. O código é provisionado em estoque (animal_id NULL,
     * status "disponivel") e vinculado a um animal no cadastro — a partir daí
     * ele é a credencial de login do app (POST /api/v1/auth/coleira).
     */
    public function up(): void
    {
        Schema::create('coleiras', function (Blueprint $table) {
            $table->id();
            $table->foreignId('animal_id')->nullable()->unique()
                  ->comment('NULL = coleira em estoque, ainda não vinculada')
                  ->constrained('animals')->nullOnDelete();
            $table->string('codigo', 50)->unique();

            // Última posição conhecida (o histórico fica em localizacoes)
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->decimal('precisao', 6, 2)->nullable()->comment('Precisão do GPS em metros');
            $table->timestamp('localizacao_atualizada_em')->nullable();

            // Bateria (normal > 35% · baixa 16–35% · critica ≤ 15%)
            $table->unsignedTinyInteger('bateria_percentual')->default(100);
            $table->enum('bateria_status', ['normal', 'baixa', 'critica'])->default('normal');
            $table->timestamp('bateria_atualizada_em')->nullable();

            // disponivel = em estoque · ativa = vinculada e em uso
            // inativa = desativada manualmente · sem_sinal = vinculada, sem telemetria recente
            $table->enum('status', ['disponivel', 'ativa', 'inativa', 'sem_sinal'])->default('disponivel');
            $table->timestamp('ativada_em')->nullable()->comment('Quando o código foi vinculado a um animal');
            $table->timestamps();

            $table->index('status');
            $table->index('bateria_status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('coleiras');
    }
};
