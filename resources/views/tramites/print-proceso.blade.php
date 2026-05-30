<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Proceso — {{ $tramite->numero }}</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: Arial, sans-serif; font-size: 11px; color: #1a1a1a; background: #fff; line-height: 1.4; }

        .page { width: 210mm; min-height: 297mm; margin: 0 auto; padding: 12mm 14mm; background: #fff; }

        /* Header */
        .header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 16px; border-bottom: 3px solid #7C3AED; padding-bottom: 12px; }
        .header-left { display: flex; align-items: center; gap: 14px; }
        .header-logo img { height: 56px; width: auto; }
        .header-logo-placeholder { height: 56px; width: 56px; background: #7C3AED; border-radius: 8px; display: flex; align-items: center; justify-content: center; color: #fff; font-weight: 900; font-size: 22px; }
        .header-company-name { font-size: 20px; font-weight: 900; color: #1a1a1a; }
        .header-company-name span { color: #7C3AED; }
        .header-slogan { font-size: 8.5px; color: #64748B; text-transform: uppercase; letter-spacing: 1.5px; margin: 3px 0 6px; }
        .header-contact { font-size: 9.5px; color: #374151; line-height: 1.8; }
        .header-right { text-align: right; }
        .doc-badge { font-size: 22px; font-weight: 900; color: #fff; background: #7C3AED; padding: 5px 16px; border-radius: 6px; letter-spacing: 1.5px; margin-bottom: 6px; display: inline-block; }
        .doc-info { font-size: 10px; color: #374151; line-height: 1.8; }
        .doc-info strong { color: #7C3AED; }

        /* Ficha */
        .ficha { display: flex; gap: 14px; margin-bottom: 14px; }
        .ficha-col { flex: 1; border: 1px solid #E2E8F0; border-radius: 8px; overflow: hidden; }
        .ficha-header { background: #7C3AED; color: #fff; padding: 5px 12px; font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.8px; }
        .ficha-body { padding: 10px 12px; }
        .ficha-row { display: flex; gap: 4px; margin-bottom: 5px; font-size: 10.5px; }
        .ficha-label { font-weight: 700; color: #374151; min-width: 90px; flex-shrink: 0; }
        .ficha-value { color: #1a1a1a; }
        .badge { display: inline-block; padding: 2px 8px; border-radius: 20px; font-size: 9px; font-weight: 700; text-transform: uppercase; }
        .badge-success { background: #D1FAE5; color: #065F46; }
        .badge-warning { background: #FEF3C7; color: #92400E; }
        .badge-danger  { background: #FEE2E2; color: #991B1B; }
        .badge-purple  { background: #EDE9FE; color: #5B21B6; }
        .badge-gray    { background: #F3F4F6; color: #6B7280; }

        /* Tabla de proceso */
        .section-title { font-size: 10px; font-weight: 800; text-transform: uppercase; letter-spacing: 1px; color: #fff; background: #7C3AED; padding: 5px 12px; border-radius: 4px 4px 0 0; margin-bottom: 0; }
        .proc-table { width: 100%; border-collapse: collapse; border: 1px solid #E2E8F0; border-top: none; margin-bottom: 14px; }
        .proc-table thead tr { background: #F8FAFC; }
        .proc-table th { padding: 7px 9px; font-size: 9.5px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.4px; color: #374151; border-bottom: 2px solid #E2E8F0; text-align: left; }
        .proc-table th.center { text-align: center; }
        .proc-table td { padding: 8px 9px; font-size: 10.5px; color: #1a1a1a; border-bottom: 1px solid #F1F5FB; vertical-align: middle; }
        .proc-table td.center { text-align: center; }
        .proc-table tbody tr:last-child td { border-bottom: none; }
        .proc-table tbody tr:nth-child(even) td { background: #FAFAFA; }
        .step-num { width: 24px; height: 24px; background: #7C3AED; color: #fff; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; font-size: 10px; font-weight: 900; }
        .area-name { font-weight: 700; color: #1a1a1a; }
        .notif-code { font-size: 10px; color: #5B21B6; font-weight: 600; }
        .obs-text { font-size: 10px; color: #374151; max-width: 200px; }
        .empty-row td { text-align: center; padding: 28px; color: #94A3B8; font-style: italic; }

        /* Footer */
        .page-footer { margin-top: 16px; background: #7C3AED; color: #fff; text-align: center; padding: 5px 14px; border-radius: 4px; font-size: 11px; font-weight: 700; font-style: italic; }

        /* Barra acción */
        .print-btn-bar { position: fixed; top: 12px; right: 12px; display: flex; gap: 8px; z-index: 100; }
        .btn-action { border: none; padding: 9px 18px; border-radius: 8px; font-size: 13px; font-weight: 700; cursor: pointer; text-decoration: none; display: inline-flex; align-items: center; gap: 6px; }
        .btn-print { background: #7C3AED; color: #fff; box-shadow: 0 4px 12px rgba(124,58,237,.35); }
        .btn-back  { background: #374151; color: #fff; }
        .btn-action:hover { opacity: .88; }

        @media print {
            .print-btn-bar { display: none; }
            body { margin: 0; }
            .page { margin: 0; padding: 8mm 10mm; width: 100%; min-height: auto; }
            .proc-table tbody tr { page-break-inside: avoid; }
        }
        @page { size: A4; margin: 0; }
    </style>
</head>
<body>

<div class="print-btn-bar">
    <a href="{{ route('tramites.show', $tramite) }}" class="btn-action btn-back">&#8592; Volver</a>
    <button class="btn-action btn-print" onclick="window.print()">&#128424; Imprimir</button>
</div>

<div class="page">

    {{-- CABECERA --}}
    <div class="header">
        <div class="header-left">
            <div class="header-logo">
                <img src="{{ asset('images/logo.png') }}"
                     alt="Logo" style="height:56px;width:auto"
                     onerror="this.style.display='none';this.nextElementSibling.style.display='flex'">
                <div class="header-logo-placeholder" style="display:none">B</div>
            </div>
            <div>
                <div class="header-company-name"><span>BÚHO</span> Publicidad</div>
                <div class="header-slogan">{{ $empresa_propia['slogan'] }}</div>
                <div class="header-contact">
                    &#128205; {{ $empresa_propia['direccion'] }}<br>
                    &#128222; {{ $empresa_propia['telefono'] }} &nbsp;|&nbsp; {{ $empresa_propia['celular'] }}
                </div>
            </div>
        </div>
        <div class="header-right">
            <div class="doc-badge">PROCESO</div>
            <div class="doc-info">
                N° <strong>{{ $tramite->numero }}</strong><br>
                Fecha: {{ now()->format('d/m/Y') }}<br>
                Estado: <strong>{{ $tramite->badge_label }}</strong>
            </div>
        </div>
    </div>

    {{-- FICHA DEL TRÁMITE --}}
    <div class="ficha">
        <div class="ficha-col">
            <div class="ficha-header">&#128196; Datos del trámite</div>
            <div class="ficha-body">
                <div class="ficha-row"><div class="ficha-label">Tipo:</div><div class="ficha-value"><strong>{{ $tramite->tipo ?? '—' }}</strong></div></div>
                <div class="ficha-row"><div class="ficha-label">Entidad:</div><div class="ficha-value">{{ $tramite->entidad_nombre ?? '—' }}</div></div>
                @if($tramite->entidad_expediente)
                <div class="ficha-row"><div class="ficha-label">Exp. Matriz:</div><div class="ficha-value">{{ $tramite->entidad_expediente }}</div></div>
                @endif
                @if($tramite->codigo_tramite)
                <div class="ficha-row"><div class="ficha-label">Código:</div><div class="ficha-value">{{ $tramite->codigo_tramite }}</div></div>
                @endif
                <div class="ficha-row"><div class="ficha-label">Área:</div><div class="ficha-value" style="color:#7C3AED;font-weight:600">{{ $tramite->area_actual ?? '—' }}</div></div>
            </div>
        </div>
        <div class="ficha-col">
            <div class="ficha-header">&#128197; Fechas y encargados</div>
            <div class="ficha-body">
                @if($tramite->fecha_ingreso)
                <div class="ficha-row"><div class="ficha-label">Ingreso:</div><div class="ficha-value">{{ $tramite->fecha_ingreso->format('d/m/Y') }}</div></div>
                @endif
                @if($tramite->fecha_modificacion)
                <div class="ficha-row"><div class="ficha-label">Modificación:</div><div class="ficha-value">{{ $tramite->fecha_modificacion->format('d/m/Y') }}</div></div>
                @endif
                @if($tramite->fecha_vencimiento)
                <div class="ficha-row"><div class="ficha-label">Vencimiento:</div><div class="ficha-value" style="{{ $tramite->fecha_vencimiento->isPast() ? 'color:#DC2626;font-weight:700' : '' }}">{{ $tramite->fecha_vencimiento->format('d/m/Y') }}</div></div>
                @endif
                @if($tramite->encargado)
                <div class="ficha-row"><div class="ficha-label">Encargado:</div><div class="ficha-value">{{ $tramite->encargado }}</div></div>
                @endif
                @if($tramite->encargado_area)
                <div class="ficha-row"><div class="ficha-label">Enc. área:</div><div class="ficha-value">{{ $tramite->encargado_area }}</div></div>
                @endif
                @if($tramite->doc_presentado)
                <div class="ficha-row"><div class="ficha-label">Documento:</div><div class="ficha-value">{{ $tramite->doc_presentado }}</div></div>
                @endif
                @if($tramite->apunte_adicional)
                <div class="ficha-row"><div class="ficha-label">Apunte:</div><div class="ficha-value">{{ $tramite->apunte_adicional }}</div></div>
                @endif
            </div>
        </div>
    </div>

    {{-- TABLA DE PROCESO --}}
    <div class="section-title">&#128260; Pasos del Proceso — {{ $tramite->procesos->count() }} paso(s)</div>
    <table class="proc-table">
        <thead>
            <tr>
                <th class="center" style="width:36px">#</th>
                <th>ÁREA</th>
                <th>N° NOTIFICACIÓN</th>
                <th>OBSERVACIÓN</th>
                <th class="center" style="width:90px">ESTADO</th>
            </tr>
        </thead>
        <tbody>
            @forelse($tramite->procesos as $paso)
            <tr>
                <td class="center"><span class="step-num">{{ $paso->orden }}</span></td>
                <td><span class="area-name">{{ $paso->area ?? '—' }}</span></td>
                <td><span class="notif-code">{{ $paso->numero_notificacion ?? '—' }}</span></td>
                <td><span class="obs-text">{{ $paso->observacion ?? '—' }}</span></td>
                <td class="center">
                    @php
                        $bc = match($paso->badge_color) {
                            'success' => 'badge-success',
                            'warning' => 'badge-warning',
                            'danger'  => 'badge-danger',
                            default   => 'badge-gray',
                        };
                    @endphp
                    <span class="badge {{ $bc }}">{{ $paso->badge_label }}</span>
                </td>
            </tr>
            @empty
            <tr class="empty-row">
                <td colspan="5">Sin pasos de proceso registrados</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    {{-- FOOTER --}}
    <div class="page-footer">
        ¡{{ $empresa_propia['slogan'] }}! &mdash; {{ $empresa_propia['web'] }}
    </div>

</div>
</body>
</html>
