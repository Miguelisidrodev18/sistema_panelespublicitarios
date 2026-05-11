@extends('layouts.app')

@section('title', 'Ingresos')
@section('subtitle', 'Registro de entradas de dinero')

@section('content')
<div class="page-header">
    <div class="stat-card" style="padding:16px 24px;gap:14px">
        <div class="stat-icon green" style="width:42px;height:42px;border-radius:10px;font-size:18px">
            <i class="bi bi-arrow-down-circle"></i>
        </div>
        <div>
            <div class="stat-value" style="font-size:20px;color:#059669">S/. {{ number_format($total, 0, ',', '.') }}</div>
            <div class="stat-label">Total filtrado</div>
        </div>
    </div>
    @if(auth()->user()->esAdmin())
    <button class="btn btn-success" onclick="document.getElementById('modalIngreso').classList.add('open')">
        <i class="bi bi-plus-lg"></i>Registrar Ingreso
    </button>
    @endif
</div>

<form class="filter-bar" method="GET">
    @if(auth()->user()->esAdmin())
    <select name="empresa_id" class="form-select" style="max-width:220px">
        <option value="">Todas las empresas</option>
        @foreach($empresas as $e)
        <option value="{{ $e->id }}" {{ request('empresa_id') == $e->id ? 'selected' : '' }}>{{ $e->nombre }}</option>
        @endforeach
    </select>
    @endif
    <input type="text" name="tipo" value="{{ request('tipo') }}" class="form-control" style="max-width:180px" placeholder="Tipo de ingreso...">
    <button type="submit" class="btn btn-primary btn-sm"><i class="bi bi-search"></i>Filtrar</button>
    <a href="{{ route('ingresos.index') }}" class="btn btn-secondary btn-sm">Limpiar</a>
</form>

<div class="card">
    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>Fecha</th>
                    <th>Empresa</th>
                    <th>Tipo</th>
                    <th>Concepto</th>
                    <th>Método</th>
                    <th>Monto</th>
                    @if(auth()->user()->esAdmin())<th class="td-end">Acción</th>@endif
                </tr>
            </thead>
            <tbody>
                @forelse($ingresos as $ingreso)
                <tr>
                    <td class="text-muted fs-13">{{ $ingreso->created_at->format('d/m/Y') }}</td>
                    <td class="fw-600">{{ $ingreso->empresa->nombre ?? 'General' }}</td>
                    <td><span class="badge badge-gray">{{ $ingreso->tipo }}</span></td>
                    <td class="text-muted">{{ $ingreso->concepto ?? '—' }}</td>
                    <td class="text-muted">{{ $ingreso->metodo_pago ?? '—' }}</td>
                    <td class="fw-700 text-success">S/. {{ number_format($ingreso->monto, 0, ',', '.') }}</td>
                    @if(auth()->user()->esAdmin())
                    <td class="td-end">
                        <form action="{{ route('ingresos.destroy', $ingreso) }}" method="POST"
                            onsubmit="return confirm('¿Eliminar este ingreso?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-danger btn-icon"><i class="bi bi-trash"></i></button>
                        </form>
                    </td>
                    @endif
                </tr>
                @empty
                <tr><td colspan="7">
                    <div class="empty-state"><i class="bi bi-arrow-down-circle"></i><p>Sin ingresos registrados</p></div>
                </td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($ingresos->hasPages())
    <div class="card-footer">{{ $ingresos->withQueryString()->links() }}</div>
    @endif
</div>

@if(auth()->user()->esAdmin())
<div class="modal-backdrop" id="modalIngreso" onclick="if(event.target===this)this.classList.remove('open')">
    <div class="modal-box">
        <form action="{{ route('ingresos.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="modal-header">
                <h5><i class="bi bi-arrow-down-circle" style="margin-right:8px;color:var(--primary-light)"></i>Registrar Ingreso</h5>
                <button type="button" class="modal-close" onclick="document.getElementById('modalIngreso').classList.remove('open')">×</button>
            </div>
            <div class="modal-body">
                <div class="grid cols-2" style="gap:14px">
                    <div class="form-group" style="grid-column:1/-1">
                        <label class="form-label">Empresa</label>
                        <select name="empresa_id" class="form-select">
                            <option value="">General (sin empresa)</option>
                            @foreach($empresas as $e)
                            <option value="{{ $e->id }}">{{ $e->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Tipo <span class="req">*</span></label>
                        <input type="text" name="tipo" class="form-control" placeholder="Ej: Cuota, Adelanto..." required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Método de pago</label>
                        <select name="metodo_pago" class="form-select">
                            <option value="">—</option>
                            <option>Efectivo</option>
                            <option>Transferencia</option>
                            <option>Cheque</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Monto (S/.) <span class="req">*</span></label>
                        <input type="number" name="monto" class="form-control" step="0.01" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Comprobante</label>
                        <input type="file" name="comprobante" class="form-control">
                    </div>
                    <div class="form-group" style="grid-column:1/-1">
                        <label class="form-label">Concepto</label>
                        <input type="text" name="concepto" class="form-control">
                    </div>
                    <div class="form-group" style="grid-column:1/-1">
                        <label class="form-check">
                            <input type="checkbox" name="va_a_general" value="1" class="form-check-input">
                            <span class="form-check-label">Va a caja general</span>
                        </label>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="document.getElementById('modalIngreso').classList.remove('open')">Cancelar</button>
                <button type="submit" class="btn btn-success"><i class="bi bi-check-lg"></i>Guardar</button>
            </div>
        </form>
    </div>
</div>
@endif
@endsection
