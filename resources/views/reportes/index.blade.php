@extends('layouts.app')

@section('title', 'Reportes')
@section('subtitle', 'Análisis y estadísticas del sistema')

@section('content')
<div class="stats-grid stagger" style="grid-template-columns:repeat(auto-fill,minmax(260px,1fr))">
    <a href="{{ route('reportes.flujo_mensual') }}" class="report-card">
        <div style="position:relative;margin-bottom:16px">
            <div style="position:absolute;inset:-8px;border-radius:50%;background:linear-gradient(135deg,#2563EB22,#3B82F633);border:2px dashed #2563EB33;animation:spin 12s linear infinite"></div>
            <i class="bi bi-bar-chart-line" style="color:#2563EB;background:linear-gradient(135deg,#DBEAFE,#BFDBFE);width:68px;height:68px;font-size:28px!important;border-radius:50%;display:inline-flex!important;align-items:center;justify-content:center;position:relative"></i>
        </div>
        <h6>Flujo Mensual</h6>
        <p>Ingresos y egresos por mes del año</p>
        <span style="font-size:11px;color:var(--text-light);margin-top:4px;display:block">📊 Análisis de balance financiero</span>
    </a>
    <a href="{{ route('reportes.cobranzas_pendientes') }}" class="report-card">
        <div style="position:relative;margin-bottom:16px">
            <div style="position:absolute;inset:-8px;border-radius:50%;background:linear-gradient(135deg,#D9770622,#F59E0B33);border:2px dashed #D9770633;animation:spin 12s linear infinite"></div>
            <i class="bi bi-exclamation-circle" style="color:#D97706;background:linear-gradient(135deg,#FEF3C7,#FDE68A);width:68px;height:68px;font-size:28px!important;border-radius:50%;display:inline-flex!important;align-items:center;justify-content:center;position:relative"></i>
        </div>
        <h6>Cobranzas Pendientes</h6>
        <p>Cuotas pendientes agrupadas por empresa</p>
        <span style="font-size:11px;color:var(--text-light);margin-top:4px;display:block">⚠️ Control de deudas activas</span>
    </a>
    <a href="{{ route('reportes.ingresos_por_empresa') }}" class="report-card">
        <div style="position:relative;margin-bottom:16px">
            <div style="position:absolute;inset:-8px;border-radius:50%;background:linear-gradient(135deg,#05966922,#10B98133);border:2px dashed #05966933;animation:spin 12s linear infinite"></div>
            <i class="bi bi-building" style="color:#059669;background:linear-gradient(135deg,#D1FAE5,#6EE7B7);width:68px;height:68px;font-size:28px!important;border-radius:50%;display:inline-flex!important;align-items:center;justify-content:center;position:relative"></i>
        </div>
        <h6>Ingresos por Empresa</h6>
        <p>Ranking de ingresos por empresa en el año</p>
        <span style="font-size:11px;color:var(--text-light);margin-top:4px;display:block">🏆 Top empresas contribuyentes</span>
    </a>
</div>
<style>@keyframes spin{to{transform:rotate(360deg)}}</style>
@endsection
