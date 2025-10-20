<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
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
                    <button class="btn btn-outline-dark">Crear Nueva Temporada</button>
                    <button class="btn btn-outline-dark">Administrar Temporada</button>
                    <button class="btn btn-outline-dark">Rankings</button>
                    <button class="btn btn-active">Alta de Equipos</button>
                    <button class="btn btn-outline-dark">Alta Jugadores</button>
                </div>
            </div>

            <!-- Formulario principal -->
            <div class="col-md-9 contenedor-principal">
                <form id="formequipo" enctype="multipart/form-data" method="POST">
                    <div class="row">
                        <!-- Foto -->
                        <div class="col-md-3">
                            <div class="foto-jugador" id="previewContainer"
                                onclick="document.getElementById('foto').click()">
                                <span id="placeholder">Foto del Equipo</span>
                                <img id="previewImg" class="d-none" />
                            </div>
                            <input type="file" name="foto" id="foto" class="d-none" accept="image/*"
                                onchange="previewImage(event)">
                        </div>

                        <!-- Datos del jugador -->
                        <div class="col-md-6">
                            <div class="mb-2">
                                <input type="text" class="form-control" placeholder="Nombre del equipo" name="nombre">
                            </div>
                     
                            <div class="mt-3 d-flex justify-content-end">
                                <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                                    data-bs-target="#confirmacionModal">
                                    Confirmar Alta
                                </button>
                            </div>
                        </div>

                        <!-- Modal de Confirmación -->
                        <div class="modal fade" id="confirmacionModal" tabindex="-1"
                            aria-labelledby="confirmacionModalLabel" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="confirmacionModalLabel">Confirmación</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Cerrar"></button>
                                    </div>
                                    <div class="modal-body">
                                        ¿Estás seguro de que deseas guardar los datos del Equipo?
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary"
                                            data-bs-dismiss="modal">Cancelar</button>
                                        <button type="submit" class="btn btn-success" form="formJugador">Confirmar y
                                            Guardar</button>
                                    </div>
                                </div>

                                <!-- Scripts -->
                                <script
                                    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"
                                    integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL"
                                    crossorigin="anonymous"></script>

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
</body>

</html>