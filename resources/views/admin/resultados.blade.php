<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administrador de Partidos</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">


      <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background-color: #f8f9fa;
            padding: 20px;
        }
        .header-card {
            background: linear-gradient(135deg, #2c3e50, #1a2530);
            color: white;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .match-card {
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            margin-bottom: 15px;
            padding: 15px;
            transition: all 0.3s;
        }
        .match-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
        }
        .team-select {
            font-weight: bold;
            background-color: #f8f9fa;
            padding: 8px 12px;
            border-radius: 5px;
            text-align: center;
            height: 45px;
        }
        .score-input {
            width: 60px;
            text-align: center;
            font-weight: bold;
            font-size: 18px;
            height: 45px;
        }
        .btn-add {
            background: linear-gradient(135deg, #28a745, #20c997);
            border: none;
            border-radius: 50px;
            padding: 10px 20px;
            font-weight: bold;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: all 0.3s;
        }
        .btn-add:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 8px rgba(0, 0, 0, 0.15);
        }
        .btn-save {
            background: linear-gradient(135deg, #007bff, #0056b3);
            border: none;
            border-radius: 5px;
            padding: 8px 15px;
            font-weight: bold;
        }
        .btn-remove {
            background: linear-gradient(135deg, #dc3545, #c82333);
            border: none;
            border-radius: 5px;
            padding: 8px 15px;
            font-weight: bold;
        }
        .vs-text {
            font-weight: bold;
            color: #6c757d;
            margin: 0 10px;
            line-height: 45px;
        }
        .actions-container {
            display: flex;
            gap: 10px;
            justify-content: center;
        }
        .container {
            max-width: 900px;
        }
    </style>
</head>
<body>

    <div class="container">
        <!-- Header -->
        <div class="header-card p-4 mb-4">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2><i class="fas fa-futbol me-2"></i>Temporada 1</h2>
                    <p class="mb-0">Fecha nro 1</p>
                </div>
            </div>
        </div>

        <!-- Partidos Container -->
        <div id="matches-container">
            <!-- Los partidos se agregarán aquí dinámicamente -->
        </div>

        <!-- Controles -->
        <div class="d-flex justify-content-between mt-4">
            <button id="add-match" class="btn btn-add">
                <i class="fas fa-plus me-2"></i>Agregar Partido
            </button>
            <button id="save-all" class="btn btn-save">
                <i class="fas fa-save me-2"></i>Guardar Todos los Cambios
            </button>
        </div>

        <!-- Plantilla de partido (hidden) -->
        <template id="match-template">
            <div class="match-card" data-match-id="">
                <div class="row align-items-center">
                    <!-- Equipo Local -->
                    <div class="col-md-4">
                        <select class="form-control team-select team-home" required>
                            <option value="">-- Seleccionar Equipo Local --</option>
                        </select>
                    </div>

                    <!-- Marcador -->
                    <div class="col-md-3 d-flex justify-content-center align-items-center">
                        <input type="number" class="form-control score-input home-score" value="0" min="0">
                        <span class="vs-text">VS</span>
                        <input type="number" class="form-control score-input away-score" value="0" min="0">
                    </div>

                    <!-- Equipo Visitante -->
                    <div class="col-md-4">
                        <select class="form-control team-select team-away" required>
                            <option value="">-- Seleccionar Equipo Visitante --</option>
                        </select>
                    </div>

                    <!-- Acciones -->
                    <div class="col-md-1 actions-container">
                        <button class="btn btn-save btn-sm save-match">
                            <i class="fas fa-check"></i>
                        </button>
                        <button class="btn btn-remove btn-sm remove-match">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
            </div>
        </template>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const matchesContainer = document.getElementById('matches-container');
            const matchTemplate = document.getElementById('match-template');
            const addMatchBtn = document.getElementById('add-match');
            const saveAllBtn = document.getElementById('save-all');
            let matchCounter = 0;

            // Función para agregar un partido
            function addMatch() {
                const matchClone = matchTemplate.content.cloneNode(true);
                const matchElement = matchClone.querySelector('.match-card');
                matchElement.setAttribute('data-match-id', matchCounter++);

                // Agregar al contenedor
                matchesContainer.appendChild(matchClone);

                // Agregar event listeners a los botones
                const saveBtn = matchesContainer.querySelector('[data-match-id="' + (matchCounter - 1) + '"] .save-match');
                const removeBtn = matchesContainer.querySelector('[data-match-id="' + (matchCounter - 1) + '"] .remove-match');

                saveBtn.addEventListener('click', function () {
                    const matchId = this.closest('.match-card').getAttribute('data-match-id');
                    saveMatch(matchId);
                });

                removeBtn.addEventListener('click', function () {
                    const matchId = this.closest('.match-card').getAttribute('data-match-id');
                    removeMatch(matchId);
                });
            }

            // Función para guardar un partido individual
            function saveMatch(matchId) {
                const matchElement = matchesContainer.querySelector('[data-match-id="' + matchId + '"]');
                const homeTeam = matchElement.querySelector('.team-home option:checked').text;
                const homeScore = matchElement.querySelector('.home-score').value;
                const awayTeam = matchElement.querySelector('.team-away option:checked').text;
                const awayScore = matchElement.querySelector('.away-score').value;

                // Aquí normalmente enviarías los datos al servidor
                alert(`Partido guardado: ${homeTeam} ${homeScore} - ${awayScore} ${awayTeam}`);
            }

            // Función para eliminar un partido
            function removeMatch(matchId) {
                const matchElement = matchesContainer.querySelector('[data-match-id="' + matchId + '"]');
                if (confirm('¿Estás seguro de que quieres eliminar este partido?')) {
                    matchElement.remove();
                }
            }

            // Función para guardar todos los partidos
            function saveAllMatches() {
                const matchElements = matchesContainer.querySelectorAll('.match-card');
                const matchesData = [];

                matchElements.forEach(matchElement => {
                    const homeTeamId = matchElement.querySelector('.team-home').value;
                    const homeTeam = matchElement.querySelector('.team-home option:checked').text;
                    const homeScore = matchElement.querySelector('.home-score').value;
                    const awayTeamId = matchElement.querySelector('.team-away').value;
                    const awayTeam = matchElement.querySelector('.team-away option:checked').text;
                    const awayScore = matchElement.querySelector('.away-score').value;

                    matchesData.push({
                        home_team_id: homeTeamId,
                        home_team: homeTeam,
                        home_score: homeScore,
                        away_team_id: awayTeamId,
                        away_team: awayTeam,
                        away_score: awayScore
                    });
                });

                // Aquí normalmente enviarías todos los datos al servidor
                console.log('Datos de todos los partidos:', matchesData);
                alert(`Se han guardado ${matchesData.length} partidos correctamente.`);
            }

            // Event Listeners
            addMatchBtn.addEventListener('click', function () {
                addMatch();
            });

            saveAllBtn.addEventListener('click', function () {
                saveAllMatches();
            });
        });
    </script>
</body>
</html>