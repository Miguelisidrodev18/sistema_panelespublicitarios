@extends('layouts.app')

@section('title', 'Proveedores')
@section('subtitle', 'Gestión de proveedores')

@section('content')
<div class="page-header">
    <div class="page-header-left">
        <span style="font-size:13px;color:var(--text-light);font-weight:500">{{ $proveedores->total() }} proveedor(es)</span>
    </div>
    <a href="{{ route('proveedores.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg"></i>Nuevo Proveedor</a>
</div>

<form class="filter-bar" method="GET" action="{{ route('proveedores.index') }}">
    <input type="text" name="buscar" value="{{ request('buscar') }}" class="form-control" placeholder="Buscar por razón social, RUC o contacto...">
    <select name="estado" class="form-select" style="max-width:160px">
        <option value="">Todos</option>
        <option value="activo" {{ request('estado') === 'activo' ? 'selected' : '' }}>Activos</option>
        <option value="inactivo" {{ request('estado') === 'inactivo' ? 'selected' : '' }}>Inactivos</option>
    </select>
    <button type="submit" class="btn btn-primary"><i class="bi bi-search"></i>Buscar</button>
</form>

<div class="card">
    <div class="card-header"><span><i class="bi bi-truck"></i>Listado de Proveedores</span></div>
    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>Razón Social</th>
                    <th>RUC</th>
                    <th>Contacto</th>
                    <th>Teléfono</th>
                    <th>Rubro</th>
                    <th>Estado</th>
                    <th class="td-end">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($proveedores as $proveedor)
                <tr>
                    <td class="fw-600">{{ $proveedor->razon_social }}</td>
                    <td><code>{{ $proveedor->ruc ?? '—' }}</code></td>
                    <td>{{ $proveedor->contacto ?? '—' }}</td>
                    <td>{{ $proveedor->telefono ?? '—' }}</td>
                    <td>{{ $proveedor->rubro ?? '—' }}</td>
                    <td>
                        <span class="badge badge-{{ $proveedor->estado === 'activo' ? 'success' : 'gray' }}">
                            <i class="bi bi-circle-fill dot"></i>{{ ucfirst($proveedor->estado) }}
                        </span>
                    </td>
                    <td class="td-end">
                        <a href="{{ route('proveedores.show', $proveedor) }}" class="btn btn-sm btn-secondary" title="Ver">
                            <i class="bi bi-eye"></i>
                        </a>
                        <a href="{{ route('proveedores.edit', $proveedor) }}" class="btn btn-sm btn-warning" title="Editar">
                            <i class="bi bi-pencil"></i>
                        </a>
                        @if($proveedor->estado === 'activo')
                        <form action="{{ route('proveedores.destroy', $proveedor) }}" method="POST" style="display:inline"
                            onsubmit="return confirm('¿Desactivar este proveedor?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-danger btn-icon" title="Desactivar"><i class="bi bi-x-lg"></i></button>
                        </form>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center text-muted" style="padding:40px">
                        <i class="bi bi-truck" style="font-size:2rem;display:block;margin-bottom:8px;opacity:.4"></i>
                        No hay proveedores registrados
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{ $proveedores->links() }}
@endsection
