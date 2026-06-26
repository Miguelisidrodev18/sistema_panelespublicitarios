<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; font-size: 11px; color: #333; }
        .header { text-align: center; margin-bottom: 15px; border-bottom: 2px solid #333; padding-bottom: 8px; }
        .header h1 { font-size: 14px; margin-bottom: 3px; }
        .info { margin-bottom: 8px; }
        .info-label { font-weight: bold; }
        .section { margin-bottom: 15px; }
        .section-title { font-size: 12px; font-weight: bold; background: #f0f0f0; padding: 4px 8px; margin-bottom: 6px; border-left: 3px solid #333; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #999; padding: 4px 6px; text-align: left; font-size: 10px; }
        th { background: #f0f0f0; font-weight: bold; text-transform: uppercase; }
        .text-center { text-align: center; }
        .fw-bold { font-weight: bold; }
    </style>
</head>
<body>
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
