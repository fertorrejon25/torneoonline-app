@extends('layouts.app')

@section('title', 'Alta de Temporadas')

@section('content')
    <!-- Mensajes de éxito y errores -->
    <div id="alertas"></div>

    <!-- Errores de validación -->
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    <div class="row">
        <!-- Formulario de creación -->
        <div class="col-md-9">
            <form id="formTemporada" method="POST" action="{{ route('admin.temporada.store') }}">
                @csrf
                <div class="row">
                    <!-- Nombre de la Temporada -->
                    <div class="col-md-6">
                        <div class="mb-2">
                            <input type="text" class="form-control @error('nombretemporada') is-invalid @enderror"
                                placeholder="Nombre de la temporada (Ej: Temporada 2025)" name="nombretemporada"
                                value="{{ old('nombretemporada') }}" required>
                            @error('nombretemporada')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <label class="form-label mt-3 mb-2">Seleccionar Equipos</label>
                        <div class="border rounded p-3" style="max-height: 250px; overflow-y: auto;">
                            @foreach ($equipos as $equipo)
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="equipos[]" value="{{ $equipo->id }}"
                                        id="equipo_{{ $equipo->id }}" {{ in_array($equipo->id, old('equipos', [])) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="equipo_{{ $equipo->id }}">
                                        {{ $equipo->NombreEquipos }}
                                    </label>
                                </div>
                            @endforeach
                        </div>
                        <small class="text-muted d-block mt-2">Podés seleccionar varios marcando las casillas</small>

                        @error('equipos')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror

                        <div class="mt-3 d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary">Crear Temporada</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Listado de temporadas -->
    @if (!empty($temporadas) && count($temporadas) > 0)
        <hr class="my-5">
        <h4 class="mb-4">Temporadas Cargadas</h4>

        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="table-light">
                    <tr>
                        <th>Nombre</th>
                        <th>Equipos</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($temporadas as $temporada)
                        <tr id="temporada-{{ $temporada->id }}">
                            <td><strong>{{ $temporada->NombreTemporada }}</strong></td>
                            <td>
                                @if ($temporada->equipos->count() > 0)
                                    <small class="badge bg-info">{{ $temporada->equipos->count() }} equipo(s)</small>
                                    <div class="mt-2">
                                        @foreach ($temporada->equipos as $equipo)
                                            <span class="badge bg-secondary">{{ $equipo->NombreEquipos }}</span>
                                        @endforeach
                                    </div>
                                @else
                                    <small class="text-muted">Sin equipos</small>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('admin.temporada.show', $temporada->id) }}" class="btn btn-sm btn-info">Ver</a>
                                    <form class="form-eliminar" action="{{ route('temporada.destroy', $temporada->id) }}" method="POST" style="display:inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger">Eliminar</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div class="alert alert-info mt-4">No hay temporadas creadas aún.</div>
    @endif
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Manejar eliminación de temporadas con AJAX
    const formsEliminar = document.querySelectorAll('.form-eliminar');
    
    formsEliminar.forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            if (!confirm('¿Estás seguro de que deseas eliminar esta temporada?')) {
                return;
            }
            
            const formData = new FormData(this);
            const url = this.action;
            const temporadaId = url.split('/').pop();
            
            fetch(url, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Eliminar la fila de la tabla
                    const fila = document.getElementById(`temporada-${temporadaId}`);
                    if (fila) {
                        fila.remove();
                    }
                    
                    // Mostrar mensaje de éxito
                    mostrarMensaje('Temporada eliminada correctamente', 'success');
                    
                    // Si no quedan temporadas, recargar la página para mostrar el mensaje de "No hay temporadas"
                    const filasRestantes = document.querySelectorAll('tbody tr');
                    if (filasRestantes.length === 0) {
                        location.reload();
                    }
                } else {
                    mostrarMensaje(data.message || 'Error al eliminar la temporada', 'danger');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                mostrarMensaje('Error al eliminar la temporada', 'danger');
            });
        });
    });
    
    function mostrarMensaje(mensaje, tipo) {
        const alertasDiv = document.getElementById('alertas');
        const alerta = document.createElement('div');
        alerta.className = `alert alert-${tipo} alert-dismissible fade show`;
        alerta.innerHTML = `
            ${mensaje}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        alertasDiv.appendChild(alerta);
        
        // Auto-eliminar después de 5 segundos
        setTimeout(() => {
            if (alerta.parentNode) {
                alerta.remove();
            }
        }, 5000);
    }
});
</script>
@endpush