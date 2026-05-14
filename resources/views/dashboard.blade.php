@extends('layouts.app')

@section('title', 'Dashboard')
@section('subtitle', 'Resumen general del sistema')

@section('content')

{{-- Alertas del sistema --}}
@if(isset($alertas) && $alertas->count() > 0)
<div class="mb-3">
    @foreach($alertas as $alerta)
    <div class="alert alert-{{ $alerta['tipo'] }} alert-dismissible d-flex align-items-center gap-2 py-2 mb-2" role="alert">
        <i class="bi bi-{{ $alerta['icono'] }} fs-5 flex-shrink-0"></i>
        <div class="flex-grow-1">{!! $alerta['mensaje'] !!}</div>
        <a href="{{ $alerta['url'] }}" class="btn btn-sm btn-{{ $alerta['tipo'] }} ms-2">Ver</a>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endforeach
</div>
@endif

@if(auth()->user()->esAdmin())

{{-- Welcome banner --}}
<div style="background:linear-gradient(135deg,var(--sidebar-bg) 0%,#2D3147 60%,#1E2035 100%);border-radius:var(--radius-lg);padding:24px 32px;margin-bottom:24px;display:flex;align-items:center;justify-content:space-between;gap:20px;box-shadow:0 8px 32px rgba(15,23,42,.18);">
    <div>
        <div style="font-size:11px;font-weight:700;color:rgba(255,255,255,.4);text-transform:uppercase;letter-spacing:1.2px;margin-bottom:6px">Panel de administración</div>
        <div style="font-size:22px;font-weight:800;color:#fff;letter-spacing:-.3px">Bienvenido, {{ auth()->user()->nombre_completo }}</div>
        <div style="font-size:13px;color:rgba(255,255,255,.5);margin-top:4px"><i class="bi bi-calendar3" style="margin-right:5px"></i>{{ now()->locale('es')->isoFormat('dddd, D [de] MMMM [de] YYYY') }}</div>
    </div>
    <div style="display:flex;gap:12px;flex-wrap:wrap">
        <a href="{{ route('cotizaciones.create') }}" class="btn btn-primary btn-sm"><i class="bi bi-plus-lg"></i>Nueva cotización</a>
        <a href="{{ route('empresas.index') }}" class="btn btn-sm" style="background:rgba(255,255,255,.1);color:#fff;border:1px solid rgba(255,255,255,.15)"><i class="bi bi-building"></i>Empresas</a>
    </div>
</div>

<div class="stats-grid" style="grid-template-columns:repeat(auto-fill,minmax(180px,1fr))">
    <div class="stat-card">
        <div class="stat-icon blue"><i class="bi bi-building"></i></div>
        <div>
            <div class="stat-value">{{ $stats['total_empresas'] }}</div>
            <div class="stat-label">Empresas activas</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon green"><i class="bi bi-arrow-down-circle"></i></div>
        <div>
            <div class="stat-value" style="font-size:20px;color:#059669">S/. {{ number_format($stats['ingresos_mes'], 0, ',', '.') }}</div>
            <div class="stat-label">Ingresos del mes</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon red"><i class="bi bi-arrow-up-circle"></i></div>
        <div>
            <div class="stat-value" style="font-size:20px;color:var(--primary)">S/. {{ number_format($stats['egresos_mes'], 0, ',', '.') }}</div>
            <div class="stat-label">Egresos del mes</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon amber"><i class="bi bi-exclamation-triangle"></i></div>
        <div>
            <div class="stat-value" style="color:#D97706">{{ $stats['cuotas_vencidas'] }}</div>
            <div class="stat-label">Cuotas vencidas</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon purple"><i class="bi bi-display"></i></div>
        <div>
            <div class="stat-value" style="color:#7C3AED">{{ $ocupacion_pct }}%</div>
            <div class="stat-label">Ocupación paneles ({{ $paneles_activos }}/{{ $total_paneles }})</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon green"><i class="bi bi-megaphone-fill"></i></div>
        <div>
            <div class="stat-value" style="font-size:20px;color:#059669">S/. {{ number_format($stats['ingresos_publicidad'], 0, ',', '.') }}</div>
            <div class="stat-label">Cobrado publicidad</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon amber"><i class="bi bi-hourglass-split"></i></div>
        <div>
            <div class="stat-value" style="font-size:20px;color:#D97706">S/. {{ number_format($stats['pendiente_publicidad'], 0, ',', '.') }}</div>
            <div class="stat-label">Pendiente publicidad</div>
        </div>
    </div>
