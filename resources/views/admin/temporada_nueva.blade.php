@extends('layouts.admin')

@section('title', 'Alta de Temporadas')

@section('content')
    {{-- FORMULARIO DE CREACIÓN --}}
    <form id="formtemporada" action="{{ route('temporada.store') }}" method="POST">
        @csrf
        <div class="row">
            <div class="col-md-6">
                <input type="text"
                       class="form-control mb-2"
                       placeholder="Nombre de la temporada"
                       name="nombretemporada"
                       required>
                <div class="d-flex justify-content-end">
                    <button type="submit" class="btn btn-primary">Crear temporada</button>
                </div>
            </div>
        </div>
    </form>

    {{-- MENSAJE DE ÉXITO --}}
    @if (session('success'))
        <div class="alert alert-success mt-3">
            {{ session('success') }}
        </div>
    @endif

    {{-- LISTADO DE TEMPORADAS EXISTENTES --}}
    @if (!empty($temporadas) && count($temporadas) > 0)
        <h4 class="mt-4">Temporadas cargadas</h4>
        <div class="d-flex flex-wrap gap-2 mt-2">
            @foreach ($temporadas as $temporada)
                <a href="{{ route('temporada.show', $temporada->id) }}"
                   class="btn btn-outline-primary">
                    {{ $temporada->NombreTemporada }}
                </a>
            @endforeach
        </div>
    @else
    @endif
@endsection



