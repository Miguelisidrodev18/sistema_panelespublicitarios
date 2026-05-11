<?php

namespace App\Http\Controllers;

use App\Models\Cotizacion;
use App\Models\CotizacionElemento;
use App\Models\Contrato;
use App\Models\Empresa;
use App\Models\PanelDigital;
use App\Models\PanelUbicacion;
use Illuminate\Http\Request;

class CotizacionController extends Controller
{
    public function index(Request $request)
    {
        $query = Cotizacion::with('empresa')->latest();

        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        if ($request->filled('empresa_id')) {
            $query->where('empresa_id', $request->empresa_id);
        }

        if ($request->filled('buscar')) {
            $buscar = $request->buscar;
            $query->where(function ($q) use ($buscar) {
                $q->where('cliente_nombre', 'like', "%{$buscar}%")
                  ->orWhere('cliente_empresa', 'like', "%{$buscar}%")
                  ->orWhere('numero', 'like', "%{$buscar}%");
            });
        }

        $cotizaciones          = $query->paginate(20);
        $empresas              = Empresa::activas()->orderBy('nombre')->get();
        $paneles_digitales     = PanelDigital::where('activo', true)->orderBy('codigo')->get();
        $paneles_tradicionales = PanelUbicacion::where('activo', true)->orderBy('codigo')->get();
        $numero                = 'COT-' . date('Y') . '-' . str_pad(Cotizacion::whereYear('created_at', date('Y'))->count() + 1, 4, '0', STR_PAD_LEFT);

        $stats_cot = [
            'total'      => Cotizacion::count(),
            'pendientes' => Cotizacion::where('estado', 'pendiente')->count(),
            'aprobadas'  => Cotizacion::where('estado', 'aprobada')->count(),
            'monto'      => Cotizacion::whereIn('estado', ['pendiente', 'aprobada'])->sum('monto_propuesto'),
        ];

        return view('cotizaciones.index', compact(
            'cotizaciones', 'empresas', 'paneles_digitales', 'paneles_tradicionales', 'numero', 'stats_cot'
        ));
    }

    public function create()
    {
        $numero             = 'COT-' . date('Y') . '-' . str_pad(Cotizacion::whereYear('created_at', date('Y'))->count() + 1, 4, '0', STR_PAD_LEFT);
        $empresas           = Empresa::activas()->orderBy('nombre')->get();
        $paneles_digitales  = PanelDigital::where('activo', true)->orderBy('codigo')->get();
        $paneles_tradicionales = PanelUbicacion::where('activo', true)->orderBy('codigo')->get();
        return view('cotizaciones.create', compact('numero', 'empresas', 'paneles_digitales', 'paneles_tradicionales'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'empresa_id'        => 'nullable|exists:empresas,id',
            'numero'            => 'nullable|string|max:50',
            'cliente_nombre'    => 'nullable|string|max:200',
            'cliente_empresa'   => 'nullable|string|max:200',
            'cliente_telefono'  => 'nullable|string|max:50',
            'cliente_email'     => 'nullable|email|max:100',
            'tipo_contrato'     => 'nullable|string|max:50',
            'monto_propuesto'   => 'nullable|numeric|min:0',
            'fecha_cotizacion'  => 'nullable|date',
            'fecha_vencimiento' => 'nullable|date',
            'notas'             => 'nullable|string',
        ]);

        $validated['fecha_cotizacion'] = $validated['fecha_cotizacion'] ?? now()->toDateString();

        // Auto-rellenar datos del cliente desde la empresa si no se completaron
        if (!empty($validated['empresa_id']) && empty($validated['cliente_nombre'])) {
            $empresa = Empresa::find($validated['empresa_id']);
            $validated['cliente_nombre']  = $empresa->encargado ?: $empresa->nombre;
            $validated['cliente_empresa'] = $empresa->nombre;
        }

        $cotizacion = Cotizacion::create($validated);

        // Guardar paneles de interés
        $panelIds  = $request->input('elemento_panel_id', []);
        $tipos     = $request->input('elemento_tipo', []);
        $codigos   = $request->input('elemento_codigo', []);
        $tiempos   = $request->input('elemento_tiempo', []);
        $precios   = $request->input('elemento_precio', []);

        foreach ($panelIds as $i => $panelId) {
            if (!$panelId) continue;
            CotizacionElemento::create([
                'cotizacion_id'  => $cotizacion->id,
                'tipo_elemento'  => $tipos[$i] ?? 'digital',
                'panel_id'       => $panelId,
                'codigo'         => $codigos[$i] ?? '',
                'tiempo_contrato' => $tiempos[$i] ?? null,
                'precio_unitario' => $precios[$i] ?? 0,
            ]);
        }

        return redirect()->route('cotizaciones.show', $cotizacion)->with('success', 'Cotización creada correctamente.');
    }

