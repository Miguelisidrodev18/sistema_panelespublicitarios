@extends('layouts.app')

@section('title', 'Flujo Mensual ' . $año)

@section('content')
<div class="d-flex align-items-center justify-content-between mb-3">
    <div class="d-flex align-items-center gap-2">
        <a href="{{ route('reportes.index') }}" class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-arrow-left"></i>
        </a>
        <h5 class="mb-0 fw-semibold">Flujo Mensual {{ $año }}</h5>
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
        <table class="table align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Mes</th>
                    <th class="text-success">Ingresos</th>
                    <th class="text-danger">Egresos</th>
                    <th>Balance</th>
                    <th style="width: 30%">Barra</th>
                </tr>
            </thead>
            <tbody>
                @php $maxVal = $meses->max(fn($m) => max($m['ingresos'], $m['egresos'])) ?: 1; @endphp
                @foreach($meses as $mes)
                <tr>
                    <td class="fw-medium">{{ ucfirst($mes['nombre']) }}</td>
                    <td class="text-success">S/. {{ number_format($mes['ingresos'], 0, ',', '.') }}</td>
                    <td class="text-danger">S/. {{ number_format($mes['egresos'], 0, ',', '.') }}</td>
                    <td class="{{ $mes['balance'] >= 0 ? 'text-success' : 'text-danger' }} fw-medium">
                        S/. {{ number_format($mes['balance'], 0, ',', '.') }}
                    </td>
                    <td>
                        <div class="d-flex flex-column gap-1">
                            <div class="progress" style="height:8px">
                                <div class="progress-bar bg-success" style="width:{{ ($mes['ingresos'] / $maxVal) * 100 }}%"></div>
                            </div>
                            <div class="progress" style="height:8px">
                                <div class="progress-bar bg-danger" style="width:{{ ($mes['egresos'] / $maxVal) * 100 }}%"></div>
                            </div>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
            <tfoot class="table-light fw-bold">
                <tr>
                    <td>Total</td>
                    <td class="text-success">S/. {{ number_format($meses->sum('ingresos'), 0, ',', '.') }}</td>
                    <td class="text-danger">S/. {{ number_format($meses->sum('egresos'), 0, ',', '.') }}</td>
                    <td class="{{ $meses->sum('balance') >= 0 ? 'text-success' : 'text-danger' }}">
                        S/. {{ number_format($meses->sum('balance'), 0, ',', '.') }}
                    </td>
                    <td></td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>
@endsection
