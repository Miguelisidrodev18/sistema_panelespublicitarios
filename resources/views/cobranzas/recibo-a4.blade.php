<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Recibo #{{ str_pad($cobranza->id, 6, '0', STR_PAD_LEFT) }}</title>
<style>
* { box-sizing: border-box; margin: 0; padding: 0; }
body { font-family: Arial, sans-serif; font-size: 11px; color: #1a1a1a; background: #fff; line-height: 1.5; }

.page { width: 210mm; min-height: 297mm; margin: 0 auto; padding: 14mm 16mm; background: #fff; position: relative; }

/* Cabecera */
.header { display: flex; justify-content: space-between; align-items: flex-start; padding-bottom: 14px; border-bottom: 3px solid #DC1E2E; margin-bottom: 18px; }
.header-left { display: flex; align-items: center; gap: 12px; }
.logo-img { height: 60px; width: auto; }
.logo-placeholder { height: 60px; width: 60px; background: #DC1E2E; border-radius: 8px; display: flex; align-items: center; justify-content: center; color: #fff; font-weight: 900; font-size: 22px; }
.brand { font-size: 20px; font-weight: 900; color: #1a1a1a; }
.brand span { color: #DC1E2E; }
.tagline { font-size: 8px; color: #64748B; text-transform: uppercase; letter-spacing: 1.5px; margin-top: 3px; }
.company-info { font-size: 9.5px; color: #374151; margin-top: 5px; line-height: 1.7; }

.doc-badge { text-align: right; }
.doc-title { font-size: 28px; font-weight: 900; color: #fff; background: #1A1D29; padding: 6px 18px; border-radius: 6px; letter-spacing: 2px; display: inline-block; margin-bottom: 8px; }
.doc-table { border-collapse: collapse; font-size: 10.5px; min-width: 200px; }
.doc-table td { padding: 4px 8px; border: 1px solid #E2E8F0; }
.doc-table .lbl { font-weight: 700; background: #F8FAFC; color: #374151; white-space: nowrap; }
.doc-table .val { color: #DC1E2E; font-weight: 700; }
.doc-table .val-dark { color: #1a1a1a; font-weight: 600; }

/* Estado badge */
.estado-badge { display: inline-block; padding: 3px 12px; border-radius: 20px; font-size: 9px; font-weight: 700; text-transform: uppercase; }
.estado-pagada  { background: #D1FAE5; color: #065F46; }
.estado-pendiente { background: #FEF3C7; color: #92400E; }

/* Secciones */
.section-title { font-size: 9.5px; font-weight: 800; text-transform: uppercase; letter-spacing: 1px; color: #fff; background: #1A1D29; padding: 5px 12px; border-radius: 4px 4px 0 0; }
.info-box { border: 1px solid #E2E8F0; border-top: none; border-radius: 0 0 8px 8px; padding: 12px 14px; margin-bottom: 14px; }
.info-row { display: flex; gap: 4px; margin-bottom: 5px; font-size: 10.5px; }
.info-row:last-child { margin-bottom: 0; }
.info-label { font-weight: 700; color: #374151; min-width: 110px; flex-shrink: 0; }
.info-value { color: #1a1a1a; }

/* Tabla monto */
.monto-section { display: flex; justify-content: flex-end; margin-bottom: 20px; }
.monto-box { width: 280px; border: 1px solid #E2E8F0; border-radius: 8px; overflow: hidden; }
.monto-line { display: flex; justify-content: space-between; padding: 7px 14px; font-size: 11px; border-bottom: 1px solid #F1F5F9; }
.monto-line .ml { color: #374151; font-weight: 600; }
.monto-line .mv { font-weight: 700; color: #1a1a1a; }
.monto-total { display: flex; justify-content: space-between; padding: 10px 14px; background: #DC1E2E; color: #fff; }
.monto-total .ml { font-size: 12px; font-weight: 700; }
.monto-total .mv { font-size: 17px; font-weight: 900; color: #FFF9C4; }

/* Firma */
.firma-area { display: flex; justify-content: space-between; margin-top: 32px; padding-top: 16px; border-top: 1px dashed #E2E8F0; }
.firma-col { text-align: center; width: 180px; }
.firma-line { border-top: 1px solid #1a1a1a; margin: 0 auto 6px; width: 140px; }
.firma-label { font-size: 10px; color: #374151; font-weight: 600; }
.firma-sub { font-size: 9px; color: #94A3B8; }

/* Nota al pie */
.footer { position: absolute; bottom: 10mm; left: 16mm; right: 16mm; text-align: center; background: #DC1E2E; color: #fff; padding: 5px 14px; border-radius: 4px; font-size: 11px; font-weight: 700; font-style: italic; }

/* Botones */
.print-btn-bar { position: fixed; top: 12px; right: 12px; display: flex; gap: 8px; z-index: 100; }
.btn-action { border: none; padding: 9px 18px; border-radius: 8px; font-size: 13px; font-weight: 700; cursor: pointer; text-decoration: none; display: inline-flex; align-items: center; gap: 6px; }
.btn-print { background: #DC1E2E; color: #fff; box-shadow: 0 4px 12px rgba(220,30,46,.35); }
.btn-back  { background: #374151; color: #fff; }
.btn-action:hover { opacity: .88; }

/* Watermark si está pagada */
.watermark { position: absolute; top: 50%; left: 50%; transform: translate(-50%,-50%) rotate(-30deg); font-size: 90px; font-weight: 900; color: rgba(5,150,105,.06); text-transform: uppercase; pointer-events: none; white-space: nowrap; z-index: 0; }

@media print {
    .print-btn-bar { display: none; }
    body { margin: 0; }
    .page { margin: 0; padding: 10mm 14mm; width: 100%; min-height: auto; }
    .footer { position: fixed; bottom: 0; }
}
@page { size: A4; margin: 0; }
</style>
</head>
<body>

<div class="print-btn-bar">
    <a href="{{ route('cobranzas.index') }}" class="btn-action btn-back">&#8592; Volver</a>
    <button class="btn-action btn-print" onclick="window.print()">&#128424; Imprimir</button>
</div>

<div class="page">

    @if($cobranza->estado === 'pagada')
    <div class="watermark">PAGADO</div>
    @endif

    {{-- CABECERA --}}
    <div class="header">
        <div class="header-left">
            <div>
                <img src="{{ asset('images/logo.png') }}" alt="Logo" class="logo-img"
                     onerror="this.style.display='none';this.nextElementSibling.style.display='flex'">
                <div class="logo-placeholder" style="display:none">B</div>
            </div>
            <div>
                <div class="brand"><span>{{ explode(' ', $empresa_propia['nombre'])[0] }}</span> {{ implode(' ', array_slice(explode(' ', $empresa_propia['nombre']), 1)) }}</div>
                <div class="tagline">{{ $empresa_propia['slogan'] }}</div>
                <div class="company-info">
                    &#128205; {{ $empresa_propia['direccion'] }}<br>
                    &#128222; {{ $empresa_propia['telefono'] }} &nbsp;|&nbsp; {{ $empresa_propia['celular'] }}<br>
                    &#9993; {{ $empresa_propia['email'] }} &nbsp;|&nbsp; {{ $empresa_propia['web'] }}<br>
                    RUC: <strong>{{ $empresa_propia['ruc'] }}</strong>
                </div>
            </div>
        </div>

        <div class="doc-badge">
            <div class="doc-title">RECIBO</div>
            <table class="doc-table">
                <tr><td class="lbl">N° RECIBO</td><td class="val">REC-{{ str_pad($cobranza->id, 6, '0', STR_PAD_LEFT) }}</td></tr>
                <tr><td class="lbl">FECHA</td><td class="val-dark">{{ now()->format('d/m/Y') }}</td></tr>
                <tr><td class="lbl">ESTADO</td><td>
                    <span class="estado-badge estado-{{ $cobranza->estado }}">
                        {{ $cobranza->estado === 'pagada' ? 'Pagado' : 'Pendiente' }}
                    </span>
                </td></tr>
                @if($cobranza->contrato)
                <tr><td class="lbl">CONTRATO</td><td class="val-dark">{{ $cobranza->contrato->numero_contrato }}</td></tr>
                @endif
            </table>
        </div>
    </div>

    {{-- CLIENTE --}}
    <div class="section-title">&#128100; Datos del Cliente</div>
    <div class="info-box">
        @php $emp = $cobranza->empresa; @endphp
        <div class="info-row">
            <div class="info-label">Empresa / Cliente:</div>
            <div class="info-value"><strong>{{ $emp?->nombre ?? '—' }}</strong></div>
        </div>
        @if($emp?->ruc)
        <div class="info-row">
            <div class="info-label">RUC:</div>
            <div class="info-value">{{ $emp->ruc }}</div>
        </div>
        @endif
        @if($emp?->encargado)
        <div class="info-row">
            <div class="info-label">Contacto:</div>
            <div class="info-value">{{ $emp->encargado }}</div>
        </div>
        @endif
        @if($emp?->celular)
        <div class="info-row">
            <div class="info-label">Teléfono:</div>
            <div class="info-value">{{ $emp->celular }}</div>
        </div>
        @endif
        @if($emp?->correo)
        <div class="info-row">
            <div class="info-label">Correo:</div>
            <div class="info-value">{{ $emp->correo }}</div>
        </div>
        @endif
    </div>

    {{-- DETALLE DEL PAGO --}}
    <div class="section-title">&#128203; Detalle del Pago</div>
    <div class="info-box" style="margin-bottom:16px">
        <div class="info-row">
            <div class="info-label">Concepto:</div>
            <div class="info-value"><strong>{{ $cobranza->concepto ?? 'Pago de cuota' }}</strong></div>
        </div>
        <div class="info-row">
            <div class="info-label">N° de cuota:</div>
            <div class="info-value">{{ $cobranza->numero_cuota }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Fecha vencimiento:</div>
            <div class="info-value">
                {{ $cobranza->fecha_vencimiento->format('d/m/Y') }}
                @if($cobranza->estado === 'pendiente' && $cobranza->fecha_vencimiento->isPast())
                    <span class="estado-badge" style="background:#FEE2E2;color:#DC2626">Vencida</span>
                @endif
            </div>
        </div>
        @if($cobranza->contrato)
        <div class="info-row">
            <div class="info-label">Contrato:</div>
            <div class="info-value">{{ $cobranza->contrato->numero_contrato }} — {{ $cobranza->contrato->tipo_contrato }}</div>
        </div>
        @endif
    </div>

    {{-- MONTO --}}
    <div class="monto-section">
        <div class="monto-box">
            <div class="monto-line">
                <span class="ml">SUBTOTAL</span>
                <span class="mv">S/ {{ number_format($cobranza->monto, 2, '.', ',') }}</span>
            </div>
            <div class="monto-line">
                <span class="ml">IGV ({{ $empresa_propia['igv_porcentaje'] }}%)</span>
                <span class="mv" style="color:#94A3B8">Incluido</span>
            </div>
            <div class="monto-total">
                <span class="ml">TOTAL A PAGAR</span>
                <span class="mv">S/ {{ number_format($cobranza->monto, 2, '.', ',') }}</span>
            </div>
        </div>
    </div>

    {{-- MÉTODOS DE PAGO --}}
    <div class="section-title">&#128179; Métodos de Pago Aceptados</div>
    <div class="info-box">
        <div style="display:flex;gap:24px;flex-wrap:wrap">
            <div class="info-row" style="margin:0"><div class="info-label">Banco:</div><div class="info-value">{{ $empresa_propia['banco_nombre'] }} — {{ $empresa_propia['banco_cuenta'] }}</div></div>
            <div class="info-row" style="margin:0"><div class="info-label">Yape/Plin:</div><div class="info-value">{{ $empresa_propia['yape_numero'] }}</div></div>
        </div>
    </div>

    {{-- FIRMAS --}}
    <div class="firma-area">
        <div class="firma-col">
            <div class="firma-line"></div>
            <div class="firma-label">{{ $empresa_propia['razon_social'] ?? $empresa_propia['nombre'] }}</div>
            <div class="firma-sub">Emisor del recibo</div>
        </div>
        <div class="firma-col">
            <div class="firma-line"></div>
            <div class="firma-label">{{ $emp?->nombre ?? 'Cliente' }}</div>
            <div class="firma-sub">Recibí conforme</div>
        </div>
    </div>

    <div class="footer">¡{{ $empresa_propia['slogan'] }}! — {{ $empresa_propia['web'] }}</div>
</div>
</body>
</html>