</div>

<div class="card" style="margin-bottom:24px">
    <div class="card-header ch-blue">
        <span><i class="bi bi-bar-chart-line"></i>Ingresos últimos 6 meses</span>
    </div>
    <div class="card-body" style="height:240px;position:relative">
        <canvas id="chartIngresos"></canvas>
    </div>
</div>
@else
<div class="stats-grid" style="grid-template-columns: repeat(3,1fr); margin-bottom:28px">
    <div class="stat-card">
        <div class="stat-icon blue"><i class="bi bi-cash-coin"></i></div>
        <div>
            <div class="stat-value">{{ $stats['cuotas_pendientes'] }}</div>
            <div class="stat-label">Cuotas pendientes</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon red"><i class="bi bi-exclamation-circle"></i></div>
        <div>
            <div class="stat-value" style="color:var(--primary)">{{ $stats['cuotas_vencidas'] }}</div>
            <div class="stat-label">Cuotas vencidas</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon green"><i class="bi bi-check-circle"></i></div>
        <div>
            <div class="stat-value" style="font-size:18px">{{ number_format($stats['total_pagado'], 0, ',', '.') }}</div>
            <div class="stat-label">Total pagado (S/.)</div>
        </div>
    </div>
</div>
@endif

@if(auth()->user()->esAdmin() && $contratos_morosos->count() > 0)
<div class="card" style="margin-bottom:24px">
    <div class="card-header" style="border-left-color:var(--primary);background:linear-gradient(to right,#FFF5F5,#fff)">
        <span><i class="bi bi-exclamation-circle"></i>Contratos morosos</span>
        <a href="{{ route('contratos.index') }}" class="btn btn-sm btn-secondary">Ver todos</a>
    </div>
    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>N° Contrato</th>
                    <th>Contratante / Empresa</th>
                    <th>Saldo pendiente</th>
                    <th>Ultimo cobro</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach($contratos_morosos as $cm)
                <tr>
                    <td class="fw-600">{{ $cm->numero_contrato }}</td>
                    <td>
                        <div>{{ $cm->contratante }}</div>
                        @if($cm->empresa)<div class="text-muted" style="font-size:12px">{{ $cm->empresa->nombre }}</div>@endif
                    </td>
                    <td class="fw-700" style="color:var(--primary)">S/. {{ number_format($cm->saldo_pendiente, 0, ',', '.') }}</td>
                    <td class="text-muted" style="font-size:12px">
                        {{ $cm->cobros->sortByDesc('fecha_cobro')->first()?->fecha_cobro?->format('d/m/Y') ?? '—' }}
                    </td>
                    <td>
                        <a href="{{ route('contratos.show', $cm) }}" class="btn btn-sm btn-secondary">
                            <i class="bi bi-eye"></i>
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif

<div class="card">
    <div class="card-header ch-amber">
        <span><i class="bi bi-calendar-event"></i>Próximas cuotas a vencer</span>
        <a href="{{ route('cobranzas.index') }}?estado=pendiente" class="btn btn-sm btn-secondary">Ver todas</a>
    </div>
    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    @if(auth()->user()->esAdmin())<th>Empresa</th>@endif
                    <th>N° Cuota</th>
                    <th>Concepto</th>
                    <th>Monto</th>
                    <th>Vencimiento</th>
                    <th>Estado</th>
                </tr>
            </thead>
            <tbody>
                @forelse($proximas_cuotas as $cuota)
                <tr>
                    @if(auth()->user()->esAdmin())
                    <td class="fw-600">{{ $cuota->empresa->nombre ?? '—' }}</td>
                    @endif
                    <td>N° {{ $cuota->numero_cuota }}</td>
                    <td class="text-muted">{{ $cuota->concepto ?? '—' }}</td>
                    <td class="fw-700">S/. {{ number_format($cuota->monto, 0, ',', '.') }}</td>
                    <td>
                        {{ $cuota->fecha_vencimiento->format('d/m/Y') }}
                        @if($cuota->fecha_vencimiento->isPast())
                            <span class="badge badge-danger" style="margin-left:6px">Vencida</span>
                        @elseif($cuota->fecha_vencimiento->diffInDays(now()) <= 7)
                            <span class="badge badge-warning" style="margin-left:6px">Próxima</span>
                        @endif
                    </td>
                    <td><span class="badge badge-warning">Pendiente</span></td>
                </tr>
                @empty
                <tr>
                    <td colspan="6">
                        <div class="empty-state" style="padding:40px 24px">
                            <i class="bi bi-check2-circle" style="color:#10B981"></i>
                            <p>No hay cuotas pendientes</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@if(auth()->user()->esAdmin() && $proximas_campanas->count() > 0)
