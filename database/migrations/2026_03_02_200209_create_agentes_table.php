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
        Schema::create('agentes', function (Blueprint $table) {
            $table->id();
            $table->string('nombre_pc')->unique();
            $table->unsignedBigInteger('sala_id');
            $table->foreign('sala_id')
                  ->references('id')
                  ->on('salas')
                  ->onDelete('cascade');
            $table->string('estado')->default('desconectado'); // conectado, desconectado, offline
            $table->timestamp('ultimo_heartbeat')->nullable();
            $table->timestamp('fecha_registro')->nullable();
            $table->text('info_sistema')->nullable(); // JSON con info del agente
            $table->timestamps();
            $table->index('sala_id');
            $table->index('estado');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('agentes');
    }
};