    public function show(Cotizacion $cotizacion)
    {
        $cotizacion->load('elementos', 'empresa');
        return view('cotizaciones.show', compact('cotizacion'));
    }

    public function edit(Cotizacion $cotizacion)
    {
        $empresas = Empresa::activas()->orderBy('nombre')->get();
        return view('cotizaciones.edit', compact('cotizacion', 'empresas'));
    }

    public function update(Request $request, Cotizacion $cotizacion)
    {
        $validated = $request->validate([
            'empresa_id'        => 'nullable|exists:empresas,id',
            'cliente_nombre'    => 'nullable|string|max:200',
            'cliente_empresa'   => 'nullable|string|max:200',
            'cliente_telefono'  => 'nullable|string|max:50',
            'cliente_email'     => 'nullable|email|max:100',
            'tipo_contrato'     => 'nullable|string|max:50',
            'monto_propuesto'   => 'nullable|numeric|min:0',
            'fecha_cotizacion'  => 'nullable|date',
            'fecha_vencimiento' => 'nullable|date',
            'notas'             => 'nullable|string',
            'estado'            => 'in:pendiente,aprobada,rechazada,convertida',
        ]);

        $cotizacion->update($validated);

        return redirect()->route('cotizaciones.show', $cotizacion)->with('success', 'Cotización actualizada.');
    }

    public function destroy(Cotizacion $cotizacion)
    {
        $cotizacion->delete();
        return redirect()->route('cotizaciones.index')->with('success', 'Cotización eliminada.');
    }

    public function convertirAContrato(Cotizacion $cotizacion)
    {
        $numero = 'CONT-' . date('Y') . '-' . str_pad(Contrato::whereYear('created_at', date('Y'))->count() + 1, 4, '0', STR_PAD_LEFT);
        $empresas = Empresa::activas()->orderBy('nombre')->get();
        return view('cotizaciones.convertir', compact('cotizacion', 'numero', 'empresas'));
    }

    public function guardarContrato(Request $request, Cotizacion $cotizacion)
    {
        $validated = $request->validate([
            'numero_contrato' => 'required|string|max:50|unique:contratos',
            'empresa_id'      => 'nullable|exists:empresas,id',
            'contratante'     => 'required|string|max:200',
            'doc_tipo'        => 'nullable|string|max:20',
            'doc_numero'      => 'nullable|string|max:50',
            'direccion'       => 'nullable|string',
            'tipo_contrato'   => 'required|string|max:50',
            'monto_total'     => 'required|numeric|min:0',
            'adelanto'        => 'nullable|numeric|min:0',
            'fecha_inicio'    => 'nullable|date',
            'fecha_fin'       => 'nullable|date',
            'descripcion'     => 'nullable|string',
        ]);

        $validated['saldo_pendiente'] = $validated['monto_total'] - ($validated['adelanto'] ?? 0);

        Contrato::create($validated);

        $cotizacion->update(['estado' => 'convertida']);

        return redirect()->route('contratos.index')->with('success', 'Contrato creado a partir de la cotización.');
    }
}
