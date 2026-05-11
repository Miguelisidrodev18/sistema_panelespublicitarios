@extends('layouts.app')

@section('title', 'Deudas')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <div class="card border-0 shadow-sm px-4 py-3">
        <div class="text-muted small">Total pendiente</div>
        <div class="fs-5 fw-bold text-danger">S/. {{ number_format($total_pendiente, 0, ',', '.') }}</div>
    </div>
    <a href="{{ route('deudas.create') }}" class="btn btn-danger">
        <i class="bi bi-plus-lg me-1"></i>Nueva Deuda
    </a>
</div>

<div class="card border-0 shadow-sm mb-3">
    <div class="card-body py-2">
        <form class="row g-2" method="GET">
            <div class="col-md-3">
                <select name="estado" class="form-select">
                    <option value="">Todos los estados</option>
                    <option value="pendiente" {{ request('estado') === 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                    <option value="pagada" {{ request('estado') === 'pagada' ? 'selected' : '' }}>Pagada</option>
                    <option value="cancelada" {{ request('estado') === 'cancelada' ? 'selected' : '' }}>Cancelada</option>
                </select>
            </div>
            <div class="col-md-3">
                <select name="prioridad" class="form-select">
                    <option value="">Todas las prioridades</option>
                    <option value="alta" {{ request('prioridad') === 'alta' ? 'selected' : '' }}>Alta</option>
                    <option value="media" {{ request('prioridad') === 'media' ? 'selected' : '' }}>Media</option>
                    <option value="baja" {{ request('prioridad') === 'baja' ? 'selected' : '' }}>Baja</option>
                </select>
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-outline-primary"><i class="bi bi-search"></i> Filtrar</button>
                <a href="{{ route('deudas.index') }}" class="btn btn-outline-secondary ms-1">Limpiar</a>
            </div>
        </form>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Acreedor</th>
                    <th>Concepto</th>
                    <th>Monto total</th>
                    <th>Pendiente</th>
                    <th>Vencimiento</th>
                    <th>Prioridad</th>
                    <th>Estado</th>
                    <th class="text-end">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($deudas as $deuda)
                <tr>
                    <td class="fw-medium">{{ $deuda->acreedor }}</td>
                    <td>{{ $deuda->concepto }}</td>
                    <td>S/. {{ number_format($deuda->monto, 0, ',', '.') }}</td>
                    <td class="text-danger fw-medium">S/. {{ number_format($deuda->monto_pendiente, 0, ',', '.') }}</td>
                    <td>
                        {{ $deuda->fecha_vencimiento?->format('d/m/Y') ?? '-' }}
                        @if($deuda->estado === 'pendiente' && $deuda->fecha_vencimiento?->isPast())
                            <span class="badge bg-danger ms-1">Vencida</span>
                        @endif
                    </td>
                    <td>
                        @php $prioColors = ['alta'=>'danger','media'=>'warning','baja'=>'secondary']; @endphp
                        <span class="badge bg-{{ $prioColors[$deuda->prioridad] ?? 'secondary' }} {{ $deuda->prioridad==='media'?'text-dark':'' }}">
                            {{ ucfirst($deuda->prioridad) }}
                        </span>
                    </td>
                    <td>
                        @if($deuda->estado === 'pagada')
                            <span class="badge bg-success">Pagada</span>
                        @elseif($deuda->estado === 'cancelada')
                            <span class="badge bg-secondary">Cancelada</span>
                        @else
                            <span class="badge bg-warning text-dark">Pendiente</span>
                        @endif
                    </td>
                    <td class="text-end">
                        <a href="{{ route('deudas.show', $deuda) }}" class="btn btn-sm btn-outline-primary me-1">
                            <i class="bi bi-eye"></i>
                        </a>
                        <a href="{{ route('deudas.edit', $deuda) }}" class="btn btn-sm btn-outline-warning">
                            <i class="bi bi-pencil"></i>
                        </a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="8" class="text-center text-muted py-4">No hay deudas registradas</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($deudas->hasPages())
    <div class="card-footer bg-white">{{ $deudas->withQueryString()->links() }}</div>
    @endif
</div>
@endsection
