<?php

namespace App\Http\Controllers;

use App\Models\Deuda;
use App\Models\PagoDeuda;
use Illuminate\Http\Request;

class DeudaController extends Controller
{
    public function index(Request $request)
    {
        $query = Deuda::with('pagos');

        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        if ($request->filled('prioridad')) {
            $query->where('prioridad', $request->prioridad);
        }

        $deudas = $query->orderBy('fecha_vencimiento')->paginate(20);
        $total_pendiente = Deuda::where('estado', 'pendiente')->sum('monto_pendiente');

        return view('deudas.index', compact('deudas', 'total_pendiente'));
    }

    public function create()
    {
        return view('deudas.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'acreedor' => 'required|string|max:200',
            'concepto' => 'required|string|max:300',
            'monto' => 'required|numeric|min:0',
            'fecha_deuda' => 'required|date',
            'fecha_vencimiento' => 'nullable|date',
            'prioridad' => 'in:baja,media,alta',
            'notas' => 'nullable|string',
        ]);

        $validated['monto_pendiente'] = $validated['monto'];

        Deuda::create($validated);

        return redirect()->route('deudas.index')->with('success', 'Deuda registrada correctamente.');
    }

    public function show(Deuda $deuda)
    {
        $deuda->load('pagos');
        return view('deudas.show', compact('deuda'));
    }

    public function edit(Deuda $deuda)
    {
        return view('deudas.edit', compact('deuda'));
    }

    public function update(Request $request, Deuda $deuda)
    {
        $validated = $request->validate([
            'acreedor' => 'required|string|max:200',
            'concepto' => 'required|string|max:300',
            'monto' => 'required|numeric|min:0',
            'fecha_deuda' => 'required|date',
            'fecha_vencimiento' => 'nullable|date',
            'prioridad' => 'in:baja,media,alta',
            'notas' => 'nullable|string',
            'estado' => 'in:pendiente,pagada,cancelada',
        ]);

        $deuda->update($validated);

        return redirect()->route('deudas.show', $deuda)->with('success', 'Deuda actualizada.');
    }

    public function registrarPago(Request $request, Deuda $deuda)
    {
        $validated = $request->validate([
            'monto' => 'required|numeric|min:0.01|max:' . $deuda->monto_pendiente,
            'fecha_pago' => 'required|date',
            'metodo_pago' => 'nullable|string|max:50',
            'notas' => 'nullable|string',
            'comprobante' => 'nullable|file|max:10240',
        ]);

        if ($request->hasFile('comprobante')) {
            $validated['comprobante'] = $request->file('comprobante')->store('comprobantes/deudas', 'public');
        }

        $validated['deuda_id'] = $deuda->id;
        PagoDeuda::create($validated);

        $nuevo_pendiente = $deuda->monto_pendiente - $validated['monto'];
        $deuda->update([
            'monto_pendiente' => $nuevo_pendiente,
            'estado' => $nuevo_pendiente <= 0 ? 'pagada' : 'pendiente',
        ]);

        return back()->with('success', 'Pago registrado correctamente.');
    }
}
