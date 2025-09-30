

public function up(): void
{
    Schema::create('fechas', function (Blueprint $table) {
        $table->id();
        $table->foreignId('temporada_id')->constrained('temporadas')->onDelete('cascade');
        $table->string('nombre'); // Ej: Fecha 1, Fecha 2
        $table->date('dia');
        $table->timestamps();
    });
}

