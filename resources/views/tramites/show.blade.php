@extends('layouts.app')

@section('title', $tramite->numero)
@section('subtitle', $tramite->entidad_nombre ?? 'Trámite')

@section('content')

<div class="page-header">
    <div class="page-header-left">
        <a href="{{ route('tramites.index') }}" class="back-btn"><i class="bi bi-arrow-left"></i></a>
        <div>
            <div class="page-title">{{ $tramite->tipo ?? $tramite->numero }}</div>
            <div style="margin-top:4px;display:flex;gap:8px;align-items:center;flex-wrap:wrap">
                @if($tramite->entidad_expediente)
                    <span style="font-size:12px;color:var(--text-light);font-weight:500">{{ $tramite->entidad_expediente }}</span>
                @endif
                <span class="badge badge-{{ $tramite->badge_color }}">{{ $tramite->badge_label }}</span>
                @if($tramite->activo)
                    <span class="badge badge-success"><i class="bi bi-circle-fill dot"></i>Activa</span>
                @endif
            </div>
        </div>
    </div>
    <div class="flex gap-8">
        @if($tramite->archivo_pdf)
        <a href="{{ Storage::url($tramite->archivo_pdf) }}" target="_blank" class="btn btn-secondary">
            <i class="bi bi-paperclip"></i>Adjunto
        </a>
        @endif
        <a href="{{ route('tramites.proceso', $tramite) }}" target="_blank" class="btn btn-secondary">
            <i class="bi bi-file-earmark-pdf"></i>Ver PDF
        </a>
        @if(auth()->user()->esAdmin())
        <a href="{{ route('tramites.edit', $tramite) }}" class="btn btn-warning">
            <i class="bi bi-pencil"></i>Editar
        </a>
        @endif
    </div>
</div>

@if(session('success'))
<div class="alert alert-success">
    <i class="bi bi-check-circle-fill"></i>{{ session('success') }}
    <button class="alert-dismiss" onclick="this.closest('.alert').remove()">&times;</button>
</div>
@endif

