<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tutor = dono dos animais. 1:1 com users (o próprio usuário logado).
     */
    public function up(): void
    {
        Schema::create('tutors', function (Blueprint $table) {
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
        Schema::dropIfExists('tutors');
    }
};
