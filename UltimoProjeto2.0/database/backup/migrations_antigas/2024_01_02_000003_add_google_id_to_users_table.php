<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Observação: não usamos ->change() aqui de propósito, pois isso exigiria
        // a dependência doctrine/dbal (não presente no composer.json original).
        // Usuários criados via Google recebem uma senha aleatória com hash
        // (ver AuthController::handleGoogleCallback), preservando a coluna
        // "password" como NOT NULL sem quebrar o cadastro tradicional.
        Schema::table('users', function (Blueprint $table) {
            $table->string('google_id')->nullable()->unique()->after('email');
            $table->string('avatar')->nullable()->after('google_id');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['google_id', 'avatar']);
        });
    }
};
