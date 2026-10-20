<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Leituras de batimento cardíaco enviadas pela coleira, por animal.
     * Alimenta GET /api/v1/animais/{id}/batimentos (app) e o gráfico do site.
     *
     * Versão anterior tinha colunas legadas "nome" (repetia animals.nome) e
     * "localizacao" (repetia a tabela localizacoes) — removidas.
     */
    public function up(): void
    {
        Schema::create('historico', function (Blueprint $table) {
            $table->id();
            $table->foreignId('animal_id')->constrained('animals')->cascadeOnDelete();
            $table->unsignedSmallInteger('batimento_cardiaco')->comment('Batimentos por minuto (bpm)');
            // useCurrent(): ver comentário em create_localizacoes_table.
            $table->timestamp('registrado_em')->useCurrent();
            $table->timestamps();

            $table->index(['animal_id', 'registrado_em']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('historico');
    }
};
