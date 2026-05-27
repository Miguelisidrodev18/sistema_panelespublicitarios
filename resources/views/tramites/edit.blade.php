@extends('layouts.app')

@section('title', 'Editar Trámite')
@section('subtitle', $tramite->numero)

@section('content')

<div class="page-header">
    <div class="page-header-left">
        <a href="{{ route('tramites.show', $tramite) }}" class="back-btn"><i class="bi bi-arrow-left"></i></a>
        <div>
            <div class="page-title">Editar — {{ $tramite->numero }}</div>
            <div style="font-size:13px;color:var(--text-light);margin-top:2px">{{ $tramite->tipo ?? 'Trámite' }}</div>
        </div>
    </div>
    <form method="POST" action="{{ route('tramites.destroy', $tramite) }}" onsubmit="return confirm('¿Eliminar este trámite? Esta acción no se puede deshacer.')">
        @csrf @method('DELETE')
        <button type="submit" class="btn btn-danger btn-sm"><i class="bi bi-trash"></i>Eliminar</button>
    </form>
</div>

@if($errors->any())
<div class="alert alert-danger">
    <i class="bi bi-exclamation-triangle-fill"></i>
    <ul style="margin:0;padding-left:16px">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
</div>
@endif

<form method="POST" action="{{ route('tramites.update', $tramite) }}">
@csrf @method('PUT')

