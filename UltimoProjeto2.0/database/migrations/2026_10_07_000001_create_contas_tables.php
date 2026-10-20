<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Contas do site (users, padrão do Laravel) e tutores (1:1 com users).
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            // Login com Google: recebem uma senha aleatória com hash (ver
            // AuthController), por isso password continua NOT NULL.
            $table->string('google_id')->nullable()->unique();
            $table->string('avatar')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('tutores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained('users')->cascadeOnDelete();
            $table->string('nome', 150);
            $table->string('telefone', 20)->default('');
            $table->string('email', 200)->unique();
            $table->string('endereco', 255)->nullable();
            $table->timestamps();

            $table->index('nome');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tutores');
        Schema::dropIfExists('users');
    }
};
