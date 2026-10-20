<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabela "historico": leituras de batimento cardíaco da coleira.
     *
     * Antes ela apontava para usuario.id (id_usuario) — mas quem é
     * monitorado é o PET, não o usuário. Agora cada leitura pertence a um
     * registro de animals (animal_id).
     */
    public function up(): void
    {
        if (! Schema::hasTable('historico')) {
            Schema::create('historico', function (Blueprint $table) {
                $table->id();
                $table->string('nome', 100)->nullable();
                $table->string('localizacao', 100)->nullable();
                $table->integer('batimento_cardiaco')->nullable()->comment('Batimentos por minuto (bpm)');
                $table->foreignId('animal_id')->constrained('animals')->cascadeOnDelete();
                $table->timestamp('registrado_em')->nullable();
                $table->timestamps();

                $table->index(['animal_id', 'registrado_em']);
            });

            return;
        }

        // Tabela legada já existe (criada fora das migrations): troca o
        // vínculo com usuario por um vínculo com animals.
        if (Schema::hasColumn('historico', 'id_usuario')) {
            Schema::table('historico', function (Blueprint $table) {
                $table->dropForeign('historico_ibfk_1');
                $table->dropIndex('id_usuario');
                $table->dropColumn('id_usuario');
            });
        }

        Schema::table('historico', function (Blueprint $table) {
            $table->foreignId('animal_id')->after('batimento_cardiaco')
                  ->constrained('animals')->cascadeOnDelete();
            $table->timestamp('registrado_em')->nullable()->after('animal_id');
            $table->timestamps();

            $table->index(['animal_id', 'registrado_em']);
        });
    }

    public function down(): void
    {
        Schema::table('historico', function (Blueprint $table) {
            $table->dropForeign(['animal_id']);
            $table->dropIndex(['animal_id', 'registrado_em']);
            $table->dropColumn(['animal_id', 'registrado_em', 'created_at', 'updated_at']);

            $table->integer('id_usuario')->nullable();
            $table->foreign('id_usuario', 'historico_ibfk_1')->references('id')->on('usuario');
        });
    }
};
