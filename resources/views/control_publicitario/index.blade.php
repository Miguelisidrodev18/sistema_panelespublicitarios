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
.pub-stat-card .stat-val  { font-size:36px; font-weight:800; line-height:1; color:#fff; }
.pub-stat-card .stat-lbl  { font-size:12px; font-weight:600; letter-spacing:.6px; text-transform:uppercase; color:rgba(255,255,255,.82); display:flex; align-items:center; gap:6px; }
.pub-stat-card.green  { background:linear-gradient(135deg,#10B981,#059669); box-shadow:0 6px 24px rgba(16,185,129,.35); }
.pub-stat-card.amber  { background:linear-gradient(135deg,#F59E0B,#D97706); box-shadow:0 6px 24px rgba(245,158,11,.35); }
.pub-stat-card.red    { background:linear-gradient(135deg,#DC1E2E,#B01825); box-shadow:0 6px 24px rgba(220,30,46,.35); }

.badge-vencida  { background:linear-gradient(135deg,#DC2626,#B91C1C); color:#fff; box-shadow:0 2px 8px rgba(220,38,38,.45); }
.badge-urgente  { background:linear-gradient(135deg,#EA580C,#C2410C); color:#fff; box-shadow:0 0 10px rgba(234,88,12,.55); animation:pulse-orange 1.6s ease-in-out infinite; }
.badge-proximo  { background:linear-gradient(135deg,#2563EB,#1D4ED8); color:#fff; box-shadow:0 2px 8px rgba(37,99,235,.4); }
@keyframes pulse-orange { 0%,100%{box-shadow:0 0 6px rgba(234,88,12,.45);}50%{box-shadow:0 0 18px rgba(234,88,12,.9);} }

.pub-table thead tr th { background:linear-gradient(135deg,#1E293B,#334155) !important; color:#fff !important; font-weight:700; border-bottom:2px solid #2563EB; }

.btn-exportar { background:linear-gradient(135deg,#059669,#047857); color:#fff; border:none; box-shadow:0 4px 14px rgba(5,150,105,.4); transition:box-shadow .2s,transform .15s; }
.btn-exportar:hover { box-shadow:0 6px 20px rgba(5,150,105,.6); transform:translateY(-1px); color:#fff; }

/* RUC search en modal */
.ruc-search-box { background:linear-gradient(135deg,#F0F4FF,#EFF6FF); border:1.5px solid #BFDBFE; border-radius:12px; padding:14px 16px; margin-bottom:16px; }
.ruc-search-box .ruc-title { font-size:11px; font-weight:700; letter-spacing:.5px; text-transform:uppercase; color:#2563EB; margin-bottom:8px; display:flex; align-items:center; gap:6px; }
.empresa-found-banner { background:linear-gradient(135deg,#D1FAE5,#A7F3D0); border:1px solid #6EE7B7; border-radius:8px; padding:8px 12px; font-size:12.5px; color:#065F46; display:flex; align-items:center; gap:8px; margin-top:8px; }
.empresa-new-banner   { background:linear-gradient(135deg,#FEF3C7,#FDE68A); border:1px solid #FCD34D; border-radius:8px; padding:8px 12px; font-size:12.5px; color:#92400E; display:flex; align-items:center; gap:8px; margin-top:8px; }
.ruc-tag { display:inline-flex; align-items:center; gap:4px; background:#EFF6FF; color:#2563EB; border:1px solid #BFDBFE; border-radius:6px; padding:2px 8px; font-size:11px; font-weight:600; font-family:monospace; }

/* Panel rows dinámicos */
.panel-row { display:flex; gap:8px; align-items:center; background:var(--bg-card,#F8FAFC); border:1px solid var(--border,#E2E8F0); border-radius:8px; padding:8px 10px; margin-bottom:6px; }
.panel-row .panel-row-fields { display:flex; gap:6px; flex:1; align-items:center; }
.panel-row select { min-width:120px; }
.panel-row input  { flex:1; }
.btn-remove-panel { background:none; border:1px solid #FCA5A5; color:#DC2626; border-radius:6px; padding:3px 8px; font-size:13px; cursor:pointer; transition:background .15s; }
.btn-remove-panel:hover { background:#FEE2E2; }
.btn-add-panel { background:linear-gradient(135deg,#EFF6FF,#DBEAFE); border:1px dashed #2563EB; color:#1D4ED8; border-radius:8px; padding:7px 14px; font-size:13px; font-weight:600; cursor:pointer; width:100%; margin-top:4px; transition:background .15s; }
.btn-add-panel:hover { background:linear-gradient(135deg,#DBEAFE,#BFDBFE); }
.paneles-section-label { font-size:11px; font-weight:700; letter-spacing:.4px; text-transform:uppercase; color:#2563EB; margin-bottom:8px; display:flex; align-items:center; gap:6px; }

/* Panel chips en tabla */
.panel-chip { display:inline-flex; align-items:center; gap:4px; background:#EFF6FF; border:1px solid #BFDBFE; border-radius:6px; padding:2px 7px; font-size:11px; font-weight:600; margin:2px 2px 0 0; cursor:pointer; transition:background .12s; }
.panel-chip:hover { background:#DBEAFE; }
.panel-chip.tradicional { background:#FEF3C7; border-color:#FCD34D; }
.panel-chip.tradicional:hover { background:#FDE68A; }

/* Panel preview */
@keyframes spin { from{transform:rotate(0deg)} to{transform:rotate(360deg)} }
.panel-preview-photo { width:100%; max-height:220px; object-fit:cover; border-radius:10px; display:block; }
.panel-preview-placeholder { width:100%; height:150px; background:linear-gradient(135deg,#F1F5F9,#E2E8F0); border-radius:10px; display:flex; align-items:center; justify-content:center; color:#CBD5E1; font-size:52px; }
.panel-spec-grid { display:grid; grid-template-columns:auto 1fr; gap:7px 16px; font-size:13px; margin:12px 0; }
.panel-spec-label { color:var(--text-lighter); font-weight:600; font-size:12px; white-space:nowrap; padding-top:1px; }
.panel-spec-value { color:var(--text-dark); }
</style>
@endpush

@section('content')

{{-- Estadísticas --}}
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
        <input type="text" name="buscar" value="{{ request('buscar') }}" class="form-control" style="max-width:260px"
               placeholder="Empresa, RUC o código panel...">
        <select name="estado" class="form-select" style="max-width:160px">
            <option value="">Todos los estados</option>
            <option value="activo"    {{ request('estado') === 'activo'    ? 'selected' : '' }}>Activo</option>
            <option value="pausado"   {{ request('estado') === 'pausado'   ? 'selected' : '' }}>Pausado</option>
            <option value="cancelado" {{ request('estado') === 'cancelado' ? 'selected' : '' }}>Cancelado</option>
        </select>
        <select name="tipo_panel" class="form-select" style="max-width:150px">
            <option value="">Tipo panel</option>
            <option value="digital"     {{ request('tipo_panel') === 'digital'     ? 'selected' : '' }}>Digital</option>
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
                    <th>Empresa / RUC</th><th>Pantallas</th><th>Período</th>
                    <th>Estado</th><th>Montos</th><th class="td-end">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($registros as $reg)
                @php
                    $panelesList = $reg->paneles->count()
                        ? $reg->paneles
                        : collect([['panel_codigo' => $reg->panel_codigo, 'tipo_panel' => $reg->tipo_panel]]);
                @endphp
                <tr>
                    <td>
                        <div class="fw-700" style="color:var(--text-dark)">{{ $reg->empresa_nombre }}</div>
                        @if($reg->ruc)
                            <span class="ruc-tag"><i class="bi bi-upc-scan"></i>{{ $reg->ruc }}</span>
                        @endif
                        @if($reg->empresa_id)
                            <a href="{{ route('empresas.show', $reg->empresa_id) }}" class="text-muted" style="font-size:11px;display:block;margin-top:2px" title="Ver empresa">
                                <i class="bi bi-building"></i> Ver empresa
                            </a>
                        @endif
                    </td>
                    <td>
                        @foreach($reg->paneles->count() ? $reg->paneles : collect([(object)['panel_codigo'=>$reg->panel_codigo,'tipo_panel'=>$reg->tipo_panel]]) as $p)
                            @php
                                $pCodigo = is_object($p) ? $p->panel_codigo : $p['panel_codigo'];
                                $pTipo   = is_object($p) ? $p->tipo_panel   : $p['tipo_panel'];
                                $pNombre = $pTipo === 'digital'
                                    ? ($mapaDigital[$pCodigo]->nombre ?? null)
                                    : ($mapaTradicional[$pCodigo]->nombre ?? null);
                            @endphp
                            <span class="panel-chip {{ $pTipo === 'tradicional' ? 'tradicional' : '' }}"
                                  onclick="mostrarVistaPanel('{{ $pCodigo }}', '{{ $pTipo }}')"
                                  title="{{ $pNombre ?? $pCodigo }} — Ver panel">
                                <i class="bi bi-{{ $pTipo === 'digital' ? 'display' : 'signpost-2' }}"></i>
                                {{ $pCodigo }}
                                @if($pNombre) <span style="opacity:.7;font-weight:400">{{ $pNombre }}</span> @endif
                            </span>
                        @endforeach
                        @if($reg->paneles->count() > 1)
                            <div style="font-size:10.5px;color:var(--text-lighter);margin-top:3px">
                                <i class="bi bi-collection"></i> Paquete · {{ $reg->paneles->count() }} pantallas
                            </div>
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
                        @php $bmap = ['activo'=>'success','pausado'=>'warning','cancelado'=>'danger']; @endphp
                        <span class="badge badge-{{ $bmap[$reg->estado] ?? 'gray' }}">{{ ucfirst($reg->estado) }}</span>
                    </td>
                    <td style="font-size:12.5px">
                        @if($reg->monto_pagado !== null)
                            <div style="color:#065F46"><i class="bi bi-check-circle-fill" style="font-size:10px"></i> S/ {{ number_format($reg->monto_pagado,2) }}</div>
                        @endif
                        @if($reg->monto_pendiente !== null)
                            <div style="color:#92400E"><i class="bi bi-clock-fill" style="font-size:10px"></i> S/ {{ number_format($reg->monto_pendiente,2) }}</div>
                        @endif
                        @if($reg->monto_pagado === null && $reg->monto_pendiente === null)
                            <span class="text-muted">—</span>
                        @endif
                    </td>
                    <td class="td-end">
                        <div class="flex flex-center gap-8" style="justify-content:flex-end">
                            <a href="{{ route('control-publicitario.show', $reg) }}" class="btn btn-sm btn-secondary btn-icon" title="Ver historial"><i class="bi bi-clock-history"></i></a>
                            @if(auth()->user()->esAdmin())
                            <button class="btn btn-sm btn-warning btn-icon"
                                onclick="openEditModal({{ $reg->id }}, '{{ $reg->estado }}', '{{ $reg->fecha_inicio?->format('Y-m-d') ?? '' }}', '{{ $reg->fecha_fin?->format('Y-m-d') ?? '' }}', {{ json_encode($reg->notas ?? '') }}, '{{ $reg->monto_pagado ?? '' }}', '{{ $reg->monto_pendiente ?? '' }}', '{{ $reg->ruc ?? '' }}', {{ $reg->paneles->count() ? $reg->paneles->map(fn($p)=>['codigo'=>$p->panel_codigo,'tipo'=>$p->tipo_panel])->toJson() : json_encode([['codigo'=>$reg->panel_codigo,'tipo'=>$reg->tipo_panel]]) }})"
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
                <tr><td colspan="6"><div class="empty-state"><i class="bi bi-clipboard2-check"></i><p>No hay registros de control publicitario</p></div></td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($registros->hasPages())
    <div class="card-footer">{{ $registros->withQueryString()->links() }}</div>
    @endif
</div>

{{-- Datalists globales para paneles --}}
<datalist id="lista_paneles_digital">
    @foreach($paneles_digitales as $p)<option value="{{ $p->codigo }}">{{ $p->codigo }} — {{ $p->nombre }}</option>@endforeach
</datalist>
<datalist id="lista_paneles_tradicional">
    @foreach($paneles_tradicionales as $p)<option value="{{ $p->codigo }}">{{ $p->codigo }} — {{ $p->nombre }}</option>@endforeach
</datalist>

@if(auth()->user()->esAdmin())
{{-- ═══════════════════════════ MODAL NUEVO ═══════════════════════════ --}}
<div class="modal-backdrop" id="modalNuevo" onclick="if(event.target===this)this.classList.remove('open')">
    <div class="modal-box" style="max-width:660px">
        <form action="{{ route('control-publicitario.store') }}" method="POST">
            @csrf
            <div class="modal-header">
                <h5><i class="bi bi-clipboard2-plus" style="margin-right:8px;color:var(--primary-light)"></i>Nuevo Registro Publicitario</h5>
                <button type="button" class="modal-close" onclick="document.getElementById('modalNuevo').classList.remove('open')">×</button>
            </div>
            <div class="modal-body">

                {{-- Búsqueda RUC --}}
                <div class="ruc-search-box">
                    <div class="ruc-title"><i class="bi bi-upc-scan"></i>Buscar empresa por RUC (SUNAT)</div>
                    <div style="display:flex;gap:8px;align-items:center">
                        <input type="text" id="rucInputNuevo" class="form-control" style="max-width:180px;font-family:monospace;letter-spacing:1px"
                               placeholder="RUC (11 dígitos)" maxlength="11" inputmode="numeric">
                        <button type="button" id="btnBuscarRucNuevo" class="btn btn-primary btn-sm" onclick="buscarRucNuevo()">
                            <i class="bi bi-search"></i>Buscar
                        </button>
                        <span class="text-muted" style="font-size:12px">o escribe el nombre abajo</span>
                    </div>
                    <div id="rucResultNuevo" style="display:none;margin-top:8px"></div>
                </div>

                <div class="grid cols-2" style="gap:14px">
                    {{-- Empresa --}}
                    <div class="form-group" style="grid-column:1/-1">
                        <label class="form-label">Empresa <span class="req">*</span></label>
                        <input type="text" name="empresa_nombre" id="nombreEmpresaNuevo" class="form-control"
                               list="lista_empresas_nuevo" required placeholder="Nombre de la empresa..."
                               oninput="onNombreEmpresaChange(this.value)">
                        <input type="hidden" name="ruc" id="rucHiddenNuevo">
                        <datalist id="lista_empresas_nuevo">
                            @foreach($empresas_data as $emp)
                                <option value="{{ $emp->nombre }}">
                            @endforeach
                        </datalist>
                        <div id="empresaStatusNuevo" style="display:none;margin-top:6px"></div>
                    </div>

                    <div class="form-group">
                        <label class="form-label"><i class="bi bi-envelope" style="color:#2563EB"></i> Correo empresa</label>
                        <input type="email" name="empresa_correo" id="correoEmpresaNuevo" class="form-control" placeholder="correo@empresa.com">
                    </div>
                    <div class="form-group">
                        <label class="form-label"><i class="bi bi-phone" style="color:#059669"></i> Celular empresa</label>
                        <input type="text" name="empresa_celular" id="celularEmpresaNuevo" class="form-control" placeholder="999 999 999">
                    </div>
                    <div class="form-group" style="grid-column:1/-1">
                        <label class="form-label"><i class="bi bi-person" style="color:#7C3AED"></i> Encargado / Contacto</label>
                        <input type="text" name="empresa_encargado" id="encargadoEmpresaNuevo" class="form-control" placeholder="Nombre del contacto">
                    </div>

                    {{-- Pantallas (paquete) --}}
                    <div class="form-group" style="grid-column:1/-1">
                        <div class="paneles-section-label"><i class="bi bi-collection"></i> Pantallas contratadas <span class="req">*</span></div>
                        <div id="panelesContainerNuevo"></div>
                        <button type="button" class="btn-add-panel" onclick="addPanelRow('panelesContainerNuevo')">
                            <i class="bi bi-plus-circle"></i> Agregar pantalla
                        </button>
                    </div>

                    {{-- Fechas --}}
                    <div class="form-group"><label class="form-label">Fecha inicio</label><input type="date" name="fecha_inicio" class="form-control"></div>
                    <div class="form-group"><label class="form-label">Fecha fin</label><input type="date" name="fecha_fin" class="form-control"></div>

                    {{-- Estado --}}
                    <div class="form-group">
                        <label class="form-label">Estado</label>
                        <select name="estado" class="form-select">
                            <option value="activo">Activo</option>
                            <option value="pausado">Pausado</option>
                            <option value="cancelado">Cancelado</option>
                        </select>
                    </div>
                    <div class="form-group">{{-- spacer --}}</div>

                    {{-- Montos --}}
                    <div class="form-group">
                        <label class="form-label"><i class="bi bi-currency-dollar" style="color:#059669"></i> Monto Pagado</label>
                        <input type="number" name="monto_pagado" class="form-control" step="0.01" min="0" placeholder="0.00">
                    </div>
                    <div class="form-group">
                        <label class="form-label"><i class="bi bi-currency-dollar" style="color:#EA580C"></i> Monto Pendiente</label>
                        <input type="number" name="monto_pendiente" class="form-control" step="0.01" min="0" placeholder="0.00">
                    </div>

                    <div class="form-group" style="grid-column:1/-1">
                        <label class="form-label">Notas</label>
                        <textarea name="notas" class="form-control" rows="2" placeholder="Observaciones del contrato..."></textarea>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="document.getElementById('modalNuevo').classList.remove('open')">Cancelar</button>
                <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg"></i>Guardar Registro</button>
            </div>
        </form>
    </div>
</div>

{{-- ═══════════════════════════ MODAL EDITAR ═══════════════════════════ --}}
<div class="modal-backdrop" id="modalEditar" onclick="if(event.target===this)this.classList.remove('open')">
    <div class="modal-box" style="max-width:580px">
        <form id="formEditar" method="POST">
            @csrf @method('PATCH')
            <div class="modal-header">
                <h5><i class="bi bi-pencil-square" style="margin-right:8px;color:#FCD34D"></i>Actualizar registro</h5>
                <button type="button" class="modal-close" onclick="document.getElementById('modalEditar').classList.remove('open')">×</button>
            </div>
            <div class="modal-body">
                <div class="grid cols-2" style="gap:14px">
                    <div class="form-group" style="grid-column:1/-1">
                        <label class="form-label"><i class="bi bi-upc-scan" style="color:#2563EB"></i> RUC</label>
                        <input type="text" name="ruc" id="editRuc" class="form-control" style="font-family:monospace;letter-spacing:1px" maxlength="11" placeholder="RUC (11 dígitos)">
                    </div>

                    {{-- Pantallas del paquete --}}
                    <div class="form-group" style="grid-column:1/-1">
                        <div class="paneles-section-label"><i class="bi bi-collection"></i> Pantallas contratadas</div>
                        <div id="panelesContainerEdit"></div>
                        <button type="button" class="btn-add-panel" onclick="addPanelRow('panelesContainerEdit')">
                            <i class="bi bi-plus-circle"></i> Agregar pantalla
                        </button>
                    </div>

                    <div class="form-group"><label class="form-label">Fecha inicio</label><input type="date" name="fecha_inicio" id="editFechaInicio" class="form-control"></div>
                    <div class="form-group"><label class="form-label">Fecha fin</label><input type="date" name="fecha_fin" id="editFechaFin" class="form-control"></div>
                    <div class="form-group" style="grid-column:1/-1">
                        <label class="form-label">Estado</label>
                        <select name="estado" id="editEstado" class="form-select">
                            <option value="activo">Activo</option>
                            <option value="pausado">Pausado</option>
                            <option value="cancelado">Cancelado</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label"><i class="bi bi-currency-dollar" style="color:#059669"></i> Monto Pagado</label>
                        <input type="number" name="monto_pagado" id="editMontoPagado" class="form-control" step="0.01" min="0" placeholder="0.00">
                    </div>
                    <div class="form-group">
                        <label class="form-label"><i class="bi bi-currency-dollar" style="color:#EA580C"></i> Monto Pendiente</label>
                        <input type="number" name="monto_pendiente" id="editMontoPendiente" class="form-control" step="0.01" min="0" placeholder="0.00">
                    </div>
                    <div class="form-group" style="grid-column:1/-1">
                        <label class="form-label">Notas</label>
                        <textarea name="notas" id="editNotas" class="form-control" rows="2"></textarea>
                    </div>
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

{{-- Modal: vista rápida panel --}}
<div class="modal-backdrop" id="modalPanelPreview" onclick="if(event.target===this)this.classList.remove('open')">
    <div class="modal-box" style="max-width:500px">
        <div class="modal-header">
            <h5 id="panelPreviewTitle">
                <i class="bi bi-geo-alt-fill" style="margin-right:8px;color:var(--primary-light)"></i>Vista rápida del panel
            </h5>
            <button type="button" class="modal-close" onclick="document.getElementById('modalPanelPreview').classList.remove('open')">×</button>
        </div>
        <div class="modal-body" id="panelPreviewBody" style="padding:20px"></div>
    </div>
</div>
@endsection

@push('scripts')
<script>
const empresasData = @json($empresas_json);

// ── Panel rows dinámicos ─────────────────────────────────────────────────────
let _panelRowIndex = 0;

function addPanelRow(containerId, codigo = '', tipo = 'digital') {
    const container = document.getElementById(containerId);
    const idx = _panelRowIndex++;
    const prefix = containerId === 'panelesContainerNuevo' ? 'nuevo' : 'edit';
    const listId = tipo === 'digital' ? 'lista_paneles_digital' : 'lista_paneles_tradicional';

    const div = document.createElement('div');
    div.className = 'panel-row';
    div.dataset.rowId = idx;
    div.innerHTML =
        '<div class="panel-row-fields">' +
            '<select name="paneles[' + idx + '][tipo]" class="form-select" onchange="onTipoPanelChange(this)">' +
                '<option value="digital"' + (tipo === 'digital' ? ' selected' : '') + '>Digital</option>' +
                '<option value="tradicional"' + (tipo === 'tradicional' ? ' selected' : '') + '>Tradicional</option>' +
            '</select>' +
            '<input type="text" name="paneles[' + idx + '][codigo]" class="form-control" ' +
                   'placeholder="Ej: PD-001" required ' +
                   'list="' + (tipo === 'digital' ? 'lista_paneles_digital' : 'lista_paneles_tradicional') + '" ' +
                   'value="' + escapeHtml(codigo) + '" ' +
                   'onblur="mostrarVistaPanel(this.value, this.closest(\'.panel-row\').querySelector(\'select\').value)">' +
        '</div>' +
        '<button type="button" class="btn-remove-panel" onclick="removePanelRow(this)" title="Quitar pantalla">' +
            '<i class="bi bi-x-lg"></i>' +
        '</button>';
    container.appendChild(div);
}

function removePanelRow(btn) {
    const row = btn.closest('.panel-row');
    const container = row.parentElement;
    if (container.querySelectorAll('.panel-row').length <= 1) {
        alert('Debe haber al menos una pantalla en el contrato.');
        return;
    }
    row.remove();
}

function onTipoPanelChange(select) {
    const input = select.closest('.panel-row-fields').querySelector('input');
    input.setAttribute('list', select.value === 'digital' ? 'lista_paneles_digital' : 'lista_paneles_tradicional');
    input.value = '';
}

function escapeHtml(str) {
    return String(str).replace(/&/g,'&amp;').replace(/"/g,'&quot;').replace(/</g,'&lt;').replace(/>/g,'&gt;');
}

// Inicializar modal nuevo con una fila vacía al abrir
document.getElementById('modalNuevo')?.addEventListener('transitionend', function() {
    if (this.classList.contains('open')) {
        const c = document.getElementById('panelesContainerNuevo');
        if (c && c.children.length === 0) addPanelRow('panelesContainerNuevo');
    }
});
// Fallback: al hacer click en el botón "Nuevo Registro"
document.querySelector('[onclick*="modalNuevo"]')?.addEventListener('click', function() {
    setTimeout(() => {
        const c = document.getElementById('panelesContainerNuevo');
        if (c && c.children.length === 0) addPanelRow('panelesContainerNuevo');
    }, 50);
});

// ── Modal editar ─────────────────────────────────────────────────────────────
function openEditModal(id, estado, fechaInicio, fechaFin, notas, montoPagado, montoPendiente, ruc, paneles) {
    document.getElementById('formEditar').action = '/control-publicitario/' + id;
    document.getElementById('editEstado').value         = estado;
    document.getElementById('editFechaInicio').value    = fechaInicio;
    document.getElementById('editFechaFin').value       = fechaFin;
    document.getElementById('editNotas').value          = notas;
    document.getElementById('editMontoPagado').value    = montoPagado;
    document.getElementById('editMontoPendiente').value = montoPendiente;
    document.getElementById('editRuc').value            = ruc;

    // Cargar pantallas
    const container = document.getElementById('panelesContainerEdit');
    container.innerHTML = '';
    _panelRowIndex = 1000; // offset para evitar colisión con modal nuevo
    const lista = Array.isArray(paneles) ? paneles : [];
    if (lista.length === 0) {
        addPanelRow('panelesContainerEdit');
    } else {
        lista.forEach(p => addPanelRow('panelesContainerEdit', p.codigo || '', p.tipo || 'digital'));
    }

    document.getElementById('modalEditar').classList.add('open');
}

// ── Empresa lookup ───────────────────────────────────────────────────────────
function onNombreEmpresaChange(value) {
    const match = empresasData.find(e => e.nombre.toLowerCase() === value.toLowerCase());
    const statusDiv = document.getElementById('empresaStatusNuevo');

    if (match) {
        document.getElementById('rucHiddenNuevo').value = match.ruc || '';
        if (match.ruc)       document.getElementById('rucInputNuevo').value         = match.ruc;
        if (match.correo)    document.getElementById('correoEmpresaNuevo').value    = match.correo;
        if (match.celular)   document.getElementById('celularEmpresaNuevo').value   = match.celular;
        if (match.encargado) document.getElementById('encargadoEmpresaNuevo').value = match.encargado;
        statusDiv.style.display = 'block';
        statusDiv.innerHTML = '<div class="empresa-found-banner"><i class="bi bi-check-circle-fill"></i>' +
            '<span><strong>Empresa encontrada</strong> en el sistema' +
            (match.ruc ? ' · RUC: <strong>' + match.ruc + '</strong>' : '') + '</span></div>';
    } else if (value.length > 2) {
        document.getElementById('rucHiddenNuevo').value = document.getElementById('rucInputNuevo').value;
        statusDiv.style.display = 'block';
        statusDiv.innerHTML = '<div class="empresa-new-banner"><i class="bi bi-building-add"></i>' +
            '<span>Empresa nueva — se creará automáticamente al guardar.</span></div>';
    } else {
        statusDiv.style.display = 'none';
    }
}

// ── Búsqueda SUNAT ───────────────────────────────────────────────────────────
function buscarRucNuevo() {
    const ruc = document.getElementById('rucInputNuevo').value.trim();
    const resultDiv = document.getElementById('rucResultNuevo');
    const btn = document.getElementById('btnBuscarRucNuevo');

    if (!/^\d{11}$/.test(ruc)) {
        resultDiv.style.display = 'block';
        resultDiv.innerHTML = '<div class="alert alert-warning" style="margin:0"><i class="bi bi-exclamation-triangle-fill"></i> El RUC debe tener exactamente 11 dígitos.</div>';
        return;
    }

    const localMatch = empresasData.find(e => e.ruc === ruc);
    if (localMatch) {
        document.getElementById('nombreEmpresaNuevo').value = localMatch.nombre;
        document.getElementById('rucHiddenNuevo').value = ruc;
        if (localMatch.correo)    document.getElementById('correoEmpresaNuevo').value   = localMatch.correo;
        if (localMatch.celular)   document.getElementById('celularEmpresaNuevo').value  = localMatch.celular;
        if (localMatch.encargado) document.getElementById('encargadoEmpresaNuevo').value = localMatch.encargado;
        document.getElementById('empresaStatusNuevo').style.display = 'block';
        document.getElementById('empresaStatusNuevo').innerHTML =
            '<div class="empresa-found-banner"><i class="bi bi-check-circle-fill"></i><span><strong>' +
            localMatch.nombre + '</strong> encontrada en el sistema.</span></div>';
        resultDiv.style.display = 'block';
        resultDiv.innerHTML = '<div class="empresa-found-banner"><i class="bi bi-database-check"></i> Empresa cargada desde el sistema local.</div>';
        return;
    }

    btn.disabled = true;
    btn.innerHTML = '<i class="bi bi-arrow-repeat"></i>Buscando...';
    resultDiv.style.display = 'none';

    fetch('/sunat/ruc/' + ruc)
        .then(res => { if (!res.ok) throw new Error(); return res.json(); })
        .then(data => {
            if (!data.nombre) throw new Error();
            document.getElementById('nombreEmpresaNuevo').value = data.nombre;
            document.getElementById('rucHiddenNuevo').value = ruc;
            document.getElementById('empresaStatusNuevo').style.display = 'block';
            document.getElementById('empresaStatusNuevo').innerHTML =
                '<div class="empresa-new-banner"><i class="bi bi-building-add"></i>' +
                '<span>Empresa nueva encontrada en SUNAT — se creará al guardar.</span></div>';
            const dir = [data.direccion, data.distrito, data.provincia, data.departamento]
                .filter(v => v && v !== '-').join(', ');
            resultDiv.style.display = 'block';
            resultDiv.innerHTML =
                '<div class="empresa-found-banner"><i class="bi bi-check-circle-fill"></i>' +
                '<div><strong>' + data.nombre + '</strong>' +
                '<div style="font-size:11.5px;margin-top:3px">RUC: <strong>' + ruc + '</strong>' +
                (dir ? '<br>' + dir : '') + '</div></div></div>';
        })
        .catch(() => {
            resultDiv.style.display = 'block';
            resultDiv.innerHTML = '<div class="alert alert-danger" style="margin:0"><i class="bi bi-x-circle-fill"></i> RUC no encontrado. Completá los datos manualmente.</div>';
        })
        .finally(() => { btn.disabled = false; btn.innerHTML = '<i class="bi bi-search"></i>Buscar'; });
}

document.getElementById('rucInputNuevo')?.addEventListener('keydown', e => {
    if (e.key === 'Enter') { e.preventDefault(); buscarRucNuevo(); }
});

// ── Panel preview ────────────────────────────────────────────────────────────
const _panelCache = {};

async function _fetchPanel(codigo, tipo) {
    const key = tipo + ':' + codigo;
    if (_panelCache[key] !== undefined) return _panelCache[key];
    try {
        const r = await fetch('/panel-preview/' + encodeURIComponent(tipo) + '/' + encodeURIComponent(codigo));
        _panelCache[key] = r.ok ? await r.json() : null;
    } catch { _panelCache[key] = null; }
    return _panelCache[key];
}

async function mostrarVistaPanel(codigoArg, tipoArg) {
    const codigo = (codigoArg || '').trim();
    const tipo   = tipoArg || 'digital';
    if (!codigo) return;

    const modal = document.getElementById('modalPanelPreview');
    const body  = document.getElementById('panelPreviewBody');
    const title = document.getElementById('panelPreviewTitle');

    title.innerHTML = '<i class="bi bi-geo-alt-fill" style="margin-right:8px;color:var(--primary-light)"></i>Vista rápida: <code>' + codigo + '</code>';
    body.innerHTML  = '<div style="display:flex;flex-direction:column;align-items:center;gap:10px;padding:30px;color:var(--text-light)">' +
        '<i class="bi bi-arrow-repeat" style="font-size:28px;animation:spin .8s linear infinite"></i>Cargando datos del panel...</div>';
    modal.classList.add('open');

    const data = await _fetchPanel(codigo, tipo);
    if (!data) {
        body.innerHTML = '<div class="empty-state" style="padding:32px"><i class="bi bi-geo-alt"></i><p>Panel <strong>' + codigo + '</strong> no encontrado.</p></div>';
        return;
    }

    const fotoHtml = data.foto_url
        ? '<img src="' + data.foto_url + '" class="panel-preview-photo" alt="' + data.nombre + '">'
        : '<div class="panel-preview-placeholder"><i class="bi bi-image-alt"></i></div>';

    const typeBadge = data.tipo === 'digital'
        ? '<span class="badge badge-primary"><i class="bi bi-display"></i> Digital</span>'
        : '<span class="badge badge-warning"><i class="bi bi-signpost-2"></i> Tradicional</span>';

    let specs = '<div class="panel-spec-grid">' +
        '<span class="panel-spec-label">Código</span><span class="panel-spec-value"><code>' + data.codigo + '</code></span>' +
        '<span class="panel-spec-label">Tipo</span><span class="panel-spec-value">' + typeBadge + '</span>';
    if (data.direccion) specs += '<span class="panel-spec-label">Dirección</span><span class="panel-spec-value">' + data.direccion + '</span>';
    if (data.medidas)   specs += '<span class="panel-spec-label">Medidas</span><span class="panel-spec-value">' + data.medidas + '</span>';
    if (data.tipo === 'digital') {
        if (data.resolucion)  specs += '<span class="panel-spec-label">Resolución</span><span class="panel-spec-value">' + data.resolucion + '</span>';
        if (data.orientacion) specs += '<span class="panel-spec-label">Orientación</span><span class="panel-spec-value">' + data.orientacion + '</span>';
        if (data.tandas)      specs += '<span class="panel-spec-label">Tandas/hora</span><span class="panel-spec-value">' + data.tandas + '</span>';
    } else {
        if (data.caras)         specs += '<span class="panel-spec-label">Caras</span><span class="panel-spec-value">' + data.caras + '</span>';
        if (data.gramaje_lonas) specs += '<span class="panel-spec-label">Gramaje lonas</span><span class="panel-spec-value">' + data.gramaje_lonas + '</span>';
    }
    specs += '</div>';

    const mapBtn = (data.lat && data.lng)
        ? '<a href="https://www.google.com/maps?q=' + data.lat + ',' + data.lng + '" target="_blank" rel="noopener" ' +
          'class="btn btn-sm btn-secondary" style="margin-top:4px"><i class="bi bi-map"></i> Ver en Google Maps</a>'
        : '';

    body.innerHTML =
        '<div style="margin-bottom:14px">' + fotoHtml + '</div>' +
        '<div style="font-size:16px;font-weight:700;margin-bottom:8px;color:var(--text-dark)">' + data.nombre + '</div>' +
        specs +
        (mapBtn ? '<div>' + mapBtn + '</div>' : '');
}
</script>
@endpush
