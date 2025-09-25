<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Vista del Jugador</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .menu-lateral {
            background-color: #f8f9fa;
            min-height: 100vh;
            padding: 20px;
        }

        .logout-btn {
            background: transparent;
            border: 2px solid #dc3545;
            color: #dc3545;
            transition: background-color .2s, color .2s;
        }

        .logout-btn:hover {
            background-color: #dc3545;
            color: #fff;
        }

        .foto-cuadro {
            width: 150px;
            height: 150px;
            border: 2px solid #000;
            display: flex;
            justify-content: center;
            align-items: center;
            background-color: #fff;
            margin-bottom: 20px;
        }

        .contenedor-principal {
            padding: 30px;
        }
    </style>
</head>

<body class="fs-5">
    <div class="container-fluid">
        <div class="row">
            <!-- Menú lateral -->
            <div class="col-md-3 menu-lateral">
                <div class="d-grid gap-3">
                    <button class="btn btn-outline-dark btn-lg">Histórico del club</button>
                    <button class="btn btn-outline-dark btn-lg">Temporada actual</button>
                    <button class="btn btn-outline-dark btn-lg">Ranking histórico</button>

                    <!-- Aquí justo debajo, el Cerrar sesión -->
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="btn logout-btn btn-lg w-100">
                            Cerrar sesión
                        </button>
                    </form>
                </div>
            </div>

            <!-- Contenido principal -->
            <div class="col-md-9 contenedor-principal">
                <div class="mb-4">
                    <input type="text" class="form-control form-control-lg fs-5"
                        value="{{ $equipo['nombre'] ?? 'Sin equipo' }}" readonly>
                </div>
                <div class="row align-items-center mb-4">
                    <div class="col-md-2 text-center">
                        <div class="col-md-2 text-center">
                            @if($jugador['foto_jugador'])
                                @php
                                    // Asegurar que la ruta use el formato correcto
                                    $rutaFoto = 'storage/' . str_replace('public/', '', $jugador['foto_jugador']);
                                @endphp
                                <img src="{{ asset($rutaFoto) }}" alt="Foto de {{ $jugador['nombre'] }}" width="150"
                                    style="border-radius: 10px; border: 3px solid #3498db;"
                                    onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">

                            @else
                                <div
                                    style="width: 150px; height: 150px; background: #f0f0f0; border-radius: 10px; display: flex; align-items: center; justify-content: center;">
                                    <i class="fas fa-user" style="font-size: 50px; color: #ccc;"></i>
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="col-md-10">
                        <div class="table-responsive">
                            <table class="table table-bordered text-center align-middle fs-5">
                                <thead class="table-light fs-5">
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
                                        <td>{{ $jugador['dni'] }}</td>
                                        <td>{{ $jugador['nombre'] }}</td>
                                        <td>{{ $jugador['pj'] }}</td>
                                        <td>{{ $jugador['goles'] }}</td>
                                        <td>{{ $jugador['asistencias'] }}</td>
                                        <td>{{ number_format($mediaGoleadora, 2) }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <!-- /Contenido principal -->
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>