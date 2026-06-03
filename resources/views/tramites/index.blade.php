@extends('layouts.app')

@section('title', 'Trámites')
@section('subtitle', 'Gestión de trámites administrativos')

@section('content')

{{-- ── Stats ── --}}
<div class="row g-3" style="margin-bottom:24px">
    <div class="col-6 col-lg-3">
        <div class="stat-card">
            <div class="stat-icon" style="background:#EDE9FE;color:#7C3AED"><i class="bi bi-file-earmark-check"></i></div>
            <div>
                <div class="stat-value">{{ $stats['total'] }}</div>
                <div class="stat-label">Total procesos</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="stat-card">
            <div class="stat-icon" style="background:var(--amber-light);color:var(--amber-dark)"><i class="bi bi-hourglass-split"></i></div>
            <div>
                <div class="stat-value">{{ $stats['en_tramite'] }}</div>
                <div class="stat-label">En trámite</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="stat-card">
            <div class="stat-icon" style="background:var(--primary-lighter);color:var(--primary)"><i class="bi bi-exclamation-triangle"></i></div>
            <div>
                <div class="stat-value">{{ $stats['observados'] }}</div>
                <div class="stat-label">Observados</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="stat-card">
            <div class="stat-icon" style="background:var(--green-light);color:var(--green-dark)"><i class="bi bi-check-circle"></i></div>
            <div>
                <div class="stat-value">{{ $stats['aprobados'] }}</div>
                <div class="stat-label">Aprobados</div>
            </div>
        </div>
    </div>
</div>

{{-- ── Header + filtros ── --}}
<div class="page-header" style="margin-bottom:14px">
    <div class="page-header-left">
        {{-- Tabs de estado --}}
        <div class="flex gap-8" style="flex-wrap:wrap">
            @php
                $tabEstados = ['' => 'Todos', 'en_tramite' => 'En trámite', 'observado' => 'Observado', 'aprobado' => 'Aprobado', 'rechazado' => 'Rechazado'];
                $estadoActual = request('estado', '');
            @endphp
            @foreach($tabEstados as $val => $label)
            <a href="{{ route('tramites.index', array_merge(request()->except('estado','page'), $val ? ['estado' => $val] : [])) }}"
               class="btn btn-sm {{ $estadoActual === $val ? 'btn-primary' : 'btn-secondary' }}">
                {{ $label }}
            </a>
            @endforeach
        </div>
    </div>
    @if(auth()->user()->esAdmin())
    <a href="{{ route('tramites.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-lg"></i>Nuevo trámite
    </a>
    @endif
</div>

{{-- Búsqueda --}}
<div class="filter-bar" style="margin-bottom:16px">
    <form style="display:flex;align-items:center;gap:10px;width:100%" method="GET">
        @if(request('estado'))<input type="hidden" name="estado" value="{{ request('estado') }}">@endif
        <input type="text" name="buscar" value="{{ request('buscar') }}"
               class="form-control" style="max-width:320px"
               placeholder="Buscar por N°, tipo, entidad...">
        <button type="submit" class="btn btn-primary btn-sm"><i class="bi bi-search"></i>Buscar</button>
        <a href="{{ route('tramites.index') }}" class="btn btn-secondary btn-sm">Limpiar</a>
    </form>
</div>

