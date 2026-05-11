<?php

namespace App\Http\Controllers;

use App\Models\Contrato;
use App\Models\ContratoElemento;
use App\Models\Empresa;
use App\Models\ContratoCobro;
use App\Models\Cobranza;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ContratoController extends Controller
{
    public function index(Request $request)
    {
        $query = Contrato::with('empresa', 'cobros');

        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        if ($request->filled('empresa_id')) {
            $query->where('empresa_id', $request->empresa_id);
        }

        $contratos = $query->latest()->paginate(20);
        $empresas = Empresa::activas()->orderBy('nombre')->get();

        return view('contratos.index', compact('contratos', 'empresas'));
    }

    public function create()
    {
        $empresas = Empresa::activas()->orderBy('nombre')->get();
        return view('contratos.create', compact('empresas'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'numero_contrato'  => 'required|string|max:50|unique:contratos',
            'empresa_id'       => 'nullable|exists:empresas,id',
            'contratante'      => 'required|string|max:200',
            'doc_tipo'         => 'nullable|string|max:20',
            'doc_numero'       => 'nullable|string|max:50',
            'direccion'        => 'nullable|string',
            'tipo_contrato'    => 'required|string|max:50',
            'monto_total'      => 'required|numeric|min:0',
            'adelanto'         => 'nullable|numeric|min:0',
            'fecha_inicio'     => 'nullable|date',
            'fecha_fin'        => 'nullable|date',
            'descripcion'      => 'nullable|string',
            'frecuencia_cobro' => 'nullable|in:mensual,bimestral,trimestral,semestral,anual',
        ]);

        $validated['saldo_pendiente']  = $validated['monto_total'] - ($validated['adelanto'] ?? 0);
        $validated['frecuencia_cobro'] = $validated['frecuencia_cobro'] ?? 'mensual';

        $contrato = Contrato::create($validated);

        if ($request->boolean('generar_cuotas') && $request->filled('num_cuotas') && $request->filled('primera_fecha')) {
            $numCuotas      = (int) $request->num_cuotas;
            $montoCuota     = round($validated['saldo_pendiente'] / $numCuotas);
            $fecha          = Carbon::parse($request->primera_fecha);
            $empresaId      = $validated['empresa_id'] ?? null;
            $mesesIntervalo = $contrato->mesesFrecuencia();

            for ($i = 1; $i <= $numCuotas; $i++) {
                Cobranza::create([
                    'empresa_id'        => $empresaId,
                    'numero_cuota'      => $i,
                    'monto'             => $montoCuota,
                    'fecha_vencimiento' => $fecha->copy(),
                    'estado'            => 'pendiente',
                    'concepto'          => "Contrato {$contrato->numero_contrato} — Cuota {$i}/{$numCuotas}",
                ]);
                $fecha->addMonths($mesesIntervalo);
            }
        }

        return redirect()->route('contratos.show', $contrato)->with('success', 'Contrato creado correctamente.');
    }

    public function show(Contrato $contrato)
    {
        $contrato->load(['empresa', 'elementos', 'cobros']);
        return view('contratos.show', compact('contrato'));
    }

    public function edit(Contrato $contrato)
    {
        $empresas = Empresa::activas()->orderBy('nombre')->get();
        return view('contratos.edit', compact('contrato', 'empresas'));
    }

    public function update(Request $request, Contrato $contrato)
    {
        $validated = $request->validate([
            'empresa_id'       => 'nullable|exists:empresas,id',
            'contratante'      => 'required|string|max:200',
            'doc_tipo'         => 'nullable|string|max:20',
            'doc_numero'       => 'nullable|string|max:50',
            'direccion'        => 'nullable|string',
            'tipo_contrato'    => 'required|string|max:50',
            'monto_total'      => 'required|numeric|min:0',
            'adelanto'         => 'nullable|numeric|min:0',
            'fecha_inicio'     => 'nullable|date',
            'fecha_fin'        => 'nullable|date',
            'descripcion'      => 'nullable|string',
            'estado'           => 'in:activo,finalizado,cancelado',
            'frecuencia_cobro' => 'nullable|in:mensual,bimestral,trimestral,semestral,anual',
        ]);

        $contrato->update($validated);

        return redirect()->route('contratos.show', $contrato)->with('success', 'Contrato actualizado.');
    }

    public function registrarCobro(Request $request, Contrato $contrato)
    {
        $validated = $request->validate([
            'tipo_cobro'  => 'required|string|max:50',
            'metodo_pago' => 'nullable|string|max:30',
            'monto'       => 'required|numeric|min:0',
            'fecha_cobro' => 'required|date',
            'notas'       => 'nullable|string',
        ]);

        $validated['contrato_id'] = $contrato->id;
        ContratoCobro::create($validated);

        $total_cobrado = $contrato->cobros()->sum('monto');
        $contrato->update(['saldo_pendiente' => $contrato->monto_total - $total_cobrado]);

        return back()->with('success', 'Cobro registrado correctamente.');
    }

    public function actualizarInstalacion(Request $request, Contrato $contrato, ContratoElemento $elemento)
    {
        $validated = $request->validate([
            'estado_instalacion' => 'required|in:pendiente_instalacion,instalado,retirado',
            'fecha_instalacion'  => 'nullable|date',
            'fecha_retiro'       => 'nullable|date',
        ]);

        $elemento->update($validated);

        return back()->with('success', 'Estado de instalación actualizado.');
    }
}
