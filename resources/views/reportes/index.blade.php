@extends('layouts.app')

@section('title', 'Reportes')
@section('subtitle', 'Análisis y estadísticas del sistema')

@section('content')
<div class="stats-grid stagger" style="grid-template-columns:repeat(auto-fill,minmax(280px,1fr))">
    <a href="{{ route('reportes.flujo_mensual') }}" class="report-card">
        <i class="bi bi-bar-chart-line" style="color:#2563EB"></i>
        <h6>Flujo Mensual</h6>
        <p>Ingresos y egresos por mes del año</p>
    </a>
    <a href="{{ route('reportes.cobranzas_pendientes') }}" class="report-card">
        <i class="bi bi-exclamation-circle" style="color:#F59E0B"></i>
        <h6>Cobranzas Pendientes</h6>
        <p>Cuotas pendientes agrupadas por empresa</p>
    </a>
    <a href="{{ route('reportes.ingresos_por_empresa') }}" class="report-card">
        <i class="bi bi-building" style="color:#10B981"></i>
        <h6>Ingresos por Empresa</h6>
        <p>Ranking de ingresos por empresa en el año</p>
    </a>
</div>
@endsection