<div class="row g-3">

    {{-- ── Col izquierda: Información ── --}}
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <span><i class="bi bi-info-circle"></i>Información</span>
            </div>
            <div class="card-body">
                <div class="detail-grid">

                    @if($tramite->entidad_nombre)
                    <div class="detail-row">
                        <div class="detail-label">Entidad</div>
                        <div class="detail-value fw-600">{{ $tramite->entidad_nombre }}</div>
                    </div>
                    @endif

                    @if($tramite->encargado)
                    <div class="detail-row">
                        <div class="detail-label">Encargado</div>
                        <div class="detail-value">{{ $tramite->encargado }}</div>
                    </div>
                    @else
                    <div class="detail-row">
                        <div class="detail-label">Encargado</div>
                        <div class="detail-value text-muted">—</div>
                    </div>
                    @endif

                    <div class="detail-row">
                        <div class="detail-label">Área actual</div>
                        <div class="detail-value fw-600" style="color:var(--primary)">{{ $tramite->area_actual ?? '—' }}</div>
                    </div>

                    @if($tramite->doc_presentado)
                    <div class="detail-row">
                        <div class="detail-label">Doc. presentado</div>
                        <div class="detail-value" style="font-size:12.5px">{{ $tramite->doc_presentado }}</div>
                    </div>
                    @endif

                    @if($tramite->encargado_area)
                    <div class="detail-row">
                        <div class="detail-label">Encargado de área</div>
                        <div class="detail-value" style="font-size:12.5px">{{ $tramite->encargado_area }}</div>
                    </div>
                    @endif

                    <div class="detail-row">
                        <div class="detail-label">Contacto</div>
                        <div class="detail-value" style="font-size:12.5px;color:var(--text-light)">
                            {{ $tramite->contacto ?? 'Celular o E-mail' }}
                        </div>
                    </div>

                    <div class="detail-row">
                        <div class="detail-label">Apunte adicional</div>
                        <div class="detail-value" style="font-size:12.5px;color:var(--text-light)">
                            {{ $tramite->apunte_adicional ?? 'Celular o E-mail' }}
                        </div>
                    </div>
                </div>

                {{-- Fechas --}}
                <div style="margin-top:14px;padding-top:12px;border-top:1px solid var(--border)">
                    <div class="detail-grid">
                        @if($tramite->fecha_ingreso)
                        <div class="detail-row">
                            <div class="detail-label">Ingreso</div>
                            <div class="detail-value">{{ $tramite->fecha_ingreso->format('d/m/Y') }}</div>
                        </div>
                        @endif

                        @if($tramite->fecha_modificacion)
                        <div class="detail-row">
                            <div class="detail-label">Modificación</div>
                            <div class="detail-value">{{ $tramite->fecha_modificacion->format('d/m/Y') }}</div>
                        </div>
                        @endif

                        @if($tramite->fecha_vencimiento)
                        <div class="detail-row">
                            <div class="detail-label">Vencimiento</div>
                            <div class="detail-value">
                                {{ $tramite->fecha_vencimiento->format('d/m/Y') }}
                                @if($tramite->fecha_vencimiento->isPast())
                                    <span class="badge badge-danger" style="margin-left:4px">Vencido</span>
                                @elseif($tramite->fecha_vencimiento->diffInDays(now()) <= 7)
                                    <span class="badge badge-warning" style="margin-left:4px">Por vencer</span>
                                @endif
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ── Col derecha: Proceso ── --}}
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header" style="border-left-color:#7C3AED">
                <span><i class="bi bi-diagram-3" style="color:#7C3AED;margin-right:8px"></i>Proceso</span>
                <div style="display:flex;gap:8px;align-items:center">
                    <span class="badge badge-gray" id="contador-pasos">{{ $tramite->procesos->count() }} paso(s)</span>
                    <button type="button" onclick="abrirModalPaso()"
                            class="btn btn-sm btn-icon"
                            style="background:#7C3AED;color:#fff;border:none"
                            title="Agregar paso">
                        <i class="bi bi-plus-lg"></i>
                    </button>
                </div>
            </div>
            <div class="table-wrapper">
                <table id="tabla-pasos">
                    <thead>
                        <tr>
                            <th style="width:36px">#</th>
                            <th>ÁREA</th>
                            <th>N° NOTIFICACIÓN</th>
                            <th>OBSERVACIÓN</th>
                            <th style="width:100px">ESTADO</th>
                            <th class="td-end" style="width:110px">ACCIONES</th>
                        </tr>
                    </thead>
                    <tbody id="tbody-pasos">
                        @forelse($tramite->procesos as $paso)
                        @php
                            $circleBg = match($paso->estado) {
                                'finalizado' => '#D1FAE5', 'en_proceso' => '#FEF3C7', default => '#F3F4F6'
                            };
                            $circleColor = match($paso->estado) {
                                'finalizado' => '#065F46', 'en_proceso' => '#92400E', default => '#6B7280'
                            };
                        @endphp
                        <tr data-paso-id="{{ $paso->id }}">
                            <td>
                                <span style="display:inline-flex;align-items:center;justify-content:center;width:26px;height:26px;border-radius:50%;background:#EDE9FE;color:#7C3AED;font-size:11px;font-weight:900">{{ $paso->orden }}</span>
                            </td>
                            <td class="fw-600">{{ $paso->area ?? '—' }}</td>
                            <td style="font-size:12.5px;color:var(--text-light)">{{ $paso->numero_notificacion ?? '—' }}</td>
                            <td style="font-size:12.5px;color:var(--text-medium);max-width:200px">{{ $paso->observacion ?? '—' }}</td>
                            <td><span class="badge badge-{{ $paso->badge_color }}">{{ $paso->badge_label }}</span></td>
                            <td class="td-end">
                                <div style="display:flex;align-items:center;gap:6px;justify-content:flex-end">
                                    {{-- PDF por paso --}}
                                    <button type="button"
                                        title="{{ $paso->archivo_pdf ? 'Ver / cambiar PDF' : 'Subir PDF' }}"
                                        onclick="abrirPanelPaso({{ $paso->id }})"
                                        style="background:{{ $paso->archivo_pdf ? '#FEE2E2' : '#F3F4F6' }};color:{{ $paso->archivo_pdf ? '#DC2626' : '#6B7280' }};border:none;width:28px;height:28px;border-radius:6px;cursor:pointer;display:inline-flex;align-items:center;justify-content:center;font-size:13px">
                                        <i class="bi bi-file-earmark-pdf"></i>
                                    </button>
                                    {{-- Imprimir paso --}}
                                    <button type="button"
                                        title="Imprimir este paso"
                                        onclick="imprimirPaso({{ $paso->id }})"
                                        style="background:#F0FDF4;color:#16A34A;border:none;width:28px;height:28px;border-radius:6px;cursor:pointer;display:inline-flex;align-items:center;justify-content:center;font-size:13px">
                                        <i class="bi bi-printer"></i>
                                    </button>
                                    {{-- Círculo resumen rápido --}}
                                    <button type="button"
                                        title="Resumen rápido"
                                        onclick="abrirPanelPaso({{ $paso->id }})"
                                        style="background:{{ $circleBg }};color:{{ $circleColor }};border:none;width:32px;height:32px;border-radius:50%;cursor:pointer;display:inline-flex;align-items:center;justify-content:center;font-size:14px;font-weight:900;flex-shrink:0">
                                        {{ $paso->orden }}
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr id="empty-row">
                            <td colspan="6">
                                <div class="empty-state" style="padding:28px">
                                    <i class="bi bi-diagram-3"></i>
                                    <p>Sin pasos de proceso registrados</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

