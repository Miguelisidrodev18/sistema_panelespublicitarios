@extends('layouts.app')

@section('title', 'Ingresos por Empresa ' . $año)

@section('content')
<div class="d-flex align-items-center justify-content-between mb-3">
    <div class="d-flex align-items-center gap-2">
        <a href="{{ route('reportes.index') }}" class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-arrow-left"></i>
        </a>
        <h5 class="mb-0 fw-semibold">Ingresos por Empresa — {{ $año }}</h5>
    </div>
    <form method="GET" class="d-flex gap-2">
        <select name="año" class="form-select form-select-sm" onchange="this.form.submit()">
            @for($y = now()->year; $y >= now()->year - 4; $y--)
            <option value="{{ $y }}" {{ $año == $y ? 'selected' : '' }}>{{ $y }}</option>
            @endfor
        </select>
    </form>
</div>

<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr><th>#</th><th>Empresa</th><th>Total ingresos</th><th style="width:35%">Barra</th></tr>
            </thead>
            <tbody>
                @php $max = $datos->max('total_ingresos') ?: 1; @endphp
                @forelse($datos as $i => $empresa)
                <tr>
                    <td class="text-muted">{{ $i + 1 }}</td>
                    <td class="fw-medium">{{ $empresa->nombre }}</td>
                    <td class="text-success fw-medium">S/. {{ number_format($empresa->total_ingresos, 0, ',', '.') }}</td>
                    <td>
                        <div class="progress" style="height:12px">
                            <div class="progress-bar bg-success"
                                style="width:{{ ($empresa->total_ingresos / $max) * 100 }}%"></div>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="4" class="text-center text-muted py-4">Sin datos para el año seleccionado</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
