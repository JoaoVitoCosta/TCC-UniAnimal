<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('animals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tutor_id')
                  ->constrained('tutors')
                  ->onDelete('cascade');
            $table->string('nome', 100);
            $table->string('especie', 60);
            $table->string('raca', 100)->nullable();
            $table->enum('porte', ['pequeno', 'medio', 'grande']);
            $table->unsignedTinyInteger('idade')->nullable();
            $table->decimal('peso', 6, 2)->nullable();
            $table->enum('status', ['saudavel', 'atencao', 'emergencia'])->default('saudavel');
            $table->timestamps();

            $table->index('tutor_id');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('animals');
    }
};
