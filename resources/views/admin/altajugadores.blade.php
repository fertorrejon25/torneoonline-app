@extends('layouts.app')

@section('title', 'Alta de Jugador')

@section('content')
    <!-- Mensajes de éxito y errores -->
    <div id="alertas"></div>
    
    <!-- Errores de validación -->
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
            <form id="formJugador" enctype="multipart/form-data" method="POST" action="{{ route('jugadores.store') }}">
                @csrf
                <div class="row">
                    <!-- Foto -->
                    <div class="col-md-3">
                        <div class="foto-jugador" id="previewContainer" onclick="document.getElementById('foto').click()">
                            <span id="placeholder">Foto del jugador</span>
                            <img id="previewImg" class="d-none" />
                        </div>
                        <input type="file" name="foto" id="foto" class="d-none" accept="image/*" onchange="previewImage(event)">
                    </div>
                    
                    <!-- Datos -->
                    <div class="col-md-9">
                        <div class="mb-2">
                            <select class="form-control" name="equipo_id" required>
                                <option value="">-- Seleccionar Equipo --</option>
                                @foreach($equipos as $equipo)
                                    <option value="{{ $equipo->id }}">{{ $equipo->NombreEquipos }}</option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="mb-2">
                            <input type="text" class="form-control" placeholder="Nombre del Jugador" name="nombre" required>
                        </div>
                        
                        <div class="mb-2">
                            <input type="text" class="form-control" placeholder="DNI" name="dni" pattern="\d{8}" maxlength="8" inputmode="numeric" title="El DNI debe tener exactamente 8 números" required>
                        </div>
                        
                        <div class="mb-2">
                            <input type="email" class="form-control" placeholder="Mail del Jugador" name="mail" required>
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