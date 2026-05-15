<?php

namespace App\Http\Controllers;

use App\Exports\ControlPublicitarioExport;
use App\Models\ControlPublicitario;
use App\Models\ControlPublicitarioHistorial;
use App\Models\ControlPublicitarioPanel;
use App\Models\Empresa;
use App\Models\PanelDigital;
use App\Models\PanelUbicacion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class ControlPublicitarioController extends Controller
{
    public function index(Request $request)
    {
        $query = ControlPublicitario::with('paneles');

        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }
        if ($request->filled('tipo_panel')) {
            $query->whereHas('paneles', fn($q) => $q->where('tipo_panel', $request->tipo_panel));
        }
        if ($request->filled('buscar')) {
            $buscar = $request->buscar;
            $query->where(function ($q) use ($buscar) {
                $q->where('empresa_nombre', 'like', "%{$buscar}%")
                  ->orWhere('ruc', 'like', "%{$buscar}%")
                  ->orWhereHas('paneles', fn($p) => $p->where('panel_codigo', 'like', "%{$buscar}%"));
            });
        }

        $registros = $query->orderBy('empresa_nombre')->paginate(30);

        $empresas_data = Empresa::activas()
            ->orderBy('nombre')
            ->get(['id', 'nombre', 'ruc', 'correo', 'celular', 'encargado']);

        $paneles_digitales    = PanelDigital::where('activo', true)->orderBy('nombre')->get(['id', 'codigo', 'nombre']);
        $paneles_tradicionales = PanelUbicacion::where('activo', true)->orderBy('nombre')->get(['id', 'codigo', 'nombre']);

        $mapaDigital     = $paneles_digitales->keyBy('codigo');
        $mapaTradicional = $paneles_tradicionales->keyBy('codigo');

        $stats = [
            'activos'    => ControlPublicitario::where('estado', 'activo')->count(),
            'pausados'   => ControlPublicitario::where('estado', 'pausado')->count(),
            'cancelados' => ControlPublicitario::where('estado', 'cancelado')->count(),
        ];

        $empresas_json = $empresas_data->map(fn($e) => [
            'id'        => $e->id,
            'nombre'    => $e->nombre,
            'ruc'       => $e->ruc,
            'correo'    => $e->correo,
            'celular'   => $e->celular,
            'encargado' => $e->encargado,
        ])->values()->all();

        return view('control_publicitario.index', compact(
            'registros', 'empresas_data', 'empresas_json', 'paneles_digitales', 'paneles_tradicionales',
            'mapaDigital', 'mapaTradicional', 'stats'
        ));
    }

    public function exportar(Request $request)
    {
        return Excel::download(
            new ControlPublicitarioExport($request->all()),
            'control_publicitario_' . now()->format('Ymd_His') . '.xlsx'
        );
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'empresa_nombre'    => 'required|string|max:255',
            'ruc'               => ['nullable', 'string', 'size:11', 'regex:/^\d{11}$/'],
            'empresa_correo'    => 'nullable|email|max:150',
            'empresa_celular'   => 'nullable|string|max:20',
            'empresa_encargado' => 'nullable|string|max:100',
            'paneles'           => 'required|array|min:1',
            'paneles.*.codigo'  => 'required|string|max:50',
            'paneles.*.tipo'    => 'required|in:digital,tradicional',
            'fecha_inicio'      => 'nullable|date',
            'fecha_fin'         => 'nullable|date|after_or_equal:fecha_inicio',
            'estado'            => 'required|in:activo,pausado,cancelado',
            'notas'             => 'nullable|string',
            'monto_pagado'      => 'nullable|numeric|min:0',
            'monto_pendiente'   => 'nullable|numeric|min:0',
        ]);

        // Buscar o crear empresa
        $empresa = null;
        if (!empty($validated['ruc'])) {
            $empresa = Empresa::where('ruc', $validated['ruc'])->first();
        }
        if (!$empresa) {
            $empresa = Empresa::where('nombre', $validated['empresa_nombre'])->first();
        }

        $empresaCreada = false;
        if ($empresa) {
            $updates = [];
            if (!$empresa->ruc && !empty($validated['ruc']))                     $updates['ruc']       = $validated['ruc'];
            if (!$empresa->correo && !empty($validated['empresa_correo']))       $updates['correo']    = $validated['empresa_correo'];
            if (!$empresa->celular && !empty($validated['empresa_celular']))     $updates['celular']   = $validated['empresa_celular'];
            if (!$empresa->encargado && !empty($validated['empresa_encargado'])) $updates['encargado'] = $validated['empresa_encargado'];
            if ($updates) $empresa->update($updates);
        } else {
            $empresa = Empresa::create([
                'nombre'    => $validated['empresa_nombre'],
                'ruc'       => $validated['ruc'] ?? null,
                'correo'    => $validated['empresa_correo'] ?? null,
                'celular'   => $validated['empresa_celular'] ?? null,
                'encargado' => $validated['empresa_encargado'] ?? null,
                'activo'    => true,
            ]);
            $empresaCreada = true;
        }

        // Usar primer panel como referencia en la fila principal (legacy)
        $primerPanel = $validated['paneles'][0];

        $registro = ControlPublicitario::create([
            'empresa_nombre'  => $validated['empresa_nombre'],
            'ruc'             => $validated['ruc'] ?? null,
            'empresa_id'      => $empresa->id,
            'panel_codigo'    => $primerPanel['codigo'],
            'tipo_panel'      => $primerPanel['tipo'],
            'fecha_inicio'    => $validated['fecha_inicio'] ?? null,
            'fecha_fin'       => $validated['fecha_fin'] ?? null,
            'estado'          => $validated['estado'],
            'notas'           => $validated['notas'] ?? null,
            'monto_pagado'    => $validated['monto_pagado'] ?? null,
            'monto_pendiente' => $validated['monto_pendiente'] ?? null,
        ]);

        // Guardar todos los paneles en la tabla pivot
        foreach ($validated['paneles'] as $p) {
            ControlPublicitarioPanel::create([
                'control_publicitario_id' => $registro->id,
                'panel_codigo'            => $p['codigo'],
                'tipo_panel'              => $p['tipo'],
            ]);
        }

        ControlPublicitarioHistorial::create([
            'control_publicitario_id' => $registro->id,
            'estado_anterior'         => null,
            'estado_nuevo'            => $registro->estado,
            'notas'                   => 'Registro creado',
            'usuario_id'              => auth()->id(),
        ]);

        $msg = 'Registro creado correctamente.';
        if ($empresaCreada) {
            $msg .= ' La empresa "' . $empresa->nombre . '" fue creada en el módulo Empresas.';
        }

        return redirect()->route('control-publicitario.index')->with('success', $msg);
    }

    public function update(Request $request, ControlPublicitario $controlPublicitario)
    {
        $validated = $request->validate([
            'estado'           => 'required|in:activo,pausado,cancelado',
            'ruc'              => ['nullable', 'string', 'size:11', 'regex:/^\d{11}$/'],
            'fecha_inicio'     => 'nullable|date',
            'fecha_fin'        => 'nullable|date|after_or_equal:fecha_inicio',
            'notas'            => 'nullable|string',
            'monto_pagado'     => 'nullable|numeric|min:0',
            'monto_pendiente'  => 'nullable|numeric|min:0',
            'paneles'          => 'nullable|array|min:1',
            'paneles.*.codigo' => 'required_with:paneles|string|max:50',
            'paneles.*.tipo'   => 'required_with:paneles|in:digital,tradicional',
        ]);

        $estadoAnterior = $controlPublicitario->estado;

        if ($validated['estado'] === 'cancelado' && $estadoAnterior !== 'cancelado') {
            $validated['fecha_cancelacion'] = now();
        }

        if (!empty($validated['ruc']) && $controlPublicitario->empresa_id) {
            $empresa = Empresa::find($controlPublicitario->empresa_id);
            if ($empresa && !$empresa->ruc) {
                $empresa->update(['ruc' => $validated['ruc']]);
            }
        }

        // Sincronizar paneles si se enviaron
        if (!empty($validated['paneles'])) {
            $controlPublicitario->paneles()->delete();
            foreach ($validated['paneles'] as $p) {
                ControlPublicitarioPanel::create([
                    'control_publicitario_id' => $controlPublicitario->id,
                    'panel_codigo'            => $p['codigo'],
                    'tipo_panel'              => $p['tipo'],
                ]);
            }
            // Actualizar referencia legacy con el primer panel
            $validated['panel_codigo'] = $validated['paneles'][0]['codigo'];
            $validated['tipo_panel']   = $validated['paneles'][0]['tipo'];
        }

        unset($validated['paneles']);
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

        return redirect()->route('control-publicitario.index')->with('success', 'Registro actualizado correctamente.');
    }

    public function show(ControlPublicitario $controlPublicitario)
    {
        $controlPublicitario->load('historial.usuario', 'empresa', 'paneles');

        $paneles_digitales    = PanelDigital::where('activo', true)->orderBy('nombre')->get(['id', 'codigo', 'nombre']);
        $paneles_tradicionales = PanelUbicacion::where('activo', true)->orderBy('nombre')->get(['id', 'codigo', 'nombre']);
        $mapaDigital     = $paneles_digitales->keyBy('codigo');
        $mapaTradicional = $paneles_tradicionales->keyBy('codigo');

        return view('control_publicitario.show', compact(
            'controlPublicitario', 'paneles_digitales', 'paneles_tradicionales', 'mapaDigital', 'mapaTradicional'
        ));
    }

    public function destroy(ControlPublicitario $controlPublicitario)
    {
        $controlPublicitario->delete();
        return redirect()->route('control-publicitario.index')->with('success', 'Registro eliminado.');
    }

    public function panelPreview(string $tipo, string $codigo)
    {
        if ($tipo === 'digital') {
            $panel = PanelDigital::where('codigo', $codigo)->first();
            if (!$panel) return response()->json(['error' => 'no encontrado'], 404);
            return response()->json([
                'tipo'        => 'digital',
                'codigo'      => $panel->codigo,
                'nombre'      => $panel->nombre,
                'direccion'   => $panel->direccion,
                'medidas'     => $panel->medidas,
                'resolucion'  => $panel->resolucion,
                'orientacion' => $panel->orientacion,
                'tandas'      => $panel->tandas,
                'lat'         => $panel->lat,
                'lng'         => $panel->lng,
                'foto_url'    => $panel->foto ? Storage::url($panel->foto) : null,
            ]);
        }

        $panel = PanelUbicacion::where('codigo', $codigo)->first();
        if (!$panel) return response()->json(['error' => 'no encontrado'], 404);
        return response()->json([
            'tipo'          => 'tradicional',
            'codigo'        => $panel->codigo,
            'nombre'        => $panel->nombre,
            'direccion'     => $panel->direccion,
            'medidas'       => $panel->medidas,
            'caras'         => $panel->caras,
            'gramaje_lonas' => $panel->gramaje_lonas,
            'lat'           => $panel->lat,
            'lng'           => $panel->lng,
            'foto_url'      => $panel->foto ? Storage::url($panel->foto) : null,
        ]);
    }
}
