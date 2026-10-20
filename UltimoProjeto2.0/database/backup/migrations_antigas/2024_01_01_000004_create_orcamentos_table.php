<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('orcamentos', function (Blueprint $table) {
            $table->id();
            $table->string('nome');
            $table->string('email');
            $table->string('telefone');
            $table->string('porte')->default('medio');
            $table->integer('quantidade')->default(1);
            $table->text('observacoes')->nullable();
            $table->string('status')->default('pendente');
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('orcamentos'); }
};