<div class="card" style="margin-top:24px">
    <div class="card-header" style="border-left-color:#EA580C;background:linear-gradient(to right,#FFF7ED,#fff)">
        <span><i class="bi bi-megaphone" style="color:#EA580C"></i> Campañas publicitarias con pago pendiente</span>
        <a href="{{ route('control-publicitario.index') }}" class="btn btn-sm btn-secondary">Ver todas</a>
    </div>
    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>Empresa</th>
                    <th>Panel</th>
                    <th>Cobrado</th>
                    <th>Pendiente</th>
                    <th>Vencimiento</th>
                    <th>Estado</th>
                </tr>
            </thead>
            <tbody>
                @foreach($proximas_campanas as $camp)
                @php
                    $diasFin = $camp->fecha_fin ? (int) now()->diffInDays($camp->fecha_fin, false) : null;
                @endphp
                <tr>
                    <td>
                        <div class="fw-600">{{ $camp->empresa_nombre }}</div>
                        @if($camp->ruc)<div class="text-muted" style="font-size:11px;font-family:monospace">{{ $camp->ruc }}</div>@endif
                    </td>
                    <td>
                        <code style="font-size:12px">{{ $camp->panel_codigo }}</code>
                        <span class="badge {{ $camp->tipo_panel === 'digital' ? 'badge-primary' : 'badge-warning' }}" style="font-size:10px;margin-left:4px">{{ ucfirst($camp->tipo_panel) }}</span>
                    </td>
                    <td class="fw-600" style="color:#059669">
                        @if($camp->monto_pagado !== null) S/. {{ number_format($camp->monto_pagado, 2) }} @else <span class="text-muted">—</span> @endif
                    </td>
                    <td class="fw-700" style="color:#EA580C">S/. {{ number_format($camp->monto_pendiente, 2) }}</td>
                    <td>
                        @if($camp->fecha_fin)
                            <div style="font-size:12.5px">{{ $camp->fecha_fin->format('d/m/Y') }}</div>
                            @if($diasFin !== null)
                                @if($diasFin < 0)
                                    <span class="badge badge-danger" style="font-size:10px">Vencida</span>
                                @elseif($diasFin <= 7)
                                    <span class="badge badge-warning" style="font-size:10px">{{ $diasFin }}d</span>
                                @endif
                            @endif
                        @else
                            <span class="text-muted">—</span>
                        @endif
                    </td>
                    <td>
                        @php $bmap = ['activo'=>'success','pausado'=>'warning','cancelado'=>'danger']; @endphp
                        <span class="badge badge-{{ $bmap[$camp->estado] ?? 'gray' }}">{{ ucfirst($camp->estado) }}</span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif

@endsection

@if(auth()->user()->esAdmin())
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
<script>
(function () {
    var labels = @json($ingresos6meses->pluck('label'));
    var data   = @json($ingresos6meses->pluck('monto'));
    var ctx    = document.getElementById('chartIngresos');
    if (!ctx) return;
    var gradient = ctx.getContext('2d').createLinearGradient(0, 0, 0, 220);
    gradient.addColorStop(0, 'rgba(59,130,246,0.4)');
    gradient.addColorStop(1, 'rgba(59,130,246,0.02)');
    new Chart(ctx.getContext('2d'), {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Ingresos',
                data: data,
                backgroundColor: gradient,
                borderColor: '#3B82F6',
                borderWidth: 2,
                borderRadius: 8,
                borderSkipped: false,
                hoverBackgroundColor: 'rgba(59,130,246,0.55)',
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#1A1D29',
                    titleColor: 'rgba(255,255,255,.6)',
                    bodyColor: '#fff',
                    padding: 12,
                    cornerRadius: 8,
                    callbacks: {
                        label: function(ctx) { return ' S/. ' + ctx.raw.toLocaleString('es'); }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(v) { return 'S/. ' + v.toLocaleString('es'); },
                        font: { size: 11 }, color: '#94A3B8'
                    },
                    grid: { color: '#F1F5F9' }
                },
                x: { grid: { display: false }, ticks: { color: '#64748B', font: { size: 12, weight: '600' } } }
            }
        }
    });
})();
</script>
@endpush
@endif