{{-- ── Tabla ── --}}
<div class="card">
    <div class="card-header" style="border-left-color:#7C3AED">
        <span><i class="bi bi-file-earmark-check" style="color:#7C3AED;margin-right:8px"></i>Lista de trámites</span>
        <span style="font-size:12px;font-weight:500;color:var(--text-light)">{{ $tramites->total() }} registro(s)</span>
    </div>
    @php
        $grupos = $tramites->getCollection()->groupBy(fn($t) => $t->entidad_nombre ?? 'Sin entidad');
    @endphp
    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>N°</th>
                    <th>TIPO / ENTIDAD</th>
                    <th># DE TRÁMITE</th>
                    <th>ÁREA ACTUAL</th>
                    <th>FECHA MODIFICACIÓN<br><span style="font-weight:400;color:var(--text-light)">INGRESO</span></th>
                    <th>ESTADO</th>
                    <th class="td-end">ACCIONES</th>
                </tr>
            </thead>
            <tbody>
                @if($tramites->isEmpty())
                <tr>
                    <td colspan="7">
                        <div class="empty-state">
                            <i class="bi bi-file-earmark-check"></i>
                            <p>No hay trámites registrados</p>
                            @if(auth()->user()->esAdmin())
                                <a href="{{ route('tramites.create') }}" class="btn btn-primary btn-sm">
                                    <i class="bi bi-plus-lg"></i>Nuevo trámite
                                </a>
                            @endif
                        </div>
                    </td>
                </tr>
                @else
                @foreach($grupos as $entidadNombre => $grupoTramites)
                @php
                    $gId       = 'g'.$loop->index;
                    $collapsed = $grupoTramites->count() > 1;
                @endphp
                {{-- ── Fila cabecera de carpeta ── --}}
                <tr onclick="toggleGrupo('{{ $gId }}')"
                    style="cursor:pointer;background:#F5F3FF;user-select:none;border-left:3px solid #7C3AED">
                    <td colspan="7" style="padding:9px 16px;border-bottom:1px solid #E9D5FF">
                        <div style="display:flex;align-items:center;gap:10px">
                            <i id="icon-{{ $gId }}" class="bi {{ $collapsed ? 'bi-folder' : 'bi-folder2-open' }}"
                               style="color:#7C3AED;font-size:17px;flex-shrink:0"></i>
                            <span style="font-weight:700;color:#374151;font-size:13px">{{ $entidadNombre }}</span>
                            <span style="font-size:11px;font-weight:600;color:#7C3AED;background:#EDE9FE;padding:2px 9px;border-radius:12px;flex-shrink:0">
                                {{ $grupoTramites->count() }} {{ $grupoTramites->count() === 1 ? 'trámite' : 'trámites' }}
                            </span>
                            <i id="arrow-{{ $gId }}" class="bi {{ $collapsed ? 'bi-chevron-right' : 'bi-chevron-down' }}"
                               style="color:#7C3AED;font-size:12px;margin-left:auto;flex-shrink:0"></i>
                        </div>
                    </td>
                </tr>
                {{-- ── Filas de trámites del grupo ── --}}
                @foreach($grupoTramites as $tramite)
                <tr class="gfila-{{ $gId }}" style="{{ $collapsed ? 'display:none' : '' }}">
                    <td class="fw-700" style="color:var(--text-dark);white-space:nowrap;padding-left:24px">{{ $tramite->numero }}</td>
                    <td>
                        <div class="fw-600" style="color:var(--primary)">{{ $tramite->tipo ?? '—' }}</div>
                        <div style="font-size:12px;color:var(--text-light)">{{ $tramite->entidad_nombre ?? '—' }}</div>
                    </td>
                    <td>
                        @if($tramite->codigo_tramite)
                            <span class="badge badge-gray">{{ $tramite->codigo_tramite }}</span>
                        @else
                            <span class="text-muted">—</span>
                        @endif
                    </td>
                    <td style="font-size:12.5px;color:var(--primary);font-weight:600">
                        {{ $tramite->area_actual ?? '—' }}
                    </td>
                    <td>
                        @if($tramite->fecha_modificacion)
                            <div style="font-size:12.5px;font-weight:600">{{ $tramite->fecha_modificacion->format('d/m/Y') }}</div>
                        @endif
                        @if($tramite->fecha_ingreso)
                            <div style="font-size:11px;color:var(--text-light)">
                                INGRESO {{ $tramite->fecha_ingreso->format('d/m/Y') }}
                            </div>
                        @endif
                        @if($tramite->fecha_vencimiento)
                            <div style="font-size:11px;color:{{ $tramite->fecha_vencimiento->isPast() ? 'var(--primary)' : 'var(--text-light)' }}">
                                vence {{ $tramite->fecha_vencimiento->format('d/m/Y') }}
                            </div>
                        @endif
                    </td>
                    <td>
                        <span class="badge badge-{{ $tramite->badge_color }}">{{ $tramite->badge_label }}</span>
                    </td>
                    <td class="td-end">
                        <div class="flex flex-center gap-8" style="justify-content:flex-end">
                            <button type="button"
                               class="btn btn-sm btn-icon"
                               style="background:#EDE9FE;color:#7C3AED;border:none"
                               title="Ver proceso"
                               onclick="verProceso({{ $tramite->id }})">
                                <i class="bi bi-diagram-3"></i>
                            </button>
                            <button type="button"
                               class="btn btn-sm btn-icon"
                               style="background:{{ $tramite->archivo_pdf ? '#FEE2E2' : '#F3F4F6' }};color:{{ $tramite->archivo_pdf ? '#DC2626' : '#6B7280' }};border:none"
                               title="{{ $tramite->archivo_pdf ? 'Ver / reemplazar PDF' : 'Subir PDF' }}"
                               onclick="verPdf({{ $tramite->id }})">
                                <i class="bi bi-file-earmark-pdf"></i>
                            </button>
                            <a href="{{ route('tramites.show', $tramite) }}"
                               class="btn btn-sm btn-secondary btn-icon" title="Ver detalle">
                                <i class="bi bi-eye"></i>
                            </a>
                            @if(auth()->user()->esAdmin())
                            <a href="{{ route('tramites.edit', $tramite) }}"
                               class="btn btn-sm btn-warning btn-icon" title="Editar">
                                <i class="bi bi-pencil"></i>
                            </a>
                            @endif
                        </div>
                    </td>
                </tr>
                @endforeach
                @endforeach
                @endif
            </tbody>
        </table>
    </div>
    @if($tramites->hasPages())
    <div class="card-footer">{{ $tramites->withQueryString()->links() }}</div>
    @endif
