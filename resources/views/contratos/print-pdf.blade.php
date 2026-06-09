<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<style>
    * { margin: 0; padding: 0; box-sizing: border-box; }

    body {
        font-family: Arial, sans-serif;
        font-size: 10pt;
        color: #1a1a1a;
        line-height: 1.6;
    }

    /* ── Sello de agua ── */
    .watermark {
        position: fixed;
        top: 38%;
        left: 50%;
        width: 300px;
        margin-left: -150px;
        opacity: 0.06;
        filter: grayscale(100%);
        z-index: -1;
    }

    /* ── Encabezado ── */
    .header-table { width: 100%; border-bottom: 2px solid #CC1A1A; padding-bottom: 10px; margin-bottom: 14px; }
    .header-logo  { width: 70px; vertical-align: middle; }
    .header-logo img { height: 56px; width: auto; }
    .company-name { font-size: 15pt; font-weight: bold; }
    .company-sub  { font-size: 8pt; color: #666; text-transform: uppercase; letter-spacing: 1px; }
    .company-ruc  { font-size: 8.5pt; color: #444; margin-top: 3px; }

    /* ── Título ── */
    .contract-title {
        text-align: center;
        font-size: 11pt;
        font-weight: bold;
        font-style: italic;
        text-decoration: underline;
        text-transform: uppercase;
        margin: 12px 0;
        padding: 8px 0;
        border-top: 1px solid #ddd;
        border-bottom: 1px solid #ddd;
    }

    /* ── Párrafo de partes ── */
    .intro-partes {
        font-size: 12pt;
        font-style: italic;
        text-align: justify;
        margin-bottom: 14px;
        line-height: 1.7;
    }

    /* ── Cláusulas ── */
    .clausula { margin-bottom: 12px; }
    .clausula-title {
        font-weight: bold;
        font-style: italic;
        text-decoration: underline;
        font-size: 10pt;
        margin-bottom: 4px;
    }
    .clausula-body {
        font-size: 10pt;
        font-style: italic;
        text-align: justify;
        line-height: 1.7;
    }
    .clausula-body p { margin-bottom: 5px; }

    /* ── Servicios ── */
    .servicio-num  { font-weight: bold; font-style: italic; font-size: 10pt; margin: 4px 0 2px 14px; }
    .servicio-item { margin: 2px 0 6px 30px; font-style: italic; font-size: 10pt; line-height: 1.6; }

    /* ── Aclaratorio ── */
    .aclaratorio-title { font-weight: bold; font-style: italic; text-decoration: underline; margin: 6px 0 3px 0; font-size: 10pt; }
    .aclaratorio-num   { font-weight: bold; font-style: italic; font-size: 10pt; margin: 3px 0 2px 14px; }
    .aclaratorio-item  { margin: 2px 0 4px 30px; font-style: italic; font-size: 10pt; }

    /* ── Bullets ── */
    .bullet-list { margin: 4px 0 6px 14px; }
    .bullet-list li { font-style: italic; font-size: 10pt; line-height: 1.6; list-style: disc; margin-left: 14px; text-align: justify; }

    /* ── Banco ── */
    .banco-data { font-style: italic; font-size: 10pt; margin: 4px 0 4px 14px; line-height: 1.8; }

    /* ── Firma ── */
    .firma-intro { font-style: italic; font-size: 10pt; margin-bottom: 8px; }
    .firma-ciudad { font-style: italic; font-size: 10pt; text-align: right; margin-bottom: 40px; }
    .firma-table  { width: 100%; }
    .firma-col    { width: 48%; text-align: center; vertical-align: top; }
    .firma-espacio { height: 44px; }
    .firma-linea  { border-top: 1px solid #1a1a1a; margin-bottom: 4px; }
    .firma-label  { font-weight: bold; font-style: italic; font-size: 10pt; text-transform: uppercase; }
    .firma-nombre { font-weight: bold; font-style: italic; font-size: 10pt; }
    .firma-cargo  { font-style: italic; font-size: 9pt; text-transform: uppercase; }
</style>
</head>
<body>

@php
    if (!function_exists('montoALetrasPdf')) {
        function montoALetrasPdf(float $numero): string {
            $unidades = ['','UN','DOS','TRES','CUATRO','CINCO','SEIS','SIETE','OCHO','NUEVE',
                         'DIEZ','ONCE','DOCE','TRECE','CATORCE','QUINCE','DIECISEIS',
                         'DIECISIETE','DIECIOCHO','DIECINUEVE'];
            $decenas  = ['','','VEINTE','TREINTA','CUARENTA','CINCUENTA',
                         'SESENTA','SETENTA','OCHENTA','NOVENTA'];
            $centenas = ['','CIENTO','DOSCIENTOS','TRESCIENTOS','CUATROCIENTOS','QUINIENTOS',
                         'SEISCIENTOS','SETECIENTOS','OCHOCIENTOS','NOVECIENTOS'];
            $entero   = (int) $numero;
            $decimal  = round(($numero - $entero) * 100);
            $conv = function(int $n) use ($unidades,$decenas,$centenas,&$conv): string {
                if ($n===0)   return '';
                if ($n===100) return 'CIEN';
                if ($n<20)    return $unidades[$n];
                if ($n<100)  { $d=intdiv($n,10);$u=$n%10; return $decenas[$d].($u?' Y '.$unidades[$u]:''); }
                if ($n<1000) { $c=intdiv($n,100);$r=$n%100; return $centenas[$c].($r?' '.$conv($r):''); }
                $m=intdiv($n,1000);$r=$n%1000;
                return ($m===1?'MIL':$conv($m).' MIL').($r?' '.$conv($r):'');
            };
            $texto = $entero===0?'CERO':$conv($entero);
            return ucfirst(strtolower($texto)).' con '.str_pad($decimal,2,'0',STR_PAD_LEFT).'/100';
        }
    }

    $montoLetras   = montoALetrasPdf((float)$contrato->monto_total);
    $anio          = $contrato->fecha_inicio?->format('Y') ?? now()->format('Y');
    $duracionMeses = 1;
    if ($contrato->fecha_inicio && $contrato->fecha_fin) {
        $d = (int) $contrato->fecha_inicio->diffInMonths($contrato->fecha_fin);
        if ($d >= 1) $duracionMeses = $d;
    }
    $fechaInicioTexto = $contrato->fecha_inicio
        ? $contrato->fecha_inicio->locale('es')->isoFormat('D [de] MMMM [del] YYYY')
        : '—';
    $fechaFirmaTexto  = now()->locale('es')->isoFormat('D [de] MMMM [del] YYYY');
    $logoPath = public_path('images/logo.png');
@endphp

{{-- Sello de agua --}}
@if(file_exists($logoPath))
<img src="{{ $logoPath }}" class="watermark" alt="">
@endif

{{-- ══ ENCABEZADO ══ --}}
<table class="header-table" cellpadding="0" cellspacing="0">
    <tr>
        <td class="header-logo">
            @if(file_exists($logoPath))
            <img src="{{ $logoPath }}" style="height:56px;width:auto">
            @endif
        </td>
        <td style="padding-left:12px;vertical-align:middle">
            <div class="company-name">{{ $empresa_contrato['nombre'] }}</div>
            <div class="company-sub">Arte Visual &amp; Diseño</div>
            <div class="company-ruc">RUC: {{ $empresa_contrato['ruc'] }} | {{ $empresa_contrato['domicilio'] }}</div>
        </td>
    </tr>
</table>

{{-- ══ TÍTULO ══ --}}
<div class="contract-title">
    CONTRATO N.º {{ $contrato->numero_contrato }} – PPSAC/{{ $anio }} DE SERVICIOS PUBLICITARIOS
</div>

{{-- ══ PARTES ══ --}}
<p class="intro-partes">
    Conste por el presente documento un Contrato de Publicidad, que celebramos de una parte
    <b>{{ $empresa_contrato['nombre'] }}</b> con RUC. N°{{ $empresa_contrato['ruc'] }},
    con domicilio en {{ $empresa_contrato['domicilio'] }} representado por su Gerente General
    <b>{{ $empresa_contrato['representante'] }}</b>,
    identificada con D.N.I. N°{{ $empresa_contrato['dni_representante'] }},
    a quien en adelante se le denominará <b>"LA EMPRESA"</b>,
    y de la otra parte debidamente representado por
    <b>{{ strtoupper($contrato->contratante) }}</b>{{ ($contrato->doc_tipo && $contrato->doc_numero) ? ', identificado con '.$contrato->doc_tipo.' N°'.$contrato->doc_numero : '' }}{{ $contrato->direccion ? ', domiciliado en '.strtoupper($contrato->direccion) : '' }}
    – a quien en lo sucesivo se le denominará <b>"EL CONTRATANTE"</b>,
    en los términos y condiciones siguientes:
</p>

{{-- CLÁUSULA PRIMERA --}}
<div class="clausula">
    <div class="clausula-title">CLÁUSULA PRIMERA. - ANTECEDENTES</div>
    <div class="clausula-body">
        <p><b>"LA EMPRESA"</b>, es una persona jurídica cuyo objeto es brindar servicio de publicidad
        visual en pantalla LED, paneles publicitarios y otros servicios a favor de
        <b>"EL CONTRATANTE"</b> <b>{{ strtoupper($contrato->contratante) }}</b></p>
    </div>
</div>

{{-- CLÁUSULA SEGUNDA --}}
<div class="clausula">
    <div class="clausula-title">CLÁUSULA SEGUNDA. - OBJETO</div>
    <div class="clausula-body">
        <p><b>"LA EMPRESA"</b>, se obliga a prestar el servicio de publicidad a
        <b>"EL CONTRATANTE"</b> <b>{{ strtoupper($contrato->contratante) }}</b>
        de acuerdo a los términos y detalles establecidos en el presente contrato.</p>
        <p>Servicio de Publicidad Exterior <b>"{{ $empresa_contrato['nombre'] }}"</b></p>
        @forelse($contrato->elementos as $i => $elem)
        @php
            $panel     = $elem->panel ?? null;
            $ubicacion = $panel ? ($panel->nombre ?? $panel->direccion ?? null) : null;
            $medidas   = $panel ? ($panel->medidas ?? null) : null;
            $tipoLabel = match($elem->tipo_elemento) {
                'digital'     => 'PANTALLA LED',
                'tradicional' => 'VALLA / PANEL',
                default       => strtoupper($elem->tipo_elemento),
            };
        @endphp
        <div class="servicio-num">
            {{ $i+1 }}. {{ $tipoLabel }}{{ $ubicacion ? ' - Ubicado: '.strtoupper($ubicacion) : ($elem->codigo ? ' - Código: '.$elem->codigo : '') }}
        </div>
        @if($medidas)
        <div class="servicio-item">• MEDIDAS: {{ $medidas }}</div>
        @endif
        @if($elem->tiempo_contrato)
        <div class="servicio-item">• TIEMPO DE CONTRATO: {{ $elem->tiempo_contrato }} {{ $elem->tiempo_contrato == 1 ? 'mes' : 'meses' }}</div>
        @endif
        @if($elem->observaciones)
        <div class="servicio-item">• {{ strtoupper($elem->observaciones) }}</div>
        @endif
        @empty
        <p><i>Sin elementos registrados.</i></p>
        @endforelse
    </div>
</div>

{{-- CLÁUSULA TERCERA --}}
<div class="clausula">
    <div class="clausula-title">CLÁUSULA TERCERA. - VIGENCIA DEL CONTRATO</div>
    <div class="clausula-body">
        <p>El presente contrato tendrá una duración de
        <b>{{ $duracionMeses }} {{ $duracionMeses == 1 ? 'mes' : 'meses' }}</b>,
        a partir del {{ $fechaInicioTexto }} como primer mes.
        EL usuario podrá elegir en qué meses ingresará a una de las pantallas.</p>
        @php
            $elementosConFechas = $contrato->elementos->filter(fn($e) => $e->fecha_instalacion || $e->fecha_retiro);
        @endphp
        @if($elementosConFechas->count() > 0)
        <div class="aclaratorio-title">ACLARATORIO:</div>
        @foreach($elementosConFechas as $j => $elem)
        @php
            $panel     = $elem->panel ?? null;
            $ubicacion = $panel ? ($panel->nombre ?? $panel->direccion ?? $elem->codigo) : $elem->codigo;
            $tipoLabel = match($elem->tipo_elemento) { 'digital' => 'PANTALLA LED', 'tradicional' => 'VALLA / PANEL', default => strtoupper($elem->tipo_elemento) };
            $fInicio = $elem->fecha_instalacion?->format('d/m/Y') ?? $contrato->fecha_inicio?->format('d/m/Y') ?? '—';
            $fFin    = $elem->fecha_retiro?->format('d/m/Y') ?? $contrato->fecha_fin?->format('d/m/Y') ?? '—';
        @endphp
        <div class="aclaratorio-num">{{ $j+1 }}. {{ $tipoLabel }} - Ubicado: {{ strtoupper($ubicacion) }}</div>
        <div class="aclaratorio-item">• <b>FECHA INICIO:</b> {{ $fInicio }} &nbsp;&nbsp;&nbsp; <b>FECHA FINAL:</b> {{ $fFin }}</div>
        @endforeach
        @else
        <p>FECHA INICIO: <b>{{ $contrato->fecha_inicio?->format('d/m/Y') ?? '—' }}</b>
        &nbsp;&nbsp;&nbsp; FECHA FINAL: <b>{{ $contrato->fecha_fin?->format('d/m/Y') ?? '—' }}</b></p>
        @endif
    </div>
</div>

{{-- CLÁUSULA CUARTA --}}
<div class="clausula">
    <div class="clausula-title">CLÁUSULA CUARTA. – MONTO CONTRACTUAL</div>
    <div class="clausula-body">
        <p>El monto de servicio de publicidad es de
        <b>S/{{ number_format((float)$contrato->monto_total, 2, '.', ',') }}</b>
        ({{ $montoLetras }} soles)</p>
    </div>
</div>

{{-- CLÁUSULA QUINTA --}}
<div class="clausula">
    <div class="clausula-title">CLÁUSULA QUINTA. - FORMA DE PAGO</div>
    <div class="clausula-body">
        <ul class="bullet-list">
            <li>El monto dinerario especificado es definitivo, y <b>LA EMPRESA</b> no tiene derecho bajo ninguna circunstancia a exigir otros montos adicionales.</li>
            <li>Los precios incluyen, derechos de uso de espacio, en caso corresponda.</li>
        </ul>
        <p>El monto será abonado a la Cuenta del Banco {{ $empresa_contrato['banco'] }}</p>
        <div class="banco-data">
            CTA: {{ $empresa_contrato['cta'] }}<br>
            CCI: {{ $empresa_contrato['cci'] }}<br>
            Cta. Detracción: {{ $empresa_contrato['cta_detraccion'] }}
        </div>
    </div>
</div>

{{-- CLÁUSULA SEXTA --}}
<div class="clausula">
    <div class="clausula-title">CLÁUSULA SEXTA. RESOLUCIÓN</div>
    <div class="clausula-body">
        <p>El <b>ARRENDATARIO</b> podrá resolver el presente contrato antes de su término, comunicando por escrito a <b>"EL CONTRATANTE"</b> con un plazo de no menor a 10 días calendarios a la fecha de vencimiento. En caso de la referida resolución anticipada del contrato, así como de su suspensión por cualquier motivo, el <b>ARRENDATARIO</b> solo pagará a <b>"EL CONTRATANTE"</b> por los días y servicios efectivamente prestados.</p>
    </div>
</div>

{{-- CLÁUSULA SÉPTIMA --}}
<div class="clausula">
    <div class="clausula-title">CLÁUSULA SÉPTIMA. - OBLIGACIONES DE LAS PARTES</div>
    <div class="clausula-body">
        <p><b>LA EMPRESA</b> se obliga a:</p>
        <ul class="bullet-list">
            <li>Ejecutar la prestación, según lo establecido en el presente contrato y en sus partes integrantes.</li>
            <li>A reconocer que es exclusivo responsable de sujetarse a las disposiciones legales vigentes en materia de contenidos publicitarios respecto de la publicidad que se difunda a través de los espacios publicitarios.</li>
            <li>La empresa es exclusivamente responsable por el contenido de la publicidad que se difunda en los espacios publicitarios en virtud del presente contrato.</li>
        </ul>
        <p>En caso que no se cumpla con el pago acordado, establecido en la cláusula quinta; <b>LA EMPRESA</b> queda facultado para desinstalar el módulo de venta del espacio publicitario, quedando expedito su derecho a interponer las acciones legales correspondientes respecto a los daños y perjuicios por incumplimiento de <b>"EL CONTRATANTE"</b></p>
    </div>
</div>

{{-- CLÁUSULA OCTAVA --}}
<div class="clausula">
    <div class="clausula-title">CLÁUSULA OCTAVA. - PENALIDADES</div>
    <div class="clausula-body">
        <p>En caso de incumplimiento parcial o total a sus obligaciones por parte de <b>"LA EMPRESA"</b> o del <b>"EL CONTRATANTE"</b>, la parte afectada podrá exponer los agravios y perjuicios y exigir el pago de la relación civil correspondiente al incumplimiento de la obligación pecuniaria y demás daños ocasionados a los intereses de un contrato de servicios empresariales, pudiendo así iniciar las acciones legales en la vía arbitral o judicial para hacer efectivo el cobro de dichas obligaciones e indemnizaciones.</p>
    </div>
</div>

{{-- ══ FIRMA ══ --}}
<div class="clausula">
    <p class="firma-intro">Las partes firman por duplicado en señal de conformidad.</p>
    <p class="firma-ciudad">{{ $empresa_contrato['ciudad'] }}, {{ $fechaFirmaTexto }}</p>
    <table class="firma-table" cellpadding="0" cellspacing="0">
        <tr>
            <td class="firma-col">
                <div class="firma-espacio"></div>
                <div class="firma-linea"></div>
                <div class="firma-label">EL CONTRATANTE</div>
                <div class="firma-nombre">{{ strtoupper($contrato->contratante) }}</div>
                @if($contrato->doc_tipo && $contrato->doc_numero)
                <div class="firma-cargo">{{ $contrato->doc_tipo }}: {{ $contrato->doc_numero }}</div>
                @endif
            </td>
            <td style="width:4%"></td>
            <td class="firma-col">
                <div class="firma-espacio"></div>
                <div class="firma-linea"></div>
                <div class="firma-label">LA EMPRESA</div>
                <div class="firma-nombre">{{ $empresa_contrato['representante'] }}</div>
                <div class="firma-cargo">Gerente General</div>
            </td>
        </tr>
    </table>
</div>

</body>
</html>
