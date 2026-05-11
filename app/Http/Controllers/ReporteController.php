<?php

namespace App\Http\Controllers;

use App\Models\Ingreso;
use App\Models\Egreso;
use App\Models\Empresa;
use App\Models\Cobranza;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReporteController extends Controller
{
    public function index()
    {
        return view('reportes.index');
    }

    public function flujoMensual(Request $request)
    {
        $año = $request->get('año', now()->year);

        $ingresos_mensuales = Ingreso::selectRaw('MONTH(created_at) as mes, SUM(monto) as total')
            ->whereYear('created_at', $año)
            ->groupBy('mes')
            ->pluck('total', 'mes');

        $egresos_mensuales = Egreso::selectRaw('MONTH(created_at) as mes, SUM(monto) as total')
            ->whereYear('created_at', $año)
            ->groupBy('mes')
            ->pluck('total', 'mes');

        $meses = collect(range(1, 12))->map(fn($m) => [
            'mes' => $m,
            'nombre' => \Carbon\Carbon::create()->month($m)->locale('es')->monthName,
            'ingresos' => $ingresos_mensuales->get($m, 0),
            'egresos' => $egresos_mensuales->get($m, 0),
            'balance' => $ingresos_mensuales->get($m, 0) - $egresos_mensuales->get($m, 0),
        ]);

        return view('reportes.flujo_mensual', compact('meses', 'año'));
    }

    public function cobranzasPendientes()
    {
        $empresas = Empresa::with(['cobranzas' => function ($q) {
            $q->where('estado', 'pendiente')->orderBy('fecha_vencimiento');
        }])->whereHas('cobranzas', fn($q) => $q->where('estado', 'pendiente'))
           ->get();

        return view('reportes.cobranzas_pendientes', compact('empresas'));
    }

    public function ingresosPorEmpresa(Request $request)
    {
        $año = $request->get('año', now()->year);

        $datos = Empresa::withSum(['ingresos as total_ingresos' => function ($q) use ($año) {
            $q->whereYear('created_at', $año);
        }], 'monto')
        ->having('total_ingresos', '>', 0)
        ->orderByDesc('total_ingresos')
        ->get();

        return view('reportes.ingresos_por_empresa', compact('datos', 'año'));
    }
}
