@extends('layouts.admin')

@section('title', 'Alta de Equipos')

@section('content')
    <form id="formtemporada" action="{{ route('temporada.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="row">
            <div class="col-md-6">
                <input type="text" class="form-control mb-2" placeholder="Nombre de la temporada" name="nombretemporada">
                <div class="d-flex justify-content-end">
                    <button type="submit" class="btn btn-primary">Crear temporada</button>
                </div>
            </div>
        </div>
    </form>
@endsection


