@extends('layouts.app')

@section('title', 'Editar Usuario')

@section('content')
<div class="row justify-content-center">
<div class="col-lg-8">

<div class="d-flex align-items-center mb-3">
    <a href="{{ route('usuarios.index') }}" class="btn btn-sm btn-outline-secondary me-3">
        <i class="bi bi-arrow-left"></i>
    </a>
    <h5 class="mb-0 fw-semibold">Editar: {{ $usuario->username }}</h5>
</div>

<form action="{{ route('usuarios.update', $usuario) }}" method="POST">
    @csrf @method('PUT')

    <div class="card border-0 shadow-sm mb-3">
        <div class="card-header bg-white fw-medium py-3">Datos de acceso</div>
        <div class="card-body row g-3">
            <div class="col-md-6">
                <label class="form-label">Usuario</label>
                <input type="text" class="form-control" value="{{ $usuario->username }}" disabled>
            </div>
            <div class="col-md-6">
                <label class="form-label">Email</label>
                <input type="email" name="email" value="{{ old('email', $usuario->email) }}" class="form-control">
            </div>
            <div class="col-md-6">
                <label class="form-label">Nueva contraseña <span class="text-muted small">(dejar vacío para no cambiar)</span></label>
                <input type="password" name="password" class="form-control">
            </div>
            <div class="col-md-6">
                <label class="form-label">Confirmar contraseña</label>
                <input type="password" name="password_confirmation" class="form-control">
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm mb-3">
        <div class="card-header bg-white fw-medium py-3">Perfil</div>
        <div class="card-body row g-3">
            <div class="col-md-7">
                <label class="form-label">Nombre completo <span class="text-danger">*</span></label>
                <input type="text" name="nombre_completo" value="{{ old('nombre_completo', $usuario->nombre_completo) }}"
                    class="form-control" required>
            </div>
            <div class="col-md-3">
                <label class="form-label">Rol</label>
                <select name="rol" class="form-select" id="rolSelect">
                    <option value="admin" {{ old('rol', $usuario->rol) === 'admin' ? 'selected' : '' }}>Admin</option>
                    <option value="empresa" {{ old('rol', $usuario->rol) === 'empresa' ? 'selected' : '' }}>Empresa</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Estado</label>
                <div class="form-check form-switch mt-2">
                    <input class="form-check-input" type="checkbox" name="activo" value="1" id="activo"
                        {{ old('activo', $usuario->activo) ? 'checked' : '' }}>
                    <label class="form-check-label" for="activo">Activo</label>
                </div>
            </div>
            <div class="col-12" id="empresaField" style="{{ old('rol', $usuario->rol) === 'empresa' ? '' : 'display:none' }}">
                <label class="form-label">Empresa vinculada</label>
                <select name="empresa_id" class="form-select">
                    <option value="">Sin empresa</option>
                    @foreach($empresas as $empresa)
                    <option value="{{ $empresa->id }}" {{ old('empresa_id', $usuario->empresa_id) == $empresa->id ? 'selected' : '' }}>
                        {{ $empresa->nombre }}
                    </option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm mb-3" id="permisosCard" style="{{ old('rol', $usuario->rol) === 'admin' ? 'display:none' : '' }}">
        <div class="card-header bg-white fw-medium py-3 d-flex justify-content-between">
            <span>Permisos</span>
            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="toggleAll(this)">Seleccionar todo</button>
        </div>
        <div class="card-body">
            <div class="row g-2">
                @foreach($permisos_disponibles as $key => $label)
                <div class="col-md-4 col-sm-6">
                    <div class="form-check">
                        <input class="form-check-input permiso-check" type="checkbox" name="permisos[]"
                            value="{{ $key }}" id="perm_{{ $key }}"
                            {{ in_array($key, old('permisos', $usuario->permisos ?? [])) ? 'checked' : '' }}>
                        <label class="form-check-label" for="perm_{{ $key }}">{{ $label }}</label>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    <div class="d-flex gap-2 justify-content-end">
        <a href="{{ route('usuarios.index') }}" class="btn btn-outline-secondary">Cancelar</a>
        <button type="submit" class="btn btn-warning">
            <i class="bi bi-check-lg me-1"></i>Guardar cambios
        </button>
    </div>
</form>

</div>
</div>
@endsection

@push('scripts')
<script>
document.getElementById('rolSelect').addEventListener('change', function () {
    const isEmpresa = this.value === 'empresa';
    document.getElementById('empresaField').style.display = isEmpresa ? '' : 'none';
    document.getElementById('permisosCard').style.display = isEmpresa ? '' : 'none';
});

function toggleAll(btn) {
    const checks = document.querySelectorAll('.permiso-check');
    const allChecked = Array.from(checks).every(c => c.checked);
    checks.forEach(c => c.checked = !allChecked);
    btn.textContent = allChecked ? 'Seleccionar todo' : 'Deseleccionar todo';
}
</script>
@endpush
