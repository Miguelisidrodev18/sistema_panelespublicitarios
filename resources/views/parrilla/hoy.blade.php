@extends('layouts.app')

@section('title', 'Parrilla del dia')
@section('subtitle', $hoy->format('d/m/Y'))

@section('content')

@php
    $digitales     = $campanas->where('tipo_panel', 'digital');
    $tradicionales = $campanas->where('tipo_panel', 'tradicional');
@endphp

<div class="stats-grid" style="grid-template-columns: repeat(3,1fr); margin-bottom:24px">
    <div class="stat-card">
        <div class="stat-icon dark"><i class="bi bi-broadcast"></i></div>
        <div>
            <div class="stat-value">{{ $campanas->count() }}</div>
            <div class="stat-label">Campanas activas hoy</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon blue"><i class="bi bi-display"></i></div>
        <div>
            <div class="stat-value">{{ $digitales->count() }}</div>
            <div class="stat-label">Paneles digitales</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon amber"><i class="bi bi-signpost-2"></i></div>
        <div>
            <div class="stat-value">{{ $tradicionales->count() }}</div>
            <div class="stat-label">Paneles tradicionales</div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <span><i class="bi bi-calendar-check" style="color:var(--primary);margin-right:8px"></i>Campanas en aire — {{ $hoy->format('d/m/Y') }}</span>
        <a href="{{ route('control-publicitario.index') }}" class="btn btn-sm btn-secondary">Ver control publicitario</a>
    </div>
    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>Panel</th>
                    <th>Tipo</th>
                    <th>Empresa / Anunciante</th>
                    <th>Desde</th>
                    <th>Hasta</th>
                    <th>Dias restantes</th>
                    <th>Notas</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($campanas as $c)
                <tr>
                    <td class="fw-600">{{ $c->panel_codigo }}</td>
                    <td>
                        @if($c->tipo_panel === 'digital')
                            <span class="badge" style="background:#3B82F6;color:#fff"><i class="bi bi-display"></i> Digital</span>
                        @else
                            <span class="badge badge-warning"><i class="bi bi-signpost-2"></i> Tradicional</span>
                        @endif
                    </td>
                    <td>{{ $c->empresa_nombre }}</td>
                    <td>{{ $c->fecha_inicio?->format('d/m/Y') ?? '—' }}</td>
                    <td>
                        {{ $c->fecha_fin?->format('d/m/Y') ?? '—' }}
                        @if($c->fecha_fin && $c->fecha_fin->diffInDays(now()) <= 7)
                            <span class="badge badge-danger" style="margin-left:4px">Por vencer</span>
                        @endif
                    </td>
                    <td>
                        @if($c->fecha_fin)
                            @php $dias = (int) now()->diffInDays($c->fecha_fin, false); @endphp
                            @if($dias >= 0)
                                <span class="fw-600 {{ $dias <= 7 ? 'text-danger' : '' }}">{{ $dias }} dias</span>
                            @else
                                <span class="badge badge-danger">Vencido</span>
                            @endif
                        @else
                            <span class="text-muted">Sin fecha fin</span>
                        @endif
                    </td>
                    <td class="text-muted" style="font-size:13px">{{ $c->notas ?? '—' }}</td>
                    <td>
                        <a href="{{ route('control-publicitario.show', $c) }}" class="btn btn-sm btn-secondary">
                            <i class="bi bi-eye"></i>
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8">
                        <div class="empty-state" style="padding:40px 24px">
                            <i class="bi bi-calendar-x" style="color:#10B981"></i>
                            <p>No hay campanas activas hoy</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection
