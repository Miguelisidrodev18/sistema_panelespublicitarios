@extends('layouts.app')

@section('title', 'Empresas')
@section('subtitle', 'Gestión de clientes y contratos')

@section('content')
<div class="page-header">
    <div class="page-header-left">
        <div>
            <div style="font-size:13px;color:var(--text-light);font-weight:500">
                {{ $empresas->total() }} empresa(s) encontrada(s)
            </div>
        </div>
    </div>
    @if(auth()->user()->esAdmin())
    <a href="{{ route('empresas.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-lg"></i>Nueva Empresa
    </a>
    @endif
</div>

<form class="filter-bar" method="GET">
    <input type="text" name="buscar" value="{{ request('buscar') }}"
        class="form-control" style="max-width:240px" placeholder="Buscar empresa...">
    <select name="estado" class="form-select" style="max-width:160px">
        <option value="">Todos los estados</option>
        <option value="activo"   {{ request('estado') === 'activo'   ? 'selected' : '' }}>Activas</option>
        <option value="inactivo" {{ request('estado') === 'inactivo' ? 'selected' : '' }}>Inactivas</option>
    </select>
    <button type="submit" class="btn btn-primary btn-sm"><i class="bi bi-search"></i>Buscar</button>
    <a href="{{ route('empresas.index') }}" class="btn btn-secondary btn-sm">Limpiar</a>
</form>

<div class="card">
    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>Empresa</th>
                    <th>Encargado</th>
                    <th>Contrato</th>
                    <th>Servicios</th>
                    <th>Monto mensual</th>
                    <th>Estado</th>
                    <th class="td-end">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($empresas as $empresa)
                <tr>
                    <td>
                        <span class="fw-700" style="color:var(--text-dark)">{{ $empresa->nombre }}</span>
                    </td>
                    <td class="text-muted">{{ $empresa->encargado ?? '—' }}</td>
                    <td>
                        @if($empresa->tipo_contrato)
                            @php $tc = ['mensual'=>'info','convenio'=>'purple','eventual'=>'gray'][$empresa->tipo_contrato] ?? 'gray'; @endphp
                            <span class="badge badge-{{ $tc }}">{{ ucfirst($empresa->tipo_contrato) }}</span>
                        @else <span class="text-muted">—</span> @endif
                    </td>
                    <td>
                        @if($empresa->panel_digital)    <span class="badge badge-primary" style="margin-right:3px">Digital</span>@endif
                        @if($empresa->panel_tradicional)<span class="badge badge-gray"    style="margin-right:3px">Tradicional</span>@endif
                        @if($empresa->marketing_digital)<span class="badge badge-success">Marketing</span>@endif
                    </td>
                    <td class="fw-600">S/. {{ number_format($empresa->monto ?? 0, 0, ',', '.') }}</td>
                    <td>
                        @if($empresa->activo)
                            <span class="badge badge-success"><i class="bi bi-circle-fill" style="font-size:7px"></i>Activa</span>
                        @else
                            <span class="badge badge-gray">Inactiva</span>
                        @endif
                    </td>
                    <td class="td-end">
                        <div class="flex flex-center gap-8" style="justify-content:flex-end">
                            <a href="{{ route('empresas.show', $empresa) }}" class="btn btn-sm btn-secondary btn-icon" title="Ver detalle">
                                <i class="bi bi-eye"></i>
                            </a>
                            @if(auth()->user()->esAdmin())
                            <a href="{{ route('empresas.edit', $empresa) }}" class="btn btn-sm btn-warning btn-icon" title="Editar">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form action="{{ route('empresas.destroy', $empresa) }}" method="POST"
                                onsubmit="return confirm('¿Desactivar esta empresa?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-danger btn-icon" title="Desactivar">
                                    <i class="bi bi-x-lg"></i>
                                </button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7">
                    <div class="empty-state">
                        <i class="bi bi-building"></i>
                        <p>No se encontraron empresas</p>
                    </div>
                </td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($empresas->hasPages())
    <div class="card-footer">{{ $empresas->withQueryString()->links() }}</div>
    @endif
</div>
@endsection
