@extends('layouts.app')

@section('title', 'Deuda: ' . $deuda->acreedor)
@section('subtitle', 'Detalle y pagos')

@section('content')
<div class="page-header">
    <div class="page-header-left">
        <a href="{{ route('deudas.index') }}" class="back-btn"><i class="bi bi-arrow-left"></i></a>
        <div>
            <div class="page-title">{{ $deuda->acreedor }}</div>
            <div style="margin-top:4px">
                @php $prioColors = ['alta'=>'danger','media'=>'warning','baja'=>'gray']; @endphp
                <span class="badge badge-{{ $prioColors[$deuda->prioridad] ?? 'gray' }}">{{ ucfirst($deuda->prioridad) }}</span>
                @if($deuda->estado === 'pagada')
                    <span class="badge badge-success">Pagada</span>
                @elseif($deuda->estado === 'cancelada')
                    <span class="badge badge-gray">Cancelada</span>
                @else
                    <span class="badge badge-warning">Pendiente</span>
                @endif
            </div>
        </div>
    </div>
    <a href="{{ route('deudas.edit', $deuda) }}" class="btn btn-warning"><i class="bi bi-pencil"></i>Editar</a>
</div>

<div class="row g-3">
    <div class="col-lg-4">
        <div class="card" style="margin-bottom:20px">
            <div class="card-header"><span><i class="bi bi-info-circle" style="color:var(--primary);margin-right:8px"></i>Información</span></div>
            <div class="card-body">
                <div class="detail-grid">
                    <div class="detail-row"><div class="detail-label">Concepto</div><div class="detail-value">{{ $deuda->concepto }}</div></div>
                    <div class="detail-row"><div class="detail-label">Monto total</div><div class="detail-value fw-700">S/. {{ number_format($deuda->monto, 0, ',', '.') }}</div></div>
                    <div class="detail-row"><div class="detail-label">Pendiente</div><div class="detail-value fw-800" style="color:var(--primary)">S/. {{ number_format($deuda->monto_pendiente, 0, ',', '.') }}</div></div>
                    <div class="detail-row"><div class="detail-label">Fecha deuda</div><div class="detail-value">{{ $deuda->fecha_deuda->format('d/m/Y') }}</div></div>
                    <div class="detail-row"><div class="detail-label">Vencimiento</div><div class="detail-value">{{ $deuda->fecha_vencimiento?->format('d/m/Y') ?? '—' }}</div></div>
                </div>
                @if($deuda->notas)
                    <div style="margin-top:12px;padding-top:10px;border-top:1px solid var(--border);font-size:13px;color:var(--text-light)">{{ $deuda->notas }}</div>
                @endif
            </div>
        </div>

        @if($deuda->estado === 'pendiente')
        <div class="card">
            <div class="card-header"><span><i class="bi bi-cash-coin" style="color:#10B981;margin-right:8px"></i>Registrar Pago</span></div>
            <div class="card-body">
                <form action="{{ route('deudas.pago', $deuda) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="form-group">
                        <label class="form-label">Monto (S/.) <span class="req">*</span></label>
                        <input type="number" name="monto" class="form-control" step="0.01" max="{{ $deuda->monto_pendiente }}" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Fecha pago <span class="req">*</span></label>
                        <input type="date" name="fecha_pago" class="form-control" value="{{ date('Y-m-d') }}" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Método</label>
                        <select name="metodo_pago" class="form-select">
                            <option value="">Seleccionar...</option>
                            <option>Efectivo</option><option>Transferencia</option><option>Cheque</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Comprobante</label>
                        <input type="file" name="comprobante" class="form-control">
                    </div>
                    <button type="submit" class="btn btn-success w-100"><i class="bi bi-check-lg"></i>Registrar Pago</button>
                </form>
            </div>
        </div>
        @endif
    </div>

    <div class="col-lg-8">
        <div class="card">
            <div class="card-header"><span><i class="bi bi-clock-history" style="color:var(--primary);margin-right:8px"></i>Historial de pagos</span></div>
            <div class="table-wrapper">
                <table>
                    <thead><tr><th>Fecha</th><th>Monto</th><th>Método</th><th>Notas</th><th>Comprobante</th></tr></thead>
                    <tbody>
                        @forelse($deuda->pagos as $pago)
                        <tr>
                            <td>{{ $pago->fecha_pago->format('d/m/Y') }}</td>
                            <td class="text-success fw-700">S/. {{ number_format($pago->monto, 0, ',', '.') }}</td>
                            <td class="text-muted">{{ $pago->metodo_pago ?? '—' }}</td>
                            <td class="text-muted" style="font-size:13px">{{ $pago->notas ?? '—' }}</td>
                            <td>
                                @if($pago->comprobante)
                                <a href="{{ Storage::url($pago->comprobante) }}" target="_blank" class="btn btn-xs btn-outline"><i class="bi bi-file-earmark"></i></a>
                                @else — @endif
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="5"><div class="empty-state" style="padding:32px"><i class="bi bi-clock-history"></i><p>Sin pagos registrados</p></div></td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
