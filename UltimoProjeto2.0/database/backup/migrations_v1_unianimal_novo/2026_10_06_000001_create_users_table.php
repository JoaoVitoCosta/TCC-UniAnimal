<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Contas de acesso ao site (login por e-mail/senha ou Google).
     * Cada usuário tem exatamente um tutor (tabela tutors).
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            // Login com Google: usuários Google recebem uma senha aleatória
            // com hash (ver AuthController), por isso password segue NOT NULL.
            $table->string('google_id')->nullable()->unique();
            $table->string('avatar')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
