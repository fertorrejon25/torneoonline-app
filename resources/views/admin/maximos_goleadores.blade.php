@extends('layouts.admin')

@section('title', 'Máximos Goleadores')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">
                        <i class="fas fa-futbol"></i> Máximos Goleadores
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
                        @if($goleadores->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-striped table-hover">
                                    <thead class="table-dark">
                                        <tr>
                                            <th class="text-center" style="width: 60px;">#</th>
                                            <th>Jugador</th>
                                            <th class="text-center" style="width: 100px;">Equipo</th>
                                            <th class="text-center">Partidos</th>
                                            <th class="text-center">Goles</th>
                                            <th class="text-center">Promedio</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($goleadores as $index => $goleador)
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
                                                    <strong>{{ $goleador['jugador'] }}</strong>
                                                </td>
                                                <td class="text-center">
                                                    @if($goleador['equipo_logo'])
                                                        <img src="{{ asset('storage/' . $goleador['equipo_logo']) }}" 
                                                             alt="{{ $goleador['equipo'] }}" 
                                                             class="img-fluid"
                                                             style="width: 50px; height: 50px; object-fit: contain;"
                                                             title="{{ $goleador['equipo'] }}">
                                                    @else
                                                        <span class="text-muted small">{{ $goleador['equipo'] }}</span>
                                                    @endif
                                                </td>
                                                <td class="text-center">
                                                    {{ $goleador['partidos'] }}
                                                </td>
                                                <td class="text-center">
                                                    <span class="badge bg-success fs-6">
                                                        <i class="fas fa-futbol"></i> {{ $goleador['goles'] }}
                                                    </span>
                                                </td>
                                                <td class="text-center">
                                                    @if($goleador['partidos'] > 0)
                                                        {{ number_format($goleador['goles'] / $goleador['partidos'], 2) }}
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
                                    Mostrando {{ $goleadores->count() }} goleador(es)
                                </small>
                            </div>
                        @else
                            <div class="text-center py-5">
                                <i class="fas fa-futbol fa-4x text-muted mb-3"></i>
                                <h4 class="text-muted">Sin datos de goleadores</h4>
                                <p class="text-muted">Aún no hay estadísticas de goles registradas para esta temporada</p>
                            </div>
                        @endif
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-exclamation-triangle fa-4x text-warning mb-3"></i>
                            <h4 class="text-warning">Selecciona una temporada</h4>
                            <p class="text-muted">Por favor, selecciona una temporada para ver los máximos goleadores.</p>
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