</div>

{{-- ── Modal: Proceso del trámite ── --}}
<div id="modal-proceso" style="display:none;position:fixed;inset:0;z-index:1000;background:rgba(0,0,0,.45);align-items:center;justify-content:center;padding:16px">
    <div style="background:#fff;border-radius:12px;width:100%;max-width:740px;max-height:92vh;display:flex;flex-direction:column;box-shadow:0 20px 60px rgba(0,0,0,.25)">
        {{-- Header --}}
        <div style="display:flex;align-items:center;justify-content:space-between;padding:16px 20px;border-bottom:1px solid var(--border);flex-shrink:0">
            <div>
                <div style="font-weight:700;font-size:15px;color:var(--text-dark)" id="modal-proceso-titulo">Proceso</div>
                <div style="font-size:12px;color:var(--text-light);margin-top:2px" id="modal-proceso-subtitulo"></div>
            </div>
            <div style="display:flex;gap:8px;align-items:center">
                <a id="modal-proceso-print-link" href="#" target="_blank"
                   class="btn btn-sm btn-secondary" style="font-size:12px">
                    <i class="bi bi-printer"></i>Imprimir
                </a>
                <button type="button" onclick="cerrarModalProceso()"
                        style="background:none;border:none;font-size:20px;cursor:pointer;color:var(--text-light);line-height:1">&times;</button>
            </div>
        </div>
        {{-- Body: tabla de pasos --}}
        <div style="overflow-y:auto;padding:16px 20px;flex:1">
            <div id="modal-proceso-body"></div>
        </div>
        {{-- Footer: agregar paso --}}
        <div id="modal-proceso-footer" style="border-top:1px solid var(--border);padding:14px 20px;flex-shrink:0;background:#F8FAFC;border-radius:0 0 12px 12px">
            <div style="font-size:11px;font-weight:700;color:#7C3AED;text-transform:uppercase;letter-spacing:.6px;margin-bottom:10px">
                <i class="bi bi-plus-circle"></i> Agregar paso
            </div>
            <div style="display:grid;grid-template-columns:1fr 1fr 1fr auto auto;gap:8px;align-items:end">
                <div>
                    <label style="font-size:10px;font-weight:600;color:var(--text-light);display:block;margin-bottom:3px">ÁREA *</label>
                    <input id="np-area" type="text" class="form-control form-control-sm" placeholder="Área" list="np-areas-list">
                    <datalist id="np-areas-list">
                        <option value="Mesa de Partes">
                        <option value="Gerencia de Desarrollo Urbano">
                        <option value="Gerencia General">
                        <option value="Alcaldía">
                        <option value="Gerencia de Obras">
                        <option value="Secretaría General">
                        <option value="Asesoría Legal">
                        <option value="Subgerencia de Control Urbano">
                    </datalist>
                </div>
                <div>
                    <label style="font-size:10px;font-weight:600;color:var(--text-light);display:block;margin-bottom:3px">N° NOTIFICACIÓN</label>
                    <input id="np-notif" type="text" class="form-control form-control-sm" placeholder="Ej: N° 6211-2026">
                </div>
                <div>
                    <label style="font-size:10px;font-weight:600;color:var(--text-light);display:block;margin-bottom:3px">OBSERVACIÓN</label>
                    <input id="np-obs" type="text" class="form-control form-control-sm" placeholder="(opcional)">
                </div>
                <div>
                    <label style="font-size:10px;font-weight:600;color:var(--text-light);display:block;margin-bottom:3px">ESTADO</label>
                    <select id="np-estado" class="form-control form-control-sm">
                        <option value="pendiente">Pendiente</option>
                        <option value="en_proceso">En proceso</option>
                        <option value="finalizado">Finalizado</option>
                    </select>
                </div>
                <div>
                    <button type="button" id="btn-guardar-paso"
                            style="background:#7C3AED;color:#fff;border:none;padding:6px 14px;border-radius:7px;font-size:12px;font-weight:700;cursor:pointer;white-space:nowrap;height:34px">
                        <i class="bi bi-plus-lg"></i> Guardar
                    </button>
                </div>
            </div>
            <div id="np-error" style="display:none;color:var(--primary);font-size:12px;margin-top:6px"></div>
        </div>
    </div>
