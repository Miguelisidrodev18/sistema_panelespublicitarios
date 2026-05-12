@extends('layouts.app')

@section('title', 'Flujo Mensual ' . $año)
@section('subtitle', 'Ingresos vs Egresos')

@section('content')
<div class="page-header">
    <div class="page-header-left">
        <a href="{{ route('reportes.index') }}" class="back-btn"><i class="bi bi-arrow-left"></i></a>
        <div class="page-title">Flujo Mensual {{ $año }}</div>
    </div>
    <form method="GET">
        <select name="año" class="form-select" style="width:auto" onchange="this.form.submit()">
            @for($y = now()->year; $y >= now()->year - 4; $y--)
            <option value="{{ $y }}" {{ $año == $y ? 'selected' : '' }}>{{ $y }}</option>
            @endfor
        </select>
    </form>
</div>

<div class="card">
    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>Mes</th>
                    <th style="color:#10B981">Ingresos</th>
                    <th style="color:var(--primary)">Egresos</th>
                    <th>Balance</th>
                    <th style="width:30%">Barra</th>
                </tr>
            </thead>
            <tbody>
                @php $maxVal = $meses->max(fn($m) => max($m['ingresos'], $m['egresos'])) ?: 1; @endphp
                @foreach($meses as $mes)
                <tr>
                    <td class="fw-600">{{ ucfirst($mes['nombre']) }}</td>
                    <td style="color:#10B981">S/. {{ number_format($mes['ingresos'], 0, ',', '.') }}</td>
                    <td style="color:var(--primary)">S/. {{ number_format($mes['egresos'], 0, ',', '.') }}</td>
                    <td class="fw-700" style="color:{{ $mes['balance'] >= 0 ? '#10B981' : 'var(--primary)' }}">
                        S/. {{ number_format($mes['balance'], 0, ',', '.') }}
                    </td>
                    <td>
                        <div style="display:flex;flex-direction:column;gap:3px">
                            <div class="progress-bar-wrap"><div class="progress-bar-fill" style="width:{{ ($mes['ingresos'] / $maxVal) * 100 }}%;background:#10B981"></div></div>
                            <div class="progress-bar-wrap"><div class="progress-bar-fill" style="width:{{ ($mes['egresos'] / $maxVal) * 100 }}%"></div></div>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr style="font-weight:700;border-top:2px solid var(--border)">
                    <td>Total</td>
                    <td style="color:#10B981">S/. {{ number_format($meses->sum('ingresos'), 0, ',', '.') }}</td>
                    <td style="color:var(--primary)">S/. {{ number_format($meses->sum('egresos'), 0, ',', '.') }}</td>
                    <td style="color:{{ $meses->sum('balance') >= 0 ? '#10B981' : 'var(--primary)' }}">
                        S/. {{ number_format($meses->sum('balance'), 0, ',', '.') }}
                    </td>
                    <td></td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>
@endsection
