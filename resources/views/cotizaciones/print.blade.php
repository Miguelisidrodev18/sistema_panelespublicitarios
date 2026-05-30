<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cotización {{ $cotizacion->numero }}</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Arial', sans-serif;
            font-size: 11px;
            color: #1a1a1a;
            background: #fff;
            line-height: 1.4;
        }

        .page {
            width: 210mm;
            min-height: 297mm;
            margin: 0 auto;
            padding: 12mm 14mm 12mm;
            background: #fff;
        }

        /* ── Cabecera ── */
        .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 14px;
            border-bottom: 3px solid #DC1E2E;
            padding-bottom: 12px;
        }
        .header-left { display: flex; align-items: flex-start; gap: 14px; }
        .header-logo img { height: 64px; width: auto; }
        .header-logo-placeholder {
            height: 64px; width: 90px;
            background: #DC1E2E; border-radius: 8px;
            display: flex; align-items: center; justify-content: center;
            color: #fff; font-weight: 900; font-size: 20px;
        }
        .header-company-name { font-size: 22px; font-weight: 900; color: #1a1a1a; line-height: 1; }
        .header-company-name span { color: #DC1E2E; }
        .header-slogan { font-size: 9px; color: #64748B; text-transform: uppercase; letter-spacing: 1.5px; margin: 3px 0 8px; }
        .header-contact { font-size: 10px; color: #374151; line-height: 1.8; }

        .header-right { text-align: right; }
        .header-title {
            font-size: 26px; font-weight: 900; color: #fff;
            background: #1A1D29; padding: 6px 18px;
            border-radius: 6px; letter-spacing: 2px;
            margin-bottom: 8px; display: inline-block;
        }
        .cot-info-table { width: 100%; border-collapse: collapse; font-size: 10.5px; }
        .cot-info-table td { padding: 4px 8px; border: 1px solid #E2E8F0; }
        .cot-info-table .lbl { font-weight: 700; background: #F8FAFC; color: #374151; white-space: nowrap; width: 90px; }
        .cot-info-table .val { color: #DC1E2E; font-weight: 700; }
        .cot-info-table .val-dark { color: #1a1a1a; font-weight: 600; }

        /* ── Dos columnas: cliente + condiciones ── */
        .section-two-col { display: flex; gap: 14px; margin-bottom: 14px; }
        .section-two-col .col-client { flex: 1; }
        .section-two-col .col-conditions { flex: 1; }

        .section-title {
            font-size: 10px; font-weight: 800; text-transform: uppercase;
            letter-spacing: 1px; color: #fff; background: #1A1D29;
            padding: 5px 10px; border-radius: 4px 4px 0 0;
        }

        .client-box {
            border: 1px solid #E2E8F0; border-top: none;
            border-radius: 0 0 6px 6px; padding: 10px 12px;
            display: flex; gap: 10px;
        }
        .client-data { flex: 1; }
        .client-row { display: flex; margin-bottom: 5px; font-size: 10.5px; }
        .client-label { font-weight: 700; color: #374151; min-width: 70px; }
        .client-value { color: #1a1a1a; }
        .client-avatar {
            width: 44px; height: 44px; background: #F1F5FB;
            border-radius: 50%; border: 2px solid #E2E8F0;
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0; font-size: 20px; color: #94A3B8;
        }

        .conditions-box { border: 1px solid #E2E8F0; border-top: none; border-radius: 0 0 6px 6px; padding: 10px 12px; }
        .condition-item { display: flex; align-items: flex-start; gap: 6px; margin-bottom: 5px; font-size: 10px; color: #374151; }
        .condition-dot {
            width: 14px; height: 14px; background: #FCD34D; border-radius: 50%;
            flex-shrink: 0; margin-top: 1px; display: flex; align-items: center;
            justify-content: center; font-size: 8px; color: #92400E; font-weight: 700;
        }

        /* ── Tabla de detalle ── */
        .detail-section-title {
            font-size: 10px; font-weight: 800; text-transform: uppercase;
            letter-spacing: 1px; color: #fff; background: #DC1E2E;
            padding: 5px 12px; border-radius: 4px 4px 0 0;
        }

        .detail-table { width: 100%; border-collapse: collapse; border: 1px solid #E2E8F0; border-top: none; margin-bottom: 12px; }
        .detail-table thead tr { background: #F8FAFC; }
        .detail-table th {
            padding: 6px 7px; font-size: 9px; font-weight: 700;
            text-transform: uppercase; letter-spacing: 0.4px;
            color: #374151; border-bottom: 2px solid #E2E8F0; text-align: center;
        }
        .detail-table th.left { text-align: left; }
        .detail-table td { padding: 6px 7px; font-size: 10px; color: #1a1a1a; border-bottom: 1px solid #F1F5FB; vertical-align: middle; text-align: center; }
        .detail-table td.left { text-align: left; }
        .detail-table tbody tr:last-child td { border-bottom: none; }
        .detail-table tbody tr:nth-child(even) td { background: #FAFAFA; }

        .panel-photo { width: 58px; height: 42px; object-fit: cover; border-radius: 4px; border: 1px solid #E2E8F0; }
        .panel-no-photo {
            width: 58px; height: 42px; background: #F1F5FB; border-radius: 4px;
            border: 1px solid #E2E8F0; display: inline-flex; align-items: center;
            justify-content: center; font-size: 16px; color: #94A3B8;
        }

        .badge { display: inline-block; padding: 2px 7px; border-radius: 20px; font-size: 8.5px; font-weight: 700; text-transform: uppercase; }
        .badge-led     { background: #DBEAFE; color: #1D4ED8; }
        .badge-banner  { background: #FEF3C7; color: #D97706; }
        .badge-servicio{ background: #EDE9FE; color: #6D28D9; }

        .fw-700 { font-weight: 700; }
        .text-green { color: #059669; }
        .text-right { text-align: right !important; }

        /* ── Totales ── */
        .totals-row { display: flex; justify-content: flex-end; margin-bottom: 12px; }
        .totals-box { width: 210px; border: 1px solid #E2E8F0; border-radius: 6px; overflow: hidden; }
        .totals-line { display: flex; justify-content: space-between; padding: 5px 12px; font-size: 10.5px; border-bottom: 1px solid #F1F5FB; }
        .totals-line:last-child { border-bottom: none; }
        .totals-line .t-label { color: #374151; font-weight: 600; }
        .totals-line .t-value { font-weight: 700; color: #1a1a1a; }
        .totals-total { display: flex; justify-content: space-between; padding: 8px 12px; background: #1A1D29; color: #fff; }
        .totals-total .t-label { font-size: 11px; font-weight: 700; }
        .totals-total .t-value { font-size: 14px; font-weight: 900; color: #FCD34D; }

        /* ── Obs + pagos ── */
        .bottom-section { display: flex; gap: 14px; margin-bottom: 14px; }
        .obs-box { flex: 1; border: 1px solid #E2E8F0; border-radius: 6px; overflow: hidden; }
        .obs-header { background: #FEF3C7; padding: 5px 10px; font-size: 10px; font-weight: 700; color: #92400E; border-bottom: 1px solid #FDE68A; }
        .obs-body { padding: 10px 12px; font-size: 10px; color: #374151; line-height: 1.7; }
        .obs-body ul { list-style: disc; padding-left: 14px; }
        .obs-body li { margin-bottom: 3px; }

        .pay-box { width: 180px; border: 1px solid #E2E8F0; border-radius: 6px; overflow: hidden; }
        .pay-header { background: #FEF3C7; padding: 5px 10px; font-size: 10px; font-weight: 700; color: #92400E; border-bottom: 1px solid #FDE68A; }
        .pay-body { padding: 10px 12px; }
        .pay-method { display: flex; align-items: center; gap: 8px; margin-bottom: 8px; font-size: 10px; }
        .pay-method:last-child { margin-bottom: 0; }
        .pay-icon { width: 24px; height: 24px; border-radius: 50%; background: #F1F5FB; border: 1px solid #E2E8F0; display: flex; align-items: center; justify-content: center; font-size: 11px; flex-shrink: 0; }
        .pay-detail strong { display: block; font-weight: 700; color: #1a1a1a; }
        .pay-detail span { color: #64748B; font-size: 9.5px; }

        /* ── Footer ── */
        .page-footer { margin-top: 14px; background: #DC1E2E; color: #fff; text-align: center; padding: 5px 14px; border-radius: 4px; font-size: 11px; font-weight: 700; font-style: italic; }

        /* ── Barra de acción (solo pantalla) ── */
        .print-btn-bar {
            position: fixed; top: 12px; right: 12px;
            display: flex; gap: 8px; z-index: 100; align-items: center;
        }
        .btn-action {
            border: none; padding: 9px 18px; border-radius: 8px;
            font-size: 13px; font-weight: 700; cursor: pointer;
            text-decoration: none; display: inline-flex; align-items: center; gap: 6px;
        }
        .btn-print  { background: #DC1E2E; color: #fff; box-shadow: 0 4px 12px rgba(220,30,46,.35); }
        .btn-back   { background: #374151; color: #fff; }
        .btn-toggle { background: #F59E0B; color: #fff; }
        .btn-action:hover { opacity: .88; }

        /* ── Galería de fotos (anexo) ── */
        .photo-annex-page {
            width: 210mm;
            min-height: 297mm;
            margin: 0 auto;
            padding: 12mm 14mm;
            background: #fff;
            page-break-before: always;
        }
        .photo-annex-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 3px solid #DC1E2E;
            padding-bottom: 10px;
            margin-bottom: 14px;
        }
        .photo-annex-title {
            font-size: 13px; font-weight: 900; text-transform: uppercase;
            letter-spacing: 1.2px; color: #1A1D29;
        }
        .photo-annex-title span { color: #DC1E2E; }
        .photo-annex-badge {
            background: #DC1E2E; color: #fff;
            font-size: 9px; font-weight: 700; text-transform: uppercase;
            letter-spacing: 1px; padding: 4px 12px; border-radius: 20px;
        }
        .photo-annex-subtitle {
            font-size: 9.5px; color: #64748B; margin-bottom: 18px; font-style: italic;
        }
        .photo-grid-3 {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 14px;
        }
        .photo-card {
            border: 1px solid #E2E8F0; border-radius: 8px; overflow: hidden;
            box-shadow: 0 2px 8px rgba(0,0,0,.06);
        }
        .photo-card img {
            width: 100%; height: 145px; object-fit: cover; display: block;
        }
        .photo-card-no-img {
            width: 100%; height: 145px; background: #F1F5FB;
            display: flex; align-items: center; justify-content: center;
            font-size: 36px; color: #CBD5E1;
        }
        .photo-card-info {
            padding: 8px 10px; background: #F8FAFC;
            border-top: 2px solid #DC1E2E;
        }
        .photo-card-code {
            font-size: 11.5px; font-weight: 900; color: #1A1D29; margin-bottom: 2px;
        }
        .photo-card-name {
            font-size: 9px; color: #374151; margin-bottom: 4px; line-height: 1.3;
        }
        .photo-card-location {
            font-size: 8.5px; color: #64748B; margin-bottom: 2px;
        }
        .photo-card-size {
            font-size: 8.5px; color: #94A3B8;
        }
        .photo-annex-footer {
            margin-top: 20px; background: #DC1E2E; color: #fff;
            text-align: center; padding: 5px 14px; border-radius: 4px;
            font-size: 11px; font-weight: 700; font-style: italic;
        }
        .photo-page-num {
            text-align: right; font-size: 8.5px; color: #94A3B8; margin-top: 10px;
        }

        @media print {
            .print-btn-bar { display: none; }
            body { margin: 0; }
            .page { margin: 0; padding: 8mm 10mm; width: 100%; min-height: auto; }
            .detail-table tbody tr { page-break-inside: avoid; }
            .photo-annex-page { margin: 0; padding: 8mm 10mm; width: 100%; min-height: auto; }
            .photo-card { page-break-inside: avoid; }
        }
        @page { size: A4; margin: 0; }
    </style>
</head>
<body>

{{-- ── Barra de acciones (solo pantalla) ── --}}
<div class="print-btn-bar">
    <a href="{{ route('cotizaciones.show', $cotizacion) }}" class="btn-action btn-back">&#8592; Volver</a>
    <a href="{{ route('cotizaciones.imprimir', $cotizacion) }}?foto={{ $con_foto ? '0' : '1' }}"
       class="btn-action btn-toggle" title="Alternar fotos">
        {{ $con_foto ? '&#128247; Sin foto' : '&#128247; Con foto' }}
    </a>
    <button class="btn-action btn-print" onclick="window.print()">&#128424; Imprimir</button>
</div>

<div class="page">

    {{-- ══════════════════ CABECERA ══════════════════ --}}
    <div class="header">
        <div class="header-left">
            <div class="header-logo">
                <img src="{{ asset('images/logo.png') }}" alt="Logo"
                     onerror="this.style.display='none';this.nextElementSibling.style.display='flex'">
                <div class="header-logo-placeholder" style="display:none">B</div>
            </div>
            <div>
                <div class="header-company-name"><span>BÚHO</span> Publicidad</div>
                <div class="header-slogan">{{ $empresa_propia['slogan'] }}</div>
                <div class="header-contact">
                    &#128205; {{ $empresa_propia['direccion'] }}<br>
                    &#128222; {{ $empresa_propia['telefono'] }} &nbsp;|&nbsp; &#128241; {{ $empresa_propia['celular'] }}<br>
                    &#9993; {{ $empresa_propia['email'] }} &nbsp;&nbsp; &#127760; {{ $empresa_propia['web'] }}
                </div>
            </div>
        </div>

        <div class="header-right">
            <div class="header-title">COTIZACIÓN</div>
            <table class="cot-info-table">
                <tr><td class="lbl">N° COTIZACIÓN</td><td class="val">{{ $cotizacion->numero }}</td></tr>
                <tr><td class="lbl">FECHA</td><td class="val-dark">{{ $cotizacion->fecha_cotizacion?->format('d/m/Y') ?? now()->format('d/m/Y') }}</td></tr>
                <tr>
                    <td class="lbl">VÁLIDO HASTA</td>
                    <td class="val-dark">
                        {{ $cotizacion->fecha_vencimiento?->format('d/m/Y') ?? '—' }}
                        @if($cotizacion->fecha_vencimiento)
                            @php $dias = now()->diffInDays($cotizacion->fecha_vencimiento, false); @endphp
                            <span style="color:#94A3B8;font-size:9px">({{ max(0,$dias) }} días)</span>
                        @endif
                    </td>
                </tr>
                <tr><td class="lbl">RUC</td><td class="val-dark">{{ $empresa_propia['ruc'] }}</td></tr>
            </table>
        </div>
    </div>

    {{-- ══════════════════ CLIENTE + CONDICIONES ══════════════════ --}}
    <div class="section-two-col">
        <div class="col-client">
            <div class="section-title">&#128100; Datos del Cliente</div>
            <div class="client-box">
                <div class="client-data">
                    @php
                        $empresa  = $cotizacion->empresa;
                        $nombre   = $empresa ? $empresa->nombre : ($cotizacion->cliente_empresa ?? '—');
                        $ruc      = $empresa?->ruc ?? null;
                        $contacto = $cotizacion->cliente_nombre ?? ($empresa?->encargado ?? null);
                        $telefono = $cotizacion->cliente_telefono ?? ($empresa?->celular ?? null);
                        $email    = $cotizacion->cliente_email ?? ($empresa?->correo ?? null);
                    @endphp
                    <div class="client-row"><div class="client-label">Cliente:</div><div class="client-value"><strong>{{ $nombre }}</strong></div></div>
                    @if($ruc)<div class="client-row"><div class="client-label">RUC:</div><div class="client-value">{{ $ruc }}</div></div>@endif
                    @if($contacto)<div class="client-row"><div class="client-label">Contacto:</div><div class="client-value">{{ $contacto }}</div></div>@endif
                    @if($telefono)<div class="client-row"><div class="client-label">Teléfono:</div><div class="client-value">{{ $telefono }}</div></div>@endif
                    @if($email)<div class="client-row"><div class="client-label">Correo:</div><div class="client-value">{{ $email }}</div></div>@endif
                    @if($cotizacion->tipo_contrato)<div class="client-row"><div class="client-label">Servicio:</div><div class="client-value"><span class="badge badge-led">{{ $cotizacion->tipo_contrato }}</span></div></div>@endif
                </div>
                <div class="client-avatar">&#128100;</div>
            </div>
        </div>

        <div class="col-conditions">
            <div class="section-title">&#9989; Condiciones Generales</div>
            <div class="conditions-box">
                <div class="condition-item"><div class="condition-dot">&#10003;</div><span>Sujeto a disponibilidad del espacio publicitario.</span></div>
                <div class="condition-item"><div class="condition-dot">&#10003;</div><span>La reserva se formaliza con el 50% de adelanto.</span></div>
                <div class="condition-item"><div class="condition-dot">&#10003;</div><span>El arte final debe enviarse con 3 días de anticipación.</span></div>
                <div class="condition-item"><div class="condition-dot">&#10003;</div><span>Los precios incluyen instalación y mantenimiento básico.</span></div>
                <div class="condition-item"><div class="condition-dot">&#10003;</div><span>
                    @if($cotizacion->fecha_vencimiento)
                        Cotización válida hasta el {{ $cotizacion->fecha_vencimiento->format('d/m/Y') }}.
                    @else
                        Cotización válida por 15 días desde la fecha de emisión.
                    @endif
                </span></div>
            </div>
        </div>
    </div>

    {{-- ══════════════════ TABLA DE DETALLE ══════════════════ --}}
    <div class="detail-section-title">&#128202; Detalle de Publicidad</div>

    @php
        $subtotal = 0;
        $igv_pct  = $empresa_propia['igv_porcentaje'] / 100;
    @endphp

    <table class="detail-table">
        <thead>
            <tr>
                <th style="width:22px">#</th>
                <th class="left" style="min-width:80px">Panel / Servicio</th>
                <th class="left" style="min-width:80px">Ubicación</th>
                @if($con_foto)<th style="width:66px">Foto</th>@endif
                <th style="width:58px">Medidas</th>
                <th style="width:44px">Tipo</th>
                <th style="width:52px">Período</th>
                <th style="width:26px">Cant.</th>
                <th style="width:66px">Precio</th>
                <th style="width:72px">Costo Prod.</th>
                <th style="width:70px">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @forelse($cotizacion->elementos as $i => $elem)
            @php
                $panel      = $elem->panel ?? null;
                $servicio   = $elem->servicio ?? null;
                $nombre_item = $panel
                    ? ($panel->nombre ?? $elem->codigo)
                    : ($servicio ? $servicio->nombre : $elem->codigo);
                $ubicacion  = $panel ? ($panel->direccion ?? '—') : '—';
                $medidas    = $panel ? ($panel->medidas ?? '—') : '—';
                $foto_url   = null;
                if ($con_foto && $panel && $panel->foto) {
                    $foto_url = Storage::url($panel->foto);
                }

                // Badge: para servicio usar subtipo si existe
                if ($elem->tipo_elemento === 'servicio') {
                    $subtipo_val = $elem->subtipo ?? 'general';
                    $badge_class = match($subtipo_val) {
                        'led'    => 'badge-led',
                        'banner' => 'badge-banner',
                        default  => 'badge-servicio',
                    };
                    $badge_label = match($subtipo_val) {
                        'led'    => 'LED',
                        'banner' => 'BANNER',
                        default  => 'SERVICIO',
                    };
                } else {
                    $badge_class = match($elem->tipo_elemento) {
                        'digital'    => 'badge-led',
                        'tradicional'=> 'badge-banner',
                        default      => 'badge-servicio',
                    };
                    $badge_label = match($elem->tipo_elemento) {
                        'digital'    => 'LED',
                        'tradicional'=> 'BANNER',
                        default      => strtoupper($elem->tipo_elemento),
                    };
                }

                $periodo   = $elem->tiempo_contrato ? $elem->tiempo_contrato . ' mes' . ($elem->tiempo_contrato > 1 ? 'es' : '') : '—';
                $precio    = (float)$elem->precio_unitario;
                $costo     = (float)($elem->costo_produccion ?? 0);
                $linea     = $precio + $costo;
                $subtotal += $linea;
            @endphp
            <tr>
                <td class="fw-700">{{ $i + 1 }}</td>
                <td class="left">
                    <strong>{{ $elem->codigo }}</strong>
                    @if($nombre_item && $nombre_item !== $elem->codigo)
                        <br><span style="color:#64748B;font-size:9px">{{ $nombre_item }}</span>
                    @endif
                </td>
                <td class="left" style="font-size:9.5px;color:#374151">{{ $ubicacion }}</td>
                @if($con_foto)
                <td>
                    @if($foto_url)
                        <img src="{{ $foto_url }}" class="panel-photo" alt="Foto">
                    @else
                        <div class="panel-no-photo">&#128247;</div>
                    @endif
                </td>
                @endif
                <td style="font-size:9.5px">{{ $medidas }}</td>
                <td><span class="badge {{ $badge_class }}">{{ $badge_label }}</span></td>
                <td style="font-size:9.5px">{{ $periodo }}</td>
                <td>1</td>
                <td class="text-right fw-700">S/ {{ number_format($precio, 2, '.', ',') }}</td>
                <td class="text-right" style="font-size:9.5px">
                    @if($costo > 0)
                        <strong>S/ {{ number_format($costo, 2, '.', ',') }}</strong><br>
                        <span style="color:#64748B;font-size:8.5px">{{ $elem->desc_costo ?? '' }}</span>
                    @else
                        —
                    @endif
                </td>
                <td class="text-right fw-700 text-green">S/ {{ number_format($linea, 2, '.', ',') }}</td>
            </tr>
            @if($elem->observaciones)
            <tr>
                <td></td>
                <td colspan="{{ $con_foto ? 9 : 8 }}" class="left" style="color:#64748B;font-size:9px;padding-top:2px;padding-bottom:5px;font-style:italic">
                    Obs: {{ $elem->observaciones }}
                </td>
            </tr>
            @endif
            @empty
            <tr><td colspan="{{ $con_foto ? 11 : 10 }}" style="text-align:center;padding:20px;color:#94A3B8">Sin elementos detallados</td></tr>
            @endforelse
        </tbody>
    </table>

    {{-- ══════════════════ TOTALES ══════════════════ --}}
    @php
        $igv   = $subtotal * $igv_pct;
        $total = $subtotal + $igv;
    @endphp
    <div class="totals-row">
        <div class="totals-box">
            <div class="totals-line">
                <span class="t-label">SUBTOTAL</span>
                <span class="t-value">S/ {{ number_format($subtotal, 2, '.', ',') }}</span>
            </div>
            <div class="totals-line">
                <span class="t-label">IGV ({{ $empresa_propia['igv_porcentaje'] }}%)</span>
                <span class="t-value">S/ {{ number_format($igv, 2, '.', ',') }}</span>
            </div>
            <div class="totals-total">
                <span class="t-label">TOTAL</span>
                <span class="t-value">S/ {{ number_format($total, 2, '.', ',') }}</span>
            </div>
        </div>
    </div>

    {{-- ══════════════════ OBSERVACIONES + PAGOS ══════════════════ --}}
    <div class="bottom-section">
        <div class="obs-box">
            <div class="obs-header">&#128221; Observaciones</div>
            <div class="obs-body">
                <ul>
                    <li>Los precios están expresados en Soles e incluyen IGV.</li>
                    <li>No incluye diseño gráfico salvo se especifique.</li>
                    @if($cotizacion->notas)
                        <li>{{ $cotizacion->notas }}</li>
                    @endif
                    <li>En caso de cancelación del servicio, no habrá devolución del adelanto.</li>
                </ul>
            </div>
        </div>

        <div class="pay-box">
            <div class="pay-header">&#128179; Métodos de Pago</div>
            <div class="pay-body">
                <div class="pay-method">
                    <div class="pay-icon">&#127968;</div>
                    <div class="pay-detail">
                        <strong>{{ $empresa_propia['banco_nombre'] }}</strong>
                        <span>{{ $empresa_propia['banco_cuenta'] }}</span>
                    </div>
                </div>
                <div class="pay-method">
                    <div class="pay-icon">&#128241;</div>
                    <div class="pay-detail">
                        <strong>Yape / Plin</strong>
                        <span>{{ $empresa_propia['yape_numero'] }}</span>
                    </div>
                </div>
                <div class="pay-method">
                    <div class="pay-icon">&#128176;</div>
                    <div class="pay-detail">
                        <strong>Depósito bancario</strong>
                        <span>{{ $empresa_propia['deposito_nombre'] }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ══════════════════ FOOTER ══════════════════ --}}
    <div class="page-footer">
        ¡{{ $empresa_propia['slogan'] }}! — {{ $empresa_propia['web'] }}
    </div>

</div>

{{-- ══════════════════ ANEXO: GALERÍA DE FOTOS DE PANELES ══════════════════ --}}
@php
    $elementos_con_foto = collect();
    foreach ($cotizacion->elementos as $elem) {
        if ($con_foto && isset($elem->panel) && $elem->panel && $elem->panel->foto) {
            $elementos_con_foto->push($elem);
        }
    }
    $grupos_foto = $elementos_con_foto->chunk(3);
    $total_paginas_foto = $grupos_foto->count();
@endphp

@if($con_foto && $elementos_con_foto->count() > 0)
    @foreach($grupos_foto as $gi => $grupo)
    <div class="photo-annex-page">

        {{-- Mini-cabecera en cada hoja de galería --}}
        <div class="photo-annex-header">
            <div class="photo-annex-title">
                &#128247; Anexo: <span>Galería de Paneles</span>
            </div>
            <div style="text-align:right">
                <div class="photo-annex-badge">COT. {{ $cotizacion->numero }}</div>
                <div style="font-size:8.5px;color:#94A3B8;margin-top:4px">
                    Hoja {{ $gi + 1 }} de {{ $total_paginas_foto }}
                </div>
            </div>
        </div>

        @if($gi === 0)
        <p class="photo-annex-subtitle">
            Imágenes de referencia de los paneles incluidos en esta cotización.
            Los paneles se muestran con sus datos de identificación y ubicación.
        </p>
        @endif

        {{-- Grilla de hasta 3 fotos --}}
        <div class="photo-grid-3">
            @foreach($grupo as $elem)
            @php
                $panel       = $elem->panel;
                $foto_url    = Storage::url($panel->foto);
                $nombre_item = $panel->nombre ?? $elem->codigo;
                $ubicacion   = $panel->direccion ?? '—';
                $medidas     = $panel->medidas ?? '—';
            @endphp
            <div class="photo-card">
                <img src="{{ $foto_url }}" alt="{{ $nombre_item }}">
                <div class="photo-card-info">
                    <div class="photo-card-code">{{ $elem->codigo }}</div>
                    @if($nombre_item && $nombre_item !== $elem->codigo)
                        <div class="photo-card-name">{{ $nombre_item }}</div>
                    @endif
                    <div class="photo-card-location">&#128205; {{ $ubicacion }}</div>
                    @if($medidas && $medidas !== '—')
                        <div class="photo-card-size">&#128207; {{ $medidas }}</div>
                    @endif
                </div>
            </div>
            @endforeach

            {{-- Relleno si hay menos de 3 en la última fila --}}
            @for($pad = $grupo->count(); $pad < 3; $pad++)
                <div></div>
            @endfor
        </div>

        {{-- Footer de la hoja --}}
        <div class="photo-annex-footer">
            ¡{{ $empresa_propia['slogan'] }}! — {{ $empresa_propia['web'] }}
        </div>

    </div>
    @endforeach
@endif

</body>
</html>
