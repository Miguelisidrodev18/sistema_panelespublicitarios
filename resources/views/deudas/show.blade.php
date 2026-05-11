@extends('layouts.app')

@section('title', 'Deuda: ' . $deuda->acreedor)

@section('content')
<div class="d-flex align-items-center justify-content-between mb-3">
    <div class="d-flex align-items-center gap-2">
        <a href="{{ route('deudas.index') }}" class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-arrow-left"></i>
        </a>
        <h5 class="mb-0 fw-semibold">{{ $deuda->acreedor }}</h5>
    </div>
    <a href="{{ route('deudas.edit', $deuda) }}" class="btn btn-warning">
        <i class="bi bi-pencil me-1"></i>Editar
    </a>
</div>

<div class="row g-3">
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header bg-white fw-medium py-3">Información</div>
            <div class="card-body">
                <table class="table table-sm table-borderless mb-0">
                    <tr><th>Concepto</th><td>{{ $deuda->concepto }}</td></tr>
                    <tr><th>Monto total</th><td>S/. {{ number_format($deuda->monto, 0, ',', '.') }}</td></tr>
                    <tr><th>Pendiente</th>
                        <td class="fw-bold text-danger">S/. {{ number_format($deuda->monto_pendiente, 0, ',', '.') }}</td>
                    </tr>
                    <tr><th>Fecha deuda</th><td>{{ $deuda->fecha_deuda->format('d/m/Y') }}</td></tr>
                    <tr><th>Vencimiento</th><td>{{ $deuda->fecha_vencimiento?->format('d/m/Y') ?? '-' }}</td></tr>
                    <tr><th>Prioridad</th><td>{{ ucfirst($deuda->prioridad) }}</td></tr>
                    <tr><th>Estado</th><td>{{ ucfirst($deuda->estado) }}</td></tr>
                </table>
                @if($deuda->notas)
                    <div class="mt-2 small text-muted">{{ $deuda->notas }}</div>
                @endif
            </div>
        </div>

        @if($deuda->estado === 'pendiente')
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white fw-medium py-3">Registrar Pago</div>
            <div class="card-body">
                <form action="{{ route('deudas.pago', $deuda) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Monto (S/.) <span class="text-danger">*</span></label>
                        <input type="number" name="monto" class="form-control" step="0.01"
                            max="{{ $deuda->monto_pendiente }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Fecha pago <span class="text-danger">*</span></label>
                        <input type="date" name="fecha_pago" class="form-control" value="{{ date('Y-m-d') }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Método</label>
                        <select name="metodo_pago" class="form-select">
                            <option value="">Seleccionar...</option>
                            <option>Efectivo</option>
                            <option>Transferencia</option>
                            <option>Cheque</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Comprobante</label>
                        <input type="file" name="comprobante" class="form-control">
                    </div>
                    <button type="submit" class="btn btn-success w-100">
                        <i class="bi bi-check-lg me-1"></i>Registrar Pago
                    </button>
                </form>
            </div>
        </div>
        @endif
    </div>

    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white fw-medium py-3">Historial de pagos</div>
            <div class="table-responsive">
                <table class="table table-sm table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr><th>Fecha</th><th>Monto</th><th>Método</th><th>Notas</th><th>Comprobante</th></tr>
                    </thead>
                    <tbody>
                        @forelse($deuda->pagos as $pago)
                        <tr>
                            <td>{{ $pago->fecha_pago->format('d/m/Y') }}</td>
                            <td class="text-success fw-medium">S/. {{ number_format($pago->monto, 0, ',', '.') }}</td>
                            <td>{{ $pago->metodo_pago ?? '-' }}</td>
                            <td>{{ $pago->notas ?? '-' }}</td>
                            <td>
                                @if($pago->comprobante)
                                <a href="{{ Storage::url($pago->comprobante) }}" target="_blank" class="btn btn-xs btn-outline-primary">
                                    <i class="bi bi-file"></i>
                                </a>
                                @else -
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="5" class="text-center text-muted py-3">Sin pagos registrados</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