{{-- ── Panel lateral: Resumen rápido del paso ── --}}
<div id="panel-overlay" onclick="cerrarPanelPaso()"
     style="display:none;position:fixed;inset:0;z-index:800;background:rgba(0,0,0,.25)"></div>

<div id="panel-paso"
     style="position:fixed;top:0;right:-380px;width:360px;height:100vh;background:#fff;z-index:900;
            box-shadow:-6px 0 30px rgba(0,0,0,.15);transition:right .25s ease;
            display:flex;flex-direction:column;overflow:hidden">

    {{-- Panel header --}}
    <div style="padding:16px 20px;border-bottom:1px solid var(--border);flex-shrink:0;background:#7C3AED">
        <div style="display:flex;align-items:flex-start;justify-content:space-between">
            <div>
                <div style="font-size:10px;font-weight:700;color:rgba(255,255,255,.7);text-transform:uppercase;letter-spacing:.8px;margin-bottom:2px">
                    Paso <span id="pp-orden">#</span>
                </div>
                <div style="font-weight:700;font-size:15px;color:#fff" id="pp-area">—</div>
            </div>
            <button onclick="cerrarPanelPaso()"
                    style="background:rgba(255,255,255,.2);border:none;width:28px;height:28px;border-radius:50%;cursor:pointer;color:#fff;font-size:16px;line-height:1;flex-shrink:0;display:flex;align-items:center;justify-content:center">
                &times;
            </button>
        </div>
        <div id="pp-estado-wrap" style="margin-top:10px"></div>
    </div>

    {{-- Panel body --}}
    <div style="flex:1;overflow-y:auto;padding:18px 20px">

        {{-- N° Notificación --}}
        <div style="margin-bottom:14px">
            <div style="font-size:10px;font-weight:700;text-transform:uppercase;color:var(--text-light);letter-spacing:.6px;margin-bottom:4px">
                <i class="bi bi-bell" style="margin-right:4px"></i>N° Notificación
            </div>
            <div id="pp-notif" style="font-size:13px;color:#5B21B6;font-weight:600;background:#EDE9FE;padding:6px 10px;border-radius:6px">—</div>
        </div>

        {{-- Observación --}}
        <div style="margin-bottom:16px">
            <div style="font-size:10px;font-weight:700;text-transform:uppercase;color:var(--text-light);letter-spacing:.6px;margin-bottom:4px">
                <i class="bi bi-chat-left-text" style="margin-right:4px"></i>Observación
            </div>
            <div id="pp-obs" style="font-size:13px;color:var(--text-medium);background:#F8FAFC;padding:8px 10px;border-radius:6px;border:1px solid var(--border);min-height:44px">—</div>
        </div>

        {{-- Archivo PDF del paso --}}
        <div style="margin-bottom:16px;border-top:1px solid var(--border);padding-top:16px">
            <div style="font-size:10px;font-weight:700;text-transform:uppercase;color:var(--text-light);letter-spacing:.6px;margin-bottom:8px">
                <i class="bi bi-file-earmark-pdf" style="margin-right:4px"></i>Archivo PDF
            </div>

            {{-- PDF existente --}}
            <div id="pp-pdf-existing" style="display:none;margin-bottom:10px;padding:10px 12px;background:#FEF2F2;border-radius:8px;border:1px solid #FEE2E2">
                <div style="display:flex;align-items:center;gap:8px">
                    <i class="bi bi-file-earmark-pdf-fill" style="color:#DC2626;font-size:20px;flex-shrink:0"></i>
                    <a id="pp-pdf-link" href="#" target="_blank"
                       style="font-size:12px;color:#DC2626;text-decoration:none;flex:1">Ver PDF adjunto &rarr;</a>
                    <button onclick="eliminarPdfPaso()"
                            style="background:none;border:1px solid #FCA5A5;border-radius:6px;padding:3px 8px;color:#DC2626;font-size:11px;cursor:pointer;flex-shrink:0">
                        <i class="bi bi-trash"></i>
                    </button>
                </div>
            </div>

            {{-- Upload --}}
            <form id="pp-pdf-form">
                <input type="file" id="pp-pdf-input" accept=".pdf"
                       style="display:block;width:100%;padding:6px 8px;border:1px solid var(--border);border-radius:7px;font-size:12px;margin-bottom:8px;background:#fff">
                <button type="submit"
                        style="background:#DC2626;color:#fff;border:none;padding:7px 14px;border-radius:7px;font-size:12px;font-weight:700;cursor:pointer;width:100%">
                    <i class="bi bi-cloud-upload"></i> Subir PDF al paso
                </button>
                <div id="pp-pdf-msg" style="display:none;font-size:11px;margin-top:6px;text-align:center"></div>
            </form>
        </div>

    </div>

    {{-- Panel footer: imprimir --}}
    <div style="padding:14px 20px;border-top:1px solid var(--border);background:#F8FAFC;flex-shrink:0">
        <button onclick="imprimirPasoActivo()"
                style="width:100%;background:#374151;color:#fff;border:none;padding:9px 14px;border-radius:8px;font-size:13px;font-weight:700;cursor:pointer;display:flex;align-items:center;justify-content:center;gap:8px">
            <i class="bi bi-printer"></i> Imprimir este paso
        </button>
    </div>
