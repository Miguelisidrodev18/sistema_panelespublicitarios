<?php

namespace App\Http\Controllers;

use App\Models\Proveedor;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class ProveedorController extends Controller
{
    public function index(Request $request)
    {
        $query = Proveedor::query();

        if ($buscar = $request->input('buscar')) {
            $query->where(function ($q) use ($buscar) {
                $q->where('razon_social', 'like', "%{$buscar}%")
                  ->orWhere('ruc', 'like', "%{$buscar}%")
                  ->orWhere('contacto', 'like', "%{$buscar}%");
            });
        }

        if ($estado = $request->input('estado')) {
            $query->where('estado', $estado);
        }

        $proveedores = $query->orderBy('razon_social')->paginate(20)->withQueryString();

        return view('proveedores.index', compact('proveedores'));
    }

    public function create()
    {
        return view('proveedores.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'razon_social'  => 'required|string|max:200',
            'ruc'           => 'nullable|string|max:11|unique:proveedores,ruc',
            'direccion'     => 'nullable|string|max:300',
            'telefono'      => 'nullable|string|max:20',
            'email'         => 'nullable|email|max:100',
            'contacto'      => 'nullable|string|max:150',
            'rubro'         => 'nullable|string|max:100',
            'observaciones' => 'nullable|string',
            'estado'        => 'in:activo,inactivo',
        ]);

        $proveedor = Proveedor::create($validated);

        ActivityLog::registrar('created', 'Proveedor', $proveedor->id, "Proveedor '{$proveedor->razon_social}' registrado");

        return redirect()->route('proveedores.index')->with('success', 'Proveedor registrado correctamente.');
    }

    public function show(Proveedor $proveedor)
    {
        $proveedor->load(['items.almacen']);
        return view('proveedores.show', compact('proveedor'));
    }

    public function edit(Proveedor $proveedor)
    {
        return view('proveedores.edit', compact('proveedor'));
    }

    public function update(Request $request, Proveedor $proveedor)
    {
        $validated = $request->validate([
            'razon_social'  => 'required|string|max:200',
            'ruc'           => 'nullable|string|max:11|unique:proveedores,ruc,' . $proveedor->id,
            'direccion'     => 'nullable|string|max:300',
            'telefono'      => 'nullable|string|max:20',
            'email'         => 'nullable|email|max:100',
            'contacto'      => 'nullable|string|max:150',
            'rubro'         => 'nullable|string|max:100',
            'observaciones' => 'nullable|string',
            'estado'        => 'in:activo,inactivo',
        ]);

        $proveedor->update($validated);

        ActivityLog::registrar('updated', 'Proveedor', $proveedor->id, "Proveedor '{$proveedor->razon_social}' actualizado");

        return redirect()->route('proveedores.index')->with('success', 'Proveedor actualizado correctamente.');
    }

    public function destroy(Proveedor $proveedor)
    {
        $proveedor->update(['estado' => 'inactivo']);

        ActivityLog::registrar('deleted', 'Proveedor', $proveedor->id, "Proveedor '{$proveedor->razon_social}' desactivado");

        return redirect()->route('proveedores.index')->with('success', 'Proveedor desactivado.');
    }
}
