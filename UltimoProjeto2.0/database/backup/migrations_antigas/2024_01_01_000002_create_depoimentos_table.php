<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('depoimentos', function (Blueprint $table) {
            $table->id();
            $table->string('nome');
            $table->string('email');
            $table->string('telefone')->nullable();
            $table->text('depoimento');
            $table->integer('estrelas')->default(5);
            $table->boolean('aprovado')->default(true);
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('depoimentos'); }
};
