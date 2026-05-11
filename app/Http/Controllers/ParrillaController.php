<?php

namespace App\Http\Controllers;

use App\Models\ControlPublicitario;
use Carbon\Carbon;

class ParrillaController extends Controller
{
    public function hoy()
    {
        $hoy = Carbon::today();

        $campanas = ControlPublicitario::where('estado', 'activo')
            ->whereDate('fecha_inicio', '<=', $hoy)
            ->where(function ($q) use ($hoy) {
                $q->whereNull('fecha_fin')
                  ->orWhereDate('fecha_fin', '>=', $hoy);
            })
            ->orderBy('tipo_panel')
            ->orderBy('panel_codigo')
            ->get();

        return view('parrilla.hoy', compact('campanas', 'hoy'));
    }
}
