@extends('layouts.app')

@section('title', 'Control Publicitario')
@section('subtitle', 'Estado de campañas por empresa y panel')

@section('content')

{{-- Estadísticas --}}
<div class="row g-3 mb-3">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm text-center py-3">
            <div class="fs-4 fw-bold text-success">{{ $stats['activos'] }}</div>
            <div class="small text-muted"><i class="bi bi-circle-fill text-success me-1"></i>Activos</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm text-center py-3">
            <div class="fs-4 fw-bold text-warning">{{ $stats['pausados'] }}</div>
            <div class="small text-muted"><i class="bi bi-pause-circle-fill text-warning me-1"></i>Pausados</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm text-center py-3">
            <div class="fs-4 fw-bold text-danger">{{ $stats['cancelados'] }}</div>
            <div class="small text-muted"><i class="bi bi-x-circle-fill text-danger me-1"></i>Cancelados</div>
        </div>
    </div>
</div>

<div class="d-flex justify-content-between align-items-center mb-3">
    <div></div>
    @if(auth()->user()->esAdmin())
    <button class="btn btn-danger" onclick="document.getElementById('modalNuevo').classList.add('open')">
        <i class="bi bi-plus-lg me-1"></i>Nuevo Registro
    </button>
    @endif
</div>

<div class="card border-0 shadow-sm mb-3">
    <div class="card-body py-2">
        <form class="row g-2" method="GET">
            <div class="col-md-4">
                <input type="text" name="buscar" value="{{ request('buscar') }}"
                    class="form-control" placeholder="Empresa o código de panel...">
            </div>
            <div class="col-md-2">
                <select name="estado" class="form-select">
                    <option value="">Todos los estados</option>
                    <option value="activo"    {{ request('estado') === 'activo'    ? 'selected' : '' }}>Activo</option>
                    <option value="pausado"   {{ request('estado') === 'pausado'   ? 'selected' : '' }}>Pausado</option>
                    <option value="cancelado" {{ request('estado') === 'cancelado' ? 'selected' : '' }}>Cancelado</option>
                </select>
            </div>
            <div class="col-md-2">
                <select name="tipo_panel" class="form-select">
                    <option value="">Tipo panel</option>
                    <option value="digital"     {{ request('tipo_panel') === 'digital'     ? 'selected' : '' }}>Digital</option>
                    <option value="tradicional" {{ request('tipo_panel') === 'tradicional' ? 'selected' : '' }}>Tradicional</option>
                </select>
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-outline-primary"><i class="bi bi-search"></i> Filtrar</button>
                <a href="{{ route('control-publicitario.index') }}" class="btn btn-outline-secondary ms-1">Limpiar</a>
            </div>
        </form>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Empresa</th>
                    <th>Panel</th>
                    <th>Tipo</th>
                    <th>Período</th>
                    <th>Estado</th>
                    <th>Notas</th>
                    <th class="text-end">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($registros as $reg)
                <tr>
                    <td class="fw-medium">{{ $reg->empresa_nombre }}</td>
                    <td><code class="text-primary">{{ $reg->panel_codigo }}</code></td>
                    <td>
                        @if($reg->tipo_panel === 'digital')
                            <span class="badge bg-primary">Digital</span>
                        @else
                            <span class="badge bg-warning text-dark">Tradicional</span>
                        @endif
                    </td>
                    <td class="small">
                        @if($reg->fecha_inicio)
                            <div>{{ $reg->fecha_inicio->format('d/m/Y') }}</div>
                            @if($reg->fecha_fin)
                                <div class="text-muted">al {{ $reg->fecha_fin->format('d/m/Y') }}</div>
                                @if($reg->estado === 'activo' && $reg->fecha_fin->isPast())
                                    <span class="badge bg-danger">Vencida</span>
                                @elseif($reg->estado === 'activo' && $reg->fecha_fin->diffInDays(now()) <= 30)
                                    <span class="badge bg-warning text-dark">Por vencer</span>
                                @endif
                            @endif
                        @else
                            <span class="text-muted">—</span>
                        @endif
                    </td>
                    <td>
                        @php $bmap = ['activo'=>'success','pausado'=>'warning','cancelado'=>'danger']; @endphp
                        <span class="badge bg-{{ $bmap[$reg->estado] ?? 'secondary' }} {{ $reg->estado==='pausado'?'text-dark':'' }}">
                            {{ ucfirst($reg->estado) }}
                        </span>
                    </td>
                    <td class="text-muted small" style="max-width:180px">
                        {{ Str::limit($reg->notas, 40) ?? '—' }}
                    </td>
                    <td class="text-end">
                        <div class="d-flex gap-1 justify-content-end">
                            <a href="{{ route('control-publicitario.show', $reg) }}" class="btn btn-sm btn-outline-primary" title="Ver historial">
                                <i class="bi bi-clock-history"></i>
                            </a>
                            @if(auth()->user()->esAdmin())
                            <button class="btn btn-sm btn-outline-warning"
                                onclick="openEditModal({{ $reg->id }}, '{{ $reg->estado }}', '{{ $reg->fecha_inicio?->format('Y-m-d') ?? '' }}', '{{ $reg->fecha_fin?->format('Y-m-d') ?? '' }}', '{{ addslashes($reg->notas ?? '') }}')"
                                title="Editar">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <form action="{{ route('control-publicitario.destroy', $reg) }}" method="POST"
                                onsubmit="return confirm('¿Eliminar este registro?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center text-muted py-5">
                        <i class="bi bi-clipboard2-check fs-1 d-block mb-2"></i>No hay registros de control publicitario
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($registros->hasPages())
    <div class="card-footer bg-white">{{ $registros->withQueryString()->links() }}</div>
    @endif
