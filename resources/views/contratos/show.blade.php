@extends('layouts.app')

@section('title', 'Contrato ' . $contrato->numero_contrato)
@section('subtitle', $contrato->contratante)

@section('content')
<div class="page-header">
    <div class="page-header-left">
        <a href="{{ route('contratos.index') }}" class="back-btn"><i class="bi bi-arrow-left"></i></a>
        <div>
            <div class="page-title">{{ $contrato->numero_contrato }}</div>
            <div class="flex gap-8" style="margin-top:4px">
                @if($contrato->estado === 'activo')
                    <span class="badge badge-success"><i class="bi bi-circle-fill dot"></i>Activo</span>
                @elseif($contrato->estado === 'finalizado')
                    <span class="badge badge-gray">Finalizado</span>
                @else
                    <span class="badge badge-danger">Cancelado</span>
                @endif
                @php
                    $estadoDeuda = $contrato->estado_deuda;
                    $bdColor = match($estadoDeuda) {
                        'Moroso'    => 'danger',
                        'Al día'    => 'success',
                        'Cancelado' => 'gray',
                        default     => 'warning',
                    };
                @endphp
                <span class="badge badge-{{ $bdColor }}">{{ $estadoDeuda }}</span>
            </div>
        </div>
    </div>
    @if(auth()->user()->esAdmin())
    <a href="{{ route('contratos.edit', $contrato) }}" class="btn btn-warning">
        <i class="bi bi-pencil"></i>Editar
    </a>
    @endif
</div>

