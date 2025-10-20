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
        /* Estilos personalizados */
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            min-height: 100vh;
        }
        
        /* Fondo del campo de fútbol */
        .football-field {
            background: url('/imagen/estadio.png') no-repeat center center;
            background-size: cover;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            overflow: hidden;
            z-index: 1;
        }
        
               
        
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-15px); }
        }
        
        @keyframes rotate {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
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
        
        /* Card de login mejorado */
        .login-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 3rem;
            box-shadow: 
                0 20px 40px rgba(0,0,0,0.1),
                0 0 0 1px rgba(255,255,255,0.2);
            border: 1px solid rgba(255,255,255,0.3);
            max-width: 450px;
            width: 100%;
            position: relative;
            overflow: hidden;
        }
        
        .login-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 5px;
            background: linear-gradient(90deg, #4CAF50, #45a049, #4CAF50);
            border-radius: 20px 20px 0 0;
        }
        
        /* Título */
        .login-title {
            text-align: center;
            margin-bottom: 2rem;
            color: #2d5f3f;
            font-weight: bold;
            font-size: 2rem;
        }
        
        .login-subtitle {
            text-align: center;
            margin-bottom: 2rem;
            color: #4a8f5f;
            font-size: 1.1rem;
        }
        
        /* Form controls mejorados */
        .form-label {
            font-weight: 600;
            color: #2d5f3f;
            margin-bottom: 0.5rem;
        }
        
        .form-control {
            border: 2px solid #e9ecef;
            border-radius: 10px;
            padding: 12px 16px;
            font-size: 1rem;
            transition: all 0.3s ease;
            background: rgba(255, 255, 255, 0.9);
        }
        
        .form-control:focus {
            border-color: #4CAF50;
            box-shadow: 0 0 0 0.2rem rgba(76, 175, 80, 0.25);
            background: rgba(255, 255, 255, 1);
            transform: translateY(-2px);
        }
        
        .form-control.is-invalid {
            border-color: #dc3545;
        }
        
        .form-control.is-invalid:focus {
            border-color: #dc3545;
            box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);
        }
        
        /* Checkbox personalizado */
        .form-check-input {
            width: 1.2rem;
            height: 1.2rem;
            margin-top: 0.1rem;
            border: 2px solid #4CAF50;
        }
        
        .form-check-input:checked {
            background-color: #4CAF50;
            border-color: #4CAF50;
        }
        
        .form-check-label {
            color: #2d5f3f;
            font-weight: 500;
            margin-left: 0.5rem;
        }
        
        /* Botones mejorados */
        .btn-primary {
            background: linear-gradient(45deg, #4CAF50, #45a049);
            border: none;
            border-radius: 10px;
            padding: 12px 24px;
            font-weight: 600;
            font-size: 1rem;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(76, 175, 80, 0.3);
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(76, 175, 80, 0.4);
            background: linear-gradient(45deg, #45a049, #3d8b40);
        }
        
        .btn-primary:active {
            transform: translateY(0px);
        }
        
        /* Enlaces */
        .text-decoration-none {
            color: #4a8f5f !important;
            font-weight: 500;
            transition: color 0.3s ease;
        }
        
        .text-decoration-none:hover {
            color: #2d5f3f !important;
            text-decoration: underline !important;
        }
        
        /* Alertas mejoradas */
        .alert {
            border-radius: 10px;
            border: none;
            padding: 1rem 1.5rem;
            margin-bottom: 1.5rem;
        }
        
        .alert-success {
            background: linear-gradient(45deg, rgba(76, 175, 80, 0.1), rgba(76, 175, 80, 0.05));
            color: #2d5f3f;
            border-left: 4px solid #4CAF50;
        }
        
        .invalid-feedback {
            font-size: 0.9rem;
            margin-top: 0.5rem;
            font-weight: 500;
        }
        
        

        @keyframes particle-float {
            0%, 100% { transform: translateY(0px) rotate(0deg) scale(1); opacity: 0.3; }
            33% { transform: translateY(-20px) rotate(120deg) scale(1.1); opacity: 0.6; }
            66% { transform: translateY(-10px) rotate(240deg) scale(0.9); opacity: 0.4; }
        }
        
        /* Icono de fútbol para el título */
        .football-icon {
            display: inline-block;
            margin-right: 10px;
            font-size: 2rem;
            color: #4CAF50;
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
            
            .football {
                width: 60px;
                height: 60px;
                top: 10%;
                right: 5%;
            }
            
            .football-small {
                width: 30px;
                height: 30px;
                bottom: 15%;
                left: 10%;
            }
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
        
        .login-card {
            animation: slideInUp 0.8s ease-out;
        }
    </style>
</head>

<body>
    <!-- Fondo del campo de fútbol -->
    <div class="football-field">
        <div class="football"></div>
        <div class="football-small"></div>
        
        
    </div>

    <!-- Contenedor principal del login -->
    <div class="login-container">
        <div class="login-card">
            <!-- Título -->
            <div class="text-center mb-4">
                <h2 class="login-title">
                    Iniciar Sesión
                </h2>
            </div>

            {{-- Mensaje de estado --}}
            @if (session('status'))
                <div class="alert alert-success">
                    <strong>✅</strong> {{ session('status') }}
                </div>
            @endif

            {{-- Formulario --}}
            <form method="POST" action="{{ route('login') }}">
                @csrf

                <!-- DNI -->
                <div class="mb-3">
                    <label for="dni" class="form-label">
                        <i class="me-2"></i>DNI
                    </label>
                    <input type="text" class="form-control @error('dni') is-invalid @enderror" id="dni" name="dni"
                        value="{{ old('dni') }}" required autofocus autocomplete="username" 
                        placeholder="Ingresa tu DNI">
                    @error('dni')
                        <div class="invalid-feedback">
                            <strong></strong> {{ $message }}
                        </div>
                    @enderror
                </div>

                <!-- Password -->
                <div class="mb-3">
                    <label for="password" class="form-label">
                        <i class="me-2"></i>Contraseña
                    </label>
                    <input type="password" class="form-control @error('password') is-invalid @enderror"
                        id="password" name="password" required autocomplete="current-password"
                        placeholder="Ingresa tu contraseña">
                    @error('password')
                        <div class="invalid-feedback">
                            <strong></strong> {{ $message }}
                        </div>
                    @enderror
                </div>

                <!-- Remember Me -->
                <div class="mb-4 form-check">
                    <input type="checkbox" class="form-check-input" id="remember_me" name="remember" 
                           {{ old('remember') ? 'checked' : '' }}>
                    <label class="form-check-label" for="remember_me">
                        Recordarme en este dispositivo
                    </label>
                </div>

                <!-- Botones -->
                <div class="d-flex flex-column gap-3">
                    <button type="submit" class="btn btn-primary w-100">
                        <strong> Iniciar Sesión</strong>
                    </button>
                    
                    @if (Route::has('password.request'))
                        <div class="text-center">
                            <a href="{{ route('password.request') }}" class="text-decoration-none">
                                ¿Olvidaste tu contraseña?
                            </a>
                        </div>
                    @endif
                </div>
            </form>

            <!-- Información adicional -->
            <div class="text-center mt-4 pt-3" style="border-top: 1px solid #e9ecef;">
                <small class="text-muted">
                    Plataforma oficial del Torneo IFTS12<br>
                    <strong style="color: #4CAF50;">BLUEBYTE SOLUCIONES</strong>
                </small>
            </div>
        </div>
    </div>

    <!-- Scripts de Bootstrap -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL"
        crossorigin="anonymous"></script>
    
    <script>
        // Efecto de enfoque mejorado en los inputs
        document.querySelectorAll('.form-control').forEach(input => {
            input.addEventListener('focus', function() {
                this.parentElement.style.transform = 'scale(1.02)';
            });
            
            input.addEventListener('blur', function() {
                this.parentElement.style.transform = 'scale(1)';
            });
        });
        
        // Animación adicional para el botón de envío
        document.querySelector('form').addEventListener('submit', function(e) {
            const btn = this.querySelector('.btn-primary');
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Iniciando sesión...';
            btn.disabled = true;
        });
        
        // Validación en tiempo real
        document.getElementById('dni').addEventListener('input', function() {
            const value = this.value.replace(/\D/g, ''); // Solo números
            this.value = value;
            
            if (value.length > 0 && value.length < 7) {
                this.setCustomValidity('El DNI debe tener al menos 7 dígitos');
            } else {
                this.setCustomValidity('');
            }
        });
    </script>
</body>

</html>