</div>

{{-- ── Modal: Agregar paso de proceso ── --}}
<div id="modal-add-paso" style="display:none;position:fixed;inset:0;z-index:1000;background:rgba(0,0,0,.45);align-items:center;justify-content:center;padding:16px">
    <div style="background:#fff;border-radius:12px;width:100%;max-width:500px;box-shadow:0 20px 60px rgba(0,0,0,.25)">
        <div style="display:flex;align-items:center;justify-content:space-between;padding:16px 20px;border-bottom:1px solid var(--border)">
            <div style="font-weight:700;font-size:15px;color:var(--text-dark)">
                <i class="bi bi-plus-circle" style="color:#7C3AED;margin-right:6px"></i>Agregar paso de proceso
            </div>
            <button type="button" onclick="cerrarModalPaso()"
                    style="background:none;border:none;font-size:22px;cursor:pointer;color:var(--text-light);line-height:1">&times;</button>
        </div>
        <div style="padding:20px">
            <div class="row g-3">
                <div class="col-12">
                    <label class="form-label">Área <span style="color:var(--primary)">*</span></label>
                    <input id="mp-area" type="text" class="form-control" placeholder="Ej: Gerencia de Desarrollo Urbano" list="mp-areas-list">
                    <datalist id="mp-areas-list">
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
                <div class="col-md-6">
                    <label class="form-label">N° Notificación <span style="color:var(--text-light);font-weight:400">(opcional)</span></label>
                    <input id="mp-notif" type="text" class="form-control" placeholder="Ej: N° 6211-2026">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Estado</label>
                    <select id="mp-estado" class="form-control">
                        <option value="pendiente">Pendiente</option>
                        <option value="en_proceso">En proceso</option>
                        <option value="finalizado">Finalizado</option>
                    </select>
                </div>
                <div class="col-12">
                    <label class="form-label">Observación <span style="color:var(--text-light);font-weight:400">(opcional)</span></label>
                    <input id="mp-obs" type="text" class="form-control" placeholder="Observación o detalle...">
                </div>
            </div>
            <div id="mp-error" style="display:none;color:var(--primary);font-size:13px;margin-top:10px;padding:8px 12px;background:#FEF2F2;border-radius:6px"></div>
            <div style="display:flex;gap:10px;justify-content:flex-end;margin-top:16px">
                <button type="button" onclick="cerrarModalPaso()" class="btn btn-secondary">Cancelar</button>
                <button type="button" id="btn-mp-guardar"
                        style="background:#7C3AED;color:#fff;border:none;padding:9px 20px;border-radius:8px;font-size:14px;font-weight:700;cursor:pointer">
                    <i class="bi bi-floppy"></i> Guardar paso
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Área de impresión de un solo paso --}}
<div id="print-paso-area" style="display:none"></div>

