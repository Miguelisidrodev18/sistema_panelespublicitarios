<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contrato {{ $contrato->numero_contrato }}</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: Arial, sans-serif;
            font-size: 11pt;
            color: #1a1a1a;
            background: #fff;
            line-height: 1.6;
        }

        .page {
            width: 210mm;
            min-height: 297mm;
            margin: 0 auto;
            padding: 25mm 25mm 20mm;
            background: #fff;
        }

        /* ── Encabezado ── */
        .header {
            display: flex;
            align-items: center;
            gap: 18px;
            margin-bottom: 18px;
            border-bottom: 3px solid #DC1E2E;
            padding-bottom: 14px;
        }
        .header-logo img { height: 70px; width: auto; }
        .header-logo-placeholder {
            height: 70px; width: 100px;
            background: #DC1E2E; border-radius: 8px;
            display: flex; align-items: center; justify-content: center;
            color: #fff; font-weight: 900; font-size: 22px;
        }
        .header-company {
            flex: 1;
        }
        .header-company-name {
            font-size: 17px; font-weight: 900; color: #1a1a1a; line-height: 1.1;
        }
        .header-company-sub {
            font-size: 9.5px; color: #64748B; text-transform: uppercase;
            letter-spacing: 1.2px; margin-top: 3px;
        }
        .header-company-ruc {
            font-size: 10px; color: #374151; margin-top: 5px;
        }

        /* ── Título del contrato ── */
        .contract-title {
            text-align: center;
            font-size: 13pt;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin: 18px 0 20px;
            padding: 10px 0;
            border-top: 1px solid #E2E8F0;
            border-bottom: 1px solid #E2E8F0;
        }

        /* ── Partes del contrato ── */
        .partes-box {
            background: #F8FAFC;
            border: 1px solid #E2E8F0;
            border-left: 4px solid #DC1E2E;
            border-radius: 4px;
            padding: 12px 16px;
            margin-bottom: 20px;
            font-size: 10.5pt;
        }
        .partes-box p { margin-bottom: 6px; }
        .partes-box p:last-child { margin-bottom: 0; }
        .parte-label { font-weight: 700; color: #DC1E2E; }

        /* ── Cláusulas ── */
        .clausula {
            margin-bottom: 18px;
            page-break-inside: avoid;
        }
        .clausula-title {
            font-weight: 700;
            font-style: italic;
            text-decoration: underline;
            font-size: 11pt;
            text-transform: uppercase;
            margin-bottom: 8px;
            color: #1a1a1a;
        }
        .clausula-body {
            font-size: 10.5pt;
            text-align: justify;
            line-height: 1.7;
        }
        .clausula-body p { margin-bottom: 8px; }
        .clausula-body p:last-child { margin-bottom: 0; }

        /* ── Listas de servicios ── */
        .servicio-item {
            border: 1px solid #E2E8F0;
            border-left: 3px solid #1a1a1a;
            border-radius: 4px;
            padding: 10px 14px;
            margin-bottom: 10px;
            background: #FAFAFA;
        }
        .servicio-tipo {
            font-weight: 700;
            font-size: 10.5pt;
            text-transform: uppercase;
            margin-bottom: 4px;
        }
        .servicio-detalle {
            font-size: 10pt;
            line-height: 1.8;
            padding-left: 14px;
        }
        .servicio-detalle .row { display: flex; gap: 8px; }
        .servicio-detalle .lbl { font-weight: 700; min-width: 130px; }

        /* ── Aclaratorio de vigencia ── */
        .aclaratorio-item {
            margin-bottom: 8px;
            font-size: 10pt;
            padding-left: 14px;
        }
        .aclaratorio-item .ac-name { font-weight: 700; }
        .aclaratorio-item .ac-dates { color: #374151; }

        /* ── Monto ── */
        .monto-box {
            display: inline-block;
            background: #1a1a1a;
            color: #fff;
            padding: 8px 20px;
            border-radius: 6px;
            font-size: 13pt;
            font-weight: 900;
            margin: 8px 0;
        }
        .monto-letras {
            font-size: 10.5pt;
            font-style: italic;
            color: #374151;
        }

        /* ── Datos bancarios ── */
        .banco-table { width: 100%; border-collapse: collapse; margin: 8px 0; }
        .banco-table td { padding: 5px 10px; font-size: 10.5pt; border: 1px solid #E2E8F0; }
        .banco-table .lbl { font-weight: 700; background: #F8FAFC; width: 160px; }

        /* ── Lista de obligaciones ── */
        .obligaciones-list {
            padding-left: 20px;
            margin-top: 6px;
        }
        .obligaciones-list li {
            margin-bottom: 5px;
            font-size: 10.5pt;
            text-align: justify;
        }

        /* ── Firma ── */
        .firma-section {
            margin-top: 40px;
            page-break-inside: avoid;
        }
        .firma-intro {
            font-size: 10.5pt;
            text-align: justify;
            margin-bottom: 16px;
        }
        .firma-ciudad {
            font-size: 10.5pt;
            margin-bottom: 40px;
            font-weight: 600;
        }
        .firma-cols {
            display: flex;
            justify-content: space-around;
            gap: 40px;
        }
        .firma-col {
            flex: 1;
            text-align: center;
        }
        .firma-linea {
            border-top: 1.5px solid #1a1a1a;
            margin-bottom: 8px;
        }
        .firma-nombre { font-weight: 700; font-size: 10.5pt; }
        .firma-empresa { font-size: 10pt; color: #374151; margin-top: 3px; }

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
        .btn-print { background: #DC1E2E; color: #fff; box-shadow: 0 4px 12px rgba(220,30,46,.35); }
        .btn-back  { background: #374151; color: #fff; }
        .btn-action:hover { opacity: .88; }

        @media print {
            .print-btn-bar { display: none; }
            body { margin: 0; }
            .page { margin: 0; padding: 18mm 20mm; width: 100%; min-height: auto; }
            .clausula { page-break-inside: avoid; }
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

        $entero   = (int) $numero;
        $decimal  = round(($numero - $entero) * 100);

        $convertir = function(int $n) use ($unidades, $decenas, $centenas, &$convertir): string {
            if ($n === 0)   return '';
            if ($n === 100) return 'CIEN';
            if ($n < 20)    return $unidades[$n];
            if ($n < 100) {
                $d = intdiv($n, 10); $u = $n % 10;
                return $decenas[$d] . ($u ? ' Y ' . $unidades[$u] : '');
            }
            if ($n < 1000) {
                $c = intdiv($n, 100); $r = $n % 100;
                return $centenas[$c] . ($r ? ' ' . $convertir($r) : '');
            }
            if ($n < 1000000) {
                $m = intdiv($n, 1000); $r = $n % 1000;
                $miles = $m === 1 ? 'MIL' : $convertir($m) . ' MIL';
                return $miles . ($r ? ' ' . $convertir($r) : '');
            }
            return (string) $n;
        };

        $texto = $entero === 0 ? 'CERO' : $convertir($entero);
        return $texto . ' CON ' . str_pad($decimal, 2, '0', STR_PAD_LEFT) . '/100';
    }

    $montoLetras  = montoALetras((float)$contrato->monto_total);
    $anioContrato = $contrato->fecha_inicio?->format('Y') ?? now()->format('Y');
    $duracionMeses = 1;
    if ($contrato->fecha_inicio && $contrato->fecha_fin) {
        $duracionMeses = (int) $contrato->fecha_inicio->diffInMonths($contrato->fecha_fin);
        if ($duracionMeses < 1) $duracionMeses = 1;
    }
    $fechaFirma = now()->locale('es')->isoFormat('D [de] MMMM [del] YYYY');
@endphp

{{-- ── Barra de acciones (solo pantalla) ── --}}
<div class="print-btn-bar">
    <a href="{{ route('contratos.show', $contrato) }}" class="btn-action btn-back">&#8592; Volver</a>
    <button class="btn-action btn-print" onclick="window.print()">&#128424; Imprimir / PDF</button>
</div>

<div class="page">

    {{-- ══════════════════ ENCABEZADO ══════════════════ --}}
    <div class="header">
        <div class="header-logo">
            <img src="{{ asset('images/logo.png') }}" alt="Logo"
                 onerror="this.style.display='none';this.nextElementSibling.style.display='flex'">
            <div class="header-logo-placeholder" style="display:none">PP</div>
        </div>
        <div class="header-company">
            <div class="header-company-name">{{ $empresa_contrato['nombre'] }}</div>
            <div class="header-company-sub">Servicios Publicitarios</div>
            <div class="header-company-ruc">
                RUC: {{ $empresa_contrato['ruc'] }} &nbsp;|&nbsp; {{ $empresa_contrato['domicilio'] }}
            </div>
        </div>
    </div>

    {{-- ══════════════════ TÍTULO ══════════════════ --}}
    <div class="contract-title">
        CONTRATO N.º {{ $contrato->numero_contrato }} – PPSAC/{{ $anioContrato }}<br>
        DE SERVICIOS PUBLICITARIOS
    </div>

    {{-- ══════════════════ IDENTIFICACIÓN DE PARTES ══════════════════ --}}
    <div class="partes-box">
        <p>
            <span class="parte-label">"LA EMPRESA":</span>
            {{ $empresa_contrato['nombre'] }}, con RUC N.º {{ $empresa_contrato['ruc'] }},
            con domicilio en {{ $empresa_contrato['domicilio'] }},
            representada por {{ $empresa_contrato['representante'] }},
            identificado(a) con DNI N.º {{ $empresa_contrato['dni_representante'] }},
            en su calidad de Gerente General.
        </p>
        <p>
            <span class="parte-label">"EL CONTRATANTE":</span>
            {{ $contrato->contratante }}@if($contrato->doc_tipo && $contrato->doc_numero),
            con {{ $contrato->doc_tipo }} N.º {{ $contrato->doc_numero }}@endif@if($contrato->direccion),
            con domicilio en {{ $contrato->direccion }}@endif.
        </p>
        <p style="margin-top:8px;font-size:10pt;color:#374151">
            Ambas partes acuerdan celebrar el presente Contrato de Servicios Publicitarios,
            bajo las siguientes cláusulas y condiciones:
        </p>
    </div>

    {{-- ══════════════════ CLÁUSULA PRIMERA ══════════════════ --}}
    <div class="clausula">
        <div class="clausula-title">Cláusula Primera – Antecedentes</div>
        <div class="clausula-body">
            <p>
                "LA EMPRESA" es una persona jurídica cuyo objeto principal es brindar servicio
                de publicidad visual en pantalla LED, paneles publicitarios y otros servicios
                afines a favor de sus clientes. En el presente contrato, presta sus servicios
                a favor de "EL CONTRATANTE" <strong>{{ strtoupper($contrato->contratante) }}</strong>.
            </p>
            @if($contrato->descripcion)
            <p>
                <em>Antecedentes adicionales:</em> {{ $contrato->descripcion }}
            </p>
            @endif
        </div>
    </div>

    {{-- ══════════════════ CLÁUSULA SEGUNDA ══════════════════ --}}
    <div class="clausula">
        <div class="clausula-title">Cláusula Segunda – Objeto</div>
        <div class="clausula-body">
            <p>
                "LA EMPRESA" se obliga a prestar a "EL CONTRATANTE"
                <strong>{{ strtoupper($contrato->contratante) }}</strong>
                el siguiente servicio de Publicidad Exterior:
            </p>

            @forelse($contrato->elementos as $elem)
            @php
                $panel     = $elem->panel ?? null;
                $ubicacion = $panel ? ($panel->direccion ?? $panel->nombre ?? $elem->codigo) : $elem->codigo;
                $medidas   = $panel ? ($panel->medidas ?? null) : null;
                $tipoLabel = match($elem->tipo_elemento) {
                    'digital'     => 'PANTALLA LED',
                    'tradicional' => 'PANEL TRADICIONAL',
                    default       => strtoupper($elem->tipo_elemento),
                };
            @endphp
            <div class="servicio-item">
                <div class="servicio-tipo">
                    Servicio de Publicidad Exterior "{{ $empresa_contrato['nombre'] }}"
                </div>
                <div class="servicio-detalle">
                    <div class="row">
                        <span class="lbl">– {{ $tipoLabel }}:</span>
                        <span>Código <strong>{{ $elem->codigo }}</strong></span>
                    </div>
                    @if($ubicacion && $ubicacion !== $elem->codigo)
                    <div class="row">
                        <span class="lbl">&nbsp;&nbsp;Ubicado:</span>
                        <span>{{ $ubicacion }}</span>
                    </div>
                    @endif
                    @if($medidas)
                    <div class="row">
                        <span class="lbl">&nbsp;&nbsp;Medidas:</span>
                        <span>{{ $medidas }}</span>
                    </div>
                    @endif
                    @if($elem->tiempo_contrato)
                    <div class="row">
                        <span class="lbl">&nbsp;&nbsp;Tiempo:</span>
                        <span>{{ $elem->tiempo_contrato }} mes{{ $elem->tiempo_contrato > 1 ? 'es' : '' }}</span>
                    </div>
                    @endif
                    @if($elem->observaciones)
                    <div class="row">
                        <span class="lbl">&nbsp;&nbsp;Observaciones:</span>
                        <span>{{ $elem->observaciones }}</span>
                    </div>
                    @endif
                </div>
            </div>
            @empty
            <p><em>Sin elementos registrados en el contrato.</em></p>
            @endforelse
        </div>
    </div>

    {{-- ══════════════════ CLÁUSULA TERCERA ══════════════════ --}}
    <div class="clausula">
        <div class="clausula-title">Cláusula Tercera – Vigencia del Contrato</div>
        <div class="clausula-body">
            <p>
                El presente contrato tendrá una duración de
                <strong>{{ $duracionMeses }} mes{{ $duracionMeses > 1 ? 'es' : '' }}</strong>,
                a partir del <strong>{{ $contrato->fecha_inicio?->format('d/m/Y') ?? '—' }}</strong>
                hasta el <strong>{{ $contrato->fecha_fin?->format('d/m/Y') ?? '—' }}</strong>.
                El usuario podrá elegir en qué meses ingresará a una de las pantallas.
            </p>

            @php
                $elementosConFechas = $contrato->elementos->filter(fn($e) =>
                    $e->fecha_instalacion || $e->fecha_retiro
                );
            @endphp
            @if($elementosConFechas->count() > 0)
            <p><strong>ACLARATORIO:</strong></p>
            @foreach($elementosConFechas as $elem)
            @php
                $panel     = $elem->panel ?? null;
                $ubicacion = $panel ? ($panel->nombre ?? $panel->direccion ?? $elem->codigo) : $elem->codigo;
                $tipoLabel = match($elem->tipo_elemento) {
                    'digital'     => 'PANTALLA LED',
                    'tradicional' => 'PANEL TRADICIONAL',
                    default       => strtoupper($elem->tipo_elemento),
                };
            @endphp
            <div class="aclaratorio-item">
                <div class="ac-name">– {{ $tipoLabel }} – {{ $ubicacion }}</div>
                <div class="ac-dates">
                    &nbsp;&nbsp;FECHA INICIO:
                    <strong>{{ $elem->fecha_instalacion?->format('d/m/Y') ?? $contrato->fecha_inicio?->format('d/m/Y') ?? '—' }}</strong>
                    &nbsp;&nbsp;&nbsp;&nbsp;FECHA FINAL:
                    <strong>{{ $elem->fecha_retiro?->format('d/m/Y') ?? $contrato->fecha_fin?->format('d/m/Y') ?? '—' }}</strong>
                </div>
            </div>
            @endforeach
            @endif
        </div>
    </div>

    {{-- ══════════════════ CLÁUSULA CUARTA ══════════════════ --}}
    <div class="clausula">
        <div class="clausula-title">Cláusula Cuarta – Monto Contractual</div>
        <div class="clausula-body">
            <p>
                El monto de servicio de publicidad pactado entre las partes es de:
            </p>
            <div style="text-align:center;margin:10px 0">
                <div class="monto-box">
                    S/ {{ number_format((float)$contrato->monto_total, 2, '.', ',') }}
                </div>
                <div class="monto-letras">
                    ({{ $montoLetras }} SOLES)
                </div>
            </div>
            @if(($contrato->adelanto ?? 0) > 0)
            <p>
                Se pactó un adelanto de <strong>S/ {{ number_format((float)$contrato->adelanto, 2, '.', ',') }}</strong>
                al inicio del contrato, quedando un saldo de
                <strong>S/ {{ number_format((float)$contrato->saldo_pendiente, 2, '.', ',') }}</strong>.
            </p>
            @endif
        </div>
    </div>

    {{-- ══════════════════ CLÁUSULA QUINTA ══════════════════ --}}
    <div class="clausula">
        <div class="clausula-title">Cláusula Quinta – Forma de Pago</div>
        <div class="clausula-body">
            <p>
                El monto dinerario especificado es definitivo, y "LA EMPRESA" no tiene derecho,
                bajo ninguna circunstancia, a exigir otros montos adicionales. Los precios
                incluyen derechos de uso de espacio publicitario, en caso corresponda.
            </p>
            <p>El monto será abonado a la Cuenta del Banco {{ $empresa_contrato['banco'] }}:</p>
            <table class="banco-table">
                <tr>
                    <td class="lbl">Cuenta</td>
                    <td>{{ $empresa_contrato['cta'] }}</td>
                </tr>
                <tr>
                    <td class="lbl">CCI</td>
                    <td>{{ $empresa_contrato['cci'] }}</td>
                </tr>
                <tr>
                    <td class="lbl">Cta. Detracción</td>
                    <td>{{ $empresa_contrato['cta_detraccion'] }}</td>
                </tr>
            </table>
        </div>
    </div>

    {{-- ══════════════════ CLÁUSULA SEXTA ══════════════════ --}}
    <div class="clausula">
        <div class="clausula-title">Cláusula Sexta – Resolución</div>
        <div class="clausula-body">
            <p>
                Cualquiera de las partes podrá resolver el presente contrato, sin expresión de causa,
                mediante comunicación escrita con un mínimo de <strong>15 días calendario</strong> de
                anticipación. En caso de resolución anticipada imputable a "EL CONTRATANTE",
                el adelanto abonado no será devuelto, salvo acuerdo expreso entre las partes.
            </p>
            <p>
                En caso de resolución por incumplimiento de pago, "LA EMPRESA" quedará facultada
                para proceder al retiro inmediato del material publicitario instalado, sin responsabilidad
                alguna frente a "EL CONTRATANTE".
            </p>
        </div>
    </div>

    {{-- ══════════════════ CLÁUSULA SÉPTIMA ══════════════════ --}}
    <div class="clausula">
        <div class="clausula-title">Cláusula Séptima – Obligaciones de las Partes</div>
        <div class="clausula-body">
            <p><strong>Obligaciones de "LA EMPRESA":</strong></p>
            <ul class="obligaciones-list">
                <li>Brindar el servicio publicitario en los términos y condiciones pactados.</li>
                <li>Mantener operativos los equipos y espacios publicitarios contratados durante
                    la vigencia del contrato.</li>
                <li>Notificar oportunamente cualquier inconveniente técnico que afecte la prestación
                    del servicio.</li>
                <li>Emitir los comprobantes de pago correspondientes.</li>
            </ul>
            <p style="margin-top:10px"><strong>Obligaciones de "EL CONTRATANTE":</strong></p>
            <ul class="obligaciones-list">
                <li>Abonar puntualmente el monto pactado en las fechas acordadas.</li>
                <li>Proporcionar el material publicitario (artes, archivos) con un mínimo de
                    <strong>3 días hábiles</strong> de anticipación a la fecha de inicio del servicio.</li>
                <li>El material deberá cumplir con los requisitos técnicos indicados por "LA EMPRESA".</li>
                <li>En caso de incumplimiento de pago, "LA EMPRESA" procederá a la desinstalación
                    del material publicitario, sin responsabilidad frente a "EL CONTRATANTE".</li>
            </ul>
        </div>
    </div>

    {{-- ══════════════════ CLÁUSULA OCTAVA ══════════════════ --}}
    <div class="clausula">
        <div class="clausula-title">Cláusula Octava – Penalidades</div>
        <div class="clausula-body">
            <p>
                En caso de mora en el pago por parte de "EL CONTRATANTE", se aplicará un interés
                moratorio equivalente al <strong>2% mensual</strong> sobre el monto adeudado por
                cada mes de retraso.
            </p>
            <p>
                Si "EL CONTRATANTE" incumpliera con la entrega del material publicitario en los
                plazos establecidos, y como consecuencia de ello el servicio no pudiera prestarse
                en la fecha acordada, dicho período se computará igualmente como parte del tiempo
                contratado, sin derecho a reclamo ni compensación.
            </p>
            <p>
                Cualquier controversia derivada del presente contrato será resuelta por las partes
                de manera directa y amigable. De no llegarse a un acuerdo, ambas partes se someten
                a la jurisdicción de los Juzgados y Tribunales de <strong>{{ $empresa_contrato['ciudad'] }}</strong>.
            </p>
        </div>
    </div>

    {{-- ══════════════════ PIE DE FIRMA ══════════════════ --}}
    <div class="firma-section">
        <div class="firma-intro">
            Las partes firman el presente contrato en señal de conformidad con todas y cada una
            de las cláusulas precedentes, en dos (2) ejemplares de igual tenor y valor, en la
            ciudad de {{ $empresa_contrato['ciudad'] }}.
        </div>
        <div class="firma-ciudad">
            {{ $empresa_contrato['ciudad'] }}, {{ $fechaFirma }}
        </div>
        <div class="firma-cols">
            <div class="firma-col">
                <div style="height:50px"></div>
                <div class="firma-linea"></div>
                <div class="firma-nombre">LA EMPRESA</div>
                <div class="firma-empresa">{{ $empresa_contrato['nombre'] }}</div>
                <div class="firma-empresa">{{ $empresa_contrato['representante'] }}</div>
                <div class="firma-empresa" style="font-size:9.5pt;color:#64748B">
                    DNI: {{ $empresa_contrato['dni_representante'] }}
                </div>
            </div>
            <div class="firma-col">
                <div style="height:50px"></div>
                <div class="firma-linea"></div>
                <div class="firma-nombre">EL CONTRATANTE</div>
                <div class="firma-empresa">{{ strtoupper($contrato->contratante) }}</div>
                @if($contrato->doc_tipo && $contrato->doc_numero)
                <div class="firma-empresa" style="font-size:9.5pt;color:#64748B">
                    {{ $contrato->doc_tipo }}: {{ $contrato->doc_numero }}
                </div>
                @endif
            </div>
        </div>
    </div>

</div>

</body>
</html>
