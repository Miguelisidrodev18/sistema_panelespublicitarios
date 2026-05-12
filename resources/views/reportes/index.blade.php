@extends('layouts.app')

@section('title', 'Reportes')
@section('subtitle', 'Análisis y estadísticas del sistema')

@section('content')
<div class="stats-grid stagger" style="grid-template-columns:repeat(auto-fill,minmax(260px,1fr))">
    <a href="{{ route('reportes.flujo_mensual') }}" class="report-card">
        <i class="bi bi-bar-chart-line" style="color:#2563EB;background:linear-gradient(135deg,#DBEAFE,#BFDBFE);width:68px;height:68px;font-size:28px!important;border-radius:50%;display:inline-flex!important;align-items:center;justify-content:center;margin-bottom:16px"></i>
        <h6>Flujo Mensual</h6>
        <p>Ingresos y egresos por mes del año</p>
    </a>
    <a href="{{ route('reportes.cobranzas_pendientes') }}" class="report-card">
        <i class="bi bi-exclamation-circle" style="color:#D97706;background:linear-gradient(135deg,#FEF3C7,#FDE68A);width:68px;height:68px;font-size:28px!important;border-radius:50%;display:inline-flex!important;align-items:center;justify-content:center;margin-bottom:16px"></i>
        <h6>Cobranzas Pendientes</h6>
        <p>Cuotas pendientes agrupadas por empresa</p>
    </a>
    <a href="{{ route('reportes.ingresos_por_empresa') }}" class="report-card">
        <i class="bi bi-building" style="color:#059669;background:linear-gradient(135deg,#D1FAE5,#6EE7B7);width:68px;height:68px;font-size:28px!important;border-radius:50%;display:inline-flex!important;align-items:center;justify-content:center;margin-bottom:16px"></i>
        <h6>Ingresos por Empresa</h6>
        <p>Ranking de ingresos por empresa en el año</p>
    </a>
</div>
@endsection
