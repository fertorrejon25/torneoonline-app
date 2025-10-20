<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('partidos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('temporada_id')->constrained();
            $table->foreignId('equipo_local_id')->constrained('equipos');
            $table->foreignId('equipo_visitante_id')->constrained('equipos');
            $table->integer('goles_local')->nullable();
            $table->integer('goles_visitante')->nullable();
            $table->date('fecha_id')->nullable();
            $table->time('hora')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('partidos');
    }
};