@extends('layouts.app')

@section('title', 'Control Publicitario')
@section('subtitle', 'Estado de campañas por empresa y panel')

@section('content')

{{-- Estadísticas --}}
<div class="stats-grid stagger" style="grid-template-columns:repeat(3,1fr);margin-bottom:24px">
    <div class="info-card">
        <div class="info-card-value" style="color:#10B981">{{ $stats['activos'] }}</div>
        <div class="info-card-label"><i class="bi bi-circle-fill" style="font-size:8px;color:#10B981"></i>Activos</div>
    </div>
    <div class="info-card">
        <div class="info-card-value" style="color:#F59E0B">{{ $stats['pausados'] }}</div>
        <div class="info-card-label"><i class="bi bi-pause-circle-fill" style="color:#F59E0B"></i>Pausados</div>
    </div>
    <div class="info-card">
        <div class="info-card-value" style="color:var(--primary)">{{ $stats['cancelados'] }}</div>
        <div class="info-card-label"><i class="bi bi-x-circle-fill" style="color:var(--primary)"></i>Cancelados</div>
    </div>
</div>

<div class="page-header">
    <div></div>
    @if(auth()->user()->esAdmin())
    <button class="btn btn-primary" onclick="document.getElementById('modalNuevo').classList.add('open')">
        <i class="bi bi-plus-lg"></i>Nuevo Registro
    </button>
    @endif
</div>

<div class="filter-bar">
    <form style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;width:100%" method="GET">
        <input type="text" name="buscar" value="{{ request('buscar') }}" class="form-control" style="max-width:240px" placeholder="Empresa o código de panel...">
        <select name="estado" class="form-select" style="max-width:160px">
            <option value="">Todos los estados</option>
            <option value="activo" {{ request('estado') === 'activo' ? 'selected' : '' }}>Activo</option>
            <option value="pausado" {{ request('estado') === 'pausado' ? 'selected' : '' }}>Pausado</option>
            <option value="cancelado" {{ request('estado') === 'cancelado' ? 'selected' : '' }}>Cancelado</option>
        </select>
        <select name="tipo_panel" class="form-select" style="max-width:150px">
            <option value="">Tipo panel</option>
            <option value="digital" {{ request('tipo_panel') === 'digital' ? 'selected' : '' }}>Digital</option>
            <option value="tradicional" {{ request('tipo_panel') === 'tradicional' ? 'selected' : '' }}>Tradicional</option>
        </select>
        <button type="submit" class="btn btn-primary btn-sm"><i class="bi bi-search"></i>Filtrar</button>
        <a href="{{ route('control-publicitario.index') }}" class="btn btn-secondary btn-sm">Limpiar</a>
    </form>
</div>

<div class="card">
    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>Empresa</th><th>Panel</th><th>Tipo</th><th>Período</th>
                    <th>Estado</th><th>Notas</th><th class="td-end">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($registros as $reg)
                <tr>
                    <td class="fw-700" style="color:var(--text-dark)">{{ $reg->empresa_nombre }}</td>
                    <td><code>{{ $reg->panel_codigo }}</code></td>
                    <td>
                        @if($reg->tipo_panel === 'digital')
                            <span class="badge badge-primary"><i class="bi bi-display"></i>Digital</span>
                        @else
                            <span class="badge badge-warning"><i class="bi bi-signpost-2"></i>Tradicional</span>
                        @endif
                    </td>
                    <td>
                        @if($reg->fecha_inicio)
                            <div style="font-size:12.5px">{{ $reg->fecha_inicio->format('d/m/Y') }}</div>
                            @if($reg->fecha_fin)
                                <div class="text-muted" style="font-size:12px">al {{ $reg->fecha_fin->format('d/m/Y') }}</div>
                                @if($reg->estado === 'activo' && $reg->fecha_fin->isPast())
                                    <span class="badge badge-danger" style="margin-top:2px">Vencida</span>
                                @elseif($reg->estado === 'activo' && $reg->fecha_fin->diffInDays(now()) <= 30)
                                    <span class="badge badge-warning" style="margin-top:2px">Por vencer</span>
                                @endif
                            @endif
                        @else <span class="text-muted">—</span> @endif
                    </td>
                    <td>
                        @php $bmap = ['activo'=>'success','pausado'=>'warning','cancelado'=>'danger']; @endphp
                        <span class="badge badge-{{ $bmap[$reg->estado] ?? 'gray' }}">{{ ucfirst($reg->estado) }}</span>
                    </td>
                    <td class="text-muted" style="font-size:13px;max-width:180px">{{ Str::limit($reg->notas, 40) ?? '—' }}</td>
                    <td class="td-end">
                        <div class="flex flex-center gap-8" style="justify-content:flex-end">
                            <a href="{{ route('control-publicitario.show', $reg) }}" class="btn btn-sm btn-secondary btn-icon" title="Ver historial"><i class="bi bi-clock-history"></i></a>
                            @if(auth()->user()->esAdmin())
                            <button class="btn btn-sm btn-warning btn-icon"
                                onclick="openEditModal({{ $reg->id }}, '{{ $reg->estado }}', '{{ $reg->fecha_inicio?->format('Y-m-d') ?? '' }}', '{{ $reg->fecha_fin?->format('Y-m-d') ?? '' }}', '{{ addslashes($reg->notas ?? '') }}')"
                                title="Editar"><i class="bi bi-pencil"></i></button>
                            <form action="{{ route('control-publicitario.destroy', $reg) }}" method="POST" onsubmit="return confirm('¿Eliminar este registro?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-danger btn-icon"><i class="bi bi-trash"></i></button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7"><div class="empty-state"><i class="bi bi-clipboard2-check"></i><p>No hay registros de control publicitario</p></div></td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($registros->hasPages())
    <div class="card-footer">{{ $registros->withQueryString()->links() }}</div>
    @endif
