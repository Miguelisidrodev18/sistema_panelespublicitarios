@extends('layouts.app')

@section('title', 'Cotizaciones')

@section('content')

{{-- Stats --}}
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon purple"><i class="bi bi-receipt"></i></div>
        <div>
            <div class="stat-value">{{ $stats_cot['total'] }}</div>
            <div class="stat-label">Total</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon amber"><i class="bi bi-hourglass-split"></i></div>
        <div>
            <div class="stat-value">{{ $stats_cot['pendientes'] }}</div>
            <div class="stat-label">Pendientes</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon green"><i class="bi bi-check-circle"></i></div>
        <div>
            <div class="stat-value">{{ $stats_cot['aprobadas'] }}</div>
            <div class="stat-label">Aprobadas</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon blue"><i class="bi bi-currency-dollar"></i></div>
        <div>
            <div class="stat-value" style="font-size:18px">S/. {{ number_format($stats_cot['monto'], 0, ',', '.') }}</div>
            <div class="stat-label">Monto activo</div>
        </div>
    </div>
</div>

{{-- Filtros + botón --}}
<div class="card mb-3">
    <div class="card-body py-2">
        <form class="d-flex flex-wrap gap-2 align-items-center" method="GET">
            <input type="text" name="buscar" value="{{ request('buscar') }}" class="form-control"
                style="max-width:260px" placeholder="Buscar cliente, empresa, N°...">
            <select name="empresa_id" class="form-select" style="max-width:220px">
                <option value="">Todas las empresas</option>
                @foreach($empresas as $emp)
                    <option value="{{ $emp->id }}" {{ request('empresa_id') == $emp->id ? 'selected' : '' }}>
                        {{ $emp->nombre }}
                    </option>
                @endforeach
            </select>
            <div class="d-flex gap-1 flex-wrap">
                @php
                $statusPills = [
                    '' => ['label'=>'Todos','cls'=>'sp-all'],
                    'pendiente'  => ['label'=>'Pendiente', 'cls'=>'sp-pen'],
                    'aprobada'   => ['label'=>'Aprobada',  'cls'=>'sp-apr'],
                    'rechazada'  => ['label'=>'Rechazada', 'cls'=>'sp-rec'],
                    'convertida' => ['label'=>'Convertida','cls'=>'sp-con'],
                ];
                @endphp
                @foreach($statusPills as $val => $pill)
                <a href="{{ route('cotizaciones.index', array_merge(request()->except('estado','page'), $val ? ['estado'=>$val] : [])) }}"
                   class="status-pill {{ $pill['cls'] }} {{ request('estado','') === $val ? 'active' : '' }}">
                    {{ $pill['label'] }}
                </a>
                @endforeach
            </div>
            @if(request()->hasAny(['buscar','empresa_id','estado']))
            <a href="{{ route('cotizaciones.index') }}" class="btn btn-sm btn-secondary">
                <i class="bi bi-x-circle me-1"></i>Limpiar
            </a>
            @endif
            <div class="ms-auto">
                @if(auth()->user()->esAdmin())
                <button type="button" class="btn btn-primary" onclick="abrirModal('modalNuevaCot')">
                    <i class="bi bi-plus-lg me-1"></i>Nueva Cotización
                </button>
                @endif
            </div>
        </form>
    </div>
</div>

{{-- Tabla --}}
<div class="card" style="overflow:visible">
    <div class="card-header">
        <span><i class="bi bi-list-ul" style="color:var(--primary);margin-right:8px"></i>Lista de Cotizaciones</span>
        <span class="text-muted small">{{ $cotizaciones->total() }} cotización(es)</span>
    </div>
    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>N°</th>
                    <th>Cliente</th>
                    <th>Tipo</th>
                    <th>Monto</th>
                    <th>Fecha</th>
                    <th>Estado</th>
                    <th class="text-end">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($cotizaciones as $cot)
                <tr>
                    <td>
                        <span class="badge bg-light border text-dark fw-600">{{ $cot->numero ?? '-' }}</span>
                    </td>
                    <td>
                        <div class="fw-600">
                            @if($cot->empresa)
                                <a href="{{ route('empresas.show', $cot->empresa) }}" class="text-decoration-none">
                                    {{ $cot->empresa->nombre }}
                                </a>
                            @else
                                {{ $cot->cliente_nombre ?? $cot->cliente_empresa ?? '—' }}
                            @endif
                        </div>
                        @if($cot->cliente_nombre && $cot->empresa)
                            <div class="text-muted" style="font-size:12px">{{ $cot->cliente_nombre }}</div>
                        @endif
                        @if($cot->cliente_telefono)
                            <div class="text-muted" style="font-size:12px"><i class="bi bi-telephone me-1"></i>{{ $cot->cliente_telefono }}</div>
                        @endif
                    </td>
                    <td>
                        @if($cot->tipo_contrato)
                            <span class="badge badge-info" style="background:#EFF6FF;color:#2563EB;border:1px solid #BFDBFE">
                                {{ $cot->tipo_contrato }}
                            </span>
                        @else <span class="text-muted">—</span> @endif
                    </td>
                    <td class="fw-700" style="color:#059669">S/. {{ number_format($cot->monto_propuesto, 0, ',', '.') }}</td>
                    <td>
                        <div style="font-size:13px">{{ $cot->fecha_cotizacion?->format('d/m/Y') ?? '-' }}</div>
                        @if($cot->fecha_vencimiento)
                            <div class="text-muted" style="font-size:11px">
                                vence {{ $cot->fecha_vencimiento->format('d/m/Y') }}
                                @if($cot->estado === 'pendiente' && $cot->fecha_vencimiento->isPast())
                                    <span class="badge badge-danger ms-1" style="font-size:10px">Vencida</span>
                                @endif
                            </div>
                        @endif
                    </td>
                    <td>
                        @php $estadoMap = ['pendiente'=>['warning','Pendiente'],'aprobada'=>['success','Aprobada'],'rechazada'=>['danger','Rechazada'],'convertida'=>['primary','Convertida']]; @endphp
                        @php [$bc, $bl] = $estadoMap[$cot->estado] ?? ['secondary', ucfirst($cot->estado)]; @endphp
                        <span class="badge badge-{{ $bc }}">{{ $bl }}</span>
                    </td>
                    <td class="text-end" style="white-space:nowrap">
                        <a href="{{ route('cotizaciones.show', $cot) }}" class="btn btn-sm btn-act btn-act-view" title="Ver detalle">
                            <i class="bi bi-eye"></i>
                        </a>
                        @if(auth()->user()->esAdmin())
                        <a href="{{ route('cotizaciones.edit', $cot) }}" class="btn btn-sm btn-act btn-act-edit ms-1" title="Editar">
                            <i class="bi bi-pencil"></i>
                        </a>
                        @if(in_array($cot->estado, ['pendiente', 'aprobada']))
                        <a href="{{ route('cotizaciones.convertir', $cot) }}" class="btn btn-sm btn-act btn-act-conv ms-1" title="Convertir a Contrato">
                            <i class="bi bi-arrow-right-circle"></i>
                        </a>
                        @endif
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7">
                        <div class="empty-state" style="padding:40px">
                            <i class="bi bi-receipt"></i>
                            <p>No hay cotizaciones registradas</p>
                            @if(auth()->user()->esAdmin())
                            <button class="btn btn-primary btn-sm" onclick="abrirModal('modalNuevaCot')">
                                <i class="bi bi-plus-lg me-1"></i>Crear primera cotización
                            </button>
                            @endif
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($cotizaciones->hasPages())
    <div class="card-footer">{{ $cotizaciones->withQueryString()->links() }}</div>
    @endif