@php
$_pasosData = $tramite->procesos->map(fn($p) => [
    'id'                  => $p->id,
    'orden'               => $p->orden,
    'area'                => $p->area,
    'numero_notificacion' => $p->numero_notificacion,
    'observacion'         => $p->observacion,
    'estado'              => $p->estado,
    'badge_label'         => $p->badge_label,
    'badge_color'         => $p->badge_color,
    'archivo_pdf'         => $p->archivo_pdf ? Storage::url($p->archivo_pdf) : null,
])->keyBy('id');
@endphp

@push('scripts')
<script>
const _tramiteId  = {{ $tramite->id }};
const _tramiteNum = '{{ $tramite->numero }}';
const _tramiteTipo = @json($tramite->tipo ?? $tramite->numero);
const _pasoUrl    = '{{ route("tramites.agregar-paso", $tramite) }}';
const _pasosPdfBase = '{{ url("/tramites/".$tramite->id."/pasos") }}';
const _csrfToken  = document.querySelector('meta[name="csrf-token"]').content;

const pasosData = @json($_pasosData);

const badgeClasses = { pendiente: 'badge-gray', en_proceso: 'badge-warning', finalizado: 'badge-success' };
const badgeLabels  = { pendiente: 'Pendiente',  en_proceso: 'En proceso',   finalizado: 'Finalizado'  };
const circleColors = {
    pendiente:  { bg: '#F3F4F6', color: '#6B7280' },
    en_proceso: { bg: '#FEF3C7', color: '#92400E' },
    finalizado: { bg: '#D1FAE5', color: '#065F46' },
};

// ── Modal agregar paso ────────────────────────────────────
function abrirModalPaso() {
    document.getElementById('mp-area').value   = '';
    document.getElementById('mp-notif').value  = '';
    document.getElementById('mp-obs').value    = '';
    document.getElementById('mp-estado').value = 'pendiente';
    document.getElementById('mp-error').style.display = 'none';
    document.getElementById('modal-add-paso').style.display = 'flex';
    document.body.style.overflow = 'hidden';
    setTimeout(() => document.getElementById('mp-area').focus(), 50);
}

function cerrarModalPaso() {
    document.getElementById('modal-add-paso').style.display = 'none';
    document.body.style.overflow = '';
}

document.getElementById('modal-add-paso').addEventListener('click', function(e) {
    if (e.target === this) cerrarModalPaso();
});