</div>

{{-- ── Modal: PDF del trámite ── --}}
<div id="modal-pdf" style="display:none;position:fixed;inset:0;z-index:1001;background:rgba(0,0,0,.45);align-items:center;justify-content:center;padding:16px">
    <div style="background:#fff;border-radius:12px;width:100%;max-width:460px;box-shadow:0 20px 60px rgba(0,0,0,.25)">
        <div style="display:flex;align-items:center;justify-content:space-between;padding:16px 20px;border-bottom:1px solid var(--border)">
            <div style="font-weight:700;font-size:15px;color:var(--text-dark)">
                <i class="bi bi-file-earmark-pdf" style="color:#DC2626;margin-right:6px"></i>Archivo PDF
            </div>
            <button type="button" onclick="cerrarModalPdf()"
                    style="background:none;border:none;font-size:20px;cursor:pointer;color:var(--text-light);line-height:1">&times;</button>
        </div>
        <div style="padding:20px">
            <div id="pdf-tramite-info" style="font-size:13px;color:var(--text-light);margin-bottom:14px"></div>
            {{-- PDF existente --}}
            <div id="pdf-existing" style="display:none;margin-bottom:16px;padding:10px 14px;background:#FEF2F2;border-radius:8px;border:1px solid #FEE2E2">
                <div style="display:flex;align-items:center;gap:10px">
                    <i class="bi bi-file-earmark-pdf-fill" style="color:#DC2626;font-size:22px;flex-shrink:0"></i>
                    <div style="flex:1">
                        <div style="font-weight:600;font-size:13px;color:var(--text-dark)">PDF cargado</div>
                        <a id="pdf-ver-link" href="#" target="_blank"
                           style="font-size:12px;color:#DC2626;text-decoration:none">Abrir / Ver PDF &rarr;</a>
                    </div>
                    <button type="button" onclick="eliminarPdf()"
                            style="background:none;border:1px solid #FCA5A5;border-radius:6px;padding:4px 8px;color:#DC2626;font-size:11px;cursor:pointer">
                        <i class="bi bi-trash"></i>
                    </button>
                </div>
            </div>
            {{-- Upload --}}
            <form id="pdf-upload-form">
                <label style="font-size:12px;font-weight:600;color:var(--text-light);display:block;margin-bottom:6px">
                    {{ '¿Subir nuevo PDF? (máx. 5 MB)' }}
                </label>
                <input type="file" id="pdf-file-input" accept=".pdf"
                       style="display:block;width:100%;padding:7px 10px;border:1px solid var(--border);border-radius:8px;font-size:13px;margin-bottom:12px">
                <button type="submit"
                        style="background:#DC2626;color:#fff;border:none;padding:9px 20px;border-radius:8px;font-size:13px;font-weight:700;cursor:pointer;width:100%">
                    <i class="bi bi-cloud-upload"></i> Subir PDF
                </button>
                <div id="pdf-upload-msg" style="display:none;margin-top:8px;font-size:12px;text-align:center"></div>
            </form>
        </div>
    </div>
