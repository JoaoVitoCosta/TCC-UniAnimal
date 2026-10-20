<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tokens de acesso à API usados pelo aplicativo Flutter.
     *
     * O app nunca guarda usuário/senha nem o código da coleira como
     * credencial de sessão: após o login por código, o backend emite um
     * token opaco (aqui armazenado apenas como hash SHA-256, nunca em
     * texto puro) que o Flutter usa em toda chamada autenticada
     * (Authorization: Bearer <token>).
     */
    public function up(): void
    {
        Schema::create('api_tokens', function (Blueprint $table) {
            $table->id();

            $table->foreignId('tutor_id')
                  ->constrained('tutors')
                  ->onDelete('cascade');

            $table->foreignId('coleira_id')
                  ->nullable()
                  ->constrained('coleiras')
                  ->onDelete('set null')
                  ->comment('Coleira usada para autenticar (auditoria: qual código gerou este token)');

            $table->string('token_hash', 64)->unique();
            $table->string('nome_dispositivo', 150)->nullable();
            $table->timestamp('last_used_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();

            $table->index('tutor_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('api_tokens');
    }
};
