@extends('layouts.app')

@section('title', $empresa->nombre)

@section('content')
<div class="d-flex align-items-center justify-content-between mb-3">
    <div class="d-flex align-items-center gap-2">
        <a href="{{ route('empresas.index') }}" class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-arrow-left"></i>
        </a>
        <h5 class="mb-0 fw-semibold">{{ $empresa->nombre }}</h5>
        @if($empresa->activo)
            <span class="badge bg-success">Activa</span>
        @else
            <span class="badge bg-secondary">Inactiva</span>
        @endif
    </div>
    @if(auth()->user()->esAdmin())
    <a href="{{ route('empresas.edit', $empresa) }}" class="btn btn-warning">
        <i class="bi bi-pencil me-1"></i>Editar
    </a>
    @endif
</div>

<div class="row g-3">
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header bg-white fw-medium py-3">Información</div>
            <div class="card-body">
                <table class="table table-sm table-borderless mb-0">
                    <tr><th width="120">Encargado</th><td>{{ $empresa->encargado ?? '-' }}</td></tr>
                    <tr><th>Tipo contrato</th><td>{{ $empresa->tipo_contrato ? ucfirst($empresa->tipo_contrato) : '-' }}</td></tr>
                    <tr><th>Monto</th><td>S/. {{ number_format($empresa->monto ?? 0, 0, ',', '.') }}</td></tr>
                    <tr><th>Duración</th><td>{{ $empresa->dias_duracion ? $empresa->dias_duracion . ' días' : '-' }}</td></tr>
                    <tr><th>Inicio</th><td>{{ $empresa->fecha_inicio?->format('d/m/Y') ?? '-' }}</td></tr>
                    <tr><th>Fin</th><td>{{ $empresa->fecha_fin?->format('d/m/Y') ?? '-' }}</td></tr>
                    @if($empresa->contrato_pdf)
                    <tr><th>Contrato</th><td>
                        <a href="{{ Storage::url($empresa->contrato_pdf) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                            <i class="bi bi-file-pdf"></i> Ver PDF
                        </a>
                    </td></tr>
                    @endif
                </table>
            </div>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white fw-medium py-3">Servicios contratados</div>
            <div class="card-body">
                @if($empresa->panel_digital) <span class="badge bg-primary me-1 mb-1">Panel Digital</span> @endif
                @if($empresa->panel_tradicional) <span class="badge bg-secondary me-1 mb-1">Panel Tradicional</span> @endif
                @if($empresa->marketing_digital) <span class="badge bg-success me-1 mb-1">Marketing Digital</span> @endif
                @if($empresa->bonificacion)
                    <div class="mt-2 small text-muted">
                        <strong>Bonificación:</strong> {{ $empresa->comentario_bonificacion ?? '-' }}
                    </div>
                @endif
                @if($empresa->adendas_pagos)
                    <div class="mt-1 small text-muted">
                        <strong>Adendas:</strong> {{ $empresa->comentario_adendas ?? '-' }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                <span class="fw-medium">Cuotas de cobranza</span>
                @if(auth()->user()->esAdmin())
                <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#modalCuota">
                    <i class="bi bi-plus-lg"></i>
                </button>
                @endif
            </div>
            <div class="table-responsive">
                <table class="table table-sm table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>N° Cuota</th><th>Concepto</th><th>Monto</th><th>Vencimiento</th><th>Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($empresa->cobranzas->sortBy('numero_cuota') as $cuota)
                        <tr>
                            <td>{{ $cuota->numero_cuota }}</td>
                            <td>{{ $cuota->concepto ?? '-' }}</td>
                            <td>S/. {{ number_format($cuota->monto, 0, ',', '.') }}</td>
                            <td>
                                {{ $cuota->fecha_vencimiento->format('d/m/Y') }}
                                @if($cuota->estado === 'pendiente' && $cuota->fecha_vencimiento->isPast())
                                    <span class="badge bg-danger ms-1">Vencida</span>
                                @endif
                            </td>
                            <td>
                                @if($cuota->estado === 'pagada')
                                    <span class="badge bg-success">Pagada</span>
                                @else
                                    <span class="badge bg-warning text-dark">Pendiente</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="5" class="text-center text-muted py-3">Sin cuotas registradas</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@if(auth()->user()->esAdmin())
<div class="modal fade" id="modalCuota" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('cobranzas.store') }}" method="POST">
                @csrf
                <input type="hidden" name="empresa_id" value="{{ $empresa->id }}">
                <div class="modal-header">
                    <h6 class="modal-title">Agregar cuota</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body row g-3">
                    <div class="col-6">
                        <label class="form-label">N° Cuota</label>
                        <input type="number" name="numero_cuota" class="form-control" min="1"
                            value="{{ $empresa->cobranzas->max('numero_cuota') + 1 }}" required>
                    </div>
                    <div class="col-6">
                        <label class="form-label">Monto (S/.)</label>
                        <input type="number" name="monto" class="form-control" step="0.01"
                            value="{{ $empresa->monto }}" required>
                    </div>
                    <div class="col-6">
                        <label class="form-label">Vencimiento</label>
                        <input type="date" name="fecha_vencimiento" class="form-control" required>
                    </div>
                    <div class="col-6">
                        <label class="form-label">Concepto</label>
                        <input type="text" name="concepto" class="form-control" placeholder="Ej: Cuota mensual">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
@endsection
