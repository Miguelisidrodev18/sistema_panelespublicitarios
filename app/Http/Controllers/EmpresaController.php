<?php

namespace App\Http\Controllers;

use App\Models\Empresa;
use App\Models\Servicio;
use App\Models\PanelDigital;
use App\Models\PanelUbicacion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class EmpresaController extends Controller
{
    public function index(Request $request)
    {
        $query = Empresa::query();

        if ($request->filled('buscar')) {
            $query->where('nombre', 'like', '%' . $request->buscar . '%');
        }

        if ($request->filled('estado')) {
            $query->where('activo', $request->estado === 'activo');
        }

        $empresas = $query->orderBy('nombre')->paginate(20);

        return view('empresas.index', compact('empresas'));
    }

    public function create()
    {
        $servicios = Servicio::activos()->get();
        $paneles_digitales = PanelDigital::activos()->get();
        $paneles_tradicionales = PanelUbicacion::activos()->get();

        return view('empresas.create', compact('servicios', 'paneles_digitales', 'paneles_tradicionales'));
    }

    public function store(Request $request)
    {
        $validated = $this->validateEmpresa($request);

        $empresa = Empresa::create($validated);

        $this->syncRelaciones($empresa, $request);

        return redirect()->route('empresas.show', $empresa)
            ->with('success', 'Empresa creada correctamente.');
    }

    public function show(Empresa $empresa)
    {
        $empresa->load(['cobranzas', 'servicios', 'panalesDigitales', 'panalesTradicionales', 'fotos', 'documentos']);

        return view('empresas.show', compact('empresa'));
    }

    public function edit(Empresa $empresa)
    {
        $servicios = Servicio::activos()->get();
        $paneles_digitales = PanelDigital::activos()->get();
        $paneles_tradicionales = PanelUbicacion::activos()->get();

        return view('empresas.edit', compact('empresa', 'servicios', 'paneles_digitales', 'paneles_tradicionales'));
    }

    public function update(Request $request, Empresa $empresa)
    {
        $validated = $this->validateEmpresa($request, $empresa->id);

        $empresa->update($validated);

        $this->syncRelaciones($empresa, $request);

        return redirect()->route('empresas.show', $empresa)
            ->with('success', 'Empresa actualizada correctamente.');
    }

    public function destroy(Empresa $empresa)
    {
        $empresa->update(['activo' => false]);

        return redirect()->route('empresas.index')
            ->with('success', 'Empresa desactivada correctamente.');
    }

    private function validateEmpresa(Request $request, ?int $ignoreId = null): array
    {
        $rules = [
            'nombre' => 'required|string|max:200',
            'correo' => 'nullable|email|max:150',
            'celular' => 'nullable|string|max:20',
            'panel_digital' => 'boolean',
            'panel_tradicional' => 'boolean',
            'marketing_digital' => 'boolean',
            'otros_servicios' => 'nullable|string',
            'tipo_contrato' => 'nullable|in:mensual,convenio,eventual',
            'detalles_convenio' => 'nullable|string',
            'bonificacion' => 'boolean',
            'comentario_bonificacion' => 'nullable|string',
            'adendas_pagos' => 'boolean',
            'comentario_adendas' => 'nullable|string',
            'encargado' => 'nullable|string|max:200',
            'monto' => 'nullable|numeric|min:0',
            'dias_duracion' => 'nullable|integer|min:1',
            'fecha_inicio' => 'nullable|date',
            'fecha_fin' => 'nullable|date|after_or_equal:fecha_inicio',
            'contrato_pdf' => 'nullable|file|mimes:pdf|max:10240',
        ];

        $validated = $request->validate($rules);

        if ($request->hasFile('contrato_pdf')) {
            $validated['contrato_pdf'] = $request->file('contrato_pdf')->store('contratos', 'public');
        }

        return $validated;
    }

    private function syncRelaciones(Empresa $empresa, Request $request): void
    {
        if ($request->has('servicios')) {
            $servicios = collect($request->servicios)->mapWithKeys(fn($s) => [
                $s['id'] => ['monto' => $s['monto'] ?? 0]
            ]);
            $empresa->servicios()->sync($servicios);
        }

        if ($request->has('paneles_digitales')) {
            $empresa->panalesDigitales()->sync($request->paneles_digitales);
        }

        if ($request->has('paneles_tradicionales')) {
            $empresa->panalesTradicionales()->sync($request->paneles_tradicionales);
        }
    }
}
