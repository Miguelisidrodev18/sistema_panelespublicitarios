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
            <i class="bi bi-file-earmark-pdf"></i>Ver PDF
        </a>
        @endif
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

    {{-- ── Col derecha: Proceso (timeline) ── --}}
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header" style="border-left-color:#7C3AED">
                <span><i class="bi bi-diagram-3" style="color:#7C3AED;margin-right:8px"></i>Proceso</span>
                <div style="display:flex;gap:8px;align-items:center">
                    <span class="badge badge-gray" id="contador-pasos">{{ $tramite->procesos->count() }} paso(s)</span>
                    <a href="{{ route('tramites.proceso', $tramite) }}" target="_blank"
                       class="btn btn-sm btn-secondary btn-icon" title="Imprimir proceso">
                        <i class="bi bi-printer"></i>
                    </a>
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
                            <th style="width:30px">#</th>
                            <th>ÁREA</th>
                            <th>NÚMERO DE NOTIFICACIÓN</th>
                            <th>OBSERVACIÓN</th>
                            <th>ESTADO</th>
                        </tr>
                    </thead>
                    <tbody id="tbody-pasos">
                        @forelse($tramite->procesos as $paso)
                        <tr>
                            <td style="color:var(--text-lighter);font-size:12px">{{ $paso->orden }}</td>
                            <td class="fw-600">{{ $paso->area ?? '—' }}</td>
                            <td style="font-size:12.5px;color:var(--text-light)">{{ $paso->numero_notificacion ?? '—' }}</td>
                            <td style="font-size:12.5px;color:var(--text-medium);max-width:240px">{{ $paso->observacion ?? '—' }}</td>
                            <td><span class="badge badge-{{ $paso->badge_color }}">{{ $paso->badge_label }}</span></td>
                        </tr>
                        @empty
                        <tr id="empty-row">
                            <td colspan="5">
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

@push('scripts')
<script>
const _tramiteId  = {{ $tramite->id }};
const _pasoUrl    = '{{ route("tramites.agregar-paso", $tramite) }}';
const _csrfToken  = document.querySelector('meta[name="csrf-token"]').content;

const badgeClasses = {
    pendiente:  'badge-gray',
    en_proceso: 'badge-warning',
    finalizado: 'badge-success',
};
const badgeLabels = {
    pendiente:  'Pendiente',
    en_proceso: 'En proceso',
    finalizado: 'Finalizado',
};

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

        // Quitar fila vacía si existe
        const emptyRow = document.getElementById('empty-row');
        if (emptyRow) emptyRow.remove();

        // Insertar nueva fila en la tabla
        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td style="color:var(--text-lighter);font-size:12px">${p.orden}</td>
            <td class="fw-600">${esc(p.area)}</td>
            <td style="font-size:12.5px;color:var(--text-light)">${esc(p.numero_notificacion || '—')}</td>
            <td style="font-size:12.5px;color:var(--text-medium);max-width:240px">${esc(p.observacion || '—')}</td>
            <td><span class="badge ${badgeClasses[p.estado] || 'badge-gray'}">${badgeLabels[p.estado] || p.estado}</span></td>
        `;
        document.getElementById('tbody-pasos').appendChild(tr);

        // Actualizar contador
        const cont = document.getElementById('contador-pasos');
        const n = document.querySelectorAll('#tbody-pasos tr').length;
        cont.textContent = n + ' paso(s)';

        cerrarModalPaso();

    } catch (e) {
        errDiv.textContent = e.message;
        errDiv.style.display = 'block';
    } finally {
        btn.disabled = false;
        btn.innerHTML = '<i class="bi bi-floppy"></i> Guardar paso';
    }
});

function esc(s) {
    return String(s ?? '').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;');
}

document.addEventListener('keydown', e => { if (e.key === 'Escape') cerrarModalPaso(); });
</script>
@endpush

@endsection
