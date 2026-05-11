@extends('layouts.app')

@section('title', 'Campaña: ' . $controlPublicitario->empresa_nombre)

@section('content')
<div class="d-flex align-items-center justify-content-between mb-3">
    <div class="d-flex align-items-center gap-2">
        <a href="{{ route('control-publicitario.index') }}" class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-arrow-left"></i>
        </a>
        <div>
            <h5 class="mb-0 fw-semibold">{{ $controlPublicitario->empresa_nombre }}</h5>
            <div class="small text-muted">
                <code>{{ $controlPublicitario->panel_codigo }}</code>
                <span class="badge bg-{{ $controlPublicitario->tipo_panel === 'digital' ? 'primary' : 'warning text-dark' }} ms-1">
                    {{ ucfirst($controlPublicitario->tipo_panel) }}
                </span>
            </div>
        </div>
    </div>
    @php $bmap = ['activo'=>'success','pausado'=>'warning','cancelado'=>'danger']; @endphp
    <span class="badge bg-{{ $bmap[$controlPublicitario->estado] ?? 'secondary' }} fs-6 px-3 py-2
        {{ $controlPublicitario->estado === 'pausado' ? 'text-dark' : '' }}">
        {{ ucfirst($controlPublicitario->estado) }}
    </span>
</div>

<div class="row g-3">
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white fw-medium py-3">Datos de la campaña</div>
            <div class="card-body">
                <table class="table table-sm table-borderless mb-0">
                    <tr>
                        <th class="text-muted small">Empresa</th>
                        <td class="fw-medium">{{ $controlPublicitario->empresa_nombre }}</td>
                    </tr>
                    <tr>
                        <th class="text-muted small">Panel</th>
                        <td><code>{{ $controlPublicitario->panel_codigo }}</code></td>
                    </tr>
                    <tr>
                        <th class="text-muted small">Tipo</th>
                        <td>{{ ucfirst($controlPublicitario->tipo_panel) }}</td>
                    </tr>
                    <tr>
                        <th class="text-muted small">Inicio</th>
                        <td>{{ $controlPublicitario->fecha_inicio?->format('d/m/Y') ?? '—' }}</td>
                    </tr>
                    <tr>
                        <th class="text-muted small">Fin</th>
                        <td>
                            {{ $controlPublicitario->fecha_fin?->format('d/m/Y') ?? '—' }}
                            @if($controlPublicitario->estado === 'activo' && $controlPublicitario->fecha_fin?->isPast())
                                <span class="badge bg-danger ms-1">Vencida</span>
                            @endif
                        </td>
                    </tr>
                    @if($controlPublicitario->fecha_cancelacion)
                    <tr>
                        <th class="text-muted small">Cancelación</th>
                        <td>{{ $controlPublicitario->fecha_cancelacion->format('d/m/Y H:i') }}</td>
                    </tr>
                    @endif
                </table>
                @if($controlPublicitario->notas)
                    <div class="mt-2 small text-muted border-top pt-2">{{ $controlPublicitario->notas }}</div>
                @endif
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white fw-medium py-3 d-flex justify-content-between align-items-center">
                <span><i class="bi bi-clock-history me-2 text-secondary"></i>Historial de cambios</span>
                <span class="badge bg-secondary">{{ $controlPublicitario->historial->count() }}</span>
            </div>
            <div class="table-responsive">
                <table class="table table-sm table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Fecha</th>
                            <th>Cambio</th>
                            <th>Notas</th>
                            <th>Usuario</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($controlPublicitario->historial as $h)
                        <tr>
                            <td class="small text-muted">{{ $h->created_at->format('d/m/Y H:i') }}</td>
                            <td>
                                @if($h->estado_anterior)
                                    @php $bmap = ['activo'=>'success','pausado'=>'warning','cancelado'=>'danger']; @endphp
                                    <span class="badge bg-{{ $bmap[$h->estado_anterior] ?? 'secondary' }}
                                        {{ $h->estado_anterior==='pausado'?'text-dark':'' }}">
                                        {{ ucfirst($h->estado_anterior) }}
                                    </span>
                                    <i class="bi bi-arrow-right mx-1 text-muted small"></i>
                                    <span class="badge bg-{{ $bmap[$h->estado_nuevo] ?? 'secondary' }}
                                        {{ $h->estado_nuevo==='pausado'?'text-dark':'' }}">
                                        {{ ucfirst($h->estado_nuevo) }}
                                    </span>
                                @else
                                    <span class="badge bg-secondary">Creado como {{ ucfirst($h->estado_nuevo) }}</span>
                                @endif
                            </td>
                            <td class="small text-muted">{{ $h->notas ?? '—' }}</td>
                            <td class="small">{{ $h->usuario?->nombre_completo ?? $h->usuario?->username ?? '—' }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="4" class="text-center text-muted py-3">Sin historial</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
