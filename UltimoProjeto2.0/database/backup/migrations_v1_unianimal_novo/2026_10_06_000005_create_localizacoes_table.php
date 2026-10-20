<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Histórico de posições GPS de cada coleira (rota no mapa do site e do app).
     */
    public function up(): void
    {
        Schema::create('localizacoes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('coleira_id')->constrained('coleiras')->cascadeOnDelete();
            $table->decimal('latitude', 10, 7);
            $table->decimal('longitude', 10, 7);
            $table->decimal('precisao', 6, 2)->nullable()->comment('Metros');
            // useCurrent() é obrigatório aqui: sem um DEFAULT explícito, o
            // MariaDB do XAMPP (explicit_defaults_for_timestamp = OFF) cria a
            // coluna com ON UPDATE CURRENT_TIMESTAMP e a data da posição
            // mudaria sozinha a cada UPDATE da linha.
            $table->timestamp('registrado_em')->useCurrent();
            $table->timestamps();

            $table->index(['coleira_id', 'registrado_em']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('localizacoes');
    }
};
