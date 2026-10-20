<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Animais monitorados. Os valores de porte e status são os mesmos usados
     * pelo site e pelo app Flutter (Pet.fromApiJson).
     */
    public function up(): void
    {
        Schema::create('animals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tutor_id')->constrained('tutors')->cascadeOnDelete();
            $table->string('nome', 100);
            $table->string('especie', 60)->comment('Cachorro, Gato, Coelho, Cavalo, Bovino ou Outro');
            $table->string('raca', 100)->nullable();
            $table->enum('porte', ['pequeno', 'medio', 'grande']);
            $table->unsignedTinyInteger('idade')->nullable()->comment('Anos');
            $table->decimal('peso', 6, 2)->nullable()->comment('Kg');
            $table->enum('status', ['saudavel', 'atencao', 'emergencia'])->default('saudavel');
            $table->timestamps();

            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('animals');
    }
};
