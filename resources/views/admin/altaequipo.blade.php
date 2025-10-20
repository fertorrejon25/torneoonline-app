@extends('layouts.app')

@section('title', 'Alta de Equipo')

@section('content')
    <!-- Mensajes de éxito y errores -->
    <div id="alertas"></div>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <div class="row">
        <!-- Datos + Foto (formulario completo) -->
        <div class="col-md-9">
            <form id="formEquipo" enctype="multipart/form-data" method="POST" action="{{ route('equipos.store') }}">
                @csrf
                <div class="row">
                    <!-- Foto -->
                    <div class="col-md-3">
                        <div class="foto-jugador" id="previewContainer" onclick="document.getElementById('foto').click()">
                            <span id="placeholder">Foto del equipo</span>
                            <img id="previewImg" class="d-none" />
                        </div>
                        <input type="file" name="foto" id="foto" class="d-none" accept="image/*" onchange="previewImage(event)">
                    </div>

                    <!-- Datos -->
                    <div class="col-md-9">
                        <div class="mb-2">
                            <input type="text" class="form-control" placeholder="Nombre del Equipo" name="nombre" required>
                        </div>

                        <div class="mt-3 d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary">Confirmar Alta</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('styles')
<style>
    /* Asegura que la caja de foto sea igual a la del jugador */
    .foto-jugador {
        width: 150px;
        height: 150px;
        border: 2px solid #000;
        display: flex;
        justify-content: center;
        align-items: center;
        cursor: pointer;
        background-color: #fff;
        font-weight: bold;
        text-align: center;
        overflow: hidden;
        border-radius: 8px;
    }

    .foto-jugador img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
</style>
@endpush

@push('scripts')
<script>
    function previewImage(event) {
        const input = event.target;
        const previewImg = document.getElementById('previewImg');
        const placeholder = document.getElementById('placeholder');

        if (input.files && input.files[0]) {
            const reader = new FileReader();

            reader.onload = function (e) {
                previewImg.src = e.target.result;
                previewImg.classList.remove('d-none');
                placeholder.style.display = 'none';
            };

            reader.readAsDataURL(input.files[0]);
        }
    }
</script>
@endpush
