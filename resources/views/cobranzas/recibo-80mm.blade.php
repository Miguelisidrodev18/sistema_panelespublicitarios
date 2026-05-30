<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Ticket #{{ str_pad($cobranza->id, 6, '0', STR_PAD_LEFT) }}</title>
<style>
* { box-sizing: border-box; margin: 0; padding: 0; }
body {
    font-family: 'Courier New', Courier, monospace;
    font-size: 10px;
    color: #000;
    background: #fff;
    line-height: 1.45;
}

.ticket {
    width: 80mm;
    margin: 0 auto;
    padding: 4mm 4mm 6mm;
    background: #fff;
}

/* Logo / Marca */
.t-logo {
    text-align: center;
    padding: 6px 0 4px;
}
.t-logo img {
    max-width: 48mm;
    max-height: 18mm;
    object-fit: contain;
}
.t-logo-text {
    font-size: 14px;
    font-weight: 700;
    letter-spacing: 1px;
    text-transform: uppercase;
    text-align: center;
}
.t-slogan { font-size: 8px; text-align: center; color: #555; text-transform: uppercase; letter-spacing: 1px; }
.t-info   { font-size: 8.5px; text-align: center; color: #333; margin-top: 3px; line-height: 1.6; }
.t-ruc    { font-size: 8.5px; font-weight: 700; text-align: center; margin-top: 2px; }

.divider-solid  { border: none; border-top: 1px solid #000; margin: 5px 0; }
.divider-dashed { border: none; border-top: 1px dashed #000; margin: 5px 0; }

/* Título del documento */
.t-doctype {
    text-align: center;
    font-size: 13px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 2px;
    margin: 4px 0;
}
.t-docnum {
    text-align: center;
    font-size: 11px;
    font-weight: 700;
    margin-bottom: 3px;
}

/* Filas de datos */
.t-row {
    display: flex;
    justify-content: space-between;
    font-size: 9px;
    margin-bottom: 2px;
}
.t-row .lbl { color: #444; }
.t-row .val { font-weight: 700; text-align: right; max-width: 55mm; }

/* Sección título */
.t-section {
    font-size: 8.5px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .8px;
    margin: 4px 0 2px;
}

/* Monto destacado */
.t-total-label { text-align: center; font-size: 9px; font-weight: 700; text-transform: uppercase; margin-top: 4px; letter-spacing: 1px; }
.t-total-amount { text-align: center; font-size: 20px; font-weight: 900; margin: 2px 0 4px; }

/* Estado */
.t-estado {
    text-align: center;
    font-size: 9px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 1.5px;
    padding: 3px 0;
    border: 2px solid #000;
    margin: 4px 0;
}

/* Pie */
.t-footer { text-align: center; font-size: 8px; color: #555; margin-top: 4px; line-height: 1.6; }
.t-thanks { text-align: center; font-size: 9.5px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; margin-top: 5px; }

/* Botones pantalla */
.print-btn-bar { position: fixed; top: 10px; right: 10px; display: flex; gap: 8px; z-index: 100; }
.btn-action { border: none; padding: 8px 16px; border-radius: 8px; font-size: 12px; font-weight: 700; cursor: pointer; text-decoration: none; display: inline-flex; align-items: center; gap: 5px; }
.btn-print { background: #DC1E2E; color: #fff; }
.btn-back  { background: #374151; color: #fff; }
.btn-action:hover { opacity: .88; }

@media print {
    .print-btn-bar { display: none; }
    body { margin: 0; background: #fff; }
    .ticket { margin: 0; padding: 2mm 3mm; width: 80mm; }
    @page { size: 80mm auto; margin: 0; }
}
</style>
</head>
<body>

<div class="print-btn-bar">
    <a href="{{ route('cobranzas.index') }}" class="btn-action btn-back">&#8592; Volver</a>
    <button class="btn-action btn-print" onclick="window.print()">&#128424; Imprimir</button>
</div>

<div class="ticket">

    {{-- Cabecera --}}
    <div class="t-logo">
        <img src="{{ asset('images/logo.png') }}" alt="Logo"
             onerror="this.style.display='none'">
    </div>
    <div class="t-logo-text">{{ $empresa_propia['nombre'] }}</div>
    <div class="t-slogan">{{ $empresa_propia['slogan'] }}</div>
    <div class="t-ruc">RUC: {{ $empresa_propia['ruc'] }}</div>
    <div class="t-info">
        {{ $empresa_propia['direccion'] }}<br>
        Tel: {{ $empresa_propia['telefono'] }} / {{ $empresa_propia['celular'] }}<br>
        {{ $empresa_propia['email'] }}
    </div>

    <hr class="divider-solid">

    {{-- Tipo documento --}}
    <div class="t-doctype">RECIBO DE PAGO</div>
    <div class="t-docnum">N° REC-{{ str_pad($cobranza->id, 6, '0', STR_PAD_LEFT) }}</div>
    <div class="t-row">
        <span class="lbl">Fecha:</span>
        <span class="val">{{ now()->format('d/m/Y H:i') }}</span>
    </div>

    <hr class="divider-dashed">

    {{-- Cliente --}}
    <div class="t-section">Cliente</div>
    <div class="t-row">
        <span class="lbl">Empresa:</span>
        <span class="val">{{ $cobranza->empresa?->nombre ?? '—' }}</span>
    </div>
    @if($cobranza->empresa?->ruc)
    <div class="t-row">
        <span class="lbl">RUC:</span>
        <span class="val">{{ $cobranza->empresa->ruc }}</span>
    </div>
    @endif
    @if($cobranza->empresa?->encargado)
    <div class="t-row">
        <span class="lbl">Contacto:</span>
        <span class="val">{{ $cobranza->empresa->encargado }}</span>
    </div>
    @endif

    <hr class="divider-dashed">

    {{-- Detalle --}}
    <div class="t-section">Detalle del Pago</div>
    @if($cobranza->concepto)
    <div class="t-row">
        <span class="lbl">Concepto:</span>
        <span class="val">{{ $cobranza->concepto }}</span>
    </div>
    @endif
    <div class="t-row">
        <span class="lbl">N° Cuota:</span>
        <span class="val">{{ $cobranza->numero_cuota }}</span>
    </div>
    <div class="t-row">
        <span class="lbl">Vencimiento:</span>
        <span class="val">{{ $cobranza->fecha_vencimiento->format('d/m/Y') }}</span>
    </div>
    @if($cobranza->contrato)
    <div class="t-row">
        <span class="lbl">Contrato:</span>
        <span class="val">{{ $cobranza->contrato->numero_contrato }}</span>
    </div>
    @endif

    <hr class="divider-solid">

    {{-- Monto --}}
    <div class="t-total-label">TOTAL A PAGAR</div>
    <div class="t-total-amount">S/ {{ number_format($cobranza->monto, 2, '.', ',') }}</div>

    {{-- Estado --}}
    <div class="t-estado">
        @if($cobranza->estado === 'pagada') ✓ PAGADO
        @else ⏳ PENDIENTE
        @endif
    </div>

    <hr class="divider-dashed">

    {{-- Métodos de pago --}}
    <div class="t-section">Métodos de Pago</div>
    <div class="t-row"><span class="lbl">{{ $empresa_propia['banco_nombre'] }}:</span><span class="val">{{ $empresa_propia['banco_cuenta'] }}</span></div>
    <div class="t-row"><span class="lbl">Yape/Plin:</span><span class="val">{{ $empresa_propia['yape_numero'] }}</span></div>

    <hr class="divider-solid">

    {{-- Pie --}}
    <div class="t-thanks">¡Gracias por su pago!</div>
    <div class="t-footer">
        {{ $empresa_propia['web'] }}<br>
        Documento generado el {{ now()->format('d/m/Y H:i:s') }}
    </div>

    {{-- Espacio para corte --}}
    <div style="margin-top:10mm"></div>

</div>
</body>
</html>
