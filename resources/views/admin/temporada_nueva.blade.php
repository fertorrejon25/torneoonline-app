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
            <form id="formTemporada" method="POST" action="{{ route('temporada.store') }}">
                @csrf
                <div class="row">
                    <!-- Nombre de la Temporada -->
                    <div class="col-md-6">
                        <div class="mb-2">
                            <input type="text"
                                   class="form-control @error('nombretemporada') is-invalid @enderror"
                                   placeholder="Nombre de la temporada (Ej: Temporada 2025)"
                                   name="nombretemporada"
                                   value="{{ old('nombretemporada') }}"
                                   required>
                            @error('nombretemporada')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <label class="form-label mt-3 mb-2">Seleccionar Equipos</label>
                        <select name="equipos[]"
                                class="form-control @error('equipos') is-invalid @enderror"
                                multiple
                                size="8"
                                required>
                            @foreach ($equipos as $equipo)
                                <option value="{{ $equipo->id }}" 
                                        @if(in_array($equipo->id, old('equipos', []))) selected @endif>
                                    {{ $equipo->NombreEquipos }}
                                </option>
                            @endforeach
                        </select>
                        <small class="text-muted d-block mt-2">Mantén presionado Ctrl (o Cmd en Mac) para seleccionar múltiples</small>
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
                        <tr>
                            <td>
                                <strong>{{ $temporada->NombreTemporada }}</strong>
                            </td>
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
                                    <a href="{{ route('temporada.show', $temporada->id) }}"
                                       class="btn btn-sm btn-info"
                                       title="Ver detalles">
                                        Ver
                                    </a>
                                    <a href="{{ route('temporada.edit', $temporada->id) }}"
                                       class="btn btn-sm btn-warning"
                                       title="Editar">
                                        Editar
                                    </a>
                                    <form action="{{ route('temporada.destroy', $temporada->id) }}"
                                          method="POST"
                                          style="display:inline;"
                                          onsubmit="return confirm('¿Estás seguro de que deseas eliminar esta temporada?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" title="Eliminar">
                                            Eliminar
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div class="alert alert-info mt-4">
            No hay temporadas creadas aún.
        </div>
    @endif
@endsection