document.getElementById('btn-mp-guardar').addEventListener('click', async () => {
    const area   = document.getElementById('mp-area').value.trim();
    const notif  = document.getElementById('mp-notif').value.trim();
    const obs    = document.getElementById('mp-obs').value.trim();
    const estado = document.getElementById('mp-estado').value;
    const errDiv = document.getElementById('mp-error');

    if (!area) {
        errDiv.textContent = 'El campo Área es obligatorio.';
        errDiv.style.display = 'block';
        document.getElementById('mp-area').focus();
        return;
    }
    errDiv.style.display = 'none';

    const btn = document.getElementById('btn-mp-guardar');
    btn.disabled = true;
    btn.innerHTML = '<i class="bi bi-hourglass"></i> Guardando...';

    try {
        const res  = await fetch(_pasoUrl, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': _csrfToken },
            body: JSON.stringify({ area, numero_notificacion: notif, observacion: obs, estado }),
        });
        const data = await res.json();
        if (!res.ok) throw new Error(data.message || 'Error al guardar el paso.');

        const p = data.paso;
        pasosData[p.id] = { ...p, archivo_pdf: null };

        const emptyRow = document.getElementById('empty-row');
        if (emptyRow) emptyRow.remove();

        const cc = circleColors[p.estado] || circleColors.pendiente;
        const tr = document.createElement('tr');
        tr.dataset.pasoId = p.id;
        tr.innerHTML = `
            <td>
                <span style="display:inline-flex;align-items:center;justify-content:center;width:26px;height:26px;border-radius:50%;background:#EDE9FE;color:#7C3AED;font-size:11px;font-weight:900">${p.orden}</span>
            </td>
            <td class="fw-600">${esc(p.area)}</td>
            <td style="font-size:12.5px;color:var(--text-light)">${esc(p.numero_notificacion || '—')}</td>
            <td style="font-size:12.5px;color:var(--text-medium);max-width:200px">${esc(p.observacion || '—')}</td>
            <td><span class="badge ${badgeClasses[p.estado] || 'badge-gray'}">${badgeLabels[p.estado] || p.estado}</span></td>
            <td class="td-end">
                <div style="display:flex;align-items:center;gap:6px;justify-content:flex-end">
                    <button type="button" title="Subir PDF"
                        onclick="abrirPanelPaso(${p.id})"
                        style="background:#F3F4F6;color:#6B7280;border:none;width:28px;height:28px;border-radius:6px;cursor:pointer;display:inline-flex;align-items:center;justify-content:center;font-size:13px">
                        <i class="bi bi-file-earmark-pdf"></i>
                    </button>
                    <button type="button" title="Imprimir este paso"
                        onclick="imprimirPaso(${p.id})"
                        style="background:#F0FDF4;color:#16A34A;border:none;width:28px;height:28px;border-radius:6px;cursor:pointer;display:inline-flex;align-items:center;justify-content:center;font-size:13px">
                        <i class="bi bi-printer"></i>
                    </button>
                    <button type="button" title="Resumen rápido"
                        onclick="abrirPanelPaso(${p.id})"
                        style="background:${cc.bg};color:${cc.color};border:none;width:32px;height:32px;border-radius:50%;cursor:pointer;display:inline-flex;align-items:center;justify-content:center;font-size:14px;font-weight:900;flex-shrink:0">
                        ${p.orden}
                    </button>
                </div>
            </td>`;
        document.getElementById('tbody-pasos').appendChild(tr);

        const n = document.querySelectorAll('#tbody-pasos tr').length;
        document.getElementById('contador-pasos').textContent = n + ' paso(s)';

        cerrarModalPaso();

    } catch (e) {
        errDiv.textContent = e.message;
        errDiv.style.display = 'block';
    } finally {
        btn.disabled = false;
        btn.innerHTML = '<i class="bi bi-floppy"></i> Guardar paso';
    }
});

// ── Panel lateral de resumen rápido ──────────────────────
let _pasoActivoId = null;