</div>

@if(auth()->user()->esAdmin())
{{-- Modal nuevo --}}
<div class="modal-backdrop" id="modalNuevo" onclick="if(event.target===this)this.classList.remove('open')">
    <div class="modal-box">
        <form action="{{ route('control-publicitario.store') }}" method="POST">
            @csrf
            <div class="modal-header">
                <h5><i class="bi bi-clipboard2-plus" style="margin-right:8px;color:var(--primary-light)"></i>Nuevo Registro</h5>
                <button type="button" class="modal-close" onclick="document.getElementById('modalNuevo').classList.remove('open')">×</button>
            </div>
            <div class="modal-body">
                <div class="grid cols-2" style="gap:14px">
                    <div class="form-group" style="grid-column:1/-1">
                        <label class="form-label">Empresa <span class="req">*</span></label>
                        <input type="text" name="empresa_nombre" class="form-control" list="lista_empresas" required placeholder="Nombre de la empresa...">
                        <datalist id="lista_empresas">@foreach($empresas as $nombre)<option value="{{ $nombre }}">@endforeach</datalist>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Tipo de panel <span class="req">*</span></label>
                        <select name="tipo_panel" id="tipoPanelModal" class="form-select" onchange="actualizarPaneles()">
                            <option value="digital">Digital</option><option value="tradicional">Tradicional</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Código de panel <span class="req">*</span></label>
                        <input type="text" name="panel_codigo" id="panelCodigoModal" class="form-control" list="lista_paneles_digital" required placeholder="Ej: PD-001">
                        <datalist id="lista_paneles_digital">@foreach($paneles_digitales as $p)<option value="{{ $p->codigo }}">{{ $p->codigo }} — {{ $p->nombre }}</option>@endforeach</datalist>
                        <datalist id="lista_paneles_tradicional">@foreach($paneles_tradicionales as $p)<option value="{{ $p->codigo }}">{{ $p->codigo }} — {{ $p->nombre }}</option>@endforeach</datalist>
                    </div>
                    <div class="form-group"><label class="form-label">Fecha inicio</label><input type="date" name="fecha_inicio" class="form-control"></div>
                    <div class="form-group"><label class="form-label">Fecha fin</label><input type="date" name="fecha_fin" class="form-control"></div>
                    <div class="form-group">
                        <label class="form-label">Estado</label>
                        <select name="estado" class="form-select"><option value="activo">Activo</option><option value="pausado">Pausado</option><option value="cancelado">Cancelado</option></select>
                    </div>
                    <div class="form-group" style="grid-column:1/-1"><label class="form-label">Notas</label><textarea name="notas" class="form-control" rows="2"></textarea></div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="document.getElementById('modalNuevo').classList.remove('open')">Cancelar</button>
                <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg"></i>Guardar</button>
            </div>
        </form>
    </div>
</div>

{{-- Modal editar --}}
<div class="modal-backdrop" id="modalEditar" onclick="if(event.target===this)this.classList.remove('open')">
    <div class="modal-box" style="max-width:480px">
        <form id="formEditar" method="POST">
            @csrf @method('PATCH')
            <div class="modal-header">
                <h5><i class="bi bi-pencil-square" style="margin-right:8px;color:#FCD34D"></i>Actualizar registro</h5>
                <button type="button" class="modal-close" onclick="document.getElementById('modalEditar').classList.remove('open')">×</button>
            </div>
            <div class="modal-body">
                <div class="grid cols-2" style="gap:14px">
                    <div class="form-group"><label class="form-label">Fecha inicio</label><input type="date" name="fecha_inicio" id="editFechaInicio" class="form-control"></div>
                    <div class="form-group"><label class="form-label">Fecha fin</label><input type="date" name="fecha_fin" id="editFechaFin" class="form-control"></div>
                    <div class="form-group" style="grid-column:1/-1">
                        <label class="form-label">Estado</label>
                        <select name="estado" id="editEstado" class="form-select"><option value="activo">Activo</option><option value="pausado">Pausado</option><option value="cancelado">Cancelado</option></select>
                    </div>
                    <div class="form-group" style="grid-column:1/-1"><label class="form-label">Notas</label><textarea name="notas" id="editNotas" class="form-control" rows="2"></textarea></div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="document.getElementById('modalEditar').classList.remove('open')">Cancelar</button>
                <button type="submit" class="btn btn-warning"><i class="bi bi-check-lg"></i>Guardar</button>
            </div>
        </form>
    </div>
</div>
@endif
@endsection

@push('scripts')
<script>
function openEditModal(id, estado, fechaInicio, fechaFin, notas) {
    document.getElementById('formEditar').action = '/control-publicitario/' + id;
    document.getElementById('editEstado').value = estado;
    document.getElementById('editFechaInicio').value = fechaInicio;
    document.getElementById('editFechaFin').value = fechaFin;
    document.getElementById('editNotas').value = notas;
    document.getElementById('modalEditar').classList.add('open');
}
function actualizarPaneles() {
    const tipo = document.getElementById('tipoPanelModal').value;
    const input = document.getElementById('panelCodigoModal');
    input.setAttribute('list', tipo === 'digital' ? 'lista_paneles_digital' : 'lista_paneles_tradicional');
    input.value = '';
}
</script>
@endpush
