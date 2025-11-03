@extends('layouts.jugador')
@section('title', 'Temporada Actual')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">

            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">
                        <i class="fas fa-trophy"></i> Tabla de Posiciones - Temporada Actual
                    </h4>
                    <div>
                        {{-- Botones que abren las vistas del usuario --}}
                        <a href="{{ route('user.maximos_goleadores', ['temporada' => $temporadaActual->id ?? null]) }}" 
                           class="btn btn-info me-2">
                            <i class="fas fa-futbol"></i> Ver Máximos Goleadores
                        </a>
                        <a href="{{ route('user.maximos_asistentes', ['temporada' => $temporadaActual->id ?? null]) }}" 
                           class="btn btn-success">
                            <i class="fas fa-hands-helping"></i> Ver Máximos Asistentes
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    {{-- Selector de temporada --}}
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

                    {{-- Mostrar tabla --}}
                    @if(isset($temporadaActual) && isset($equipos) && $equipos->count() > 0)
                        @php
                            $equiposConEstadisticas = [];
                            if(isset($tablaPosiciones) && $tablaPosiciones->count() > 0) {
                                foreach($tablaPosiciones as $estadistica) {
                                    if($estadistica->equipo) {
                                        $equiposConEstadisticas[$estadistica->equipo->id] = [
                                            'equipo' => $estadistica->equipo,
                                            'estadistica' => $estadistica,
                                            'tiene_estadisticas' => true
                                        ];
                                    }
                                }
                            }
                            foreach($equipos as $equipo) {
                                if(!isset($equiposConEstadisticas[$equipo->id])) {
                                    $equiposConEstadisticas[$equipo->id] = [
                                        'equipo' => $equipo,
                                        'estadistica' => null,
                                        'tiene_estadisticas' => false
                                    ];
                                }
                            }
                            usort($equiposConEstadisticas, function($a, $b) {
                                if ($a['tiene_estadisticas'] && $b['tiene_estadisticas']) {
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
                                        <th class="text-center">#</th>
                                        <th>Equipo</th>
                                        <th class="text-center">PJ</th>
                                        <th class="text-center">PG</th>
                                        <th class="text-center">PE</th>
                                        <th class="text-center">PP</th>
                                        <th class="text-center">GF</th>
                                        <th class="text-center">GC</th>
                                        <th class="text-center">DG</th>
                                        <th class="text-center"><strong>PTS</strong></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($equiposConEstadisticas as $index => $data)
                                        @php
                                            $posicion = $index + 1;
                                            $equipo = $data['equipo'];
                                            $tiene = $data['tiene_estadisticas'];
                                            $e = $data['estadistica'];
                                        @endphp
                                        <tr class="{{ $posicion <= 3 && $tiene ? 'table-warning' : '' }} {{ !$tiene ? 'table-light' : '' }}">
                                            <td class="text-center align-middle">
                                                @if($tiene)
                                                    @if($posicion == 1)
                                                        🥇
                                                    @elseif($posicion == 2)
                                                        🥈
                                                    @elseif($posicion == 3)
                                                        🥉
                                                    @else
                                                        <strong>{{ $posicion }}</strong>
                                                    @endif
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td class="align-middle d-flex align-items-center">
                                                @if($equipo->FotoEquipo)
                                                    <img src="{{ asset('storage/' . $equipo->FotoEquipo) }}" alt="{{ $equipo->NombreEquipos }}" class="rounded-circle me-2" width="35" height="35">
                                                @else
                                                    <div class="rounded-circle bg-light d-flex align-items-center justify-content-center me-2" style="width:35px;height:35px;">
                                                        <i class="fas fa-users text-muted"></i>
                                                    </div>
                                                @endif
                                                <div>
                                                    <strong>{{ $equipo->NombreEquipos }}</strong>
                                                    @if(!$tiene)
                                                        <br><small class="text-muted">Sin partidos</small>
                                                    @endif
                                                </div>
                                            </td>
                                            <td class="text-center">{{ $tiene ? $e->partidos_jugados : 0 }}</td>
                                            <td class="text-center">{{ $tiene ? $e->partidos_ganados : 0 }}</td>
                                            <td class="text-center">{{ $tiene ? $e->partidos_empatados : 0 }}</td>
                                            <td class="text-center">{{ $tiene ? $e->partidos_perdidos : 0 }}</td>
                                            <td class="text-center">{{ $tiene ? $e->goles_favor : 0 }}</td>
                                            <td class="text-center">{{ $tiene ? $e->goles_contra : 0 }}</td>
                                            <td class="text-center">{{ $tiene ? $e->diferencia_goles : 0 }}</td>
                                            <td class="text-center fw-bold">{{ $tiene ? $e->puntos : 0 }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-trophy fa-4x text-muted mb-3"></i>
                            <h4 class="text-muted">No hay datos de la temporada seleccionada</h4>
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
    const base = '{{ url("user/dashboard?section=temporada-actual") }}';
    window.location.href = temporadaId ? (base + '&temporada=' + temporadaId) : base;
}
</script>
@endpush
