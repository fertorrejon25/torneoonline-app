@extends('layouts.jugador')

@section('title', 'Máximos Goleadores (Histórico)')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">

            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">
                        <i class="fas fa-futbol"></i> Máximos Goleadores (Histórico)
                    </h4>
                    <div class="d-flex gap-2">
                        <a href="{{ route('user.ranking-historico') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Volver al Ranking Histórico
                        </a>
                        <a href="{{ route('user.maximos_asistentes_historico') }}" class="btn btn-success">
                            <i class="fas fa-hands-helping"></i> Ver Máximos Asistentes (Histórico)
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    @php
                        $top = isset($goleadores) ? $goleadores->take(20) : collect();
                    @endphp

                    @if($top->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead class="table-dark">
                                    <tr>
                                        <th class="text-center" style="width: 60px;">#</th>
                                        <th>Jugador</th>
                                        <th class="text-center" style="width: 120px;">Equipo</th>
                                        <th class="text-center" style="width: 110px;">Partidos</th>
                                        <th class="text-center" style="width: 110px;">Goles</th>
                                        <th class="text-center" style="width: 120px;">Promedio</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($top as $index => $g)
                                        @php
                                            $jugador   = is_array($g) ? ($g['jugador'] ?? '—')        : ($g->jugador ?? '—');
                                            $equipo    = is_array($g) ? ($g['equipo'] ?? 'Sin equipo'): ($g->equipo ?? 'Sin equipo');
                                            $logo      = is_array($g) ? ($g['equipo_logo'] ?? null)   : ($g->equipo_logo ?? null);
                                            $partidos  = (int)(is_array($g) ? ($g['partidos'] ?? 0)   : ($g->partidos ?? 0));
                                            $goles     = (int)(is_array($g) ? ($g['goles'] ?? 0)      : ($g->goles ?? 0));
                                            $prom      = $partidos > 0 ? number_format($goles / $partidos, 2) : '0.00';
                                        @endphp
                                        <tr>
                                            <td class="text-center">
                                                @if($index === 0)
                                                    <i class="fas fa-trophy text-warning fa-lg" title="1º"></i>
                                                @elseif($index === 1)
                                                    <i class="fas fa-medal text-secondary fa-lg" title="2º"></i>
                                                @elseif($index === 2)
                                                    <i class="fas fa-medal" style="color:#CD7F32;" title="3º"></i>
                                                @else
                                                    <span class="badge bg-light text-dark">{{ $index + 1 }}</span>
                                                @endif
                                            </td>
                                            <td><strong>{{ $jugador }}</strong></td>
                                            <td class="text-center">
                                                @if($logo)
                                                    <img src="{{ asset('storage/' . $logo) }}"
                                                         alt="{{ $equipo }}"
                                                         width="50" height="50"
                                                         style="object-fit:contain"
                                                         title="{{ $equipo }}">
                                                @else
                                                    <span class="text-muted small">{{ $equipo }}</span>
                                                @endif
                                            </td>
                                            <td class="text-center">{{ $partidos }}</td>
                                            <td class="text-center">
                                                <span class="badge bg-success fs-6">
                                                    <i class="fas fa-futbol"></i> {{ $goles }}
                                                </span>
                                            </td>
                                            <td class="text-center">{{ $prom }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-3">
                            <small class="text-muted">
                                <i class="fas fa-info-circle"></i>
                                Mostrando top {{ $top->count() }} goleadores (suma en todas las temporadas).
                            </small>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-futbol fa-4x text-muted mb-3"></i>
                            <h4 class="text-muted">Sin datos de goles históricos</h4>
                            <p class="text-muted">No hay registros de goles acumulados.</p>
                        </div>
                    @endif
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