</div>

@php
$_tramitesProcesosData = $tramites->map(fn($t) => [
    'id'         => $t->id,
    'numero'     => $t->numero,
    'tipo'       => $t->tipo,
    'entidad'    => $t->entidad_nombre,
    'archivo_pdf'=> $t->archivo_pdf ? Storage::url($t->archivo_pdf) : null,
    'procesos'   => $t->procesos->map(fn($p) => [
        'orden'              => $p->orden,
        'area'               => $p->area,
        'numero_notificacion'=> $p->numero_notificacion,
        'observacion'        => $p->observacion,
        'estado'             => $p->estado,
        'badge_color'        => $p->badge_color,
        'badge_label'        => $p->badge_label,
    ])->values(),
])->keyBy('id');
@endphp

@push('scripts')
<script>
const tramitesProcesos = @json($_tramitesProcesosData);

const printBaseUrl = '{{ url("/tramites") }}';

function toggleGrupo(gId) {
    const rows  = document.querySelectorAll('.gfila-' + gId);
    const icon  = document.getElementById('icon-'  + gId);
    const arrow = document.getElementById('arrow-' + gId);
    const abrir = rows.length > 0 && rows[0].style.display === 'none';
    rows.forEach(r => r.style.display = abrir ? '' : 'none');
    if (icon)  icon.className  = 'bi ' + (abrir ? 'bi-folder2-open' : 'bi-folder');
    if (arrow) arrow.className = 'bi ' + (abrir ? 'bi-chevron-down' : 'bi-chevron-right');
}

function verProceso(id) {
    const t = tramitesProcesos[id];
    if (!t) return;

    document.getElementById('modal-proceso-titulo').textContent = t.numero + ' — ' + (t.tipo || 'Trámite');
    document.getElementById('modal-proceso-subtitulo').textContent = t.entidad || '';
    document.getElementById('modal-proceso-print-link').href = printBaseUrl + '/' + id + '/proceso';

    const badgeColors = {
        pendiente:  'background:#F3F4F6;color:#6B7280',
        en_proceso: 'background:#DBEAFE;color:#1D4ED8',
        finalizado: 'background:#D1FAE5;color:#065F46',
    };

    let html = '';
    if (t.procesos.length === 0) {
        html = `<div style="text-align:center;padding:32px;color:var(--text-light)">
            <i class="bi bi-diagram-3" style="font-size:32px;display:block;margin-bottom:8px;opacity:.4"></i>
            Sin pasos de proceso registrados
        </div>`;
    } else {
        html = `<table style="width:100%;border-collapse:collapse;font-size:13px">
            <thead>
                <tr style="background:#F8FAFC">
                    <th style="padding:8px 10px;text-align:left;font-size:11px;font-weight:700;color:#374151;border-bottom:2px solid #E2E8F0;width:28px">#</th>
                    <th style="padding:8px 10px;text-align:left;font-size:11px;font-weight:700;color:#374151;border-bottom:2px solid #E2E8F0">ÁREA</th>
                    <th style="padding:8px 10px;text-align:left;font-size:11px;font-weight:700;color:#374151;border-bottom:2px solid #E2E8F0">N° NOTIFICACIÓN</th>
                    <th style="padding:8px 10px;text-align:left;font-size:11px;font-weight:700;color:#374151;border-bottom:2px solid #E2E8F0">OBSERVACIÓN</th>
                    <th style="padding:8px 10px;text-align:left;font-size:11px;font-weight:700;color:#374151;border-bottom:2px solid #E2E8F0">ESTADO</th>
                </tr>
            </thead>
            <tbody>`;
        t.procesos.forEach(p => {
            const style = badgeColors[p.estado] || 'background:#F3F4F6;color:#6B7280';
            html += `<tr style="border-bottom:1px solid #F1F5FB">
                <td style="padding:8px 10px;color:#9CA3AF;font-size:11px">${p.orden}</td>
                <td style="padding:8px 10px;font-weight:600;color:var(--text-dark)">${esc(p.area || '—')}</td>
                <td style="padding:8px 10px;font-size:12px;color:var(--text-light)">${esc(p.numero_notificacion || '—')}</td>
                <td style="padding:8px 10px;font-size:12px;color:var(--text-medium);max-width:180px">${esc(p.observacion || '—')}</td>
                <td style="padding:8px 10px">
                    <span style="display:inline-block;padding:2px 10px;border-radius:20px;font-size:11px;font-weight:700;${style}">${esc(p.badge_label)}</span>
                </td>
            </tr>`;
        });
        html += '</tbody></table>';
    }

    document.getElementById('modal-proceso-body').innerHTML = html;
    const modal = document.getElementById('modal-proceso');
    modal.style.display = 'flex';
    document.body.style.overflow = 'hidden';
}

