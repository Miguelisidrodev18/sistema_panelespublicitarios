@extends('layouts.app')

@section('title', $tramite->numero)
@section('subtitle', $tramite->entidad_nombre ?? 'Trámite')

@section('content')

<div class="page-header">
    <div class="page-header-left">
        <a href="{{ route('tramites.index') }}" class="back-btn"><i class="bi bi-arrow-left"></i></a>
        <div>
            <div class="page-title">{{ $tramite->tipo ?? $tramite->numero }}</div>
            <div style="margin-top:4px;display:flex;gap:8px;align-items:center;flex-wrap:wrap">
                @if($tramite->entidad_expediente)
                    <span style="font-size:12px;color:var(--text-light);font-weight:500">{{ $tramite->entidad_expediente }}</span>
                @endif
                <span class="badge badge-{{ $tramite->badge_color }}">{{ $tramite->badge_label }}</span>
                @if($tramite->activo)
                    <span class="badge badge-success"><i class="bi bi-circle-fill dot"></i>Activa</span>
                @endif
            </div>
        </div>
    </div>
    @if(auth()->user()->esAdmin())
    <a href="{{ route('tramites.edit', $tramite) }}" class="btn btn-warning">
        <i class="bi bi-pencil"></i>Editar
    </a>
    @endif
</div>

@if(session('success'))
<div class="alert alert-success">
    <i class="bi bi-check-circle-fill"></i>{{ session('success') }}
    <button class="alert-dismiss" onclick="this.closest('.alert').remove()">&times;</button>
</div>
@endif

<div class="row g-3">

    {{-- ── Col izquierda: Información ── --}}
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <span><i class="bi bi-info-circle"></i>Información</span>
            </div>
            <div class="card-body">
                <div class="detail-grid">

                    @if($tramite->entidad_nombre)
                    <div class="detail-row">
                        <div class="detail-label">Entidad</div>
                        <div class="detail-value fw-600">{{ $tramite->entidad_nombre }}</div>
                    </div>
                    @endif

                    @if($tramite->encargado)
                    <div class="detail-row">
                        <div class="detail-label">Encargado</div>
                        <div class="detail-value">{{ $tramite->encargado }}</div>
                    </div>
                    @else
                    <div class="detail-row">
                        <div class="detail-label">Encargado</div>
                        <div class="detail-value text-muted">—</div>
                    </div>
                    @endif

                    <div class="detail-row">
                        <div class="detail-label">Área actual</div>
                        <div class="detail-value fw-600" style="color:var(--primary)">{{ $tramite->area_actual ?? '—' }}</div>
                    </div>

                    @if($tramite->doc_presentado)
                    <div class="detail-row">
                        <div class="detail-label">Doc. presentado</div>
                        <div class="detail-value" style="font-size:12.5px">{{ $tramite->doc_presentado }}</div>
                    </div>
                    @endif

                    @if($tramite->encargado_area)
                    <div class="detail-row">
                        <div class="detail-label">Encargado de área</div>
                        <div class="detail-value" style="font-size:12.5px">{{ $tramite->encargado_area }}</div>
                    </div>
                    @endif

                    <div class="detail-row">
                        <div class="detail-label">Contacto</div>
                        <div class="detail-value" style="font-size:12.5px;color:var(--text-light)">
                            {{ $tramite->contacto ?? 'Celular o E-mail' }}
                        </div>
                    </div>

                    <div class="detail-row">
                        <div class="detail-label">Apunte adicional</div>
                        <div class="detail-value" style="font-size:12.5px;color:var(--text-light)">
                            {{ $tramite->apunte_adicional ?? 'Celular o E-mail' }}
                        </div>
                    </div>
                </div>

                {{-- Fechas --}}
                <div style="margin-top:14px;padding-top:12px;border-top:1px solid var(--border)">
                    <div class="detail-grid">
                        @if($tramite->fecha_ingreso)
                        <div class="detail-row">
                            <div class="detail-label">Ingreso</div>
                            <div class="detail-value">{{ $tramite->fecha_ingreso->format('d/m/Y') }}</div>
                        </div>
                        @endif

                        @if($tramite->fecha_modificacion)
                        <div class="detail-row">
                            <div class="detail-label">Modificación</div>
                            <div class="detail-value">{{ $tramite->fecha_modificacion->format('d/m/Y') }}</div>
                        </div>
                        @endif

                        @if($tramite->fecha_vencimiento)
                        <div class="detail-row">
                            <div class="detail-label">Vencimiento</div>
                            <div class="detail-value">
                                {{ $tramite->fecha_vencimiento->format('d/m/Y') }}
                                @if($tramite->fecha_vencimiento->isPast())
                                    <span class="badge badge-danger" style="margin-left:4px">Vencido</span>
                                @elseif($tramite->fecha_vencimiento->diffInDays(now()) <= 7)
                                    <span class="badge badge-warning" style="margin-left:4px">Por vencer</span>
                                @endif
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ── Col derecha: Proceso (timeline) ── --}}
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header" style="border-left-color:#7C3AED">
                <span><i class="bi bi-diagram-3" style="color:#7C3AED;margin-right:8px"></i>Proceso</span>
                <span class="badge badge-gray">{{ $tramite->procesos->count() }} paso(s)</span>
            </div>
            <div class="table-wrapper">
                <table>
                    <thead>
                        <tr>
                            <th style="width:30px">#</th>
                            <th>ÁREA</th>
                            <th>NÚMERO DE NOTIFICACIÓN</th>
                            <th>OBSERVACIÓN</th>
                            <th>ESTADO</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($tramite->procesos as $paso)
                        <tr>
                            <td style="color:var(--text-lighter);font-size:12px">{{ $paso->orden }}</td>
                            <td class="fw-600">{{ $paso->area ?? '—' }}</td>
                            <td style="font-size:12.5px;color:var(--text-light)">
                                {{ $paso->numero_notificacion ?? '—' }}
                            </td>
                            <td style="font-size:12.5px;color:var(--text-medium);max-width:240px">
                                {{ $paso->observacion ?? '—' }}
                            </td>
                            <td>
                                <span class="badge badge-{{ $paso->badge_color }}">{{ $paso->badge_label }}</span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5">
                                <div class="empty-state" style="padding:28px">
                                    <i class="bi bi-diagram-3"></i>
                                    <p>Sin pasos de proceso registrados</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>
@endsection