function abrirPanelPaso(id) {
    const p = pasosData[id];
    if (!p) return;
    _pasoActivoId = id;

    document.getElementById('pp-orden').textContent = '#' + p.orden;
    document.getElementById('pp-area').textContent  = p.area || '—';
    document.getElementById('pp-notif').textContent = p.numero_notificacion || '—';
    document.getElementById('pp-obs').textContent   = p.observacion || '—';

    // Estado badge en el header
    const badgeStyleMap = {
        finalizado: 'background:#D1FAE5;color:#065F46',
        en_proceso: 'background:#FEF3C7;color:#92400E',
        pendiente:  'background:rgba(255,255,255,.25);color:#fff',
    };
    const s = badgeStyleMap[p.estado] || badgeStyleMap.pendiente;
    document.getElementById('pp-estado-wrap').innerHTML =
        `<span style="display:inline-block;padding:3px 12px;border-radius:20px;font-size:11px;font-weight:700;${s}">${esc(p.badge_label)}</span>`;

    // PDF existente
    const pdfExist = document.getElementById('pp-pdf-existing');
    if (p.archivo_pdf) {
        pdfExist.style.display = 'block';
        document.getElementById('pp-pdf-link').href = p.archivo_pdf;
    } else {
        pdfExist.style.display = 'none';
    }

    document.getElementById('pp-pdf-input').value = '';
    document.getElementById('pp-pdf-msg').style.display = 'none';

    document.getElementById('panel-overlay').style.display = 'block';
    document.getElementById('panel-paso').style.right = '0';
    document.body.style.overflow = 'hidden';
}

function cerrarPanelPaso() {
    document.getElementById('panel-paso').style.right = '-380px';
    document.getElementById('panel-overlay').style.display = 'none';
    document.body.style.overflow = '';
    _pasoActivoId = null;
}

// ── PDF por paso ──────────────────────────────────────────
document.getElementById('pp-pdf-form').addEventListener('submit', async function(e) {
    e.preventDefault();
    const file   = document.getElementById('pp-pdf-input').files[0];
    const msgDiv = document.getElementById('pp-pdf-msg');
    if (!file) { msgDiv.textContent='Selecciona un archivo PDF.'; msgDiv.style.color='var(--primary)'; msgDiv.style.display='block'; return; }
    if (file.size > 5*1024*1024) { msgDiv.textContent='Máximo 5 MB.'; msgDiv.style.color='var(--primary)'; msgDiv.style.display='block'; return; }

    const btn = this.querySelector('button[type=submit]');
    btn.disabled = true; btn.textContent = 'Subiendo...';

    const fd = new FormData();
    fd.append('archivo_pdf', file);
    fd.append('_token', _csrfToken);

    try {
        const res  = await fetch(`${_pasosPdfBase}/${_pasoActivoId}/pdf`, { method:'POST', body:fd });
        const data = await res.json();
        if (!res.ok) throw new Error(data.message || 'Error al subir');

        pasosData[_pasoActivoId].archivo_pdf = data.url;

        // Actualizar botón PDF en la fila
        const row = document.querySelector(`tr[data-paso-id="${_pasoActivoId}"]`);
        if (row) {
            const pdfBtn = row.querySelector('button[title="Subir PDF"], button[title="Ver / cambiar PDF"]');
            if (pdfBtn) {
                pdfBtn.style.background = '#FEE2E2';
                pdfBtn.style.color = '#DC2626';
                pdfBtn.title = 'Ver / cambiar PDF';
            }
        }

        document.getElementById('pp-pdf-existing').style.display = 'block';
        document.getElementById('pp-pdf-link').href = data.url;
        msgDiv.textContent = '¡PDF subido!'; msgDiv.style.color='#059669'; msgDiv.style.display='block';
        document.getElementById('pp-pdf-input').value = '';
    } catch(err) {
        msgDiv.textContent = err.message; msgDiv.style.color='var(--primary)'; msgDiv.style.display='block';
    } finally {
        btn.disabled = false; btn.innerHTML = '<i class="bi bi-cloud-upload"></i> Subir PDF al paso';
    }
});

async function eliminarPdfPaso() {
    if (!confirm('¿Eliminar el PDF de este paso?')) return;
    const res = await fetch(`${_pasosPdfBase}/${_pasoActivoId}/pdf`, {
        method: 'DELETE',
        headers: { 'X-CSRF-TOKEN': _csrfToken },
    });
    if (res.ok) {
        pasosData[_pasoActivoId].archivo_pdf = null;
        document.getElementById('pp-pdf-existing').style.display = 'none';

        const row = document.querySelector(`tr[data-paso-id="${_pasoActivoId}"]`);
        if (row) {
            const pdfBtn = row.querySelector('button[title="Ver / cambiar PDF"]');
            if (pdfBtn) { pdfBtn.style.background = '#F3F4F6'; pdfBtn.style.color = '#6B7280'; pdfBtn.title = 'Subir PDF'; }
        }
    }
}

