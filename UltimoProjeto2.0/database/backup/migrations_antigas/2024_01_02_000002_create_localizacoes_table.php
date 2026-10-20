<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Histórico de posições da coleira, usado para o mapa/trajeto
     * e cálculo de distância percorrida na dashboard do cliente.
     */
    public function up(): void
    {
        Schema::create('localizacoes', function (Blueprint $table) {
            $table->id();

            $table->foreignId('coleira_id')
                  ->constrained('coleiras')
                  ->onDelete('cascade');

            $table->decimal('latitude', 10, 7);
            $table->decimal('longitude', 10, 7);
            $table->decimal('precisao', 6, 2)->nullable();
            $table->timestamp('registrado_em');

            $table->timestamps();

            $table->index(['coleira_id', 'registrado_em']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('localizacoes');
    }
};
