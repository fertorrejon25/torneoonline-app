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
            $table->unsignedBigInteger('equipos_id')->after('dni');
            $table->foreign('equipos_id')->references('id')->on('equipos');
        });
    }

    public function down()
    {
        Schema::table('jugadores', function (Blueprint $table) {
            $table->dropForeign(['equipos_id']);
            $table->dropColumn('equipos_id');
        });
    }
};
