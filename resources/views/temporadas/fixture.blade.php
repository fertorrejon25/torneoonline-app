@extends('layouts.app') {{-- o tu layout admin --}}

@section('content')
<div class="container">
    <h2>Fixture - {{ $temporada->NombreTemporada }}</h2>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if($partidos->isEmpty())
        <form action="{{ route('fixture.generar',$temporada->id) }}" method="POST">
            @csrf
            <button class="btn btn-primary">Generar Fixture</button>
        </form>
    @else
        <form action="{{ route('fixture.updateFechas',$temporada->id) }}" method="POST">
            @csrf
            <table class="table table-bordered mt-3">
                <thead>
                    <tr>
                        <th>Local</th>
                        <th>Visitante</th>
                        <th>Fecha</th>
                        <th>Hora</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($partidos->flatten() as $p)
                    <tr>
                        <td>{{ $p->local->NombreEquipos }}</td>
                        <td>{{ $p->visitante->NombreEquipos }}</td>
                        <td>
                            <input type="date" name="fechas[{{ $p->id }}]" value="{{ $p->fecha }}">
                        </td>
                        <td>
                            <input type="time" name="horas[{{ $p->id }}]" value="{{ $p->hora }}">
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            <button class="btn btn-success">Guardar Fechas y Horarios</button>
        </form>
    @endif
</div>
@endsection