{{-- ── Sección 1: Datos del trámite ── --}}
<div class="card" style="margin-bottom:20px">
    <div class="card-header" style="border-left-color:#7C3AED">
        <span><i class="bi bi-file-earmark-text" style="color:#7C3AED;margin-right:8px"></i>Datos del trámite</span>
    </div>
    <div class="card-body">
        <div class="row g-3">

            {{-- Tipo --}}
            <div class="col-md-6">
                <label class="form-label">Tipo de trámite</label>
                <input type="text" name="tipo" value="{{ old('tipo', $tramite->tipo) }}"
                       class="form-control" placeholder="Ej: Nuevo Elemento Panel"
                       list="tipos-list">
                <datalist id="tipos-list">
                    <option value="Nuevo Elemento Panel">
                    <option value="Convenio">
                    <option value="Solicitud de Video">
                    <option value="Permiso de Instalación">
                    <option value="Renovación de Permiso">
                    <option value="Cambio de Diseño">
                </datalist>
            </div>

            {{-- Estado --}}
            <div class="col-md-3">
                <label class="form-label">Estado</label>
                <select name="estado" class="form-control">
                    @foreach($estados as $val => $label)
                    <option value="{{ $val }}" {{ old('estado', $tramite->estado) === $val ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Fecha ingreso --}}
            <div class="col-md-3">
                <label class="form-label">Fecha de ingreso</label>
                <input type="date" name="fecha_ingreso"
                       value="{{ old('fecha_ingreso', $tramite->fecha_ingreso?->format('Y-m-d')) }}"
                       class="form-control">
            </div>

            {{-- Entidad nombre --}}
            <div class="col-md-6">
                <label class="form-label">Entidad / Institución</label>
                <input type="text" name="entidad_nombre" value="{{ old('entidad_nombre', $tramite->entidad_nombre) }}"
                       class="form-control" placeholder="Ej: Municipalidad de Chilca">
            </div>

            {{-- Expediente --}}
            <div class="col-md-6">
                <label class="form-label">Expediente de entidad <span class="text-muted">(opcional)</span></label>
                <input type="text" name="entidad_expediente" value="{{ old('entidad_expediente', $tramite->entidad_expediente) }}"
                       class="form-control" placeholder="Ej: EXP 00185-2026-0-1890-CH-CO-07">
            </div>

            {{-- Código trámite --}}
            <div class="col-md-4">
                <label class="form-label">Código / N° de trámite <span class="text-muted">(opcional)</span></label>
                <input type="text" name="codigo_tramite" value="{{ old('codigo_tramite', $tramite->codigo_tramite) }}"
                       class="form-control" placeholder="Ej: Ch4512-hyo">
            </div>

            {{-- Área actual --}}
            <div class="col-md-4">
                <label class="form-label">Área actual</label>
                <input type="text" name="area_actual" value="{{ old('area_actual', $tramite->area_actual) }}"
                       class="form-control" placeholder="Ej: Gerencia de Des. Urbano"
                       list="areas-list">
                <datalist id="areas-list">
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

            {{-- Fecha vencimiento --}}
            <div class="col-md-4">
                <label class="form-label">Fecha de vencimiento <span class="text-muted">(opcional)</span></label>
                <input type="date" name="fecha_vencimiento"
                       value="{{ old('fecha_vencimiento', $tramite->fecha_vencimiento?->format('Y-m-d')) }}"
                       class="form-control">
            </div>

            {{-- Encargado interno --}}
            <div class="col-md-6">
                <label class="form-label">Encargado interno <span class="text-muted">(opcional)</span></label>
                <input type="text" name="encargado" value="{{ old('encargado', $tramite->encargado) }}"
                       class="form-control" placeholder="Nombre del colaborador a cargo">
            </div>

            {{-- Encargado de área --}}
            <div class="col-md-6">
                <label class="form-label">Encargado en el área <span class="text-muted">(opcional)</span></label>
                <input type="text" name="encargado_area" value="{{ old('encargado_area', $tramite->encargado_area) }}"
                       class="form-control" placeholder="Persona en la entidad que gestiona el trámite">
            </div>

            {{-- Contacto --}}
            <div class="col-md-6">
                <label class="form-label">Contacto <span class="text-muted">(opcional)</span></label>
                <input type="text" name="contacto" value="{{ old('contacto', $tramite->contacto) }}"
                       class="form-control" placeholder="Celular o E-mail de contacto">
            </div>

            {{-- Doc presentado --}}
            <div class="col-md-6">
                <label class="form-label">Documento presentado <span class="text-muted">(opcional)</span></label>
                <input type="text" name="doc_presentado" value="{{ old('doc_presentado', $tramite->doc_presentado) }}"
                       class="form-control" placeholder="Ej: Carta, Solicitud, Expediente técnico">
            </div>

            {{-- Apunte adicional --}}
            <div class="col-12">
                <label class="form-label">Apunte adicional <span class="text-muted">(opcional)</span></label>
                <textarea name="apunte_adicional" class="form-control" rows="2"
                          placeholder="Notas, observaciones o información extra relevante...">{{ old('apunte_adicional', $tramite->apunte_adicional) }}</textarea>
            </div>

        </div>
    </div>
</div>

{{-- ── Sección 2: Pasos del proceso ── --}}
<div class="card" style="margin-bottom:20px">
    <div class="card-header" style="border-left-color:#7C3AED">
        <span><i class="bi bi-diagram-3" style="color:#7C3AED;margin-right:8px"></i>Proceso</span>
        <span class="badge badge-gray" id="contador-pasos">{{ $tramite->procesos->count() }} paso(s)</span>
    </div>
    <div class="card-body" style="padding-bottom:8px">

        <div id="pasos-container">
            {{-- Pasos existentes se cargan via JS --}}
        </div>

        <button type="button" id="btn-add-paso"
                class="btn btn-secondary btn-sm" style="margin-top:8px">
            <i class="bi bi-plus-lg"></i>Agregar paso
        </button>
    </div>
</div>

{{-- ── Acciones ── --}}
<div style="display:flex;gap:10px;justify-content:flex-end;margin-bottom:32px">
    <a href="{{ route('tramites.show', $tramite) }}" class="btn btn-secondary">Cancelar</a>
    <button type="submit" class="btn btn-primary"><i class="bi bi-floppy"></i>Guardar cambios</button>
</div>

</form>

