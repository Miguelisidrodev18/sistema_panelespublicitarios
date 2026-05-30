<?php

namespace App\Http\Controllers;

use App\Models\Cobranza;
use App\Models\Empresa;
use Illuminate\Http\Request;

class CobranzaController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $query = Cobranza::with(['empresa', 'contrato']);

        if ($user->esEmpresa()) {
            $query->where('empresa_id', $user->empresa_id);
        }

        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        if ($request->filled('empresa_id') && $user->esAdmin()) {
            $query->where('empresa_id', $request->empresa_id);
        }

        $cobranzas = $query->orderBy('fecha_vencimiento')->paginate(30);
        $empresas = $user->esAdmin() ? Empresa::activas()->orderBy('nombre')->get() : collect();

        return view('cobranzas.index', compact('cobranzas', 'empresas'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'empresa_id' => 'required|exists:empresas,id',
            'numero_cuota' => 'required|integer|min:1',
            'monto' => 'required|numeric|min:0',
            'fecha_vencimiento' => 'required|date',
            'concepto' => 'nullable|string|max:300',
        ]);

        Cobranza::create($validated);

        return back()->with('success', 'Cuota registrada correctamente.');
    }

    public function marcarPagada(Cobranza $cobranza)
    {
        $cobranza->update(['estado' => 'pagada']);
        return back()->with('success', 'Cuota marcada como pagada.');
    }

    public function destroy(Cobranza $cobranza)
    {
        $cobranza->delete();
        return back()->with('success', 'Cuota eliminada.');
    }

    public function recibo(Cobranza $cobranza, string $formato = 'a4')
    {
        $cobranza->load(['empresa', 'contrato']);
        $empresa_propia = config('empresa');
        $view = $formato === '80mm' ? 'cobranzas.recibo-80mm' : 'cobranzas.recibo-a4';
        return view($view, compact('cobranza', 'empresa_propia'));
    }
}
