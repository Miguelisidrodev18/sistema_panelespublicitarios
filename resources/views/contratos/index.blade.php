@extends('layouts.app')

@section('title', 'Contratos')
@section('subtitle', 'Gestión de contratos y convenios')

@section('content')
<div class="page-header">
    <div class="page-header-left">
        <div style="font-size:13px;color:var(--text-light);font-weight:500">
            {{ $contratos->total() }} contrato(s) registrado(s)
        </div>
    </div>
    @if(auth()->user()->esAdmin())
    <a href="{{ route('contratos.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-lg"></i>Nuevo Contrato
    </a>
    @endif
</div>

<div class="filter-bar">
    <form style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;width:100%" method="GET">
        <select name="estado" class="form-select" style="max-width:170px">
            <option value="">Todos los estados</option>
            <option value="activo" {{ request('estado') === 'activo' ? 'selected' : '' }}>Activo</option>
            <option value="finalizado" {{ request('estado') === 'finalizado' ? 'selected' : '' }}>Finalizado</option>
            <option value="cancelado" {{ request('estado') === 'cancelado' ? 'selected' : '' }}>Cancelado</option>
        </select>
        @if(auth()->user()->esAdmin())
        <select name="empresa_id" class="form-select" style="max-width:220px">
            <option value="">Todas las empresas</option>
            @foreach($empresas as $empresa)
                <option value="{{ $empresa->id }}" {{ request('empresa_id') == $empresa->id ? 'selected' : '' }}>
                    {{ $empresa->nombre }}
                </option>
            @endforeach
        </select>
        @endif
        <button type="submit" class="btn btn-primary btn-sm"><i class="bi bi-search"></i>Filtrar</button>
        <a href="{{ route('contratos.index') }}" class="btn btn-secondary btn-sm">Limpiar</a>
    </form>
</div>

<div class="card">
    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>N° Contrato</th>
                    <th>Contratante / Empresa</th>
                    <th>Tipo</th>
                    <th>Monto total</th>
                    <th>Saldo pendiente</th>
                    <th>Vigencia</th>
                    <th>Estado</th>
                    <th>Deuda</th>
                    <th class="td-end">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($contratos as $contrato)
                <tr>
                    <td class="fw-700" style="color:var(--text-dark)">{{ $contrato->numero_contrato }}</td>
                    <td>
                        <div class="fw-600">{{ $contrato->contratante }}</div>
                        @if($contrato->empresa)
                            <div class="text-muted" style="font-size:12px">{{ $contrato->empresa->nombre }}</div>
                        @endif
                    </td>
                    <td>
                        @if($contrato->tipo_contrato)
                            <span class="badge badge-info">{{ $contrato->tipo_contrato }}</span>
                        @else <span class="text-muted">—</span> @endif
                    </td>
                    <td class="fw-600">S/. {{ number_format($contrato->monto_total, 0, ',', '.') }}</td>
                    <td class="fw-700" style="color:{{ $contrato->saldo_pendiente > 0 ? 'var(--primary)' : '#059669' }}">
                        S/. {{ number_format($contrato->saldo_pendiente, 0, ',', '.') }}
                    </td>
                    <td>
                        @if($contrato->fecha_inicio && $contrato->fecha_fin)
                            <div style="font-size:12.5px">{{ $contrato->fecha_inicio->format('d/m/Y') }}</div>
                            <div style="font-size:12px;color:var(--text-light)">al {{ $contrato->fecha_fin->format('d/m/Y') }}</div>
                            @if($contrato->estado === 'activo' && $contrato->fecha_fin->isPast())
                                <span class="badge badge-danger" style="margin-top:3px">Vencido</span>
                            @elseif($contrato->estado === 'activo' && $contrato->fecha_fin->diffInDays(now()) <= 30)
                                <span class="badge badge-warning" style="margin-top:3px">Por vencer</span>
                            @endif
                        @else
                            <span class="text-muted">—</span>
                        @endif
                    </td>
                    <td>
                        @if($contrato->estado === 'activo')
                            <span class="badge badge-success"><i class="bi bi-circle-fill dot"></i>Activo</span>
                        @elseif($contrato->estado === 'finalizado')
                            <span class="badge badge-gray">Finalizado</span>
                        @else
                            <span class="badge badge-danger">Cancelado</span>
                        @endif
                    </td>
                    <td>
                        @php
                            $ed = $contrato->estado_deuda;
                            $bc = match($ed) {
                                'Moroso'    => 'danger',
                                'Al día'    => 'success',
                                'Cancelado' => 'gray',
                                default     => 'warning',
                            };
                        @endphp
                        <span class="badge badge-{{ $bc }}">{{ $ed }}</span>
                    </td>
                    <td class="td-end">
                        <div class="flex flex-center gap-8" style="justify-content:flex-end">
                            <a href="{{ route('contratos.show', $contrato) }}" class="btn btn-sm btn-secondary btn-icon" title="Ver">
                                <i class="bi bi-eye"></i>
                            </a>
                            @if(auth()->user()->esAdmin())
                            <a href="{{ route('contratos.edit', $contrato) }}" class="btn btn-sm btn-warning btn-icon" title="Editar">
                                <i class="bi bi-pencil"></i>
                            </a>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="9">
                    <div class="empty-state">
                        <i class="bi bi-file-earmark-text"></i>
                        <p>No hay contratos registrados</p>
                    </div>
                </td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($contratos->hasPages())
    <div class="card-footer">{{ $contratos->withQueryString()->links() }}</div>
    @endif
</div>
@endsection
