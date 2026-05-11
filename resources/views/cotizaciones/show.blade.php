@extends('layouts.app')

@section('title', 'Cotización ' . ($cotizacion->numero ?? $cotizacion->id))

@section('content')
<div class="d-flex align-items-center justify-content-between mb-3">
    <div class="d-flex align-items-center gap-2">
        <a href="{{ route('cotizaciones.index') }}" class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-arrow-left"></i>
        </a>
        <h5 class="mb-0 fw-semibold">{{ $cotizacion->numero ?? 'Cotización #' . $cotizacion->id }}</h5>
        @php $estadoColors = ['pendiente'=>'warning text-dark','aprobada'=>'success','rechazada'=>'danger','convertida'=>'primary']; @endphp
        <span class="badge bg-{{ $estadoColors[$cotizacion->estado] ?? 'secondary' }}">
            {{ ucfirst($cotizacion->estado) }}
        </span>
    </div>
    @if(auth()->user()->esAdmin())
    <div class="d-flex gap-2">
        @if(in_array($cotizacion->estado, ['pendiente', 'aprobada']))
        <a href="{{ route('cotizaciones.convertir', $cotizacion) }}" class="btn btn-success">
            <i class="bi bi-arrow-right-circle me-1"></i>Convertir a Contrato
        </a>
        @endif
        <a href="{{ route('cotizaciones.edit', $cotizacion) }}" class="btn btn-warning">
            <i class="bi bi-pencil me-1"></i>Editar
        </a>
    </div>
    @endif
</div>

<div class="row g-3">
    <div class="col-lg-5">
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header bg-white fw-medium py-3">Datos del cliente</div>
            <div class="card-body">
                <table class="table table-sm table-borderless mb-0">
                    @if($cotizacion->empresa)
                    <tr>
                        <th class="text-muted small">Empresa</th>
                        <td>
                            <a href="{{ route('empresas.show', $cotizacion->empresa) }}" class="text-decoration-none fw-medium">
                                <i class="bi bi-building me-1"></i>{{ $cotizacion->empresa->nombre }}
                            </a>
                        </td>
                    </tr>
                    @endif
                    @if($cotizacion->cliente_nombre)
                    <tr><th class="text-muted small">Contacto</th><td class="fw-medium">{{ $cotizacion->cliente_nombre }}</td></tr>
                    @endif
                    @if($cotizacion->cliente_empresa && !$cotizacion->empresa)
                    <tr><th class="text-muted small">Empresa</th><td>{{ $cotizacion->cliente_empresa }}</td></tr>
                    @endif
                    @if($cotizacion->cliente_telefono)
                    <tr><th class="text-muted small">Teléfono</th>
                        <td><a href="tel:{{ $cotizacion->cliente_telefono }}">{{ $cotizacion->cliente_telefono }}</a></td>
                    </tr>
                    @endif
                    @if($cotizacion->cliente_email)
                    <tr><th class="text-muted small">Email</th>
                        <td><a href="mailto:{{ $cotizacion->cliente_email }}">{{ $cotizacion->cliente_email }}</a></td>
                    </tr>
                    @endif
                </table>
            </div>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white fw-medium py-3">Propuesta</div>
            <div class="card-body">
                <table class="table table-sm table-borderless mb-0">
                    <tr><th class="text-muted small">Tipo</th><td>{{ $cotizacion->tipo_contrato ?? '-' }}</td></tr>
                    <tr><th class="text-muted small">Monto</th>
                        <td class="fw-bold fs-5">S/. {{ number_format($cotizacion->monto_propuesto, 0, ',', '.') }}</td>
                    </tr>
                    <tr><th class="text-muted small">Fecha</th>
                        <td>{{ $cotizacion->fecha_cotizacion?->format('d/m/Y') ?? '-' }}</td>
                    </tr>
                    <tr><th class="text-muted small">Vence</th>
                        <td>
                            {{ $cotizacion->fecha_vencimiento?->format('d/m/Y') ?? '-' }}
                            @if($cotizacion->estado === 'pendiente' && $cotizacion->fecha_vencimiento?->isPast())
                                <span class="badge bg-danger ms-1">Vencida</span>
                            @endif
                        </td>
                    </tr>
                </table>
                @if($cotizacion->notas)
                    <div class="mt-3 small text-muted border-top pt-2">{{ $cotizacion->notas }}</div>
                @endif
            </div>
        </div>
    </div>

    <div class="col-lg-7">
        @if($cotizacion->estado === 'convertida')
        <div class="alert alert-success d-flex align-items-center gap-2">
            <i class="bi bi-check-circle-fill fs-5"></i>
            <div>Esta cotización ya fue <strong>convertida a contrato</strong>.</div>
        </div>
        @endif

        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white fw-medium py-3 d-flex justify-content-between align-items-center">
                <span>Paneles / Servicios cotizados</span>
                <span class="badge bg-secondary">{{ $cotizacion->elementos->count() }}</span>
            </div>
            <div class="table-responsive">
                <table class="table table-sm table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Tipo</th>
                            <th>Código</th>
                            <th>Meses</th>
                            <th>Precio unitario</th>
                            <th>Observaciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($cotizacion->elementos as $elem)
                        <tr>
                            <td>
                                <span class="badge bg-{{ $elem->tipo_elemento === 'digital' ? 'primary' : 'warning text-dark' }}">
                                    {{ ucfirst($elem->tipo_elemento) }}
                                </span>
                            </td>
                            <td class="fw-medium">{{ $elem->codigo }}</td>
                            <td>{{ $elem->tiempo_contrato }}</td>
                            <td>S/. {{ number_format($elem->precio_unitario, 0, ',', '.') }}</td>
                            <td class="text-muted small">{{ $elem->observaciones ?? '-' }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="5" class="text-center text-muted py-3">Sin elementos detallados</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
