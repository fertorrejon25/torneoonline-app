

public function up(): void
{
    Schema::create('partidos', function (Blueprint $table) {
        $table->id();
        $table->foreignId('fecha_id')->constrained('fechas')->onDelete('cascade');
        $table->foreignId('equipo_local_id')->constrained('equipos')->onDelete('cascade');
        $table->foreignId('equipo_visitante_id')->constrained('equipos')->onDelete('cascade');
        $table->time('hora')->nullable();
        $table->integer('goles_local')->nullable();
        $table->integer('goles_visitante')->nullable();
        $table->timestamps();
    });
}

