<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tokens de sessão do app Flutter, emitidos no login pelo código da
     * coleira. Só o hash SHA-256 é guardado — o texto puro vai apenas para o app.
     */
    public function up(): void
    {
        Schema::create('api_tokens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tutor_id')->constrained('tutors')->cascadeOnDelete();
            $table->foreignId('coleira_id')->nullable()
                  ->comment('Coleira usada no login: o token só enxerga o animal dela')
                  ->constrained('coleiras')->nullOnDelete();
            $table->string('token_hash', 64)->unique()->comment('SHA-256 do token; o texto puro nunca é armazenado');
            $table->string('nome_dispositivo', 150)->nullable();
            $table->timestamp('last_used_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('api_tokens');
    }
};
