@extends('layouts.app')

@section('title', 'Egresos')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <div class="card border-0 shadow-sm px-4 py-3">
        <div class="text-muted small">Total filtrado</div>
        <div class="fs-5 fw-bold text-danger">S/. {{ number_format($total, 0, ',', '.') }}</div>
    </div>
    @if(auth()->user()->esAdmin())
    <button class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#modalEgreso">
        <i class="bi bi-plus-lg me-1"></i>Registrar Egreso
    </button>
    @endif
</div>

<div class="card border-0 shadow-sm mb-3">
    <div class="card-body py-2">
        <form class="row g-2" method="GET">
            @if(auth()->user()->esAdmin())
            <div class="col-md-4">
                <select name="empresa_id" class="form-select">
                    <option value="">Todas las empresas</option>
                    @foreach($empresas as $e)
                    <option value="{{ $e->id }}" {{ request('empresa_id') == $e->id ? 'selected' : '' }}>{{ $e->nombre }}</option>
                    @endforeach
                </select>
            </div>
            @endif
            <div class="col-md-3">
                <input type="text" name="tipo" value="{{ request('tipo') }}" class="form-control" placeholder="Tipo de egreso...">
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-outline-primary"><i class="bi bi-search"></i> Filtrar</button>
                <a href="{{ route('egresos.index') }}" class="btn btn-outline-secondary ms-1">Limpiar</a>
            </div>
        </form>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Fecha</th>
                    <th>Empresa</th>
                    <th>Tipo</th>
                    <th>Concepto</th>
                    <th>Monto</th>
                    @if(auth()->user()->esAdmin())<th class="text-end">Acciones</th>@endif
                </tr>
            </thead>
            <tbody>
                @forelse($egresos as $egreso)
                <tr>
                    <td>{{ $egreso->created_at->format('d/m/Y') }}</td>
                    <td>{{ $egreso->empresa->nombre ?? 'General' }}</td>
                    <td><span class="badge bg-light text-dark border">{{ $egreso->tipo }}</span></td>
                    <td>{{ $egreso->concepto ?? '-' }}</td>
                    <td class="fw-medium text-danger">S/. {{ number_format($egreso->monto, 0, ',', '.') }}</td>
                    @if(auth()->user()->esAdmin())
                    <td class="text-end">
                        <form action="{{ route('egresos.destroy', $egreso) }}" method="POST" class="d-inline"
                            onsubmit="return confirm('¿Eliminar este egreso?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                        </form>
                    </td>
                    @endif
                </tr>
                @empty
                <tr><td colspan="6" class="text-center text-muted py-4">No hay egresos registrados</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($egresos->hasPages())
    <div class="card-footer bg-white">{{ $egresos->withQueryString()->links() }}</div>
    @endif
</div>

@if(auth()->user()->esAdmin())
<div class="modal fade" id="modalEgreso" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('egresos.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h6 class="modal-title">Registrar Egreso</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body row g-3">
                    <div class="col-12">
                        <label class="form-label">Empresa</label>
                        <select name="empresa_id" class="form-select">
                            <option value="">General</option>
                            @foreach($empresas as $e)
                            <option value="{{ $e->id }}">{{ $e->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-6">
                        <label class="form-label">Tipo <span class="text-danger">*</span></label>
                        <input type="text" name="tipo" class="form-control" required>
                    </div>
                    <div class="col-6">
                        <label class="form-label">Monto (S/.) <span class="text-danger">*</span></label>
                        <input type="number" name="monto" class="form-control" step="0.01" required>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Concepto</label>
                        <input type="text" name="concepto" class="form-control">
                    </div>
                    <div class="col-12">
                        <label class="form-label">Comprobante</label>
                        <input type="file" name="comprobante" class="form-control">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-danger">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
@endsection
