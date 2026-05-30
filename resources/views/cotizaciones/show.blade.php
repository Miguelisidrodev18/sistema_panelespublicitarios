@extends('layouts.app')

@section('title', 'Cotización ' . ($cotizacion->numero ?? $cotizacion->id))
@section('subtitle', 'Detalle de la cotización')

@section('content')
<div class="page-header">
    <div class="page-header-left">
        <a href="{{ route('cotizaciones.index') }}" class="back-btn"><i class="bi bi-arrow-left"></i></a>
        <div>
            <div class="page-title">{{ $cotizacion->numero ?? 'Cotización #' . $cotizacion->id }}</div>
            <div style="margin-top:4px">
                @php $estadoMap = ['pendiente'=>['warning','Pendiente'],'aprobada'=>['success','Aprobada'],'rechazada'=>['danger','Rechazada'],'convertida'=>['primary','Convertida']]; @endphp
                @php [$bc, $bl] = $estadoMap[$cotizacion->estado] ?? ['gray', ucfirst($cotizacion->estado)]; @endphp
                <span class="badge badge-{{ $bc }}">{{ $bl }}</span>
            </div>
        </div>
    </div>
    <div class="flex gap-8">
        <a href="{{ route('cotizaciones.imprimir', $cotizacion) }}" target="_blank" class="btn btn-secondary"><i class="bi bi-printer"></i>Imprimir</a>
        <a href="{{ route('cotizaciones.imprimir-carta', $cotizacion) }}" target="_blank" class="btn btn-secondary" title="Imprimir formato carta de propuesta"><i class="bi bi-envelope-paper"></i>Carta</a>
        @if(auth()->user()->esAdmin())
        @if(in_array($cotizacion->estado, ['pendiente', 'aprobada']))
        <a href="{{ route('cotizaciones.convertir', $cotizacion) }}" class="btn btn-success"><i class="bi bi-arrow-right-circle"></i>Convertir a Contrato</a>
        @endif
        <a href="{{ route('cotizaciones.edit', $cotizacion) }}" class="btn btn-warning"><i class="bi bi-pencil"></i>Editar</a>
        @endif
    </div>
</div>

