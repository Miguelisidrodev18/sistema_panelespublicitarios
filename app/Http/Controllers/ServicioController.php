<?php

namespace App\Http\Controllers;

use App\Models\Servicio;
use Illuminate\Http\Request;

class ServicioController extends Controller
{
    public function index()
    {
        $servicios = Servicio::orderBy('nombre')->paginate(20);
        return view('servicios.index', compact('servicios'));
    }

    public function create()
    {
        return view('servicios.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre'      => 'required|string|max:100',
            'descripcion' => 'nullable|string|max:500',
            'icono'       => 'nullable|string|max:50',
            'monto'       => 'nullable|numeric|min:0',
            'activo'      => 'boolean',
        ]);
        $validated['activo'] = $request->boolean('activo', true);

        Servicio::create($validated);

        return redirect()->route('servicios.index')->with('success', 'Servicio creado correctamente.');
    }

    public function edit(Servicio $servicio)
    {
        return view('servicios.edit', compact('servicio'));
    }

    public function update(Request $request, Servicio $servicio)
    {
        $validated = $request->validate([
            'nombre'      => 'required|string|max:100',
            'descripcion' => 'nullable|string|max:500',
            'icono'       => 'nullable|string|max:50',
            'monto'       => 'nullable|numeric|min:0',
            'activo'      => 'boolean',
        ]);
        $validated['activo'] = $request->boolean('activo', false);

        $servicio->update($validated);

        return redirect()->route('servicios.index')->with('success', 'Servicio actualizado correctamente.');
    }

    public function destroy(Servicio $servicio)
    {
        $servicio->update(['activo' => false]);
        return redirect()->route('servicios.index')->with('success', 'Servicio desactivado.');
    }
}
