@extends('layouts.app')

@section('title', 'Deudas')
@section('subtitle', 'Control de deudas y obligaciones')

@section('content')
<div class="page-header">
    <div class="stat-card" style="padding:16px 24px;gap:14px">
        <div class="stat-icon red" style="width:42px;height:42px;border-radius:10px;font-size:18px">
            <i class="bi bi-exclamation-triangle"></i>
        </div>
        <div>
            <div class="stat-value" style="font-size:20px;color:var(--primary)">S/. {{ number_format($total_pendiente, 0, ',', '.') }}</div>
            <div class="stat-label">Total pendiente</div>
        </div>
    </div>
    <a href="{{ route('deudas.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-lg"></i>Nueva Deuda
    </a>
</div>

<div class="filter-bar">
    <form style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;width:100%" method="GET">
        <select name="estado" class="form-select" style="max-width:170px">
            <option value="">Todos los estados</option>
            <option value="pendiente" {{ request('estado') === 'pendiente' ? 'selected' : '' }}>Pendiente</option>
            <option value="pagada" {{ request('estado') === 'pagada' ? 'selected' : '' }}>Pagada</option>
            <option value="cancelada" {{ request('estado') === 'cancelada' ? 'selected' : '' }}>Cancelada</option>
        </select>
        <select name="prioridad" class="form-select" style="max-width:170px">
            <option value="">Todas las prioridades</option>
            <option value="alta" {{ request('prioridad') === 'alta' ? 'selected' : '' }}>Alta</option>
            <option value="media" {{ request('prioridad') === 'media' ? 'selected' : '' }}>Media</option>
            <option value="baja" {{ request('prioridad') === 'baja' ? 'selected' : '' }}>Baja</option>
        </select>
        <button type="submit" class="btn btn-primary btn-sm"><i class="bi bi-search"></i>Filtrar</button>
        <a href="{{ route('deudas.index') }}" class="btn btn-secondary btn-sm">Limpiar</a>
    </form>
</div>

<div class="card">
    <div class="card-header ch-amber">
        <span><i class="bi bi-exclamation-triangle"></i>Registro de Deudas</span>
        <span style="font-size:12px;font-weight:600;color:var(--primary)">S/. {{ number_format($total_pendiente, 0, ',', '.') }} pendiente</span>
    </div>
    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>Acreedor</th><th>Concepto</th><th>Monto total</th><th>Pendiente</th>
                    <th>Vencimiento</th><th>Prioridad</th><th>Estado</th><th class="td-end">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($deudas as $deuda)
                <tr>
                    <td class="fw-700" style="color:var(--text-dark)">{{ $deuda->acreedor }}</td>
                    <td class="text-muted">{{ $deuda->concepto }}</td>
                    <td class="fw-600">S/. {{ number_format($deuda->monto, 0, ',', '.') }}</td>
                    <td class="fw-700" style="color:var(--primary)">S/. {{ number_format($deuda->monto_pendiente, 0, ',', '.') }}</td>
                    <td>
                        {{ $deuda->fecha_vencimiento?->format('d/m/Y') ?? '—' }}
                        @if($deuda->estado === 'pendiente' && $deuda->fecha_vencimiento?->isPast())
                            <span class="badge badge-danger" style="margin-left:4px">Vencida</span>
                        @endif
                    </td>
                    <td>
                        @php $prioColors = ['alta'=>'danger','media'=>'warning','baja'=>'gray']; @endphp
                        <span class="badge badge-{{ $prioColors[$deuda->prioridad] ?? 'gray' }}">{{ ucfirst($deuda->prioridad) }}</span>
                    </td>
                    <td>
                        @if($deuda->estado === 'pagada')
                            <span class="badge badge-success">Pagada</span>
                        @elseif($deuda->estado === 'cancelada')
                            <span class="badge badge-gray">Cancelada</span>
                        @else
                            <span class="badge badge-warning">Pendiente</span>
                        @endif
                    </td>
                    <td class="td-end">
                        <div class="flex flex-center gap-8" style="justify-content:flex-end">
                            <a href="{{ route('deudas.show', $deuda) }}" class="btn btn-sm btn-secondary btn-icon" title="Ver"><i class="bi bi-eye"></i></a>
                            <a href="{{ route('deudas.edit', $deuda) }}" class="btn btn-sm btn-warning btn-icon" title="Editar"><i class="bi bi-pencil"></i></a>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="8"><div class="empty-state"><i class="bi bi-exclamation-triangle"></i><p>No hay deudas registradas</p></div></td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($deudas->hasPages())
    <div class="card-footer">{{ $deudas->withQueryString()->links() }}</div>
    @endif
</div>
@endsection
