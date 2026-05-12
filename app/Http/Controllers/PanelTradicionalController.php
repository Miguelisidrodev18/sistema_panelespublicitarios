<?php

namespace App\Http\Controllers;

use App\Models\PanelUbicacion;
use Illuminate\Http\Request;

class PanelTradicionalController extends Controller
{
    public function index()
    {
        $paneles = PanelUbicacion::with('empresas')->orderBy('nombre')->paginate(20);
        return view('paneles.tradicionales.index', compact('paneles'));
    }

    public function create()
    {
        return view('paneles.tradicionales.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'codigo' => 'nullable|string|max:50',
            'nombre' => 'required|string|max:200',
            'direccion' => 'nullable|string',
            'caras' => 'nullable|integer|min:1',
            'medidas' => 'nullable|string|max:100',
            'costo_produccion' => 'nullable|numeric|min:0',
            'gramaje_lonas' => 'nullable|string|max:50',
            'foto' => 'nullable|file|image|max:5120',
            'lat' => 'nullable|numeric',
            'lng' => 'nullable|numeric',
        ]);

        if ($request->hasFile('foto')) {
            $validated['foto'] = $request->file('foto')->store('paneles/tradicionales', 'public');
        }

        PanelUbicacion::create($validated);

        return redirect()->route('paneles-tradicionales.index')->with('success', 'Panel tradicional creado correctamente.');
    }

    public function edit(PanelUbicacion $panelTradicional)
    {
        return view('paneles.tradicionales.edit', compact('panelTradicional'));
    }

    public function update(Request $request, PanelUbicacion $panelTradicional)
    {
        $validated = $request->validate([
            'codigo' => 'nullable|string|max:50',
            'nombre' => 'required|string|max:200',
            'direccion' => 'nullable|string',
            'caras' => 'nullable|integer|min:1',
            'medidas' => 'nullable|string|max:100',
            'costo_produccion' => 'nullable|numeric|min:0',
            'gramaje_lonas' => 'nullable|string|max:50',
            'foto' => 'nullable|file|image|max:5120',
            'lat' => 'nullable|numeric',
            'lng' => 'nullable|numeric',
            'activo' => 'boolean',
        ]);

        if ($request->hasFile('foto')) {
            $validated['foto'] = $request->file('foto')->store('paneles/tradicionales', 'public');
        }

        $panelTradicional->update($validated);

        return redirect()->route('paneles-tradicionales.index')->with('success', 'Panel actualizado correctamente.');
    }

    public function destroy(PanelUbicacion $panelTradicional)
    {
        $panelTradicional->update(['activo' => false]);
        return redirect()->route('paneles-tradicionales.index')->with('success', 'Panel desactivado.');
    }
}
