@extends('layouts.admin')

@section('title', 'Detalle de Temporada')

@section('content')
    <h3>Temporada: {{ $temporada->NombreTemporada }}</h3>

    {{-- Fechas de la temporada --}}
    @if($temporada->fechas->count())
        <div class="mt-3">
            <h5>Fechas</h5>
            <ul class="list-group">
                @foreach ($temporada->fechas as $fecha)
                    <li class="list-group-item">
                        <strong>{{ $fecha->nombre }}</strong> ({{ $fecha->dia }})
                        @if($fecha->partidos->count())
                            <ul class="mt-2">
                                @foreach ($fecha->partidos as $partido)
                                    <li>
                                        {{ $partido->equipo_local_id }} vs {{ $partido->equipo_visitante_id }}
                                        @if(!is_null($partido->goles_local))
                                            — Resultado: {{ $partido->goles_local }} - {{ $partido->goles_visitante }}
                                        @endif
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <small class="text-muted">Sin partidos cargados</small>
                        @endif
                    </li>
                @endforeach
            </ul>
        </div>
    @else
        <p class="mt-3 text-muted">No hay fechas registradas para esta temporada.</p>
    @endif
   
@endsection
