@extends('layouts.jugador')

@section('title', 'Estadísticas Históricas del Club')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-xl-10 col-lg-11">

            @php
                // Soporta objeto (modelo Equipo) o array
                $escudo = is_object($equipo)
                    ? ($equipo->FotoEquipo ?? null)
                    : ($equipo['FotoEquipo'] ?? null);

                $nombreEquipo = is_object($equipo)
                    ? ($equipo->NombreEquipos ?? ($equipo->nombre ?? 'Equipo'))
                    : ($equipo['NombreEquipos'] ?? ($equipo['nombre'] ?? 'Equipo'));

                // Valores por defecto si no viene $tot
                $pj  = $tot['pj']  ?? 0;
                $pg  = $tot['pg']  ?? 0;
                $pe  = $tot['pe']  ?? 0;
                $pp  = $tot['pp']  ?? 0;
                $gf  = $tot['gf']  ?? 0;
                $gc  = $tot['gc']  ?? 0;
                $pts = $tot['pts'] ?? 0;

                // Derivados
                $dg  = $gf - $gc;
                $promVictorias = $tot['prom_victorias'] ?? 0;
            @endphp

            {{-- Contenedor horizontal: escudo a la izquierda + contenido (nombre + tabla) --}}
            <div class="d-flex align-items-center gap-3 py-3" style="padding-left: 0; margin-left: -60px;">
                {{-- Escudo grande sin marco --}}
                <div class="flex-shrink-0">
                    @if(!empty($escudo))
                        <img src="{{ asset('storage/' . $escudo) }}"
                             alt="Escudo {{ $nombreEquipo }}"
                             class="escudo-4x4">
                    @else
                        <div class="placeholder-escudo escudo-4x4 d-flex align-items-center justify-content-center">
                            <i class="fas fa-users text-muted"></i>
                        </div>
                    @endif
                </div>

                {{-- Nombre y tabla (sin tarjeta alrededor) --}}
                <div class="flex-grow-1">
                    <h4 class="mb-2">{{ $nombreEquipo }}</h4>

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped align-middle mb-0">
                            <thead class="table-dark">
                                <tr class="text-center">
                                    <th>PJ</th>
                                    <th>PG</th>
                                    <th>PE</th>
                                    <th>PP</th>
                                    <th>GF</th>
                                    <th>GC</th>
                                    <th>DG</th>
                                    <th>PTS</th>
                                    <th>Prom. Victorias</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr class="text-center fs-5">
                                    <td>{{ $pj }}</td>
                                    <td class="text-success fw-bold">{{ $pg }}</td>
                                    <td class="text-warning fw-semibold">{{ $pe }}</td>
                                    <td class="text-danger fw-semibold">{{ $pp }}</td>
                                    <td class="text-primary fw-semibold">{{ $gf }}</td>
                                    <td class="text-danger fw-semibold">{{ $gc }}</td>
                                    <td class="{{ $dg >= 0 ? 'text-success' : 'text-danger' }} fw-bold">
                                        {{ $dg > 0 ? '+' : '' }}{{ $dg }}
                                    </td>
                                    <td class="fw-bold">{{ $pts }}</td>
                                    <td>
                                        <span class="badge bg-dark">
                                            {{ number_format($promVictorias, 2) }}
                                        </span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    @if(!$equipo)
                        <div class="mt-2 text-muted">Sin equipo asignado o sin datos disponibles.</div>
                    @endif
                </div>
            </div>

        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    /* Evitar cualquier estilo tipo "card" heredado */
    .card, .card-body {
        box-shadow: none !important;
        border: none !important;
        background: transparent !important;
    }

    /* Escudo aumentado */
    .escudo-4x4 {
        width: 9rem;
        height: 9rem;
        object-fit: cover;
        border: 3px solid #000;
        border-radius: 0; /* si querés redondo: 50% */
        display: block;
    }

    /* Placeholder cuando no hay imagen */
    .placeholder-escudo {
        width: 9rem;
        height: 9rem;
        background: transparent;
        border-radius: 0;
        border: 3px solid #000;
    }

    /* Ajustes para que la tabla no tenga fondo-card */
    .table {
        background: transparent;
    }
    .table thead th {
        border-top: 0;
    }

    /* Responsive */
    @media (max-width: 576px) {
        .escudo-4x4, .placeholder-escudo {
            width: 4.8rem;
            height: 4.8rem;
        }
        .d-flex.align-items-center {
            flex-direction: column;
            align-items: flex-start;
            gap: 0.5rem;
        }
    }
</style>
@endpush