function cerrarModalProceso() {
    document.getElementById('modal-proceso').style.display = 'none';
    document.body.style.overflow = '';
}

function esc(s) {
    return String(s ?? '').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;');
}

document.getElementById('modal-proceso').addEventListener('click', function(e) {
    if (e.target === this) cerrarModalProceso();
});

// ── Agregar paso desde el modal ───────────────────────────
let _procesoActivoId = null;

const _verProcesoOrig = verProceso;
verProceso = function(id) {
    _procesoActivoId = id;
    _verProcesoOrig(id);
};

document.getElementById('btn-guardar-paso').addEventListener('click', async () => {
    const area   = document.getElementById('np-area').value.trim();
    const notif  = document.getElementById('np-notif').value.trim();
    const obs    = document.getElementById('np-obs').value.trim();
    const estado = document.getElementById('np-estado').value;
    const errDiv = document.getElementById('np-error');

    if (!area) { errDiv.textContent = 'El área es obligatoria.'; errDiv.style.display = 'block'; return; }
    errDiv.style.display = 'none';

    const btn = document.getElementById('btn-guardar-paso');
    btn.disabled = true;
    btn.textContent = 'Guardando...';

    try {
        const res = await fetch(`${printBaseUrl}/${_procesoActivoId}/pasos`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            },
            body: JSON.stringify({ area, numero_notificacion: notif, observacion: obs, estado }),
        });
        const data = await res.json();
        if (!res.ok) throw new Error(data.message || 'Error al guardar');

        tramitesProcesos[_procesoActivoId].procesos.push({
            orden: data.paso.orden, area: data.paso.area,
            numero_notificacion: data.paso.numero_notificacion,
            observacion: data.paso.observacion, estado: data.paso.estado,
            badge_color: data.paso.badge_color, badge_label: data.paso.badge_label,
        });

        document.getElementById('np-area').value  = '';
        document.getElementById('np-notif').value = '';
        document.getElementById('np-obs').value   = '';
        document.getElementById('np-estado').value = 'pendiente';

        const t = tramitesProcesos[_procesoActivoId];
        const bc = { pendiente:'background:#F3F4F6;color:#6B7280', en_proceso:'background:#DBEAFE;color:#1D4ED8', finalizado:'background:#D1FAE5;color:#065F46' };
        let html = `<table style="width:100%;border-collapse:collapse;font-size:13px"><thead><tr style="background:#F8FAFC">
            <th style="padding:8px 10px;font-size:11px;font-weight:700;color:#374151;border-bottom:2px solid #E2E8F0;width:28px">#</th>
            <th style="padding:8px 10px;font-size:11px;font-weight:700;color:#374151;border-bottom:2px solid #E2E8F0">ÁREA</th>
            <th style="padding:8px 10px;font-size:11px;font-weight:700;color:#374151;border-bottom:2px solid #E2E8F0">N° NOTIFICACIÓN</th>
            <th style="padding:8px 10px;font-size:11px;font-weight:700;color:#374151;border-bottom:2px solid #E2E8F0">OBSERVACIÓN</th>
            <th style="padding:8px 10px;font-size:11px;font-weight:700;color:#374151;border-bottom:2px solid #E2E8F0">ESTADO</th>
        </tr></thead><tbody>`;
        t.procesos.forEach(p => {
            const s = bc[p.estado] || 'background:#F3F4F6;color:#6B7280';
            html += `<tr style="border-bottom:1px solid #F1F5FB">
                <td style="padding:8px 10px;color:#9CA3AF;font-size:11px">${p.orden}</td>
                <td style="padding:8px 10px;font-weight:600">${esc(p.area||'—')}</td>
                <td style="padding:8px 10px;font-size:12px;color:#64748B">${esc(p.numero_notificacion||'—')}</td>
                <td style="padding:8px 10px;font-size:12px;max-width:180px">${esc(p.observacion||'—')}</td>
                <td style="padding:8px 10px"><span style="display:inline-block;padding:2px 10px;border-radius:20px;font-size:11px;font-weight:700;${s}">${esc(p.badge_label)}</span></td>
            </tr>`;
        });
        html += '</tbody></table>';
        document.getElementById('modal-proceso-body').innerHTML = html;

    } catch(e) {
        errDiv.textContent = e.message; errDiv.style.display = 'block';
    } finally {
        btn.disabled = false;
        btn.innerHTML = '<i class="bi bi-plus-lg"></i> Guardar';
    }
});

