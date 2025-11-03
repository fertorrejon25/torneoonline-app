@extends('layouts.admin')

@section('title', 'Detalle de Temporada')

@section('content')
<div class="container py-4">
    {{-- Mensajes flash (solo por si llegan) --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="mb-0">
            Temporada:
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
                <div class="accordion-item mb-3" id="fecha-item-{{ $fecha->id }}">
                    <h2 class="accordion-header" id="heading-{{ $fecha->id }}">
                        <button class="accordion-button collapsed" type="button"
                                data-bs-toggle="collapse" data-bs-target="#collapse-{{ $fecha->id }}"
                                aria-expanded="false" aria-controls="collapse-{{ $fecha->id }}">
                            <div class="me-3">
                                <strong class="fecha-nombre">{{ $fecha->nombre }}</strong>
                                @if($fecha->dia)
                                    <small class="text-muted d-block">
                                        {{ \Carbon\Carbon::parse($fecha->dia)->format('d/m/Y') }}
                                    </small>
                                @endif
                            </div>
                            <small class="ms-auto text-muted">
                                <span class="partidos-count" id="count-{{ $fecha->id }}">{{ $fecha->partidos->count() }}</span> partido(s)
                            </small>
                        </button>
                    </h2>

                    <div id="collapse-{{ $fecha->id }}" class="accordion-collapse collapse"
                         aria-labelledby="heading-{{ $fecha->id }}" data-bs-parent="#fechasAccordion">
                        <div class="accordion-body">
                            <div class="d-flex justify-content-between mb-3">
                                <button class="btn btn-sm btn-primary" data-bs-toggle="collapse"
                                        data-bs-target="#newMatch-{{ $fecha->id }}">
                                    Agregar partido
                                </button>

                                {{-- BOTÓN ELIMINAR FECHA (sin redirigir) --}}
                                <button class="btn btn-sm btn-outline-danger"
                                        data-bs-toggle="modal"
                                        data-bs-target="#confirmDeleteFechaModal"
                                        data-fecha-id="{{ $fecha->id }}"
                                        data-fecha-nombre="{{ $fecha->nombre }}"
                                        data-destroy-url="{{ route('fechas.destroy', $fecha->id) }}">
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
                                        <div class="card mb-2 border partido-card"
                                             id="partido-card-{{ $p->id }}"
                                             data-fecha-id="{{ $fecha->id }}">
                                            <div class="card-body p-3">
                                                <div class="d-flex align-items-center justify-content-between">
                                                    {{-- Local --}}
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

                                                    {{-- Visitante --}}
                                                    <div class="d-flex align-items-center gap-2 justify-content-end flex-fill">
                                                        <strong class="text-truncate text-end">{{ $p->equipoVisitante->NombreEquipos }}</strong>
                                                        <img src="{{ $p->equipoVisitante->FotoEquipo ? asset('storage/' . $p->equipoVisitante->FotoEquipo) : asset('images/placeholder.png') }}"
                                                             style="width: 40px; height: 40px; object-fit: contain; border-radius: 50%;">
                                                    </div>

                                                    {{-- Acciones --}}
                                                    <div class="d-flex gap-2 ms-3 flex-shrink-0">
                                                        <a href="{{ route('admin.partidos.edit_detailed', $p->id) }}"
                                                           class="btn btn-sm btn-outline-dark">
                                                            {{ is_null($p->goles_local) ? 'Definir' : 'Editar' }}
                                                        </a>

                                                        {{-- ELIMINAR PARTIDO (sin redirigir) --}}
                                                        <button class="btn btn-sm btn-outline-danger"
                                                                data-bs-toggle="modal"
                                                                data-bs-target="#confirmDeletePartidoModal"
                                                                data-partido-id="{{ $p->id }}"
                                                                data-partido-local="{{ $p->equipoLocal->NombreEquipos }}"
                                                                data-partido-visitante="{{ $p->equipoVisitante->NombreEquipos }}"
                                                                data-destroy-url="{{ route('partidos.destroy', $p->id) }}">
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

{{-- Modal eliminar FECHA --}}
<div class="modal fade" id="confirmDeleteFechaModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Eliminar fecha</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>¿Confirma eliminar <strong id="fechaNombreModal"></strong>?</p>
                <div class="text-muted small">Se eliminarán también sus partidos.</div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-danger btn-sm" id="btn-confirm-delete-fecha">Eliminar</button>
            </div>
        </div>
    </div>
</div>

{{-- Modal eliminar PARTIDO --}}
<div class="modal fade" id="confirmDeletePartidoModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Eliminar partido</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>
                    ¿Seguro que deseas eliminar el partido entre
                    <strong id="partidoLocalModal"></strong> y
                    <strong id="partidoVisitanteModal"></strong>?
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-danger btn-sm" id="btn-confirm-delete-partido">Eliminar</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
(function() {
    const csrf = '{{ csrf_token() }}';

    // ========= helpers =========
    async function deleteWithAjax(url) {
        const res = await fetch(url, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': csrf,
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            },
            redirect: 'manual' // por si el backend intenta redirigir
        });
        // aceptamos 200/204/422 como manejables
        if (res.status === 200 || res.status === 204) return { ok: true };
        try { const data = await res.json(); return data; } catch { return { ok:false }; }
    }

    // ========= FECHA =========
    let fechaDestroyUrl = null;
    let fechaIdTarget   = null;

    const modalFecha = document.getElementById('confirmDeleteFechaModal');
    modalFecha?.addEventListener('show.bs.modal', e => {
        const btn = e.relatedTarget;
        fechaDestroyUrl = btn.getAttribute('data-destroy-url');
        fechaIdTarget   = btn.getAttribute('data-fecha-id');
        document.getElementById('fechaNombreModal').textContent =
            btn.getAttribute('data-fecha-nombre') || '';
    });

    document.getElementById('btn-confirm-delete-fecha')?.addEventListener('click', async () => {
        if (!fechaDestroyUrl || !fechaIdTarget) return;

        const { ok } = await deleteWithAjax(fechaDestroyUrl);
        if (ok) {
            // quitar bloque de la fecha del DOM
            const item = document.getElementById(`fecha-item-${fechaIdTarget}`);
            item?.parentNode?.removeChild(item);

            // cerrar modal
            bootstrap.Modal.getInstance(modalFecha)?.hide();
        } else {
            alert('No se pudo eliminar la fecha.');
        }
    });

    // ========= PARTIDO =========
    let partidoDestroyUrl = null;
    let partidoIdTarget   = null;
    let partidoFechaId    = null;

    const modalPartido = document.getElementById('confirmDeletePartidoModal');
    modalPartido?.addEventListener('show.bs.modal', e => {
        const btn = e.relatedTarget;
        partidoDestroyUrl = btn.getAttribute('data-destroy-url');
        partidoIdTarget   = btn.getAttribute('data-partido-id');

        document.getElementById('partidoLocalModal').textContent =
            btn.getAttribute('data-partido-local') || '';
        document.getElementById('partidoVisitanteModal').textContent =
            btn.getAttribute('data-partido-visitante') || '';

        const card = document.getElementById(`partido-card-${partidoIdTarget}`);
        partidoFechaId = card?.getAttribute('data-fecha-id');
    });

    document.getElementById('btn-confirm-delete-partido')?.addEventListener('click', async () => {
        if (!partidoDestroyUrl || !partidoIdTarget) return;

        const { ok } = await deleteWithAjax(partidoDestroyUrl);
        if (ok) {
            // eliminar tarjeta del DOM
            const card = document.getElementById(`partido-card-${partidoIdTarget}`);
            card?.parentNode?.removeChild(card);

            // actualizar contador en el header de la fecha
            if (partidoFechaId) {
                const countEl = document.getElementById(`count-${partidoFechaId}`);
                if (countEl) {
                    const current = parseInt(countEl.textContent || '0', 10);
                    countEl.textContent = Math.max(0, current - 1);
                }
            }

            // cerrar modal
            bootstrap.Modal.getInstance(modalPartido)?.hide();
        } else {
            alert('No se pudo eliminar el partido.');
        }
    });
})();
</script>
@endpush
