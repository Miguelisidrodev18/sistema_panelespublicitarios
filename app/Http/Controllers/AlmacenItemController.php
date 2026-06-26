<?php

namespace App\Http\Controllers;

use App\Models\Almacen;
use App\Models\AlmacenItem;
use App\Models\Proveedor;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class AlmacenItemController extends Controller
{
    public function index(Request $request)
    {
        $query = AlmacenItem::with(['almacen', 'proveedor']);

        if ($buscar = $request->input('buscar')) {
            $query->where(function ($q) use ($buscar) {
                $q->where('nombre', 'like', "%{$buscar}%")
                  ->orWhere('codigo', 'like', "%{$buscar}%")
                  ->orWhere('marca', 'like', "%{$buscar}%");
            });
        }

        if ($tipo = $request->input('tipo')) {
            $query->where('tipo', $tipo);
        }

        if ($almacenId = $request->input('almacen_id')) {
            $query->where('almacen_id', $almacenId);
        }

        if ($estado = $request->input('estado')) {
            $query->where('estado', $estado);
        }

        $items = $query->orderByDesc('created_at')->paginate(20)->withQueryString();

        $stats = [
            'total'         => AlmacenItem::activos()->count(),
            'maquina'       => AlmacenItem::activos()->where('tipo', 'maquina')->count(),
            'herramienta'   => AlmacenItem::activos()->where('tipo', 'herramienta')->count(),
            'indumentaria'  => AlmacenItem::activos()->where('tipo', 'indumentaria')->count(),
            'materiales'    => AlmacenItem::activos()->where('tipo', 'materiales')->count(),
        ];

        $almacenes = Almacen::activos()->orderBy('nombre')->get();

        return view('almacen-items.index', compact('items', 'stats', 'almacenes'));
    }

    public function create()
    {
        $almacenes = Almacen::activos()->orderBy('nombre')->get();
        $proveedores = Proveedor::activos()->orderBy('razon_social')->get();
        return view('almacen-items.create', compact('almacenes', 'proveedores'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'almacen_id'     => 'required|exists:almacenes,id',
            'nombre'         => 'required|string|max:200',
            'codigo'         => 'nullable|string|max:50',
            'marca'          => 'nullable|string|max:100',
            'serie'          => 'nullable|string|max:100',
            'tipo'           => 'required|in:maquina,herramienta,indumentaria,materiales',
            'unidad_medida'  => 'nullable|string|max:30',
            'anio_compra'    => 'nullable|integer|min:1900|max:' . (date('Y') + 1),
            'proveedor_id'   => 'nullable|exists:proveedores,id',
            'estado'         => 'in:activo,inactivo',
        ]);

        if ($validated['tipo'] !== 'materiales') {
            $validated['unidad_medida'] = 'unidad';
        } elseif (empty($validated['unidad_medida'])) {
            $validated['unidad_medida'] = 'unidad';
        }

        $item = AlmacenItem::create($validated);

        ActivityLog::registrar('created', 'AlmacenItem', $item->id, "Ítem '{$item->nombre}' registrado en almacén");

        return redirect()->route('almacen-items.index')->with('success', 'Ítem registrado correctamente.');
    }

    public function show(AlmacenItem $item)
    {
        $item->load(['almacen', 'proveedor', 'movimientos' => function ($q) {
            $q->with(['responsable', 'proveedor'])->orderByDesc('fecha')->orderByDesc('id')->limit(20);
        }]);
        return view('almacen-items.show', compact('item'));
    }

    public function edit(AlmacenItem $item)
    {
        $almacenes = Almacen::activos()->orderBy('nombre')->get();
        $proveedores = Proveedor::activos()->orderBy('razon_social')->get();
        return view('almacen-items.edit', compact('item', 'almacenes', 'proveedores'));
    }

    public function update(Request $request, AlmacenItem $item)
    {
        $validated = $request->validate([
            'almacen_id'     => 'required|exists:almacenes,id',
            'nombre'         => 'required|string|max:200',
            'codigo'         => 'nullable|string|max:50',
            'marca'          => 'nullable|string|max:100',
            'serie'          => 'nullable|string|max:100',
            'tipo'           => 'required|in:maquina,herramienta,indumentaria,materiales',
            'unidad_medida'  => 'nullable|string|max:30',
            'anio_compra'    => 'nullable|integer|min:1900|max:' . (date('Y') + 1),
            'proveedor_id'   => 'nullable|exists:proveedores,id',
            'estado'         => 'in:activo,inactivo',
        ]);

        if ($validated['tipo'] !== 'materiales') {
            $validated['unidad_medida'] = 'unidad';
        } elseif (empty($validated['unidad_medida'])) {
            $validated['unidad_medida'] = 'unidad';
        }

        $item->update($validated);

        ActivityLog::registrar('updated', 'AlmacenItem', $item->id, "Ítem '{$item->nombre}' actualizado");

        return redirect()->route('almacen-items.index')->with('success', 'Ítem actualizado correctamente.');
    }

    public function destroy(AlmacenItem $item)
    {
        $item->update(['estado' => 'inactivo']);

        ActivityLog::registrar('deleted', 'AlmacenItem', $item->id, "Ítem '{$item->nombre}' desactivado");

        return redirect()->route('almacen-items.index')->with('success', 'Ítem desactivado.');
    }
}