// ── Modal PDF ─────────────────────────────────────────────
let _pdfActivoId = null;

function verPdf(id) {
    _pdfActivoId = id;
    const t = tramitesProcesos[id];
    document.getElementById('pdf-tramite-info').textContent = (t?.numero||'') + (t?.tipo ? ' — '+t.tipo : '');
    const pdfUrl = t?.archivo_pdf;
    const existDiv = document.getElementById('pdf-existing');
    if (pdfUrl) { existDiv.style.display = 'block'; document.getElementById('pdf-ver-link').href = pdfUrl; }
    else { existDiv.style.display = 'none'; }
    document.getElementById('pdf-file-input').value = '';
    document.getElementById('pdf-upload-msg').style.display = 'none';
    document.getElementById('modal-pdf').style.display = 'flex';
    document.body.style.overflow = 'hidden';
}

function cerrarModalPdf() {
    document.getElementById('modal-pdf').style.display = 'none';
    document.body.style.overflow = '';
}

document.getElementById('modal-pdf').addEventListener('click', function(e) {
    if (e.target === this) cerrarModalPdf();
});

document.getElementById('pdf-upload-form').addEventListener('submit', async function(e) {
    e.preventDefault();
    const file   = document.getElementById('pdf-file-input').files[0];
    const msgDiv = document.getElementById('pdf-upload-msg');
    if (!file) { msgDiv.textContent='Selecciona un archivo PDF.'; msgDiv.style.color='var(--primary)'; msgDiv.style.display='block'; return; }
    if (file.size > 5*1024*1024) { msgDiv.textContent='El archivo no debe superar 5 MB.'; msgDiv.style.color='var(--primary)'; msgDiv.style.display='block'; return; }

    const btn = this.querySelector('button[type=submit]');
    btn.disabled = true; btn.textContent = 'Subiendo...';

    const fd = new FormData();
    fd.append('archivo_pdf', file);
    fd.append('_token', document.querySelector('meta[name="csrf-token"]').content);

    try {
        const res  = await fetch(`${printBaseUrl}/${_pdfActivoId}/pdf`, { method:'POST', body:fd });
        const data = await res.json();
        if (!res.ok) throw new Error(data.message||'Error al subir');
        tramitesProcesos[_pdfActivoId].archivo_pdf = data.url;
        document.getElementById('pdf-existing').style.display = 'block';
        document.getElementById('pdf-ver-link').href = data.url;
        msgDiv.textContent = '¡PDF subido correctamente!'; msgDiv.style.color='#059669'; msgDiv.style.display='block';
        document.getElementById('pdf-file-input').value = '';
    } catch(err) {
        msgDiv.textContent = err.message; msgDiv.style.color='var(--primary)'; msgDiv.style.display='block';
    } finally {
        btn.disabled = false; btn.innerHTML = '<i class="bi bi-cloud-upload"></i> Subir PDF';
    }
});

async function eliminarPdf() {
    if (!confirm('¿Eliminar el PDF adjunto?')) return;
    const res = await fetch(`${printBaseUrl}/${_pdfActivoId}/pdf`, {
        method: 'DELETE',
        headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
    });
    if (res.ok) {
        tramitesProcesos[_pdfActivoId].archivo_pdf = null;
        document.getElementById('pdf-existing').style.display = 'none';
    }
}

document.addEventListener('keydown', e => {
    if (e.key === 'Escape') { cerrarModalProceso(); cerrarModalPdf(); }
});
</script>
@endpush

@endsection
