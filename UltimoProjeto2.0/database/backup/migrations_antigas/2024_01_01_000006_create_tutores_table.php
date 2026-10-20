<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tutors', function (Blueprint $table) {
            $table->id();

            // Cada conta possui exatamente um tutor (o próprio usuário autenticado).
            $table->foreignId('user_id')
                  ->unique()
                  ->constrained('users')
                  ->onDelete('cascade');

            $table->string('nome', 150);
            $table->string('telefone', 20);
            $table->string('email', 200)->unique();
            $table->string('endereco', 255)->nullable();
            $table->timestamps();

            $table->index('email');
            $table->index('nome');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tutors');
    }
};
