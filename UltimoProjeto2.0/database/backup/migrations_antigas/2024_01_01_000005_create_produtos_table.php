<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('produtos', function (Blueprint $table) {
            $table->id();
            $table->string('nome');
            $table->string('porte');
            $table->text('descricao');
            $table->decimal('preco', 10, 2);
            $table->decimal('preco_promocional', 10, 2)->nullable();
            $table->string('imagem')->nullable();
            $table->boolean('ativo')->default(true);
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('produtos'); }
};
