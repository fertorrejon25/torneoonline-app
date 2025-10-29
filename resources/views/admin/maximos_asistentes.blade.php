@extends('layouts.admin')

@section('title', 'Máximos Asistentes')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">
                        <i class="fas fa-hands-helping"></i> Máximos Asistentes
                    </h4>
                    <div>
                        <a href="{{ route('admin.dashboard', ['section' => 'ranking', 'temporada' => $temporadaId]) }}" 
                           class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Volver al Ranking
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if($temporadaId && $temporadaActual)
                        @if($asistentes->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-striped table-hover">
                                    <thead class="table-dark">
                                        <tr>
                                            <th class="text-center" style="width: 60px;">#</th>
                                            <th>Jugador</th>
                                            <th class="text-center" style="width: 100px;">Equipo</th>
                                            <th class="text-center">Partidos</th>
                                            <th class="text-center">Asistencias</th>
                                            <th class="text-center">Promedio</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($asistentes as $index => $asistente)
                                            <tr>
                                                <td class="text-center">
                                                    @if($index === 0)
                                                        <i class="fas fa-trophy text-warning fa-lg"></i>
                                                    @elseif($index === 1)
                                                        <i class="fas fa-medal text-secondary fa-lg"></i>
                                                    @elseif($index === 2)
                                                        <i class="fas fa-medal" style="color: #CD7F32;"></i>
                                                    @else
                                                        <span class="badge bg-light text-dark">{{ $index + 1 }}</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <strong>{{ $asistente['jugador'] }}</strong>
                                                </td>
                                                <td class="text-center">
                                                    @if(isset($asistente['FotoEquipo']) && $asistente['FotoEquipo'])
                                                        <img src="{{ asset('storage/' . $asistente['FotoEquipo']) }}" 
                                                             alt="{{ $asistente['equipo'] }}" 
                                                             class="img-fluid"
                                                             style="width: 50px; height: 50px; object-fit: contain;"
                                                             title="{{ $asistente['equipo'] }}">
                                                    @else
                                                        <span class="text-muted small">{{ $asistente['equipo'] ?? 'Sin equipo' }}</span>
                                                    @endif
                                                </td>
                                                <td class="text-center">
                                                    {{ $asistente['partidos'] }}
                                                </td>
                                                <td class="text-center">
                                                    <span class="badge bg-primary fs-6">
                                                        <i class="fas fa-hands-helping"></i> {{ $asistente['asistencias'] }}
                                                    </span>
                                                </td>
                                                <td class="text-center">
                                                    @if($asistente['partidos'] > 0)
                                                        {{ number_format($asistente['asistencias'] / $asistente['partidos'], 2) }}
                                                    @else
                                                        0.00
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            
                            <div class="mt-3">
                                <small class="text-muted">
                                    <i class="fas fa-info-circle"></i> 
                                    Mostrando {{ $asistentes->count() }} asistente(s)
                                </small>
                            </div>
                        @else
                            <div class="text-center py-5">
                                <i class="fas fa-hands-helping fa-4x text-muted mb-3"></i>
                                <h4 class="text-muted">Sin datos de asistentes</h4>
                                <p class="text-muted">Aún no hay estadísticas de asistencias registradas para esta temporada</p>
                            </div>
                        @endif
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-exclamation-triangle fa-4x text-warning mb-3"></i>
                            <h4 class="text-warning">Selecciona una temporada</h4>
                            <p class="text-muted">Por favor, selecciona una temporada para ver los máximos asistentes.</p>
                            <div class="mt-4">
                                <a href="{{ route('admin.dashboard', ['section' => 'ranking']) }}" class="btn btn-primary">
                                    <i class="fas fa-trophy"></i> Ir al Ranking
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