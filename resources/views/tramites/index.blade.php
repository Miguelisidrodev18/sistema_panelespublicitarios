@extends('layouts.app')

@section('title', 'Trámites')
@section('subtitle', 'Gestión de trámites administrativos')

@section('content')

{{-- ── Stats ── --}}
<div class="row g-3" style="margin-bottom:24px">
    <div class="col-6 col-lg-3">
        <div class="stat-card">
            <div class="stat-icon" style="background:#EDE9FE;color:#7C3AED"><i class="bi bi-file-earmark-check"></i></div>
            <div>
                <div class="stat-value">{{ $stats['total'] }}</div>
                <div class="stat-label">Total procesos</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="stat-card">
            <div class="stat-icon" style="background:var(--amber-light);color:var(--amber-dark)"><i class="bi bi-hourglass-split"></i></div>
            <div>
                <div class="stat-value">{{ $stats['en_tramite'] }}</div>
                <div class="stat-label">En trámite</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="stat-card">
            <div class="stat-icon" style="background:var(--primary-lighter);color:var(--primary)"><i class="bi bi-exclamation-triangle"></i></div>
            <div>
                <div class="stat-value">{{ $stats['observados'] }}</div>
                <div class="stat-label">Observados</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="stat-card">
            <div class="stat-icon" style="background:var(--green-light);color:var(--green-dark)"><i class="bi bi-check-circle"></i></div>
            <div>
                <div class="stat-value">{{ $stats['aprobados'] }}</div>
                <div class="stat-label">Aprobados</div>
            </div>
        </div>
    </div>
</div>

{{-- ── Header + filtros ── --}}
<div class="page-header" style="margin-bottom:14px">
    <div class="page-header-left">
        {{-- Tabs de estado --}}
        <div class="flex gap-8" style="flex-wrap:wrap">
            @php
                $tabEstados = ['' => 'Todos', 'en_tramite' => 'En trámite', 'observado' => 'Observado', 'aprobado' => 'Aprobado', 'rechazado' => 'Rechazado'];
                $estadoActual = request('estado', '');
            @endphp
            @foreach($tabEstados as $val => $label)
            <a href="{{ route('tramites.index', array_merge(request()->except('estado','page'), $val ? ['estado' => $val] : [])) }}"
               class="btn btn-sm {{ $estadoActual === $val ? 'btn-primary' : 'btn-secondary' }}">
                {{ $label }}
            </a>
            @endforeach
        </div>
    </div>
    @if(auth()->user()->esAdmin())
    <a href="{{ route('tramites.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-lg"></i>Nuevo trámite
    </a>
    @endif
</div>

{{-- Búsqueda --}}
<div class="filter-bar" style="margin-bottom:16px">
    <form style="display:flex;align-items:center;gap:10px;width:100%" method="GET">
        @if(request('estado'))<input type="hidden" name="estado" value="{{ request('estado') }}">@endif
        <input type="text" name="buscar" value="{{ request('buscar') }}"
               class="form-control" style="max-width:320px"
               placeholder="Buscar por N°, tipo, entidad...">
        <button type="submit" class="btn btn-primary btn-sm"><i class="bi bi-search"></i>Buscar</button>
        <a href="{{ route('tramites.index') }}" class="btn btn-secondary btn-sm">Limpiar</a>
    </form>
</div>

{{-- ── Tabla ── --}}
<div class="card">
    <div class="card-header" style="border-left-color:#7C3AED">
        <span><i class="bi bi-file-earmark-check" style="color:#7C3AED;margin-right:8px"></i>Lista de trámites</span>
        <span style="font-size:12px;font-weight:500;color:var(--text-light)">{{ $tramites->total() }} registro(s)</span>
    </div>
    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>N°</th>
                    <th>TIPO / ENTIDAD</th>
                    <th># DE TRÁMITE</th>
                    <th>ÁREA ACTUAL</th>
                    <th>FECHA MODIFICACIÓN<br><span style="font-weight:400;color:var(--text-light)">INGRESO</span></th>
                    <th>ESTADO</th>
                    <th class="td-end">ACCIONES</th>
                </tr>
            </thead>
            <tbody>
                @forelse($tramites as $tramite)
                <tr>
                    <td class="fw-700" style="color:var(--text-dark);white-space:nowrap">{{ $tramite->numero }}</td>
                    <td>
                        <div class="fw-600" style="color:var(--primary)">{{ $tramite->tipo ?? '—' }}</div>
                        <div style="font-size:12px;color:var(--text-light)">{{ $tramite->entidad_nombre ?? '—' }}</div>
                    </td>
                    <td>
                        @if($tramite->codigo_tramite)
                            <span class="badge badge-gray">{{ $tramite->codigo_tramite }}</span>
                        @else
                            <span class="text-muted">—</span>
                        @endif
                    </td>
                    <td style="font-size:12.5px;color:var(--primary);font-weight:600">
                        {{ $tramite->area_actual ?? '—' }}
                    </td>
                    <td>
                        @if($tramite->fecha_modificacion)
                            <div style="font-size:12.5px;font-weight:600">{{ $tramite->fecha_modificacion->format('d/m/Y') }}</div>
                        @endif
                        @if($tramite->fecha_ingreso)
                            <div style="font-size:11px;color:var(--text-light)">
                                INGRESO {{ $tramite->fecha_ingreso->format('d/m/Y') }}
                            </div>
                        @endif
                        @if($tramite->fecha_vencimiento)
                            <div style="font-size:11px;color:{{ $tramite->fecha_vencimiento->isPast() ? 'var(--primary)' : 'var(--text-light)' }}">
                                vence {{ $tramite->fecha_vencimiento->format('d/m/Y') }}
                            </div>
                        @endif
                    </td>
                    <td>
                        <span class="badge badge-{{ $tramite->badge_color }}">{{ $tramite->badge_label }}</span>
                    </td>
                    <td class="td-end">
                        <div class="flex flex-center gap-8" style="justify-content:flex-end">
                            <a href="{{ route('tramites.show', $tramite) }}"
                               class="btn btn-sm btn-secondary btn-icon" title="Ver detalle">
                                <i class="bi bi-eye"></i>
                            </a>
                            @if(auth()->user()->esAdmin())
                            <a href="{{ route('tramites.edit', $tramite) }}"
                               class="btn btn-sm btn-warning btn-icon" title="Editar">
                                <i class="bi bi-pencil"></i>
                            </a>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7">
                        <div class="empty-state">
                            <i class="bi bi-file-earmark-check"></i>
                            <p>No hay trámites registrados</p>
                            @if(auth()->user()->esAdmin())
                                <a href="{{ route('tramites.create') }}" class="btn btn-primary btn-sm">
                                    <i class="bi bi-plus-lg"></i>Nuevo trámite
                                </a>
                            @endif
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($tramites->hasPages())
    <div class="card-footer">{{ $tramites->withQueryString()->links() }}</div>
    @endif
</div>

@endsection
