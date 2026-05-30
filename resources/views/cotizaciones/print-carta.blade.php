<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Propuesta — {{ $cotizacion->numero }}</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: Arial, sans-serif; font-size: 11px; color: #1a1a1a; background: #fff; line-height: 1.5; }

        .page { width: 210mm; min-height: 297mm; margin: 0 auto; padding: 14mm 16mm 14mm; background: #fff; position: relative; }

        /* Cabecera */
        .header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 18px; border-bottom: 3px solid #DC1E2E; padding-bottom: 12px; }
        .header-left { display: flex; align-items: center; gap: 12px; }
        .header-logo img { height: 60px; width: auto; }
        .header-logo-placeholder { height: 60px; width: 60px; background: #DC1E2E; border-radius: 8px; display: flex; align-items: center; justify-content: center; color: #fff; font-weight: 900; font-size: 22px; }
        .header-brand { font-size: 19px; font-weight: 900; color: #1a1a1a; line-height: 1.1; }
        .header-brand span { color: #DC1E2E; }
        .header-tagline { font-size: 8px; color: #64748B; text-transform: uppercase; letter-spacing: 1.5px; margin-top: 3px; }
        .header-date { font-size: 11px; color: #374151; font-style: italic; text-align: right; padding-top: 4px; }

        /* Destinatario */
        .destinatario { margin-bottom: 16px; }
        .dest-ciudad { font-style: italic; color: #374151; margin-bottom: 10px; font-size: 11px; }
        .dest-title { font-weight: 700; font-size: 11.5px; margin-bottom: 2px; }
        .dest-nombre { font-weight: 900; font-size: 12.5px; color: #DC1E2E; margin-bottom: 4px; text-transform: uppercase; }
        .dest-saludo { font-style: italic; color: #374151; font-size: 11px; }

        /* Cuerpo de la carta */
        .carta-body { margin-bottom: 14px; }
        .carta-body p { font-size: 11px; color: #1a1a1a; text-align: justify; margin-bottom: 8px; line-height: 1.6; }
        .carta-body p strong { font-weight: 700; }

        /* Subtítulo de sección */
        .section-label { font-size: 10px; font-weight: 800; text-transform: uppercase; letter-spacing: 1px; color: #fff; background: #DC1E2E; padding: 5px 12px; border-radius: 4px 4px 0 0; }

        /* Tabla de propuesta */
        .prop-table { width: 100%; border-collapse: collapse; border: 1px solid #E2E8F0; border-top: none; margin-bottom: 14px; }
        .prop-table thead tr { background: #F8FAFC; }
        .prop-table th { padding: 7px 10px; font-size: 9.5px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.4px; color: #374151; border-bottom: 2px solid #E2E8F0; text-align: left; }
        .prop-table th.right { text-align: right; }
        .prop-table td { padding: 7px 10px; font-size: 10.5px; color: #1a1a1a; border-bottom: 1px solid #F1F5FB; vertical-align: top; }
        .prop-table td.right { text-align: right; font-weight: 700; }
        .prop-table tbody tr:nth-child(even) td { background: #FAFAFA; }
        .prop-table tbody tr:last-child td { border-bottom: none; }

        /* Fila de categoría (grupo) */
        .group-row td { background: #FEF2F2 !important; font-weight: 700; font-size: 10px; color: #991B1B; padding: 5px 10px; border-bottom: 1px solid #FEE2E2; }

        /* Descripción con bullet points */
        .desc-list { list-style: none; padding: 0; margin: 0; }
        .desc-list li { padding: 1px 0; font-size: 10px; color: #374151; }
        .desc-list li::before { content: "• "; color: #DC1E2E; font-weight: 700; }
        .item-name { font-weight: 700; color: #1a1a1a; margin-bottom: 2px; font-size: 11px; }

        /* Totales */
        .totals-wrapper { display: flex; justify-content: flex-end; margin-bottom: 14px; }
        .totals-box { width: 230px; border: 1px solid #E2E8F0; border-radius: 6px; overflow: hidden; }
        .totals-line { display: flex; justify-content: space-between; padding: 6px 12px; font-size: 10.5px; border-bottom: 1px solid #F1F5FB; }
        .totals-line:last-child { border-bottom: none; }
        .totals-line .t-lbl { color: #374151; font-weight: 600; }
        .totals-line .t-val { font-weight: 700; color: #1a1a1a; }
        .totals-grand { display: flex; justify-content: space-between; padding: 9px 12px; background: #DC1E2E; color: #fff; }
        .totals-grand .t-lbl { font-size: 11px; font-weight: 700; }
        .totals-grand .t-val { font-size: 14px; font-weight: 900; color: #FFF9C4; }

        /* Cierre */
        .cierre { margin-top: 14px; }
        .cierre p { font-size: 11px; color: #374151; font-style: italic; margin-bottom: 12px; }
        .firma-block { margin-top: 24px; }
        .firma-line { border-top: 1px solid #374151; width: 160px; margin-bottom: 4px; }
        .firma-nombre { font-weight: 700; font-size: 11px; color: #1a1a1a; }
        .firma-cargo { font-size: 10px; color: #64748B; }

        /* Footer */
        .page-footer { position: absolute; bottom: 10mm; left: 16mm; right: 16mm; background: #DC1E2E; color: #fff; text-align: center; padding: 5px 14px; border-radius: 4px; font-size: 11px; font-weight: 700; font-style: italic; }

        /* Barra acciones */
        .print-btn-bar { position: fixed; top: 12px; right: 12px; display: flex; gap: 8px; z-index: 100; align-items: center; }
        .btn-action { border: none; padding: 9px 18px; border-radius: 8px; font-size: 13px; font-weight: 700; cursor: pointer; text-decoration: none; display: inline-flex; align-items: center; gap: 6px; }
        .btn-print { background: #DC1E2E; color: #fff; box-shadow: 0 4px 12px rgba(220,30,46,.35); }
        .btn-back  { background: #374151; color: #fff; }
        .btn-action:hover { opacity: .88; }

        @media print {
            .print-btn-bar { display: none; }
            body { margin: 0; }
            .page { margin: 0; padding: 10mm 14mm 14mm; width: 100%; min-height: auto; }
            .prop-table tbody tr { page-break-inside: avoid; }
        }
        @page { size: A4; margin: 0; }
    </style>
</head>
<body>

<div class="print-btn-bar">
    <a href="{{ route('cotizaciones.show', $cotizacion) }}" class="btn-action btn-back">&#8592; Volver</a>
    <button class="btn-action btn-print" onclick="window.print()">&#128424; Imprimir</button>
</div>

<div class="page">

    {{-- CABECERA --}}
    <div class="header">
        <div class="header-left">
            <div class="header-logo">
                <img src="{{ asset('images/logo.png') }}" alt="Logo"
                     onerror="this.style.display='none';this.nextElementSibling.style.display='flex'">
                <div class="header-logo-placeholder" style="display:none">B</div>
            </div>
            <div>
                <div class="header-brand"><span>BÚHO</span> Publicidad</div>
                <div class="header-tagline">{{ $empresa_propia['slogan'] }}</div>
            </div>
        </div>
        <div class="header-date">
            {{ $ciudad }}, {{ now()->locale('es')->isoFormat('D [de] MMMM [del] YYYY') }}
        </div>
    </div>

    {{-- DESTINATARIO --}}
    @php
        $empresa  = $cotizacion->empresa;
        $dest_nombre = $empresa ? $empresa->nombre : ($cotizacion->cliente_empresa ?? ($cotizacion->cliente_nombre ?? 'Estimado cliente'));
        $dest_contacto = $cotizacion->cliente_nombre ?? ($empresa?->encargado ?? null);
    @endphp

    <div class="destinatario">
        <div class="dest-ciudad">Señores.</div>
        <div class="dest-nombre">{{ strtoupper($dest_nombre) }}</div>
        @if($dest_contacto && $dest_contacto !== $dest_nombre)
        <div class="dest-title">Attn: {{ $dest_contacto }}</div>
        @endif
        <div class="dest-saludo" style="margin-top:6px">De nuestra mayor consideración</div>
    </div>

    {{-- CUERPO DE LA CARTA --}}
    <div class="carta-body">
        <p>
            Por medio de la presente, me es grato dirigirme a usted y envío mi más cordial saludo, y a su vez
            presentarles nuestra propuesta comercial. Nuestra empresa, <strong>{{ $empresa_propia['nombre'] ?? 'BÚHO Publicidad' }}</strong>,
            ofrece servicios integrales de marketing digital y publicidad exterior.
        </p>
        <p>
            A continuación, presentamos nuestra <strong>PROPUESTA PUBLICITARIA</strong> diseñada especialmente
            para <strong>{{ strtoupper($dest_nombre) }}</strong>:
        </p>
    </div>

    {{-- TABLA DE PROPUESTA --}}
    <div class="section-label">&#128202; PRESENTAMOS NUESTRA PROPUESTA DE PRODUCCIÓN DE CONTENIDO</div>
    @php
        $subtotal = 0;
        $igv_pct  = $empresa_propia['igv_porcentaje'] / 100;

        // Agrupar elementos por tipo
        $grupos = [];
        foreach ($cotizacion->elementos as $elem) {
            if ($elem->tipo_elemento === 'digital') {
                $grupos['Paneles LED'][] = $elem;
            } elseif ($elem->tipo_elemento === 'tradicional') {
                $grupos['Publicidad Exterior'][] = $elem;
            } else {
                $grupos['Servicios'][] = $elem;
            }
        }
    @endphp

    <table class="prop-table">
        <thead>
            <tr>
                <th style="width:180px">ITEM</th>
                <th>DESCRIPCIÓN</th>
                <th class="right" style="width:100px">COSTO SIN IGV</th>
            </tr>
        </thead>
        <tbody>
            @forelse($cotizacion->elementos as $elem)
            @php
                $panel    = $elem->panel ?? null;
                $servicio = $elem->servicio ?? null;
                $nombre   = $panel
                    ? ($panel->nombre ?? $elem->codigo)
                    : ($servicio ? $servicio->nombre : $elem->codigo);
                $ubicacion = $panel ? ($panel->direccion ?? null) : null;
                $medidas   = $panel ? ($panel->medidas ?? null) : null;
                $periodo   = $elem->tiempo_contrato
                    ? $elem->tiempo_contrato . ' ' . ($elem->tiempo_contrato == 1 ? 'mes' : 'meses')
                    : null;
                $precio    = (float)$elem->precio_unitario;
                $costo     = (float)($elem->costo_produccion ?? 0);
                $linea     = $precio + $costo;
                $subtotal += $linea;
            @endphp
            <tr>
                <td>
                    <div class="item-name">{{ $elem->codigo }}</div>
                    @if($nombre && $nombre !== $elem->codigo)
                        <div style="font-size:9.5px;color:#64748B">{{ $nombre }}</div>
                    @endif
                </td>
                <td>
                    <ul class="desc-list">
                        @if($ubicacion)<li>Ubicación: {{ $ubicacion }}</li>@endif
                        @if($medidas)<li>Medidas: {{ $medidas }}</li>@endif
                        @if($periodo)<li>Tiempo: {{ $periodo }}</li>@endif
                        @if($costo > 0)<li>Producción: S/ {{ number_format($costo, 2, '.', ',') }}{{ $elem->desc_costo ? ' (' . $elem->desc_costo . ')' : '' }}</li>@endif
                        @if($elem->observaciones)<li>{{ $elem->observaciones }}</li>@endif
                        @if(!$ubicacion && !$medidas && !$periodo && !$costo && !$elem->observaciones)
                            <li>Servicio publicitario</li>
                        @endif
                    </ul>
                </td>
                <td class="right">{{ number_format($linea, 2, '.', ',') }}</td>
            </tr>
            @empty
            <tr><td colspan="3" style="text-align:center;padding:20px;color:#94A3B8">Sin elementos detallados</td></tr>
            @endforelse
        </tbody>
    </table>

    {{-- TOTALES --}}
    @php
        $igv   = $subtotal * $igv_pct;
        $total = $subtotal + $igv;
    @endphp
    <div class="totals-wrapper">
        <div class="totals-box">
            <div class="totals-line">
                <span class="t-lbl">PRECIO TOTAL</span>
                <span class="t-val">{{ number_format($subtotal, 2, '.', ',') }}</span>
            </div>
            <div class="totals-line">
                <span class="t-lbl">IGV {{ $empresa_propia['igv_porcentaje'] }}%</span>
                <span class="t-val">{{ number_format($igv, 2, '.', ',') }}</span>
            </div>
            <div class="totals-grand">
                <span class="t-lbl">PRECIO TOTAL INCLUIDO IGV</span>
                <span class="t-val">{{ number_format($total, 2, '.', ',') }}</span>
            </div>
        </div>
    </div>

    {{-- CIERRE --}}
    <div class="cierre">
        <p>Atentamente,</p>
        <div class="firma-block">
            <div class="firma-line"></div>
            <div class="firma-nombre">{{ $empresa_propia['nombre'] ?? 'BÚHO Publicidad' }}</div>
            <div class="firma-cargo">{{ $empresa_propia['slogan'] ?? '' }}</div>
            <div style="font-size:10px;color:#64748B;margin-top:3px">{{ $empresa_propia['email'] ?? '' }} | {{ $empresa_propia['celular'] ?? '' }}</div>
        </div>
    </div>

    {{-- FOOTER --}}
    <div class="page-footer">
        ¡{{ $empresa_propia['slogan'] }}! — {{ $empresa_propia['web'] }}
    </div>

</div>
</body>
</html>