</div>


{{-- ============================================================
     MODAL NUEVA COTIZACIÓN — usa el sistema custom del layout
     ============================================================ --}}
@if(auth()->user()->esAdmin())
<div class="modal-backdrop" id="modalNuevaCot" onclick="if(event.target===this)cerrarModal('modalNuevaCot')">
<div class="modal-box" style="max-width:900px;width:95%">

    <div class="modal-header">
        <div class="d-flex align-items-center gap-3">
            <i class="bi bi-receipt" style="color:var(--primary);font-size:22px"></i>
            <div>
                <h5 class="mb-0">Nueva Cotización</h5>
                <div style="font-size:12px;opacity:.7;margin-top:2px">
                    Número asignado: <strong>{{ $numero }}</strong>
                </div>
            </div>
        </div>
        <button class="modal-close" onclick="cerrarModal('modalNuevaCot')">×</button>
    </div>

    <form action="{{ route('cotizaciones.store') }}" method="POST" id="formNuevaCot">
    @csrf
    <input type="hidden" name="numero" value="{{ $numero }}">

    <div class="modal-body" style="padding:0;max-height:70vh;overflow-y:auto">

        {{-- Sección 1 --}}
        <div class="cot-sec-title"><i class="bi bi-person-fill"></i> Datos del Cliente</div>
        <div style="padding:20px 28px;border-bottom:1px solid var(--border)">
            <div class="row g-3">
                <div class="col-12">
                    <label class="form-label fw-medium" style="font-size:13px">Empresa del sistema</label>
                    <input type="hidden" name="empresa_id" id="modal_empresa_id" value="">
                    <div style="position:relative">
                        <div id="modal-emp-chip" style="display:none;margin-bottom:6px;padding:7px 12px;background:#F0FDF4;border:1px solid #BBF7D0;border-radius:8px;display:none;align-items:center;gap:10px">
                            <i class="bi bi-building" style="color:#059669;font-size:15px;flex-shrink:0"></i>
                            <span id="modal-emp-chip-name" style="flex:1;font-size:13px;font-weight:600;color:#065F46"></span>
                            <span id="modal-emp-chip-ruc" style="font-size:11px;color:#64748B"></span>
                            <button type="button" onclick="empClear('modal')"
                                    style="background:none;border:none;cursor:pointer;color:#94A3B8;font-size:16px;line-height:1;padding:0">&times;</button>
                        </div>
                        <input type="text" id="modal-emp-search" autocomplete="off"
                               class="form-control form-control-sm"
                               placeholder="Buscar empresa por nombre, RUC o encargado..."
                               oninput="empSearch(this.value,'modal')"
                               onfocus="empSearch(this.value,'modal')">
                        <div id="modal-emp-drop" class="emp-drop" style="display:none"></div>
                    </div>
                </div>
                <div class="col-md-6">
                    <label class="form-label" style="font-size:13px">Nombre del contacto</label>
                    <input type="text" name="cliente_nombre" id="modal_cliente_nombre"
                        class="form-control form-control-sm" placeholder="Nombre completo">
                </div>
                <div class="col-md-6">
                    <label class="form-label" style="font-size:13px">Empresa / Negocio</label>
                    <input type="text" name="cliente_empresa" id="modal_cliente_empresa"
                        class="form-control form-control-sm" placeholder="Razón social">
                </div>
                <div class="col-md-6">
                    <label class="form-label" style="font-size:13px">Teléfono</label>
                    <input type="text" name="cliente_telefono" class="form-control form-control-sm" placeholder="999 999 999">
                </div>
                <div class="col-md-6">
                    <label class="form-label" style="font-size:13px">Email</label>
                    <input type="email" name="cliente_email" class="form-control form-control-sm" placeholder="correo@ejemplo.com">
                </div>
            </div>
        </div>

        {{-- Sección 2 --}}
        <div class="cot-sec-title"><i class="bi bi-file-invoice-dollar"></i> Datos de la Cotización</div>
        <div style="padding:20px 28px;border-bottom:1px solid var(--border)">
            <div class="row g-3">
                <div class="col-md-8">
                    <label class="form-label" style="font-size:13px">Tipo de servicio</label>
                    <input type="hidden" name="tipo_contrato" id="tipo_contrato_val">
                    <div style="position:relative">
                        <div id="tipo-tags-container" style="display:flex;flex-wrap:wrap;gap:5px;align-items:center;min-height:34px;padding:4px 8px;border:1px solid var(--border);border-radius:var(--radius-sm);background:#fff;cursor:text" onclick="document.getElementById('tipo-tag-input').focus()">
                            <input type="text" id="tipo-tag-input" autocomplete="off"
                                   style="border:none;outline:none;font-size:13px;flex:1;min-width:120px;padding:2px 0;background:transparent"
                                   placeholder="Escribe o elige tipo..."
                                   oninput="tipoTagFilter(this.value)" onkeydown="tipoTagKey(event)">
                        </div>
                        <div id="tipo-tag-drop" style="display:none;position:absolute;top:calc(100% + 4px);left:0;right:0;z-index:3100;background:#fff;border:1px solid #E2E8F0;border-radius:8px;box-shadow:0 8px 20px rgba(0,0,0,.1);max-height:180px;overflow-y:auto"></div>
                    </div>
                </div>
                <div class="col-md-4">
                    <label class="form-label" style="font-size:13px">Válida hasta</label>
                    <input type="date" name="fecha_vencimiento" class="form-control form-control-sm">
                </div>
                <div class="col-12">
                    <label class="form-label" style="font-size:13px">Notas / Descripción</label>
                    <textarea name="notas" class="form-control form-control-sm" rows="2"
                        placeholder="Detalles del servicio, condiciones..."></textarea>
                </div>
            </div>
        </div>

        {{-- Sección 3 --}}
        <div class="cot-sec-title"><i class="bi bi-geo-alt-fill"></i> Paneles de Interés</div>
        <div style="padding:20px 28px">

            <div class="mb-4">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <div style="font-weight:600;font-size:13px;display:flex;align-items:center;gap:6px">
                        <i class="bi bi-display" style="color:#2563EB"></i> Digital Outdoor
                        <span id="m-cnt-digital" style="background:#EFF6FF;color:#2563EB;padding:1px 8px;border-radius:20px;font-size:11px">0</span>
                    </div>
                    <button type="button" class="cot-add-btn" onclick="mAddPanel('digital')">
                        <i class="bi bi-plus-lg"></i> Agregar
                    </button>
                </div>
                <div id="m-cont-digital">
                    <div class="cot-empty" id="m-empty-digital">
                        <i class="bi bi-display me-1" style="opacity:.4"></i>Sin paneles digitales
                    </div>
                </div>
            </div>

            <div>
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <div style="font-weight:600;font-size:13px;display:flex;align-items:center;gap:6px">
                        <i class="bi bi-signpost-2" style="color:#EA580C"></i> Tradicional Outdoor
                        <span id="m-cnt-tradicional" style="background:#FFF7ED;color:#EA580C;padding:1px 8px;border-radius:20px;font-size:11px">0</span>
                    </div>
                    <button type="button" class="cot-add-btn" onclick="mAddPanel('tradicional')">
                        <i class="bi bi-plus-lg"></i> Agregar
                    </button>
                </div>
                <div id="m-cont-tradicional">
                    <div class="cot-empty" id="m-empty-tradicional">
                        <i class="bi bi-signpost-2 me-1" style="opacity:.4"></i>Sin paneles tradicionales
                    </div>
                </div>
            </div>

        </div>

        {{-- Sección 4: Servicios adicionales --}}
        <div class="cot-sec-title">
            <i class="bi bi-box-seam"></i> Servicios Adicionales
            <button type="button" id="btn-quick-srv"
                    style="margin-left:auto;background:#059669;color:#fff;border:none;padding:4px 12px;border-radius:6px;font-size:11px;font-weight:700;cursor:pointer;display:flex;align-items:center;gap:5px">
                <i class="bi bi-plus-lg"></i> Nuevo servicio
            </button>
        </div>
        <div style="padding:16px 28px">
            <div id="m-srv-list" style="margin-bottom:10px">
                <div class="cot-empty" id="m-empty-srv">
                    <i class="bi bi-box-seam me-1" style="opacity:.4"></i>Sin servicios agregados
                </div>
            </div>
            {{-- Buscador de servicios --}}
            <div style="position:relative">
                <input type="text" id="srv-search-input" autocomplete="off"
                       class="form-control form-control-sm"
                       placeholder="Buscar servicio para agregar..."
                       oninput="srvSearch(this.value)" onfocus="srvSearch(this.value)">
                <div id="srv-search-drop" style="display:none;position:absolute;top:calc(100% + 4px);left:0;right:0;z-index:3100;background:#fff;border:1px solid #E2E8F0;border-radius:8px;box-shadow:0 8px 20px rgba(0,0,0,.1);max-height:200px;overflow-y:auto"></div>
            </div>
        </div>

    </div>{{-- /modal-body --}}

    {{-- Resumen IGV --}}
    <div style="padding:10px 28px;background:#F8FAFC;border-top:1px solid #E2E8F0">
        <div style="display:flex;justify-content:flex-end">
            <table style="font-size:12px;min-width:240px">
                <tr>
                    <td style="padding:3px 12px 3px 0;color:#64748B">Subtotal neto</td>
                    <td style="font-weight:600;text-align:right">S/. <span id="m-sub">0.00</span></td>
                </tr>
                <tr>
                    <td style="padding:3px 12px 3px 0;color:#64748B">IGV (18%)</td>
                    <td style="font-weight:600;text-align:right">S/. <span id="m-igv">0.00</span></td>
                </tr>
                <tr style="border-top:2px solid #E2E8F0">
                    <td style="padding:6px 12px 0 0;font-weight:700;font-size:13px">TOTAL CON IGV</td>
                    <td style="font-weight:800;text-align:right;font-size:13px;color:#059669;padding-top:6px">S/. <span id="m-total">0.00</span></td>
                </tr>
            </table>
        </div>
    </div>

    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" onclick="cerrarModal('modalNuevaCot')">
            <i class="bi bi-x-lg me-1"></i>Cancelar
        </button>
        <button type="submit" class="btn btn-primary">
            <i class="bi bi-check-lg me-1"></i>Guardar Cotización
        </button>
    </div>

    </form>