@push('scripts')
<script>
const estadosPaso = {
    pendiente:   'Pendiente',
    en_proceso:  'En proceso',
    finalizado:  'Finalizado',
};
const areasComunes = [
    'Mesa de Partes','Gerencia de Desarrollo Urbano','Gerencia General',
    'Alcaldía','Gerencia de Obras','Secretaría General','Asesoría Legal',
    'Subgerencia de Control Urbano',
];

// Pasos existentes inyectados desde PHP
const pasosExistentes = @json($tramite->procesos->map(fn($p) => [
    'area'               => $p->area,
    'numero_notificacion'=> $p->numero_notificacion,
    'observacion'        => $p->observacion,
    'estado'             => $p->estado,
]));

let pasoIdx = 0;

function crearFilaPaso(data = {}) {
    const idx = pasoIdx++;
    const area   = data.area   ?? '';
    const notif  = data.numero_notificacion ?? '';
    const obs    = data.observacion ?? '';
    const estado = data.estado ?? 'pendiente';

    const areasOpts = areasComunes.map(a => `<option value="${a}">`).join('');

    const row = document.createElement('div');
    row.className = 'paso-row';
    row.style.cssText = 'display:grid;grid-template-columns:1fr 1fr 1fr auto auto;gap:8px;align-items:start;margin-bottom:10px;padding:10px 12px;background:var(--bg-subtle,#f8f9fa);border-radius:8px;border:1px solid var(--border)';
    row.innerHTML = `
        <div>
            <label style="font-size:11px;font-weight:600;color:var(--text-light);margin-bottom:3px;display:block">ÁREA</label>
            <input type="text" name="proc_area[]" value="${esc(area)}"
                   class="form-control form-control-sm" placeholder="Área del trámite"
                   list="areas-proc-list-${idx}">
            <datalist id="areas-proc-list-${idx}">${areasOpts}</datalist>
        </div>
        <div>
            <label style="font-size:11px;font-weight:600;color:var(--text-light);margin-bottom:3px;display:block">N° NOTIFICACIÓN</label>
            <input type="text" name="proc_notificacion[]" value="${esc(notif)}"
                   class="form-control form-control-sm" placeholder="Ej: N° 6211-2026">
        </div>
        <div>
            <label style="font-size:11px;font-weight:600;color:var(--text-light);margin-bottom:3px;display:block">OBSERVACIÓN</label>
            <input type="text" name="proc_observacion[]" value="${esc(obs)}"
                   class="form-control form-control-sm" placeholder="Observación (opcional)">
        </div>
        <div>
            <label style="font-size:11px;font-weight:600;color:var(--text-light);margin-bottom:3px;display:block">ESTADO</label>
            <select name="proc_estado[]" class="form-control form-control-sm">
                ${Object.entries(estadosPaso).map(([v,l]) =>
                    `<option value="${v}" ${estado===v?'selected':''}>${l}</option>`
                ).join('')}
            </select>
        </div>
        <div style="padding-top:19px">
            <button type="button" class="btn btn-sm btn-danger btn-icon btn-remove-paso" title="Quitar paso">
                <i class="bi bi-trash"></i>
            </button>
        </div>
    `;
    row.querySelector('.btn-remove-paso').addEventListener('click', () => {
        row.remove();
        actualizarContador();
    });
    return row;
}

function actualizarContador() {
    const n = document.querySelectorAll('.paso-row').length;
    document.getElementById('contador-pasos').textContent = n + ' paso(s)';
}

function esc(s) {
    return String(s ?? '').replace(/&/g,'&amp;').replace(/"/g,'&quot;').replace(/</g,'&lt;');
}

// Cargar pasos existentes
const container = document.getElementById('pasos-container');
pasosExistentes.forEach(p => {
    container.appendChild(crearFilaPaso(p));
});
actualizarContador();

document.getElementById('btn-add-paso').addEventListener('click', () => {
    container.appendChild(crearFilaPaso());
    actualizarContador();
});
</script>
@endpush

@endsection
