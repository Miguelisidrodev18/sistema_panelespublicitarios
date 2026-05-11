<?php

namespace App\Http\Controllers;

use App\Models\ControlPublicitario;
use App\Models\ControlPublicitarioHistorial;
use App\Models\Empresa;
use App\Models\PanelDigital;
use App\Models\PanelUbicacion;
use Illuminate\Http\Request;

class ControlPublicitarioController extends Controller
{
    public function index(Request $request)
    {
        $query = ControlPublicitario::query();

        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        if ($request->filled('tipo_panel')) {
            $query->where('tipo_panel', $request->tipo_panel);
        }

        if ($request->filled('buscar')) {
            $query->where(function ($q) use ($request) {
                $q->where('empresa_nombre', 'like', '%' . $request->buscar . '%')
                  ->orWhere('panel_codigo', 'like', '%' . $request->buscar . '%');
            });
        }

        $registros = $query->orderBy('empresa_nombre')->paginate(30);

        $empresas = Empresa::activas()->orderBy('nombre')->pluck('nombre', 'nombre');
        $paneles_digitales = PanelDigital::where('activo', true)->orderBy('nombre')->get(['codigo', 'nombre']);
        $paneles_tradicionales = PanelUbicacion::where('activo', true)->orderBy('nombre')->get(['codigo', 'nombre']);

        $stats = [
            'activos'   => ControlPublicitario::where('estado', 'activo')->count(),
            'pausados'  => ControlPublicitario::where('estado', 'pausado')->count(),
            'cancelados'=> ControlPublicitario::where('estado', 'cancelado')->count(),
        ];

        return view('control_publicitario.index', compact('registros', 'empresas', 'paneles_digitales', 'paneles_tradicionales', 'stats'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'empresa_nombre' => 'required|string|max:255',
            'panel_codigo'   => 'required|string|max:50',
            'tipo_panel'     => 'required|in:digital,tradicional',
            'fecha_inicio'   => 'nullable|date',
            'fecha_fin'      => 'nullable|date|after_or_equal:fecha_inicio',
            'estado'         => 'required|in:activo,pausado,cancelado',
            'notas'          => 'nullable|string',
        ]);

        $registro = ControlPublicitario::create($validated);

        ControlPublicitarioHistorial::create([
            'control_publicitario_id' => $registro->id,
            'estado_anterior'         => null,
            'estado_nuevo'            => $registro->estado,
            'notas'                   => 'Registro creado',
            'usuario_id'              => auth()->id(),
        ]);

        return back()->with('success', 'Registro creado correctamente.');
    }

    public function update(Request $request, ControlPublicitario $controlPublicitario)
    {
        $validated = $request->validate([
            'estado'       => 'required|in:activo,pausado,cancelado',
            'fecha_inicio' => 'nullable|date',
            'fecha_fin'    => 'nullable|date|after_or_equal:fecha_inicio',
            'notas'        => 'nullable|string',
        ]);

        $estadoAnterior = $controlPublicitario->estado;

        if ($validated['estado'] === 'cancelado' && $estadoAnterior !== 'cancelado') {
            $validated['fecha_cancelacion'] = now();
        }

        $controlPublicitario->update($validated);

        if ($validated['estado'] !== $estadoAnterior) {
            ControlPublicitarioHistorial::create([
                'control_publicitario_id' => $controlPublicitario->id,
                'estado_anterior'         => $estadoAnterior,
                'estado_nuevo'            => $validated['estado'],
                'notas'                   => $validated['notas'] ?? null,
                'usuario_id'              => auth()->id(),
            ]);
        }

        return back()->with('success', 'Registro actualizado correctamente.');
    }

    public function show(ControlPublicitario $controlPublicitario)
    {
        $controlPublicitario->load('historial.usuario');
        return view('control_publicitario.show', compact('controlPublicitario'));
    }

    public function destroy(ControlPublicitario $controlPublicitario)
    {
        $controlPublicitario->delete();
        return back()->with('success', 'Registro eliminado.');
    }
}