</div>
</div>
@endif

{{-- Modal: Crear servicio rápido ────────────────────────────── --}}
@if(auth()->user()->esAdmin())
<div id="modal-quick-srv"
     style="display:none;position:fixed;inset:0;z-index:4000;background:rgba(0,0,0,.6);
            align-items:center;justify-content:center;padding:16px"
     onclick="if(event.target===this)cerrarQuickSrv()">
    <div style="background:#fff;border-radius:14px;width:100%;max-width:420px;box-shadow:0 24px 60px rgba(0,0,0,.35)">
        <div style="display:flex;align-items:center;justify-content:space-between;padding:14px 20px;border-bottom:1px solid #E2E8F0">
            <span style="font-weight:700;font-size:15px;color:#0F172A">
                <i class="bi bi-box-seam" style="color:#059669;margin-right:6px"></i>Nuevo servicio
            </span>
            <button type="button" onclick="cerrarQuickSrv()"
                    style="background:none;border:none;font-size:22px;cursor:pointer;color:#94A3B8;line-height:1">&times;</button>
        </div>
        <div style="padding:20px">
            <div class="row g-3">
                <div class="col-12">
                    <label class="form-label" style="font-size:13px">Nombre <span style="color:var(--primary)">*</span></label>
                    <input type="text" id="qs-nombre" class="form-control form-control-sm" placeholder="Ej: Diseño gráfico, Producción de video...">
                </div>
                <div class="col-md-6">
                    <label class="form-label" style="font-size:13px">Monto base (S/.)</label>
                    <input type="number" id="qs-monto" class="form-control form-control-sm" placeholder="0.00" min="0" step="0.01">
                </div>
                <div class="col-md-6">
                    <label class="form-label" style="font-size:13px">Ícono Bootstrap</label>
                    <input type="text" id="qs-icono" class="form-control form-control-sm" placeholder="star, camera, megaphone..." value="box">
                </div>
                <div class="col-12">
                    <label class="form-label" style="font-size:13px">Descripción <span style="color:var(--text-light);font-weight:400">(opcional)</span></label>
                    <textarea id="qs-desc" class="form-control form-control-sm" rows="2" placeholder="Detalles del servicio..."></textarea>
                </div>
            </div>
            <div id="qs-error" style="display:none;color:var(--primary);font-size:12px;margin-top:10px;padding:7px 12px;background:#FEF2F2;border-radius:6px"></div>
            <div style="display:flex;gap:10px;justify-content:flex-end;margin-top:16px">
                <button type="button" onclick="cerrarQuickSrv()" class="btn btn-secondary btn-sm">Cancelar</button>
                <button type="button" id="btn-qs-guardar"
                        style="background:#059669;color:#fff;border:none;padding:8px 18px;border-radius:8px;font-size:13px;font-weight:700;cursor:pointer">
                    <i class="bi bi-floppy"></i> Guardar y agregar
                </button>
            </div>
        </div>
    </div>
