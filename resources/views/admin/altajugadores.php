 <form action="{{ route('jugadores.store') }}" method="POST">
    
    <div class="mb-3">
        <label for="apellido" class="form-label">Apellido</label>
        <input type="text" class="form-control" name="apellido" id="apellido" required>
    </div>
     <div class="mb-3">
         <label for="nombre" class="form-label">Nombre</label>
        <input type="text" class="form-control" name="nombre" id="nombre" required>
    </div>
    <div class="mb-3">
        <label for="dni" class="form-label">DNI</label>
        <input type="text" class="form-control" name="dni" id="dni" required>
    </div>
        <label for="correo" class="form-label">Correo electrónico</label>
        <input type="email" class="form-control" name="correo" id="correo" required>
    </div>
    <div class="mb-3">
        <label for="nombreclub" class="form-label">Nombre del Club</label>
        <input type="text" class="form-control" name="nombreclub" id="nombreclub" required>
    </div>
    <div class="d-flex justify-content-end">
        <button type="submit" class="btn btn-primary">Cargar Jugador</button>
    </div>
</form>

