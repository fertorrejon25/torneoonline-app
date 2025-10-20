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
    public function up()
    {
        Schema::table('jugadores', function (Blueprint $table) {
            // Borrar la columna id actual
            $table->dropColumn('id');
        });

        Schema::table('jugadores', function (Blueprint $table) {
            // Volver a crear la columna id como autoIncrement
            $table->id();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('jugadores', function (Blueprint $table) {
            $table->dropColumn('id');
        });

        Schema::table('jugadores', function (Blueprint $table) {
            // Restaurar como entero simple si lo querés así
            $table->integer('id');
        });
    }
};
