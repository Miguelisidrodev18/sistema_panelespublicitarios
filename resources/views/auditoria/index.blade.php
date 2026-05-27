@extends('layouts.app')

@section('title', 'Auditoría')
@section('subtitle', 'Registro de actividad del sistema')

@section('content')

<div class="page-header">
    <div class="page-header-left">
        <span style="font-size:13px;color:var(--text-light);font-weight:500">Registro completo de actividad del sistema</span>
    </div>
</div>

<div class="filter-bar" style="margin-bottom:20px">
    <form style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;width:100%" method="GET">
            <select name="modulo" class="form-select" style="max-width:160px">
                <option value="">Todos los módulos</option>
                @foreach($modulos as $m)
                    <option value="{{ $m }}" {{ request('modulo') === $m ? 'selected' : '' }}>{{ $m }}</option>
                @endforeach
            </select>
            <select name="accion" class="form-select" style="max-width:140px">
                <option value="">Todas las acciones</option>
                @foreach(['created' => 'Creado', 'updated' => 'Editado', 'deleted' => 'Eliminado'] as $val => $label)
                    <option value="{{ $val }}" {{ request('accion') === $val ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
            <select name="usuario_id" class="form-select" style="max-width:200px">
                <option value="">Todos los usuarios</option>
                @foreach($usuarios as $u)
                    <option value="{{ $u->id }}" {{ request('usuario_id') == $u->id ? 'selected' : '' }}>{{ $u->nombre_completo }}</option>
                @endforeach
            </select>
            <input type="date" name="fecha_desde" value="{{ request('fecha_desde') }}" class="form-control" style="max-width:160px">
            <input type="date" name="fecha_hasta" value="{{ request('fecha_hasta') }}" class="form-control" style="max-width:160px">
            <button type="submit" class="btn btn-primary btn-sm"><i class="bi bi-search"></i>Filtrar</button>
            <a href="{{ route('auditoria.index') }}" class="btn btn-secondary btn-sm">Limpiar</a>
        </form>
</div>

<div class="card">
    <div class="card-header" style="border-left-color:var(--purple);background:linear-gradient(to right,#F5F3FF,#fff)">
        <span><i class="bi bi-shield-check" style="color:var(--purple)"></i>Registro de Actividad</span>
        <span style="font-size:12px;font-weight:500;color:var(--text-light)">{{ $logs->total() }} registro(s)</span>
    </div>
    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>Fecha</th>
                    <th>Usuario</th>
                    <th>Acción</th>
                    <th>Módulo</th>
                    <th>Descripción</th>
                    <th>IP</th>
                </tr>
            </thead>
            <tbody>
                @forelse($logs as $log)
                <tr>
                    <td style="font-size:12px;white-space:nowrap">{{ $log->created_at->format('d/m/Y H:i') }}</td>
                    <td>{{ $log->usuario?->nombre_completo ?? '<em class="text-muted">Sistema</em>' }}</td>
                    <td>
                        @php $colores = ['created'=>'success','updated'=>'warning','deleted'=>'danger']; @endphp
                        <span class="badge badge-{{ $colores[$log->accion] ?? 'gray' }}">{{ ucfirst($log->accion) }}</span>
                    </td>
                    <td><span class="badge badge-gray">{{ $log->modulo }}</span></td>
                    <td style="font-size:12.5px">{{ $log->descripcion ?? '—' }}</td>
                    <td style="font-size:11px;color:var(--text-light)">{{ $log->ip ?? '—' }}</td>
                </tr>
                @empty
                <tr><td colspan="6"><div class="empty-state"><i class="bi bi-shield-check"></i><p>No hay registros de auditoría.</p></div></td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($logs->hasPages())
    <div class="card-footer">{{ $logs->links() }}</div>
    @endif
</div>
@endsection
