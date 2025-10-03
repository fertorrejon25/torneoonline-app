@extends('layouts.admin')

@section('title', 'Detalle de Temporada')

@section('content')
    <h3>Temporada: {{ $temporada->NombreTemporada }}</h3>

    {{-- FORMULARIO PARA CREAR UNA FECHA --}}
    <div class="card p-3 mb-3">
        <h5>Agregar nueva fecha</h5>
        <form action="{{ route('fechas.store') }}" method="POST">
            @csrf
            <input type="hidden" name="temporada_id" value="{{ $temporada->id }}">
            
            <div class="mb-2">
                <input type="text" name="nombre" class="form-control" placeholder="Ej: 1º Fecha" required>
            </div>
            <div class="mb-2">
                <input type="date" name="dia" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-success">Crear Fecha</button>
        </form>
    </div>

    {{-- LISTADO DE FECHAS --}}
    @if($temporada->fechas->count())
        @foreach ($temporada->fechas as $fecha)
            <div class="card p-3 mb-3">
                <h5>{{ $fecha->nombre }} ({{ $fecha->dia }})</h5>

                {{-- FORMULARIO PARA CREAR PARTIDO --}}
                <form action="{{ route('partidos.store') }}" method="POST" class="mb-3">
                    @csrf
                    <input type="hidden" name="fecha_id" value="{{ $fecha->id }}">

                    <div class="row">
                        <div class="col-md-5">
                            <select name="equipo_local" class="form-control" required>
                                <option value="">-- Local --</option>
                                @foreach($equipos as $equipo)
                                    <option value="{{ $equipo->id }}">{{ $equipo->NombreEquipos }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-2 text-center">
                            <strong>VS</strong>
                        </div>

                        <div class="col-md-5">
                            <select name="equipo_visitante" class="form-control" required>
                                <option value="">-- Visitante --</option>
                                @foreach($equipos as $equipo)
                                    <option value="{{ $equipo->id }}">{{ $equipo->NombreEquipos }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="mt-2 text-end">
                        <button type="submit" class="btn btn-primary btn-sm">Agregar Partido</button>
                    </div>
                </form>

                {{-- LISTADO DE PARTIDOS --}}
                @if($fecha->partidos->count())
                    <ul class="list-group">
                        @foreach ($fecha->partidos as $partido)
                            <li class="list-group-item">
                                {{ $partido->equipoLocal->NombreEquipos ?? '??' }}
                                vs
                                {{ $partido->equipoVisitante->NombreEquipos ?? '??' }}

                                @if(!is_null($partido->goles_local))
                                    — <strong>{{ $partido->goles_local }} : {{ $partido->goles_visitante }}</strong>
                                @endif
                            </li>
                        @endforeach
                    </ul>
                @else
                    <small class="text-muted">Sin partidos cargados</small>
                @endif
            </div>
        @endforeach
    @else
        <p class="mt-3 text-muted">No hay fechas registradas para esta temporada.</p>
    @endif

@endsection

