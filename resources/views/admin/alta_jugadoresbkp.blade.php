<!DOCTYPE html>
<html>
<head>
    <title>Alta de Jugadores</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: white;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .form-wrapper {
            text-align: center;
            position: absolute;
            top: 30px;
            width: 100%;
        }

        .form-container {
            background-color: #d3d3d3;
            padding: 30px 40px;
            border: 1px solid #000;
            display: flex;
            align-items: center;
            min-width: 250px;
            min-height: 350px;
            justify-content: center;
            gap: 30px;
        }

        .form-container img {
            width: 120px;
            height: 120px;
            object-fit: cover;
            border: 1px solid #999;
            background-color: white;
        }

        .form-container input {
            display: block;
            margin-bottom: 10px;
            padding: 8px;
            width: 220px;
            font-size: 14px;
        }

        h2 {
            margin-bottom: 20px;
        }
    </style>
</head>
<body>

    <div class="form-wrapper">
        <h2>Alta de Jugadores</h2>

        <div class="form-container">
            <img src="public/images/IconoPerfils.png" alt="Foto jugador">
            <div>
                <input type="text" placeholder="Apellido del Jugador">
                <input type="text" placeholder="Nombre del Jugador">
                <input type="text" placeholder="DNI">
                <input type="text" placeholder="Equipo del Jugador">
            </div>
        </div>
          <br>

        <a href="{{ route('admin.dashboard') }}">
            <button type="button" style="margin-top: 15px; padding: 8px 16px;">Volver</button>
        </a>
    </div>

</body>
</html>
