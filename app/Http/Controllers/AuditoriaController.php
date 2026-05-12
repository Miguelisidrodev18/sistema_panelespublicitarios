<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Usuario;
use Illuminate\Http\Request;

class AuditoriaController extends Controller
{
    public function index(Request $request)
    {
        $query = ActivityLog::with('usuario')->latest();

        if ($request->filled('modulo')) {
            $query->where('modulo', $request->modulo);
        }

        if ($request->filled('accion')) {
            $query->where('accion', $request->accion);
        }

        if ($request->filled('usuario_id')) {
            $query->where('usuario_id', $request->usuario_id);
        }

        if ($request->filled('fecha_desde')) {
            $query->whereDate('created_at', '>=', $request->fecha_desde);
        }

        if ($request->filled('fecha_hasta')) {
            $query->whereDate('created_at', '<=', $request->fecha_hasta);
        }

        $logs = $query->paginate(50);
        $usuarios = Usuario::orderBy('nombre_completo')->get();
        $modulos = ActivityLog::select('modulo')->distinct()->orderBy('modulo')->pluck('modulo');

        return view('auditoria.index', compact('logs', 'usuarios', 'modulos'));
    }
}
