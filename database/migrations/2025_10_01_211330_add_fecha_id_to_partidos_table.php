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
    Schema::table('partidos', function (Blueprint $table) {
        $table->foreignId('fecha_id')->nullable()->constrained('fechas');
    });
}

public function down()
{
    Schema::table('partidos', function (Blueprint $table) {
        $table->dropColumn('fecha_id');
    });
}
};
