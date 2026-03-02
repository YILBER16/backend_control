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
        Schema::create('salas', function (Blueprint $table) {
            $table->id();
            $table->string('nombre')->unique(); // Sala 1, Sala 2, Aula A, etc
            $table->string('descripcion')->nullable(); // Descripción de la sala
            $table->integer('capacidad')->default(30); // Capacidad máxima de agentes
            $table->string('ubicacion')->nullable(); // Edificio, Piso, etc
            $table->enum('estado', ['activa', 'inactiva', 'mantenimiento'])->default('activa');
            $table->timestamps();
            $table->index('estado');
            $table->index('nombre');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('salas');
    }
};
