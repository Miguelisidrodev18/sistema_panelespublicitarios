@extends('layouts.app')

@section('title', 'Cobranzas')
@section('subtitle', 'Control de cuotas y pagos')

@section('content')
<div class="page-header">
    <div class="page-header-left">
        <span style="font-size:13px;color:var(--text-light);font-weight:500">{{ $cobranzas->total() }} cuota(s)</span>
    </div>
</div>

<div class="filter-bar">
    <form style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;width:100%" method="GET">
        <input type="text" name="buscar" value="{{ request('buscar') }}" class="form-control" style="max-width:250px" placeholder="Buscar empresa...">
        <select name="estado" class="form-select" style="max-width:160px">
            <option value="">Todos los estados</option>
            <option value="pendiente" {{ request('estado') === 'pendiente' ? 'selected' : '' }}>Pendiente</option>
            <option value="pagada" {{ request('estado') === 'pagada' ? 'selected' : '' }}>Pagada</option>
            <option value="vencida" {{ request('estado') === 'vencida' ? 'selected' : '' }}>Vencida</option>
        </select>
        <input type="month" name="mes" value="{{ request('mes') }}" class="form-control" style="max-width:160px">
        <button type="submit" class="btn btn-primary btn-sm"><i class="bi bi-search"></i>Filtrar</button>
        <a href="{{ route('cobranzas.index') }}" class="btn btn-secondary btn-sm">Limpiar</a>
    </form>
</div>

<div class="card">
    <div class="card-header ch-green">
        <span><i class="bi bi-cash-coin"></i>Registro de Cobranzas</span>
        <span style="font-size:12px;font-weight:500;color:var(--text-light)">{{ $cobranzas->total() }} cuota(s)</span>
    </div>
    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>Empresa</th><th>N° Cuota</th><th>Concepto</th><th>Monto</th>
                    <th>Vencimiento</th><th>Estado</th>
                    @if(auth()->user()->esAdmin())<th class="td-end">Acciones</th>@endif
                </tr>
            </thead>
            <tbody>
                @forelse($cobranzas as $cob)
                <tr>
                    <td class="fw-700" style="color:var(--text-dark)">
                        @if($cob->empresa)
                            <a href="{{ route('empresas.show', $cob->empresa) }}" style="color:var(--text-dark);text-decoration:none">{{ $cob->empresa->nombre }}</a>
                        @else {{ $cob->empresa_nombre ?? '—' }} @endif
                    </td>
                    <td class="fw-600">{{ $cob->numero_cuota }}</td>
                    <td class="text-muted">{{ $cob->concepto ?? '—' }}</td>
                    <td class="fw-700">S/. {{ number_format($cob->monto, 0, ',', '.') }}</td>
                    <td>
                        {{ $cob->fecha_vencimiento->format('d/m/Y') }}
                        @if($cob->estado === 'pendiente' && $cob->fecha_vencimiento->isPast())
                            <span class="badge badge-danger" style="margin-left:4px">Vencida</span>
                        @endif
                    </td>
                    <td>
                        @if($cob->estado === 'pagada')
                            <span class="badge badge-success"><i class="bi bi-check-lg"></i>Pagada</span>
                        @else
                            <span class="badge badge-warning">Pendiente</span>
                        @endif
                    </td>
                    @if(auth()->user()->esAdmin())
                    <td class="td-end">
                        <div class="flex flex-center gap-8" style="justify-content:flex-end">
                            @if($cob->estado === 'pendiente')
                            <form action="{{ route('cobranzas.pagar', $cob) }}" method="POST" onsubmit="return confirm('¿Marcar como pagada?')">
                                @csrf @method('PATCH')
                                <button class="btn btn-sm btn-success btn-icon" title="Marcar pagada"><i class="bi bi-check-lg"></i></button>
                            </form>
                            @endif
                            <form action="{{ route('cobranzas.destroy', $cob) }}" method="POST" onsubmit="return confirm('¿Eliminar esta cuota?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-danger btn-icon"><i class="bi bi-trash"></i></button>
                            </form>
                        </div>
                    </td>
                    @endif
                </tr>
                @empty
                <tr><td colspan="7"><div class="empty-state"><i class="bi bi-cash-coin"></i><p>No hay cuotas registradas</p></div></td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($cobranzas->hasPages())
    <div class="card-footer">{{ $cobranzas->withQueryString()->links() }}</div>
    @endif
</div>
@endsection
