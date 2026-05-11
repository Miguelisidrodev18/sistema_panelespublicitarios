@extends('layouts.app')

@section('title', 'Cobranzas Pendientes')

@section('content')
<div class="d-flex align-items-center mb-3">
    <a href="{{ route('reportes.index') }}" class="btn btn-sm btn-outline-secondary me-3">
        <i class="bi bi-arrow-left"></i>
    </a>
    <h5 class="mb-0 fw-semibold">Cobranzas Pendientes por Empresa</h5>
</div>

@forelse($empresas as $empresa)
<div class="card border-0 shadow-sm mb-3">
    <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
        <span class="fw-semibold">{{ $empresa->nombre }}</span>
        <span class="badge bg-danger">
            S/. {{ number_format($empresa->cobranzas->sum('monto'), 0, ',', '.') }}
        </span>
    </div>
    <div class="table-responsive">
        <table class="table table-sm align-middle mb-0">
            <thead class="table-light">
                <tr><th>N° Cuota</th><th>Concepto</th><th>Monto</th><th>Vencimiento</th></tr>
            </thead>
            <tbody>
                @foreach($empresa->cobranzas as $cuota)
                <tr>
                    <td>{{ $cuota->numero_cuota }}</td>
                    <td>{{ $cuota->concepto ?? '-' }}</td>
                    <td>S/. {{ number_format($cuota->monto, 0, ',', '.') }}</td>
                    <td>
                        {{ $cuota->fecha_vencimiento->format('d/m/Y') }}
                        @if($cuota->fecha_vencimiento->isPast())
                            <span class="badge bg-danger ms-1">Vencida</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@empty
<div class="card border-0 shadow-sm">
    <div class="card-body text-center text-muted py-5">
        <i class="bi bi-check-circle fs-1 text-success d-block mb-3"></i>
        No hay cobranzas pendientes.
    </div>
</div>
@endforelse
@endsection
