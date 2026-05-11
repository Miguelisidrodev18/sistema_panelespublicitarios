@extends('layouts.app')

@section('title', 'Reportes')

@section('content')
<div class="row g-3">
    <div class="col-md-4">
        <a href="{{ route('reportes.flujo_mensual') }}" class="text-decoration-none">
            <div class="card border-0 shadow-sm h-100 hover-shadow">
                <div class="card-body text-center py-5">
                    <i class="bi bi-bar-chart-line fs-1 text-primary d-block mb-3"></i>
                    <h6 class="fw-semibold">Flujo Mensual</h6>
                    <p class="text-muted small mb-0">Ingresos y egresos por mes del año</p>
                </div>
            </div>
        </a>
    </div>
    <div class="col-md-4">
        <a href="{{ route('reportes.cobranzas_pendientes') }}" class="text-decoration-none">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center py-5">
                    <i class="bi bi-exclamation-circle fs-1 text-warning d-block mb-3"></i>
                    <h6 class="fw-semibold">Cobranzas Pendientes</h6>
                    <p class="text-muted small mb-0">Cuotas pendientes agrupadas por empresa</p>
                </div>
            </div>
        </a>
    </div>
    <div class="col-md-4">
        <a href="{{ route('reportes.ingresos_por_empresa') }}" class="text-decoration-none">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center py-5">
                    <i class="bi bi-building fs-1 text-success d-block mb-3"></i>
                    <h6 class="fw-semibold">Ingresos por Empresa</h6>
                    <p class="text-muted small mb-0">Ranking de ingresos por empresa en el año</p>
                </div>
            </div>
        </a>
    </div>
</div>
@endsection
