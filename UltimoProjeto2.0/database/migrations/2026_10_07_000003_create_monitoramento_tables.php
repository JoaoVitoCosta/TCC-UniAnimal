<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Dados que chegam da coleira (IoT) e o que o sistema gera a partir deles.
     *
     * Observação sobre timestamps: o MariaDB do XAMPP vem com
     * explicit_defaults_for_timestamp = OFF, e nesse modo a primeira coluna
     * TIMESTAMP NOT NULL sem DEFAULT ganha "ON UPDATE CURRENT_TIMESTAMP" —
     * a data da leitura mudaria sozinha a cada UPDATE. Por isso toda data de
     * leitura aqui usa useCurrent() (DEFAULT explícito, sem ON UPDATE).
     */
    public function up(): void
    {
        // Sinais vitais medidos pela coleira (gráfico do site e do app).
        Schema::create('leituras_saude', function (Blueprint $table) {
            $table->id();
            $table->foreignId('animal_id')->constrained('animais')->cascadeOnDelete();
            $table->foreignId('coleira_id')->nullable()->constrained('coleiras')->nullOnDelete();
            $table->unsignedSmallInteger('batimento_cardiaco')->nullable()->comment('bpm');
            $table->decimal('temperatura', 4, 1)->nullable()->comment('°C');
            $table->timestamp('registrado_em')->useCurrent()->comment('Momento da medição na coleira');
            $table->timestamps();

            $table->index(['animal_id', 'registrado_em']);
        });

        // Histórico de posições GPS (rota no mapa).
        Schema::create('localizacoes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('coleira_id')->constrained('coleiras')->cascadeOnDelete();
            $table->decimal('latitude', 10, 7);
            $table->decimal('longitude', 10, 7);
            $table->decimal('precisao', 6, 2)->nullable()->comment('Metros');
            $table->timestamp('registrado_em')->useCurrent();
            $table->timestamps();

            $table->index(['coleira_id', 'registrado_em']);
        });

        // Cerca virtual: área circular onde o animal deve permanecer.
        Schema::create('zonas_seguras', function (Blueprint $table) {
            $table->id();
            $table->foreignId('animal_id')->constrained('animais')->cascadeOnDelete();
            $table->string('nome', 80)->default('Casa');
            $table->decimal('latitude', 10, 7);
            $table->decimal('longitude', 10, 7);
            $table->unsignedInteger('raio_metros')->default(200);
            $table->boolean('ativa')->default(true);
            $table->timestamps();
        });

        // Alertas gerados automaticamente a partir da telemetria.
        Schema::create('alertas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('animal_id')->constrained('animais')->cascadeOnDelete();
            $table->foreignId('coleira_id')->nullable()->constrained('coleiras')->nullOnDelete();
            $table->enum('tipo', ['batimento', 'temperatura', 'bateria', 'sem_sinal', 'zona_segura']);
            $table->enum('nivel', ['alerta', 'emergencia']);
            $table->string('titulo', 120);
            $table->string('mensagem', 255);
            $table->string('valor', 40)->nullable()->comment('Valor que disparou o alerta, ex.: "182 bpm"');
            $table->timestamp('lido_em')->nullable()->comment('Tutor viu o alerta');
            $table->timestamp('resolvido_em')->nullable()->comment('Situação voltou ao normal');
            $table->timestamps();

            $table->index(['animal_id', 'resolvido_em']);
            $table->index(['animal_id', 'created_at']);
        });

        // Registro bruto de tudo que chegou na API IoT (auditoria/depuração).
        Schema::create('telemetrias', function (Blueprint $table) {
            $table->id();
            $table->foreignId('coleira_id')->nullable()->constrained('coleiras')->nullOnDelete();
            $table->json('payload');
            $table->string('ip', 45)->nullable();
            $table->enum('situacao', ['processada', 'rejeitada'])->default('processada');
            $table->string('erro', 255)->nullable();
            $table->timestamp('recebido_em')->useCurrent();

            $table->index(['coleira_id', 'recebido_em']);
        });

        // Sessões do app Flutter (login pelo código da coleira).
        Schema::create('api_tokens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tutor_id')->constrained('tutores')->cascadeOnDelete();
            $table->foreignId('coleira_id')->nullable()
                  ->comment('Coleira usada no login: o token só enxerga o animal dela')
                  ->constrained('coleiras')->nullOnDelete();
            $table->string('token_hash', 64)->unique()->comment('SHA-256; o texto puro nunca é armazenado');
            $table->string('nome_dispositivo', 150)->nullable();
            $table->timestamp('last_used_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('api_tokens');
        Schema::dropIfExists('telemetrias');
        Schema::dropIfExists('alertas');
        Schema::dropIfExists('zonas_seguras');
        Schema::dropIfExists('localizacoes');
        Schema::dropIfExists('leituras_saude');
    }
};
