@extends('layouts.jugador')

@section('title', 'Ranking Histórico')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">

            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">
                        <i class="fas fa-trophy"></i> Tabla de Posiciones - Ranking Histórico
                    </h4>
                    <div>
                        <a href="{{ route('user.maximos_goleadores_historico') }}" class="btn btn-info me-2">
                            <i class="fas fa-futbol"></i> Ver Máximos Goleadores (Histórico)
                        </a>
                        <a href="{{ route('user.maximos_asistentes_historico') }}" class="btn btn-success">
                            <i class="fas fa-hands-helping"></i> Ver Máximos Asistentes (Histórico)
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    <div class="table-responsive">

                        @php
                            // $tablaHistorica llega desde el controlador ya ordenada
                        @endphp

                        <table class="table table-striped table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th class="text-center" width="60">#</th>
                                    <th>Equipo</th>
                                    <th class="text-center">PJ</th>
                                    <th class="text-center">PG</th>
                                    <th class="text-center">PE</th>
                                    <th class="text-center">PP</th>
                                    <th class="text-center">GF</th>
                                    <th class="text-center">GC</th>
                                    <th class="text-center">DG</th>
                                    <th class="text-center">PTS</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($tablaHistorica as $index => $e)
                                    @php
                                        $posicion = $index + 1;
                                        $dg = ($e->gf ?? 0) - ($e->gc ?? 0);
                                    @endphp
                                    <tr class="{{ $posicion <= 3 ? 'table-warning' : '' }}">
                                        <td class="text-center align-middle">
                                            @if($posicion == 1)
                                                <span class="badge bg-warning text-dark fs-6">🥇</span>
                                            @elseif($posicion == 2)
                                                <span class="badge bg-secondary fs-6">🥈</span>
                                            @elseif($posicion == 3)
                                                <span class="badge bg-danger fs-6">🥉</span>
                                            @else
                                                <strong>{{ $posicion }}</strong>
                                            @endif
                                        </td>
                                        <td class="align-middle">
                                            <div class="d-flex align-items-center">
                                                @if(!empty($e->FotoEquipo))
                                                    <img src="{{ asset('storage/' . $e->FotoEquipo) }}"
                                                         alt="{{ $e->NombreEquipos }}"
                                                         class="rounded-circle me-2"
                                                         width="35" height="35" style="object-fit: cover;">
                                                @else
                                                    <div class="rounded-circle bg-light d-flex align-items-center justify-content-center me-2"
                                                         style="width:35px;height:35px;">
                                                        <i class="fas fa-users text-muted"></i>
                                                    </div>
                                                @endif
                                                <div>
                                                    <strong>{{ $e->NombreEquipos }}</strong>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-center align-middle">{{ $e->pj }}</td>
                                        <td class="text-center align-middle text-success">{{ $e->pg }}</td>
                                        <td class="text-center align-middle text-warning">{{ $e->pe }}</td>
                                        <td class="text-center align-middle text-danger">{{ $e->pp }}</td>
                                        <td class="text-center align-middle text-primary">{{ $e->gf }}</td>
                                        <td class="text-center align-middle text-danger">{{ $e->gc }}</td>
                                        <td class="text-center align-middle {{ $dg >= 0 ? 'text-success' : 'text-danger' }}">
                                            <strong>{{ $dg > 0 ? '+' : '' }}{{ $dg }}</strong>
                                        </td>
                                        <td class="text-center align-middle">
                                            <span class="badge bg-dark fs-6">{{ $e->pts }}</span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="10" class="text-center py-5 text-muted">
                                            <i class="fas fa-info-circle"></i> No hay datos históricos para mostrar.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>

                    </div> {{-- /table-responsive --}}
                </div> {{-- /card-body --}}
            </div> {{-- /card --}}

        </div>
    </div>
</div>
@endsection