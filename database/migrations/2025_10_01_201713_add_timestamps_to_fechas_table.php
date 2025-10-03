<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
{
    Schema::create('fechas', function (Blueprint $table) {
        $table->id();
        $table->foreignId('temporada_id')->constrained('temporadas')->onDelete('cascade');
        $table->string('nombre'); // Ej: 1° Fecha
        $table->date('dia')->nullable();
        $table->timestamps();
    });
}
};