</div>
@endif

{{-- Modal: Preview de panel — z-index > modal-backdrop (2000) ── --}}
<div id="modal-panel-preview"
     style="display:none;position:fixed;inset:0;z-index:3000;background:rgba(0,0,0,.55);
            align-items:center;justify-content:center;padding:16px"
     onclick="if(event.target===this){this.style.display='none';document.body.style.overflow=''}">
    <div style="background:#fff;border-radius:14px;width:100%;max-width:380px;
                box-shadow:0 24px 60px rgba(0,0,0,.35);overflow:hidden">
        <div style="display:flex;align-items:center;justify-content:space-between;
                    padding:14px 18px;border-bottom:1px solid #E2E8F0">
            <span style="font-weight:700;font-size:14px;color:#0F172A">
                <i class="bi bi-display" style="color:var(--primary);margin-right:6px"></i>Vista previa del panel
            </span>
            <button type="button"
                    onclick="document.getElementById('modal-panel-preview').style.display='none';document.body.style.overflow=''"
                    style="background:none;border:none;font-size:22px;cursor:pointer;color:#94A3B8;line-height:1">&times;</button>
        </div>
        <div id="mpp-body" style="padding:16px 18px 18px;max-height:70vh;overflow-y:auto"></div>
    </div>
</div>

@endsection

