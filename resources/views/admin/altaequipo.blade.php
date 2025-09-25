<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Alta de Equipo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .foto_equipo {
            width: 150px;
            height: 150px;
            border: 1px solid #000;
            /* borde negro */
            display: flex;
            justify-content: center;
            align-items: center;
            cursor: pointer;
            background-color: #fff;
            /* fondo blanco */
            font-weight: bold;
            text-align: center;
            overflow: hidden;
        }

        .foto_equipo img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
    </style>
</head>

<body>
    <div class="container mt-5">

        <!-- Mensajes de éxito y errores -->
        <div id="alertas" class="mt-3"></div>
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
            <!-- Formulario -->
            <div class="col-md-9">
                <form id="formEquipo" enctype="multipart/form-data" method="POST" action="{{ route('equipos.store') }}">
                    @csrf

                    <div class="row g-4">
                        <!-- Foto -->
                        <div class="col-md-3">
                            <div class="foto_equipo" id="previewContainer"
                                onclick="document.getElementById('foto').click()">
                                <span id="placeholder">Foto del equipo</span>
                                <img id="previewImg" class="d-none" />
                            </div>
                            <input type="file" name="foto" id="foto" class="d-none" accept="image/*"
                                onchange="previewImage(event)">
                        </div>

                        <!-- Datos -->
                        <div class="col-md-9">
                            <div class="mb-4">
                                <input type="text" class="form-control" placeholder="Nombre del Equipo" name="nombre"
                                    required>
                            </div>
                            <div class="d-flex justify-content-end">
                                <button type="submit" class="btn btn-primary">Confirmar Alta</button>
                            </div>
                        </div>
                    </div>

                </form>
            </div>
        </div>
    </div>

    <!-- Script para previsualizar foto -->
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
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>