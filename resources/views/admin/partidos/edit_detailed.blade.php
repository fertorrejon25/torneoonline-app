@extends('layouts.admin')

@section('title', 'Editar Partido')

@section('content')
<div class="container py-4">
    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3>Editando Partido</h3>
        <a href="{{ $partido->fecha ? route('admin.temporadas.show', $partido->fecha->temporada_id) : route('admin.dashboard', ['section' => 'temporadacargadas']) }}" 
           class="btn btn-outline-secondary btn-sm">Volver</a>
    </div>

    {{-- Información del Partido --}}
    <div class="card mb-4">
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-md-5 text-center">
                    <div class="d-flex flex-column align-items-center">
                        <img src="{{ $partido->equipoLocal->FotoEquipo ? asset('storage/'.$partido->equipoLocal->FotoEquipo) : asset('images/placeholder.png') }}" 
                             class="mb-2" style="width: 80px; height: 80px; object-fit: contain; border-radius: 50%;">
                        <h5 class="mb-0">{{ $partido->equipoLocal->NombreEquipos }}</h5>
                    </div>
                </div>
                
                <div class="col-md-2 text-center">
                    <div class="resultado-display mb-3">
                        <span class="badge bg-primary fs-4 px-4 py-2">
                            {{ $partido->goles_local ?? 0 }} : {{ $partido->goles_visitante ?? 0 }}
                        </span>
                    </div>
                    <small class="text-muted">
                        @if($partido->fecha)
                            {{ $partido->fecha->nombre }}
                        @else
                            Sin fecha asignada
                        @endif
                    </small>
                </div>
                
                <div class="col-md-5 text-center">
                    <div class="d-flex flex-column align-items-center">
                        <img src="{{ $partido->equipoVisitante->FotoEquipo ? asset('storage/'.$partido->equipoVisitante->FotoEquipo) : asset('images/placeholder.png') }}" 
                             class="mb-2" style="width: 80px; height: 80px; object-fit: contain; border-radius: 50%;">
                        <h5 class="mb-0">{{ $partido->equipoVisitante->NombreEquipos }}</h5>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Formulario de Estadísticas --}}
    <form action="{{ route('admin.partidos.update_detailed', $partido->id) }}" method="POST">
        @csrf
        @method('PUT')
        
        <div class="row">
            {{-- Equipo Local --}}
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">{{ $partido->equipoLocal->NombreEquipos }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Jugador</th>
                                        <th width="80">Jugó</th>
                                        <th width="80">Goles</th>
                                        <th width="80">Asist.</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($partido->equipoLocal->jugadores as $jugador)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <img src="{{ $jugador->foto_jugador ? asset('storage/'.$jugador->foto_jugador) : asset('images/player-placeholder.png') }}" 
                                                     style="width: 30px; height: 30px; object-fit: cover; border-radius: 50%;" class="me-2">
                                                <span>{{ $jugador->user->name }}</span>
                                            </div>
                                        </td>
                                        <td>
                                            <input type="checkbox" name="jugadores[{{ $jugador->id }}][jugo]" value="1" 
                                                   class="form-check-input jugo-checkbox" 
                                                   data-equipo="local"
                                                   {{ old('jugadores.'.$jugador->id.'.jugo', $estadisticasPartido[$jugador->id]['jugo'] ?? false) ? 'checked' : '' }}>
                                        </td>
                                        <td>
                                            <input type="number" name="jugadores[{{ $jugador->id }}][goles]" 
                                                   min="0" 
                                                   class="form-control form-control-sm goles-input"
                                                   data-equipo="local"
                                                   value="{{ old('jugadores.'.$jugador->id.'.goles', $estadisticasPartido[$jugador->id]['goles'] ?? 0) }}">
                                        </td>
                                        <td>
                                            <input type="number" name="jugadores[{{ $jugador->id }}][asistencias]" 
                                                   min="0" 
                                                   class="form-control form-control-sm"
                                                   value="{{ old('jugadores.'.$jugador->id.'.asistencias', $estadisticasPartido[$jugador->id]['asistencias'] ?? 0) }}">
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr class="table-info">
                                        <td><strong>Total</strong></td>
                                        <td id="total-jugadores-local">0</td>
                                        <td id="total-goles-local">0</td>
                                        <td id="total-asistencias-local">0</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Equipo Visitante --}}
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-danger text-white">
                        <h5 class="mb-0">{{ $partido->equipoVisitante->NombreEquipos }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Jugador</th>
                                        <th width="80">Jugó</th>
                                        <th width="80">Goles</th>
                                        <th width="80">Asist.</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($partido->equipoVisitante->jugadores as $jugador)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <img src="{{ $jugador->foto_jugador ? asset('storage/'.$jugador->foto_jugador) : asset('images/player-placeholder.png') }}" 
                                                     style="width: 30px; height: 30px; object-fit: cover; border-radius: 50%;" class="me-2">
                                                <span>{{ $jugador->user->name }}</span>
                                            </div>
                                        </td>
                                        <td>
                                            <input type="checkbox" name="jugadores[{{ $jugador->id }}][jugo]" value="1" 
                                                   class="form-check-input jugo-checkbox"
                                                   data-equipo="visitante"
                                                   {{ old('jugadores.'.$jugador->id.'.jugo', $estadisticasPartido[$jugador->id]['jugo'] ?? false) ? 'checked' : '' }}>
                                        </td>
                                        <td>
                                            <input type="number" name="jugadores[{{ $jugador->id }}][goles]" 
                                                   min="0" 
                                                   class="form-control form-control-sm goles-input"
                                                   data-equipo="visitante"
                                                   value="{{ old('jugadores.'.$jugador->id.'.goles', $estadisticasPartido[$jugador->id]['goles'] ?? 0) }}">
                                        </td>
                                        <td>
                                            <input type="number" name="jugadores[{{ $jugador->id }}][asistencias]" 
                                                   min="0" 
                                                   class="form-control form-control-sm"
                                                   value="{{ old('jugadores.'.$jugador->id.'.asistencias', $estadisticasPartido[$jugador->id]['asistencias'] ?? 0) }}">
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr class="table-info">
                                        <td><strong>Total</strong></td>
                                        <td id="total-jugadores-visitante">0</td>
                                        <td id="total-goles-visitante">0</td>
                                        <td id="total-asistencias-visitante">0</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Campos ocultos para los totales --}}
        <input type="hidden" name="goles_local" id="input-goles-local" value="{{ $partido->goles_local ?? 0 }}">
        <input type="hidden" name="goles_visitante" id="input-goles-visitante" value="{{ $partido->goles_visitante ?? 0 }}">

        {{-- Botones --}}
        <div class="row mt-4">
            <div class="col-12 text-center">
                <button type="submit" class="btn btn-success btn-lg px-5">Confirmar Resultado</button>
                <a href="{{ $partido->fecha ? route('admin.temporadas.show', $partido->fecha->temporada_id) : route('admin.dashboard', ['section' => 'temporadacargadas']) }}" 
                   class="btn btn-secondary btn-lg px-5">Cancelar</a>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Función para calcular totales por equipo
    function calcularTotales(equipo) {
        let totalJugadores = 0;
        let totalGoles = 0;
        let totalAsistencias = 0;

        // Calcular jugadores que jugaron
        const checkboxes = document.querySelectorAll(`.jugo-checkbox[data-equipo="${equipo}"]:checked`);
        totalJugadores = checkboxes.length;

        // Calcular goles
        const golesInputs = document.querySelectorAll(`.goles-input[data-equipo="${equipo}"]`);
        golesInputs.forEach(input => {
            totalGoles += parseInt(input.value) || 0;
        });

        // Calcular asistencias (todas las inputs del equipo)
        const asistenciasInputs = document.querySelectorAll(`input[name^="jugadores"][name$="[asistencias]"]`);
        asistenciasInputs.forEach(input => {
            // Verificar si este input pertenece al equipo actual
            const row = input.closest('tr');
            const checkbox = row.querySelector('.jugo-checkbox');
            if (checkbox && checkbox.getAttribute('data-equipo') === equipo) {
                totalAsistencias += parseInt(input.value) || 0;
            }
        });

        // Actualizar display
        document.getElementById(`total-jugadores-${equipo}`).textContent = totalJugadores;
        document.getElementById(`total-goles-${equipo}`).textContent = totalGoles;
        document.getElementById(`total-asistencias-${equipo}`).textContent = totalAsistencias;

        // Actualizar campos ocultos para el formulario
        if (equipo === 'local') {
            document.getElementById('input-goles-local').value = totalGoles;
        } else {
            document.getElementById('input-goles-visitante').value = totalGoles;
        }

        // Actualizar display del resultado
        const golesLocal = document.getElementById('input-goles-local').value;
        const golesVisitante = document.getElementById('input-goles-visitante').value;
        
        // Actualizar el badge del resultado (si existe)
        const resultadoBadge = document.querySelector('.resultado-display .badge');
        if (resultadoBadge) {
            resultadoBadge.textContent = `${golesLocal} : ${golesVisitante}`;
        }
    }

    // Event listeners para cambios en los inputs
    document.querySelectorAll('.jugo-checkbox, .goles-input').forEach(element => {
        element.addEventListener('change', function() {
            const equipo = this.getAttribute('data-equipo');
            calcularTotales(equipo);
        });
    });

    // Event listeners para inputs numéricos (actualizar en tiempo real)
    document.querySelectorAll('.goles-input').forEach(input => {
        input.addEventListener('input', function() {
            const equipo = this.getAttribute('data-equipo');
            calcularTotales(equipo);
        });
    });

    // Calcular totales iniciales
    calcularTotales('local');
    calcularTotales('visitante');
});
</script>
@endpush