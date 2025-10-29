@extends('layouts.admin')

@section('title', 'Detalle de Temporada')

@section('content')
    <div class="container py-4">
        {{-- Mensajes --}}
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        {{-- Header --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3>Temporada:
                <a href="{{ route('admin.dashboard', ['section' => 'temporadacargadas']) }}"
                    class="text-primary text-decoration-none">
                    {{ $temporada->NombreTemporada }}
                </a>
            </h3>
            <a href="{{ route('admin.dashboard', ['section' => 'temporadacargadas']) }}"
                class="btn btn-outline-secondary btn-sm">Volver</a>
        </div>

        {{-- Crear fecha --}}
        <div class="card mb-4">
            <div class="card-body">
                <form action="{{ route('fechas.store') }}" method="POST" class="row g-2">
                    @csrf
                    <input type="hidden" name="temporada_id" value="{{ $temporada->id }}">
                    <div class="col-md-8">
                        <input type="text" name="nombre" class="form-control" placeholder="Ej: 1º Fecha" required>
                    </div>
                    <div class="col-md-4 text-end">
                        <button class="btn btn-success" type="submit">Crear fecha</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Fechas --}}
        @if($temporada->fechas->count())
            <div class="accordion" id="fechasAccordion">
                @foreach($temporada->fechas as $fecha)
                    <div class="accordion-item mb-3">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                data-bs-target="#collapse-{{ $fecha->id }}">
                                <div class="me-3">
                                    <strong>{{ $fecha->nombre }}</strong>
                                    @if($fecha->dia)
                                        <small
                                            class="text-muted d-block">{{ \Carbon\Carbon::parse($fecha->dia)->format('d/m/Y') }}</small>
                                    @endif
                                </div>
                                <small class="ms-auto text-muted">{{ $fecha->partidos->count() }} partido(s)</small>
                            </button>
                        </h2>

                        <div id="collapse-{{ $fecha->id }}" class="accordion-collapse collapse">
                            <div class="accordion-body">
                                <div class="d-flex justify-content-between mb-3">
                                    <button class="btn btn-sm btn-primary" data-bs-toggle="collapse"
                                        data-bs-target="#newMatch-{{ $fecha->id }}">
                                        Agregar partido
                                    </button>
                                    <button class="btn btn-sm btn-outline-danger" data-bs-toggle="modal"
                                        data-bs-target="#confirmDeleteModal" data-fecha-id="{{ $fecha->id }}"
                                        data-fecha-nombre="{{ $fecha->nombre }}">
                                        Eliminar fecha
                                    </button>
                                </div>

                                {{-- Form crear partido --}}
                                <div class="collapse mb-3" id="newMatch-{{ $fecha->id }}">
                                    <form action="{{ route('partidos.store') }}" method="POST" class="row g-2">
                                        @csrf
                                        <input type="hidden" name="fecha_id" value="{{ $fecha->id }}">
                                        <div class="col-md-5">
                                            <select name="equipo_local_id" class="form-select form-select-sm" required>
                                                <option value="">-- Local --</option>
                                                @foreach($temporada->equipos as $eq)
                                                    <option value="{{ $eq->id }}">{{ $eq->NombreEquipos }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-2 text-center pt-1"><strong>VS</strong></div>
                                        <div class="col-md-5">
                                            <select name="equipo_visitante_id" class="form-select form-select-sm" required>
                                                <option value="">-- Visitante --</option>
                                                @foreach($temporada->equipos as $eq)
                                                    <option value="{{ $eq->id }}">{{ $eq->NombreEquipos }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-12 text-end">
                                            <button type="submit" class="btn btn-sm btn-primary">Agregar</button>
                                        </div>
                                    </form>
                                </div>

                                {{-- Lista partidos --}}
                                @if($fecha->partidos->count())
                                    <div style="max-width: 800px; margin: 0 auto;">
                                        @foreach($fecha->partidos as $p)
                                            <div class="card mb-2 border">
                                                <div class="card-body p-3">
                                                    <div class="d-flex align-items-center justify-content-between">
                                                        {{-- Equipo Local --}}
                                                        <div class="d-flex align-items-center gap-2 flex-fill">
                                                            <img src="{{ $p->equipoLocal->FotoEquipo ? asset('storage/' . $p->equipoLocal->FotoEquipo) : asset('images/placeholder.png') }}"
                                                                style="width: 40px; height: 40px; object-fit: contain; border-radius: 50%;">
                                                            <strong class="text-truncate">{{ $p->equipoLocal->NombreEquipos }}</strong>
                                                        </div>

                                                        {{-- Resultado --}}
                                                        <div class="px-3 text-center flex-shrink-0">
                                                            @if(!is_null($p->goles_local) && !is_null($p->goles_visitante))
                                                                <span class="badge bg-light text-dark border px-3 py-2">
                                                                    {{ $p->goles_local }} : {{ $p->goles_visitante }}
                                                                </span>
                                                            @else
                                                                <strong class="text-muted">VS</strong>
                                                            @endif
                                                        </div>

                                                        {{-- Equipo Visitante --}}
                                                        <div class="d-flex align-items-center gap-2 justify-content-end flex-fill">
                                                            <strong
                                                                class="text-truncate text-end">{{ $p->equipoVisitante->NombreEquipos }}</strong>
                                                            <img src="{{ $p->equipoVisitante->FotoEquipo ? asset('storage/' . $p->equipoVisitante->FotoEquipo) : asset('images/placeholder.png') }}"
                                                                style="width: 40px; height: 40px; object-fit: contain; border-radius: 50%;">
                                                        </div>

                                                        {{-- Botones --}}
                                                        <div class="d-flex gap-2 ms-3 flex-shrink-0">
                                                            <a href="{{ route('admin.partidos.edit_detailed', $p->id) }}"
                                                                class="btn btn-sm btn-outline-dark">
                                                                {{ is_null($p->goles_local) ? 'Definir' : 'Editar' }}
                                                            </a>
                                                            <button class="btn btn-sm btn-outline-danger" data-bs-toggle="modal"
                                                                data-bs-target="#confirmDeletePartidoModal" data-partido-id="{{ $p->id }}"
                                                                data-partido-local="{{ $p->equipoLocal->NombreEquipos }}"
                                                                data-partido-visitante="{{ $p->equipoVisitante->NombreEquipos }}">
                                                                Eliminar
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <p class="text-muted text-center">Sin partidos</p>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-muted">No hay fechas registradas.</p>
        @endif
    </div>

    {{-- Modal eliminar fecha --}}
    <div class="modal fade" id="confirmDeleteModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="deleteFechaForm" method="POST">
                    @csrf
                    @method('DELETE')
                    <div class="modal-header">
                        <h5 class="modal-title">Eliminar fecha</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <p>¿Confirma eliminar <strong id="fechaNombreModal"></strong>?</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-danger btn-sm">Eliminar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Modal eliminar partido --}}
    <div class="modal fade" id="confirmDeletePartidoModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="deletePartidoForm" method="POST">
                    @csrf
                    @method('DELETE')
                    <div class="modal-header">
                        <h5 class="modal-title">Eliminar partido</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <p>¿Seguro que deseas eliminar el partido entre
                            <strong id="partidoLocalModal"></strong> y
                            <strong id="partidoVisitanteModal"></strong>?
                        </p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-danger btn-sm">Eliminar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Modal eliminar fecha
            const modalFecha = document.getElementById('confirmDeleteModal');
            if (modalFecha) {
                modalFecha.addEventListener('show.bs.modal', function (e) {
                    const btn = e.relatedTarget;
                    document.getElementById('fechaNombreModal').textContent = btn.dataset.fechaNombre;
                    document.getElementById('deleteFechaForm').action = '/fechas/' + btn.dataset.fechaId;
                });
            }

            // Modal eliminar partido
            const modalPartido = document.getElementById('confirmDeletePartidoModal');
            if (modalPartido) {
                modalPartido.addEventListener('show.bs.modal', function (e) {
                    const btn = e.relatedTarget;
                    document.getElementById('partidoLocalModal').textContent = btn.dataset.partidoLocal;
                    document.getElementById('partidoVisitanteModal').textContent = btn.dataset.partidoVisitante;
                    document.getElementById('deletePartidoForm').action = '/partidos/' + btn.dataset.partidoId;
                });
            }
        });
    </script>
@endsection