@extends('layouts.app')

@section('title', 'Cobranzas')
@section('subtitle', 'Control de cuotas y pagos')

@section('content')
<form class="filter-bar" method="GET">
    @if(auth()->user()->esAdmin())
    <select name="empresa_id" class="form-select" style="max-width:220px">
        <option value="">Todas las empresas</option>
        @foreach($empresas as $e)
        <option value="{{ $e->id }}" {{ request('empresa_id') == $e->id ? 'selected' : '' }}>{{ $e->nombre }}</option>
        @endforeach
    </select>
    @endif
    <select name="estado" class="form-select" style="max-width:160px">
        <option value="">Todos los estados</option>
        <option value="pendiente" {{ request('estado') === 'pendiente' ? 'selected' : '' }}>Pendientes</option>
        <option value="pagada"    {{ request('estado') === 'pagada'    ? 'selected' : '' }}>Pagadas</option>
    </select>
    <button type="submit" class="btn btn-primary btn-sm"><i class="bi bi-search"></i>Filtrar</button>
    <a href="{{ route('cobranzas.index') }}" class="btn btn-secondary btn-sm">Limpiar</a>
</form>

<div class="card">
    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>Empresa</th>
                    <th>N° Cuota</th>
                    <th>Concepto</th>
                    <th>Monto</th>
                    <th>Vencimiento</th>
                    <th>Estado</th>
                    @if(auth()->user()->esAdmin())<th class="td-end">Acciones</th>@endif
                </tr>
            </thead>
            <tbody>
                @forelse($cobranzas as $cuota)
                <tr>
                    <td class="fw-600">{{ $cuota->empresa->nombre ?? '—' }}</td>
                    <td class="text-muted">N° {{ $cuota->numero_cuota }}</td>
                    <td class="text-muted">{{ $cuota->concepto ?? '—' }}</td>
                    <td class="fw-700">S/. {{ number_format($cuota->monto, 0, ',', '.') }}</td>
                    <td>
                        {{ $cuota->fecha_vencimiento->format('d/m/Y') }}
                        @if($cuota->estado === 'pendiente' && $cuota->fecha_vencimiento->isPast())
                            <span class="badge badge-danger" style="margin-left:6px">Vencida</span>
                        @endif
                    </td>
                    <td>
                        @if($cuota->estado === 'pagada')
                            <span class="badge badge-success"><i class="bi bi-check-lg"></i>Pagada</span>
                        @else
                            <span class="badge badge-warning">Pendiente</span>
                        @endif
                    </td>
                    @if(auth()->user()->esAdmin())
                    <td class="td-end">
                        <div class="flex flex-center gap-8" style="justify-content:flex-end">
                            @if($cuota->estado === 'pendiente')
                            <form action="{{ route('cobranzas.pagar', $cuota) }}" method="POST">
                                @csrf @method('PATCH')
                                <button class="btn btn-sm btn-success btn-icon" title="Marcar como pagada">
                                    <i class="bi bi-check-lg"></i>
                                </button>
                            </form>
                            @endif
                            <form action="{{ route('cobranzas.destroy', $cuota) }}" method="POST"
                                onsubmit="return confirm('¿Eliminar esta cuota?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-danger btn-icon" title="Eliminar">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                    @endif
                </tr>
                @empty
                <tr><td colspan="7">
                    <div class="empty-state">
                        <i class="bi bi-cash-coin"></i>
                        <p>No hay cuotas registradas</p>
                    </div>
                </td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($cobranzas->hasPages())
    <div class="card-footer">{{ $cobranzas->withQueryString()->links() }}</div>
    @endif
</div>
@endsection
