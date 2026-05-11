@extends('layouts.app')

@section('title', 'Contrato ' . $contrato->numero_contrato)

@section('content')
<div class="d-flex align-items-center justify-content-between mb-3">
    <div class="d-flex align-items-center gap-2">
        <a href="{{ route('contratos.index') }}" class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-arrow-left"></i>
        </a>
        <h5 class="mb-0 fw-semibold">Contrato {{ $contrato->numero_contrato }}</h5>
        @if($contrato->estado === 'activo')
            <span class="badge bg-success">Activo</span>
        @elseif($contrato->estado === 'finalizado')
            <span class="badge bg-secondary">Finalizado</span>
        @else
            <span class="badge bg-danger">Cancelado</span>
        @endif
        @php
            $estadoDeuda = $contrato->estado_deuda;
            $bdColor = match($estadoDeuda) {
                'Moroso'    => 'danger',
                'Al día'    => 'success',
                'Cancelado' => 'secondary',
                default     => 'warning text-dark',
            };
        @endphp
        <span class="badge bg-{{ $bdColor }}">{{ $estadoDeuda }}</span>
    </div>
    @if(auth()->user()->esAdmin())
    <a href="{{ route('contratos.edit', $contrato) }}" class="btn btn-warning">
        <i class="bi bi-pencil me-1"></i>Editar
    </a>
    @endif
</div>

