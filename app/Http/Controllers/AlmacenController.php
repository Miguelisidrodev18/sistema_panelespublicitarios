<?php

namespace App\Http\Controllers;

use App\Models\Almacen;
use Illuminate\Http\Request;

class AlmacenController extends Controller
{
    public function index()
    {
        $almacenes = Almacen::orderByDesc('es_principal')->orderBy('nombre')->get();
        return view('almacenes.index', compact('almacenes'));
    }

    public function create()
    {
        return view('almacenes.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre'      => 'required|string|max:200',
            'codigo'      => 'nullable|string|max:50',
            'direccion'   => 'nullable|string',
            'telefono'    => 'nullable|string|max:50',
            'responsable' => 'nullable|string|max:200',
            'estado'      => 'in:activo,inactivo',
            'es_principal' => 'boolean',
        ]);

        $validated['es_principal'] = $request->boolean('es_principal');

        Almacen::create($validated);

        return redirect()->route('almacenes.index')->with('success', 'Almacén registrado correctamente.');
    }

    public function edit(Almacen $almacen)
    {
        return view('almacenes.edit', compact('almacen'));
    }

    public function update(Request $request, Almacen $almacen)
    {
        $validated = $request->validate([
            'nombre'      => 'required|string|max:200',
            'codigo'      => 'nullable|string|max:50',
            'direccion'   => 'nullable|string',
            'telefono'    => 'nullable|string|max:50',
            'responsable' => 'nullable|string|max:200',
            'estado'      => 'in:activo,inactivo',
            'es_principal' => 'boolean',
        ]);

        $validated['es_principal'] = $request->boolean('es_principal');

        $almacen->update($validated);

        return redirect()->route('almacenes.index')->with('success', 'Almacén actualizado correctamente.');
    }

    public function destroy(Almacen $almacen)
    {
        $almacen->update(['estado' => 'inactivo']);
        return redirect()->route('almacenes.index')->with('success', 'Almacén desactivado.');
    }
}
