@extends('layouts.app')

@section('title', 'Campaña: ' . $controlPublicitario->empresa_nombre)
@section('subtitle', 'Historial de campaña')

@section('content')
<div class="page-header">
    <div class="page-header-left">
        <a href="{{ route('control-publicitario.index') }}" class="back-btn"><i class="bi bi-arrow-left"></i></a>
        <div>
            <div class="page-title">{{ $controlPublicitario->empresa_nombre }}</div>
            <div style="margin-top:4px">
                <code>{{ $controlPublicitario->panel_codigo }}</code>
                <span class="badge badge-{{ $controlPublicitario->tipo_panel === 'digital' ? 'primary' : 'warning' }}" style="margin-left:6px">
                    {{ ucfirst($controlPublicitario->tipo_panel) }}
                </span>
            </div>
        </div>
    </div>
    @php $bmap = ['activo'=>'success','pausado'=>'warning','cancelado'=>'danger']; @endphp
    <span class="badge badge-{{ $bmap[$controlPublicitario->estado] ?? 'gray' }}" style="font-size:14px;padding:8px 18px">
        {{ ucfirst($controlPublicitario->estado) }}
    </span>
</div>

<div class="row g-3">
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header"><span><i class="bi bi-info-circle" style="color:var(--primary);margin-right:8px"></i>Datos de la campaña</span></div>
            <div class="card-body">
                <div class="detail-grid">
                    <div class="detail-row"><div class="detail-label">Empresa</div><div class="detail-value fw-600">{{ $controlPublicitario->empresa_nombre }}</div></div>
                    <div class="detail-row"><div class="detail-label">Panel</div><div class="detail-value"><code>{{ $controlPublicitario->panel_codigo }}</code></div></div>
                    <div class="detail-row"><div class="detail-label">Tipo</div><div class="detail-value">{{ ucfirst($controlPublicitario->tipo_panel) }}</div></div>
                    <div class="detail-row"><div class="detail-label">Inicio</div><div class="detail-value">{{ $controlPublicitario->fecha_inicio?->format('d/m/Y') ?? '—' }}</div></div>
                    <div class="detail-row"><div class="detail-label">Fin</div><div class="detail-value">
                        {{ $controlPublicitario->fecha_fin?->format('d/m/Y') ?? '—' }}
                        @if($controlPublicitario->estado === 'activo' && $controlPublicitario->fecha_fin?->isPast())
                            <span class="badge badge-danger" style="margin-left:4px">Vencida</span>
                        @endif
                    </div></div>
                    @if($controlPublicitario->fecha_cancelacion)
                    <div class="detail-row"><div class="detail-label">Cancelación</div><div class="detail-value">{{ $controlPublicitario->fecha_cancelacion->format('d/m/Y H:i') }}</div></div>
                    @endif
                </div>
                @if($controlPublicitario->notas)
                    <div style="margin-top:12px;padding-top:10px;border-top:1px solid var(--border);font-size:13px;color:var(--text-light)">{{ $controlPublicitario->notas }}</div>
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
