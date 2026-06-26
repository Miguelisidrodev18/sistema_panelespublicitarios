<?php

namespace App\Http\Controllers;

use App\Models\AlmacenItem;
use App\Models\AlmacenMovimiento;
use App\Models\Proveedor;
use App\Models\PanelDigital;
use App\Models\PanelUbicacion;
use App\Models\Usuario;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class AlmacenMovimientoController extends Controller
{
    public function kardex(Request $request)
    {
        $items = AlmacenItem::activos()->with('almacen')->orderBy('nombre')->get();
        $usuarios = Usuario::where('activo', true)->orderBy('nombre_completo')->get();
        $proveedores = Proveedor::activos()->orderBy('razon_social')->get();
        $panelesDigitales = PanelDigital::where('activo', true)->orderBy('nombre')->get();
        $panelesUbicaciones = PanelUbicacion::where('activo', true)->orderBy('nombre')->get();

        $movimientos = collect();
        $itemSeleccionado = null;

        if ($itemId = $request->input('almacen_item_id')) {
            $itemSeleccionado = AlmacenItem::with('almacen')->find($itemId);

            if ($itemSeleccionado) {
                $query = AlmacenMovimiento::where('almacen_item_id', $itemId)
                    ->with(['responsable', 'proveedor', 'panelDigital', 'panelUbicacion']);

                if ($responsableId = $request->input('responsable_id')) {
                    $query->where('responsable_id', $responsableId);
                }

                if ($panelDigitalId = $request->input('panel_digital_id')) {
                    $query->where('panel_digital_id', $panelDigitalId);
                }

                if ($panelUbicacionId = $request->input('panel_ubicacion_id')) {
                    $query->where('panel_ubicacion_id', $panelUbicacionId);
                }

                if ($fechaDesde = $request->input('fecha_desde')) {
                    $query->where('fecha', '>=', $fechaDesde);
                }

                if ($fechaHasta = $request->input('fecha_hasta')) {
                    $query->where('fecha', '<=', $fechaHasta);
                }

                $movimientos = $query->orderBy('fecha')->orderBy('id')->get();
            }
        }

        $tipoFiltro = $request->input('tipo');
        $accion = $request->input('accion', 'entrada');

        return view('almacen-kardex.index', compact(
            'items', 'usuarios', 'proveedores', 'panelesDigitales', 'panelesUbicaciones',
            'movimientos', 'itemSeleccionado', 'tipoFiltro', 'accion'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'almacen_item_id'    => 'required|exists:almacen_items,id',
            'tipo_movimiento'    => 'required|in:entrada,salida',
            'cantidad'           => 'required|numeric|min:0.01',
            'fecha'              => 'required|date',
            'detalle'            => 'nullable|string|max:300',
            'responsable_id'     => 'nullable|exists:usuarios,id',
            'panel_digital_id'   => 'nullable|exists:paneles_digitales,id',
            'panel_ubicacion_id' => 'nullable|exists:paneles_ubicaciones,id',
            'proveedor_id'       => 'nullable|exists:proveedores,id',
            'observaciones'      => 'nullable|string',
        ]);

        try {
            DB::transaction(function () use ($validated) {
                $item = AlmacenItem::lockForUpdate()->findOrFail($validated['almacen_item_id']);

                $ultimoMovimiento = $item->movimientos()->orderByDesc('id')->first();
                $saldoAnterior = $ultimoMovimiento ? (float) $ultimoMovimiento->saldo : 0;
                $cantidad = (float) $validated['cantidad'];

                if ($validated['tipo_movimiento'] === 'entrada') {
                    $nuevoSaldo = $saldoAnterior + $cantidad;
                } else {
                    $nuevoSaldo = $saldoAnterior - $cantidad;
                    if ($nuevoSaldo < 0) {
                        throw new \Exception("Stock insuficiente. Saldo actual: {$saldoAnterior}");
                    }
                }

                AlmacenMovimiento::create([
                    'almacen_item_id'    => $item->id,
                    'tipo_movimiento'    => $validated['tipo_movimiento'],
                    'cantidad'           => $cantidad,
                    'saldo'              => $nuevoSaldo,
                    'fecha'              => $validated['fecha'],
                    'detalle'            => $validated['detalle'] ?? null,
                    'responsable_id'     => $validated['responsable_id'] ?? null,
                    'panel_digital_id'   => $validated['panel_digital_id'] ?? null,
                    'panel_ubicacion_id' => $validated['panel_ubicacion_id'] ?? null,
                    'proveedor_id'       => $validated['proveedor_id'] ?? null,
                    'registrado_por'     => auth()->id(),
                    'observaciones'      => $validated['observaciones'] ?? null,
                ]);

                $item->update(['stock_actual' => $nuevoSaldo]);

                $tipoLabel = $validated['tipo_movimiento'] === 'entrada' ? 'Entrada' : 'Salida';
                ActivityLog::registrar('created', 'AlmacenMovimiento', $item->id,
                    "{$tipoLabel} de {$cantidad} {$item->unidad_medida} de '{$item->nombre}'");
            });

            return redirect()
                ->route('almacen-kardex.index', ['almacen_item_id' => $validated['almacen_item_id']])
                ->with('success', 'Movimiento registrado correctamente.');

        } catch (\Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function resumen(Request $request)
    {
        $query = AlmacenItem::activos()->with('almacen')
            ->where('stock_actual', '>', 0);

        if ($tipo = $request->input('tipo')) {
            $query->where('tipo', $tipo);
        }

        if ($responsableId = $request->input('responsable_id')) {
            $itemIds = AlmacenMovimiento::where('responsable_id', $responsableId)
                ->distinct()->pluck('almacen_item_id');
            $query->whereIn('id', $itemIds);
        }

        $itemsPorTipo = $query->orderBy('tipo')->orderBy('nombre')->get()->groupBy('tipo');

        $usuarios = Usuario::where('activo', true)->orderBy('nombre_completo')->get();

        return view('almacen-kardex.resumen', compact('itemsPorTipo', 'usuarios'));
    }

    public function imprimirKardex(Request $request)
    {
        $itemId = $request->input('almacen_item_id');
        $item = AlmacenItem::with('almacen')->findOrFail($itemId);

        $query = AlmacenMovimiento::where('almacen_item_id', $itemId)
            ->with(['responsable', 'panelDigital', 'panelUbicacion']);

        if ($responsableId = $request->input('responsable_id')) {
            $query->where('responsable_id', $responsableId);
        }

        $movimientos = $query->orderBy('fecha')->orderBy('id')->get();

        $responsable = null;
        if ($responsableId = $request->input('responsable_id')) {
            $responsable = Usuario::find($responsableId);
        }

        return view('almacen-kardex.print', compact('item', 'movimientos', 'responsable'));
    }

    public function descargarKardexPdf(Request $request)
    {
        $itemId = $request->input('almacen_item_id');
        $item = AlmacenItem::with('almacen')->findOrFail($itemId);

        $query = AlmacenMovimiento::where('almacen_item_id', $itemId)
            ->with(['responsable', 'panelDigital', 'panelUbicacion']);

        if ($responsableId = $request->input('responsable_id')) {
            $query->where('responsable_id', $responsableId);
        }

        $movimientos = $query->orderBy('fecha')->orderBy('id')->get();

        $responsable = null;
        if ($responsableId = $request->input('responsable_id')) {
            $responsable = Usuario::find($responsableId);
        }

        $pdf = Pdf::loadView('almacen-kardex.print-pdf', compact('item', 'movimientos', 'responsable'))
            ->setPaper('a4', 'landscape');

        return $pdf->download("Kardex-{$item->codigo}.pdf");
    }

    public function imprimirResumen(Request $request)
    {
        $query = AlmacenItem::activos()->with('almacen')
            ->where('stock_actual', '>', 0);

        if ($tipo = $request->input('tipo')) {
            $query->where('tipo', $tipo);
        }

        $itemsPorTipo = $query->orderBy('tipo')->orderBy('nombre')->get()->groupBy('tipo');

        $responsable = null;
        if ($responsableId = $request->input('responsable_id')) {
            $responsable = Usuario::find($responsableId);
        }

        return view('almacen-kardex.resumen-print', compact('itemsPorTipo', 'responsable'));
    }

    public function descargarResumenPdf(Request $request)
    {
        $query = AlmacenItem::activos()->with('almacen')
            ->where('stock_actual', '>', 0);

        if ($tipo = $request->input('tipo')) {
            $query->where('tipo', $tipo);
        }

        $itemsPorTipo = $query->orderBy('tipo')->orderBy('nombre')->get()->groupBy('tipo');

        $responsable = null;
        if ($responsableId = $request->input('responsable_id')) {
            $responsable = Usuario::find($responsableId);
        }

        $pdf = Pdf::loadView('almacen-kardex.resumen-pdf', compact('itemsPorTipo', 'responsable'))
            ->setPaper('a4', 'portrait');

        return $pdf->download('Almacen-Resumen.pdf');
    }
}
