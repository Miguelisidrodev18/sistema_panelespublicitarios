@extends('layouts.app')

@section('title', 'Cobranzas Pendientes')
@section('subtitle', 'Cuotas pendientes por empresa')

@section('content')
<div class="page-header">
    <div class="page-header-left">
        <a href="{{ route('reportes.index') }}" class="back-btn"><i class="bi bi-arrow-left"></i></a>
        <div class="page-title">Cobranzas Pendientes por Empresa</div>
    </div>
</div>

@forelse($empresas as $empresa)
<div class="card" style="margin-bottom:16px">
    <div class="card-header">
        <span class="fw-700">{{ $empresa->nombre }}</span>
        <span class="badge badge-danger">S/. {{ number_format($empresa->cobranzas->sum('monto'), 0, ',', '.') }}</span>
    </div>
    <div class="table-wrapper">
        <table>
            <thead><tr><th>N° Cuota</th><th>Concepto</th><th>Monto</th><th>Vencimiento</th></tr></thead>
            <tbody>
                @foreach($empresa->cobranzas as $cuota)
                <tr>
                    <td class="fw-600">{{ $cuota->numero_cuota }}</td>
                    <td class="text-muted">{{ $cuota->concepto ?? '—' }}</td>
                    <td class="fw-700">S/. {{ number_format($cuota->monto, 0, ',', '.') }}</td>
                    <td>
                        {{ $cuota->fecha_vencimiento->format('d/m/Y') }}
                        @if($cuota->fecha_vencimiento->isPast())
                            <span class="badge badge-danger" style="margin-left:4px">Vencida</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@empty
<div class="card">
    <div class="empty-state">
        <i class="bi bi-check-circle" style="color:#10B981"></i>
        <p>No hay cobranzas pendientes. ¡Todo al día!</p>
    </div>
</div>
@endforelse
@endsection