<div class="row g-3">
    {{-- Columna izquierda: info + cobrar --}}
    <div class="col-lg-4">
        <div class="card" style="margin-bottom:20px">
            <div class="card-header">
                <span><i class="bi bi-info-circle" style="color:var(--primary);margin-right:8px"></i>Información del contrato</span>
            </div>
            <div class="card-body">
                <div class="detail-grid">
                    <div class="detail-row">
                        <div class="detail-label">Tipo</div>
                        <div class="detail-value"><span class="badge badge-info">{{ $contrato->tipo_contrato }}</span></div>
                    </div>
                    <div class="detail-row">
                        <div class="detail-label">Frecuencia</div>
                        <div class="detail-value">{{ ucfirst($contrato->frecuencia_cobro ?? 'mensual') }}</div>
                    </div>
                    <div class="detail-row">
                        <div class="detail-label">Empresa</div>
                        <div class="detail-value">
                            @if($contrato->empresa)
                                <a href="{{ route('empresas.show', $contrato->empresa) }}" style="color:var(--primary);text-decoration:none;font-weight:600">
                                    {{ $contrato->empresa->nombre }}
                                </a>
                            @else <span class="text-muted">—</span> @endif
                        </div>
                    </div>
                    <div class="detail-row">
                        <div class="detail-label">Contratante</div>
                        <div class="detail-value fw-600">{{ $contrato->contratante }}</div>
                    </div>
                    @if($contrato->doc_tipo)
                    <div class="detail-row">
                        <div class="detail-label">Documento</div>
                        <div class="detail-value"><span class="badge badge-gray">{{ $contrato->doc_tipo }}</span> {{ $contrato->doc_numero }}</div>
                    </div>
                    @endif
                    @if($contrato->direccion)
                    <div class="detail-row">
                        <div class="detail-label">Dirección</div>
                        <div class="detail-value">{{ $contrato->direccion }}</div>
                    </div>
                    @endif
                    <div class="detail-row">
                        <div class="detail-label">Inicio</div>
                        <div class="detail-value">{{ $contrato->fecha_inicio?->format('d/m/Y') ?? '—' }}</div>
                    </div>
                    <div class="detail-row">
                        <div class="detail-label">Fin</div>
                        <div class="detail-value">
                            {{ $contrato->fecha_fin?->format('d/m/Y') ?? '—' }}
                            @if($contrato->estado === 'activo' && $contrato->fecha_fin?->isPast())
                                <span class="badge badge-danger" style="margin-left:6px">Vencido</span>
                            @endif
                        </div>
                    </div>
                </div>
                @if($contrato->descripcion)
                    <div style="margin-top:12px;padding-top:12px;border-top:1px solid var(--border);font-size:13px;color:var(--text-light)">
                        {{ $contrato->descripcion }}
                    </div>
                @endif
            </div>
        </div>

        <div class="card" style="margin-bottom:20px">
            <div class="card-header">
                <span><i class="bi bi-wallet2" style="color:var(--primary);margin-right:8px"></i>Resumen financiero</span>
            </div>
            <div class="card-body">
                <div class="detail-grid">
                    <div class="detail-row">
                        <div class="detail-label">Monto total</div>
                        <div class="detail-value fw-700">S/. {{ number_format($contrato->monto_total, 0, ',', '.') }}</div>
                    </div>
                    <div class="detail-row">
                        <div class="detail-label">Adelanto</div>
                        <div class="detail-value text-success fw-600">S/. {{ number_format($contrato->adelanto ?? 0, 0, ',', '.') }}</div>
                    </div>
                    @php $total_cobrado = $contrato->cobros->sum('monto'); @endphp
                    <div class="detail-row">
                        <div class="detail-label">Cobros</div>
                        <div class="detail-value text-success fw-600">S/. {{ number_format($total_cobrado, 0, ',', '.') }}</div>
                    </div>
                    <div class="detail-row" style="border-bottom:none;padding-top:14px">
                        <div class="detail-label" style="font-size:13px;font-weight:700;color:var(--text-dark)">Saldo</div>
                        <div class="detail-value fw-800" style="font-size:18px;color:{{ $contrato->saldo_pendiente > 0 ? 'var(--primary)' : '#059669' }}">
                            S/. {{ number_format($contrato->saldo_pendiente, 0, ',', '.') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @if(auth()->user()->esAdmin() && $contrato->estado === 'activo')
        @php $cuotasExistentes = \App\Models\Cobranza::where('contrato_id', $contrato->id)->count(); @endphp
        <div class="card" style="margin-bottom:20px">
            <div class="card-header">
                <span><i class="bi bi-calendar-check" style="color:#2563EB;margin-right:8px"></i>Generar cuotas de cobro</span>
                @if($cuotasExistentes > 0)
                    <span class="badge badge-info">{{ $cuotasExistentes }} cuota(s) generadas</span>
                @endif
            </div>
            <div class="card-body">
                @if($cuotasExistentes > 0)
                <div style="font-size:13px;color:var(--text-light);margin-bottom:10px">
                    Ya hay cuotas generadas. <a href="{{ route('cobranzas.index') }}" style="color:var(--primary)">Ver en Cobranzas →</a>
                </div>
                @endif
                <form action="{{ route('contratos.generar-cuotas', $contrato) }}" method="POST">
                    @csrf
                    <div class="row g-2">
                        <div class="col-6">
                            <label class="form-label" style="font-size:12px">N° de cuotas</label>
                            <input type="number" name="num_cuotas" class="form-control" min="1" max="120"
                                   placeholder="Ej: 7" required
                                   value="{{ $cuotasExistentes > 0 ? '' : '' }}">
                            @if($contrato->saldo_pendiente > 0)
                            <div style="font-size:11px;color:var(--text-light);margin-top:3px">
                                Saldo: S/. {{ number_format($contrato->saldo_pendiente, 2) }}
                            </div>
                            @endif
                        </div>
                        <div class="col-6">
                            <label class="form-label" style="font-size:12px">Primera fecha venc.</label>
                            <input type="date" name="primera_fecha" class="form-control"
                                   value="{{ $contrato->fecha_inicio?->format('Y-m-d') ?? date('Y-m-d') }}" required>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary btn-sm w-100" style="margin-top:10px"
                            {{ $cuotasExistentes > 0 ? 'onclick="return confirm(\'Ya existen cuotas. ¿Generar cuotas adicionales?\')"' : '' }}>
                        <i class="bi bi-calendar-plus"></i>
                        {{ $cuotasExistentes > 0 ? 'Generar más cuotas' : 'Generar cuotas en Cobranzas' }}
                    </button>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <span><i class="bi bi-cash-coin" style="color:#10B981;margin-right:8px"></i>Registrar cobro</span>
            </div>
            <div class="card-body">
                <form action="{{ route('contratos.cobro', $contrato) }}" method="POST">
                    @csrf
                    <div class="form-group">
                        <label class="form-label">Tipo de cobro <span class="req">*</span></label>
                        <input type="text" name="tipo_cobro" class="form-control"
                            list="tipos_cobro" placeholder="Ej: Cuota, Saldo final..." required>
                        <datalist id="tipos_cobro">
                            <option value="Cuota mensual">
                            <option value="Saldo final">
                            <option value="Pago parcial">
                            <option value="Adelanto">
                        </datalist>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Método de pago</label>
                        <select name="metodo_pago" class="form-select">
                            <option value="">No especificado</option>
                            <option value="efectivo">Efectivo</option>
                            <option value="transferencia">Transferencia</option>
                            <option value="cheque">Cheque</option>
                            <option value="tarjeta">Tarjeta</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Monto (S/.) <span class="req">*</span></label>
                        <input type="number" name="monto" class="form-control" step="1" min="0" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Fecha cobro <span class="req">*</span></label>
                        <input type="date" name="fecha_cobro" class="form-control" value="{{ date('Y-m-d') }}" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Notas</label>
                        <textarea name="notas" class="form-control" rows="2"></textarea>
                    </div>
                    <button type="submit" class="btn btn-success w-100">
                        <i class="bi bi-cash"></i>Registrar cobro
                    </button>
                </form>
            </div>
        </div>
        @endif
    </div>

    {{-- Columna derecha: elementos y cobros --}}
    <div class="col-lg-8">

        {{-- Banner de importar cotización (solo admin, solo si no tiene elementos) --}}
        @php
            $faltaAdelanto = ($contrato->adelanto ?? 0) > 0
                && !$contrato->cobros->where('tipo_cobro','Adelanto')->count();
        @endphp
        @if(auth()->user()->esAdmin() && ($contrato->elementos->count() === 0 || $faltaAdelanto))
        <div style="margin-bottom:16px;padding:14px 18px;background:#FFF7ED;border:1px solid #FED7AA;border-radius:10px;display:flex;align-items:center;gap:14px;flex-wrap:wrap">
            <div style="flex:1;min-width:200px">
                <div style="font-weight:700;font-size:13px;color:#92400E">
                    <i class="bi bi-exclamation-triangle-fill" style="margin-right:6px"></i>
                    Datos incompletos del contrato
                </div>
                <div style="font-size:12px;color:#78350F;margin-top:2px">
                    @if($contrato->elementos->count() === 0)Faltan elementos. @endif
                    @if($faltaAdelanto)El adelanto (S/. {{ number_format($contrato->adelanto,2) }}) no está en el historial de cobros. @endif
                    Selecciona la cotización de origen para importarlos.
                </div>
            </div>
            <form action="{{ route('contratos.importar-cotizacion', $contrato) }}" method="POST"
                  style="display:flex;align-items:center;gap:8px;flex-wrap:wrap">
                @csrf
                @php
                    $cotizacionesConvertidas = \App\Models\Cotizacion::where('estado','convertida')
                        ->orderByDesc('created_at')->get();
                @endphp
                <select name="cotizacion_id" class="form-control form-control-sm" style="min-width:220px" required>
                    <option value="">Seleccionar cotización...</option>
                    @if($contrato->cotizacion_id)
                    <option value="{{ $contrato->cotizacion_id }}" selected>
                        Cotización vinculada ({{ \App\Models\Cotizacion::find($contrato->cotizacion_id)?->numero ?? '#'.$contrato->cotizacion_id }})
                    </option>
                    @endif
                    @foreach($cotizacionesConvertidas as $cot)
                        @if($cot->id !== $contrato->cotizacion_id)
                        <option value="{{ $cot->id }}">{{ $cot->numero }} — {{ $cot->cliente_nombre ?? $cot->cliente_empresa ?? 'Sin nombre' }}</option>
                        @endif
                    @endforeach
                </select>
                <button type="submit"
                        style="background:#EA580C;color:#fff;border:none;padding:7px 16px;border-radius:8px;font-size:13px;font-weight:700;cursor:pointer;white-space:nowrap">
                    <i class="bi bi-download"></i> Importar elementos
                </button>
            </form>
        </div>
        @endif

        <div class="card" style="margin-bottom:20px">
            <div class="card-header">
                <span><i class="bi bi-grid-3x3" style="color:var(--primary);margin-right:8px"></i>Elementos del contrato</span>
                <span class="badge badge-gray">{{ $contrato->elementos->count() }}</span>
            </div>
            <div class="table-wrapper">
                <table>
                    <thead>
                        <tr>
                            <th>Tipo</th>
                            <th>Código / Panel</th>
                            <th>Tiempo (meses)</th>
                            <th>Observaciones</th>
                            <th>Instalación</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($contrato->elementos as $elemento)
                        <tr>
                            <td>
                                <span class="badge badge-{{ $elemento->tipo_elemento === 'digital' ? 'primary' : 'warning' }}">
                                    {{ ucfirst($elemento->tipo_elemento) }}
                                </span>
                            </td>
                            <td class="fw-600">{{ $elemento->codigo }}</td>
                            <td>{{ $elemento->tiempo_contrato }}</td>
                            <td class="text-muted" style="font-size:13px">{{ $elemento->observaciones ?? '—' }}</td>
                            <td>
                                @if($elemento->tipo_elemento === 'tradicional')
                                    @php
                                        $colorInst = match($elemento->estado_instalacion ?? 'pendiente_instalacion') {
                                            'instalado' => 'success',
                                            'retirado'  => 'gray',
                                            default     => 'warning',
                                        };
                                        $labelInst = match($elemento->estado_instalacion ?? 'pendiente_instalacion') {
                                            'instalado' => 'Instalado',
                                            'retirado'  => 'Retirado',
                                            default     => 'Por instalar',
                                        };
                                    @endphp
                                    <span class="badge badge-{{ $colorInst }}">{{ $labelInst }}</span>
                                    @if($elemento->fecha_instalacion)
                                        <div class="text-muted" style="font-size:12px;margin-top:2px">{{ $elemento->fecha_instalacion->format('d/m/Y') }}</div>
                                    @endif
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                        </tr>
                        @if($elemento->tipo_elemento === 'tradicional' && auth()->user()->esAdmin())
                        <tr style="background:var(--bg)">
                            <td colspan="5" style="padding:8px 20px">
                                <form action="{{ route('contratos.elemento.instalacion', [$contrato, $elemento]) }}"
                                      method="POST" style="display:flex;align-items:center;gap:8px;flex-wrap:wrap">
                                    @csrf @method('PATCH')
                                    <select name="estado_instalacion" class="form-select" style="width:auto;padding:6px 30px 6px 10px;font-size:12.5px">
                                        <option value="pendiente_instalacion" {{ ($elemento->estado_instalacion ?? 'pendiente_instalacion') === 'pendiente_instalacion' ? 'selected' : '' }}>Por instalar</option>
                                        <option value="instalado" {{ ($elemento->estado_instalacion ?? '') === 'instalado' ? 'selected' : '' }}>Instalado</option>
                                        <option value="retirado" {{ ($elemento->estado_instalacion ?? '') === 'retirado' ? 'selected' : '' }}>Retirado</option>
                                    </select>
                                    <input type="date" name="fecha_instalacion" class="form-control" style="width:auto;padding:6px 10px;font-size:12.5px"
                                        value="{{ $elemento->fecha_instalacion?->format('Y-m-d') }}" placeholder="Fecha instalación">
                                    <input type="date" name="fecha_retiro" class="form-control" style="width:auto;padding:6px 10px;font-size:12.5px"
                                        value="{{ $elemento->fecha_retiro?->format('Y-m-d') }}" placeholder="Fecha retiro">
                                    <button type="submit" class="btn btn-sm btn-success">
                                        <i class="bi bi-check-lg"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @endif
                        @empty
                        <tr><td colspan="5">
                            <div class="empty-state" style="padding:32px">
                                <i class="bi bi-grid-3x3"></i>
                                <p>Sin elementos registrados</p>
                            </div>
                        </td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <span><i class="bi bi-clock-history" style="color:var(--primary);margin-right:8px"></i>Historial de cobros</span>
                <span class="badge badge-gray">{{ $contrato->cobros->count() }}</span>
            </div>
            <div class="table-wrapper">
                <table>
                    <thead>
                        <tr>
                            <th>Fecha</th>
                            <th>Tipo</th>
                            <th>Método</th>
                            <th>Monto</th>
                            <th>Notas</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($contrato->cobros->sortByDesc('fecha_cobro') as $cobro)
                        <tr>
                            <td>{{ $cobro->fecha_cobro instanceof \Carbon\Carbon ? $cobro->fecha_cobro->format('d/m/Y') : \Carbon\Carbon::parse($cobro->fecha_cobro)->format('d/m/Y') }}</td>
                            <td><span class="badge badge-gray">{{ $cobro->tipo_cobro }}</span></td>
                            <td class="text-muted">{{ ucfirst($cobro->metodo_pago ?? '—') }}</td>
                            <td class="text-success fw-700">S/. {{ number_format($cobro->monto, 0, ',', '.') }}</td>
                            <td class="text-muted" style="font-size:13px">{{ $cobro->notas ?? '—' }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="5">
                            <div class="empty-state" style="padding:32px">
                                <i class="bi bi-clock-history"></i>
                                <p>Sin cobros registrados</p>
                            </div>
                        </td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
