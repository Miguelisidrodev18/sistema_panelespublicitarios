@extends('layouts.app')

@section('title', $empresa->nombre)
@section('subtitle', 'Detalle de empresa')

@section('content')
<div class="page-header">
    <div class="page-header-left">
        <a href="{{ route('empresas.index') }}" class="back-btn"><i class="bi bi-arrow-left"></i></a>
        <div>
            <div class="page-title">{{ $empresa->nombre }}</div>
            <div style="margin-top:4px">
                @if($empresa->activo)
                    <span class="badge badge-success"><i class="bi bi-circle-fill dot"></i>Activa</span>
                @else
                    <span class="badge badge-gray">Inactiva</span>
                @endif
            </div>
        </div>
    </div>
    @if(auth()->user()->esAdmin())
    <a href="{{ route('empresas.edit', $empresa) }}" class="btn btn-warning"><i class="bi bi-pencil"></i>Editar</a>
    @endif
</div>

<div class="row g-3">
    <div class="col-lg-4">
        <div class="card" style="margin-bottom:20px">
            <div class="card-header"><span><i class="bi bi-info-circle" style="color:var(--primary);margin-right:8px"></i>Información</span></div>
            <div class="card-body">
                <div class="detail-grid">
                    <div class="detail-row"><div class="detail-label">Encargado</div><div class="detail-value">{{ $empresa->encargado ?? '—' }}</div></div>
                    @if($empresa->correo)
                    <div class="detail-row"><div class="detail-label">Correo</div><div class="detail-value"><a href="mailto:{{ $empresa->correo }}">{{ $empresa->correo }}</a></div></div>
                    @endif
                    @if($empresa->celular)
                    <div class="detail-row"><div class="detail-label">Celular</div><div class="detail-value"><a href="tel:{{ $empresa->celular }}">{{ $empresa->celular }}</a></div></div>
                    @endif
                    <div class="detail-row"><div class="detail-label">Tipo contrato</div><div class="detail-value">{{ $empresa->tipo_contrato ? ucfirst($empresa->tipo_contrato) : '—' }}</div></div>
                    <div class="detail-row"><div class="detail-label">Monto</div><div class="detail-value fw-700">S/. {{ number_format($empresa->monto ?? 0, 0, ',', '.') }}</div></div>
                    <div class="detail-row"><div class="detail-label">Duración</div><div class="detail-value">{{ $empresa->dias_duracion ? $empresa->dias_duracion . ' días' : '—' }}</div></div>
                    <div class="detail-row"><div class="detail-label">Inicio</div><div class="detail-value">{{ $empresa->fecha_inicio?->format('d/m/Y') ?? '—' }}</div></div>
                    <div class="detail-row"><div class="detail-label">Fin</div><div class="detail-value">{{ $empresa->fecha_fin?->format('d/m/Y') ?? '—' }}</div></div>
                    @if($empresa->contrato_pdf)
                    <div class="detail-row"><div class="detail-label">Contrato</div><div class="detail-value">
                        <a href="{{ Storage::url($empresa->contrato_pdf) }}" target="_blank" class="btn btn-sm btn-outline"><i class="bi bi-file-pdf"></i>Ver PDF</a>
                    </div></div>
                    @endif
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header"><span><i class="bi bi-tags" style="color:var(--primary);margin-right:8px"></i>Servicios contratados</span></div>
            <div class="card-body">
                <div class="chip-group">
                    @if($empresa->panel_digital) <span class="chip active"><i class="bi bi-display"></i>Panel Digital</span> @endif
                    @if($empresa->panel_tradicional) <span class="chip active" style="background:#F59E0B;border-color:#F59E0B"><i class="bi bi-signpost-2"></i>Panel Tradicional</span> @endif
                    @if($empresa->marketing_digital) <span class="chip active" style="background:#10B981;border-color:#10B981"><i class="bi bi-megaphone"></i>Marketing Digital</span> @endif
                </div>
                @if($empresa->bonificacion)
                    <div style="margin-top:12px;padding-top:10px;border-top:1px solid var(--border);font-size:12.5px;color:var(--text-light)">
                        <strong>Bonificación:</strong> {{ $empresa->comentario_bonificacion ?? '—' }}
                    </div>
                @endif
                @if($empresa->adendas_pagos)
                    <div style="margin-top:6px;font-size:12.5px;color:var(--text-light)">
                        <strong>Adendas:</strong> {{ $empresa->comentario_adendas ?? '—' }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <span><i class="bi bi-cash-coin" style="color:var(--primary);margin-right:8px"></i>Cuotas de cobranza</span>
                @if(auth()->user()->esAdmin())
                <button class="btn btn-sm btn-primary" onclick="document.getElementById('modalCuota').classList.add('open')">
                    <i class="bi bi-plus-lg"></i>
                </button>
                @endif
            </div>
            <div class="table-wrapper">
                <table>
                    <thead>
                        <tr><th>N° Cuota</th><th>Concepto</th><th>Monto</th><th>Vencimiento</th><th>Estado</th></tr>
                    </thead>
                    <tbody>
                        @forelse($empresa->cobranzas->sortBy('numero_cuota') as $cuota)
                        <tr>
                            <td class="fw-600">{{ $cuota->numero_cuota }}</td>
                            <td class="text-muted">{{ $cuota->concepto ?? '—' }}</td>
                            <td class="fw-700">S/. {{ number_format($cuota->monto, 0, ',', '.') }}</td>
                            <td>
                                {{ $cuota->fecha_vencimiento->format('d/m/Y') }}
                                @if($cuota->estado === 'pendiente' && $cuota->fecha_vencimiento->isPast())
                                    <span class="badge badge-danger" style="margin-left:4px">Vencida</span>
                                @endif
                            </td>
                            <td>
                                @if($cuota->estado === 'pagada')
                                    <span class="badge badge-success"><i class="bi bi-check-lg"></i>Pagada</span>
                                @else
                                    <span class="badge badge-warning">Pendiente</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="5">
                            <div class="empty-state" style="padding:32px"><i class="bi bi-cash-coin"></i><p>Sin cuotas registradas</p></div>
                        </td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@if(auth()->user()->esAdmin())
<div class="modal-backdrop" id="modalCuota" onclick="if(event.target===this)this.classList.remove('open')">
    <div class="modal-box">
        <form action="{{ route('cobranzas.store') }}" method="POST">
            @csrf
            <input type="hidden" name="empresa_id" value="{{ $empresa->id }}">
            <div class="modal-header">
                <h5><i class="bi bi-plus-circle" style="margin-right:8px;color:var(--primary-light)"></i>Agregar cuota</h5>
                <button type="button" class="modal-close" onclick="document.getElementById('modalCuota').classList.remove('open')">×</button>
            </div>
            <div class="modal-body">
                <div class="grid cols-2" style="gap:14px">
                    <div class="form-group">
                        <label class="form-label">N° Cuota</label>
                        <input type="number" name="numero_cuota" class="form-control" min="1"
                            value="{{ $empresa->cobranzas->max('numero_cuota') + 1 }}" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Monto (S/.)</label>
                        <input type="number" name="monto" class="form-control" step="0.01"
                            value="{{ $empresa->monto }}" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Vencimiento</label>
                        <input type="date" name="fecha_vencimiento" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Concepto</label>
                        <input type="text" name="concepto" class="form-control" placeholder="Ej: Cuota mensual">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="document.getElementById('modalCuota').classList.remove('open')">Cancelar</button>
                <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg"></i>Guardar</button>
            </div>
        </form>
    </div>
</div>
@endif
@endsection
