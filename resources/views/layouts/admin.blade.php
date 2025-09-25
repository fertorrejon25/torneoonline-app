<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>@yield('title', 'Panel Admin')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <style>
        .menu-lateral {
            background-color: #f8f9fa;
            padding: 15px;
            min-height: 100vh;
        }

        .menu-lateral .btn-active {
            background-color: green;
            color: white;
        }

        .contenedor-principal {
            background-color: #e2e2e2;
            padding: 20px;
            border: 1px solid #ccc;
            min-height: 100vh;
        }

        .foto-jugador {
            background-color: white;
            width: 150px;
            height: 150px;
            display: flex;
            justify-content: center;
            align-items: center;
            border: 1px solid #000;
            margin-bottom: 10px;
            overflow: hidden;
            position: relative;
            cursor: pointer;
            text-align: center;
        }

        .foto-jugador span {
            position: absolute;
            color: black;
            font-weight: bold;
            z-index: 1;
            pointer-events: none;
        }

        .foto-jugador img {
            max-width: 100%;
            max-height: 100%;
            z-index: 2;
            object-fit: contain;
        }
    </style>
</head>

<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Menú lateral -->
            <div class="col-md-3 menu-lateral">
                <div class="d-grid gap-2">
                    
                    <a href="{{ route('admin.dashboard', ['section' => 'temporadacargadas']) }}" class="btn btn-outline-dark">Administrar Temporada</a>
                    <a href="{{ route('admin.dashboard', ['section' => 'ranking']) }}" class="btn btn-outline-dark">Rankings</a>
                    <a href="{{ route('admin.dashboard', ['section' => 'temporada']) }}" class="btn btn-outline-dark">Crear Nueva Temporada</a>
                    <a href="{{ route('admin.dashboard', ['section' => 'equipos']) }}" class="btn btn-outline-dark">Alta de Equipos</a>
                    <a href="{{ route('admin.dashboard', ['section' => 'jugadores']) }}" class="btn btn-outline-dark">Alta Jugadores</a>
                    <a href="{{ route('admin.dashboard', ['section' => 'resultados']) }}" class="btn btn-outline-dark">Resultados</a>

                    

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="btn btn-outline-danger w-100 mt-2">Cerrar sesión</button>
                    </form>
                </div>
            </div>

            <!-- Contenido dinámico -->
            <div class="col-md-9 contenedor-principal">
                @yield('content')
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL"
        crossorigin="anonymous"></script>

    @stack('scripts')
</body>
</html>
