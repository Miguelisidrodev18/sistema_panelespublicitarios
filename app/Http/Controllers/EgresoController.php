<?php

namespace App\Http\Controllers;

use App\Models\Egreso;
use App\Models\Empresa;
use Illuminate\Http\Request;

class EgresoController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $query = Egreso::with('empresa');

        if ($request->filled('empresa_id') && $user->esAdmin()) {
            $query->where('empresa_id', $request->empresa_id);
        }

        if ($request->filled('tipo')) {
            $query->where('tipo', $request->tipo);
        }

        $egresos = $query->latest()->paginate(30);
        $total = $query->sum('monto');
        $empresas = $user->esAdmin() ? Empresa::activas()->orderBy('nombre')->get() : collect();

        return view('egresos.index', compact('egresos', 'total', 'empresas'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'empresa_id' => 'nullable|exists:empresas,id',
            'tipo' => 'required|string|max:100',
            'monto' => 'required|numeric|min:0',
            'concepto' => 'nullable|string|max:300',
            'observaciones' => 'nullable|string',
            'comprobante' => 'nullable|file|max:10240',
        ]);

        $validated['creado_por'] = auth()->user()->nombre_completo;

        if ($request->hasFile('comprobante')) {
            $validated['comprobante'] = $request->file('comprobante')->store('comprobantes/egresos', 'public');
        }

        Egreso::create($validated);

        return back()->with('success', 'Egreso registrado correctamente.');
    }

    public function destroy(Egreso $egreso)
    {
        $egreso->delete();
        return back()->with('success', 'Egreso eliminado.');
    }
}
