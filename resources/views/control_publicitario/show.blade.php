@extends('layouts.app')

@section('title', 'Campaña: ' . $controlPublicitario->empresa_nombre)
@section('subtitle', 'Historial de campaña')

@push('styles')
<style>
.badge-vencida  { background:linear-gradient(135deg,#DC2626,#B91C1C); color:#fff; box-shadow:0 2px 8px rgba(220,38,38,.45); }
.badge-urgente  { background:linear-gradient(135deg,#EA580C,#C2410C); color:#fff; box-shadow:0 0 10px rgba(234,88,12,.55); animation:pulse-orange 1.6s ease-in-out infinite; }
.badge-proximo  { background:linear-gradient(135deg,#2563EB,#1D4ED8); color:#fff; box-shadow:0 2px 8px rgba(37,99,235,.4); }
@keyframes pulse-orange { 0%,100%{box-shadow:0 0 6px rgba(234,88,12,.45);}50%{box-shadow:0 0 18px rgba(234,88,12,.9);} }
.campaign-header-card {
    background:linear-gradient(135deg,#1E293B,#334155);
    border-radius:16px 16px 0 0; padding:20px 24px;
    display:flex; align-items:center; gap:14px;
}
.campaign-header-card .ch-icon { width:48px; height:48px; border-radius:12px; background:rgba(255,255,255,.12); display:flex; align-items:center; justify-content:center; font-size:22px; color:#fff; }
.campaign-header-card .ch-title { font-size:15px; font-weight:700; color:#fff; }
.campaign-header-card .ch-sub   { font-size:12px; color:rgba(255,255,255,.65); margin-top:2px; }
.monto-badge        { display:inline-flex; align-items:center; gap:6px; padding:6px 14px; border-radius:20px; font-weight:700; font-size:14px; }
.monto-pagado       { background:linear-gradient(135deg,#D1FAE5,#A7F3D0); color:#065F46; }
.monto-pendiente    { background:linear-gradient(135deg,#FEF3C7,#FDE68A); color:#92400E; }
.empresa-link-card  { background:linear-gradient(135deg,#EFF6FF,#DBEAFE); border:1px solid #BFDBFE; border-radius:10px; padding:10px 14px; display:flex; align-items:center; gap:10px; }
.ruc-tag { display:inline-flex; align-items:center; gap:4px; background:#EFF6FF; color:#2563EB; border:1px solid #BFDBFE; border-radius:6px; padding:3px 10px; font-size:12px; font-weight:600; font-family:monospace; }
</style>
@endpush

@section('content')
<div class="page-header">
    <div class="page-header-left">
        <a href="{{ route('control-publicitario.index') }}" class="back-btn"><i class="bi bi-arrow-left"></i></a>
        <div>
            <div class="page-title">{{ $controlPublicitario->empresa_nombre }}</div>
            <div style="margin-top:4px;display:flex;align-items:center;gap:6px;flex-wrap:wrap">
                @if($controlPublicitario->ruc)
                    <span class="ruc-tag"><i class="bi bi-upc-scan"></i>{{ $controlPublicitario->ruc }}</span>
                @endif
                <code>{{ $controlPublicitario->panel_codigo }}</code>
                <span class="badge badge-{{ $controlPublicitario->tipo_panel === 'digital' ? 'primary' : 'warning' }}">
                    {{ ucfirst($controlPublicitario->tipo_panel) }}
                </span>
            </div>
        </div>
    </div>
    <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap">
        @php $bmap = ['activo'=>'success','pausado'=>'warning','cancelado'=>'danger']; @endphp
        <span class="badge badge-{{ $bmap[$controlPublicitario->estado] ?? 'gray' }}" style="font-size:14px;padding:8px 18px">
            {{ ucfirst($controlPublicitario->estado) }}
        </span>
        @if($controlPublicitario->estado === 'activo' && $controlPublicitario->fecha_fin)
            @php $dias = (int) now()->diffInDays($controlPublicitario->fecha_fin, false) @endphp
            @if($dias < 0)
                <span class="badge badge-vencida" style="font-size:13px;padding:7px 14px">Vencida</span>
            @elseif($dias <= 3)
                <span class="badge badge-urgente" style="font-size:13px;padding:7px 14px">Vence en {{ $dias }}d</span>
            @elseif($dias <= 15)
                <span class="badge badge-proximo" style="font-size:13px;padding:7px 14px">{{ $dias }} días restantes</span>
            @endif
        @endif
    </div>
</div>

<div class="row g-3">
    <div class="col-lg-4">
        <div class="card" style="overflow:hidden">
            <div class="campaign-header-card">
                <div class="ch-icon"><i class="bi bi-megaphone-fill"></i></div>
                <div>
                    <div class="ch-title">Datos de la campaña</div>
                    <div class="ch-sub">Información del contrato publicitario</div>
                </div>
            </div>
            <div class="card-body">

                {{-- Link a empresa --}}
                @if($controlPublicitario->empresa)
                <div class="empresa-link-card" style="margin-bottom:14px">
                    <i class="bi bi-building-fill" style="color:#2563EB;font-size:18px"></i>
                    <div style="flex:1;min-width:0">
                        <div style="font-size:12px;color:#1D4ED8;font-weight:700">Empresa vinculada</div>
                        <div style="font-weight:600;color:#1E293B;white-space:nowrap;overflow:hidden;text-overflow:ellipsis">{{ $controlPublicitario->empresa->nombre }}</div>
                    </div>
                    <a href="{{ route('empresas.show', $controlPublicitario->empresa_id) }}"
                       class="btn btn-sm" style="background:#2563EB;color:#fff;white-space:nowrap">
                        <i class="bi bi-box-arrow-up-right"></i> Ver
                    </a>
                </div>
                @endif

                <div class="detail-grid">
                    <div class="detail-row">
                        <div class="detail-label">Empresa</div>
                        <div class="detail-value fw-600">{{ $controlPublicitario->empresa_nombre }}</div>
                    </div>
                    @if($controlPublicitario->ruc)
                    <div class="detail-row">
                        <div class="detail-label">RUC</div>
                        <div class="detail-value"><span class="ruc-tag">{{ $controlPublicitario->ruc }}</span></div>
                    </div>
                    @endif
                    <div class="detail-row">
                        <div class="detail-label">Panel</div>
                        <div class="detail-value"><code>{{ $controlPublicitario->panel_codigo }}</code></div>
                    </div>
                    <div class="detail-row">
                        <div class="detail-label">Tipo</div>
                        <div class="detail-value">{{ ucfirst($controlPublicitario->tipo_panel) }}</div>
                    </div>
                    <div class="detail-row">
                        <div class="detail-label">Inicio</div>
                        <div class="detail-value">{{ $controlPublicitario->fecha_inicio?->format('d/m/Y') ?? '—' }}</div>
                    </div>
                    <div class="detail-row">
                        <div class="detail-label">Fin</div>
                        <div class="detail-value">
                            {{ $controlPublicitario->fecha_fin?->format('d/m/Y') ?? '—' }}
                            @if($controlPublicitario->estado === 'activo' && $controlPublicitario->fecha_fin)
                                @php $d = (int) now()->diffInDays($controlPublicitario->fecha_fin, false) @endphp
                                @if($d < 0)
                                    <span class="badge badge-vencida" style="margin-left:4px;font-size:11px">Vencida</span>
                                @elseif($d <= 3)
                                    <span class="badge badge-urgente" style="margin-left:4px;font-size:11px">{{ $d }}d</span>
                                @elseif($d <= 15)
                                    <span class="badge badge-proximo" style="margin-left:4px;font-size:11px">{{ $d }}d</span>
                                @endif
                            @endif
                        </div>
                    </div>
                    @if($controlPublicitario->fecha_cancelacion)
                    <div class="detail-row">
                        <div class="detail-label">Cancelación</div>
                        <div class="detail-value">{{ $controlPublicitario->fecha_cancelacion->format('d/m/Y H:i') }}</div>
                    </div>
                    @endif
                </div>

                {{-- Montos --}}
                @if($controlPublicitario->monto_pagado !== null || $controlPublicitario->monto_pendiente !== null)
                <div style="margin-top:16px;padding-top:14px;border-top:1px solid var(--border)">
                    <div style="font-size:11px;font-weight:700;letter-spacing:.5px;text-transform:uppercase;color:var(--text-lighter);margin-bottom:10px">Montos del Contrato</div>
                    <div style="display:flex;flex-direction:column;gap:8px">
                        @if($controlPublicitario->monto_pagado !== null)
                        <div class="monto-badge monto-pagado">
                            <i class="bi bi-check-circle-fill"></i>
                            Pagado: ${{ number_format($controlPublicitario->monto_pagado, 2) }}
                        </div>
                        @endif
                        @if($controlPublicitario->monto_pendiente !== null)
                        <div class="monto-badge monto-pendiente">
                            <i class="bi bi-clock-fill"></i>
                            Pendiente: ${{ number_format($controlPublicitario->monto_pendiente, 2) }}
                        </div>
                        @endif
                    </div>
                </div>
                @endif

                @if($controlPublicitario->notas)
                    <div style="margin-top:12px;padding-top:10px;border-top:1px solid var(--border);font-size:13px;color:var(--text-light)">
                        {{ $controlPublicitario->notas }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <span><i class="bi bi-clock-history" style="color:var(--primary);margin-right:8px"></i>Historial de cambios</span>
                <span class="badge badge-gray">{{ $controlPublicitario->historial->count() }}</span>
            </div>
            <div class="table-wrapper">
                <table>
                    <thead><tr><th>Fecha</th><th>Cambio</th><th>Notas</th><th>Usuario</th></tr></thead>
                    <tbody>
                        @forelse($controlPublicitario->historial as $h)
                        <tr>
                            <td class="text-muted" style="font-size:12.5px">{{ $h->created_at->format('d/m/Y H:i') }}</td>
                            <td>
                                @if($h->estado_anterior)
                                    @php $bm = ['activo'=>'success','pausado'=>'warning','cancelado'=>'danger']; @endphp
                                    <span class="badge badge-{{ $bm[$h->estado_anterior] ?? 'gray' }}">{{ ucfirst($h->estado_anterior) }}</span>
                                    <i class="bi bi-arrow-right" style="margin:0 6px;color:var(--text-lighter);font-size:11px"></i>
                                    <span class="badge badge-{{ $bm[$h->estado_nuevo] ?? 'gray' }}">{{ ucfirst($h->estado_nuevo) }}</span>
                                @else
                                    <span class="badge badge-gray">Creado como {{ ucfirst($h->estado_nuevo) }}</span>
                                @endif
                            </td>
                            <td class="text-muted" style="font-size:13px">{{ $h->notas ?? '—' }}</td>
                            <td style="font-size:13px">{{ $h->usuario?->nombre_completo ?? $h->usuario?->username ?? '—' }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="4"><div class="empty-state" style="padding:32px"><i class="bi bi-clock-history"></i><p>Sin historial</p></div></td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
