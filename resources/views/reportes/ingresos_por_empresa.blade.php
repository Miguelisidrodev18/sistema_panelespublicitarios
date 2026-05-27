@extends('layouts.app')

@section('title', 'Ingresos por Empresa ' . $año)
@section('subtitle', 'Ranking de ingresos')

@section('content')
<div class="page-header">
    <div class="page-header-left">
        <a href="{{ route('reportes.index') }}" class="back-btn"><i class="bi bi-arrow-left"></i></a>
        <div class="page-title">Ingresos por Empresa — {{ $año }}</div>
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
    <div class="card-header ch-green">
        <span><i class="bi bi-trophy"></i>Ranking de Ingresos por Empresa</span>
        <span style="font-size:12px;font-weight:600;color:var(--text-light)">Año {{ $año }}</span>
    </div>
    <div class="table-wrapper">
        <table>
            <thead><tr><th>#</th><th>Empresa</th><th>Total ingresos</th><th style="width:35%">Barra</th></tr></thead>
            <tbody>
                @php $max = $datos->max('total_ingresos') ?: 1; @endphp
                @forelse($datos as $i => $empresa)
                <tr>
                    @if($i === 0)
                        <td><span style="font-size:18px">🥇</span></td>
                    @elseif($i === 1)
                        <td><span style="font-size:18px">🥈</span></td>
                    @elseif($i === 2)
                        <td><span style="font-size:18px">🥉</span></td>
                    @else
                        <td class="text-muted">{{ $i + 1 }}</td>
                    @endif
                    <td class="fw-700" style="color:var(--text-dark)">{{ $empresa->nombre }}</td>
                    <td class="fw-700" style="color:#10B981">S/. {{ number_format($empresa->total_ingresos, 0, ',', '.') }}</td>
                    <td>
                        <div class="progress-bar-wrap" style="height:12px">
                            <div class="progress-bar-fill" style="width:{{ ($empresa->total_ingresos / $max) * 100 }}%;background:#10B981"></div>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="4"><div class="empty-state" style="padding:32px"><i class="bi bi-building"></i><p>Sin datos para el año seleccionado</p></div></td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
