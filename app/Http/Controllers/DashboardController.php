<?php

namespace App\Http\Controllers;

use App\Models\Empresa;
use App\Models\Cobranza;
use App\Models\Contrato;
use App\Models\ControlPublicitario;
use App\Models\Ingreso;
use App\Models\Egreso;
use App\Models\Deuda;
use App\Models\PanelDigital;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        if ($user->esAdmin()) {
            $stats = [
                'total_empresas'    => Empresa::where('activo', true)->count(),
                'cuotas_pendientes' => Cobranza::where('estado', 'pendiente')->count(),
                'cuotas_vencidas'   => Cobranza::where('estado', 'pendiente')
                    ->where('fecha_vencimiento', '<', now())->count(),
                'ingresos_mes'      => Ingreso::whereMonth('created_at', now()->month)
                    ->whereYear('created_at', now()->year)->sum('monto'),
                'egresos_mes'       => Egreso::whereMonth('created_at', now()->month)
                    ->whereYear('created_at', now()->year)->sum('monto'),
                'deudas_pendientes' => Deuda::where('estado', 'pendiente')->sum('monto_pendiente'),
            ];

            $proximas_cuotas = Cobranza::with('empresa')
                ->where('estado', 'pendiente')
                ->orderBy('fecha_vencimiento')
                ->limit(10)
                ->get();

            // Alertas para el admin
            $alertas = collect();

            $contratos_por_vencer = Contrato::with('empresa')
                ->where('estado', 'activo')
                ->whereNotNull('fecha_fin')
                ->whereBetween('fecha_fin', [now(), now()->addDays(30)])
                ->orderBy('fecha_fin')
                ->get();
            foreach ($contratos_por_vencer as $c) {
                $alertas->push([
                    'tipo'    => 'warning',
                    'icono'   => 'file-earmark-text',
                    'mensaje' => "Contrato <strong>{$c->numero_contrato}</strong> ({$c->contratante}) vence el {$c->fecha_fin->format('d/m/Y')}",
                    'url'     => route('contratos.show', $c),
                ]);
            }

            $contratos_vencidos = Contrato::where('estado', 'activo')
                ->whereNotNull('fecha_fin')
                ->where('fecha_fin', '<', now())
                ->count();
            if ($contratos_vencidos > 0) {
                $alertas->push([
                    'tipo'    => 'danger',
                    'icono'   => 'exclamation-triangle',
                    'mensaje' => "<strong>{$contratos_vencidos}</strong> contrato(s) vencido(s) aún marcado(s) como activo",
                    'url'     => route('contratos.index', ['estado' => 'activo']),
                ]);
            }

            if ($stats['cuotas_vencidas'] > 0) {
                $alertas->push([
                    'tipo'    => 'danger',
                    'icono'   => 'cash-coin',
                    'mensaje' => "<strong>{$stats['cuotas_vencidas']}</strong> cuota(s) de cobranza vencida(s) sin pagar",
                    'url'     => route('cobranzas.index'),
                ]);
            }

            $campanas_vencidas = ControlPublicitario::where('estado', 'activo')
                ->whereNotNull('fecha_fin')
                ->where('fecha_fin', '<', now())
                ->count();
            if ($campanas_vencidas > 0) {
                $alertas->push([
                    'tipo'    => 'warning',
                    'icono'   => 'clipboard2-check',
                    'mensaje' => "<strong>{$campanas_vencidas}</strong> campaña(s) publicitaria(s) con período vencido",
                    'url'     => route('control-publicitario.index'),
                ]);
            }

            // Ingresos últimos 6 meses para el gráfico
            $ingresos6meses = collect();
            for ($i = 5; $i >= 0; $i--) {
                $mes = now()->subMonths($i);
                $ingresos6meses->push([
                    'label' => $mes->translatedFormat('M Y'),
                    'monto' => (int) Ingreso::whereYear('created_at', $mes->year)
                        ->whereMonth('created_at', $mes->month)
                        ->sum('monto'),
                ]);
            }

            // Ocupación de paneles digitales
            $total_paneles   = PanelDigital::count();
            $paneles_activos = ControlPublicitario::where('estado', 'activo')
                ->where('tipo_panel', 'digital')
                ->whereDate('fecha_inicio', '<=', now())
                ->where(function ($q) {
                    $q->whereNull('fecha_fin')->orWhereDate('fecha_fin', '>=', now());
                })
                ->distinct('panel_codigo')
                ->count('panel_codigo');
            $ocupacion_pct = $total_paneles > 0
                ? round(($paneles_activos / $total_paneles) * 100)
                : 0;

            // Contratos morosos
            $contratos_morosos = Contrato::with('empresa', 'cobros')
                ->where('estado', 'activo')
                ->where('saldo_pendiente', '>', 0)
                ->latest()
                ->get()
                ->filter(fn($c) => $c->estado_deuda === 'Moroso')
                ->take(10)
                ->values();

        } else {
            $empresa = $user->empresa;
            $stats = [
                'cuotas_pendientes' => $empresa ? $empresa->cobranzas()->where('estado', 'pendiente')->count() : 0,
                'cuotas_vencidas'   => $empresa ? $empresa->cobranzas()->where('estado', 'pendiente')
                    ->where('fecha_vencimiento', '<', now())->count() : 0,
                'total_pagado'      => $empresa ? $empresa->ingresos()->sum('monto') : 0,
            ];

            $proximas_cuotas = $empresa
                ? $empresa->cobranzas()->where('estado', 'pendiente')->orderBy('fecha_vencimiento')->limit(5)->get()
                : collect();

            $alertas = collect();
            if ($stats['cuotas_vencidas'] > 0) {
                $alertas->push([
                    'tipo'    => 'danger',
                    'icono'   => 'cash-coin',
                    'mensaje' => "Tenés <strong>{$stats['cuotas_vencidas']}</strong> cuota(s) vencida(s) sin pagar",
                    'url'     => route('cobranzas.index'),
                ]);
            }

            $ingresos6meses    = collect();
            $total_paneles     = 0;
            $paneles_activos   = 0;
            $ocupacion_pct     = 0;
            $contratos_morosos = collect();
        }

        return view('dashboard', compact(
            'stats', 'proximas_cuotas', 'alertas',
            'ingresos6meses', 'total_paneles', 'paneles_activos', 'ocupacion_pct',
            'contratos_morosos'
        ));
    }
}
