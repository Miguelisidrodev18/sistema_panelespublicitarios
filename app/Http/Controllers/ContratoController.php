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

        // Registrar el adelanto automáticamente como cobro para que figure en ingresos
        if (!empty($validated['adelanto']) && $validated['adelanto'] > 0) {
            ContratoCobro::create([
                'contrato_id' => $contrato->id,
                'tipo_cobro'  => 'Adelanto',
                'monto'       => $validated['adelanto'],
                'fecha_cobro' => $validated['fecha_inicio'] ?? now()->toDateString(),
                'notas'       => 'Adelanto registrado al crear el contrato',
            ]);
        }

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

        // Preservar adelanto si no se envió o viene vacío (columna NOT NULL en BD)
        $validated['adelanto'] = $validated['adelanto'] ?? $contrato->adelanto ?? 0;

        $contrato->update($validated);

        return redirect()->route('contratos.show', $contrato)->with('success', 'Contrato actualizado.');
    }

    public function generarCuotas(Request $request, Contrato $contrato)
    {
        $request->validate([
            'num_cuotas'    => 'required|integer|min:1|max:120',
            'primera_fecha' => 'required|date',
        ]);

        $saldo          = (float)$contrato->saldo_pendiente;
        $numCuotas      = (int)$request->num_cuotas;
        $monto_cuota    = round($saldo / $numCuotas, 2);
        $fecha          = \Carbon\Carbon::parse($request->primera_fecha);
        $mesesIntervalo = $contrato->mesesFrecuencia();

        for ($i = 1; $i <= $numCuotas; $i++) {
            Cobranza::create([
                'empresa_id'        => $contrato->empresa_id,
                'contrato_id'       => $contrato->id,
                'numero_cuota'      => $i,
                'monto'             => $monto_cuota,
                'fecha_vencimiento' => $fecha->copy(),
                'estado'            => 'pendiente',
                'concepto'          => "Contrato {$contrato->numero_contrato} — Cuota {$i}/{$numCuotas}",
            ]);
            $fecha->addMonths($mesesIntervalo);
        }

        return back()->with('success', "Se generaron {$numCuotas} cuota(s) de S/. " . number_format($monto_cuota, 2) . " cada una.");
    }

    public function importarDeCotizacion(Request $request, Contrato $contrato)
    {
        $request->validate(['cotizacion_id' => 'required|exists:cotizaciones,id']);

        $cotizacion = \App\Models\Cotizacion::with('elementos')->find($request->cotizacion_id);

        $importados = 0;
        foreach ($cotizacion->elementos as $elem) {
            if (!in_array($elem->tipo_elemento, ['digital', 'tradicional'])) continue;

            // Evitar duplicados: omitir si ya existe el mismo codigo+tipo
            $existe = $contrato->elementos()
                ->where('tipo_elemento', $elem->tipo_elemento)
                ->where('codigo', $elem->codigo)
                ->exists();
            if ($existe) continue;

            ContratoElemento::create([
                'contrato_id'        => $contrato->id,
                'tipo_elemento'      => $elem->tipo_elemento,
                'panel_id'           => $elem->panel_id,
                'codigo'             => $elem->codigo,
                'tiempo_contrato'    => $elem->tiempo_contrato,
                'observaciones'      => $elem->observaciones,
                'estado_instalacion' => 'pendiente_instalacion',
            ]);
            $importados++;
        }

        // Vincular la cotizacion a este contrato si no estaba vinculado
        if (!$contrato->cotizacion_id) {
            $contrato->update(['cotizacion_id' => $cotizacion->id]);
        }

        // Registrar el adelanto como cobro si existe y aún no hay un cobro de tipo Adelanto
        $adelanto = (float)($contrato->adelanto ?? 0);
        $yaExisteAdelanto = $contrato->cobros()->where('tipo_cobro', 'Adelanto')->exists();

        if ($adelanto > 0 && !$yaExisteAdelanto) {
            ContratoCobro::create([
                'contrato_id' => $contrato->id,
                'tipo_cobro'  => 'Adelanto',
                'monto'       => $adelanto,
                'fecha_cobro' => $contrato->fecha_inicio?->toDateString() ?? now()->toDateString(),
                'notas'       => 'Adelanto importado desde cotización ' . $cotizacion->numero,
            ]);
            $msgAdelanto = " y adelanto de S/. " . number_format($adelanto, 2) . " registrado";
        }

        $msg = "Se importaron {$importados} elemento(s) desde {$cotizacion->numero}" . ($msgAdelanto ?? '') . ".";
        return back()->with('success', $msg);
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
