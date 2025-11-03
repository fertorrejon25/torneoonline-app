@extends('layouts.jugador')

@section('title', 'Mi Carnet')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-xl-10 col-lg-11">

            @php
                // Soporta objeto/array para equipo y jugador
                $nombreEquipo = is_object($equipo)
                    ? ($equipo->NombreEquipos ?? ($equipo->nombre ?? 'Sin equipo'))
                    : ($equipo['NombreEquipos'] ?? ($equipo['nombre'] ?? 'Sin equipo'));

                $dniJugador = is_array($jugador) ? ($jugador['dni'] ?? 'N/A') : 'N/A';
                $nombreJugador = is_array($jugador) ? ($jugador['nombre'] ?? 'N/A') : 'N/A';

                $pj  = is_array($jugador) ? ($jugador['pj'] ?? 0) : 0;
                $g   = is_array($jugador) ? ($jugador['goles'] ?? 0) : 0;
                $a   = is_array($jugador) ? ($jugador['asistencias'] ?? 0) : 0;
                $mg  = isset($mediaGoleadora) ? number_format($mediaGoleadora, 2) : '0.00';

                // Foto del jugador tolerante a "public/..." -> "storage/..."
                $foto = is_array($jugador) ? ($jugador['foto_jugador'] ?? null) : null;
                if (!empty($foto)) {
                    $foto = 'storage/' . str_replace('public/', '', $foto);
                }
            @endphp

            {{-- Contenedor horizontal: foto a la izquierda + contenido (nombre equipo + tabla) --}}
            <div class="d-flex align-items-center gap-3 py-3" style="padding-left: 0; margin-left: -60px;">
                {{-- Foto grande del jugador con borde negro --}}
                <div class="flex-shrink-0">
                    @if(!empty($foto))
                        <img src="{{ asset($foto) }}"
                             alt="Foto {{ $nombreJugador }}"
                             class="foto-4x4"
                             onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                        <div class="placeholder-foto foto-4x4 d-none align-items-center justify-content-center">
                            <i class="fas fa-user text-muted"></i>
                        </div>
                    @else
                        <div class="placeholder-foto foto-4x4 d-flex align-items-center justify-content-center">
                            <i class="fas fa-user text-muted"></i>
                        </div>
                    @endif
                </div>

                {{-- Nombre del equipo y tabla de stats (sin card) --}}
                <div class="flex-grow-1">
                    <h4 class="mb-3">Jugador de {{ $nombreEquipo }}</h4>

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped align-middle mb-0">
                            <thead class="table-dark">
                                <tr class="text-center">
                                    <th>DNI</th>
                                    <th>JUGADOR</th>
                                    <th>PJ</th>
                                    <th>GOLES</th>
                                    <th>ASISTENCIAS</th>
                                    <th>PROM. GOLES</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr class="text-center fs-5">
                                    <td>{{ $dniJugador }}</td>
                                    <td>{{ $nombreJugador }}</td>
                                    <td>{{ $pj }}</td>
                                    <td>{{ $g }}</td>
                                    <td>{{ $a }}</td>
                                    <td>
                                        <span class="badge bg-dark">{{ $mg }}</span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>

        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    /* Quitar estilo de card heredado si lo hubiera */
    .card, .card-body {
        box-shadow: none !important;
        border: none !important;
        background: transparent !important;
    }

    /* Foto grande del jugador con borde negro */
    .foto-4x4 {
        width: 9rem;
        height: 9rem;
        object-fit: cover;
        border: 3px solid #000;
        border-radius: 0;
        display: block;
    }

    /* Placeholder cuando no hay foto */
    .placeholder-foto {
        width: 9rem;
        height: 9rem;
        background: transparent;
        border-radius: 0;
        border: 3px solid #000;
    }

    /* Tabla sin fondo-card y con mismo estilo que histórico */
    .table {
        background: transparent;
    }
    .table thead th {
        border-top: 0;
    }

    /* Responsive: foto más chica y apilar en móviles */
    @media (max-width: 576px) {
        .foto-4x4, .placeholder-foto {
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