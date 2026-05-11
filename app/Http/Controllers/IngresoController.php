<?php

namespace App\Http\Controllers;

use App\Models\Ingreso;
use App\Models\Empresa;
use Illuminate\Http\Request;

class IngresoController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $query = Ingreso::with('empresa');

        if ($user->esEmpresa()) {
            $query->where('empresa_id', $user->empresa_id);
        }

        if ($request->filled('empresa_id') && $user->esAdmin()) {
            $query->where('empresa_id', $request->empresa_id);
        }

        if ($request->filled('tipo')) {
            $query->where('tipo', $request->tipo);
        }

        $ingresos = $query->latest()->paginate(30);
        $total = $query->sum('monto');
        $empresas = $user->esAdmin() ? Empresa::activas()->orderBy('nombre')->get() : collect();

        return view('ingresos.index', compact('ingresos', 'total', 'empresas'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'empresa_id' => 'nullable|exists:empresas,id',
            'tipo' => 'required|string|max:100',
            'monto' => 'required|numeric|min:0',
            'metodo_pago' => 'nullable|string|max:50',
            'concepto' => 'nullable|string|max:300',
            'observaciones' => 'nullable|string',
            'comprobante' => 'nullable|file|max:10240',
            'va_a_general' => 'boolean',
        ]);

        $validated['creado_por'] = auth()->user()->nombre_completo;

        if ($request->hasFile('comprobante')) {
            $validated['comprobante'] = $request->file('comprobante')->store('comprobantes/ingresos', 'public');
        }

        Ingreso::create($validated);

        return back()->with('success', 'Ingreso registrado correctamente.');
    }

    public function destroy(Ingreso $ingreso)
    {
        $ingreso->delete();
        return back()->with('success', 'Ingreso eliminado.');
    }
}
