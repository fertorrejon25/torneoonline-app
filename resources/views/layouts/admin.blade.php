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
        /* Reset completo */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial;
            overflow-x: hidden;
            background-color: #e2e2e2;
        }

        /* Layout principal */
        .main-container {
            display: flex;
            min-height: 100vh;
        }

        /* Menú lateral - Fijo y estable */
        .menu-lateral {
            background-color: #f8f9fa;
            padding: 20px 15px;
            width: 280px;
            min-height: 100vh;
            height: 100vh;
            position: fixed;
            left: 0;
            top: 0;
            overflow-y: auto;
            border-right: 2px solid #dee2e6;
            z-index: 1000;
        }

        .menu-lateral .d-grid {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .menu-lateral .btn {
            text-align: left;
            padding: 12px 15px;
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.3s ease;
            border: 1px solid #ced4da;font-weight: bold; /* Añade esta línea */

        }

        .menu-lateral .btn:hover {
            background-color: #e9ecef;
            transform: translateX(5px);
        }

        .menu-lateral .btn-active {
            background-color: #198754;
            color: white;
            border-color: #198754;
            font-weight: bold; 

        }

        .menu-lateral .btn-logout {
            margin-top: 20px;
            background-color: #dc3545;
            color: white;
            border: none;
            font-weight: bold; /* Añade esta línea */

        }

        .menu-lateral .btn-logout:hover {
            background-color: #bb2d3b;
            transform: translateX(5px);
        }

        /* Contenido principal */
        .contenedor-principal {
            flex: 1;
            background-color: #e2e2e2;
            padding: 30px;
            min-height: 100vh;
            margin-left: 280px; /* Mismo ancho que el menú */
            width: calc(100% - 280px);
        }

        /* Estilos para el contenido específico */
        .content-wrapper {
            background: white;
            border-radius: 10px;
            padding: 25px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            min-height: calc(100vh - 60px);
        }

        /* Estilos para las fotos de jugadores (manteniendo los tuyos) */
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
            border-radius: 8px;
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

        /* Responsive */
        @media (max-width: 768px) {
            .menu-lateral {
                width: 100%;
                height: auto;
                position: relative;
                min-height: auto;
            }
            
            .contenedor-principal {
                margin-left: 0;
                width: 100%;
                padding: 15px;
            }
            
            .main-container {
                flex-direction: column;
            }
        }

        /* Scrollbar personalizado para el menú */
        .menu-lateral::-webkit-scrollbar {
            width: 6px;
        }

        .menu-lateral::-webkit-scrollbar-track {
            background: #f1f1f1;
        }

        .menu-lateral::-webkit-scrollbar-thumb {
            background: #c1c1c1;
            border-radius: 3px;
        }

        .menu-lateral::-webkit-scrollbar-thumb:hover {
            background: #a8a8a8;
        }
    </style>
</head>

<body>
    <div class="main-container">
        <!-- Menú lateral fijo -->
        <nav class="menu-lateral">
            <div class="d-grid">
                <a href="{{ route('admin.dashboard', ['section' => 'temporadacargadas']) }}" 
                   class="btn btn-outline-dark {{ request('section') == 'temporadacargadas' ? 'btn-active' : '' }}">
                   📊 Administrar Temporada
                </a>
                <a href="{{ route('admin.dashboard', ['section' => 'ranking']) }}" 
                   class="btn btn-outline-dark {{ request('section') == 'ranking' ? 'btn-active' : '' }}">
                   🏆 Rankings
                </a>
                <a href="{{ route('admin.dashboard', ['section' => 'temporada']) }}" 
                   class="btn btn-outline-dark {{ request('section') == 'temporada' ? 'btn-active' : '' }}">
                   🆕 Crear Nueva Temporada
                </a>
                <a href="{{ route('admin.dashboard', ['section' => 'equipos']) }}" 
                   class="btn btn-outline-dark {{ request('section') == 'equipos' ? 'btn-active' : '' }}">
                   ⚽ Alta de Equipos
                </a>
                <a href="{{ route('admin.dashboard', ['section' => 'jugadores']) }}" 
                   class="btn btn-outline-dark {{ request('section') == 'jugadores' ? 'btn-active' : '' }}">
                   👤 Alta Jugadores
                </a>
                <a href="{{ route('admin.dashboard', ['section' => 'resultados']) }}" 
                   class="btn btn-outline-dark {{ request('section') == 'resultados' ? 'btn-active' : '' }}">
                   📝 Resultados
                </a>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="btn btn-logout">🚪 Cerrar sesión</button>
                </form>
            </div>
        </nav>

        <!-- Contenido dinámico -->
        <main class="contenedor-principal">
            <div class="content-wrapper">
                @yield('content')
            </div>
        </main>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL"
        crossorigin="anonymous"></script>

    @stack('scripts')
</body>
</html>
</style>