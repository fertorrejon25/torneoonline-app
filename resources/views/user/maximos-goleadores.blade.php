@extends('layouts.jugador')
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
                        <a href="{{ route('user.dashboard', ['section' => 'temporada-actual', 'temporada' => $temporadaId]) }}" 
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
                                            <th class="text-center" width="60">#</th>
                                            <th>Jugador</th>
                                            <th class="text-center">Equipo</th>
                                            <th class="text-center">Partidos</th>
                                            <th class="text-center">Goles</th>
                                            <th class="text-center">Promedio</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($goleadores as $index => $g)
                                            <tr>
                                                <td class="text-center">
                                                    @if($index === 0) 🥇
                                                    @elseif($index === 1) 🥈
                                                    @elseif($index === 2) 🥉
                                                    @else {{ $index + 1 }}
                                                    @endif
                                                </td>
                                                <td><strong>{{ $g['jugador'] }}</strong></td>
                                                <td class="text-center">
                                                    @if($g['equipo_logo'])
                                                        <img src="{{ asset('storage/' . $g['equipo_logo']) }}" alt="{{ $g['equipo'] }}" width="50" height="50" style="object-fit:contain">
                                                    @else
                                                        <span class="text-muted small">{{ $g['equipo'] }}</span>
                                                    @endif
                                                </td>
                                                <td class="text-center">{{ $g['partidos'] }}</td>
                                                <td class="text-center"><span class="badge bg-success">{{ $g['goles'] }}</span></td>
                                                <td class="text-center">{{ $g['partidos'] > 0 ? number_format($g['goles'] / $g['partidos'], 2) : '0.00' }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-5">
                                <i class="fas fa-futbol fa-4x text-muted mb-3"></i>
                                <h4 class="text-muted">Sin datos de goleadores</h4>
                            </div>
                        @endif
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-exclamation-triangle fa-4x text-warning mb-3"></i>
                            <h4 class="text-warning">Selecciona una temporada</h4>
                        </div>
                    @endif
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
