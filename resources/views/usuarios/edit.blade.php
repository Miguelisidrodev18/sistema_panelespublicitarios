@extends('layouts.app')

@section('title', 'Editar Usuario')
@section('subtitle', $usuario->username)

@section('content')
<div class="form-card">

<div class="page-header">
    <div class="page-header-left">
        <a href="{{ route('usuarios.index') }}" class="back-btn"><i class="bi bi-arrow-left"></i></a>
        <div class="page-title">Editar: {{ $usuario->username }}</div>
    </div>
</div>

<form action="{{ route('usuarios.update', $usuario) }}" method="POST">
    @csrf @method('PUT')

    <div class="card">
        <div class="card-header"><span><i class="bi bi-key"></i>Datos de acceso</span></div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Usuario</label>
                    <input type="text" class="form-control" value="{{ $usuario->username }}" disabled style="background:#F8FAFC">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" value="{{ old('email', $usuario->email) }}" class="form-control">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Nueva contraseña <span class="text-muted" style="font-size:11px">(vacío = no cambiar)</span></label>
                    <input type="password" name="password" class="form-control">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Confirmar contraseña</label>
                    <input type="password" name="password_confirmation" class="form-control">
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header"><span><i class="bi bi-person"></i>Perfil</span></div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-7">
                    <label class="form-label">Nombre completo <span class="req">*</span></label>
                    <input type="text" name="nombre_completo" value="{{ old('nombre_completo', $usuario->nombre_completo) }}" class="form-control" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Rol</label>
                    <select name="rol" class="form-select" id="rolSelect">
                        <option value="admin" {{ old('rol', $usuario->rol) === 'admin' ? 'selected' : '' }}>Admin</option>
                        <option value="gerencia" {{ old('rol', $usuario->rol) === 'gerencia' ? 'selected' : '' }}>Gerencia</option>
                        <option value="empresa" {{ old('rol', $usuario->rol) === 'empresa' ? 'selected' : '' }}>Empresa</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Estado</label>
                    <label class="toggle-switch" style="width:100%;justify-content:center">
                        <input type="checkbox" name="activo" value="1" {{ old('activo', $usuario->activo) ? 'checked' : '' }}>
                        <span>Activo</span>
                    </label>
                </div>
                <div class="col-12" id="empresaField" style="{{ old('rol', $usuario->rol) === 'empresa' ? '' : 'display:none' }}">
                    <label class="form-label">Empresa vinculada</label>
                    <select name="empresa_id" class="form-select">
                        <option value="">Sin empresa</option>
                        @foreach($empresas as $empresa)
                        <option value="{{ $empresa->id }}" {{ old('empresa_id', $usuario->empresa_id) == $empresa->id ? 'selected' : '' }}>{{ $empresa->nombre }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    </div>

    <div class="card" id="permisosCard" style="{{ in_array(old('rol', $usuario->rol), ['admin','gerencia']) ? 'display:none' : '' }}">
        <div class="card-header">
            <span><i class="bi bi-shield-check"></i>Permisos</span>
            <button type="button" class="btn btn-sm btn-secondary" onclick="toggleAll(this)">Seleccionar todo</button>
        </div>
        <div class="card-body">
            <div class="perm-grid">
                @foreach($permisos_disponibles as $key => $label)
                <label class="perm-chip">
                    <input type="checkbox" name="permisos[]" value="{{ $key }}" class="permiso-check"
                        {{ in_array($key, old('permisos', $usuario->permisos ?? [])) ? 'checked' : '' }}>
                    {{ $label }}
                </label>
                @endforeach
            </div>
        </div>
    </div>

    <div class="action-bar">
        <a href="{{ route('usuarios.index') }}" class="btn btn-secondary">Cancelar</a>
        <button type="submit" class="btn btn-warning"><i class="bi bi-check-lg"></i>Guardar cambios</button>
    </div>
</form>
</div>
@endsection

@push('scripts')
<script>
document.getElementById('rolSelect').addEventListener('change', function () {
    const isEmpresa = this.value === 'empresa';
    const isAdmin = this.value === 'admin' || this.value === 'gerencia';
    document.getElementById('empresaField').style.display = isEmpresa ? '' : 'none';
    document.getElementById('permisosCard').style.display = isAdmin ? 'none' : '';
});

function toggleAll(btn) {
    const checks = document.querySelectorAll('.permiso-check');
    const allChecked = Array.from(checks).every(c => c.checked);
    checks.forEach(c => c.checked = !allChecked);
    btn.textContent = allChecked ? 'Seleccionar todo' : 'Deseleccionar todo';
}
</script>
@endpush
