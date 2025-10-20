@extends('layouts.admin')

@section('title', 'Panel de Administración')

@section('content')
    @if ($section === 'jugadores')
        @include('admin.altajugadores')
    @elseif ($section === 'equipos')
        @include('admin.altaequipo')
    @elseif ($section === 'temporada')
        @include('admin.temporada_nueva')
    @elseif ($section === 'resultados')
        @include('admin.resultados')
    @elseif ($section === 'ranking')
        @include('admin.ranking')
    @elseif ($section === 'temporadacargadas')
        <h4>Selecciona la temporada</h4>
        @if(count($temporadas) > 0)
            <div class="d-grid gap-2">
                @foreach ($temporadas as $temp)
                    <a href="{{ route('admin.temporada.show', $temp->id) }}"
                    class="btn btn-outline-primary">
                    {{ $temp->NombreTemporada }}
                    </a>
                @endforeach
            </div>
        @else
            <p>No hay temporadas registradas.</p>
        @endif
    @else
        <h4>Bienvenido al panel de administración</h4>
        <p>Seleccioná una opción del menú lateral.</p>
    @endif
@endsection

