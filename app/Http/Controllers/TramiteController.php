<?php

namespace App\Http\Controllers;

use App\Models\Tramite;
use App\Models\TramiteProceso;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TramiteController extends Controller
{

    public function index(Request $request)
    {
        $query = Tramite::latest();

        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        if ($request->filled('buscar')) {
            $b = $request->buscar;
            $query->where(function ($q) use ($b) {
                $q->where('numero', 'like', "%{$b}%")
                  ->orWhere('tipo', 'like', "%{$b}%")
                  ->orWhere('entidad_nombre', 'like', "%{$b}%")
                  ->orWhere('codigo_tramite', 'like', "%{$b}%");
            });
        }

        $tramites = $query->with('procesos')->paginate(20);

        $stats = [
            'total'      => Tramite::count(),
            'en_tramite' => Tramite::where('estado', 'en_tramite')->count(),
            'observados' => Tramite::where('estado', 'observado')->count(),
            'aprobados'  => Tramite::where('estado', 'aprobado')->count(),
        ];

        $estados = Tramite::estadosParaSelect();

        return view('tramites.index', compact('tramites', 'stats', 'estados'));
    }

    public function create()
    {
        $numero  = $this->generarNumero();
        $estados = Tramite::estadosParaSelect();
        return view('tramites.create', compact('numero', 'estados'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'tipo'                => 'nullable|string|max:200',
            'entidad_nombre'      => 'nullable|string|max:200',
            'entidad_expediente'  => 'nullable|string|max:200',
            'codigo_tramite'      => 'nullable|string|max:100',
            'area_actual'         => 'nullable|string|max:200',
            'encargado'           => 'nullable|string|max:200',
            'doc_presentado'      => 'nullable|string',
            'encargado_area'      => 'nullable|string|max:200',
            'contacto'            => 'nullable|string|max:200',
            'apunte_adicional'    => 'nullable|string',
            'archivo_pdf'         => 'nullable|file|mimes:pdf|max:5120',
            'fecha_ingreso'       => 'nullable|date',
            'fecha_modificacion'  => 'nullable|date',
            'fecha_vencimiento'   => 'nullable|date',
            'estado'              => 'nullable|string|max:30',
        ]);

        $validated['numero']        = $this->generarNumero();
        $validated['fecha_ingreso'] = $validated['fecha_ingreso'] ?? now()->toDateString();
        $validated['activo']        = true;

        if ($request->hasFile('archivo_pdf')) {
            $validated['archivo_pdf'] = $request->file('archivo_pdf')->store('tramites/pdf', 'public');
        }

        $tramite = Tramite::create($validated);

        // Guardar pasos del proceso
        $this->sincronizarProcesos($request, $tramite);

        ActivityLog::registrar('created', 'Tramite', $tramite->id, "Trámite {$tramite->numero} creado");

        return redirect()->route('tramites.show', $tramite)
            ->with('success', 'Trámite creado correctamente.');
    }

    public function show(Tramite $tramite)
    {
        $tramite->load('procesos');
        return view('tramites.show', compact('tramite'));
    }

    public function edit(Tramite $tramite)
    {
        $tramite->load('procesos');
        $estados = Tramite::estadosParaSelect();
        return view('tramites.edit', compact('tramite', 'estados'));
    }

    public function update(Request $request, Tramite $tramite)
    {
        $validated = $request->validate([
            'tipo'                => 'nullable|string|max:200',
            'entidad_nombre'      => 'nullable|string|max:200',
            'entidad_expediente'  => 'nullable|string|max:200',
            'codigo_tramite'      => 'nullable|string|max:100',
            'area_actual'         => 'nullable|string|max:200',
            'encargado'           => 'nullable|string|max:200',
            'doc_presentado'      => 'nullable|string',
            'encargado_area'      => 'nullable|string|max:200',
            'contacto'            => 'nullable|string|max:200',
            'apunte_adicional'    => 'nullable|string',
            'archivo_pdf'         => 'nullable|file|mimes:pdf|max:5120',
            'fecha_ingreso'       => 'nullable|date',
            'fecha_modificacion'  => 'nullable|date',
            'fecha_vencimiento'   => 'nullable|date',
            'estado'              => 'nullable|string|max:30',
        ]);

        $validated['fecha_modificacion'] = now()->toDateString();

        if ($request->hasFile('archivo_pdf')) {
            if ($tramite->archivo_pdf) {
                Storage::disk('public')->delete($tramite->archivo_pdf);
            }
            $validated['archivo_pdf'] = $request->file('archivo_pdf')->store('tramites/pdf', 'public');
        }

        $tramite->update($validated);

        // Sincronizar procesos
        $tramite->procesos()->delete();
        $this->sincronizarProcesos($request, $tramite);

        ActivityLog::registrar('updated', 'Tramite', $tramite->id, "Trámite {$tramite->numero} actualizado");

        return redirect()->route('tramites.show', $tramite)
            ->with('success', 'Trámite actualizado correctamente.');
    }

    public function destroy(Tramite $tramite)
    {
        $tramite->delete();
        return redirect()->route('tramites.index')
            ->with('success', 'Trámite eliminado.');
    }

    public function subirPdf(Request $request, Tramite $tramite)
    {
        $request->validate(['archivo_pdf' => 'required|file|mimes:pdf|max:5120']);

        if ($tramite->archivo_pdf) {
            Storage::disk('public')->delete($tramite->archivo_pdf);
        }

        $tramite->update([
            'archivo_pdf' => $request->file('archivo_pdf')->store('tramites/pdf', 'public'),
        ]);

        return response()->json(['ok' => true, 'url' => Storage::url($tramite->archivo_pdf)]);
    }

    public function eliminarPdf(Tramite $tramite)
    {
        if ($tramite->archivo_pdf) {
            Storage::disk('public')->delete($tramite->archivo_pdf);
            $tramite->update(['archivo_pdf' => null]);
        }

        return response()->json(['ok' => true]);
    }

    public function agregarPaso(Request $request, Tramite $tramite)
    {
        $request->validate([
            'area'               => 'required|string|max:200',
            'numero_notificacion'=> 'nullable|string|max:100',
            'observacion'        => 'nullable|string|max:500',
            'estado'             => 'required|in:pendiente,en_proceso,finalizado',
        ]);

        $orden = $tramite->procesos()->max('orden') + 1;

        $paso = TramiteProceso::create([
            'tramite_id'          => $tramite->id,
            'area'                => $request->area,
            'numero_notificacion' => $request->numero_notificacion,
            'observacion'         => $request->observacion,
            'estado'              => $request->estado,
            'orden'               => $orden,
        ]);

        return response()->json([
            'ok'          => true,
            'paso'        => [
                'id'                 => $paso->id,
                'orden'              => $paso->orden,
                'area'               => $paso->area,
                'numero_notificacion'=> $paso->numero_notificacion,
                'observacion'        => $paso->observacion,
                'estado'             => $paso->estado,
                'badge_color'        => $paso->badge_color,
                'badge_label'        => $paso->badge_label,
            ],
        ]);
    }

    public function imprimirProceso(Tramite $tramite)
    {
        $tramite->load('procesos');
        $empresa_propia = config('empresa');
        return view('tramites.print-proceso', compact('tramite', 'empresa_propia'));
    }

    // ── Helpers ──────────────────────────────────────────────

    private function generarNumero(): string
    {
        $count = Tramite::whereYear('created_at', date('Y'))->count() + 1;
        return 'TRA-' . date('Y') . '-' . str_pad($count, 3, '0', STR_PAD_LEFT);
    }

    private function sincronizarProcesos(Request $request, Tramite $tramite): void
    {
        $areas         = $request->input('proc_area', []);
        $notificaciones = $request->input('proc_notificacion', []);
        $observaciones  = $request->input('proc_observacion', []);
        $estadosPasos   = $request->input('proc_estado', []);

        foreach ($areas as $i => $area) {
            if (!$area) continue;
            TramiteProceso::create([
                'tramite_id'          => $tramite->id,
                'area'                => $area,
                'numero_notificacion' => $notificaciones[$i] ?? null,
                'observacion'         => $observaciones[$i] ?? null,
                'estado'              => $estadosPasos[$i] ?? 'pendiente',
                'orden'               => $i + 1,
            ]);
        }
    }
}