</div>

@if(auth()->user()->esAdmin())
{{-- Modal nuevo --}}
<div class="modal-backdrop" id="modalNuevo" onclick="if(event.target===this)this.classList.remove('open')">
    <div class="modal-box" style="max-width:520px">
        <form action="{{ route('control-publicitario.store') }}" method="POST">
            @csrf
            <div class="modal-header">
                <h5 class="fw-semibold"><i class="bi bi-clipboard2-plus me-2 text-danger"></i>Nuevo Registro</h5>
                <button type="button" class="modal-close" onclick="document.getElementById('modalNuevo').classList.remove('open')">×</button>
            </div>
            <div class="modal-body row g-3">
                <div class="col-12">
                    <label class="form-label">Empresa <span class="text-danger">*</span></label>
                    <input type="text" name="empresa_nombre" class="form-control"
                        list="lista_empresas" required placeholder="Nombre de la empresa...">
                    <datalist id="lista_empresas">
                        @foreach($empresas as $nombre)
                        <option value="{{ $nombre }}">
                        @endforeach
                    </datalist>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Tipo de panel <span class="text-danger">*</span></label>
                    <select name="tipo_panel" id="tipoPanelModal" class="form-select" onchange="actualizarPaneles()">
                        <option value="digital">Digital</option>
                        <option value="tradicional">Tradicional</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Código de panel <span class="text-danger">*</span></label>
                    <input type="text" name="panel_codigo" id="panelCodigoModal" class="form-control"
                        list="lista_paneles_digital" required placeholder="Ej: PD-001">
                    <datalist id="lista_paneles_digital">
                        @foreach($paneles_digitales as $p)
                        <option value="{{ $p->codigo }}">{{ $p->codigo }} — {{ $p->nombre }}</option>
                        @endforeach
                    </datalist>
                    <datalist id="lista_paneles_tradicional">
                        @foreach($paneles_tradicionales as $p)
                        <option value="{{ $p->codigo }}">{{ $p->codigo }} — {{ $p->nombre }}</option>
                        @endforeach
                    </datalist>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Fecha inicio campaña</label>
                    <input type="date" name="fecha_inicio" class="form-control">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Fecha fin campaña</label>
                    <input type="date" name="fecha_fin" class="form-control">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Estado</label>
                    <select name="estado" class="form-select">
                        <option value="activo">Activo</option>
                        <option value="pausado">Pausado</option>
                        <option value="cancelado">Cancelado</option>
                    </select>
                </div>
                <div class="col-12">
                    <label class="form-label">Notas</label>
                    <textarea name="notas" class="form-control" rows="2"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary"
                    onclick="document.getElementById('modalNuevo').classList.remove('open')">Cancelar</button>
                <button type="submit" class="btn btn-danger"><i class="bi bi-check-lg me-1"></i>Guardar</button>
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
                <h5 class="fw-semibold"><i class="bi bi-pencil-square me-2 text-warning"></i>Actualizar registro</h5>
                <button type="button" class="modal-close" onclick="document.getElementById('modalEditar').classList.remove('open')">×</button>
            </div>
            <div class="modal-body row g-3">
                <div class="col-md-6">
                    <label class="form-label">Fecha inicio</label>
                    <input type="date" name="fecha_inicio" id="editFechaInicio" class="form-control">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Fecha fin</label>
                    <input type="date" name="fecha_fin" id="editFechaFin" class="form-control">
                </div>
                <div class="col-12">
                    <label class="form-label">Estado</label>
                    <select name="estado" id="editEstado" class="form-select">
                        <option value="activo">Activo</option>
                        <option value="pausado">Pausado</option>
                        <option value="cancelado">Cancelado</option>
                    </select>
                </div>
                <div class="col-12">
                    <label class="form-label">Notas</label>
                    <textarea name="notas" id="editNotas" class="form-control" rows="2"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary"
                    onclick="document.getElementById('modalEditar').classList.remove('open')">Cancelar</button>
                <button type="submit" class="btn btn-warning"><i class="bi bi-check-lg me-1"></i>Guardar</button>
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
