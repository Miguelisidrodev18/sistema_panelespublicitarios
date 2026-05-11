@extends('layouts.app')

@section('title', 'Nuevo Usuario')

@section('content')
<div class="row justify-content-center">
<div class="col-lg-8">

<div class="d-flex align-items-center mb-3">
    <a href="{{ route('usuarios.index') }}" class="btn btn-sm btn-outline-secondary me-3">
        <i class="bi bi-arrow-left"></i>
    </a>
    <h5 class="mb-0 fw-semibold">Nuevo Usuario</h5>
</div>

<form action="{{ route('usuarios.store') }}" method="POST">
    @csrf
    <div class="card border-0 shadow-sm mb-3">
        <div class="card-header bg-white fw-medium py-3">Datos de acceso</div>
        <div class="card-body row g-3">
            <div class="col-md-6">
                <label class="form-label">Usuario <span class="text-danger">*</span></label>
                <input type="text" name="username" value="{{ old('username') }}"
                    class="form-control @error('username') is-invalid @enderror" required>
                @error('username')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6">
                <label class="form-label">Email</label>
                <input type="email" name="email" value="{{ old('email') }}" class="form-control">
            </div>
            <div class="col-md-6">
                <label class="form-label">Contraseña <span class="text-danger">*</span></label>
                <input type="password" name="password"
                    class="form-control @error('password') is-invalid @enderror" required>
                @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6">
                <label class="form-label">Confirmar contraseña <span class="text-danger">*</span></label>
                <input type="password" name="password_confirmation" class="form-control" required>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm mb-3">
        <div class="card-header bg-white fw-medium py-3">Perfil</div>
        <div class="card-body row g-3">
            <div class="col-md-8">
                <label class="form-label">Nombre completo <span class="text-danger">*</span></label>
                <input type="text" name="nombre_completo" value="{{ old('nombre_completo') }}"
                    class="form-control @error('nombre_completo') is-invalid @enderror" required>
                @error('nombre_completo')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-4">
                <label class="form-label">Rol <span class="text-danger">*</span></label>
                <select name="rol" class="form-select" id="rolSelect" required>
                    <option value="admin" {{ old('rol') === 'admin' ? 'selected' : '' }}>Admin</option>
                    <option value="empresa" {{ old('rol') === 'empresa' ? 'selected' : '' }}>Empresa</option>
                </select>
            </div>
            <div class="col-12" id="empresaField" style="{{ old('rol') === 'empresa' ? '' : 'display:none' }}">
                <label class="form-label">Empresa vinculada</label>
                <select name="empresa_id" class="form-select">
                    <option value="">Sin empresa</option>
                    @foreach($empresas as $empresa)
                    <option value="{{ $empresa->id }}" {{ old('empresa_id') == $empresa->id ? 'selected' : '' }}>
                        {{ $empresa->nombre }}
                    </option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm mb-3" id="permisosCard" style="{{ old('rol') === 'admin' ? 'display:none' : '' }}">
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
                            {{ in_array($key, old('permisos', [])) ? 'checked' : '' }}>
                        <label class="form-check-label" for="perm_{{ $key }}">{{ $label }}</label>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    <div class="d-flex gap-2 justify-content-end">
        <a href="{{ route('usuarios.index') }}" class="btn btn-outline-secondary">Cancelar</a>
        <button type="submit" class="btn btn-primary">
            <i class="bi bi-check-lg me-1"></i>Crear Usuario
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
