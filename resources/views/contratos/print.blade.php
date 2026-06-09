<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contrato {{ $contrato->numero_contrato }}</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: Calibri, 'Calibri', sans-serif;
            font-size: 12pt;
            color: #1a1a1a;
            background: #D1D5DB;
            line-height: 1.6;
        }

        .page {
            width: 210mm;
            min-height: 297mm;
            margin: 20px auto;
            padding: 20mm 22mm 18mm;
            background: #fff;
            box-shadow: 0 4px 24px rgba(0,0,0,0.18);
            position: relative;
        }

        /* ── Sello de agua ── */
        .watermark {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-30deg);
            width: 340px;
            pointer-events: none;
            z-index: 0;
            opacity: 0.07;
            filter: grayscale(100%) contrast(200%);
            user-select: none;
        }

        .page > *:not(.watermark) { position: relative; z-index: 1; }

        /* ── Encabezado ── */
        .header {
            display: flex;
            align-items: center;
            gap: 14px;
            margin-bottom: 20px;
            border-bottom: 2px solid #CC1A1A;
            padding-bottom: 12px;
        }
        .header-logo img { height: 64px; width: auto; }
        .header-logo-placeholder {
            height: 64px; width: 80px;
            background: #CC1A1A; border-radius: 6px;
            display: flex; align-items: center; justify-content: center;
            color: #fff; font-weight: 900; font-size: 18px;
        }
        .header-company-name {
            font-size: 16px; font-weight: 900; letter-spacing: 0.5px; color: #1a1a1a;
        }
        .header-company-sub {
            font-size: 9pt; color: #666; text-transform: uppercase;
            letter-spacing: 1px; margin-top: 2px;
        }
        .header-company-ruc {
            font-size: 9pt; color: #444; margin-top: 4px;
        }

        /* ── Título del contrato ── */
        .contract-title {
            text-align: center;
            font-size: 11pt;
            font-weight: 700;
            font-style: italic;
            text-decoration: underline;
            text-transform: uppercase;
            margin: 18px 0 18px;
        }

        /* ── Párrafo introductorio de partes ── */
        .intro-partes {
            font-size: 12pt;
            font-style: italic;
            text-align: justify;
            margin-bottom: 18px;
            line-height: 1.7;
        }

        /* ── Cláusulas ── */
        .clausula {
            margin-bottom: 16px;
        }
        .clausula-title {
            font-weight: 700;
            font-style: italic;
            text-decoration: underline;
            font-size: 10pt;
            margin-bottom: 6px;
        }
        .clausula-body {
            font-size: 10pt;
            font-style: italic;
            text-align: justify;
            line-height: 1.7;
        }
        .clausula-body p { margin-bottom: 6px; }

        /* ── Lista de servicios (numerada con bullets) ── */
        .servicios-list {
            margin: 8px 0 8px 14px;
        }
        .servicio-num {
            font-weight: 700;
            font-style: italic;
            margin-bottom: 2px;
        }
        .servicio-bullets {
            list-style: none;
            margin: 2px 0 8px 20px;
        }
        .servicio-bullets li::before { content: "• "; font-weight: 700; }
        .servicio-bullets li {
            font-style: italic;
            font-size: 10pt;
            line-height: 1.6;
        }

        /* ── Aclaratorio ── */
        .aclaratorio-title {
            font-weight: 700;
            font-style: italic;
            text-decoration: underline;
            margin: 8px 0 4px 0;
        }
        .aclaratorio-list {
            margin-left: 14px;
        }
        .aclaratorio-num {
            font-weight: 700;
            font-style: italic;
            margin-bottom: 2px;
        }
        .aclaratorio-bullets {
            list-style: none;
            margin: 2px 0 8px 20px;
        }
        .aclaratorio-bullets li::before { content: "• "; font-weight: 700; }
        .aclaratorio-bullets li { font-style: italic; font-size: 10pt; line-height: 1.6; }

        /* ── Datos bancarios ── */
        .banco-data {
            font-style: italic;
            font-size: 10pt;
            margin: 6px 0;
            line-height: 1.9;
        }

        /* ── Lista de obligaciones (bullets) ── */
        .oblig-list {
            list-style: none;
            margin: 4px 0 8px 10px;
        }
        .oblig-list li::before { content: "• "; font-weight: 700; }
        .oblig-list li {
            font-style: italic;
            font-size: 10pt;
            line-height: 1.6;
            text-align: justify;
            margin-bottom: 2px;
        }

        /* ── Firma ── */
        .firma-section {
            margin-top: 30px;
            page-break-inside: avoid;
        }
        .firma-intro {
            font-size: 11pt;
            font-style: italic;
            text-align: justify;
            margin-bottom: 12px;
        }
        .firma-ciudad {
            font-size: 11pt;
            font-style: italic;
            text-align: right;
            margin-bottom: 50px;
        }
        .firma-cols {
            display: flex;
            justify-content: space-between;
            gap: 30px;
        }
        .firma-col {
            flex: 1;
            text-align: center;
        }
        .firma-espacio { height: 48px; }
        .firma-linea {
            border-top: 1px solid #1a1a1a;
            margin-bottom: 5px;
        }
        .firma-label {
            font-weight: 700;
            font-style: italic;
            font-size: 10.5pt;
            text-transform: uppercase;
        }
        .firma-nombre {
            font-weight: 700;
            font-style: italic;
            font-size: 10.5pt;
        }
        .firma-cargo {
            font-style: italic;
            font-size: 10pt;
            text-transform: uppercase;
        }

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
        .btn-print { background: #CC1A1A; color: #fff; box-shadow: 0 4px 12px rgba(204,26,26,.35); }
        .btn-back  { background: #374151; color: #fff; }
        .btn-action:hover { opacity: .88; }

        @media print {
            .print-btn-bar { display: none; }
            body { margin: 0; background: #fff; }
            .page {
                margin: 0;
                padding: 15mm 18mm;
                width: 100%;
                min-height: auto;
                box-shadow: none;
            }
            .clausula { page-break-inside: auto; }
            .clausula-title { page-break-after: avoid; }
            .servicio-item, .aclaratorio-item { page-break-inside: avoid; }
            .firma-section { page-break-before: avoid; margin-top: 16px; }
            .firma-cols { page-break-inside: avoid; }
            p, li { orphans: 3; widows: 3; }
        }
        @page { size: A4; margin: 0; }
    </style>
</head>
<body>

@php
    /* ── Conversión de monto a letras (español) ── */
    function montoALetras(float $numero): string {
        $unidades = ['', 'UN', 'DOS', 'TRES', 'CUATRO', 'CINCO', 'SEIS', 'SIETE', 'OCHO', 'NUEVE',
                     'DIEZ', 'ONCE', 'DOCE', 'TRECE', 'CATORCE', 'QUINCE', 'DIECISÉIS',
                     'DIECISIETE', 'DIECIOCHO', 'DIECINUEVE'];
        $decenas  = ['', '', 'VEINTE', 'TREINTA', 'CUARENTA', 'CINCUENTA',
                     'SESENTA', 'SETENTA', 'OCHENTA', 'NOVENTA'];
        $centenas = ['', 'CIENTO', 'DOSCIENTOS', 'TRESCIENTOS', 'CUATROCIENTOS', 'QUINIENTOS',
                     'SEISCIENTOS', 'SETECIENTOS', 'OCHOCIENTOS', 'NOVECIENTOS'];
        $entero  = (int) $numero;
        $decimal = round(($numero - $entero) * 100);
        $conv = function(int $n) use ($unidades, $decenas, $centenas, &$conv): string {
            if ($n === 0)   return '';
            if ($n === 100) return 'CIEN';
            if ($n < 20)    return $unidades[$n];
            if ($n < 100) {
                $d = intdiv($n, 10); $u = $n % 10;
                return $decenas[$d] . ($u ? ' Y ' . $unidades[$u] : '');
            }
            if ($n < 1000) {
                $c = intdiv($n, 100); $r = $n % 100;
                return $centenas[$c] . ($r ? ' ' . $conv($r) : '');
            }
            $m = intdiv($n, 1000); $r = $n % 1000;
            $miles = $m === 1 ? 'MIL' : $conv($m) . ' MIL';
            return $miles . ($r ? ' ' . $conv($r) : '');
        };
        $texto = $entero === 0 ? 'CERO' : $conv($entero);
        return ucfirst(strtolower($texto)) . ' con ' . str_pad($decimal, 2, '0', STR_PAD_LEFT) . '/100';
    }

    $montoLetras  = montoALetras((float)$contrato->monto_total);
    $anio         = $contrato->fecha_inicio?->format('Y') ?? now()->format('Y');
    $duracionMeses = 1;
    if ($contrato->fecha_inicio && $contrato->fecha_fin) {
        $d = (int) $contrato->fecha_inicio->diffInMonths($contrato->fecha_fin);
        if ($d >= 1) $duracionMeses = $d;
    }
    $fechaInicioTexto = $contrato->fecha_inicio
        ? $contrato->fecha_inicio->locale('es')->isoFormat('D [de] MMMM [del] YYYY')
        : '—';
    $fechaFirmaTexto = now()->locale('es')->isoFormat('D [de] MMMM [del] YYYY');
@endphp

{{-- ── Barra de acciones ── --}}
<div class="print-btn-bar">
    <a href="{{ route('contratos.show', $contrato) }}" class="btn-action btn-back">&#8592; Volver</a>
    <button class="btn-action btn-print" onclick="window.print()">&#128424; Imprimir / PDF</button>
</div>

<div class="page">

    {{-- ══ SELLO DE AGUA ══ --}}
    <img src="{{ asset('images/logo.png') }}" class="watermark" alt="">

    {{-- ══ ENCABEZADO ══ --}}
    <div class="header">
        <div class="header-logo">
            <img src="{{ asset('images/logo.png') }}" alt="Logo"
                 onerror="this.style.display='none';this.nextElementSibling.style.display='flex'">
            <div class="header-logo-placeholder" style="display:none">PP</div>
        </div>
        <div>
            <div class="header-company-name">{{ $empresa_contrato['nombre'] }}</div>
            <div class="header-company-sub">Arte Visual &amp; Diseño</div>
            <div class="header-company-ruc">
                RUC: {{ $empresa_contrato['ruc'] }} &nbsp;|&nbsp; {{ $empresa_contrato['domicilio'] }}
            </div>
        </div>
    </div>

    {{-- ══ TÍTULO ══ --}}
    <div class="contract-title">
        CONTRATO N.º {{ $contrato->numero_contrato }} – PPSAC/{{ $anio }} DE SERVICIOS PUBLICITARIOS
    </div>

    {{-- ══ PÁRRAFO DE PARTES ══ --}}
    <p class="intro-partes">
        Conste por el presente documento un Contrato de Publicidad, que celebramos de una parte
        <strong>{{ $empresa_contrato['nombre'] }}</strong> con RUC. N°{{ $empresa_contrato['ruc'] }},
        con domicilio en {{ $empresa_contrato['domicilio'] }} representado por su Gerente General
        <strong>{{ $empresa_contrato['representante'] }}</strong>,
        identificada con D.N.I. N°{{ $empresa_contrato['dni_representante'] }},
        a quien en adelante se le denominará <strong>"LA EMPRESA"</strong>,
        y de la otra parte debidamente representado por
        <strong>{{ strtoupper($contrato->contratante) }}</strong>{{ ($contrato->doc_tipo && $contrato->doc_numero) ? ', identificado con '.$contrato->doc_tipo.' N°'.$contrato->doc_numero : '' }}{{ $contrato->direccion ? ', domiciliado en '.strtoupper($contrato->direccion) : '' }}
        – a quien en lo sucesivo se le denominará <strong>"EL CONTRATANTE"</strong>,
        en los términos y condiciones siguientes:
    </p>

    {{-- ══ CLÁUSULA PRIMERA ══ --}}
    <div class="clausula">
        <div class="clausula-title">CLÁUSULA PRIMERA. - ANTECEDENTES</div>
        <div class="clausula-body">
            <p>
                <strong>"LA EMPRESA"</strong>, es una persona jurídica cuyo objeto es brindar servicio de publicidad
                visual en pantalla LED, paneles publicitarios y otros servicios a favor de
                <strong>"EL CONTRATANTE"</strong> <strong>{{ strtoupper($contrato->contratante) }}</strong>
            </p>
        </div>
    </div>

    {{-- ══ CLÁUSULA SEGUNDA ══ --}}
    <div class="clausula">
        <div class="clausula-title">CLÁUSULA SEGUNDA. - OBJETO</div>
        <div class="clausula-body">
            <p>
                <strong>"LA EMPRESA"</strong>, se obliga a prestar el servicio de publicidad a
                <strong>"EL CONTRATANTE"</strong> <strong>{{ strtoupper($contrato->contratante) }}</strong>
                de acuerdo a los términos y detalles establecidos en el presente contrato.
            </p>
            <p>Servicio de Publicidad Exterior <strong>"{{ $empresa_contrato['nombre'] }}"</strong></p>

            <div class="servicios-list">
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
                    {{ $i + 1 }}. {{ $tipoLabel }}
                    @if($ubicacion) - Ubicado: {{ strtoupper($ubicacion) }}@endif
                    @if(!$ubicacion && $elem->codigo) – Código: {{ $elem->codigo }}@endif
                </div>
                <ul class="servicio-bullets">
                    @if($medidas)
                    <li>MEDIDAS: {{ $medidas }}</li>
                    @endif
                    @if($elem->tiempo_contrato)
                    <li>TIEMPO DE CONTRATO: {{ $elem->tiempo_contrato }} {{ $elem->tiempo_contrato == 1 ? 'mes' : 'meses' }}</li>
                    @endif
                    @if($elem->observaciones)
                    <li>{{ strtoupper($elem->observaciones) }}</li>
                    @endif
                </ul>
                @empty
                <p><em>Sin elementos registrados en el contrato.</em></p>
                @endforelse
            </div>
        </div>
    </div>

    {{-- ══ CLÁUSULA TERCERA ══ --}}
    <div class="clausula">
        <div class="clausula-title">CLÁUSULA TERCERA. - VIGENCIA DEL CONTRATO</div>
        <div class="clausula-body">
            <p>
                El presente contrato tendrá una duración de
                <strong>{{ $duracionMeses }} {{ $duracionMeses == 1 ? 'mes' : 'meses' }}</strong>,
                a partir del {{ $fechaInicioTexto }} como primer mes.
                EL usuario podrá elegir en qué meses ingresará a una de las pantallas.
            </p>

            @php
                $elementosConFechas = $contrato->elementos->filter(fn($e) =>
                    $e->fecha_instalacion || $e->fecha_retiro
                );
            @endphp
            @if($elementosConFechas->count() > 0)
            <div class="aclaratorio-title">ACLARATORIO:</div>
            <div class="aclaratorio-list">
                @foreach($elementosConFechas as $j => $elem)
                @php
                    $panel     = $elem->panel ?? null;
                    $ubicacion = $panel ? ($panel->nombre ?? $panel->direccion ?? $elem->codigo) : $elem->codigo;
                    $tipoLabel = match($elem->tipo_elemento) {
                        'digital'     => 'PANTALLA LED',
                        'tradicional' => 'VALLA / PANEL',
                        default       => strtoupper($elem->tipo_elemento),
                    };
                    $fInicio = $elem->fecha_instalacion?->format('d/m/Y')
                        ?? $contrato->fecha_inicio?->format('d/m/Y') ?? '—';
                    $fFin    = $elem->fecha_retiro?->format('d/m/Y')
                        ?? $contrato->fecha_fin?->format('d/m/Y') ?? '—';
                @endphp
                <div class="aclaratorio-num">
                    {{ $j + 1 }}. {{ $tipoLabel }} - Ubicado: {{ strtoupper($ubicacion) }}
                </div>
                <ul class="aclaratorio-bullets">
                    <li>
                        <strong>FECHA INICIO:</strong> {{ $fInicio }}
                        &nbsp;&nbsp;&nbsp;
                        <strong>FECHA FINAL:</strong> {{ $fFin }}
                    </li>
                </ul>
                @endforeach
            </div>
            @else
            <p>
                FECHA INICIO: <strong>{{ $contrato->fecha_inicio?->format('d/m/Y') ?? '—' }}</strong>
                &nbsp;&nbsp;&nbsp;
                FECHA FINAL: <strong>{{ $contrato->fecha_fin?->format('d/m/Y') ?? '—' }}</strong>
            </p>
            @endif
        </div>
    </div>

    {{-- ══ CLÁUSULA CUARTA ══ --}}
    <div class="clausula">
        <div class="clausula-title">CLÁUSULA CUARTA. – MONTO CONTRACTUAL</div>
        <div class="clausula-body">
            <p>
                El monto de servicio de publicidad es de
                <strong>S/{{ number_format((float)$contrato->monto_total, 2, '.', ',') }}</strong>
                ({{ $montoLetras }} soles)
            </p>
        </div>
    </div>

    {{-- ══ CLÁUSULA QUINTA ══ --}}
    <div class="clausula">
        <div class="clausula-title">CLÁUSULA QUINTA. - FORMA DE PAGO</div>
        <div class="clausula-body">
            <ul class="oblig-list">
                <li>El monto dinerario especificado es definitivo, y <strong>LA EMPRESA</strong> no tiene derecho bajo
                    ninguna circunstancia a exigir otros montos adicionales.</li>
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

    {{-- ══ CLÁUSULA SEXTA ══ --}}
    <div class="clausula">
        <div class="clausula-title">CLÁUSULA SEXTA. RESOLUCIÓN</div>
        <div class="clausula-body">
            <p>
                El <strong>ARRENDATARIO</strong> podrá resolver el presente contrato antes de su término, comunicando
                por escrito a <strong>"EL CONTRATANTE"</strong> con un plazo de no menor a 10 días calendarios a la
                fecha de vencimiento. En caso de la referida resolución anticipada del contrato, así
                como de su suspensión por cualquier motivo, el <strong>ARRENDATARIO</strong> solo pagará a
                <strong>"EL CONTRATANTE"</strong> por los días y servicios efectivamente prestados.
            </p>
        </div>
    </div>

    {{-- ══ CLÁUSULA SÉPTIMA ══ --}}
    <div class="clausula">
        <div class="clausula-title">CLÁUSULA SÉPTIMA. - OBLIGACIONES DE LAS PARTES</div>
        <div class="clausula-body">
            <p><strong>LA EMPRESA</strong> se obliga a:</p>
            <ul class="oblig-list">
                <li>Ejecutar la prestación, según lo establecido en el presente contrato y en sus partes integrantes.</li>
                <li>A reconocer que es exclusivo responsable de sujetarse a las disposiciones legales
                    vigentes en materia de contenidos publicitarios respecto de la publicidad que se
                    difunda a través de los espacios publicitarios.</li>
                <li>La empresa es exclusivamente responsable por el contenido de la publicidad que
                    se difunda en los espacios publicitarios en virtud del presente contrato.</li>
            </ul>
            <p>
                En caso que no se cumpla con el pago acordado, establecido en la cláusula quinta;
                <strong>LA EMPRESA</strong> queda facultado para desinstalar el módulo de venta del espacio
                publicitario, quedando expedito su derecho a interponer las acciones legales
                correspondientes respecto a los daños y perjuicios por incumplimiento de
                <strong>"EL CONTRATANTE"</strong>
            </p>
        </div>
    </div>

    {{-- ══ CLÁUSULA OCTAVA ══ --}}
    <div class="clausula">
        <div class="clausula-title">CLÁUSULA OCTAVA. - PENALIDADES</div>
        <div class="clausula-body">
            <p>
                En caso de incumplimiento parcial o total a sus obligaciones por parte de <strong>"LA EMPRESA"</strong>
                o del <strong>"EL CONTRATANTE"</strong>, la parte afectada podrá exponer los agravios y perjuicios y
                exigir el pago de la relación civil correspondiente al incumplimiento de la obligación
                pecuniaria y demás daños ocasionados a los intereses de un contrato de servicios
                empresariales, pudiendo así iniciar las acciones legales en la vía arbitral o judicial
                para hacer efectivo el cobro de dichas obligaciones e indemnizaciones.
            </p>
        </div>
    </div>

    {{-- ══ FIRMA ══ --}}
    <div class="firma-section">
        <p class="firma-intro">Las partes firman por duplicado en señal de conformidad.</p>
        <p class="firma-ciudad">{{ $empresa_contrato['ciudad'] }}, {{ $fechaFirmaTexto }}</p>
        <div class="firma-cols">
            {{-- Columna EL CONTRATANTE --}}
            <div class="firma-col">
                <div class="firma-espacio"></div>
                <div class="firma-linea"></div>
                <div class="firma-label">EL CONTRATANTE</div>
                <div class="firma-nombre">{{ strtoupper($contrato->contratante) }}</div>
                @if($contrato->doc_tipo && $contrato->doc_numero)
                <div class="firma-cargo">{{ $contrato->doc_tipo }}: {{ $contrato->doc_numero }}</div>
                @endif
            </div>
            {{-- Columna LA EMPRESA --}}
            <div class="firma-col">
                <div class="firma-espacio"></div>
                <div class="firma-linea"></div>
                <div class="firma-label">LA EMPRESA</div>
                <div class="firma-nombre">{{ $empresa_contrato['representante'] }}</div>
                <div class="firma-cargo">Gerente General</div>
            </div>
        </div>
    </div>

</div>
@if(request('auto') == '1')
<script>
    window.addEventListener('load', function() {
        setTimeout(function() { window.print(); }, 400);
    });
</script>
@endif
</body>
</html>
