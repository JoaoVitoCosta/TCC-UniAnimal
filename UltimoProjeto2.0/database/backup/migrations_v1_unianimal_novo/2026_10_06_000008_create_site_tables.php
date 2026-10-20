<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabelas do site público: catálogo, depoimentos, contato e orçamentos.
     */
    public function up(): void
    {
        // Catálogo de coleiras — fonte única dos preços da home e de /produtos.
        Schema::create('produtos', function (Blueprint $table) {
            $table->id();
            $table->string('nome', 100);
            $table->enum('porte', ['pequeno', 'medio', 'grande'])->unique();
            $table->text('descricao');
            $table->decimal('preco', 10, 2);
            $table->decimal('preco_promocional', 10, 2)->nullable()->comment('Preço da página Oferta');
            $table->string('imagem')->nullable();
            $table->boolean('ativo')->default(true);
            $table->timestamps();
        });

        Schema::create('depoimentos', function (Blueprint $table) {
            $table->id();
            $table->string('nome', 150);
            $table->string('email', 200);
            $table->string('telefone', 20)->nullable();
            $table->text('depoimento');
            $table->unsignedTinyInteger('estrelas')->default(5)->comment('1 a 5');
            $table->boolean('aprovado')->default(true);
            $table->timestamps();

            $table->index(['aprovado', 'created_at']);
        });

        Schema::create('contatos', function (Blueprint $table) {
            $table->id();
            $table->string('nome', 150);
            $table->string('email', 200);
            $table->string('telefone', 20)->nullable();
            $table->string('assunto', 150)->nullable();
            $table->text('mensagem');
            $table->timestamps();
        });

        // Pedidos de orçamento no atacado (formulário enviado por WhatsApp).
        Schema::create('orcamentos', function (Blueprint $table) {
            $table->id();
            $table->string('nome', 150);
            $table->string('email', 200)->nullable();
            $table->string('telefone', 20);
            $table->string('porte', 60)->default('medio');
            $table->unsignedInteger('quantidade')->default(1);
            $table->text('observacoes')->nullable();
            $table->enum('status', ['pendente', 'aprovado', 'recusado'])->default('pendente');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orcamentos');
        Schema::dropIfExists('contatos');
        Schema::dropIfExists('depoimentos');
        Schema::dropIfExists('produtos');
    }
};
