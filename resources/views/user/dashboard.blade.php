@extends('layouts.jugador')

@section('title', 'Panel del Jugador')

@section('content')
<div class="mb-4">
    <input type="text" class="form-control form-control-lg fs-5"
        value="{{ $equipo['nombre'] ?? 'Sin equipo' }}" readonly>
</div>

<div class="row align-items-center mb-4">
    <div class="col-md-2 text-center">
        @if(isset($jugador['foto_jugador']) && $jugador['foto_jugador'])
            @php
                $rutaFoto = 'storage/' . str_replace('public/', '', $jugador['foto_jugador']);
            @endphp
            <img src="{{ asset($rutaFoto) }}" alt="Foto de {{ $jugador['nombre'] }}" 
                style="width: 150px; height: 150px; border-radius: 10px; border: 3px solid #198754; object-fit: cover;"
                onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
            <div style="width: 150px; height: 150px; background: #f0f0f0; border-radius: 10px; display: none; align-items: center; justify-content: center; margin: 0 auto;">
                <i class="fas fa-user" style="font-size: 50px; color: #ccc;"></i>
            </div>
        @else
            <div style="width: 150px; height: 150px; background: #f0f0f0; border-radius: 10px; display: flex; align-items: center; justify-content: center; margin: 0 auto;">
                <i class="fas fa-user" style="font-size: 50px; color: #ccc;"></i>
            </div>
        @endif
    </div>
    
    <div class="col-md-10">
        <div class="table-responsive">
            <table class="table table-bordered text-center align-middle fs-5">
                <thead class="table-light">
                    <tr>
                        <th>N DNI</th>
                        <th>Jugador</th>
                        <th>PJ</th>
                        <th>G</th>
                        <th>A</th>
                        <th>Media Goleadora</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>{{ $jugador['dni'] ?? 'N/A' }}</td>
                        <td>{{ $jugador['nombre'] ?? 'N/A' }}</td>
                        <td>{{ $jugador['pj'] ?? '0' }}</td>
                        <td>{{ $jugador['goles'] ?? '0' }}</td>
                        <td>{{ $jugador['asistencias'] ?? '0' }}</td>
                        <td>{{ isset($mediaGoleadora) ? number_format($mediaGoleadora, 2) : '0.00' }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .team-name {
        font-size: 1.5rem;
        font-weight: bold;
        color: #2c3e50;
        background-color: #ecf0f1;
        border: 2px solid #bdc3c7;
        border-radius: 8px;
        text-align: center;
        padding: 10px;
    }
    
    .stats-table {
        font-size: 1.1rem;
    }
    
    .stats-table th {
        background-color: #343a40;
        color: white;
        text-align: center;
        padding: 12px;
    }
    
    .stats-table td {
        text-align: center;
        padding: 12px;
        vertical-align: middle;
    }
</style>
@endpush