<div class="row g-3">
    {{-- Columna izquierda: info + cobrar --}}
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header bg-white fw-medium py-3">Información del contrato</div>
            <div class="card-body">
                <table class="table table-sm table-borderless mb-0">
                    <tr><th class="text-muted small">Tipo</th><td>{{ $contrato->tipo_contrato }}</td></tr>
                    <tr><th class="text-muted small">Frecuencia</th><td>{{ ucfirst($contrato->frecuencia_cobro ?? 'mensual') }}</td></tr>
                    <tr><th class="text-muted small">Empresa</th>
                        <td>
                            @if($contrato->empresa)
                                <a href="{{ route('empresas.show', $contrato->empresa) }}" class="text-decoration-none">
                                    {{ $contrato->empresa->nombre }}
                                </a>
                            @else <span class="text-muted">-</span> @endif
                        </td>
                    </tr>
                    <tr><th class="text-muted small">Contratante</th><td>{{ $contrato->contratante }}</td></tr>
                    @if($contrato->doc_tipo)
                    <tr><th class="text-muted small">Documento</th>
                        <td>{{ $contrato->doc_tipo }}: {{ $contrato->doc_numero }}</td>
                    </tr>
                    @endif
                    @if($contrato->direccion)
                    <tr><th class="text-muted small">Dirección</th><td>{{ $contrato->direccion }}</td></tr>
                    @endif
                    <tr><th class="text-muted small">Inicio</th>
                        <td>{{ $contrato->fecha_inicio?->format('d/m/Y') ?? '-' }}</td>
                    </tr>
                    <tr><th class="text-muted small">Fin</th>
                        <td>
                            {{ $contrato->fecha_fin?->format('d/m/Y') ?? '-' }}
                            @if($contrato->estado === 'activo' && $contrato->fecha_fin?->isPast())
                                <span class="badge bg-danger ms-1">Vencido</span>
                            @endif
                        </td>
                    </tr>
                </table>
                @if($contrato->descripcion)
                    <div class="mt-2 small text-muted border-top pt-2">{{ $contrato->descripcion }}</div>
                @endif
            </div>
        </div>

        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header bg-white fw-medium py-3">Resumen financiero</div>
            <div class="card-body">
                <div class="d-flex justify-content-between py-1 border-bottom">
                    <span class="text-muted small">Monto total</span>
                    <span class="fw-medium">S/. {{ number_format($contrato->monto_total, 0, ',', '.') }}</span>
                </div>
                <div class="d-flex justify-content-between py-1 border-bottom">
                    <span class="text-muted small">Adelanto</span>
                    <span class="text-success fw-medium">S/. {{ number_format($contrato->adelanto ?? 0, 0, ',', '.') }}</span>
                </div>
                @php $total_cobrado = $contrato->cobros->sum('monto'); @endphp
                <div class="d-flex justify-content-between py-1 border-bottom">
                    <span class="text-muted small">Cobros registrados</span>
                    <span class="text-success fw-medium">S/. {{ number_format($total_cobrado, 0, ',', '.') }}</span>
                </div>
                <div class="d-flex justify-content-between py-1 mt-1">
                    <span class="fw-semibold">Saldo pendiente</span>
                    <span class="fw-bold {{ $contrato->saldo_pendiente > 0 ? 'text-danger' : 'text-success' }}">
                        S/. {{ number_format($contrato->saldo_pendiente, 0, ',', '.') }}
                    </span>
                </div>
            </div>
        </div>

        @if(auth()->user()->esAdmin() && $contrato->estado === 'activo')
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white fw-medium py-3">Registrar cobro</div>
            <div class="card-body">
                <form action="{{ route('contratos.cobro', $contrato) }}" method="POST">
                    @csrf
                    <div class="mb-2">
                        <label class="form-label">Tipo de cobro <span class="text-danger">*</span></label>
                        <input type="text" name="tipo_cobro" class="form-control"
                            list="tipos_cobro" placeholder="Ej: Cuota, Saldo final..." required>
                        <datalist id="tipos_cobro">
                            <option value="Cuota mensual">
                            <option value="Saldo final">
                            <option value="Pago parcial">
                            <option value="Adelanto">
                        </datalist>
                    </div>
                    <div class="mb-2">
                        <label class="form-label">Método de pago</label>
                        <select name="metodo_pago" class="form-select">
                            <option value="">No especificado</option>
                            <option value="efectivo">Efectivo</option>
                            <option value="transferencia">Transferencia</option>
                            <option value="cheque">Cheque</option>
                            <option value="tarjeta">Tarjeta</option>
                        </select>
                    </div>
                    <div class="mb-2">
                        <label class="form-label">Monto (S/.) <span class="text-danger">*</span></label>
                        <input type="number" name="monto" class="form-control" step="1" min="0" required>
                    </div>
                    <div class="mb-2">
                        <label class="form-label">Fecha cobro <span class="text-danger">*</span></label>
                        <input type="date" name="fecha_cobro" class="form-control" value="{{ date('Y-m-d') }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Notas</label>
                        <textarea name="notas" class="form-control" rows="2"></textarea>
                    </div>
                    <button type="submit" class="btn btn-success w-100">
                        <i class="bi bi-cash me-1"></i>Registrar cobro
                    </button>
                </form>
            </div>
        </div>
        @endif
    </div>

    {{-- Columna derecha: elementos y cobros --}}
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header bg-white fw-medium py-3 d-flex justify-content-between align-items-center">
                <span>Elementos del contrato</span>
                <span class="badge bg-secondary">{{ $contrato->elementos->count() }}</span>
            </div>
            <div class="table-responsive">
                <table class="table table-sm table-hover align-middle mb-0">
                    <thead class="table-light">
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
                                <span class="badge bg-{{ $elemento->tipo_elemento === 'digital' ? 'primary' : 'warning text-dark' }}">
                                    {{ ucfirst($elemento->tipo_elemento) }}
                                </span>
                            </td>
                            <td class="fw-medium">{{ $elemento->codigo }}</td>
                            <td>{{ $elemento->tiempo_contrato }}</td>
                            <td class="text-muted small">{{ $elemento->observaciones ?? '-' }}</td>
                            <td>
                                @if($elemento->tipo_elemento === 'tradicional')
                                    @php
                                        $colorInst = match($elemento->estado_instalacion ?? 'pendiente_instalacion') {
                                            'instalado' => 'success',
                                            'retirado'  => 'secondary',
                                            default     => 'warning text-dark',
                                        };
                                        $labelInst = match($elemento->estado_instalacion ?? 'pendiente_instalacion') {
                                            'instalado' => 'Instalado',
                                            'retirado'  => 'Retirado',
                                            default     => 'Por instalar',
                                        };
                                    @endphp
                                    <span class="badge bg-{{ $colorInst }}">{{ $labelInst }}</span>
                                    @if($elemento->fecha_instalacion)
                                        <div class="small text-muted">{{ $elemento->fecha_instalacion->format('d/m/Y') }}</div>
                                    @endif
                                @else
                                    <span class="text-muted small">—</span>
                                @endif
                            </td>
                        </tr>
                        @if($elemento->tipo_elemento === 'tradicional' && auth()->user()->esAdmin())
                        <tr class="table-light">
                            <td colspan="5" class="py-1 ps-4">
                                <form action="{{ route('contratos.elemento.instalacion', [$contrato, $elemento]) }}"
                                      method="POST" class="d-flex align-items-center gap-2">
                                    @csrf @method('PATCH')
                                    <select name="estado_instalacion" class="form-select form-select-sm" style="width:auto">
                                        <option value="pendiente_instalacion" {{ ($elemento->estado_instalacion ?? 'pendiente_instalacion') === 'pendiente_instalacion' ? 'selected' : '' }}>Por instalar</option>
                                        <option value="instalado" {{ ($elemento->estado_instalacion ?? '') === 'instalado' ? 'selected' : '' }}>Instalado</option>
                                        <option value="retirado" {{ ($elemento->estado_instalacion ?? '') === 'retirado' ? 'selected' : '' }}>Retirado</option>
                                    </select>
                                    <input type="date" name="fecha_instalacion" class="form-control form-control-sm" style="width:auto"
                                        value="{{ $elemento->fecha_instalacion?->format('Y-m-d') }}" placeholder="Fecha instalación">
                                    <input type="date" name="fecha_retiro" class="form-control form-control-sm" style="width:auto"
                                        value="{{ $elemento->fecha_retiro?->format('Y-m-d') }}" placeholder="Fecha retiro">
                                    <button type="submit" class="btn btn-sm btn-success">
                                        <i class="bi bi-check-lg"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @endif
                        @empty
                        <tr><td colspan="5" class="text-center text-muted py-3">Sin elementos registrados</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white fw-medium py-3 d-flex justify-content-between align-items-center">
                <span>Historial de cobros</span>
                <span class="badge bg-secondary">{{ $contrato->cobros->count() }}</span>
            </div>
            <div class="table-responsive">
                <table class="table table-sm table-hover align-middle mb-0">
                    <thead class="table-light">
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
                            <td>{{ $cobro->tipo_cobro }}</td>
                            <td class="text-muted small">{{ ucfirst($cobro->metodo_pago ?? '-') }}</td>
                            <td class="text-success fw-medium">S/. {{ number_format($cobro->monto, 0, ',', '.') }}</td>
                            <td class="text-muted small">{{ $cobro->notas ?? '-' }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="5" class="text-center text-muted py-3">Sin cobros registrados</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
