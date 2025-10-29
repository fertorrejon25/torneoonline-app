@extends('layouts.admin')

@section('title', 'Tabla de Posiciones - Ranking')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">
                        <i class="fas fa-trophy"></i> Tabla de Posiciones - Ranking General
                    </h4>
                    <div>
                        {{-- Botones para máximos goleadores y asistentes --}}
                        <a href="{{ route('admin.maximos_goleadores', ['temporada' => $temporadaActual->id ?? null]) }}" class="btn btn-info me-2">
                            <i class="fas fa-futbol"></i> Ver Máximos Goleadores
                        </a>
                        <a href="{{ route('admin.maximos_asistentes', ['temporada' => $temporadaActual->id ?? null]) }}" class="btn btn-success">
                            <i class="fas fa-assistive-listening-systems"></i> Ver Máximos Asistentes
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    {{-- Selector de Temporada --}}
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label for="temporadaSelector" class="form-label"><strong>Seleccionar Temporada:</strong></label>
                            <select class="form-select form-select-lg" id="temporadaSelector" onchange="cambiarTemporada(this.value)">
                                <option value="">-- Seleccionar Temporada --</option>
                                @if(isset($temporadas) && $temporadas->count() > 0)
                                    @foreach($temporadas as $temp)
                                        <option value="{{ $temp->id }}" {{ isset($temporadaActual) && $temporadaActual->id == $temp->id ? 'selected' : '' }}>
                                            {{ $temp->NombreTemporada }}
                                        </option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                    </div>

                    {{-- Mostrar tabla de posiciones --}}
                    @if(isset($temporadaActual) && isset($equipos) && $equipos->count() > 0)
                        @php
                            // Crear array combinado de equipos con sus estadísticas
                            $equiposConEstadisticas = [];
                            
                            // Primero añadir equipos que tienen estadísticas
                            if(isset($tablaPosiciones) && $tablaPosiciones->count() > 0) {
                                foreach($tablaPosiciones as $estadistica) {
                                    $equiposConEstadisticas[$estadistica->equipo->id] = [
                                        'equipo' => $estadistica->equipo,
                                        'estadistica' => $estadistica,
                                        'tiene_estadisticas' => true
                                    ];
                                }
                            }
                            
                            // Luego añadir equipos que no tienen estadísticas
                            foreach($equipos as $equipo) {
                                if(!isset($equiposConEstadisticas[$equipo->id])) {
                                    $equiposConEstadisticas[$equipo->id] = [
                                        'equipo' => $equipo,
                                        'estadistica' => null,
                                        'tiene_estadisticas' => false
                                    ];
                                }
                            }
                            
                            // Ordenar por puntos (si tienen) o alfabéticamente
                            usort($equiposConEstadisticas, function($a, $b) {
                                if ($a['tiene_estadisticas'] && $b['tiene_estadisticas']) {
                                    // Ordenar por puntos, luego por diferencia de goles
                                    if ($b['estadistica']->puntos != $a['estadistica']->puntos) {
                                        return $b['estadistica']->puntos - $a['estadistica']->puntos;
                                    }
                                    return $b['estadistica']->diferencia_goles - $a['estadistica']->diferencia_goles;
                                } elseif ($a['tiene_estadisticas']) {
                                    return -1;
                                } elseif ($b['tiene_estadisticas']) {
                                    return 1;
                                } else {
                                    return strcmp($a['equipo']->NombreEquipos, $b['equipo']->NombreEquipos);
                                }
                            });
                        @endphp

                        <div class="table-responsive">
                            <table class="table table-bordered table-hover table-striped">
                                <thead class="table-dark">
                                    <tr>
                                        <th width="60" class="text-center">#</th>
                                        <th>Equipo</th>
                                        <th class="text-center">PJ</th>
                                        <th class="text-center">PG</th>
                                        <th class="text-center">PE</th>
                                        <th class="text-center">PP</th>
                                        <th class="text-center">GF</th>
                                        <th class="text-center">GC</th>
                                        <th class="text-center">DG</th>
                                        <th class="text-center"><strong>Pts</strong></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($equiposConEstadisticas as $index => $data)
                                        @php
                                            $posicion = $index + 1;
                                            $equipo = $data['equipo'];
                                            $tieneEstadisticas = $data['tiene_estadisticas'];
                                            $estadistica = $data['estadistica'];
                                            
                                            // Valores por defecto para equipos sin estadísticas
                                            $pj = $tieneEstadisticas ? $estadistica->partidos_jugados : 0;
                                            $pg = $tieneEstadisticas ? $estadistica->partidos_ganados : 0;
                                            $pe = $tieneEstadisticas ? $estadistica->partidos_empatados : 0;
                                            $pp = $tieneEstadisticas ? $estadistica->partidos_perdidos : 0;
                                            $gf = $tieneEstadisticas ? $estadistica->goles_favor : 0;
                                            $gc = $tieneEstadisticas ? $estadistica->goles_contra : 0;
                                            $dg = $tieneEstadisticas ? $estadistica->diferencia_goles : 0;
                                            $puntos = $tieneEstadisticas ? $estadistica->puntos : 0;
                                        @endphp
                                        
                                        <tr class="{{ $posicion <= 3 && $tieneEstadisticas ? 'table-warning' : '' }} {{ !$tieneEstadisticas ? 'table-light' : '' }}">
                                            <td class="text-center align-middle">
                                                @if($tieneEstadisticas)
                                                    @if($posicion == 1)
                                                        <span class="badge bg-warning text-dark fs-6">🥇</span>
                                                    @elseif($posicion == 2)
                                                        <span class="badge bg-secondary fs-6">🥈</span>
                                                    @elseif($posicion == 3)
                                                        <span class="badge bg-danger fs-6">🥉</span>
                                                    @else
                                                        <strong>{{ $posicion }}</strong>
                                                    @endif
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td class="align-middle">
                                                <div class="d-flex align-items-center">
                                                    @if($equipo->FotoEquipo)
                                                        <img src="{{ asset('storage/' . $equipo->FotoEquipo) }}" 
                                                             alt="{{ $equipo->NombreEquipos }}" 
                                                             class="rounded-circle me-2" 
                                                             width="35" height="35"
                                                             style="object-fit: cover;">
                                                    @else
                                                        <div class="rounded-circle bg-light d-flex align-items-center justify-content-center me-2" 
                                                             style="width: 35px; height: 35px;">
                                                            <i class="fas fa-users text-muted"></i>
                                                        </div>
                                                    @endif
                                                    <div>
                                                        <strong>{{ $equipo->NombreEquipos }}</strong>
                                                        @if(!$tieneEstadisticas)
                                                            <br>
                                                            <small class="text-muted">Sin partidos jugados</small>
                                                        @endif
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="text-center align-middle {{ !$tieneEstadisticas ? 'text-muted' : '' }}">{{ $pj }}</td>
                                            <td class="text-center align-middle {{ $tieneEstadisticas ? 'text-success' : 'text-muted' }}">{{ $pg }}</td>
                                            <td class="text-center align-middle {{ $tieneEstadisticas ? 'text-warning' : 'text-muted' }}">{{ $pe }}</td>
                                            <td class="text-center align-middle {{ $tieneEstadisticas ? 'text-danger' : 'text-muted' }}">{{ $pp }}</td>
                                            <td class="text-center align-middle {{ $tieneEstadisticas ? 'text-primary' : 'text-muted' }}">{{ $gf }}</td>
                                            <td class="text-center align-middle {{ $tieneEstadisticas ? 'text-danger' : 'text-muted' }}">{{ $gc }}</td>
                                            <td class="text-center align-middle {{ $tieneEstadisticas ? ($dg >= 0 ? 'text-success' : 'text-danger') : 'text-muted' }}">
                                                <strong>{{ $dg > 0 ? '+' : '' }}{{ $dg }}</strong>
                                            </td>
                                            <td class="text-center align-middle">
                                                @if($tieneEstadisticas)
                                                    <span class="badge bg-dark fs-6">{{ $puntos }}</span>
                                                @else
                                                    <span class="badge bg-light text-dark fs-6">{{ $puntos }}</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                    @elseif(isset($temporadaActual) && (!isset($equipos) || $equipos->count() == 0))
                        {{-- CASO: Temporada existe pero NO tiene equipos --}}
                        <div class="text-center py-5">
                            <i class="fas fa-users-slash fa-4x text-muted mb-3"></i>
                            <h4 class="text-muted">No hay equipos en {{ $temporadaActual->NombreTemporada }}</h4>
                            <p class="text-muted">La temporada está creada pero no tiene equipos asignados.</p>
                            <div class="mt-4">
                                <a href="{{ route('admin.dashboard', ['section' => 'temporadacargadas']) }}" class="btn btn-primary me-2">
                                    <i class="fas fa-edit"></i> Gestionar Temporadas
                                </a>
                                <a href="{{ route('admin.dashboard', ['section' => 'equipos']) }}" class="btn btn-success">
                                    <i class="fas fa-plus"></i> Agregar Equipos
                                </a>
                            </div>
                        </div>

                    @else
                        {{-- CASO: No hay temporada seleccionada --}}
                        <div class="text-center py-5">
                            <i class="fas fa-trophy fa-4x text-muted mb-3"></i>
                            <h4 class="text-muted">Selecciona una temporada</h4>
                            <p class="text-muted">Por favor, selecciona una temporada para ver el ranking.</p>
                            <div class="mt-4">
                                <a href="{{ route('admin.dashboard', ['section' => 'temporada']) }}" class="btn btn-primary">
                                    <i class="fas fa-plus"></i> Crear Nueva Temporada
                                </a>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function cambiarTemporada(temporadaId) {
    if (temporadaId) {
        window.location.href = '{{ url("admin/dashboard?section=ranking") }}&temporada=' + temporadaId;
    } else {
        window.location.href = '{{ url("admin/dashboard?section=ranking") }}';
    }
}
</script>
@endpush