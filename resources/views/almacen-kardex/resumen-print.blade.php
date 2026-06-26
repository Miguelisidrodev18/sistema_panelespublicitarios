<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Almacén — Resumen de Stock</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; font-size: 12px; padding: 20px; color: #333; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #333; padding-bottom: 10px; }
        .header h1 { font-size: 16px; margin-bottom: 4px; }
        .info { margin-bottom: 10px; }
        .info-label { font-weight: bold; }
        .section { margin-bottom: 20px; }
        .section-title { font-size: 13px; font-weight: bold; background: #f0f0f0; padding: 6px 10px; margin-bottom: 8px; border-left: 3px solid #333; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #999; padding: 6px 8px; text-align: left; }
        th { background: #f0f0f0; font-weight: bold; font-size: 11px; text-transform: uppercase; }
        td { font-size: 11px; }
        .text-center { text-align: center; }
        .fw-bold { font-weight: bold; }
        @media print {
            body { padding: 0; }
            @page { margin: 15mm; }
        }
    </style>
</head>
<body onload="window.print()">
    <div class="header">
        <h1>Almacén de herramientas, material y equipo de personal</h1>
    </div>

    @if($responsable)
    <div class="info">
        <span class="info-label">Responsable:</span> {{ $responsable->nombre_completo }}
    </div>
    @endif

    @forelse($itemsPorTipo as $tipo => $itemsGrupo)
    <div class="section">
        <div class="section-title">{{ \App\Models\AlmacenItem::TIPOS[$tipo] ?? ucfirst($tipo) }}</div>
        <table>
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Código</th>
                    <th class="text-center">Cantidad</th>
                    <th>Unidad</th>
                </tr>
            </thead>
            <tbody>
                @foreach($itemsGrupo as $item)
                <tr>
                    <td class="fw-bold">{{ $item->nombre }}</td>
                    <td>{{ $item->codigo ?? '—' }}</td>
                    <td class="text-center fw-bold">{{ number_format($item->stock_actual, $item->tipo === 'materiales' ? 2 : 0) }}</td>
                    <td>{{ $item->unidad_medida }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @empty
    <p style="text-align:center;padding:30px;color:#999">No hay ítems con stock disponible</p>
    @endforelse
</body>
</html>
