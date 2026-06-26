<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; font-size: 11px; color: #333; }
        .header { text-align: center; margin-bottom: 15px; border-bottom: 2px solid #333; padding-bottom: 8px; }
        .header h1 { font-size: 14px; margin-bottom: 3px; }
        .header h2 { font-size: 11px; font-weight: normal; color: #666; }
        .info { margin-bottom: 12px; }
        .info-row { margin-bottom: 3px; }
        .info-label { font-weight: bold; }
        table { width: 100%; border-collapse: collapse; margin-top: 8px; }
        th, td { border: 1px solid #999; padding: 4px 6px; text-align: left; font-size: 10px; }
        th { background: #f0f0f0; font-weight: bold; text-transform: uppercase; }
        .text-center { text-align: center; }
        .fw-bold { font-weight: bold; }
        .entrada { color: #198754; }
        .salida { color: #dc3545; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Control de herramientas, material y equipo de personal</h1>
        <h2>KARDEX BÚHO</h2>
    </div>

    <div class="info">
        <div class="info-row">
            <span class="info-label">Ítem:</span>
            {{ $item->nombre }} {{ $item->codigo ? '(' . $item->codigo . ')' : '' }}
        </div>
        <div class="info-row">
            <span class="info-label">Tipo:</span>
            {{ $item->tipo_label }}
        </div>
        @if($responsable)
        <div class="info-row">
            <span class="info-label">Responsable:</span>
            {{ $responsable->nombre_completo }}
        </div>
        @endif
        <div class="info-row">
            <span class="info-label">Almacén:</span>
            {{ $item->almacen->nombre ?? '—' }}
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Fecha</th>
                <th>Detalle</th>
                <th class="text-center">Entradas Cant.</th>
                <th class="text-center">Salida Cant.</th>
                <th class="text-center">Saldo</th>
            </tr>
        </thead>
        <tbody>
            @forelse($movimientos as $mov)
            <tr>
                <td>{{ $mov->fecha->format('d/m/Y') }}</td>
                <td>{{ $mov->detalle ?? $item->nombre }}</td>
                <td class="text-center fw-bold entrada">
                    {{ $mov->tipo_movimiento === 'entrada' ? number_format($mov->cantidad, 2) . ' ' . $item->unidad_medida : '' }}
                </td>
                <td class="text-center fw-bold salida">
                    {{ $mov->tipo_movimiento === 'salida' ? number_format($mov->cantidad, 2) . ' ' . $item->unidad_medida : '' }}
                </td>
                <td class="text-center fw-bold">{{ number_format($mov->saldo, 2) }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="text-center">Sin movimientos</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
