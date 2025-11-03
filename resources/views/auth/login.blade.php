<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Login - Torneo de Fútbol</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <style>
        /* Estilos base */
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            min-height: 100vh;
            background: #f0f2f5;
        }

        /* Fondo del campo de fútbol */
        .football-field {
            background: url('/imagen/fondologin01.png') no-repeat center center;
            background-size: cover;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 1;
            filter: brightness(0.7);
        }

        /* Contenedor principal */
        .login-container {
            position: relative;
            z-index: 3;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
        }

        /* Card de login con glassmorphism */
        .login-card {
            background: rgba(255, 255, 255, 0.15); /* Más transparente */
            backdrop-filter: blur(15px); /* Mayor efecto blur */
            border-radius: 20px;
            padding: 3rem;
            box-shadow: 
                0 20px 40px rgba(0,0,0,0.2),
                0 0 0 1px rgba(255,255,255,0.1);
            border: 1px solid rgba(255,255,255,0.2);
            max-width: 450px;
            width: 100%;
            position: relative;
            overflow: hidden;
        }

       

        /* Título */
        .login-title {
            text-align: center;
            margin-bottom: 1.5rem;
            color: #52f113ff; /* Color moderno y llamativo */
            font-weight: bold;
            font-size: 2rem;
            text-shadow: 0 2px 4px rgba(0,0,0,0.5);
        }

        .login-subtitle {
            text-align: center;
            margin-bottom: 2rem;
            color: #ffffffcc; /* Blanco semitransparente para subtítulos */
            font-size: 1.1rem;
        }

        /* Inputs modernos */
        .form-control {
            border: none;
            border-radius: 10px;
            padding: 12px 16px;
            font-size: 1rem;
            background: rgba(255, 255, 255, 0.25);
            color: #fff;
            transition: all 0.3s ease;
            box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .form-control::placeholder {
            color: rgba(255, 255, 255, 0.7);
        }

        .form-control:focus {
            background: rgba(255, 255, 255, 0.35);
            outline: none;
            box-shadow: 0 0 0 2px rgba(76, 175, 80, 0.3);
            transform: translateY(-2px);
        }

        /* Checkbox */
        .form-check-input {
            width: 1.2rem;
            height: 1.2rem;
            border: 2px solid #4CAF50;
            background: rgba(255, 255, 255, 0.2);
        }

        .form-check-input:checked {
            background-color: #4CAF50;
            border-color: #4CAF50;
        }

        .form-check-label {
            color: #fff;
            font-weight: 500;
            margin-left: 0.5rem;
        }
        .form-label {
            font-weight: 600;
            color: #52f113ff; /* Más visible y moderno */
            margin-bottom: 0.5rem;
        }

        /* Botón */
        .btn-primary {
            background: linear-gradient(135deg, #34cad4ff, #1f3de7ff); /* Colores modernos */
            border: none;
            border-radius: 12px;
            padding: 12px 24px;
            font-weight: 600;
            font-size: 1rem;
            transition: all 0.3s ease;
            box-shadow: 0 5px 15px rgba(12, 185, 238, 0.4);
            color: #fff;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(240,101,149,0.5);
            background: linear-gradient(135deg, #f06595, #ff6b6b);
        }

        .btn-primary:active {
            transform: translateY(0px);
        }

        /* Enlaces */
        .text-decoration-none {
            color: #a0f0a0 !important;
            font-weight: 500;
            transition: color 0.3s ease;
        }

        .text-decoration-none:hover {
            color: #fff !important;
            text-decoration: underline !important;
        }

        /* Animación de entrada */
        @keyframes slideInUp {
            from {
                transform: translateY(50px);
                opacity: 0;
            }

            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        /* Responsive */
        @media (max-width: 768px) {
            .login-card {
                padding: 2rem;
                margin: 1rem;
            }

            .login-title {
                font-size: 1.5rem;
            }
        }
    </style>
</head>

<body>
    <!-- Fondo -->
    <div class="football-field"></div>

    <!-- Login -->
    <div class="login-container">
        <div class="login-card">
            <div class="text-center mb-4">
                <h2 class="login-title">Iniciar Sesión</h2>
            </div>

            @if (session('status'))
                <div class="alert alert-success">
                    <strong>✅</strong> {{ session('status') }}
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}">
                @csrf

                <div class="mb-3">
                    <label for="dni" class="form-label">DNI</label>
                    <input type="text" class="form-control @error('dni') is-invalid @enderror" id="dni"
                        name="dni" value="{{ old('dni') }}" required autofocus placeholder="Ingresa tu DNI">
                    @error('dni')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label">Contraseña</label>
                    <input type="password" class="form-control @error('password') is-invalid @enderror"
                        id="password" name="password" required placeholder="Ingresa tu contraseña">
                    @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-4 form-check">
                    <input type="checkbox" class="form-check-input" id="remember_me" name="remember"
                        {{ old('remember') ? 'checked' : '' }}>
                    <label class="form-check-label" for="remember_me">
                        Recordarme en este dispositivo
                    </label>
                </div>

                <div class="d-flex flex-column gap-3">
                    <button type="submit" class="btn btn-primary w-100"><strong>Iniciar Sesión</strong></button>
                    @if (Route::has('password.request'))
                        <div class="text-center">
                            <a href="{{ route('password.request') }}" class="text-decoration-none">
                                ¿Olvidaste tu contraseña?
                            </a>
                        </div>
                    @endif
                </div>
            </form>

            <div class="text-center mt-4 pt-3" style="border-top: 1px solid rgba(255,255,255,0.2);">
                <small class="text-white-50">
                    Plataforma oficial del Torneo IFTS12<br>
                    <strong style="color: #52f113ff;">BLUEBYTE SOLUCIONES</strong>
                </small>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL"
        crossorigin="anonymous"></script>
</body>

</html>
