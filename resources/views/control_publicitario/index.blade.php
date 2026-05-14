@extends('layouts.app')

@section('title', 'Control Publicitario')
@section('subtitle', 'Estado de campañas por empresa y panel')

@push('styles')
<style>
.pub-stat-card {
    position:relative; overflow:hidden; border-radius:16px;
    padding:22px 24px; color:#fff;
    display:flex; flex-direction:column; gap:6px;
    transition:transform .2s, box-shadow .2s;
}
.pub-stat-card:hover { transform:translateY(-3px); }
.pub-stat-card .stat-icon {
    position:absolute; right:-8px; top:-8px;
    font-size:70px; opacity:.15; line-height:1;
}
.pub-stat-card .stat-val {
    font-size:36px; font-weight:800; line-height:1; color:#fff;
}
.pub-stat-card .stat-lbl {
    font-size:12px; font-weight:600; letter-spacing:.6px;
    text-transform:uppercase; color:rgba(255,255,255,.82);
    display:flex; align-items:center; gap:6px;
}
.pub-stat-card.green  { background:linear-gradient(135deg,#10B981,#059669); box-shadow:0 6px 24px rgba(16,185,129,.35); }
.pub-stat-card.amber  { background:linear-gradient(135deg,#F59E0B,#D97706); box-shadow:0 6px 24px rgba(245,158,11,.35); }
.pub-stat-card.red    { background:linear-gradient(135deg,#DC1E2E,#B01825); box-shadow:0 6px 24px rgba(220,30,46,.35); }

/* Alert badges */
.badge-vencida {
    background:linear-gradient(135deg,#DC2626,#B91C1C);
    color:#fff; box-shadow:0 2px 8px rgba(220,38,38,.45);
}
.badge-urgente {
    background:linear-gradient(135deg,#EA580C,#C2410C);
    color:#fff; box-shadow:0 0 10px rgba(234,88,12,.55);
    animation:pulse-orange 1.6s ease-in-out infinite;
}
.badge-proximo {
    background:linear-gradient(135deg,#2563EB,#1D4ED8);
    color:#fff; box-shadow:0 2px 8px rgba(37,99,235,.4);
}
@keyframes pulse-orange {
    0%,100% { box-shadow:0 0 6px rgba(234,88,12,.45); }
    50%      { box-shadow:0 0 18px rgba(234,88,12,.9); }
}

/* Panel count badges */
.badge-panel-alto   { background:linear-gradient(135deg,#DC2626,#B91C1C); color:#fff; }
.badge-panel-medio  { background:linear-gradient(135deg,#EA580C,#C2410C); color:#fff; }
.badge-panel-bajo   { background:linear-gradient(135deg,#64748B,#475569); color:#fff; }

/* Tabla header luminoso */
.pub-table thead tr th {
    background:linear-gradient(135deg,#1E293B,#334155) !important;
    color:#fff !important; font-weight:700;
    border-bottom:2px solid #2563EB;
}

/* Btn exportar */
.btn-exportar {
    background:linear-gradient(135deg,#059669,#047857);
    color:#fff; border:none;
    box-shadow:0 4px 14px rgba(5,150,105,.4);
    transition:box-shadow .2s, transform .15s;
}
.btn-exportar:hover {
    box-shadow:0 6px 20px rgba(5,150,105,.6);
    transform:translateY(-1px); color:#fff;
}
</style>
@endpush

@section('content')

{{-- Estadísticas luminosas --}}
<div class="stats-grid stagger" style="grid-template-columns:repeat(3,1fr);margin-bottom:28px">
    <div class="pub-stat-card green">
        <span class="stat-icon"><i class="bi bi-megaphone-fill"></i></span>
        <div class="stat-val">{{ $stats['activos'] }}</div>
        <div class="stat-lbl"><i class="bi bi-circle-fill" style="font-size:7px"></i>Campañas Activas</div>
    </div>
    <div class="pub-stat-card amber">
        <span class="stat-icon"><i class="bi bi-pause-circle-fill"></i></span>
        <div class="stat-val">{{ $stats['pausados'] }}</div>
        <div class="stat-lbl"><i class="bi bi-pause-circle-fill" style="font-size:10px"></i>Pausadas</div>
    </div>
    <div class="pub-stat-card red">
        <span class="stat-icon"><i class="bi bi-x-circle-fill"></i></span>
        <div class="stat-val">{{ $stats['cancelados'] }}</div>
        <div class="stat-lbl"><i class="bi bi-x-circle-fill" style="font-size:10px"></i>Canceladas</div>
    </div>
</div>

<div class="page-header">
    <div></div>
    @if(auth()->user()->esAdmin())
    <button class="btn btn-primary" onclick="document.getElementById('modalNuevo').classList.add('open')">
        <i class="bi bi-plus-lg"></i>Nuevo Registro
    </button>
    @endif
</div>

<div class="filter-bar">
    <form style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;width:100%" method="GET">
        <input type="text" name="buscar" value="{{ request('buscar') }}" class="form-control" style="max-width:240px" placeholder="Empresa o código de panel...">
        <select name="estado" class="form-select" style="max-width:160px">
            <option value="">Todos los estados</option>
            <option value="activo" {{ request('estado') === 'activo' ? 'selected' : '' }}>Activo</option>
            <option value="pausado" {{ request('estado') === 'pausado' ? 'selected' : '' }}>Pausado</option>
            <option value="cancelado" {{ request('estado') === 'cancelado' ? 'selected' : '' }}>Cancelado</option>
        </select>
        <select name="tipo_panel" class="form-select" style="max-width:150px">
            <option value="">Tipo panel</option>
            <option value="digital" {{ request('tipo_panel') === 'digital' ? 'selected' : '' }}>Digital</option>
            <option value="tradicional" {{ request('tipo_panel') === 'tradicional' ? 'selected' : '' }}>Tradicional</option>
        </select>
        <button type="submit" class="btn btn-primary btn-sm"><i class="bi bi-search"></i>Filtrar</button>
        <a href="{{ route('control-publicitario.index') }}" class="btn btn-secondary btn-sm">Limpiar</a>
        <a href="{{ route('control-publicitario.exportar', request()->query()) }}" class="btn btn-sm btn-exportar">
            <i class="bi bi-file-earmark-excel-fill"></i> Exportar Excel
        </a>
    </form>
</div>

<div class="card">
    <div class="table-wrapper">
        <table class="pub-table">
            <thead>
                <tr>
                    <th>Empresa</th><th>Panel</th><th>Tipo</th><th>Período</th>
                    <th>En Panel</th><th>Estado</th><th>Notas</th><th class="td-end">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($registros as $reg)
                <tr>
                    <td class="fw-700" style="color:var(--text-dark)">{{ $reg->empresa_nombre }}</td>
                    <td>
                        @php
                            $panelNombre = $reg->tipo_panel === 'digital'
                                ? ($mapaDigital[$reg->panel_codigo]->nombre ?? null)
                                : ($mapaTradicional[$reg->panel_codigo]->nombre ?? null);
                        @endphp
                        @if($panelNombre)
                            <div class="fw-600" style="font-size:13px">{{ $panelNombre }}</div>
                        @endif
                        <code style="font-size:11px">{{ $reg->panel_codigo }}</code>
                    </td>
                    <td>
                        @if($reg->tipo_panel === 'digital')
                            <span class="badge badge-primary"><i class="bi bi-display"></i>Digital</span>
                        @else
                            <span class="badge badge-warning"><i class="bi bi-signpost-2"></i>Tradicional</span>
                        @endif
                    </td>
                    <td>
                        @if($reg->fecha_inicio)
                            <div style="font-size:12.5px">{{ $reg->fecha_inicio->format('d/m/Y') }}</div>
                            @if($reg->fecha_fin)
                                <div class="text-muted" style="font-size:12px">al {{ $reg->fecha_fin->format('d/m/Y') }}</div>
                                @if($reg->estado === 'activo')
                                    @php $dias = (int) now()->diffInDays($reg->fecha_fin, false) @endphp
                                    @if($dias < 0)
                                        <span class="badge badge-vencida" style="margin-top:3px">Vencida</span>
                                    @elseif($dias <= 3)
                                        <span class="badge badge-urgente" style="margin-top:3px">Vence en {{ $dias }}d</span>
                                    @elseif($dias <= 15)
                                        <span class="badge badge-proximo" style="margin-top:3px">{{ $dias }} días</span>
                                    @endif
                                @endif
                            @endif
                        @else <span class="text-muted">—</span> @endif
                    </td>
                    <td>
                        @php $cnt = (int)($panelCounts[$reg->panel_codigo] ?? 0) @endphp
                        @if($cnt > 3)
                            <span class="badge badge-panel-alto" title="{{ $cnt }} anuncios activos en este panel">
                                <i class="bi bi-display"></i> {{ $cnt }} anuncios
                            </span>
                        @elseif($cnt > 1)
                            <span class="badge badge-panel-medio" title="{{ $cnt }} anuncios activos en este panel">
                                <i class="bi bi-display"></i> {{ $cnt }} anuncios
                            </span>
                        @else
                            <span class="badge badge-panel-bajo" title="1 anuncio activo en este panel">
                                <i class="bi bi-display"></i> {{ $cnt ?: 1 }} anuncio
                            </span>
                        @endif
                    </td>
                    <td>
                        @php $bmap = ['activo'=>'success','pausado'=>'warning','cancelado'=>'danger']; @endphp
                        <span class="badge badge-{{ $bmap[$reg->estado] ?? 'gray' }}">{{ ucfirst($reg->estado) }}</span>
                    </td>
                    <td class="text-muted" style="font-size:13px;max-width:180px">{{ Str::limit($reg->notas, 40) ?? '—' }}</td>
                    <td class="td-end">
                        <div class="flex flex-center gap-8" style="justify-content:flex-end">
                            <a href="{{ route('control-publicitario.show', $reg) }}" class="btn btn-sm btn-secondary btn-icon" title="Ver historial"><i class="bi bi-clock-history"></i></a>
                            @if(auth()->user()->esAdmin())
                            <button class="btn btn-sm btn-warning btn-icon"
                                onclick="openEditModal({{ $reg->id }}, '{{ $reg->estado }}', '{{ $reg->fecha_inicio?->format('Y-m-d') ?? '' }}', '{{ $reg->fecha_fin?->format('Y-m-d') ?? '' }}', '{{ addslashes($reg->notas ?? '') }}', '{{ $reg->monto_pagado ?? '' }}', '{{ $reg->monto_pendiente ?? '' }}')"
                                title="Editar"><i class="bi bi-pencil"></i></button>
                            <form action="{{ route('control-publicitario.destroy', $reg) }}" method="POST" onsubmit="return confirm('¿Eliminar este registro?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-danger btn-icon"><i class="bi bi-trash"></i></button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="8"><div class="empty-state"><i class="bi bi-clipboard2-check"></i><p>No hay registros de control publicitario</p></div></td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($registros->hasPages())
    <div class="card-footer">{{ $registros->withQueryString()->links() }}</div>
    @endif
</div>

@if(auth()->user()->esAdmin())
{{-- Modal nuevo --}}
<div class="modal-backdrop" id="modalNuevo" onclick="if(event.target===this)this.classList.remove('open')">
    <div class="modal-box">
        <form action="{{ route('control-publicitario.store') }}" method="POST">
            @csrf
            <div class="modal-header">
                <h5><i class="bi bi-clipboard2-plus" style="margin-right:8px;color:var(--primary-light)"></i>Nuevo Registro</h5>
                <button type="button" class="modal-close" onclick="document.getElementById('modalNuevo').classList.remove('open')">×</button>
            </div>
            <div class="modal-body">
                <div class="grid cols-2" style="gap:14px">
                    <div class="form-group" style="grid-column:1/-1">
                        <label class="form-label">Empresa <span class="req">*</span></label>
                        <input type="text" name="empresa_nombre" class="form-control" list="lista_empresas" required placeholder="Nombre de la empresa...">
                        <datalist id="lista_empresas">@foreach($empresas as $nombre)<option value="{{ $nombre }}">@endforeach</datalist>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Tipo de panel <span class="req">*</span></label>
                        <select name="tipo_panel" id="tipoPanelModal" class="form-select" onchange="actualizarPaneles()">
                            <option value="digital">Digital</option><option value="tradicional">Tradicional</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Código de panel <span class="req">*</span></label>
                        <input type="text" name="panel_codigo" id="panelCodigoModal" class="form-control" list="lista_paneles_digital" required placeholder="Ej: PD-001">
                        <datalist id="lista_paneles_digital">@foreach($paneles_digitales as $p)<option value="{{ $p->codigo }}">{{ $p->codigo }} — {{ $p->nombre }}</option>@endforeach</datalist>
                        <datalist id="lista_paneles_tradicional">@foreach($paneles_tradicionales as $p)<option value="{{ $p->codigo }}">{{ $p->codigo }} — {{ $p->nombre }}</option>@endforeach</datalist>
                    </div>
                    <div class="form-group"><label class="form-label">Fecha inicio</label><input type="date" name="fecha_inicio" class="form-control"></div>
                    <div class="form-group"><label class="form-label">Fecha fin</label><input type="date" name="fecha_fin" class="form-control"></div>
                    <div class="form-group">
                        <label class="form-label">Estado</label>
                        <select name="estado" class="form-select"><option value="activo">Activo</option><option value="pausado">Pausado</option><option value="cancelado">Cancelado</option></select>
                    </div>
                    <div class="form-group">
                        <label class="form-label"><i class="bi bi-currency-dollar" style="color:#059669"></i> Monto Pagado</label>
                        <input type="number" name="monto_pagado" class="form-control" step="0.01" min="0" placeholder="0.00">
                    </div>
                    <div class="form-group">
                        <label class="form-label"><i class="bi bi-currency-dollar" style="color:#EA580C"></i> Monto Pendiente</label>
                        <input type="number" name="monto_pendiente" class="form-control" step="0.01" min="0" placeholder="0.00">
                    </div>
                    <div class="form-group" style="grid-column:1/-1"><label class="form-label">Notas</label><textarea name="notas" class="form-control" rows="2"></textarea></div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="document.getElementById('modalNuevo').classList.remove('open')">Cancelar</button>
                <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg"></i>Guardar</button>
            </div>
        </form>
    </div>
</div>

{{-- Modal editar --}}
<div class="modal-backdrop" id="modalEditar" onclick="if(event.target===this)this.classList.remove('open')">
    <div class="modal-box" style="max-width:540px">
        <form id="formEditar" method="POST">
            @csrf @method('PATCH')
            <div class="modal-header">
                <h5><i class="bi bi-pencil-square" style="margin-right:8px;color:#FCD34D"></i>Actualizar registro</h5>
                <button type="button" class="modal-close" onclick="document.getElementById('modalEditar').classList.remove('open')">×</button>
            </div>
            <div class="modal-body">
                <div class="grid cols-2" style="gap:14px">
                    <div class="form-group"><label class="form-label">Fecha inicio</label><input type="date" name="fecha_inicio" id="editFechaInicio" class="form-control"></div>
                    <div class="form-group"><label class="form-label">Fecha fin</label><input type="date" name="fecha_fin" id="editFechaFin" class="form-control"></div>
                    <div class="form-group" style="grid-column:1/-1">
                        <label class="form-label">Estado</label>
                        <select name="estado" id="editEstado" class="form-select"><option value="activo">Activo</option><option value="pausado">Pausado</option><option value="cancelado">Cancelado</option></select>
                    </div>
                    <div class="form-group">
                        <label class="form-label"><i class="bi bi-currency-dollar" style="color:#059669"></i> Monto Pagado</label>
                        <input type="number" name="monto_pagado" id="editMontoPagado" class="form-control" step="0.01" min="0" placeholder="0.00">
                    </div>
                    <div class="form-group">
                        <label class="form-label"><i class="bi bi-currency-dollar" style="color:#EA580C"></i> Monto Pendiente</label>
                        <input type="number" name="monto_pendiente" id="editMontoPendiente" class="form-control" step="0.01" min="0" placeholder="0.00">
                    </div>
                    <div class="form-group" style="grid-column:1/-1"><label class="form-label">Notas</label><textarea name="notas" id="editNotas" class="form-control" rows="2"></textarea></div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="document.getElementById('modalEditar').classList.remove('open')">Cancelar</button>
                <button type="submit" class="btn btn-warning"><i class="bi bi-check-lg"></i>Guardar</button>
            </div>
        </form>
    </div>
</div>
@endif
@endsection

@push('scripts')
<script>
function openEditModal(id, estado, fechaInicio, fechaFin, notas, montoPagado, montoPendiente) {
    document.getElementById('formEditar').action = '/control-publicitario/' + id;
    document.getElementById('editEstado').value = estado;
    document.getElementById('editFechaInicio').value = fechaInicio;
    document.getElementById('editFechaFin').value = fechaFin;
    document.getElementById('editNotas').value = notas;
    document.getElementById('editMontoPagado').value = montoPagado;
    document.getElementById('editMontoPendiente').value = montoPendiente;
    document.getElementById('modalEditar').classList.add('open');
}
function actualizarPaneles() {
    const tipo = document.getElementById('tipoPanelModal').value;
    const input = document.getElementById('panelCodigoModal');
    input.setAttribute('list', tipo === 'digital' ? 'lista_paneles_digital' : 'lista_paneles_tradicional');
    input.value = '';
}
</script>
@endpush