// ── Imprimir un solo paso ─────────────────────────────────
function imprimirPaso(id) {
    _pasoActivoId = id;
    imprimirPasoActivo();
}

function imprimirPasoActivo() {
    const p = pasosData[_pasoActivoId];
    if (!p) return;

    const badgeCss = {
        finalizado: 'background:#D1FAE5;color:#065F46',
        en_proceso: 'background:#FEF3C7;color:#92400E',
        pendiente:  'background:#F3F4F6;color:#6B7280',
    };

    const pdfSection = p.archivo_pdf
        ? `<div style="margin-top:10px;padding:8px 12px;background:#FEF2F2;border-radius:6px;border:1px solid #FEE2E2;font-size:10px;color:#DC2626">
               <strong>PDF adjunto:</strong> ${esc(p.archivo_pdf)}
           </div>`
        : '';

    const html = `<!DOCTYPE html><html lang="es"><head><meta charset="UTF-8">
<title>Paso ${esc(String(p.orden))} — ${esc(_tramiteNum)}</title>
<style>
*{box-sizing:border-box;margin:0;padding:0}
body{font-family:Arial,sans-serif;font-size:11px;color:#1a1a1a;padding:20mm 18mm}
.header{border-bottom:3px solid #7C3AED;padding-bottom:12px;margin-bottom:18px;display:flex;justify-content:space-between;align-items:center}
.title{font-size:20px;font-weight:900;color:#7C3AED}
.sub{font-size:11px;color:#374151;margin-top:4px}
.doc-badge{background:#7C3AED;color:#fff;font-size:13px;font-weight:900;padding:4px 14px;border-radius:6px}
.card{border:1px solid #E2E8F0;border-radius:8px;overflow:hidden;margin-bottom:14px}
.card-hd{background:#7C3AED;color:#fff;padding:6px 14px;font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.6px}
.card-bd{padding:12px 14px}
.row{display:flex;gap:8px;margin-bottom:8px;font-size:11px}
.lbl{font-weight:700;color:#374151;min-width:110px;flex-shrink:0}
.val{color:#1a1a1a}
.badge{display:inline-block;padding:2px 10px;border-radius:20px;font-size:10px;font-weight:700}
.footer{margin-top:24px;text-align:center;color:#94A3B8;font-size:10px;border-top:1px solid #E2E8F0;padding-top:10px}
</style></head><body>
<div class="header">
    <div>
        <div class="title">BÚHO Publicidad</div>
        <div class="sub">${esc(_tramiteNum)} — ${esc(_tramiteTipo)}</div>
    </div>
    <div class="doc-badge">PASO #${esc(String(p.orden))}</div>
</div>
<div class="card">
    <div class="card-hd">Detalle del paso</div>
    <div class="card-bd">
        <div class="row"><div class="lbl">Área:</div><div class="val"><strong>${esc(p.area||'—')}</strong></div></div>
        <div class="row"><div class="lbl">N° Notificación:</div><div class="val" style="color:#5B21B6;font-weight:600">${esc(p.numero_notificacion||'—')}</div></div>
        <div class="row"><div class="lbl">Observación:</div><div class="val">${esc(p.observacion||'—')}</div></div>
        <div class="row"><div class="lbl">Estado:</div><div class="val"><span class="badge" style="${badgeCss[p.estado]||badgeCss.pendiente}">${esc(p.badge_label)}</span></div></div>
        ${pdfSection}
    </div>
</div>
<div class="footer">Impreso el ${new Date().toLocaleDateString('es-PE')} — Sistema BÚHO</div>
<script>window.onload=()=>{window.print();}<\/script>
</body></html>`;

    const w = window.open('', '_blank', 'width=700,height=500');
    w.document.write(html);
    w.document.close();
}

// ── Utilidades ────────────────────────────────────────────
function esc(s) {
    return String(s ?? '').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;');
}

document.addEventListener('keydown', e => {
    if (e.key === 'Escape') { cerrarModalPaso(); cerrarPanelPaso(); }
});
</script>
@endpush

@endsection
