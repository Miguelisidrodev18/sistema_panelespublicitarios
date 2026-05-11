@extends('layouts.app')

@section('title', 'Contratos')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <div></div>
    @if(auth()->user()->esAdmin())
    <a href="{{ route('contratos.create') }}" class="btn btn-danger">
        <i class="bi bi-plus-lg me-1"></i>Nuevo Contrato
    </a>
    @endif
</div>

<div class="card border-0 shadow-sm mb-3">
    <div class="card-body py-2">
        <form class="row g-2" method="GET">
            <div class="col-md-3">
                <select name="estado" class="form-select">
                    <option value="">Todos los estados</option>
                    <option value="activo" {{ request('estado') === 'activo' ? 'selected' : '' }}>Activo</option>
                    <option value="finalizado" {{ request('estado') === 'finalizado' ? 'selected' : '' }}>Finalizado</option>
                    <option value="cancelado" {{ request('estado') === 'cancelado' ? 'selected' : '' }}>Cancelado</option>
                </select>
            </div>
            @if(auth()->user()->esAdmin())
            <div class="col-md-4">
                <select name="empresa_id" class="form-select">
                    <option value="">Todas las empresas</option>
                    @foreach($empresas as $empresa)
                        <option value="{{ $empresa->id }}" {{ request('empresa_id') == $empresa->id ? 'selected' : '' }}>
                            {{ $empresa->nombre }}
                        </option>
                    @endforeach
                </select>
            </div>
            @endif
            <div class="col-auto">
                <button type="submit" class="btn btn-outline-primary"><i class="bi bi-search"></i> Filtrar</button>
                <a href="{{ route('contratos.index') }}" class="btn btn-outline-secondary ms-1">Limpiar</a>
            </div>
        </form>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>N° Contrato</th>
                    <th>Contratante / Empresa</th>
                    <th>Tipo</th>
                    <th>Monto total</th>
                    <th>Saldo pendiente</th>
                    <th>Vigencia</th>
                    <th>Estado</th>
                    <th>Deuda</th>
                    <th class="text-end">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($contratos as $contrato)
                <tr>
                    <td class="fw-medium">{{ $contrato->numero_contrato }}</td>
                    <td>
                        <div class="fw-medium">{{ $contrato->contratante }}</div>
                        @if($contrato->empresa)
                            <div class="small text-muted">{{ $contrato->empresa->nombre }}</div>
                        @endif
                    </td>
                    <td>{{ $contrato->tipo_contrato }}</td>
                    <td>S/. {{ number_format($contrato->monto_total, 0, ',', '.') }}</td>
                    <td class="{{ $contrato->saldo_pendiente > 0 ? 'text-danger fw-medium' : 'text-success' }}">
                        S/. {{ number_format($contrato->saldo_pendiente, 0, ',', '.') }}
                    </td>
                    <td>
                        @if($contrato->fecha_inicio && $contrato->fecha_fin)
                            <div class="small">{{ $contrato->fecha_inicio->format('d/m/Y') }}</div>
                            <div class="small">al {{ $contrato->fecha_fin->format('d/m/Y') }}</div>
                            @if($contrato->estado === 'activo' && $contrato->fecha_fin->isPast())
                                <span class="badge bg-danger">Vencido</span>
                            @elseif($contrato->estado === 'activo' && $contrato->fecha_fin->diffInDays(now()) <= 30)
                                <span class="badge bg-warning text-dark">Por vencer</span>
                            @endif
                        @else
                            <span class="text-muted">-</span>
                        @endif
                    </td>
                    <td>
                        @if($contrato->estado === 'activo')
                            <span class="badge bg-success">Activo</span>
                        @elseif($contrato->estado === 'finalizado')
                            <span class="badge bg-secondary">Finalizado</span>
                        @else
                            <span class="badge bg-danger">Cancelado</span>
                        @endif
                    </td>
                    <td>
                        @php
                            $ed = $contrato->estado_deuda;
                            $bc = match($ed) {
                                'Moroso'    => 'danger',
                                'Al día'    => 'success',
                                'Cancelado' => 'secondary',
                                default     => 'warning text-dark',
                            };
                        @endphp
                        <span class="badge bg-{{ $bc }}">{{ $ed }}</span>
                    </td>
                    <td class="text-end">
                        <a href="{{ route('contratos.show', $contrato) }}" class="btn btn-sm btn-outline-primary me-1">
                            <i class="bi bi-eye"></i>
                        </a>
                        @if(auth()->user()->esAdmin())
                        <a href="{{ route('contratos.edit', $contrato) }}" class="btn btn-sm btn-outline-warning">
                            <i class="bi bi-pencil"></i>
                        </a>
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="9" class="text-center text-muted py-4">No hay contratos registrados</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($contratos->hasPages())
    <div class="card-footer bg-white">{{ $contratos->withQueryString()->links() }}</div>
    @endif
</div>
@endsection
