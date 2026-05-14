<?php

namespace App\Http\Controllers;

use App\Models\ControlPublicitario;
use App\Models\PanelDigital;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PanelDigitalController extends Controller
{
    public function index()
    {
        $paneles = PanelDigital::with('empresas')->orderBy('nombre')->paginate(20);

        $campanasPorPanel = ControlPublicitario::where('estado', 'activo')
            ->where('tipo_panel', 'digital')
            ->select('panel_codigo', DB::raw('count(*) as total'))
            ->groupBy('panel_codigo')
            ->pluck('total', 'panel_codigo');

        return view('paneles.digitales.index', compact('paneles', 'campanasPorPanel'));
    }

    public function create()
    {
        return view('paneles.digitales.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'codigo' => 'nullable|string|max:50',
            'nombre' => 'required|string|max:200',
            'direccion' => 'nullable|string',
            'medidas' => 'nullable|string|max:100',
            'resolucion' => 'nullable|string|max:100',
            'orientacion' => 'nullable|in:horizontal,vertical',
            'tandas' => 'nullable|integer|min:1',
            'foto' => 'nullable|file|image|max:5120',
            'lat' => 'nullable|numeric',
            'lng' => 'nullable|numeric',
        ]);

        if ($request->hasFile('foto')) {
            $validated['foto'] = $request->file('foto')->store('paneles/digitales', 'public');
        }

        PanelDigital::create($validated);

        return redirect()->route('paneles-digitales.index')->with('success', 'Panel digital creado correctamente.');
    }

    public function edit(PanelDigital $panelDigital)
    {
        return view('paneles.digitales.edit', compact('panelDigital'));
    }

    public function update(Request $request, PanelDigital $panelDigital)
    {
        $validated = $request->validate([
            'codigo' => 'nullable|string|max:50',
            'nombre' => 'required|string|max:200',
            'direccion' => 'nullable|string',
            'medidas' => 'nullable|string|max:100',
            'resolucion' => 'nullable|string|max:100',
            'orientacion' => 'nullable|in:horizontal,vertical',
            'tandas' => 'nullable|integer|min:1',
            'foto' => 'nullable|file|image|max:5120',
            'lat' => 'nullable|numeric',
            'lng' => 'nullable|numeric',
            'activo' => 'boolean',
        ]);

        if ($request->hasFile('foto')) {
            $validated['foto'] = $request->file('foto')->store('paneles/digitales', 'public');
        }

        $panelDigital->update($validated);

        return redirect()->route('paneles-digitales.index')->with('success', 'Panel actualizado correctamente.');
    }

    public function destroy(PanelDigital $panelDigital)
    {
        $panelDigital->update(['activo' => false]);
        return redirect()->route('paneles-digitales.index')->with('success', 'Panel desactivado.');
    }
}
