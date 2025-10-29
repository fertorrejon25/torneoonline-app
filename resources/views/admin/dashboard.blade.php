@extends('layouts.admin')

@section('title', 'Panel de Administración')

@section('content')
    {{-- Muestra el formulario para dar de alta jugadores --}}
    @if ($section === 'jugadores')
        @include('admin.altajugadores')

    {{-- Muestra el formulario para dar de alta equipos --}}
    @elseif ($section === 'equipos')
        @include('admin.altaequipo')

    {{-- Muestra el formulario para crear una nueva temporada --}}
    @elseif ($section === 'temporada')
        @include('admin.temporada_nueva')

    {{-- Muestra la vista para cargar resultados --}}
    @elseif ($section === 'resultados')
        @include('admin.resultados')

    {{-- Muestra la tabla de posiciones --}}
    @elseif ($section === 'ranking')
        <div class="container-fluid">
            <h1 class="mb-4">Tabla de Posiciones</h1>

            <!-- Selector de Temporada como botones -->
            <div class="row mb-4">
                <div class="col-12">
                    <h5>Selecciona la temporada para ver el ranking</h5>
                    @if(isset($temporadas) && count($temporadas) > 0)
                        <div class="d-grid gap-2 d-md-block">
                            @foreach ($temporadas as $temp)
                                <a href="{{ url('admin/dashboard?section=ranking&temporada=' . $temp->id) }}" 
                                   class="btn btn-outline-primary mb-2 {{ isset($temporadaActual) && $temporadaActual->id == $temp->id ? 'active' : '' }}">
                                    {{ $temp->NombreTemporada }}
                                </a>
                            @endforeach
                        </div>
                    @else
                        <p>No hay temporadas para mostrar el ranking.</p>
                    @endif
                </div>
            </div>

            <!-- Tabla de Posiciones -->
            @if(isset($temporadaActual))
                @if(count($tablaPosiciones) > 0)
                    <div class="card">
                        <div class="card-header bg-primary text-white">
                            <h4 class="mb-0">
                                Tabla de Posiciones - {{ $temporadaActual->NombreTemporada }}
                                <small class="float-end">{{ $tablaPosiciones->count() }} equipos</small>
                            </h4>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped table-hover">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>Pos</th>
                                            <th>Equipo</th>
                                            <th>PJ</th>
                                            <th>PG</th>
                                            <th>PE</th>
                                            <th>PP</th>
                                            <th>GF</th>
                                            <th>GC</th>
                                            <th>DG</th>
                                            <th>Pts</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($tablaPosiciones as $index => $equipo)
                                            <tr>
                                                <td><strong>{{ $index + 1 }}</strong></td>
                                                <td>
                                                    <strong>{{ $equipo->equipo->NombreEquipos }}</strong>
                                                </td>
                                                <td>{{ $equipo->partidos_jugados }}</td>
                                                <td>{{ $equipo->partidos_ganados }}</td>
                                                <td>{{ $equipo->partidos_empatados }}</td>
                                                <td>{{ $equipo->partidos_perdidos }}</td>
                                                <td>{{ $equipo->goles_favor }}</td>
                                                <td>{{ $equipo->goles_contra }}</td>
                                                <td>{{ $equipo->diferencia_goles }}</td>
                                                <td><strong class="text-primary">{{ $equipo->puntos }}</strong></td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="alert alert-info">
                        No hay equipos en la temporada "{{ $temporadaActual->NombreTemporada }}".
                    </div>
                @endif
            @else
                @if(isset($temporadas) && count($temporadas) > 0)
                    <div class="alert alert-info">
                        Selecciona una temporada para ver la tabla de posiciones.
                    </div>
                @else
                    <div class="alert alert-warning">
                        No hay temporadas creadas. Crea una temporada primero.
                    </div>
                @endif
            @endif
        </div>

    {{-- Muestra la lista de temporadas cargadas --}}
    @elseif ($section === 'temporadacargadas')
        <h4>Selecciona la temporada</h4>
        @if(isset($temporadas) && count($temporadas) > 0)
            <div class="d-grid gap-2">
                @foreach ($temporadas as $temp)
                    <a href="{{ route('admin.temporada.show', $temp->id) }}" class="btn btn-outline-primary">
                        {{ $temp->NombreTemporada }}
                    </a>
                @endforeach
            </div>
        @else
            <p>No hay temporadas registradas.</p>
        @endif

    {{-- Mensaje de bienvenida por defecto --}}
    @else
        <h4>Bienvenido al panel de administración</h4>
        <p>Seleccioná una opción del menú lateral.</p>
    @endif
@endsection