@push('styles')
<style>
/* ── Status filter pills ─────────────────────────── */
.status-pill {
    display: inline-flex; align-items: center; border-radius: 20px;
    font-size: 11.5px; padding: 5px 14px; font-weight: 600;
    border: 1.5px solid transparent; cursor: pointer; text-decoration: none;
    transition: all .18s ease;
}
.status-pill:hover { transform: translateY(-1px); text-decoration: none; }
.status-pill.sp-all          { background:#F1F5F9; color:#64748B; border-color:#E2E8F0; }
.status-pill.sp-all.active   { background:#334155; color:#fff; border-color:#334155; box-shadow:0 4px 10px rgba(51,65,85,.25); }
.status-pill.sp-pen          { background:#FFFBEB; color:#92400E; border-color:#FDE68A; }
.status-pill.sp-pen.active   { background:#D97706; color:#fff; border-color:#B45309; box-shadow:0 4px 10px rgba(217,119,6,.3); }
.status-pill.sp-apr          { background:#F0FDF4; color:#166534; border-color:#BBF7D0; }
.status-pill.sp-apr.active   { background:#16A34A; color:#fff; border-color:#15803D; box-shadow:0 4px 10px rgba(22,163,74,.3); }
.status-pill.sp-rec          { background:#FFF1F2; color:#9F1239; border-color:#FECDD3; }
.status-pill.sp-rec.active   { background:#E11D48; color:#fff; border-color:#BE123C; box-shadow:0 4px 10px rgba(225,29,72,.3); }
.status-pill.sp-con          { background:#EFF6FF; color:#1E40AF; border-color:#BFDBFE; }
.status-pill.sp-con.active   { background:#2563EB; color:#fff; border-color:#1D4ED8; box-shadow:0 4px 10px rgba(37,99,235,.3); }
/* ── Colored action buttons ──────────────────────── */
.btn-act { border-radius: var(--radius-sm); font-size: 12px; padding: 5px 10px; border-width: 1.5px; border-style: solid; transition: all .18s ease; display: inline-flex; align-items: center; justify-content: center; }
.btn-act:hover { transform: translateY(-1px); text-decoration: none; }
.btn-act-view { color:#2563EB; border-color:#BFDBFE; background:rgba(219,234,254,.45); }
.btn-act-view:hover { background:#DBEAFE; border-color:#93C5FD; color:#1D4ED8; box-shadow:0 4px 12px rgba(37,99,235,.18); }
.btn-act-edit { color:#D97706; border-color:#FDE68A; background:rgba(254,243,199,.45); }
.btn-act-edit:hover { background:#FEF3C7; border-color:#FCD34D; color:#B45309; box-shadow:0 4px 12px rgba(217,119,6,.18); }
.btn-act-conv { color:#059669; border-color:#A7F3D0; background:rgba(209,250,229,.45); }
.btn-act-conv:hover { background:#D1FAE5; border-color:#6EE7B7; color:#047857; box-shadow:0 4px 12px rgba(5,150,105,.18); }
/* ── Modal section titles ────────────────────────── */
.cot-sec-title {
    display: flex;
    align-items: center;
    gap: 8px;
    font-weight: 700;
    font-size: 11.5px;
    text-transform: uppercase;
    letter-spacing: .8px;
    color: var(--primary-dark);
    padding: 12px 28px;
    background: linear-gradient(90deg, rgba(230,57,70,0.06), rgba(255,255,255,0.7));
    border-left: 4px solid var(--primary);
    border-bottom: 1px solid rgba(226,232,240,.6);
}
.cot-add-btn {
    display: inline-flex; align-items: center; gap: 6px;
    padding: 6px 14px; border: 1.5px solid var(--primary);
    color: var(--primary); background: transparent;
    border-radius: var(--radius-sm); font-size: 12px; font-weight: 600; cursor: pointer;
    transition: all .2s ease;
}
.cot-add-btn:hover {
    background: var(--primary-lighter);
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(230,57,70,.12);
}
.cot-empty {
    padding: 16px; text-align: center; font-size: 13px;
    color: var(--text-light); border: 1px dashed var(--border); border-radius: var(--radius-md);
    background: rgba(255,255,255,.3);
}
.cot-panel-row {
    display: flex; align-items: center; gap: 8px; flex-wrap: wrap;
    padding: 10px 14px; background: rgba(255,255,255,.6);
    border: 1px solid rgba(226,232,240,.8); border-radius: var(--radius-md); margin-bottom: 8px;
    transition: all .2s ease;
    backdrop-filter: blur(4px);
}
.cot-panel-row:hover {
    border-color: rgba(230,57,70,.25);
    box-shadow: var(--shadow-sm);
}
.cot-panel-row .f-cod  { width: 76px; flex-shrink: 0; }
.cot-panel-row .f-mes  { width: 70px; flex-shrink: 0; }
.cot-panel-row .f-pre  { width: 96px; flex-shrink: 0; }
.cot-panel-row .f-desc { flex: 2; min-width: 150px; }

/* ── Empresa search ───────────────────────── */
.emp-drop {
    position: absolute; top: calc(100% + 4px); left: 0; right: 0;
    background: #fff; border: 1px solid #E2E8F0; border-radius: 10px;
    box-shadow: 0 8px 24px rgba(0,0,0,.12); z-index: 3100;
    max-height: 260px; overflow-y: auto;
}
.emp-drop-item {
    padding: 10px 14px; cursor: pointer;
    border-bottom: 1px solid #F8FAFC; transition: background .12s;
}
.emp-drop-item:last-child { border-bottom: none; }
.emp-drop-item:hover { background: #F0FDF4; }

/* ── Panel search ──────────────────────────── */
.panel-search-input {
    border-radius: var(--radius-sm) !important;
    background: #fff;
}
.panel-search-input:focus { border-color: var(--primary) !important; box-shadow: 0 0 0 3px rgba(230,57,70,.12) !important; }
.panel-dropdown {
    position: absolute; top: calc(100% + 4px); left: 0; right: 0;
    background: #fff; border: 1px solid #E2E8F0; border-radius: 10px;
    box-shadow: 0 8px 24px rgba(0,0,0,.12); z-index: 999;
    max-height: 240px; overflow-y: auto;
}
.panel-drop-item {
    padding: 9px 14px; cursor: pointer; font-size: 13px;
    border-bottom: 1px solid #F8FAFC; transition: background .12s;
    display: flex; align-items: flex-start; gap: 8px; flex-wrap: wrap;
}
.panel-drop-item:last-child { border-bottom: none; }
.panel-drop-item:hover { background: #FFF0F1; }
.panel-drop-badge {
    font-size: 9px; font-weight: 700; padding: 2px 7px;
    border-radius: 20px; flex-shrink: 0; margin-top: 2px;
}
.panel-drop-badge-digital     { background: #DBEAFE; color: #1D4ED8; }
.panel-drop-badge-tradicional { background: #FEF3C7; color: #92400E; }

/* ── Preview eye button ────────────────────── */
.btn-panel-preview {
    width: 32px; height: 32px; padding: 0;
    border: 1.5px solid #E2E8F0; background: #F8FAFC;
    color: #94A3B8; border-radius: var(--radius-sm);
    display: inline-flex; align-items: center; justify-content: center;
    transition: all .18s ease; flex-shrink: 0;
}
.btn-panel-preview:disabled { opacity: .4; cursor: not-allowed; }
.btn-panel-preview.active, .btn-panel-preview:not(:disabled):hover {
    border-color: #2563EB; background: #EFF6FF; color: #2563EB;
    box-shadow: 0 4px 10px rgba(37,99,235,.15);
}
</style>
@endpush

@push('scripts')
<script>
function abrirModal(id) {
    document.getElementById(id).classList.add('open');
    document.body.style.overflow = 'hidden';
}
function cerrarModal(id) {
    document.getElementById(id).classList.remove('open');
    document.body.style.overflow = '';
}

@php
$_mPanelesDigital = $paneles_digitales->map(function($p) {
    return [
        'id'     => $p->id,
        'codigo' => $p->codigo ?? '',
        'nombre' => $p->nombre ?? '',
        'costo'  => $p->costo_produccion ?? 0,
        'desc'   => $p->desc_costo ?? 'Instalación y puesta en marcha',
        'dir'    => $p->direccion ?? '',
        'medidas'=> $p->medidas ?? '',
        'foto'   => $p->foto ? Storage::url($p->foto) : null,
        'tipo'   => 'LED',
    ];
})->values();
$_mPanelesTradicional = $paneles_tradicionales->map(function($p) {
    return [
        'id'     => $p->id,
        'codigo' => $p->codigo ?? '',
        'nombre' => $p->nombre ?? '',
        'costo'  => $p->costo_produccion ?? 0,
        'desc'   => $p->desc_costo ?? 'Producción de lona e instalación',
        'dir'    => $p->direccion ?? '',
        'medidas'=> $p->medidas ?? '',
        'caras'  => $p->caras ?? '',
        'gramaje'=> $p->gramaje_lonas ?? '',
        'foto'   => $p->foto ? Storage::url($p->foto) : null,
        'tipo'   => 'BANNER',
    ];
})->values();
@endphp
var mPaneles = {
    digital:     @json($_mPanelesDigital),
    tradicional: @json($_mPanelesTradicional)
};
var mCounters = { digital: 0, tradicional: 0 };
var M_IGV = 0.18;

function mRecalc() {
    var sub = 0;
    document.querySelectorAll('#modalNuevaCot input[name="elemento_precio[]"]').forEach(function(i){ sub += parseFloat(i.value)||0; });
    document.querySelectorAll('#modalNuevaCot input[name="elemento_costo[]"]').forEach(function(i){ sub += parseFloat(i.value)||0; });
    var igv = sub * M_IGV;
    document.getElementById('m-sub').textContent    = sub.toFixed(2);
    document.getElementById('m-igv').textContent    = igv.toFixed(2);
    document.getElementById('m-total').textContent  = (sub + igv).toFixed(2);
}

function mUpdateCount(tipo) {
    document.getElementById('m-cnt-' + tipo).textContent =
        document.getElementById('m-cont-' + tipo).querySelectorAll('.cot-panel-row').length;
}

// ── Búsqueda dinámica de paneles ──────────────────────────────
var mSelectedPanel = {}; // rowId → panel object

function mAddPanel(tipo) {
    var cont  = document.getElementById('m-cont-' + tipo);
    var empty = document.getElementById('m-empty-' + tipo);
    if (empty) empty.style.display = 'none';
    var idx     = mCounters[tipo]++;
    var rowId   = 'm-row-'+tipo+'-'+idx;
    var descDef = tipo === 'tradicional' ? 'Producción de lona e instalación' : 'Instalación y puesta en marcha';

    var row = document.createElement('div');
    row.className = 'cot-panel-row flex-wrap';
    row.id = rowId;
    row.innerHTML = `
        <div class="panel-search-wrap" style="flex:3;min-width:220px;position:relative">
            <input type="hidden" name="elemento_panel_id[]" id="${rowId}-pid" value="">
            <input type="hidden" name="elemento_tipo[]" value="${tipo}">
            <input type="text" class="form-control form-control-sm panel-search-input"
                   id="${rowId}-search" autocomplete="off"
                   placeholder="Buscar panel por nombre o código..."
                   oninput="mSearch(this,'${rowId}','${tipo}')"
                   onfocus="mSearch(this,'${rowId}','${tipo}')">
            <div class="panel-dropdown" id="${rowId}-drop" style="display:none"></div>
        </div>
        <input type="text"   name="elemento_codigo[]" id="${rowId}-cod" class="form-control form-control-sm f-cod" placeholder="Código" readonly>
        <input type="number" name="elemento_tiempo[]" class="form-control form-control-sm f-mes" placeholder="Meses" min="1" oninput="mRecalc()">
        <input type="number" name="elemento_precio[]" class="form-control form-control-sm f-pre" placeholder="S/. Precio" min="0" step="0.01" oninput="mRecalc()">
        <input type="number" name="elemento_costo[]"  id="${rowId}-costo" class="form-control form-control-sm f-pre" placeholder="S/. Costo" min="0" step="0.01" oninput="mRecalc()">
        <input type="text"   name="elemento_desc_costo[]" id="${rowId}-desc" class="form-control form-control-sm f-desc" placeholder="Desc. costo" value="${esc2(descDef)}">
        <button type="button" class="btn btn-sm btn-panel-preview" id="${rowId}-eye"
                title="Vista previa del panel" disabled onclick="mPreview('${rowId}')">
            <i class="bi bi-eye"></i>
        </button>
        <button type="button" class="btn btn-sm btn-outline-danger flex-shrink-0"
                onclick="mRemovePanel('${tipo}',${idx})">
            <i class="bi bi-trash"></i>
        </button>`;
    cont.appendChild(row);
    mUpdateCount(tipo);

    // Cerrar dropdown al hacer clic afuera
    document.addEventListener('click', function handler(e) {
        if (!row.contains(e.target)) {
            var drop = document.getElementById(rowId+'-drop');
            if (drop) drop.style.display = 'none';
        }
    });
}

function mSearch(input, rowId, tipo) {
    var q    = input.value.toLowerCase().trim();
    var drop = document.getElementById(rowId+'-drop');
    var list = mPaneles[tipo];
    var filtered = q
        ? list.filter(function(p){ return (p.codigo+' '+p.nombre).toLowerCase().includes(q); })
        : list.slice(0, 12);

    if (filtered.length === 0) {
        drop.innerHTML = '<div style="padding:10px 14px;font-size:12px;color:#94A3B8">Sin resultados</div>';
    } else {
        drop.innerHTML = filtered.map(function(p) {
            var label = (p.codigo ? '<strong>'+esc2(p.codigo)+'</strong> — ' : '') + esc2(p.nombre);
            var sub   = p.dir ? '<br><span style="font-size:10px;color:#94A3B8">'+esc2(p.dir)+'</span>' : '';
            return '<div class="panel-drop-item" data-id="'+p.id+'" onclick="mSelectPanel(\''+rowId+'\',\''+tipo+'\','+p.id+')">'+
                   '<span class="panel-drop-badge panel-drop-badge-'+tipo+'">'+p.tipo+'</span>'+
                   label + sub + '</div>';
        }).join('');
    }
    drop.style.display = 'block';
}

function mSelectPanel(rowId, tipo, panelId) {
    var p = mPaneles[tipo].find(function(x){ return x.id === panelId; });
    if (!p) return;

    mSelectedPanel[rowId] = p;

    document.getElementById(rowId+'-pid').value   = p.id;
    document.getElementById(rowId+'-search').value = (p.codigo ? p.codigo+' — ' : '') + p.nombre;
    document.getElementById(rowId+'-cod').value   = p.codigo || '';
    document.getElementById(rowId+'-costo').value = parseFloat(p.costo||0).toFixed(2);
    document.getElementById(rowId+'-desc').value  = p.desc || '';
    document.getElementById(rowId+'-drop').style.display = 'none';

    var eye = document.getElementById(rowId+'-eye');
    if (eye) { eye.disabled = false; eye.classList.add('active'); }

    mRecalc();
}

function mPreview(rowId) {
    var p = mSelectedPanel[rowId];
    if (!p) return;
    var modal = document.getElementById('modal-panel-preview');
    var body  = document.getElementById('mpp-body');

    var fotoHtml = p.foto
        ? '<img src="'+p.foto+'" style="width:100%;height:180px;object-fit:cover;display:block">'
        : '<div style="height:100px;background:linear-gradient(135deg,#E63946,#C1121F);display:flex;align-items:center;justify-content:center"><i class="bi bi-display" style="font-size:40px;color:rgba(255,255,255,.4)"></i></div>';

    body.innerHTML = `
        <div style="border-radius:10px;overflow:hidden;border:1px solid #E2E8F0;margin-bottom:14px">${fotoHtml}</div>
        <div style="display:flex;align-items:center;gap:8px;margin-bottom:10px">
            <span style="background:${p.tipo==='LED'?'#DBEAFE':'#FEF3C7'};color:${p.tipo==='LED'?'#1D4ED8':'#92400E'};
                  padding:2px 10px;border-radius:20px;font-size:11px;font-weight:700">${p.tipo}</span>
            ${p.codigo ? '<code style="background:#F1F5F9;padding:2px 8px;border-radius:4px;font-size:12px">'+esc2(p.codigo)+'</code>' : ''}
        </div>
        <div style="font-size:16px;font-weight:700;color:#0F172A;margin-bottom:6px">${esc2(p.nombre)}</div>
        ${p.dir ? '<div style="font-size:12px;color:#64748B;margin-bottom:8px"><i class="bi bi-geo-alt-fill" style="color:#E63946;margin-right:4px"></i>'+esc2(p.dir)+'</div>' : ''}
        <div style="display:flex;gap:8px;flex-wrap:wrap;margin-bottom:10px">
            ${p.medidas ? '<span style="background:#F1F5F9;color:#334155;padding:3px 10px;border-radius:6px;font-size:12px;font-weight:600"><i class="bi bi-rulers me-1"></i>'+esc2(p.medidas)+'</span>' : ''}
            ${p.caras   ? '<span style="background:#F1F5F9;color:#334155;padding:3px 10px;border-radius:6px;font-size:12px"><i class="bi bi-layout-split me-1"></i>'+p.caras+' cara(s)</span>' : ''}
            ${p.gramaje  ? '<span style="background:#F1F5F9;color:#334155;padding:3px 10px;border-radius:6px;font-size:12px">'+esc2(p.gramaje)+'</span>' : ''}
        </div>
        ${p.costo > 0 ? '<div style="background:#FEF3C7;border-radius:8px;padding:8px 12px;font-size:12px;color:#92400E"><strong>Costo: S/. '+parseFloat(p.costo).toFixed(2)+'</strong>'+(p.desc ? ' · '+esc2(p.desc) : '')+'</div>' : ''}`;

    modal.style.display = 'flex';
    document.body.style.overflow = 'hidden';
}

function mRemovePanel(tipo, idx) {
    var rowId = 'm-row-'+tipo+'-'+idx;
    var row   = document.getElementById(rowId);
    if (row) row.remove();
    delete mSelectedPanel[rowId];
    var cont = document.getElementById('m-cont-'+tipo);
    if (!cont.querySelector('.cot-panel-row'))
        document.getElementById('m-empty-'+tipo).style.display = '';
    mUpdateCount(tipo);
    mRecalc();
}

function esc2(s) { return String(s||'').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;'); }

// ── Búsqueda dinámica de empresa ─────────────────────────────
@php
$_mEmpresas = $empresas->map(fn($e) => [
    'id'       => $e->id,
    'nombre'   => $e->nombre,
    'ruc'      => $e->ruc ?? '',
    'encargado'=> $e->encargado ?? '',
    'celular'  => $e->celular ?? '',
    'correo'   => $e->correo ?? '',
])->values();
@endphp
var mEmpresas = @json($_mEmpresas);

function empSearch(q, ctx) {
    var drop  = document.getElementById(ctx+'-emp-drop');
    var lower = q.toLowerCase().trim();
    var list  = lower
        ? mEmpresas.filter(function(e){
            return e.nombre.toLowerCase().includes(lower)
                || e.ruc.toLowerCase().includes(lower)
                || e.encargado.toLowerCase().includes(lower);
          })
        : mEmpresas.slice(0, 12);

    if (list.length === 0) {
        drop.innerHTML = '<div style="padding:10px 14px;font-size:12px;color:#94A3B8">Sin empresas encontradas</div>';
    } else {
        drop.innerHTML = list.map(function(e){
            var sub = [e.ruc ? 'RUC: '+e.ruc : '', e.encargado ? e.encargado : ''].filter(Boolean).join(' · ');
            return '<div class="emp-drop-item" onclick="empSelect('+e.id+',\''+ctx+'\')">'+
                '<div style="font-weight:600;font-size:13px;color:#0F172A">'+esc2(e.nombre)+'</div>'+
                (sub ? '<div style="font-size:11px;color:#94A3B8;margin-top:1px">'+esc2(sub)+'</div>' : '')+
                '</div>';
        }).join('');
    }
    drop.style.display = 'block';
}

function empSelect(id, ctx) {
    var e = mEmpresas.find(function(x){ return x.id === id; });
    if (!e) return;

    document.getElementById(ctx+'_empresa_id').value = e.id;
    document.getElementById(ctx+'-emp-search').value = '';
    document.getElementById(ctx+'-emp-drop').style.display = 'none';

    // Mostrar chip
    var chip = document.getElementById(ctx+'-emp-chip');
    chip.style.display = 'flex';
    document.getElementById(ctx+'-emp-chip-name').textContent = e.nombre;
    document.getElementById(ctx+'-emp-chip-ruc').textContent  = e.ruc ? 'RUC: '+e.ruc : '';

    // Autocompletar campos del cliente
    var pfx = ctx === 'modal' ? 'modal_cliente' : 'cliente';
    var n = document.getElementById(pfx+'_nombre');
    var c = document.getElementById(pfx+'_empresa');
    var t = document.getElementById(pfx+'_telefono');
    var m = document.getElementById(pfx+'_email');
    if (n && !n.value) n.value = e.encargado || '';
    if (c && !c.value) c.value = e.nombre    || '';
    if (t && !t.value) t.value = e.celular   || '';
    if (m && !m.value) m.value = e.correo    || '';
}

function empClear(ctx) {
    document.getElementById(ctx+'_empresa_id').value   = '';
    document.getElementById(ctx+'-emp-chip').style.display = 'none';
    document.getElementById(ctx+'-emp-search').value   = '';
}

document.addEventListener('click', function(e) {
    ['modal'].forEach(function(ctx) {
        var drop   = document.getElementById(ctx+'-emp-drop');
        var input  = document.getElementById(ctx+'-emp-search');
        if (drop && input && !input.contains(e.target) && !drop.contains(e.target))
            drop.style.display = 'none';
    });
});

// ── Multi-tag: Tipo de servicio ───────────────────────────────
var tipoOpciones = ['Panel Digital','Panel Tradicional','Marketing Digital','Redes Sociales','Diseño Gráfico','Producción de Video','Mixto'];
var tipoSeleccionados = [];

function tipoTagFilter(q) {
    var drop  = document.getElementById('tipo-tag-drop');
    var lower = q.toLowerCase().trim();
    var opts  = lower
        ? tipoOpciones.filter(function(o){ return o.toLowerCase().includes(lower) && !tipoSeleccionados.includes(o); })
        : tipoOpciones.filter(function(o){ return !tipoSeleccionados.includes(o); });
    if (opts.length === 0 && lower) {
        drop.innerHTML = '<div style="padding:8px 14px;font-size:12px;color:#64748B">Presiona Enter para agregar "<strong>'+esc2(q)+'</strong>"</div>';
    } else if (opts.length === 0) {
        drop.style.display = 'none'; return;
    } else {
        drop.innerHTML = opts.map(function(o){
            return '<div class="tipo-drop-item" onclick="tipoAddTag(\''+esc2(o)+'\')" style="padding:8px 14px;cursor:pointer;font-size:13px;border-bottom:1px solid #F8FAFC;transition:background .12s"onmouseover="this.style.background=\'#FFF0F1\'" onmouseout="this.style.background=\'\'">'+esc2(o)+'</div>';
        }).join('');
    }
    drop.style.display = 'block';
}

function tipoTagKey(e) {
    var input = document.getElementById('tipo-tag-input');
    if (e.key === 'Enter' || e.key === ',') {
        e.preventDefault();
        var val = input.value.trim().replace(/,$/, '');
        if (val) tipoAddTag(val);
    } else if (e.key === 'Backspace' && input.value === '' && tipoSeleccionados.length) {
        tipoRemoveTag(tipoSeleccionados[tipoSeleccionados.length - 1]);
    }
}

function tipoAddTag(val) {
    if (!val || tipoSeleccionados.includes(val)) return;
    tipoSeleccionados.push(val);
    tipoRender();
    var input = document.getElementById('tipo-tag-input');
    input.value = '';
    document.getElementById('tipo-tag-drop').style.display = 'none';
}

function tipoRemoveTag(val) {
    tipoSeleccionados = tipoSeleccionados.filter(function(t){ return t !== val; });
    tipoRender();
}

function tipoRender() {
    var container = document.getElementById('tipo-tags-container');
    var input = document.getElementById('tipo-tag-input');
    // Quitar chips existentes
    container.querySelectorAll('.tipo-chip').forEach(function(c){ c.remove(); });
    // Insertar chips antes del input
    tipoSeleccionados.forEach(function(val) {
        var chip = document.createElement('span');
        chip.className = 'tipo-chip';
        chip.style.cssText = 'display:inline-flex;align-items:center;gap:5px;background:#FFF0F1;color:var(--primary);border:1px solid #FECACA;border-radius:20px;padding:2px 10px;font-size:12px;font-weight:600';
        chip.innerHTML = esc2(val) + '<button type="button" onclick="tipoRemoveTag(\''+esc2(val)+'\')" style="background:none;border:none;cursor:pointer;color:var(--primary);font-size:14px;line-height:1;padding:0">&times;</button>';
        container.insertBefore(chip, input);
    });
    document.getElementById('tipo_contrato_val').value = tipoSeleccionados.join(', ');
}

document.addEventListener('click', function(e) {
    var drop = document.getElementById('tipo-tag-drop');
    var cont = document.getElementById('tipo-tags-container');
    if (drop && !cont.contains(e.target) && !drop.contains(e.target)) drop.style.display = 'none';
});

// ── Servicios adicionales ─────────────────────────────────────
@php
$_mServicios = $servicios->map(fn($s) => ['id'=>$s->id,'nombre'=>$s->nombre,'monto'=>(float)$s->monto,'icono'=>$s->icono??'box'])->values();
@endphp
var mServicios = @json($_mServicios);
var srvCounter = 0;

function srvSearch(q) {
    var drop  = document.getElementById('srv-search-drop');
    var lower = q.toLowerCase().trim();
    var list  = lower
        ? mServicios.filter(function(s){ return s.nombre.toLowerCase().includes(lower); })
        : mServicios.slice(0, 10);
    if (list.length === 0) { drop.innerHTML = '<div style="padding:10px 14px;font-size:12px;color:#94A3B8">Sin resultados</div>'; }
    else {
        drop.innerHTML = list.map(function(s){
            return '<div onclick="srvAdd('+s.id+')" style="padding:9px 14px;cursor:pointer;font-size:13px;border-bottom:1px solid #F8FAFC;display:flex;align-items:center;gap:8px;transition:background .12s" onmouseover="this.style.background=\'#F0FDF4\'" onmouseout="this.style.background=\'\'">'+
                '<i class="bi bi-'+esc2(s.icono)+'" style="color:#059669;font-size:15px;width:18px;flex-shrink:0"></i>'+
                '<span style="flex:1">'+esc2(s.nombre)+'</span>'+
                '<span style="color:#059669;font-weight:700;font-size:12px">S/. '+parseFloat(s.monto).toFixed(2)+'</span>'+
                '</div>';
        }).join('');
    }
    drop.style.display = 'block';
}

function srvAdd(id) {
    var s = mServicios.find(function(x){ return x.id === id; });
    if (!s) return;
    document.getElementById('srv-search-drop').style.display = 'none';
    document.getElementById('srv-search-input').value = '';
    srvAddRow(s);
}

function srvAddRow(s) {
    var cont  = document.getElementById('m-srv-list');
    var empty = document.getElementById('m-empty-srv');
    if (empty) empty.style.display = 'none';
    var j = srvCounter++;
    var row = document.createElement('div');
    row.id = 'srv-row-'+j;
    row.style.cssText = 'display:flex;align-items:center;gap:8px;flex-wrap:wrap;padding:9px 12px;background:rgba(255,255,255,.6);border:1px solid rgba(226,232,240,.8);border-radius:10px;margin-bottom:8px';
    row.innerHTML = `
        <input type="hidden" name="srv_id[]"      value="${s.id}">
        <input type="hidden" name="srv_subtipo[]" value="">
        <div style="display:flex;align-items:center;gap:7px;flex:2;min-width:160px">
            <i class="bi bi-${esc2(s.icono)}" style="color:#059669;font-size:15px;flex-shrink:0"></i>
            <span style="font-size:13px;font-weight:600;color:#0F172A">${esc2(s.nombre)}</span>
        </div>
        <input type="number" name="srv_precio[]" value="${parseFloat(s.monto).toFixed(2)}"
               class="form-control form-control-sm" style="width:100px" placeholder="S/. Precio" min="0" step="0.01" oninput="mRecalc()">
        <input type="text" name="srv_obs[]" class="form-control form-control-sm" style="flex:2;min-width:140px" placeholder="Observación (opcional)">
        <button type="button" onclick="srvRemove(${j})"
                class="btn btn-sm btn-outline-danger flex-shrink-0" style="padding:4px 8px">
            <i class="bi bi-trash"></i>
        </button>`;
    cont.appendChild(row);
    mRecalc();
}

function srvRemove(j) {
    var row = document.getElementById('srv-row-'+j);
    if (row) row.remove();
    var cont = document.getElementById('m-srv-list');
    if (!cont.querySelector('[id^="srv-row-"]'))
        document.getElementById('m-empty-srv').style.display = '';
    mRecalc();
}

document.addEventListener('click', function(e) {
    var drop  = document.getElementById('srv-search-drop');
    var input = document.getElementById('srv-search-input');
    if (drop && input && !input.contains(e.target) && !drop.contains(e.target))
        drop.style.display = 'none';
});

// ── Quick-add servicio ────────────────────────────────────────
var _quickSrvUrl  = '{{ route("servicios.quick") }}';
var _csrfToken2   = document.querySelector('meta[name="csrf-token"]').content;

var btnQSrv = document.getElementById('btn-quick-srv');
if (btnQSrv) btnQSrv.addEventListener('click', function(){
    document.getElementById('qs-nombre').value = '';
    document.getElementById('qs-monto').value  = '';
    document.getElementById('qs-icono').value  = 'box';
    document.getElementById('qs-desc').value   = '';
    document.getElementById('qs-error').style.display = 'none';
    document.getElementById('modal-quick-srv').style.display = 'flex';
});

function cerrarQuickSrv() {
    document.getElementById('modal-quick-srv').style.display = 'none';
}

var btnQsGuardar = document.getElementById('btn-qs-guardar');
if (btnQsGuardar) btnQsGuardar.addEventListener('click', async function(){
    var nombre = document.getElementById('qs-nombre').value.trim();
    var monto  = document.getElementById('qs-monto').value  || '0';
    var icono  = document.getElementById('qs-icono').value.trim() || 'box';
    var desc   = document.getElementById('qs-desc').value.trim();
    var errDiv = document.getElementById('qs-error');

    if (!nombre) { errDiv.textContent='El nombre es obligatorio.'; errDiv.style.display='block'; return; }
    errDiv.style.display = 'none';

    this.disabled = true; this.innerHTML = '<i class="bi bi-hourglass"></i> Guardando...';

    try {
        var res  = await fetch(_quickSrvUrl, {
            method: 'POST',
            headers: { 'Content-Type':'application/json', 'X-CSRF-TOKEN': _csrfToken2 },
            body: JSON.stringify({ nombre, monto: parseFloat(monto)||0, icono, descripcion: desc }),
        });
        var data = await res.json();
        if (!res.ok) throw new Error(data.message || 'Error al guardar');

        // Agregar al catálogo local y al formulario
        mServicios.push(data.servicio);
        srvAddRow(data.servicio);
        cerrarQuickSrv();

    } catch(err) {
        errDiv.textContent = err.message; errDiv.style.display = 'block';
    } finally {
        this.disabled = false; this.innerHTML = '<i class="bi bi-floppy"></i> Guardar y agregar';
    }
});
</script>
@endpush
