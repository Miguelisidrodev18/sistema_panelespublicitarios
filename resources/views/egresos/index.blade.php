@extends('layouts.app')

@section('title', 'Egresos')
@section('subtitle', 'Registro de salidas de dinero')

@section('content')
<div class="page-header">
    <div class="stat-card" style="padding:16px 24px;gap:14px">
        <div class="stat-icon red" style="width:42px;height:42px;border-radius:10px;font-size:18px">
            <i class="bi bi-arrow-up-circle"></i>
        </div>
        <div>
            <div class="stat-value" style="font-size:20px;color:var(--primary)">S/. {{ number_format($total, 0, ',', '.') }}</div>
            <div class="stat-label">Total filtrado</div>
        </div>
    </div>
    @if(auth()->user()->esAdmin())
    <button class="btn btn-primary" onclick="document.getElementById('modalEgreso').classList.add('open')">
        <i class="bi bi-plus-lg"></i>Registrar Egreso
    </button>
    @endif
</div>

<div class="filter-bar">
    <form style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;width:100%" method="GET">
        @if(auth()->user()->esAdmin())
        <select name="empresa_id" class="form-select" style="max-width:220px">
            <option value="">Todas las empresas</option>
            @foreach($empresas as $e)
            <option value="{{ $e->id }}" {{ request('empresa_id') == $e->id ? 'selected' : '' }}>{{ $e->nombre }}</option>
            @endforeach
        </select>
        @endif
        <input type="text" name="tipo" value="{{ request('tipo') }}" class="form-control" style="max-width:180px" placeholder="Tipo de egreso...">
        <button type="submit" class="btn btn-primary btn-sm"><i class="bi bi-search"></i>Filtrar</button>
        <a href="{{ route('egresos.index') }}" class="btn btn-secondary btn-sm">Limpiar</a>
    </form>
</div>

<div class="card">
    <div class="card-header" style="border-left-color:var(--primary);background:linear-gradient(to right,#FFF5F5,#fff)">
        <span><i class="bi bi-arrow-up-circle" style="color:var(--primary)"></i>Registro de Egresos</span>
    </div>
    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>Fecha</th><th>Empresa</th><th>Tipo</th><th>Concepto</th><th>Monto</th>
                    @if(auth()->user()->esAdmin())<th class="td-end">Acciones</th>@endif
                </tr>
            </thead>
            <tbody>
                @forelse($egresos as $egreso)
                <tr>
                    <td class="text-muted fs-13">{{ $egreso->created_at->format('d/m/Y') }}</td>
                    <td class="fw-600">{{ $egreso->empresa->nombre ?? 'General' }}</td>
                    <td><span class="badge badge-gray">{{ $egreso->tipo }}</span></td>
                    <td class="text-muted">{{ $egreso->concepto ?? '—' }}</td>
                    <td class="fw-700" style="color:var(--primary)">S/. {{ number_format($egreso->monto, 0, ',', '.') }}</td>
                    @if(auth()->user()->esAdmin())
                    <td class="td-end">
                        <form action="{{ route('egresos.destroy', $egreso) }}" method="POST" onsubmit="return confirm('¿Eliminar este egreso?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-danger btn-icon"><i class="bi bi-trash"></i></button>
                        </form>
                    </td>
                    @endif
                </tr>
                @empty
                <tr><td colspan="6"><div class="empty-state"><i class="bi bi-arrow-up-circle"></i><p>No hay egresos registrados</p></div></td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($egresos->hasPages())
    <div class="card-footer">{{ $egresos->withQueryString()->links() }}</div>
    @endif
</div>

@if(auth()->user()->esAdmin())
<div class="modal-backdrop" id="modalEgreso" onclick="if(event.target===this)this.classList.remove('open')">
    <div class="modal-box">
        <form action="{{ route('egresos.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="modal-header">
                <h5><i class="bi bi-arrow-up-circle" style="margin-right:8px;color:var(--primary-light)"></i>Registrar Egreso</h5>
                <button type="button" class="modal-close" onclick="document.getElementById('modalEgreso').classList.remove('open')">×</button>
            </div>
            <div class="modal-body">
                <div class="grid cols-2" style="gap:14px">
                    <div class="form-group" style="grid-column:1/-1">
                        <label class="form-label">Empresa</label>
                        <select name="empresa_id" class="form-select">
                            <option value="">General</option>
                            @foreach($empresas as $e)<option value="{{ $e->id }}">{{ $e->nombre }}</option>@endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Tipo <span class="req">*</span></label>
                        <input type="text" name="tipo" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Monto (S/.) <span class="req">*</span></label>
                        <input type="number" name="monto" class="form-control" step="0.01" required>
                    </div>
                    <div class="form-group" style="grid-column:1/-1">
                        <label class="form-label">Concepto</label>
                        <input type="text" name="concepto" class="form-control">
                    </div>
                    <div class="form-group" style="grid-column:1/-1">
                        <label class="form-label">Comprobante</label>
                        <input type="file" name="comprobante" class="form-control">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="document.getElementById('modalEgreso').classList.remove('open')">Cancelar</button>
                <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg"></i>Guardar</button>
            </div>
        </form>
    </div>
</div>
@endif
@endsection
