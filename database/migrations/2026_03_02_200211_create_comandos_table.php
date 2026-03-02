<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('comandos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('agente_id')->nullable();
            $table->string('nombre_pc')->index(); // Para referencia sin FK si la tabla agentes no existe
            $table->string('tipo'); // lock, apagar, reiniciar, limpiar_temp
            $table->text('parametros')->nullable(); // JSON con parámetros
            $table->string('estado')->default('pendiente'); // pendiente, ejecutado, error
            $table->text('resultado')->nullable(); // Resultado de ejecución
            $table->timestamp('fecha_envio')->nullable();
            $table->timestamp('fecha_ejecucion')->nullable();
            $table->timestamps();
            $table->index('estado');
            $table->index('fecha_envio');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('comandos');
    }
};