<div class="row g-3">
    <div class="col-lg-4">
        <div class="card" style="margin-bottom:20px">
            <div class="card-header ch-blue"><span><i class="bi bi-person"></i>Datos del cliente</span></div>
            <div class="card-body">
                <div class="detail-grid">
                    @if($cotizacion->empresa)
                    <div class="detail-row"><div class="detail-label">Empresa</div><div class="detail-value">
                        <a href="{{ route('empresas.show', $cotizacion->empresa) }}" style="color:var(--primary);text-decoration:none;font-weight:600">
                            <i class="bi bi-building" style="margin-right:4px"></i>{{ $cotizacion->empresa->nombre }}
                        </a>
                    </div></div>
                    @endif
                    @if($cotizacion->cliente_nombre)
                    <div class="detail-row"><div class="detail-label">Contacto</div><div class="detail-value fw-600">{{ $cotizacion->cliente_nombre }}</div></div>
                    @endif
                    @if($cotizacion->cliente_empresa && !$cotizacion->empresa)
                    <div class="detail-row"><div class="detail-label">Empresa</div><div class="detail-value">{{ $cotizacion->cliente_empresa }}</div></div>
                    @endif
                    @if($cotizacion->cliente_telefono)
                    <div class="detail-row"><div class="detail-label">Teléfono</div><div class="detail-value"><a href="tel:{{ $cotizacion->cliente_telefono }}" style="color:var(--text-dark);text-decoration:none">{{ $cotizacion->cliente_telefono }}</a></div></div>
                    @endif
                    @if($cotizacion->cliente_email)
                    <div class="detail-row"><div class="detail-label">Email</div><div class="detail-value"><a href="mailto:{{ $cotizacion->cliente_email }}" style="color:var(--primary);text-decoration:none">{{ $cotizacion->cliente_email }}</a></div></div>
                    @endif
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header ch-purple"><span><i class="bi bi-receipt"></i>Propuesta</span></div>
            <div class="card-body">
                <div class="detail-grid">
                    <div class="detail-row"><div class="detail-label">Tipo</div><div class="detail-value"><span class="badge badge-info">{{ $cotizacion->tipo_contrato ?? '—' }}</span></div></div>
                    <div class="detail-row"><div class="detail-label">Monto</div><div class="detail-value fw-800" style="font-size:18px;color:#059669">S/. {{ number_format($cotizacion->monto_propuesto, 0, ',', '.') }}</div></div>
                    <div class="detail-row"><div class="detail-label">Fecha</div><div class="detail-value">{{ $cotizacion->fecha_cotizacion?->format('d/m/Y') ?? '—' }}</div></div>
                    <div class="detail-row"><div class="detail-label">Vence</div><div class="detail-value">
                        {{ $cotizacion->fecha_vencimiento?->format('d/m/Y') ?? '—' }}
                        @if($cotizacion->estado === 'pendiente' && $cotizacion->fecha_vencimiento?->isPast())
                            <span class="badge badge-danger" style="margin-left:4px">Vencida</span>
                        @endif
                    </div></div>
                </div>
                @if($cotizacion->notas)
                    <div style="margin-top:12px;padding-top:10px;border-top:1px solid var(--border);font-size:13px;color:var(--text-light)">{{ $cotizacion->notas }}</div>
                @endif
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        @if($cotizacion->estado === 'convertida')
        <div class="card card-accent" style="border-color:#10B981;margin-bottom:20px">
            <div class="card-body" style="background:#ECFDF5;display:flex;align-items:center;gap:10px">
                <i class="bi bi-check-circle-fill" style="font-size:20px;color:#10B981"></i>
                <div style="font-size:13.5px;color:#065F46;font-weight:500">Esta cotización ya fue <strong>convertida a contrato</strong>.</div>
            </div>
        </div>
        @endif

        <div class="card">
            <div class="card-header ch-amber">
                <span><i class="bi bi-grid-3x3"></i>Paneles / Servicios cotizados</span>
                <span class="badge badge-gray">{{ $cotizacion->elementos->count() }}</span>
            </div>
            <div class="table-wrapper">
                <table>
                    <thead>
                        <tr>
                            <th>Tipo</th>
                            <th>Código / Servicio</th>
                            <th>Meses</th>
                            <th>Precio</th>
                            <th>Costo Prod.</th>
                            <th>Desc. Costo</th>
                            <th>Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $sumaTotal = 0; @endphp
                        @forelse($cotizacion->elementos as $elem)
                        @php
                            $precio = (float)$elem->precio_unitario;
                            $costo  = (float)($elem->costo_produccion ?? 0);
                            $linea  = $precio + $costo;
                            $sumaTotal += $linea;
                            $badgeColor = match($elem->tipo_elemento) {
                                'digital'    => 'primary',
                                'tradicional'=> 'warning',
                                default      => 'info',
                            };
                            $subtipo = $elem->subtipo ? strtoupper($elem->subtipo) : null;
                        @endphp
                        <tr>
                            <td>
                                <span class="badge badge-{{ $badgeColor }}">{{ ucfirst($elem->tipo_elemento) }}</span>
                                @if($subtipo)<span class="badge badge-gray" style="margin-left:3px;font-size:10px">{{ $subtipo }}</span>@endif
                            </td>
                            <td class="fw-600">{{ $elem->codigo }}</td>
                            <td>{{ $elem->tiempo_contrato ?? '—' }}</td>
                            <td class="fw-600">S/. {{ number_format($precio, 2, '.', ',') }}</td>
                            <td>{{ $costo > 0 ? 'S/. '.number_format($costo,2,'.',',') : '—' }}</td>
                            <td class="text-muted" style="font-size:12px">{{ $elem->desc_costo ?? ($elem->observaciones ?? '—') }}</td>
                            <td class="fw-700" style="color:#059669">S/. {{ number_format($linea, 2, '.', ',') }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="7"><div class="empty-state" style="padding:32px"><i class="bi bi-grid-3x3"></i><p>Sin elementos detallados</p></div></td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($cotizacion->elementos->count() > 0)
            @php
                $igv   = $sumaTotal * 0.18;
                $total = $sumaTotal + $igv;
            @endphp
            <div style="display:flex;justify-content:flex-end;padding:16px 20px;border-top:1px solid var(--border);gap:32px">
                <div style="font-size:13px;color:var(--text-light)">Subtotal neto<br><strong style="color:var(--text-dark);font-size:15px">S/. {{ number_format($sumaTotal, 2, '.', ',') }}</strong></div>
                <div style="font-size:13px;color:var(--text-light)">IGV (18%)<br><strong style="color:var(--text-dark);font-size:15px">S/. {{ number_format($igv, 2, '.', ',') }}</strong></div>
                <div style="font-size:13px;color:var(--text-light)">TOTAL CON IGV<br><strong style="color:#059669;font-size:18px;font-weight:800">S/. {{ number_format($total, 2, '.', ',') }}</strong></div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
